const validateFormButton = document.getElementById('validateFormButton');
const nameInput = document.getElementById('nameInput');
const addressInput = document.getElementById('addressInput');
const idInput = document.getElementById('idInput');
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


    validateFormButton.addEventListener('click', function(e){
        e.preventDefault();
        validateForm();
    })
})


async function validateForm(){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=1&idEntity=${idInput.value}&method=validateData&address=${addressInput.value}&name=${nameInput.value}`);

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
        }
    }catch(error){
        console.log(error)
    }
}