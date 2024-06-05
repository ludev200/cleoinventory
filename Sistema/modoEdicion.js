window.addEventListener('load', function(){
    setEvents();
})

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});



function setEvents(){
    document.querySelectorAll('#listaEditable .row .editData_button').forEach( button => {
        button.addEventListener('click', function(){
            document.querySelector(`#${button.getAttribute('idRow')} .showInActive input`).value = document.querySelector(`#${button.getAttribute('idRow')} .hideInActive span`).innerText;
            document.getElementById(button.getAttribute('idRow')).classList.add('active');
            document.querySelector(`#${button.getAttribute('idRow')} .showInActive input`).focus();
        })
    })


    document.querySelectorAll('#listaEditable .row .back').forEach( button => {
        button.addEventListener('click', function(){
            document.getElementById(button.getAttribute('idRow')).classList.remove('active');
        })
    })

    document.querySelectorAll('#listaEditable .row .save').forEach( button => {
        button.addEventListener('click', function(){
            trySave(button.getAttribute('idRow'));
        })
    })

    document.querySelectorAll('#listaEditable .row .showInActive input').forEach( input => {
        input.addEventListener('keyup', function(e){
            if(e.keyCode == 13){
                document.querySelector(`#${input.getAttribute('idRow')} .showInActive .save`).click();
            }
        })
    })
}


async function trySave(idRow){
    var link = undefined;
    var value = document.querySelector(`#${idRow} .showInActive input`).value;
    console.log(`Guardando ${idRow}`)
    
    
    switch(idRow){
        case 'row_companyRif':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setCompany_rif&value=${value}`;
        break;

        case 'row_companyName':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setCompany_name&value=${value}`;
        break;

        case 'row_companyAddress':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setCompany_address&value=${value}`;
        break;

        case 'row_companyCityData':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setCompany_cityData&value=${value}`;
        break;

        case 'row_companyPhone':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setCompany_phone&value=${value}`;
        break;

        case 'row_companyEmail':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setCompany_email&value=${value}`;
        break;

        case 'row_nationalCurrencyName':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setNationalCurrency_name&value=${value}`;
        break;

        case 'row_nationalCurrencySimbol':
            link = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=setNationalCurrency_simbol&value=${value}`;
        break;
        

        default:
            console.log(`No se especificó una funcion para el elemeto ${idRow}`)
    }


    if(link){
        try{
            response = await fetch(link);

            if(response.status == 200){
                petition = await response.json();

                showResult(idRow, petition);
            }else{
                console.log('error de response '+response.status);
            }
        }catch(error){
            console.log(error)
        }
    }
}

function showResult(idRow, petition){
    successMessage = '';

    switch(idRow){
        case 'row_companyRif': successMessage = 'RIF de la empresa ha sido modificado';
        break;

        case 'row_companyName': successMessage = 'Nombre de la empresa ha sido modificado';
        break;

        case 'row_companyAddress': successMessage = 'Dirección de la empresa ha sido modificado';
        break;

        case 'row_companyCityData': successMessage = 'Ciudad de la empresa ha sido modificado';
        break;

        case 'row_companyPhone': successMessage = 'Teléfono de la empresa ha sido modificado';
        break;

        case 'row_companyEmail': successMessage = 'Correo de la empresa ha sido modificado';
        break;

        case 'row_nationalCurrencyName': successMessage = 'Nombre de moneda nacional ha sido modificado';
        break;

        case 'row_nationalCurrencySimbol': successMessage = 'Símbolo de moneda nacional ha sido modificado';
        break;
    }

    try{
        if(petition.status == 200){
            Toast.fire({
                icon: "success",
                title: successMessage
            });
        }else{
            Toast.fire({
                icon: "error",
                title: petition.result
            });
        }
    }catch(error){
        if(petition.status == 200){
            alert(successMessage);
        }else{
            alert(petition.result);
        }
    }


    
    document.querySelector(`#${idRow} .hideInActive span`).innerText = petition.givenData.value;
    document.querySelector(`#${idRow} .showInActive .back`).click();
}