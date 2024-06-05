let ModalSeleccionDeEntrada = document.getElementById('ModalSeleccionDeEntrada');
let CuerpoDeVentanaDeEntrada = document.getElementById('CuerpoDeVentanaDeEntrada');
let BotonCerrarVentanaDeEntrada = document.getElementById('BotonCerrarVentanaDeEntrada');
let BotonAgregarEntrada = document.getElementById('BotonAgregarEntrada');



//VISIBILIDAD DEL MODAL DE ENTRADA
CuerpoDeVentanaDeEntrada.addEventListener('click', (e) => {
    e.stopPropagation();
})
ModalSeleccionDeEntrada.addEventListener('click', () => {
    BotonCerrarVentanaDeEntrada.click();
})
BotonCerrarVentanaDeEntrada.addEventListener('click', () => {
    MostrarModalDeEntrada(false);
})
BotonAgregarEntrada.addEventListener('click', () => {
    MostrarModalDeEntrada(true);
})

async function MostrarModalDeEntrada(valor){
    
    if(valor){
        ModalSeleccionDeEntrada.style = "display: flex;";
        await EsperardS(2);
        CuerpoDeVentanaDeEntrada.className ="VentanaFlotante";
    }else{
        CuerpoDeVentanaDeEntrada.className ="VentanaFlotante OcultarModal";
        await EsperardS(3);
        ModalSeleccionDeEntrada.style = "";
    }
}




let BotonRealizarBusqueda = document.getElementById('BotonRealizarBusqueda');
let SelectEstado = document.getElementById('SelectEstado');

SelectEstado.addEventListener('change', () => {
    BotonRealizarBusqueda.click();
})





function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function EsperardS(Tiempo) {
    for (let i = 0; i < Tiempo; i++) {
        await sleep(i * 100);
    }
}

