let ModalAgregarCliente = document.getElementById('ModalAgregarCliente');
let VentanaFlotanteAgregarCliente = document.getElementById('VentanaFlotanteAgregarCliente');
let BotonBuscarCliente = document.getElementById('BotonBuscarCliente');
let BotonCerrarVentanaAgregarCliente = document.getElementById('BotonCerrarVentanaAgregarCliente');
let InputDeBuscadorDeClientes = document.getElementById('InputDeBuscadorDeClientes');
let BotonFiltrarCliente = document.getElementById('BotonFiltrarCliente');
let ResultadosDeLaBusquedaDeClientes = document.getElementById('ResultadosDeLaBusqueda');
let InputIDDelCliente = document.getElementById('IdCliente');
let EspacioDeTarjetaDeCliente = document.getElementById('EspacioDeTarjetaDeCliente');
let BotonRemoverCliente = document.getElementById('BotonRemoverCliente');
let TarjetaNoCliente = document.querySelector('.NoCliente');
let TarjetaSiCliente = document.querySelector('.SiCliente');
let ImagenDelCliente = document.getElementById('ImagenDelCliente');
let CampoTipoDeRifCliente = document.getElementById('TipoDeRifCliente');
let CampoIdCliente = document.getElementById('IdCliente');
let CampoNombreCliente = document.getElementById('NombreCliente');
let CampoTelefonoCliente = document.getElementById('TelefonoCliente');
let CampoDireccionCliente = document.getElementById('DireccionCliente');
let CampoCorreoCliente = document.getElementById('CorreoCliente');




BotonRemoverCliente.addEventListener('click', () => {
    RemoverCliente();
})

function RemoverCliente(){
    InputIDDelCliente.value = '';
    MostrarTarjetaDeCliente(false);
    LimpiarNombre();
}

ResultadosDeLaBusquedaDeClientes.addEventListener('click', function(event) {
    if(event.target.tagName.toLowerCase() == 'button' || event.target.tagName.toLowerCase() == 'i'){
        InputIDDelCliente.value = event.target.getAttribute('rif');
        MostrarModalAgregarCliente(false);
        MostrarTarjetaDeCliente(true);
        AgregarCliente(event.target.getAttribute('rif'));
    }
})

function AgregarCliente(rif){
    MostrarDatosDelClienteElegido(rif);
}

function MostrarTarjetaDeCliente(valor){
    if(valor){
        EspacioDeTarjetaDeCliente.style.height = '270px';
        TarjetaNoCliente.classList = 'NoCliente Subir';
        TarjetaSiCliente.classList = 'SiCliente Subir';

        


    }else{
        EspacioDeTarjetaDeCliente.style.height = '190px';
        TarjetaNoCliente.classList = 'NoCliente';
        TarjetaSiCliente.classList = 'SiCliente';

        CampoIdCliente.value = "";
        
        
        
    }
}

function LimpiarNombre(){
    InputNombreDeLaCot.value = 'Cotización sin nombre';
}



function MostrarModalAgregarCliente(valor){
    
    if(valor){
        ActualizarLista();
        ModalAgregarCliente.style.display = 'flex';
        InputDeBuscadorDeClientes.focus();
        console.log('esto');
    }else{
        ModalAgregarCliente.style.display = 'none';
    }
}



window.addEventListener('load', () => {
    

    if(CampoIdCliente.value){
        MostrarTarjetaDeCliente(true);
        AgregarCliente(CampoIdCliente.value);
    }else{
        MostrarTarjetaDeCliente(false);
    }

    if(!InputNombreDeLaCot.value){
        LimpiarNombre()
    }
    
})

BotonFiltrarCliente.addEventListener('click', () => {
    ActualizarLista();
})

BotonCerrarVentanaAgregarCliente.addEventListener('click', () => {
    MostrarModalAgregarCliente(false);
})

BotonBuscarCliente.addEventListener('click', () => {
    MostrarModalAgregarCliente(true);
})

ModalAgregarCliente.addEventListener('click', (e) => {
    ModalAgregarCliente.style.display = 'none';
})

VentanaFlotanteAgregarCliente.addEventListener('click', (e) => {
    e.stopPropagation();
})

function ActualizarLista(){
    ConsultarClientes(InputDeBuscadorDeClientes.value);
    
}

InputDeBuscadorDeClientes.addEventListener("keyup", function(event) {
    event.preventDefault();
    if ((event.keyCode === 13) || (InputDeBuscadorDeClientes.value === "")) {
        BotonBuscarCliente.click();
    }
    
});

let InputNombreDeLaCot = document.getElementById('InputNombreDeLaCot');

function zerofill(string, max){
    for (let index = 0; max > string.length; index++) {
        string = '0'+string;
    }
    return string;
}

let MostrarDatosDelClienteElegido = async(rif) => {
    
    try{
        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Clientes.php?descripcion=' + rif);
        if(respuesta.status === 200){
            PeticionConFiltros = await respuesta.json();

            ImagenDelCliente.src = "../../Imagenes/clientes/" + ((PeticionConFiltros.objetos[0].ULRImagen)?PeticionConFiltros.objetos[0].ULRImagen:"ImagenPredefinida_Clientes.png");
            CampoTipoDeRifCliente.value = PeticionConFiltros.objetos[0].tipoDeDocumento;
            CampoIdCliente.value = zerofill(PeticionConFiltros.objetos[0].rif, 9);
            CampoNombreCliente.value = PeticionConFiltros.objetos[0].nombre;
            CampoTelefonoCliente.value = PeticionConFiltros.objetos[0].telefono1 + ((PeticionConFiltros.objetos[0].telefono2)?' / ' + PeticionConFiltros.objetos[0].telefono1:"");
            CampoCorreoCliente.value = PeticionConFiltros.objetos[0].correo;
            CampoDireccionCliente.value = PeticionConFiltros.objetos[0].direccion;
            
            
            if(!InputNombreDeLaCot.value || InputNombreDeLaCot.value == 'Cotización sin nombre'){
                InputNombreDeLaCot.value = 'Cotización de ' + PeticionConFiltros.objetos[0].nombre;
            }
        }
    }catch(error){
        console.log(error);
    }
}

let TamanioMaxDelDiv;
let TamanioDeResultados;

window.addEventListener('resize', () => {
    if(ModalAgregarCliente.style.display == 'flex'){
        TamanioMaxDelDiv = Math.round(window.innerHeight * 0.8) - 155;
        console.log('max de l div: ' + TamanioMaxDelDiv);
        console.log('resul: ' + TamanioDeResultados);

        console.log('XDDDDDD')
        if(TamanioMaxDelDiv < TamanioDeResultados){
            ResultadosDeLaBusqueda.style.height = TamanioMaxDelDiv + 'px';
        }else{
            ResultadosDeLaBusqueda.style.height = TamanioDeResultados + 'px';
        }
    }
})



let ConsultarClientes = async(descripcion) => {
    document.getElementById('ResultadosDeLaBusqueda').innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    try{

        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Clientes.php?descripcion=' + descripcion);
        
        if(respuesta.status === 200){
            
            PeticionConFiltros = await respuesta.json();

            let ResultadosDeLaBusqueda = document.getElementById('ResultadosDeLaBusqueda');
            console.log('arreglando bugs')
            ResultadosDeLaBusqueda.style = "height: calc(70vh - 90px);";
            if(PeticionConFiltros.objetos == undefined){
                console.log('no hay nada');
                
            }else{
                //ResultadosDeLaBusqueda.style = "";
            }

            let Clientes = "";
            
            
            ResultadosDeLaBusqueda.innerHTML = `
            <div class="Flex-gap2 HoverVino TablaDeClientesVacia">
                <span>No hay clientes para mostrar...</span>
            </div>
            `;

            PeticionConFiltros.objetos.forEach(cliente => {
                
                Clientes = Clientes + `
                <div class="Flex-gap2 HoverVino">
                    <span class="Celda ColumnaImagen">
                        <img src="../../Imagenes/Clientes/${(cliente.ULRImagen == null)?'ImagenPredefinida_Clientes.png':cliente.ULRImagen}" alt="">
                    </span>
                    <span class="Celda ColumnaRIF">${cliente.tipoDeDocumento} - ${zerofill(cliente.rif, 9)}</span>
                    <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">${cliente.nombre}</span>
                    <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                        <button title="Seleccionar cliente" class="BontonSeleccionar" rif="${cliente.rif}">
                            <i class="fi-rr-user-add" rif="${cliente.rif}"></i>
                        </button>
                    </div>
                </div>`;

            });

            ResultadosDeLaBusqueda.innerHTML = Clientes;
            if(PeticionConFiltros.objetos.length > 0){                
                TamanioMaxDelDiv = Math.round(window.innerHeight * 0.8) - 155;
                TamanioDeResultados = (ResultadosDeLaBusqueda.children.length * 75) + (1 * (ResultadosDeLaBusqueda.children.length - 1));
            }

            console.log('nmms bro')
            console.log('A: ' + TamanioMaxDelDiv)
            console.log('B: ' + TamanioDeResultados)
            
            if(TamanioMaxDelDiv < TamanioDeResultados){
                console.log('A: ')
                //ResultadosDeLaBusqueda.style.height = TamanioMaxDelDiv + 'px';
            }else{
                console.log('B: ')
                ResultadosDeLaBusqueda.style.height = TamanioDeResultados + 'px';
            }
            
        }

    }catch(error){
        console.log(error);
    }
    
}
