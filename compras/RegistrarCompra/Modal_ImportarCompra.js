let VentadaModal_ImportarCompra = document.getElementById('VentadaModal_ImportarCompra');
let Modal_ImportarCompra = document.getElementById('Modal_ImportarCompra');
let BotonDelAside_ImportarCompra = document.getElementById('BotonDelAside_ImportarCompra');
let BotonTextoImportar = document.getElementById('BotonTextoImportar');
let BotonBuscarCompras = document.getElementById('BotonBuscarCompras');
let InputBuscadorDeCompras = document.getElementById('InputBuscadorDeCompras');
let ListaDeComprasConsultadas = document.getElementById('ListaDeComprasConsultadas');
let InputCompraAPrevisualizar = document.getElementById('InputCompraAPrevisualizar');
let PrevisualizacionDeCompra = document.getElementById('PrevisualizacionDeCompra');
let InputCompraImportada = document.getElementById('InputCompraImportada');

let Aviso_CompraImportada = document.getElementById('Aviso_CompraImportada')
let SpanDelAvisito = document.getElementById('SpanDelAvisito');


var VisibilidadDelModal = false;
var TodasLasCompras = [];
var ObjetosDeUltimaConsultaAlAPI = [];
var CierreAutomaticoDelModalCompras = true;
var Objeto_CompraImportada = null;

InputBuscadorDeCompras.addEventListener('keyup', function(e){
    if(e.keyCode == 13){
        BotonBuscarCompras.click();
    }
})

BotonBuscarCompras.addEventListener('click', () => {
    MostrarEnListaObjetosEncontradosEnLaBusqueda(InputBuscadorDeCompras.value);
})

async function MostrarEnListaObjetosEncontradosEnLaBusqueda(Descripcion){
    await ConsultarCompras(Descripcion);


    if(ObjetosDeUltimaConsultaAlAPI.length == 0){
        ListaDeComprasConsultadas.style = "height: calc(80vh - 145px);";
        HTMLConRowsAMostrarEnLaLista = `
            <div class="Flex-gap2 HoverVino TablaDeproductosVacia">
                <span>No hay ordenes de compra para mostrar...</span>
            </div>
        `;
    }else{
        ListaDeComprasConsultadas.style = "";
        HTMLConRowsAMostrarEnLaLista = "";

        
    }


    


    ObjetosDeUltimaConsultaAlAPI.forEach(Objeto => {
            
        HTMLConRowsAMostrarEnLaLista = HTMLConRowsAMostrarEnLaLista + `
            <row id="RowCompraConsultada-${Objeto.id}" class="RowCompraConsultada">
                <celda class="ColumnaID">${Objeto.id}</celda>
                <celda class="ColumnaDescripcion">${Objeto.nombre}</celda>
                <celda class="ColumnaCantidad">${Objeto.nroDeProductos}</celda>
            </row>
        `;
    })
    ListaDeComprasConsultadas.innerHTML = HTMLConRowsAMostrarEnLaLista;



    //Ya con los objetos mostrados en rows, le pongo los eventos
    let Rows_CompraConsultada = document.querySelectorAll('.RowCompraConsultada');
    
    Rows_CompraConsultada.forEach(RowCompra => {
        RowCompra.addEventListener('click', () => {
            pedazos = RowCompra.id.split('-', 2);

            Rows_CompraConsultada.forEach( (RowParaLimpiarSuClase) => {
                RowParaLimpiarSuClase.className = 'RowCompraConsultada';
            })

            RowCompra.className = 'RowCompraConsultada ProductoSeleccionado';

            InputCompraAPrevisualizar.value = pedazos[1];
            MostrarCartaDeRowSeleccionado(true);
        })
    })
}

function MostrarCartaDeRowSeleccionado(valor){
    if(valor){
        ObjetoSeleccionado = TodasLasCompras.filter( function(ObjetoDeLaLista){
            return ObjetoDeLaLista.id == InputCompraAPrevisualizar.value;
        });
        ObjetoSeleccionado = ObjetoSeleccionado[0];


        Rows_ProductosDeCompra = "";
        ObjetoSeleccionado.productosEnFormato.split('¿').forEach(ProdXCant => {
            pedazos = ProdXCant.split('x');

            ProductoSeleccionado = TodosLosProductos.filter( function(ProductoDeLaLista){
                return ProductoDeLaLista.id == pedazos[0];
            });
            ProductoSeleccionado = ProductoSeleccionado[0];

            CantidadAMostrar = pedazos[1];
            console.log()
            if(ProductoSeleccionado.idcategoria == 1){
                CantidadAMostrar = pedazos[1];
            }else{
                CantidadAMostrar = Number(pedazos[1]).toFixed(4);
            }

            Rows_ProductosDeCompra = Rows_ProductosDeCompra + `
            <div class="RowDeCambio">
                                        <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Imagen">
                                            <img class="Imagen_ProductoDeCompra" src="../../Imagenes/Productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                                        </div>
                                        <div class="Celda_ProductoDeCompra CeldaRowDeCambio_ID">
                                            ${ProductoSeleccionado.id}
                                        </div>
                                        <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Nombre">
                                            ${ProductoSeleccionado.nombre}
                                        </div>
                                        <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Cantidad" title="${pedazos[1]} ${ProductoSeleccionado.nombredeunidad}">
                                            x ${CantidadAMostrar}
                                        </div>
                                    </div>
            `;
        });

        
        

        PrevisualizacionDeCompra.innerHTML = `
                        <div class="CompraSiSeleccionada">
                            <span class="Titulo">${ObjetoSeleccionado.nombre}</span>
                            <div class="CajaDeDatosDeLaCompra">
                                <img src="../../Imagenes/iconoDelMenu_Compras.png" alt="">
                                <div class="NombreDelDato DatosDeLaCompra">
                                    <p>Nro de productos</p>
                                    <p>Fecha de expiración</p>
                                    <p>Fecha de creación</p>
                                </div>
                                <div class="DatosDeLaCompra">
                                    <b>:</b>
                                    <b>:</b>
                                    <b>:</b>
                                </div>
                                <div class="DatosDeLaCompra">
                                    <p>${ObjetoSeleccionado.nroDeProductos}</p>
                                    <p>${((ObjetoSeleccionado.fechaExpiracion == null)?'<span style="color: gray;">Ninguna</span>':ObjetoSeleccionado.fechaExpiracion)}</p>
                                    <p>${ObjetoSeleccionado.fechaCreacion}</p>
                                </div>
                            </div>
                            <span class="SubTitulo_ProductosDeCompra"> <i class="fi-sr-package"></i> Productos a comprar: </span>
                            <div class="TablaDeCambios">
                                <div class="EspacioDeRowDeCambio mostly-customized-scrollbar">
                                    ${Rows_ProductosDeCompra}
                                    
                                </div>
                            </div>
                            <div class="EspacioBotonImportarCompra">
                                ${
                                    ((ObjetoSeleccionado.id == InputCompraImportada.value)
                                    ?'<button id="BotonQuitarOrdenInportada" class="BotonQuitarOrden"><i class="fi-rr-cross-small"></i> Quitar orden</button>'
                                    :'<button id="BotonImportarOrden" class="BotonImportarOrden"> <i class="fi-rr-arrow-alt-down"></i> Importar orden</button>')
                                }
                                <i class="IconoConInfo fi-sr-comment-exclamation">
                                    <div class="TextoDeIconoConInfo">Importar una orden de compra eliminará los productos que hayas agregado a la lista anteriormente.
                                    De igual forma, quitar una Orden de compra seleccionada eliminará todos los productos de la lista.
                                    </div>
                                </i>
                            </div>
                        </div>
        `;

        if(ObjetoSeleccionado.id == InputCompraImportada.value){
            let BotonQuitarOrdenInportada = document.getElementById('BotonQuitarOrdenInportada');
            BotonQuitarOrdenInportada.addEventListener('click', () => {
                InputProductosListados.value = "";
                InputCompraImportada.value = "";
                Objeto_CompraImportada = "";
                InputDeNombreDeCompra.value = "";

                ActualizarListaDeCompraSegunDatosDeInput();
                MostrarModalImportarCompra(false);
                VerificarDisponibilidadDelPaso2();
            })
        }else{
            let BotonImportarOrden = document.getElementById('BotonImportarOrden');
            BotonImportarOrden.addEventListener('click', () => {
                if(InputCompraAPrevisualizar.value > 0){
    
                    InputCompraImportada.value = InputCompraAPrevisualizar.value;
                    InputProductosListados.value = ObjetoSeleccionado.productosEnFormato;
                    Objeto_CompraImportada = ObjetoSeleccionado;
    
                    
                    MostrarModalImportarCompra(false);
                    MontarOrdenDeCompraImportada(ObjetoSeleccionado);
                    VerificarDisponibilidadDelPaso2();
                }else{
                    alert('La ID ' + InputCompraAPrevisualizar.value + ' de la orden de compra a importar no es válida.')
                }
            })
        }

        
    }else{
        InputCompraAPrevisualizar.value = "";
        PrevisualizacionDeCompra.innerHTML = `
        <div class="CompraNoSeleccionada">
                            <img src="../../Imagenes/Sistema/ImagenPredefinida_Compras.png" alt="">
                            <span>Seleccione una orden de compra</span>
                        </div>
        `;
    }
}

function MontarOrdenDeCompraImportada(CompraImportada){
    
    if(InputCompraImportada.value > 0){
        Aviso_CompraImportada.style = "display: block;";

        Aviso_CompraImportada.innerHTML = `<i class="fi-rr-info"> Productos importados de la orden de compra ID #${CompraImportada.id}.</i>`;


        InputDeNombreDeCompra.value = CompraImportada.nombre;
        ActualizarListaDeCompraSegunDatosDeInput();
    }else{
        Aviso_CompraImportada.style = "";
        //alert('No se encontró una orden de compra.');
    }
}

function ComprobarSiCompraCoincideConLaOriginal(){
    if(InputCompraImportada.value > 0){

        if(InputProductosListados.value == Objeto_CompraImportada.productosEnFormato){
            Aviso_CompraImportada.innerHTML = `<i class="fi-rr-info"> Productos importados de la orden de compra ID #${Objeto_CompraImportada.id}.</i>`;
        }else{
            Aviso_CompraImportada.innerHTML = `<i class="fi-rr-info"> Productos importados de la orden de compra ID #${Objeto_CompraImportada.id}. (Actualizado por el usuario)</i>`;
        }
    }
}

function ActualizarListaDeCompraSegunDatosDeInput(){
    EspacioDeRowsDeLaTabla.innerHTML = "";
    
    if(InputProductosListados.value.length > 0){
        InputProductosListados.value.split('¿').forEach(ProdXCant => {
            pedazos = ProdXCant.split('x');
    
            Producto = TodosLosProductos.filter( function(ProductoDeLaLista){
                return ProductoDeLaLista.id == pedazos[0];
            });
            Producto = Producto[0];
    
    
            EspacioDeRowsDeLaTabla.innerHTML = EspacioDeRowsDeLaTabla.innerHTML + `
            <row id="RowDeProducto-${Producto.id}">
                    <celda class="ColumnaImagen">
                        <img src="../../Imagenes/Productos/${((Producto.ULRImagen)?Producto.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                    </celda>
                    <celda class="ColumnaID">${Producto.id}</celda>
                    <celda class="ColumnaNombre">${Producto.nombre}</celda>
                    <celda class="ColumnaCantidad" title="${pedazos[1]} ${Producto.nombredeunidad}">x ${pedazos[1]}</celda>
                    <div class="CeldaOculta">
                        <i id="BotonModificarProductoEspecifico-${Producto.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                        <i id="BotonEliminarProductoEspecifico-${Producto.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                    </div>
                </row>
            `;
        });
    }else{
        EspacioDeRowsDeLaTabla.innerHTML = `
        <row class="RowVacio">
            <span>No hay productos en esta lista.</span>
        </row>
        `;
        MontarOrdenDeCompraImportada();
    }
    ActualizarArraysDeIDsYCantidades();
}

async function MostrarModalImportarCompra(valor){
    VisibilidadDelModal = !VisibilidadDelModal;
    if(valor){
        Modal_ImportarCompra.style = "display: flex";
        BotonBuscarCompras.click();
        await EsperarMS(50);
        VentadaModal_ImportarCompra.className ="VentanaFlotante";
    }else{
        InputProductoAPrevisualizar.value="";
        VentadaModal_ImportarCompra.className ="VentanaFlotante OcultarModal";
        await EsperarMS(100);
        Modal_ImportarCompra.style = "";
    }
}

async function ConsultarCompras(Descripcion){
    document.getElementById('ListaDeComprasConsultadas').innerHTML = `<div class="did_loading">
        <div class="rotating"><span class="fi fi-rr-loading"></span></div>
        Cargando
    </div>`;
    try{
        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Compras.php?descripcion=' + Descripcion +'&idEstado=63'); 

        if(respuesta.status === 200){
            ObjetosRecibidos = await respuesta.json();

            if(ObjetosRecibidos.objetos == undefined){
                ComprasRecibidas = [];
            }else{
                ComprasRecibidas = ObjetosRecibidos.objetos;
            }

            
            
            
            if(TodasLasCompras.length == 0){
                TodasLasCompras = ComprasRecibidas;
            }
            ObjetosDeUltimaConsultaAlAPI = ComprasRecibidas;


            

        }else{
            alert('Error al consultar la API de compras. Status: ' + respuesta.status);
        }
    }catch(error){
        console.log(error);
    }
}

window.addEventListener('load', () => {
    ConsultoComprasYVeoSiDeboCArgarUna();
})

async function ConsultoComprasYVeoSiDeboCArgarUna(){
    await ConsultarCompras("");
    await ConsultarProductos("", 0);
    await ConsultarAlmacenes('');

    if(InputCompraImportada.value){

        OBJ_CompraImportadaSegunInput = TodasLasCompras.find( (element) => {
            return element.id == InputCompraImportada.value;
        })
        
        
        InputProductosListados.value = OBJ_CompraImportadaSegunInput.productosEnFormato;
        MontarOrdenDeCompraImportada(OBJ_CompraImportadaSegunInput);    
    }
    VerificarDisponibilidadDelPaso2();

    PrepararAlmacenPredeterminado();
}

function VerificarDisponibilidadDelPaso2(){
    if(InputProductosListados.value && InputDeNombreDeCompra.value){
        ButtonIrAPaso2.className = "BotonContinuarDisponible";
    }else{
        ButtonIrAPaso2.className = "BotonContinuarNoDisponible";
    }
}

//VISIBILIDAD DEL MODAL
VentadaModal_ImportarCompra.addEventListener('click', (e) => {
    e.stopPropagation();
})
Modal_ImportarCompra.addEventListener('click', () => {
    MostrarModalImportarCompra(false);

})
BotonCerrarVentanaProductos.addEventListener('click', () => {
    MostrarModalImportarCompra(false);
    
})
BotonDelAside_ImportarCompra.addEventListener('click', () => {
    MostrarCartaDeRowSeleccionado(false);
    MostrarModalImportarCompra(true);  
})
BotonTextoImportar.addEventListener('click', () => {
    MostrarCartaDeRowSeleccionado(false);
    MostrarModalImportarCompra(true);
})