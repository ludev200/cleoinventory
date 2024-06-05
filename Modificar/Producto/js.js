const imagenInput = document.querySelector('.imgAndId label input');
const imagePreview = document.querySelector('.imgAndId img');
const SW_container = document.getElementById('SWAlert');
const categoryInput = document.getElementById('categoryInput');
const alertLevelInput = document.getElementById('alertLevelInput');
const unitInput = document.getElementById('unitInput');
const hideOnWorkHand = document.querySelectorAll('.hideOnWorkHand');

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


    imagenInput.addEventListener('change', function(){
        var newImage = imagenInput.files[0];
                
        const reader = new FileReader();
        reader.onload = (e) => { imagePreview.src = e.target.result; };
        reader.readAsDataURL(newImage);
    })

    showInputForCategory();
    categoryInput.addEventListener('change', function(){
        showInputForCategory();
    })

    showUnit();
    unitInput.addEventListener('change', function(){
        showUnit();
    })

    document.getElementById('validateFormButton').addEventListener('click', function(e){
        e.preventDefault();
        validateForm();
    })
})

async function validateForm(){
    idEntity = document.getElementById('idEntity').innerText;
    nname = document.querySelector('input#name').value;
    price = document.querySelector('input#priceInput').value;
    idCategory = document.getElementById('categoryInput').value;
    unit = document.getElementById('unitInput').value;
    alertLevel = document.getElementById('alertLevelInput').value;
    deafultSpoilage = document.getElementById('deafultSpoilage').value;
    description = document.getElementById('descriptionInput').value;
    providers = document.getElementById('entityAddedOnList').value;

    ulr = `http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=5&idEntity=${idEntity}&method=validateData&`+
    `deafultSpoilage=${deafultSpoilage}price=${price}&idCategory=${idCategory}&idUnit=${unit}&alertLevel=${alertLevel}&description=${description}&providers=${providers}&name=${nname}`;


    try{
        response = await fetch(ulr);
        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                document.getElementById('sendFormButton').click();
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                })
            }
        }else{
            console.log(`Error de response: ${response.status}`)    
        }
    }catch(error){
        console.log(error);
    }
}

function showInputForCategory(){
    hideOnWorkHand.forEach( element => {
        if(categoryInput.value < 3){
            element.style.display = "";
        }else{
            element.style.display = "none";
        }
    })

    if(categoryInput.value == 1){
        alertLevelInput.setAttribute('onkeypress','return validate_intNumber(this, event)');
    }
    
    console.log(categoryInput.value)
    if(categoryInput.value == 2){
        unitInput.setAttribute('disabled', 'disabled');
        unitInput.value = 2;
        document.getElementById('unitSimbol').innerText = 'u';

        alertLevelInput.setAttribute('onkeypress','return validate_4floatNumber(this, event)');
        document.querySelectorAll('.showForEquipment').forEach( node => {
            node.style.display = '';
        })
    }else{
        unitInput.removeAttribute('disabled');
        
        document.querySelectorAll('.showForEquipment').forEach( node => {
            node.style.display = 'none';
        })
    }
}

function showUnit(){
    document.getElementById('unitSimbol').innerText = unitInput.options[unitInput.selectedIndex].getAttribute('simbol');
}



function validate_intNumber(element, e){
    if(isNaN(e.key)){
        return false;
    }
}

function validate_price(element, e){
    if(e.key == '.'){
        if(element.value.includes('.')){
            return false;
        }else{
             
        }
    }else{
        if(isNaN(e.key)){
            return false;
        }else{
            if(element.value.indexOf('.') > 0){
                decimals = Number(element.value.length) - Number(element.value.indexOf('.'));
                if(decimals > 2){
                    return false;
                }
            }
        }
    }
}

function priceFormat(element){
    element.value = Number(element.value).toFixed(2);
}

function validate_2floatNumber(element, e){
    if(isNaN(e.key)){
        if(e.key != '.'){
            return false;
        }

        
        if(element.value.includes('.')){
            return false;
        }
    }else{
        if(element.value.includes('.')){
            pieces = element.value.split('.');
            if(pieces[1].length > 1){
                if(Number(element.value)>0){
                    selectionSize = element.selectionEnd - element.selectionStart;
                    if(selectionSize != element.value.length){
                        return false;
                    }
                }
            }
        }
    }
}

function validate_4floatNumber(element, e){
    if(isNaN(e.key)){
        if(e.key != '.'){
            return false;
        }

        
        if(element.value.includes('.')){
            return false;
        }
    }else{
        if(element.value.includes('.')){
            pieces = element.value.split('.');
            if(pieces[1].length > 3){
                if(Number(element.value)>0){
                    selectionSize = element.selectionEnd - element.selectionStart;
                    if(selectionSize != element.value.length){
                        return false;
                    }
                }
            }
        }
    }
}



function validateNoHashtag(element, e){
    if(e.key == '#'){
        Toast.fire({
            icon: 'warning',
            title: "El uso del caractér '#' no está permitido"
        })
        return false;
    }
}


function validate_4floatNumber(element, e){
    /*
    if(element.value == 0){
        element.setAttribute('maxlength', '6');
    }else{
        element.setAttribute('maxlength', '');
    }
    */

    if(isNaN(e.key)){
        if(e.key != '.'){
            return false;
        }

        
        if(element.value.includes('.')){
            return false;
        }
    }else{
        if(element.value.includes('.')){
            pieces = element.value.split('.');
            if(pieces[1].length > 3){
                if(Number(element.value)>0){
                    selectionSize = element.selectionEnd - element.selectionStart;
                    if(selectionSize != element.value.length){
                        return false;
                    }
                }
            }
        }
    }
}