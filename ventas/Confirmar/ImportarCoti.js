let Modal_ImportarCompra = document.getElementById('Modal_ImportarCompra');
let VentadaModal_ImportarCompra = document.getElementById('VentadaModal_ImportarCompra');
let BotonDelAside_ImportarCompra = document.getElementById('BotonDelAside_ImportarCompra');
let BotonCerrarVentana_ImportarCompra = document.getElementById('BotonCerrarVentana_ImportarCompra');
let Input_BuscadorDeCoti = document.getElementById('Input_BuscadorDeCoti');
let ListaDeComprasConsultadas = document.getElementById('ListaDeComprasConsultadas');
let Input_CotiSeleccionada = document.getElementById('Input_CotiSeleccionada');
let PrevisualizacionDeCoti = document.getElementById('PrevisualizacionDeCoti');
let BotonBuscarCotis = document.getElementById('BotonBuscarCotis');
let ID_CotiAConfirmar = document.getElementById('ID_CotiAConfirmar');
let BotonTextoImportar = document.getElementById('BotonTextoImportar');
let InputTituloDeCoti = document.getElementById('InputTituloDeCoti');
let Input_CAS = document.getElementById('Input_CAS');
let Input_Utilidades = document.getElementById('Input_Utilidades');
let Input_IVA = document.getElementById('Input_IVA');
let ID_ClienteEnCoti = document.getElementById('ID_ClienteEnCoti');
let CartaDeCliente = document.getElementById('CartaDeCliente');
let ProductosAVender = document.getElementById('ProductosAVender');
let EspacioDeRowsDeLaTabla = document.getElementById('EspacioDeRowsDeLaTabla');
let Span_totaltotal = document.getElementById('Span_totaltotal');
let ButtonIrAPaso2 = document.getElementById('ButtonIrAPaso2');
const BotonPaGuardarTodo = document.getElementById('ButtonGuardar');
const FormularioPalPost = document.getElementById('FormularioPalPost');

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

let CotizacionesEnEspera = [];
let CotizacionesDeUltimaConsulta = [];
let ClientesTotales = [];
let ClientesDeUltimaConsulta = [];
let ProductosDeCoti = [];
let Indexed_CuerpoDeCotiOriginal = [];

let TodosLosProductos = [];
let ProductosDeUltimaConsulta = [];

let CantidadDeProductoEnCoti = [];


Input_BuscadorDeCoti.addEventListener('keyup', function(e){
    if(e.keyCode == 13){
        BotonBuscarCotis.click();
    }else{
        if(!Input_BuscadorDeCoti.value){
            BotonBuscarCotis.click();
        }
    }
})

BotonBuscarCotis.addEventListener('click', async function(){
    await ConsultarAPIPorCotizaciones(Input_BuscadorDeCoti.value);
    await EnlistarCotizacionesDeUltimaBusqueda();
})



window.addEventListener('load', async function(){
    await ConsultarAPIPorCotizaciones('');
    await ConsultarAPIPorClientes('');
    await ConsultarAPIPorProductos('',0);
    await ConsultarAPIPorAlmacenes('');

    this.document.getElementById('SelectCategoriaABuscar').addEventListener('change', function(){
        document.getElementById('BotonBuscarProductos').click();
    })

    BotonPaGuardarTodo.addEventListener('click', function(){
        if(BotonPaGuardarTodo.className == 'BotonContinuarDisponible'){
            FormularioPalPost.submit();
        }
    })

    const Input_CotiSeleccionada = document.getElementById('Input_CotiSeleccionada');
    if(Input_CotiSeleccionada.value){
        if(CotizacionesEnEspera[Input_CotiSeleccionada.value]!=undefined){
            MostrarModalImportarCompra(true);
            CargarVisualizacionDeCotiSeleccionada(Input_CotiSeleccionada.value);
            await sleep(500);
            document.getElementById('BotonImportarCoti').click();
        }
    }
})

async function ConsultarAPIPorProductosDeCoti(id){
    
    try {
        console.log('http://'+ipserver+'/CleoInventory/API/API_ProductosDeCot.php?idCotizacion='+id);
        let consulta = await fetch('http://'+ipserver+'/CleoInventory/API/API_ProductosDeCot.php?idCotizacion='+id);

        if(consulta.status === 200){
            ObjetosRecibidos = await consulta.json();
        }

        if(ObjetosRecibidos.objetos == undefined){
            ProductosDeCotRecibidos = [];
        }else{
            ProductosDeCotRecibidos = ObjetosRecibidos.objetos;
        }

        ProductosDeCoti[id] = ProductosDeCotRecibidos;


    }catch (error){
        console.log(error);
    }
}

async function ConsultarAPIPorClientes(descripcion){
    document.getElementById('AquiSePonenLosClientes').innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    try{
        let consulta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Clientes.php?descripcion='+descripcion);

        if(consulta.status === 200){
            ObjetosRecibidos = await consulta.json();

            
            if(ObjetosRecibidos.objetos == undefined){
                ClientesRecibidos = [];
            }else{
                ClientesRecibidos = ObjetosRecibidos.objetos;
            }

            

            if(!ClientesTotales.length){
                ClientesRecibidos.forEach( element => {
                    ClientesTotales[element.rif] = element;
                })
            }
            ClientesDeUltimaConsulta = ClientesRecibidos;        

        }
        
    }catch(error){
        console.log(error);
    }
}

async function ConsultarAPIPorCotizaciones(descripcion){
    document.getElementById('ListaDeComprasConsultadas').innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    try{
        let consulta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Cotizaciones.php?estado=33&descripcion='+descripcion);

        if(consulta.status === 200){
            ObjetosRecibidos = await consulta.json();
            
            if(ObjetosRecibidos.objetos == undefined){
                CotisRecibidas = [];
            }else{
                CotisRecibidas = ObjetosRecibidos.objetos;
            }

            if(!CotizacionesEnEspera.length){
                CotisRecibidas.forEach( element => {
                    CotizacionesEnEspera[element.id] = element;
                })
            }

            CotizacionesDeUltimaConsulta = CotisRecibidas;
        }
    }catch(error){
        console.log(error);
    }
}


async function EnlistarCotizacionesDeUltimaBusqueda(){
    ListaDeComprasConsultadas.innerHTML = `
    
    `;


    if(CotizacionesDeUltimaConsulta.length){
        CotizacionesDeUltimaConsulta.forEach( element => {
            
            ListaDeComprasConsultadas.innerHTML = ListaDeComprasConsultadas.innerHTML + `
            <row id="RowCotiConsultada-${element.id}" class="RowCotiConsultada">
                <celda class="ColumnaID">${element.id}</celda>
                <celda class="ColumnaDescripcion">${element.nombre}</celda>
                <celda class="ColumnaCantidad">${(element.cedulaCliente? zerofill(element.cedulaCliente, 9):'<span style="color: gray;">Ninguno</span>')}</celda>
            </row>
            `;
        })
    }else{
        ListaDeComprasConsultadas.innerHTML = `
        <div class="estebetavacio">
            <span>No hay cotizaciones a mostrar</span>
        </div>
        `;
    }
    

    CrearGrupoDeRowsSeleccionables(document.querySelectorAll('.RowCotiConsultada'));
}

function CrearGrupoDeRowsSeleccionables(Grupo){
    Grupo.forEach(row => {
        row.addEventListener('click', function(){
            Grupo.forEach( element => {
                element.classList.remove('ProductoSeleccionado')
            });

            row.classList.add('ProductoSeleccionado');
            trozos = row.id.split('-');

            if(trozos[0] == 'RowCotiConsultada'){
                Input_CotiSeleccionada.value = trozos[1];
                CargarVisualizacionDeCotiSeleccionada(trozos[1]);
            }
            
        })
    });
}

async function CargarVisualizacionDeCotiSeleccionada(ID){
    Obj_Cotizacion = CotizacionesDeUltimaConsulta.find( (element) => {
        return element.id == ID;
    })


    TieneCliente = false;
    if(Obj_Cotizacion.cedulaCliente){
        ClienteDeLaCoti = ClientesTotales[Obj_Cotizacion.cedulaCliente];
        TieneCliente = true;
    }

    Expira = false;
    if(Obj_Cotizacion.fechaExpiracion){
        FechaExpiracion = Obj_Cotizacion.fechaExpiracion;
        trozos = FechaExpiracion.split('-');
        FechaExpiracion = trozos[2] + '-' + trozos[1] + '-' + trozos[0];
        Expira = true;
    }
    
    if(!ProductosDeCoti[ID]){
        await ConsultarAPIPorProductosDeCoti(ID);
    }

    Productos = ProductosDeCoti[ID];
    
    ProductosEnRow = '';
    PrecioAcumuladoDeProductos = 0;
    CostoAsociadoAlSalario = 0;
    
    CantidadDeProductoEnCoti = [];
    Indexed_CuerpoDeCotiOriginal = [];
    Productos.forEach( element => {
        
        Indexed_CuerpoDeCotiOriginal[element.idProducto] = element;
        cantidad = element.cantidad;

        
        if(element.idCategoria == 2){
            cantidad = 'x ' + cantidad;
        }else{
            if(element.idCategoria == 3 || element.idCategoria == 4){
                ped = cantidad.toString().split('.');
                cantidad = ped[0] + ' x ' + ped[1];

                if(element.idCategoria == 3){
                    CostoAsociadoAlSalario = CostoAsociadoAlSalario + element.precioMultiplicado;
                }
            }else{
                cantidad = 'x ' + cantidad;
            }
        }


        ProductosEnRow = ProductosEnRow + `
        <div class="RowDeCambio">
            <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Imagen">
                <img class="Imagen_ProductoDeCompra" src="../../Imagenes/Productos/${(element.ULRImagen?element.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
            </div>
            <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Nombre">
                ${element.nombre}
            </div>
            <div class="Celda_ProductoDeCompra CeldaRowDeCambio_PrecioU">
                ${element.precioUnitario.toFixed(2)}$
            </div>
            <div class="Celda_ProductoDeCompra CeldaRowDeCambio_CantidadXD">
                ${cantidad}
            </div>
        </div>
        `;

        PrecioAcumuladoDeProductos = PrecioAcumuladoDeProductos + Number(element.precioMultiplicado.toFixed(2));
    })

    
    
    CostoAsociadoAlSalario = (CostoAsociadoAlSalario * Obj_Cotizacion.CASalario * 0.01);
    CostoDeProductosConCAS = CostoAsociadoAlSalario + PrecioAcumuladoDeProductos;
    Utilidades = (CostoDeProductosConCAS * Obj_Cotizacion.pUtilidades * 0.01);

    Subtotal = CostoDeProductosConCAS + Utilidades;
    iva = (Subtotal * Obj_Cotizacion.pIVA * 0.01);

    TotalTotal = Subtotal + iva;

    

    

    PrevisualizacionDeCoti.innerHTML = `
    <div class="CompraSiSeleccionada">
        <span class="Titulo">${Obj_Cotizacion.nombre}</span>
        <div class="CajaDeDatosDeLaCompra">
            <img src="../../Imagenes/iconoDelMenu_Ventas.png" alt="">
            <div class="NombreDelDato DatosDeLaCompra">
                <p>Cliente</p>
                <p>Nro productos</p>
                <p>Válido hasta</p>
            </div>
            <div class="DatosDeLaCompra">
                <b>:</b>
                <b>:</b>
                <b>:</b>
            </div>
            <div class="DatosDeLaCompra">
                <p>${(TieneCliente?ClienteDeLaCoti.nombre:'<span style="color: gray;">No especificado</span>')}</p>
                <p>${Productos.length}</p>
                <p>${(Expira?FechaExpiracion:'<span style="color: gray;">No expira</span>')}</p>
            </div>
        </div>
        <div class="TablaDeCambios">
            <div class="EspacioDeRowDeCambio mostly-customized-scrollbar">    
                ${ProductosEnRow}
            </div>
        </div>
        <b id="SpanPrecioTotal" title="Costo de productos + CAS + Utilidades + IVA" class="totaldeprev">Total: 0.00$ </b>
        <div class="EspacioBotonImportarCompra">
            ${(ID == ID_CotiAConfirmar.value?'<button id="BotonQuitarCotiInportada" class="BotonQuitarOrden"><i class="fi-rr-cross-small"></i> Quitar orden</button>':'<button id="BotonImportarCoti" class="BotonImportarOrden"> <i class="fi-rr-arrow-alt-down"></i> Importar cotización</button>')}
            ${(ID_CotiAConfirmar.value && ID_CotiAConfirmar.value != Input_CotiSeleccionada.value)?'<i class="IconoConInfo fi-sr-comment-exclamation"><div class="TextoDeIconoConInfo">Importar esta cotización eliminará la cotización cargada previamente, junto a sus productos, el cliente y su configuración.</div></i>':''}
            
        </div>
    </div>
    `;

    document.getElementById('SpanPrecioTotal').innerText = 'Total: ' + TotalTotal.toFixed(2) + '$';

    ProductosAVender.value = '';
    if(ID == ID_CotiAConfirmar.value){
        document.getElementById('BotonQuitarCotiInportada').addEventListener('click', function(){
            ID_CotiAConfirmar.value = '';
            MostrarModalImportarCompra(false);
            MontarCotizacionEnInterfaz();
            CheckSiPaso2Disponible();
        })
    }else{
        document.getElementById('BotonImportarCoti').addEventListener('click', function(){
            ID_CotiAConfirmar.value = Input_CotiSeleccionada.value;
            Productos.forEach( element => {
                ProductosAVender.value = element.idProducto+'x'+element.cantidad+'x'+element.precioUnitario + (ProductosAVender.value?'¿':'')+ProductosAVender.value;
            })
            MostrarModalImportarCompra(false);
            MontarCotizacionEnInterfaz();
            CheckSiPaso2Disponible();
        })
    }
    
    
}



let PanelBotonesAbsolutos = document.getElementById('PanelBotonesAbsolutos');
function MontarCotizacionEnInterfaz(){
    if(!ID_CotiAConfirmar.value){
        InputTituloDeCoti.value = '';
        Input_CAS.value = '';
        Input_Utilidades.value = '';
        Input_IVA.value = '';

        ID_ClienteEnCoti.value = '';
        ProductosAVender.value = '';

        CartaDeCliente.innerHTML = `
        <div class="ClienteVacio">
            <span>CLIENTE NO ESPECIFICADO</span>
        </div>
        `;

        EspacioDeRowsDeLaTabla.innerHTML = `
        <row class="RowVacio">
            <span>No hay productos en esta cotización.</span>
        </row>
        `;
        PanelBotonesAbsolutos.className = 'BtnAbsolutos soloCoti';
    }else{
        Cotizacion = CotizacionesEnEspera[ID_CotiAConfirmar.value];

        InputTituloDeCoti.value = Cotizacion.nombre;
        Input_CAS.value = Cotizacion.CASalario;
        Input_Utilidades.value = Cotizacion.pUtilidades;
        Input_IVA.value = Cotizacion.pIVA;
        ID_ClienteEnCoti.value = Cotizacion.cedulaCliente;

        
        EstablecerCliente(Cotizacion.cedulaCliente);
        PanelBotonesAbsolutos.className = 'BtnAbsolutos';

    
        EspacioDeRowsDeLaTabla.innerHTML = '';
        CantidadDeProductoEnCoti = [];

        ProductosAVender.value.split('¿').forEach( element => {
            IDxCant = element.split('x');

            CantidadDeProductoEnCoti[IDxCant[0]] = IDxCant[1];

            if(Indexed_CuerpoDeCotiOriginal[IDxCant[0]]){
                Producto = Indexed_CuerpoDeCotiOriginal[IDxCant[0]];
                
                UlrImagen = Producto.ULRImagen;
                idProducto = Producto.idProducto;
                NombreProducto = Producto.nombre;
                CantidadProducto = Producto.cantidad;
                PrecioProducto = Producto.precioUnitario;
                TotalProducto = Producto.precioMultiplicado.toFixed(2);

            }else{
                idProducto = IDxCant[0];
            }


            CantidadTitle = CantidadProducto+' unidades';
            
            console.log(Producto)
            switch(Producto.idCategoria){
                case 1:
                    CantidadProducto = CantidadProducto;
                break;

                case 2:
                    CantidadProducto = CantidadProducto;
                    PrecioProducto = PrecioProducto * Producto.depreciacion;
                break;

                default:
                    ped = CantidadProducto.toString().split('.');
                    CantidadProducto = ped[0] + ' x ' + ped[1];
                    CantidadTitle = ped[0] + ' unidades x ' + ped[1]+' días';
                break;
            }

            


            EspacioDeRowsDeLaTabla.innerHTML = `
            <row id="RowDeProducto-${idProducto}">
                <celda class="ColumnaImagen">
                    <img src="../../Imagenes/Productos/${(UlrImagen?UlrImagen:'ImagenPredefinida_Productos.png')}" alt="">
                </celda>
                <celda class="ColumnaID">${idProducto}</celda>
                <celda class="ColumnaNombre">${NombreProducto}</celda>
                <celda class="ColumnaCantidad" title="${CantidadTitle} x ${Producto.depreciacion}">${CantidadProducto}</celda>
                <celda class="ColumnaPrecio" title="Precio sujeto al momento de crear la cotización">${PrecioProducto}$</celda>
                <celda class="ColumnaTotal" style="width: 120px;"><span class="TotalSumable" categoria="${Producto.idCategoria}">${TotalProducto}</span>$</celda>
                <div class="CeldaOculta">
                    <i id="BotonModificarProductoEspecifico-${idProducto}" title="Modificar este producto." class="fi-rr-pencil"></i>
                    <i id="BotonEliminarProductoEspecifico-${idProducto}" title="Eliminar este producto." class="fi-rr-trash"></i>
                </div>
            </row>
            ` + EspacioDeRowsDeLaTabla.innerHTML;
        })
    }
    

    ActualizarTotalSegunTotalesEnLista();
    
}

function EstablecerCliente(idCliente){
    if(idCliente){
        Cliente = ClientesTotales[idCliente];

        CartaDeCliente.innerHTML = `
        <img src="../../Imagenes/clientes/${(Cliente.ULRImagen?Cliente.ULRImagen:'ImagenPredefinida_Clientes.png')}" alt="">
        <div class="DatosDelCliente">
            <div class="NombreYIDDeCliente">
                <span class="NombreDelCliente">${Cliente.nombre}</span>
                <span class="IDDelCliente">${Cliente.tipoDeDocumento}-${zerofill(Cliente.rif, 9)}</span>
            </div>
            <span class="Telefono">Tlf: ${Cliente.telefono1}</span>
        </div>
        <div class="BotonesExtraDeClientes">
            <i id="BotonCambiarCliente" title="Cambiar cliente." class="fi-sr-pencil"></i>
        </div>
        `;


        
        document.getElementById('BotonCambiarCliente').addEventListener('click', function(){
            MostrarModalSeleccionarCliente(true);
        })
    }else{
        CartaDeCliente.innerHTML = `
        <div class="ClienteVacio">
            <span>CLIENTE NO ESPECIFICADO</span>
        </div>
        `;
    }
}


function ActualizarTotalSegunTotalesEnLista(){
    CostoDeProductos = 0;
    CostoEnHumanos = 0;
    Casillas = document.querySelectorAll('.TotalSumable');

    Casillas.forEach( casilla => {        
        precio = casilla.innerText;
        categoria = casilla.getAttribute('categoria');

        CostoDeProductos = CostoDeProductos + Number(precio);
        if(categoria == 3){
            CostoEnHumanos = Number(precio) + CostoEnHumanos;
        }
    })


    CostoAsociadoAlSalario = (CostoEnHumanos * Input_CAS.value * 0.01);
    CostoDeProductosConCAS = CostoAsociadoAlSalario + CostoDeProductos;
    Utilidades = (CostoDeProductosConCAS * Input_Utilidades.value * 0.01);

    Subtotal = CostoDeProductosConCAS + Utilidades;
    iva = (Subtotal * Input_IVA.value * 0.01);

    TotalTotal = Subtotal + iva;

    Span_totaltotal.innerText = TotalTotal.toFixed(2);
}



EspacioDeRowsDeLaTabla.addEventListener('click', (evento) => {
    if(evento.target.tagName.toLowerCase() == 'i'){
        if(evento.target.id.includes('-')){
            pedazos = evento.target.id.split('-');

            if(pedazos[0] == 'BotonEliminarProductoEspecifico'){
                EliminarProductoDeLaLista(pedazos[1]);
            }else if(pedazos[0] == 'BotonModificarProductoEspecifico'){
                MostrarModal_AgregarProducto(true);
                InputProductoAPrevisualizar.value = pedazos[1];
                PrevisualizarProductoSeleccionado(pedazos[1]);
            }else{
                console.log('No se reconoce la ID del elemento i.');
            }
        }else{
            alert('El boton no cuenta con una ID de formato válido.');
        }
    }
})


//FUNCIONES PARA DORMIR Y OTRAS COSAS
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
async function EsperarMS(Milisegundos) {
    for (let i = 0; i < 3; i++) {
        await sleep(i * Milisegundos);
    }
}


//VISIBILIDAD DEL MODAL
BotonTextoImportar.addEventListener('click', function(){
    MostrarModalImportarCompra(true);
})
BotonDelAside_ImportarCompra.addEventListener('click', function() {
    MostrarModalImportarCompra(true);
})
Modal_ImportarCompra.addEventListener('click', function(){
    MostrarModalImportarCompra(false);
})
BotonCerrarVentana_ImportarCompra.addEventListener('click', function(){
    MostrarModalImportarCompra(false);
})
VentadaModal_ImportarCompra.addEventListener('click', (e) => {
    e.stopPropagation();
})


async function MostrarModalImportarCompra(valor){
    
    if(valor){
        Modal_ImportarCompra.style = "display: flex";
        //BotonBuscarCompras.click();
        EnlistarCotizacionesDeUltimaBusqueda();
        await EsperarMS(50);
        VentadaModal_ImportarCompra.className ="VentanaFlotante";
    }else{
        Input_CotiSeleccionada.value="";
        VentadaModal_ImportarCompra.className ="VentanaFlotante OcultarModal";
        await EsperarMS(100);
        Modal_ImportarCompra.style = "";

        PrevisualizacionDeCoti.innerHTML = `
        <div class="CompraNoSeleccionada">
            <img src="../../Imagenes/Sistema/ImagenPredefinida_Ventas.png" alt="">
            <span>Seleccione una cotización</span>
        </div>
        `;
    }
}

InputTituloDeCoti.addEventListener('blur', function(){
    CheckSiPaso2Disponible();
})

let ContenedorDePaginas = document.getElementById('ContenedorDePaginas');

ButtonIrAPaso2.addEventListener('click', function(){
    if(ButtonIrAPaso2.className == 'BotonContinuarDisponible'){
        if(!Input_CAS.value){
            Input_CAS.value = '0';
        }
        if(!Input_Utilidades.value){
            Input_Utilidades.value = '0';
        }
        if(!Input_IVA.value){
            Input_IVA.value = '0';
        }
        //IR AL PASO 2
        ContenedorDePaginas.className = 'ContenedorDePaginas MostrarPaso2';
        CargarProductosListadosAlPaso2();
        
    }else{
        Toast.fire({
            icon: 'warning',
            title: 'Primero debe importarse una cotización'
        });
    }
})

const DivListaDeProductosAAlmacenar = document.getElementById('DivListaDeProductosAAlmacenar');
const InputProductoAlmacenar = document.getElementById('InputProductoAlmacenar');

var ProductosDelPaso2 = [];

function CargarProductosListadosAlPaso2(){
    console.log('paso 2')
    ProdXCantXPrec = ProductosAVender.value.split('¿');
    ListaDeExtraccion.value = '';

    DivListaDeProductosAAlmacenar.innerHTML = '';
    ProductosDelPaso2 = [];
    ProductosYaExtraidos = [];
    ProdXCantXPrec.forEach( element => {
        Valores = element.split('x');
        Producto = TodosLosProductos[Valores[0]];

        if(Producto.idcategoria<3){

            CantidadPasadaAlPaso2 = (Producto.idcategoria==2? Number(Valores[1]):Valores[1]);
            ProductosDelPaso2[Producto.id] = CantidadPasadaAlPaso2;
            ProductosYaExtraidos[Producto.id] = 0;
    
            DivListaDeProductosAAlmacenar.innerHTML = `
            <div class="RowDeCambio">
                <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Imagen">
                <img class="Imagen_ProductoDeCompra" src="../../Imagenes/Productos/${(Producto.ULRImagen? Producto.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                </div>
                <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Nombre">
                    ${Producto.nombre}
                </div>
                <div id="CeldaDeCantidadAlma/Comp-${Producto.id}" class="Celda_ProductoDeCompra CeldaRowDeCambio_Cantidad" unidadm="Unidades" title="Extraídos 0 ${Producto.nombredeunidad} de ${CantidadPasadaAlPaso2} vendidos">
                    <span id="CantidadAlmacenadaActualmente-${Producto.id}">0</span>
                    <span>/</span>
                    <span id="CantidadComprada-${Producto.id}">${CantidadPasadaAlPaso2}</span>
                </div>
                <div class="Celda_ProductoDeCompra CeldaBotonAlmacenar">
                    <button id="BotonAlmacenar-${Producto.id}" class="BotonAlmacenarProductoDisponible" title="Extraer este producto"><i class="fi-rr-caret-square-right"></i></button>
                </div>
            </div>
            ` + DivListaDeProductosAAlmacenar.innerHTML;
        }

        

        document.querySelectorAll('.BotonAlmacenarProductoDisponible').forEach( btn => {
            btn.addEventListener('click', function(){
                pieces = btn.id.split('-');
                InputProductoAlmacenar.value = pieces[1];
                
                if(btn.className == 'BotonAlmacenarProductoDisponible'){
                    MostrarModal_AlmacenarProducto(true);
                }
                
            })
        });

    })


    
}

const BotonVolverAPag1 = document.getElementById('BotonVolverAPag1');

BotonVolverAPag1.addEventListener('click', function(){
    ContenedorDePaginas.className = 'ContenedorDePaginas';
})

function zerofill(string, max){
    for (let index = 0; max > string.length; index++) {
        string = '0'+string;
    }
    return string;
}

function zerofill(string, max){
    for (let index = 0; max > string.length; index++) {
        string = '0'+string;
    }
    return string;
}

function onlyNumber(element, e){
    if(isNaN(e.key)){
        return false;
    }
}