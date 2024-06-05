let CheckMostrarModalDeError = document.getElementById('CheckMostrarModalDeError');
let ModalDeErroresDelPOST = document.getElementById('ModalDeErroresDelPOST');

CheckMostrarModalDeError.addEventListener('change', () => {
    ComprobarEstadoDelModalDeModalDeErrores();
    
})

function ComprobarEstadoDelModalDeModalDeErrores(){
    if(CheckMostrarModalDeError.checked){
        ModalDeErroresDelPOST.style = "display: flex;"
    }else{
        ModalDeErroresDelPOST.style = "display: none;"
    }
}

window.addEventListener('load', () => {
    ComprobarEstadoDelModalDeModalDeErrores();
})



let InputDeNombre = document.getElementById('InputDeNombre');
let DivPaMostrarLoDelInput = document.getElementById('DivPaMostrarLoDelInput');
let Aychamo = document.querySelector('.Aychamo');


InputDeNombre.addEventListener('keyup', () => {
    
    DivPaMostrarLoDelInput.innerHTML = '<div class="Aychamo"></div>' + InputDeNombre.value;
    
})