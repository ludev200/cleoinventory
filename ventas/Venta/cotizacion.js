let ModalGenerarPDF = document.getElementById('ModalGenerarPDF');
let InputTasa = document.getElementById('InputTasa');

ModalGenerarPDF.addEventListener('click', () => {

    BotonCerrar.click();
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

let BotonAbrirMenuDeCrearReporte = document.getElementById('BotonAbrirMenuDeCrearReporte');

BotonAbrirMenuDeCrearReporte.addEventListener('click', () => {
    BotonVolverASeleccion.click();
    BajarVentanita();
})

let Ventanita = document.querySelector('.Ventanita');

Ventanita.addEventListener('click', (e) => {
    e.stopPropagation();
})

let BotonCerrar = document.querySelector('.BotonCerrar');

BotonCerrar.addEventListener('click', () => {
    SubirVentanita();
})

let BotonMonedaNacional = document.getElementById('BotonMonedaNacional');
let InputMostrarSeleccionDeTasa = document.getElementById('InputMostrarSeleccionDeTasa');
let CajaDeOpciones = document.querySelector('.CajaDeOpciones');

BotonMonedaNacional.addEventListener('click', () => {
    if(InputMostrarSeleccionDeTasa.checked == false){
        InputMostrarSeleccionDeTasa.checked = true;
        CajaDeOpciones.classList = "CajaDeOpciones MostrarSeleccionDeTasa";
        BotonMonedaNacional.style = "transform: translateX(-30px);  cursor: default;";
        BotonMonedaNacional.classList = "Opcion";
        demo();
    }
})

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function demo() {
    for (let i = 0; i < 2; i++) {
        await sleep(i * 900);
    }
    InputTasa.focus();
}

async function BajarVentanita() {
    ModalGenerarPDF.style = "display: flex;";
    for (let i = 0; i < 1; i++) {
        await sleep(i * 100);
    }
    Ventanita.classList = "Ventanita BajarVentanita";
}

async function SubirVentanita() {
    Ventanita.classList = "Ventanita";
    for (let i = 0; i < 2; i++) {
        await sleep(i * 300);
    }
    ModalGenerarPDF.style = "display: none;";
}



let BotonVolverASeleccion = document.getElementById('BotonVolverASeleccion');

BotonVolverASeleccion.addEventListener('click', () => {
    if(InputMostrarSeleccionDeTasa.checked == true) {
        InputMostrarSeleccionDeTasa.checked = false;
        CajaDeOpciones.classList = "CajaDeOpciones";
        BotonMonedaNacional.style = "transform: translateX(0px); cursor: pointer;";
        BotonMonedaNacional.classList = "Opcion Pulsable";
    }
    
})


InputTasa.addEventListener('keyup', (e)=>{
    if(e.keyCode == 13){
        newOptionButton.click();
    }
})


let BotonGenerarReporteConTasaEspecifica = document.getElementById('BotonGenerarReporteConTasaEspecifica');
let newOptionButton = document.getElementById('newOptionButton');


newOptionButton.addEventListener('click', () => {
    if(isNaN(InputTasa.value)){
        Toast.fire({
            icon: 'warning',
            title: 'La tasa indicada no es un valor válido.'
        })
    }else{
        if(InputTasa.value){
            if(InputTasa.value == 0){
                Toast.fire({
                    icon: 'warning',
                    title: 'La tasa de cambio de ser mayor a cero'
                })
            }else{
                pedazos = document.URL.split('?', 2);
                CosasDelGET = pedazos[1].split('&');
                
                var IDDeLaCot = '0';
                var cantidadACotizar =  document.getElementById('cantidadACotizar_input').value;
                CosasDelGET.forEach(DatoDelGet => {
                    DatoPorParte = DatoDelGet.split('=');
                    console.log(DatoPorParte);
                    if(DatoPorParte[0] == 'id'){
                        IDDeLaCot = DatoPorParte[1];
                    }
                });


                
                if(IDDeLaCot=='0'){
                    Toast.fire({
                        icon: 'warning',
                        title: 'No se encontró la ID de la cotización'
                    })
                }else{
                    if(isNaN(IDDeLaCot)){
                        Toast.fire({
                            icon: "error",
                            title: 'La ID ' + IDDeLaCot + ' es inválida.'
                        });
                    }else{
                        if(cantidadACotizar > 0){
                            window.open('http://'+ipserver+'/CleoInventory/Reportes/CotizacionMultiplePDF.php?id=' + IDDeLaCot + '&tasa=' + InputTasa.value+'&cantidad='+cantidadACotizar);
                            SubirVentanita();
                            InputTasa.value = "";
                            CheckearInputPaberSiHayAlgo();
                        }else{
                            Toast.fire({
                                icon: "error",
                                title: 'La cantidad a cotizar debe ser mayor a 0'
                            });
                        }
                        
                    }
                }
            }
        }else{
            if(newOptionButton.classList == "BotonDeAqui botonjover"){
                Toast.fire({
                    icon: 'warning',
                    title: 'No se especificó la tasa de cambio'
                })
            }
        }
    }
})

BotonGenerarReporteConTasaEspecifica.addEventListener('click', () => {
    if(isNaN(InputTasa.value)){
        Toast.fire({
            icon: 'warning',
            title: 'Error: "' + InputTasa.value + '" no es un valor válido.'
        })
    }else{
        if(InputTasa.value){
            if(InputTasa.value == 0){
                Toast.fire({
                    icon: 'warning',
                    title: 'La tasa de cambio de ser mayor a cero'
                })
            }else{
                pedazos = document.URL.split('?', 2);
                CosasDelGET = pedazos[1].split('&');
                
                var IDDeLaCot = '0';
                var cantidadACotizar =  document.getElementById('cantidadACotizar_input').value;
                CosasDelGET.forEach(DatoDelGet => {
                    DatoPorParte = DatoDelGet.split('=');
                    console.log(DatoPorParte);
                    if(DatoPorParte[0] == 'id'){
                        IDDeLaCot = DatoPorParte[1];
                    }
                });


                
                if(IDDeLaCot=='0'){
                    Toast.fire({
                        icon: 'warning',
                        title: 'No se encontró la ID de la cotización'
                    })
                }else{
                    if(isNaN(IDDeLaCot)){
                        Toast.fire({
                            icon: "error",
                            title: 'La ID ' + IDDeLaCot + ' es inválida.'
                        });
                    }else{
                        if(cantidadACotizar > 0){
                            window.open('http://'+ipserver+'/CleoInventory/Reportes/CotizacionPDF.php?id=' + IDDeLaCot + '&modo=2&tasa=' + InputTasa.value+'&cantidad='+cantidadACotizar);
                            SubirVentanita();
                            InputTasa.value = "";
                            CheckearInputPaberSiHayAlgo();
                        }else{
                            Toast.fire({
                                icon: "error",
                                title: 'La cantidad a cotizar debe ser mayor a 0'
                            });
                        }
                        
                    }
                }
            }
        }else{
            if(BotonGenerarReporteConTasaEspecifica.classList == "BotonDeAqui botonjover"){
                Toast.fire({
                    icon: 'warning',
                    title: 'No se especificó la tasa de cambio'
                })
            }
        }
    }
})

let BotonMonedaInternacional = document.getElementById('BotonMonedaInternacional');

BotonMonedaInternacional.addEventListener('click', () => {
    SubirVentanita();
    InputTasa.value = "";
    CheckearInputPaberSiHayAlgo();
})

InputTasa.addEventListener('keydown', () => {
    CheckearInputPaberSiHayAlgo();
})


async function CheckearInputPaberSiHayAlgo(){
    for (let i = 0; i < 1; i++) {
        await sleep(i * 1000);
    }

    if(InputTasa.value){
        BotonGenerarReporteConTasaEspecifica.classList = "BotonDeAqui botonjover";
        newOptionButton.classList = "BotonDeAqui botonjover";
    }else{
        BotonGenerarReporteConTasaEspecifica.classList = "BotonDeAqui";
        newOptionButton.classList = "BotonDeAqui";
    }
}


function SoloNumerosFloat(e){  
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789.";
    especiales = "8373846";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;
//permite las telcas de borrar y flechitas
    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
//no permite meter mas de dos puntos (.)
    if(tecla=="." && InputTasa.value.includes(".")){
        return false;
    }
//solo permite dos numeros mas despues del punto
    if(InputTasa.value.includes(".")){
        pedazos = InputTasa.value.split(".",2);
        posicionDelPunto = InputTasa.value.indexOf(".");
        posicionDelTarget = e.target.selectionStart;

        if(pedazos[1].length>1 && posicionDelTarget>posicionDelPunto){
            return false;
        }
    }

    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}

let AlertaGuardadoConExito = document.getElementById('AlertaGuardadoConExito');
let BotonEliminarAlertaGuardadoConExito = document.getElementById('BotonEliminarAlertaGuardadoConExito');

window.addEventListener('load', function(){
    document.getElementById('BotonEliminar')?.addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar esta cotización?",
            html: '<b>¡Atención!</b> esta acción no se puede deshacer.',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed){
                deleteEntity(document.getElementById('idEntity').innerText);
            }
        });
    })
})


async function deleteEntity(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=2&idEntity=${id}&method=delete`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: 'success',
                    title: 'Se ha eliminado la cotizacion #'+id
                })
                
                await sleep(2500);
                window.location.href = `http://${ipserver}/CleoInventory/Ventas`;
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                })
            }
        }
    }catch(error){
        console.log(error);
    }
}


if(AlertaGuardadoConExito != null){
    BotonEliminarAlertaGuardadoConExito.addEventListener('click', () => {
        EliminarAlertaGuardadoConExito();
    })
    
    async function EliminarAlertaGuardadoConExito() {
        AlertaGuardadoConExito.style="transform: scaleX(0);";
        for (let i = 0; i < 2; i++) {
            await sleep(i * 500);
        }
        AlertaGuardadoConExito.remove();
    }
    
    window.addEventListener('load', () => {
        AnimacionDelCartelito();        
    })
    
    async function AnimacionDelCartelito(){
        AlertaGuardadoConExito.style = "box-shadow: 0 0 10px 5px lightgreen;";
        for (let i = 0; i < 3; i++) {
            await sleep(i * 500);
        }
        AlertaGuardadoConExito.style = "";
        for (let i = 0; i < 2; i++) {
            await sleep(i * 500);
        }
        AlertaGuardadoConExito.style = "box-shadow: 0 0 10px 3px lightgreen;";
        for (let i = 0; i < 3; i++) {
            await sleep(i * 500);
        }
        AlertaGuardadoConExito.style = "";
        BotonEliminarAlertaGuardadoConExito.click();
    }
}




document.getElementById('BotonMonedaInternacional').addEventListener('click', function(){
    pedazos = document.URL.split('?', 2);
    CosasDelGET = pedazos[1].split('&');
    
    var IDDeLaCot = '0';
    var cantidadACotizar =  document.getElementById('cantidadACotizar_input').value;
    CosasDelGET.forEach(DatoDelGet => {
        DatoPorParte = DatoDelGet.split('=');
        console.log(DatoPorParte);
        if(DatoPorParte[0] == 'id'){
            IDDeLaCot = DatoPorParte[1];
        }
    });

    

    var cantidadACotizar =  document.getElementById('cantidadACotizar_input').value;
    if(cantidadACotizar>0){
        window.open('http://'+ipserver+'/CleoInventory/Reportes/CotizacionPDF.php?id=' + IDDeLaCot + '&modo=1&tasa=1&cantidad='+cantidadACotizar);
    }else{
        Toast.fire({
            icon: "error",
            title: 'La cantidad a cotizar debe ser mayor a 0'
        });
    }
})


function soloNumerosPositivos(element, e){
    if(isNaN(e.key)){
        return false;
    }
}