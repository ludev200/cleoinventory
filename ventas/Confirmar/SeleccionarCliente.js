let Modal_SeleccionarCliente = document.getElementById('Modal_SeleccionarCliente');
let BotonCerrarVentana_SeleccionarCliente = document.getElementById('BotonCerrarVentana_SeleccionarCliente');
let VentadaModal_SeleccionarCliente = document.getElementById('VentadaModal_SeleccionarCliente');
let BotonDelAsideSeleccionarCliente = document.getElementById('BotonDelAsideSeleccionarCliente');
let BotonFiltrarCliente = document.getElementById('BotonFiltrarCliente');
let InputDeBuscadorDeClientes = document.getElementById('InputDeBuscadorDeClientes');
let AquiSePonenLosClientes = document.getElementById('AquiSePonenLosClientes');


BotonFiltrarCliente.addEventListener('click', async function(){
    await ConsultarAPIPorClientes(InputDeBuscadorDeClientes.value);
    await EnlistarClientesDeUltimaBusqueda();
})
InputDeBuscadorDeClientes.addEventListener('keyup', function(e){
    if(e.keyCode == 13){
        BotonFiltrarCliente.click();
    }else{
        if(!InputDeBuscadorDeClientes.value){
            BotonFiltrarCliente.click();
        }
    }
})

async function EnlistarClientesDeUltimaBusqueda(){
    AquiSePonenLosClientes.innerHTML = `
    
    `;

    if(ClientesDeUltimaConsulta.length){
        ClientesDeUltimaConsulta.forEach( element => {
            ClienteYaEnCoti = (ID_ClienteEnCoti.value == element.rif);
            if(ClienteYaEnCoti){
                BotonAccionativo = `
                <div class="BontonSeleccionar slcclibtn" title="Quitar cliente" id="rifcliente-">
                    <i class="fi-rr-cross-small"></i>
                </div>
                `;
            }else{
                BotonAccionativo = `
                <div class="BontonSeleccionar slcclibtn" title="Seleccionar cliente" id="rifcliente-${element.rif}">
                    <i class="fi-rr-user-add"></i>
                </div>
                `;
            }
    
            AquiSePonenLosClientes.innerHTML = AquiSePonenLosClientes.innerHTML + `
            <div class="RowDeClientes">
                <celda class="ColumnaImagen">
                    <img src="../../Imagenes/Clientes/${(element.ULRImagen?element.ULRImagen:'ImagenPredefinida_Clientes.png')}" alt="">
                </celda>
                <celda class="ColumnaRIF">
                    ${element.tipoDeDocumento} - ${zerofill(element.rif, 9)}
                </celda>
                <celda class="ColumnaNombre3">
                    ${element.nombre}
                </celda>
                <celda class="ColumnaSeleccionar">
                    ${BotonAccionativo}
                </celda>
            </div>
            `;
        });
    }else{
        AquiSePonenLosClientes.innerHTML = `
        <div class="estebetavacio">
        <span>No hay clientes a mostrar</span>
    <div>
        `;
    }
    

    


    
    document.querySelectorAll('.slcclibtn').forEach( element => {
        
        element.addEventListener('click', function(){
            ped = element.id.split('-');
            console.log(ped[1])
            console.log(ID_ClienteEnCoti)
            ID_ClienteEnCoti.value = ped[1];
            
            //ID_ClienteEnCoti.value = element.id;
            MostrarModalSeleccionarCliente(false);
            EstablecerCliente(ID_ClienteEnCoti.value);
        })
    })
}






//VISIBILIDAD DEL MODAL

BotonDelAsideSeleccionarCliente.addEventListener('click', function() {
    MostrarModalSeleccionarCliente(true);
})
Modal_SeleccionarCliente.addEventListener('click', function(){
    MostrarModalSeleccionarCliente(false);
})
BotonCerrarVentana_SeleccionarCliente.addEventListener('click', function(){
    MostrarModalSeleccionarCliente(false);
})
VentadaModal_SeleccionarCliente.addEventListener('click', (e) => {
    e.stopPropagation();
})


async function MostrarModalSeleccionarCliente(valor){
    
    if(valor){
        Modal_SeleccionarCliente.style = "display: flex";
        //BotonBuscarCompras.click();
        EnlistarClientesDeUltimaBusqueda();
        await EsperarMS(50);
        VentadaModal_SeleccionarCliente.className ="VentanaFlotante";
    }else{
        //Input_CotiSeleccionada.value="";
        VentadaModal_SeleccionarCliente.className ="VentanaFlotante OcultarModal";
        await EsperarMS(100);
        Modal_SeleccionarCliente.style = "";

    }
}