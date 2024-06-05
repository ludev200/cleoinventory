const imagenInput = document.querySelector('.imgAndId label input');
const imagePreview = document.querySelector('.imgAndId img');
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

imagenInput.addEventListener('change', function(){
    var newImage = imagenInput.files[0];
            
    const reader = new FileReader();
    reader.onload = (e) => { imagePreview.src = e.target.result; };
    reader.readAsDataURL(newImage);
})

window.addEventListener('load', function(){
    if(SW_container){
        Toast.fire({
            icon: "error",
            title: SW_container.innerText
        });
    }

    formEvents();
})


function formEvents(){
    document.getElementById('validateFormButton').addEventListener('click', function(){
        sendValidation();
    })
}

async function sendValidation(){
    const idEntity = document.getElementById('idEntity').value;
    const docType = document.getElementById('docType').value;
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone_input').value;
    const phone2 = document.getElementById('phone2_input').value;
    const email = document.getElementById('email_input').value;
    const address = document.getElementById('address_input').value;
    try{
        
        ulr = `http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=3&method=validateData`+
        `&idEntity=${idEntity}&docType=${docType}&phone=${phone}&phone2=${phone2}&email=${email}&name=${name}&address=${address}`;
        
        response = await fetch(ulr);
        
        console.log(ulr)
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


function valida_phoneFormat(element, event){
    if(element.value.charAt(4)!='-'){
        element.setAttribute('maxlength', '13');
    }else{
        element.setAttribute('maxlength', '12');
    }

    if(isNaN(event.key)){
        if(event.keyCode != 43){
            return false;
        }else{
            if(element.value != ''){
                return false;
            }
        }
    }else{
        value = element.value+event.key;
        
        if(value.toString().length == 4 && element.value.charAt(0)!='+'){
            element.value = value+'-';
            
            return false;
        }else{
            
        }
    }
}