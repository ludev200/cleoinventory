const SW_container = document.getElementById('SWAlert');

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


window.addEventListener('load', function(){
    if(SW_container){
        Toast.fire({
            icon: "error",
            title: SW_container.innerText
        });
    }

    
    this.document.getElementById('profileselector').addEventListener('change', function(){
        showUserLvlModulos(this.value)
    })


    this.document.getElementById('validateFormButton').addEventListener('click', function(e){
        e.preventDefault();
        moduloCounter = 0;
        document.querySelectorAll('.checkboxmodulo').forEach( input => {
            if(input.checked){
                moduloCounter++;
            }
        })
        if(moduloCounter>0){
            tryValidateForm();
        }else{
            Toast.fire({
                icon: 'error',
                title: 'No se otorgó ningún permiso al usuario'
            });
        }
        
    })
})


function showUserLvlModulos(level){
    document.querySelectorAll('.checkboxmodulo').forEach( input => {
        input.checked = false;
    })

    document.querySelectorAll('.checkboxmodulo.lvl'+level).forEach( input => {
        input.checked = true;
    })
}


async function tryValidateForm(){
    const docType = document.getElementById('docTypeSelector').value;
    const cedula = document.getElementById('cedulaInput').value;
    const nname = document.getElementById('nameInput').value;
    const sex = document.getElementById('sexSelector').value;
    const password = document.getElementById('passwordInput').value;
    const profileLevel = document.getElementById('profileselector').value;

    idEntity = document.getElementById('usernameInput').value;

    

    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=9&idEntity=${idEntity}&method=validateData&`+
        `docType=${docType}&cedula=${cedula}&name=${nname}&sex=${sex}&profileLevel=${profileLevel}&password=${password}`);

        if(response.status == 200){
            petition = await response.json();

            
            if(petition.status == 200){
                document.getElementById('form').submit();
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                });
            }
        }
    }catch(error){
        console.log(error)
    }
}