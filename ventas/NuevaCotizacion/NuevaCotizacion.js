let InputCalendario = document.getElementById('CalendarioFlotante');
let PFechaVencimiento = document.getElementById('FechaVencimiento');
let InputNroDeDias = document.getElementById('CampoNumeroDeDias');
let SelectTiempoLimitado = document.getElementById('SelectTiempoLimitado');
let LabelDias = document.getElementById('LabelDias');
let FechaActual = new Date();

let InputTipoDeRifCliente = document.getElementById('TipoDeRifCliente');
let InputIdCliente = document.getElementById('IdCliente');
let InputNombreCliente = document.getElementById('NombreCliente');
let InputTelefonoCliente = document.getElementById('TelefonoCliente');
let InputCorreoCliente = document.getElementById('CorreoCliente');
let InputDireccionCliente = document.getElementById('DireccionCliente');


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



InputTipoDeRifCliente.addEventListener('focus', () => {
    InputTipoDeRifCliente.blur();
})

InputIdCliente.addEventListener('focus', () => {
    InputIdCliente.blur();
})

InputNombreCliente.addEventListener('focus', () => {
    InputNombreCliente.blur();
})

InputTelefonoCliente.addEventListener('focus', () => {
    InputTelefonoCliente.blur();
})

InputCorreoCliente.addEventListener('focus', () => {
    InputCorreoCliente.blur();
})

InputDireccionCliente.addEventListener('focus', () => {
    InputDireccionCliente.blur();
})


window.addEventListener('load', () => {
    EstablecerLimiteDeTiempo();
    MostrarAlUsuarioElModalDeError(MostrarModalDeError.checked);
})

SelectTiempoLimitado.addEventListener('change', () => {
    EstablecerLimiteDeTiempo();
})

InputCalendario.addEventListener('change', () => {
    ActualizarDiasSegunCalendario();
    CambiarDiaSingularPlurar();
})

InputNroDeDias.addEventListener('keyup', () => {
    ActualizarCalendarioSegunDias();
    CambiarDiaSingularPlurar();
})

InputNroDeDias.addEventListener('blur', () => {
    VerificiarDiaPositivo();
})

//////////// Funciones ////////////

function SoloNumerosInt(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8¬37¬38¬46";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}

function CambiarDiaSingularPlurar(){
    if(InputNroDeDias.value == '1'){
        LabelDias.innerText = 'Día';
    }else{
        LabelDias.innerText = 'Días';
    }
}


///////////7 revisar bien cuando sea 1 de enero
function VerificiarDiaPositivo(){
    if(InputNroDeDias.value < 1){
        InputNroDeDias.value = 0;
        var DiaPalCalendario = (FechaActual.getDate()<10)?'0' + FechaActual.getDate() : FechaActual.getDate();
        var MesPalCalendario = ((FechaActual.getMonth() + 1)<10)?'0' + (FechaActual.getMonth() + 1):(FechaActual.getMonth() + 1);
        var AnioPalCalendario = FechaActual.getFullYear();
        
        PFechaVencimiento.innerHTML = FechaActual.getDate() + '/' + MesPalCalendario + '/' + FechaActual.getFullYear();
        InputCalendario.value = AnioPalCalendario + '-' + MesPalCalendario + '-' + DiaPalCalendario;
    }
}

function EstablecerLimiteDeTiempo(){
    if(SelectTiempoLimitado.value == 'Si'){
        InputNroDeDias.disabled = false;
        InputNroDeDias.style.opacity = 1;
        InputCalendario.disabled = false;
        LabelDias.style.opacity = 1;

        ActualizarCalendarioSegunDias();
    }else{
        InputNroDeDias.disabled = true;
        InputNroDeDias.style.opacity = 0.7;
        InputNroDeDias.value = 0;
        InputCalendario.disabled = true;
        LabelDias.style.opacity = 0.7;

        PFechaVencimiento.innerText = 'Esta cotización no tiene una fecha de vencimiento.';
        PFechaVencimiento.style.opacity = 0.7;
    }
}



function ActualizarDiasSegunCalendario(){
    var PedazosDeLaFecha = InputCalendario.value.split('-', 3);
        
        
        FechaDelInput = new Date(InputCalendario.value);
        var DiferenciaEnMs = FechaDelInput.getTime() - FechaActual.getTime();
        var DiferenciaDeDiasFlotante = DiferenciaEnMs / (1000 * 3600 * 24);
        var DiferenciaEnDias = Math.ceil(DiferenciaDeDiasFlotante);
        InputNroDeDias.value = DiferenciaEnDias;
    if(DiferenciaEnDias < 1){
        VerificiarDiaPositivo();
    }else{
        var FechaNuevaLista = PedazosDeLaFecha[2] + '/' + PedazosDeLaFecha[1] + '/' + PedazosDeLaFecha[0];
        PFechaVencimiento.innerText = FechaNuevaLista;
        PFechaVencimiento.style.opacity = 1;
    }
}

function ActualizarCalendarioSegunDias(){
    var NuevaFecha = new Date(FechaActual.getTime() + (86400000 * InputNroDeDias.value));
    
    var AnioPalCalendario = NuevaFecha.getFullYear();
    var MesPalCalendario = ((NuevaFecha.getMonth() + 1)<10)?'0' + (NuevaFecha.getMonth() + 1):(NuevaFecha.getMonth() + 1);
    var DiaPalCalendario = (NuevaFecha.getDate() < 10)?'0' + NuevaFecha.getDate() : NuevaFecha.getDate();
    InputCalendario.value = AnioPalCalendario + '-' + MesPalCalendario + '-' + DiaPalCalendario;
    PFechaVencimiento.innerText = DiaPalCalendario + '/' + MesPalCalendario + '/' + AnioPalCalendario;
    PFechaVencimiento.style.opacity = 1;
}



let MostrarModalDeError = document.querySelector('.MostrarModalDeError');
let ModalDeError = document.querySelector('.ModalDeError');

MostrarModalDeError.addEventListener('change', () => {
    MostrarAlUsuarioElModalDeError(MostrarModalDeError.checked);
})

function MostrarAlUsuarioElModalDeError(valor){
    if(valor){
        ModalDeError.style.display= 'flex';
    }else{
        ModalDeError.style.display= 'none';
    }
}

let Utilidades = document.getElementById('Utilidades');



function SoloDosNumeros(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8373846";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }

    if(Utilidades.value.length > 1){
        return false;
    }
}

let InputIVA = document.getElementById('InputIVA');
let InputCASalario = document.getElementById('InputCASalario');
let TituloSalario = document.getElementById('TituloSalario');

InputCASalario.addEventListener('keyup', () => {
    if(InputCASalario.value){
        TituloSalario.innerHTML = "Asociado al salario (" + InputCASalario.value + "%)"    
    }else{
        TituloSalario.innerHTML = "Asociado al salario (0%)"
    }

    ActualizarTotales();
})


function SoloDosNumeros3(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8373846";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }

    if(InputCASalario.value.length > 3){
        return false;
    }
}


function SoloDosNumeros2(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8373846";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }

    if(InputIVA.value.length > 1){
        return false;
    }
}


CeroNoPermitido(InputIVA);
CeroNoPermitido(Utilidades);

function CeroNoPermitido(Input){
    Input.addEventListener('blur', () => {
        if(!Input.value){
            Input.value = '0';
        }
    })
}

let TituloUtilidades = document.getElementById('TituloUtilidades');
let TituloIVA = document.getElementById('TituloIVA');

Utilidades.addEventListener('keyup', () => {
    if(Utilidades.value){
        TituloUtilidades.innerHTML = "Utilidades (" + Utilidades.value + "%)"    
    }else{
        TituloUtilidades.innerHTML = "Utilidades (0%)"
    }

    ActualizarTotales();
})

InputIVA.addEventListener('keyup', () => {
    if(InputIVA.value){
        TituloIVA.innerHTML = "I.V.A. (" + InputIVA.value + "%)"
    }else{
        TituloIVA.innerHTML = "I.V.A. (0%)"
    }

    ActualizarTotales();
    
})