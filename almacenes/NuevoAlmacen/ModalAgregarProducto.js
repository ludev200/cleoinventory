let BotonOcultoParaAgregarProductos = document.getElementById('BotonComoTalXD');
let ModalAgregarProducto = document.getElementById('ModalAgregarProducto');
let VentadaModalAgregarProducto = document.getElementById('VentadaModalAgregarProducto');
let BotonCerrarVentanaProductos = document.getElementById('BotonCerrarVentanaProductos');
let BotonDelAsideAgregarProducto = document.getElementById('BotonDelAsideAgregarProducto');
let InputProductoAPrevisualizar = document.getElementById('InputProductoAPrevisualizar');
let SelectCategoriaABuscar = document.getElementById('SelectCategoriaABuscar');
let BotonBuscarProductos = document.getElementById('BotonBuscarProductos');
let BuscadorDeProductos = document.getElementById('BuscadorDeProductos');
let ListaDeProductosConsultados = document.getElementById('ListaDeProductosConsultados');
let PrevisualizacionDeProducto = document.getElementById('PrevisualizacionDeProducto');
let InputProductosEnlistadosAlAlmacen = document.getElementById('InputProductosEnlistadosAlAlmacen');
let EspacioDeRowsDeLaTabla = document.getElementById('EspacioDeRowsDeLaTabla');

var VisibilidadDelModal = false;
var TodosLosProductos = "";
var ProdcutosDeUltimaConsulta = "";
var CierreAutomaticoDelModalProductos = true;
let ArrayConIDs = [];
let ArrayConCantidades = [];


/*
Toast.fire({
    icon: 'warning',
    title: 'La cantidad debe ser mayor a 0'
});
*/

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



BuscadorDeProductos.addEventListener('keyup', (event) => {
    tecla = event.which || event.keyCode;
    if(!BuscadorDeProductos.value || tecla == 13){
        BotonBuscarProductos.click();
    }
})
BotonBuscarProductos.addEventListener('click', () => {
    MostrarEnListaLosProductosConsultados(BuscadorDeProductos.value, SelectCategoriaABuscar.value);
});
SelectCategoriaABuscar.addEventListener('change', () => {
    BotonBuscarProductos.click();
})


InputProductosEnlistadosAlAlmacen.addEventListener('click', () => {
    InputProductosEnlistadosAlAlmacen.blur();
})


function MostrarCartaDeProductoSeleccionado(valor){
    if(valor){
        ProductoSeleccionado = TodosLosProductos.filter( function(ProductoDeLaLista){
            return ProductoDeLaLista.id == InputProductoAPrevisualizar.value;
        });
        ProductoSeleccionado = ProductoSeleccionado[0];
        
        PrevisualizacionDeProducto.innerHTML = `
            <div class="ProductoSiSeleccionado">
                <img src="../../Imagenes/Productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${ProductoSeleccionado.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
                <b class="NombreDelProductoSiSeleccionado">${ProductoSeleccionado.nombre}</b>
                <b class="PrecioDelProductoSiSeleccionado">${ProductoSeleccionado.precio}$</b>
                <div class="ElementosPaElegirCantidad">
                    <span>Cantidad:</span>
                    <div class="CantidadYUnidad${((ProductoSeleccionado.idcategoria == 1)?' TextToNumbre':'')}">
                        <span class="fi-rr-cross-small"></span>
                        <input onkeypress="return ${((ProductoSeleccionado.idcategoria == 1)?'SoloInt':'SoloInt')}(event)" onClick="this.select();" onpaste="return false" autocomplete="off" maxlength="9" id="InputCantidad">
                        ${((ProductoSeleccionado.idcategoria == 1)?'<span title="'+ProductoSeleccionado.nombredeunidad+'">'+ProductoSeleccionado.simbolo+'</span>':'')}
                    </div>
                    <span style="color: var(--Rosita);" id="PrecioMultiplicado" class="PrecioMultiplicado">Total: 0.00$</span>
                </div>
                <div id="CajaDeBotonBorrarYAgregar"></div>
                <label title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
                    Cierre automático
                    <input type="checkbox" ${((CierreAutomaticoDelModalProductos)?'checked':'')} name="" id="CierreAutomaticoModalProductos">
                    <div class="slider round"></div>
                </label>
            </div>
        `;
        let CierreAutomaticoModalProductos = document.getElementById('CierreAutomaticoModalProductos');
        let InputCantidad = document.getElementById('InputCantidad');
        

        

        CierreAutomaticoModalProductos.addEventListener('change', () => {
            CierreAutomaticoDelModalProductos = CierreAutomaticoModalProductos.checked
        })

        

        //Defino el tipo de input de cantidad
        InputCantidad.addEventListener('keyup', () => {
            
        });
        InputCantidad.addEventListener('blur', () => {
            if(!InputCantidad.value > 0){
                InputCantidad.value = '0';
            }
        })

        if(ProductoSeleccionado.idcategoria == 1){
            InputCantidad.value = '0';
            
            InputCantidad.addEventListener('mouseenter', () => {
                InputCantidad.type = 'number';
            }) 
            InputCantidad.addEventListener('mouseleave', () => {
                InputCantidad.type = 'text';
            })
            InputCantidad.addEventListener('change', () => {
                if(InputCantidad.value < 0){
                    InputCantidad.value = '0';
                }
                ActualizarSpanPrecioMultiplicado();
            })
        }else{
            InputCantidad.value = '0';
        }

        if(ArrayConIDs.includes(ProductoSeleccionado.id)){
            InputCantidad.value = ArrayConCantidades[ArrayConIDs.indexOf(ProductoSeleccionado.id)];
        }


        let CajaDeBotonBorrarYAgregar = document.getElementById('CajaDeBotonBorrarYAgregar');

        var ElementoQueBusco = "";
        ElementoQueBusco = InputProductosEnlistadosAlAlmacen.value.split('¿').find( (IDConCantidad) => {
            pedazos = IDConCantidad.split('x');

            return pedazos[0] == ProductoSeleccionado.id;
        } );

        if(ElementoQueBusco){
            CajaDeBotonBorrarYAgregar.innerHTML = `
                <span title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>
                <button title="Modificar la cantidad cotizada de este producto" id="BotonParaAgregarElProductoSeleccionado" class="BotonParaAgregarElProductoSeleccionado">Modificar</button>
            `;

            let BotonParaAgregarElProductoSeleccionado = document.getElementById('BotonParaAgregarElProductoSeleccionado');
            let BotonRemoverProductoSeleccionado = document.getElementById('BotonRemoverProductoSeleccionado');

            BotonParaAgregarElProductoSeleccionado.addEventListener('click', () => {
                ActualizarProductoDeLaLista(ProductoSeleccionado, InputCantidad.value);
            })
            BotonRemoverProductoSeleccionado.addEventListener('click', () => {
                EliminarProductoDeLaLista(InputProductoAPrevisualizar.value);

                MostrarModalAgregarProducto(!CierreAutomaticoDelModalProductos);
                if(!CierreAutomaticoDelModalProductos){
                    MostrarCartaDeProductoSeleccionado(false);
                }
            })
            
        }else{
            CajaDeBotonBorrarYAgregar.innerHTML = `
                <button id="BotonParaAgregarElProductoSeleccionado" title="Agregar este producto a la cotización" class="BotonParaAgregarElProductoSeleccionado">Agregar</button>
            `;

            let BotonParaAgregarElProductoSeleccionado = document.getElementById('BotonParaAgregarElProductoSeleccionado');

            BotonParaAgregarElProductoSeleccionado.addEventListener('click', () => {
                AgregarProductoALaLista(ProductoSeleccionado, InputCantidad.value);
            })
        }
        InputCantidad.addEventListener('keyup', (evento) => {
            if(evento.keyCode == 13){
                BotonParaAgregarElProductoSeleccionado.click();
            }
        })


        InputCantidad.select();

    }else{
        InputProductoAPrevisualizar.value = "";
        PrevisualizacionDeProducto.innerHTML = `
            <div class="ProductoNoSeleccionado">
                <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                <span>Seleccione un producto</span>
            </div>
        `;
    }
}

EspacioDeRowsDeLaTabla.addEventListener('click', (evento) => {
    if(evento.target.tagName.toLowerCase() == 'i'){
        if(evento.target.id.includes('-')){
            pedazos = evento.target.id.split('-');

            if(pedazos[0] == 'BotonEliminarProductoEspecifico'){
                EliminarProductoDeLaLista(pedazos[1]);
            }else if(pedazos[0] == 'BotonModificarProductoEspecifico'){
                MostrarModalAgregarProducto(true);
                InputProductoAPrevisualizar.value = pedazos[1];
                MostrarCartaDeProductoSeleccionado(true);
            }else{
                console.log('No se reconoce la ID del elemento i.');
            }
        }else{
            alert('El boton no cuenta con una ID de formato válido.');
        }
    }
})

function EliminarProductoDeLaLista(IDAEliminar){
    PosicionAEliminar = -1;

    if(ArrayConIDs.includes(IDAEliminar)){
        PosicionAEliminar = ArrayConIDs.indexOf(IDAEliminar)
        ArrayProductosEnLista = InputProductosEnlistadosAlAlmacen.value.split('¿');
        ArrayProductosEnLista.splice(PosicionAEliminar ,1);
        InputProductosEnlistadosAlAlmacen.value = ArrayProductosEnLista.join('¿');
        ActualizarArraysDeIDsYCantidades();
        
        if(InputProductosEnlistadosAlAlmacen.value){
            document.getElementById('RowDeProducto-'+IDAEliminar).remove();
        }else{
            EspacioDeRowsDeLaTabla.innerHTML = `
                <row class="RowVacio">
                    <span>No hay productos en el inventario de este almacén</span>
                </row>
            `;
        }
    }
}

function ActualizarProductoDeLaLista(Producto, CantidadNueva){
    if(CantidadNueva > 0){
        //Busco la posicion a actualizar
        PosicionAActualizar = -1;
        ArrayDeProductosEnLista = InputProductosEnlistadosAlAlmacen.value.split('¿');

        ArrayDeProductosEnLista.some( function(ProductoConPrecio){
            pedazos = ProductoConPrecio.split('x');

            ProductoEncontrado = Producto.id == pedazos[0];
            if(ProductoEncontrado){
                PosicionAActualizar = ArrayDeProductosEnLista.indexOf(ProductoConPrecio);
            }
            return ProductoEncontrado;
        });

        if(PosicionAActualizar > -1){
            //Actualizo el input
            ArrayDeProductosEnLista[PosicionAActualizar] = Producto.id + 'x' + CantidadNueva;
            InputProductosEnlistadosAlAlmacen.value = ArrayDeProductosEnLista.join('¿');
            ActualizarArraysDeIDsYCantidades();

            //Actualizo la interfaz
            document.getElementById('RowDeProducto-'+Producto.id).remove();
            EspacioDeRowsDeLaTabla.innerHTML = `
                <row id="RowDeProducto-${Producto.id}">
                    <celda class="ColumnaImagen">
                        <img src="../../Imagenes/Productos/${((Producto.ULRImagen)?Producto.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                    </celda>
                    <celda class="ColumnaID">${Producto.id}</celda>
                    <celda class="ColumnaNombre">${Producto.nombre}</celda>
                    <celda class="ColumnaExistencia">x ${CantidadNueva}</celda>
                    <celda ${((Producto.simbolo == '?')?'':'title="'+Producto.nombredeunidad+'"')} class="ColumnaCategoria">${((Producto.simbolo == '?')?'-':Producto.simbolo)}</celda>
                    <div class="CeldaOculta">
                        <i id="BotonModificarProductoEspecifico-${Producto.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                        <i id="BotonEliminarProductoEspecifico-${Producto.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                    </div>
                </row>
            ` + EspacioDeRowsDeLaTabla.innerHTML;
            
            //Vuelvo o me quedo segun cierre automatico
            MostrarModalAgregarProducto(!CierreAutomaticoDelModalProductos);
            if(!CierreAutomaticoDelModalProductos){
                MostrarCartaDeProductoSeleccionado(false);
            }
        }else{
            Toast.fire({
                icon: 'warning',
                title: 'No se encontró el producto ' + Producto.id + ' en la lista'
            });
        }
    }else{
        Toast.fire({
            icon: 'warning',
            title: 'La cantidad debe ser mayor a 0'
        });
    }
    
    
}




function AgregarProductoALaLista(Producto, Cantidad){
    if(Cantidad > 0){
        HayMasProductos = InputProductosEnlistadosAlAlmacen.value;
        
        
        InputProductosEnlistadosAlAlmacen.value = Producto.id + 'x' + Cantidad + ((InputProductosEnlistadosAlAlmacen.value)?'¿' + InputProductosEnlistadosAlAlmacen.value:'');    
        ActualizarArraysDeIDsYCantidades();

        EspacioDeRowsDeLaTabla.innerHTML = `
            <row id="RowDeProducto-${Producto.id}">
                <celda class="ColumnaImagen">
                    <img src="../../Imagenes/Productos/${((Producto.ULRImagen)?Producto.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                </celda>
                <celda class="ColumnaID">${Producto.id}</celda>
                <celda class="ColumnaNombre">${Producto.nombre}</celda>
                <celda class="ColumnaExistencia">x ${Cantidad}</celda>
                <celda ${((Producto.simbolo == '?')?'':'title="'+Producto.nombredeunidad+'"')} class="ColumnaCategoria">${((Producto.simbolo == '?')?'-':Producto.simbolo)}</celda>
                <div class="CeldaOculta">
                    <i id="BotonModificarProductoEspecifico-${Producto.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                    <i id="BotonEliminarProductoEspecifico-${Producto.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                </div>
            </row>
        ` + ((HayMasProductos)?EspacioDeRowsDeLaTabla.innerHTML:'');
    
        MostrarModalAgregarProducto(!CierreAutomaticoDelModalProductos);
        if(!CierreAutomaticoDelModalProductos){
            MostrarCartaDeProductoSeleccionado(false);
        }
    }else{
        Toast.fire({
            icon: 'warning',
            title: 'La cantidad debe ser mayor a 0'
        });
    }
}

function ActualizarArraysDeIDsYCantidades(){
    ArrayConIDs = [];
    ArrayConCantidades = [];
    InputProductosEnlistadosAlAlmacen.value.split('¿').forEach( (ProductoConCantidad)=>{
        if(ProductoConCantidad.includes('x')){
            pedazos = ProductoConCantidad.split('x');
            ArrayConIDs.push(pedazos[0]);
            ArrayConCantidades.push(pedazos[1]);
        }
        
    });
}

async function MostrarEnListaLosProductosConsultados(Descripcion, IdCategoria){    
    await ConsultarProductos(Descripcion, IdCategoria);

    if(ProdcutosDeUltimaConsulta.length == 0){
        ListaDeProductosConsultados.style = "height: calc(80vh - 145px);";
        HTMLConLosProductosAMostrarEnLaLista = `
            <div class="Flex-gap2 HoverVino TablaDeproductosVacia">
                <span>No hay productos para mostrar...</span>
            </div>
        `;
    }else{
        ListaDeProductosConsultados.style = "";
        HTMLConLosProductosAMostrarEnLaLista = "";
    }
    
    

    
    
    

    ProdcutosDeUltimaConsulta.forEach(producto => {
        
        HTMLConLosProductosAMostrarEnLaLista = HTMLConLosProductosAMostrarEnLaLista + `
            <row class="RowProductoConsultado" id="RowProductoConsultado-${producto.id}">
                <celda class="ColumnaImagen">
                    <img src="../../Imagenes/Productos/${((producto.ULRImagen)?producto.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                </celda>
                <celda class="ColumnaID">${producto.id}</celda>
                <celda class="ColumnaNombre2">${producto.nombre}</celda>
                <celda class="ColumnaExistencia"> x ${((ArrayConIDs.includes(producto.id))?ArrayConCantidades[ArrayConIDs.indexOf(producto.id)]:'0')}</celda>
            </row>
        `;
    });
    ListaDeProductosConsultados.innerHTML = HTMLConLosProductosAMostrarEnLaLista;
    
    //Agrego los eventos
    let RowsDeLaListaDeProductos = document.querySelectorAll('.RowProductoConsultado');
    RowsDeLaListaDeProductos.forEach( (RowDeProducto) => {
        RowDeProducto.addEventListener('click', () => {
            pedazos = RowDeProducto.id.split('-', 2);

            RowsDeLaListaDeProductos.forEach( (ProductoPaLimpiarSuClase) => {
                ProductoPaLimpiarSuClase.className = 'RowProductoConsultado';
            })

            RowDeProducto.className = 'RowProductoConsultado ProductoSeleccionado';
            InputProductoAPrevisualizar.value = pedazos[1];
            MostrarCartaDeProductoSeleccionado(true);
        })
    })
    
}


async function ConsultarProductos(Descripcion, IdCategoria){
    ListaDeProductosConsultados.innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    
    
    try{
        if(IdCategoria == 0){
            
            let respuesta1 = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?descripcion=' + Descripcion +'&categoria=1');
            let respuesta2 = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?descripcion=' + Descripcion +'&categoria=2');
            
            if(respuesta1.status === 200 && respuesta2.status === 200){
                MaterialesRecibidos = await respuesta1.json();
                EquiposRecibidos = await respuesta2.json();

                if(MaterialesRecibidos.objetos == undefined){
                    MaterialesRecibidos = [];
                }else{
                    MaterialesRecibidos = MaterialesRecibidos.objetos;
                }
                if(EquiposRecibidos.objetos == undefined){
                    EquiposRecibidos = [];
                }else{
                    EquiposRecibidos = EquiposRecibidos.objetos;
                }
                
                TodosLosProductos = MaterialesRecibidos.concat(EquiposRecibidos);
                ProdcutosDeUltimaConsulta  = TodosLosProductos;

            }else{
                alert('Error al consultar API por productos materiales y equipo. Status: ' + respuesta1.status + '/' + respuesta2.status)
            }
        }else{
            console.log('b')
                            console.log('http://'+ipserver+'/CleoInventory/API/API_Productos.php?descripcion=' + Descripcion +'&categoria=' + IdCategoria);
            let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?descripcion=' + Descripcion +'&categoria=' + IdCategoria);
            
            if(respuesta.status === 200){
                ProductosRecibidos = await respuesta.json();

                if(ProductosRecibidos.objetos == undefined){
                    ProdcutosDeUltimaConsulta = [];
                }else{
                    ProdcutosDeUltimaConsulta = ProductosRecibidos.objetos;
                }

            }else{
                alert('Error al consultar API. Status ' + respuesta.status);
            }
        }

    }catch(error){
        console.log(error);
    }
}

window.addEventListener('load', () => {
    ActualizarArraysDeIDsYCantidades();
    ConsultarProductos("", 0);
    
})

//VISIBILIDAD DEL MODAL
VentadaModalAgregarProducto.addEventListener('click', (e) => {
    e.stopPropagation();
})
ModalAgregarProducto.addEventListener('click', () => {
    MostrarModalAgregarProducto(false);

})
BotonCerrarVentanaProductos.addEventListener('click', () => {
    MostrarModalAgregarProducto(false);
    
})
BotonDelAsideAgregarProducto.addEventListener('click', () => {
    MostrarCartaDeProductoSeleccionado(false);
    MostrarModalAgregarProducto(true);
    
})
BotonOcultoParaAgregarProductos.addEventListener('click', () => {
    MostrarCartaDeProductoSeleccionado(false);
    MostrarModalAgregarProducto(true);
    
})

async function MostrarModalAgregarProducto(valor){
    VisibilidadDelModal = !VisibilidadDelModal;
    if(valor){
        ModalAgregarProducto.style = "display: flex";
        BotonBuscarProductos.click();
        await EsperarMS(50);
        VentadaModalAgregarProducto.className ="VentanaFlotante";
    }else{
        InputProductoAPrevisualizar.value="";
        VentadaModalAgregarProducto.className ="VentanaFlotante OcultarModal";
        await EsperarMS(100);
        ModalAgregarProducto.style = "";
    }
}








//FUNCIONES PARA DORMIR Y OTRAS COSAS
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
async function EsperarMS(Milisegundos) {
    for (let i = 0; i < 3; i++) {
        await sleep(i * Milisegundos);
    }
}

function SoloInt(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8373846";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    if(InputCantidad.value.length > 5){
        return false;
    }
    
    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}

function SoloFloat(e){  
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789.";
    especiales = "8¬37¬38¬46";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;
//permite las telcas de borrar y flechitas
    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
//no permite meter mas de dos puntos (.)
    if(tecla=="." && InputCantidad.value.includes(".")){
        return false;
    }
//solo permite dos numeros mas despues del punto
    if(InputCantidad.value.includes(".")){
        pedazos = InputCantidad.value.split(".",2);
        posicionDelPunto = InputCantidad.value.indexOf(".");
        posicionDelTarget = e.target.selectionStart;

        if(pedazos[1].length>3 && posicionDelTarget>posicionDelPunto){
            return false;
        }
    }

    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}