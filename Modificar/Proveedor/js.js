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

    
})




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