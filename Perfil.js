let CajaDerechaDeDatosPersonales = document.getElementById('CajaDerechaDeDatosPersonales');
let BotonPaEditarDatosPersonales = document.getElementById('BotonPaEditarDatosPersonales');
let BotonesDeDatosPersonales = document.getElementById('BotonesDeDatosPersonales');

let BotonPaEditarContrasenia = document.getElementById('BotonPaEditarContrasenia');

let DatosPersonalesTipoDeDocumento = document.getElementById('DatosPersonalesTipoDeDocumento');
let DatosPersonalesCedula = document.getElementById('DatosPersonalesCedula');
let DatosPersonalesNombres = document.getElementById('DatosPersonalesNombres');
let DatosPersonalesSexo = document.getElementById('DatosPersonalesSexo');

var ModoEdicionDatosPersonales = false;
var ModoEdicionContrasenia = false;
var ModoEdicionRespuestas = false;

var tipoDeDocumento = DatosPersonalesTipoDeDocumento.innerText;
var CedulaDelP = DatosPersonalesCedula.innerText;
var NombresDelP = DatosPersonalesNombres.innerText;
var SexoDelP = DatosPersonalesSexo.innerText;

BotonPaEditarDatosPersonales.addEventListener('click', () => {
    if(!ModoEdicionDatosPersonales){
        MostrarEditarDatosPersonales();
    }    
})

BotonPaEditarContrasenia.addEventListener('click', () => {
    if(!ModoEdicionContrasenia){
        MostrarEditarContrasenia();
    }    
})

let ContenidoDeLaSeccionDeContra = document.getElementById('ContenidoDeLaSeccionDeContra');

function MostrarEditarContrasenia(){
    ModoEdicionContrasenia= true;

    ContenidoDeLaSeccionDeContra.innerHTML = `
    <div class="CajaPaLaContrasenia">
        <input maxlength="20" class="CajaNuevaContra" placeholder="********" minlength="3" type="text" form="Contrasenia" name="Contrasenia" id="InputContrasenia">
        <button disabled id="BotonGuardarContrasenia" name="ActualizarContrasenia" form="Contrasenia" class="BotonPaGuardar">Guardar</button>
    </div>
    <div class="EspacioPaVolver">
        <span id="BotonVolverDeContra">Volver</span>
    </div>
    `;

    let InputContrasenia = document.getElementById('InputContrasenia');

    let BotonGuardarContrasenia = document.getElementById('BotonGuardarContrasenia');

    InputContrasenia.addEventListener('keyup', () => {
        console.log(InputContrasenia.value)

        if(InputContrasenia.value){
            if(InputContrasenia.value.length > 7){
                BotonGuardarContrasenia.classList = "BotonPaGuardar Pulsable";
                BotonGuardarContrasenia.removeAttribute("disabled");
            }else{
                BotonGuardarContrasenia.classList = "BotonPaGuardar";
                BotonGuardarContrasenia.setAttribute("disabled", "");
            }
        }else{
            BotonGuardarContrasenia.classList = "BotonPaGuardar";
        }
    })

    let BotonVolverDeContra = document.getElementById('BotonVolverDeContra');

    BotonVolverDeContra.addEventListener('click', () => {
        QuitarEditarContrasenia();
    })

    
}

function QuitarEditarContrasenia(){
    ModoEdicionContrasenia= false;
    ContenidoDeLaSeccionDeContra.innerHTML = `
        <span class="SimulacionDeInput">*********</span>
    `;
}

function MostrarEditarDatosPersonales(){
    ModoEdicionDatosPersonales = true;
    
    CajaDerechaDeDatosPersonales.innerHTML = `
        <div>
            <select name="tipoDeDocumento" id="InputTipoDeDocumento" form="DatosPersonales">
                <option ${((DatosPersonalesTipoDeDocumento.innerText == "V")?"selected":"")} value="V">V</option>
                <option ${((DatosPersonalesTipoDeDocumento.innerText == "J")?"selected":"")} value="J">J</option>
                <option ${((DatosPersonalesTipoDeDocumento.innerText == "E")?"selected":"")} value="E">E</option>
                <option ${((DatosPersonalesTipoDeDocumento.innerText == "G")?"selected":"")} value="G">G</option>
                <option ${((DatosPersonalesTipoDeDocumento.innerText == "P")?"selected":"")} value="P">P</option>
            </select>
            <input maxlength="9" id="InputCedula" value="${CedulaDelP}" name="cedula" type="text" form="DatosPersonales">
        </div>
        <input maxlength="50" value="${NombresDelP}" name="nombres" type="text" form="DatosPersonales" id="InputNombres">
        <select name="sexo" id="InputSexo" form="DatosPersonales">
            <option ${((SexoDelP == "Masculino")?"selected":"")} value="M">Masculino</option>
            <option ${((SexoDelP == "Femenino")?"selected":"")} value="F">Femenino</option>
        </select>
    `;

    BotonesDeDatosPersonales.innerHTML = `
        <button id="BotonVolverDatosPersonales" class="BotonPaVolver">Volver</button>
        <button disabled id="BotonGuardarDatosPersonales" name="ActualizarDatosPersonales" form="DatosPersonales" class="BotonPaGuardar">Guardar</button>
    `;
    EventosDeDatosPersonales();
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}





function EventosDeDatosPersonales(){
    let BotonVolverDatosPersonales = document.getElementById('BotonVolverDatosPersonales');
    let BotonGuardarDatosPersonales = document.getElementById('BotonGuardarDatosPersonales');
    let InputTipoDeDocumento = document.getElementById('InputTipoDeDocumento');
    let InputCedula = document.getElementById('InputCedula');
    let InputNombres = document.getElementById('InputNombres');
    let InputSexo = document.getElementById('InputSexo');

    async function CheckFormDatosPersonales() {
        for (let i = 0; i < 2; i++) {
            await sleep(i * 150);
        }

        var InputSexoValor = ((InputSexo.value == 'M')?"Masculino":"Femenino");

        var CambioEnTipoDeDocumento = tipoDeDocumento != InputTipoDeDocumento.value;
        var CambioEnCedula = CedulaDelP != InputCedula.value;
        var CambioEnNombres = NombresDelP != InputNombres.value;
        var CambioEnSexo = SexoDelP != InputSexoValor;

        if(!InputCedula.value){
            CambioEnCedula = false;
        }
        if(!InputNombres.value){
            CambioEnNombres = false;
        }
        
        

        

        if(CambioEnTipoDeDocumento || CambioEnCedula || CambioEnNombres || CambioEnSexo){
            BotonGuardarDatosPersonales.classList = "BotonPaGuardar Pulsable";
            BotonGuardarDatosPersonales.removeAttribute("disabled");
        }else{
            BotonGuardarDatosPersonales.classList = "BotonPaGuardar";
            BotonGuardarDatosPersonales.setAttribute("disabled", "");
            
        }
    }

    InputTipoDeDocumento.addEventListener('change', () => {
        CheckFormDatosPersonales();
    })
    InputCedula.addEventListener('keyup', () => {
        CheckFormDatosPersonales();
    })
    InputNombres.addEventListener('keyup', () => {
        CheckFormDatosPersonales();
    })
    InputSexo.addEventListener('change', () => {
        CheckFormDatosPersonales();
    })
    
    BotonVolverDatosPersonales.addEventListener('click', () => {
        ModoEdicionDatosPersonales = false;

        BotonesDeDatosPersonales.innerHTML = "";

        CajaDerechaDeDatosPersonales.innerHTML = `
            <p> <span id="DatosPersonalesTipoDeDocumento">${tipoDeDocumento}</span><span>-</span><span id="DatosPersonalesCedula">${CedulaDelP}</span></p>
            <p>${NombresDelP}</p>
            <p>${SexoDelP}</p>
        `;
    })
}






let InputMostrarVentanaDeErrores = document.getElementById('InputMostrarVentanaDeErrores');
let ModalDeErrores = document.getElementById('ModalDeErrores');

InputMostrarVentanaDeErrores.addEventListener('change', () => {
    CambiarVisibilidadDelModalDeErrores();
})

function CambiarVisibilidadDelModalDeErrores(){
    if(InputMostrarVentanaDeErrores.checked){
        ModalDeErrores.style = "display: flex";
    }else{
        ModalDeErrores.style = "display: none";
    }
}



let BotonPagina1 = document.getElementById('BotonPagina1');
let BotonPagina2 = document.getElementById('BotonPagina2');
let BotonPagina3 = document.getElementById('BotonPagina3');

BotonPagina1.addEventListener('click', () => {
    if(BotonPagina1.className == 'BotonesDeAccion BotonPulsable'){
        window.location.href = 'Perfil.php?pagina=1#Card';
    }
})

BotonPagina2.addEventListener('click', () => {
    if(BotonPagina2.className == 'BotonesDeAccion BotonPulsable'){
        window.location.href = 'Perfil.php?pagina=2#Card';
    }
})

BotonPagina3.addEventListener('click', () => {
    if(BotonPagina3.className == 'BotonesDeAccion BotonPulsable'){
        window.location.href = 'Perfil.php?pagina=3#Card';
    }
})

var test = "hola&como&estas#";

arrate = test.split('#',2)

var ParametrosDeURL = new URLSearchParams(window.location.search);
var NumeroDePagina = ParametrosDeURL.get('pagina');

console.log(NumeroDePagina);

let PaginaSectionAMostrar = document.getElementById('SectionPagina'+NumeroDePagina);


window.addEventListener('load', () => {
    CambiarVisibilidadDelModalDeErrores();
    PaginaSectionAMostrar.style = "display: flex;";
    
    
})





let BotonPaEditarRespuestas = document.getElementById('BotonPaEditarRespuestas');

BotonPaEditarRespuestas.addEventListener('click', () => {
    if(!ModoEdicionRespuestas){
        MostrarEditarRespuestas();
    }
})

let ContenidoDeLaSeccionDePreguntas = document.querySelector('.ContenidoDeLaSeccionDePreguntas');
let CajaDeMuestraDeRespuestas = document.getElementById('CajaDeMuestraDeRespuestas');
let CajaDeEdicionDeRespuestas = document.getElementById('CajaDeEdicionDeRespuestas');

let BotonVolverDeRespuestas = document.getElementById('BotonVolverDeRespuestas');

function MostrarEditarRespuestas(){
    ModoEdicionRespuestas = true;

    CajaDeMuestraDeRespuestas.style = "display: none;"
    CajaDeEdicionDeRespuestas.style = "display: flex;";
   
}


BotonVolverDeRespuestas.addEventListener('click', () => {
    ModoEdicionRespuestas = false;
    CajaDeMuestraDeRespuestas.style = "display: flex;"
    CajaDeEdicionDeRespuestas.style = "display: none;";
})

let InputRespuesta1 = document.getElementById('InputRespuesta1');
let InputRespuesta2 = document.getElementById('InputRespuesta2');
let InputRespuesta3 = document.getElementById('InputRespuesta3');
let SelectPregunta1 = document.getElementById('SelectPregunta1');
let SelectPregunta2 = document.getElementById('SelectPregunta2');
let SelectPregunta3 = document.getElementById('SelectPregunta3');

const IDPregunta1Guardada = SelectPregunta1.value;
const IDPregunta2Guardada = SelectPregunta2.value;
const IDPregunta3Guardada = SelectPregunta3.value;

SelectPregunta1.addEventListener('change', () => {
    CheckFormRespuestas();
})
SelectPregunta2.addEventListener('change', () => {
    CheckFormRespuestas();
})
SelectPregunta3.addEventListener('change', () => {
    CheckFormRespuestas();
})
InputRespuesta1.addEventListener('keyup', () => {
    CheckFormRespuestas();
})
InputRespuesta2.addEventListener('keyup', () => {
    CheckFormRespuestas();
})
InputRespuesta3.addEventListener('keyup', () => {
    CheckFormRespuestas();
})

let BotonGuardarRespuestas = document.getElementById('BotonGuardarRespuestas');

function CheckFormRespuestas(){
    

    if(InputRespuesta1.value || InputRespuesta2.value || InputRespuesta3.value || IDPregunta1Guardada != SelectPregunta1.value || IDPregunta2Guardada != SelectPregunta2.value || IDPregunta3Guardada != SelectPregunta3.value){
        BotonGuardarRespuestas.classList = "BotonPaGuardar Pulsable";
        BotonGuardarRespuestas.removeAttribute("disabled");
    }else{
        BotonGuardarRespuestas.classList = "BotonPaGuardar";
        BotonGuardarRespuestas.setAttribute("disabled", "");
    }
}