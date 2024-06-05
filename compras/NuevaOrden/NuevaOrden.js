let VentadaModalAgregarProducto = document.getElementById('VentadaModalAgregarProducto');
let ModalAgregarProducto = document.getElementById('ModalAgregarProducto');
let BotonCerrarVentanaProductos = document.getElementById('BotonCerrarVentanaProductos');
let BotonDelAsideAgregarProducto = document.getElementById('BotonAbrirModalAgregarProducto');
let BotonBuscarProductos = document.getElementById('BotonBuscarProductos');
let ListaDeProductosConsultados = document.getElementById('ListaDeProductosConsultados');
let EspacioDeRowsDeLaTabla = document.getElementById('EspacioDeRowsDeLaTabla');
let BotonDesplegable_AgregarProducto = document.getElementById('BotonDesplegable_AgregarProducto');
let DivDelPalitoDinamico = document.getElementById('DivDelPalitoDinamico');
let InputDeNombre = document.getElementById('InputDeNombre');
let CheckMostrarModalDeError = document.getElementById('CheckMostrarModalDeError');
let ModalDeErroresDelPOST = document.getElementById('ModalDeErroresDelPOST');

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

InputDeNombre.addEventListener('keyup', () => {
    if(InputDeNombre.value){
        DivDelPalitoDinamico.innerHTML = `
        <div class="PalitoDelNombre"></div>
        ${InputDeNombre.value}
    `;
    }else{
        DivDelPalitoDinamico.innerHTML = `
            <div class="PalitoDelNombre"></div>
            Escribe un nombre o descripción
        `;
    }
    
})

BotonDesplegable_AgregarProducto.addEventListener('click', () => {
    BotonDelAsideAgregarProducto.click();
})

var VisibilidadDelModal = false;
var TodosLosProveedores = "";
var TodosLosProductos = "";
var ProdcutosDeUltimaConsulta = "";
var CierreAutomaticoDelModalProductos = true;
let ArrayConIDs = [];
let ArrayConCantidades = [];
let ArrayConProveedores = [];

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
BotonBuscarProductos.addEventListener('click', () => {
    MostrarEnListaLosProductosConsultados(BuscadorDeProductos.value, SelectCategoriaABuscar.value);
});

document.getElementById('SelectCategoriaABuscar').addEventListener('change', function(){
    BotonBuscarProductos.click();
})

function MostrarCartaDeProductoSeleccionado(valor){
    if(valor){
        
        
        ProductoSeleccionado = TodosLosProductos.filter( function(ProductoDeLaLista){
            return ProductoDeLaLista.id == InputProductoAPrevisualizar.value;
        });
        ProductoSeleccionado = ProductoSeleccionado[0];
        
        
        PrevisualizacionDeProducto.innerHTML = `
            <div class="ProductoSiSeleccionado">
                <img src="../../Imagenes/Productos/${((ProductoSeleccionado.img)?ProductoSeleccionado.img:'ImagenPredefinida_Productos.png')}" alt="">
                <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${ProductoSeleccionado.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
                <b class="NombreDelProductoSiSeleccionado">${ProductoSeleccionado.name}</b>
                <b class="PrecioDelProductoSiSeleccionado">${ProductoSeleccionado.price}$</b>
                <div class="ElementosPaElegirCantidad">
                    <span>Cantidad:</span>
                    <div class="CantidadYUnidad${((ProductoSeleccionado.categoryID == 1)?' TextToNumbre':'')}">
                        <span class="fi-rr-cross-small"></span>
                        <input onkeypress="return ${((ProductoSeleccionado.categoryID == 1)?'onlyNumber':'onlyFloat')}(this, event)" onClick="this.select();" autocomplete="off" maxlength="9" id="InputCantidad">
                        ${((ProductoSeleccionado.categoryID == 1)?'<span title="'+ProductoSeleccionado.unit+'">'+ProductoSeleccionado.symbol+'</span>':'')}
                    </div>
                    <span id="PrecioMultiplicado" class="PrecioMultiplicado"></span>
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
        let SpanPrecioMultiplicado = document.getElementById('PrecioMultiplicado');

        

        CierreAutomaticoModalProductos.addEventListener('change', () => {
            CierreAutomaticoDelModalProductos = CierreAutomaticoModalProductos.checked
        })

        function ActualizarSpanPrecioMultiplicado(){
            PrecioMultiplicado = InputCantidad.value * ProductoSeleccionado.precio;
            SpanPrecioMultiplicado.innerText = "Total: " + PrecioMultiplicado.toFixed(2) + "$";
        }

        
        InputCantidad.addEventListener('blur', () => {
            if(!InputCantidad.value > 0){
                InputCantidad.value = '0';
            }
        })

        
        if(ProductoSeleccionado.categoryID == 1){
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
            InputCantidad.value = '0.0000';
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

async function ConsultarProductos2(Descripcion, IdCategoria){
    document.getElementById('ListaDeProductosConsultados').innerHTML = `<div class="did_loading">
        <div class="rotating"><span class="fi fi-rr-loading"></span></div>
        Cargando
    </div>`;

    try{
        url = `http://${ipserver}/CleoInventory/API/publicFunctions.php?method=getProductsToPurchase&description=${Descripcion}&category=${IdCategoria}`;
        response = await fetch(url);
        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                ProdcutosDeUltimaConsulta = petition.result;
                TodosLosProductos = petition.result;
            }
        }else{
            console.log(`Status: ${response.status}`);
        }
    }catch(err){
        console.log(err)
    }
}

async function ConsultarProductos(Descripcion, IdCategoria){
    document.getElementById('ListaDeProductosConsultados').innerHTML = `<div class="did_loading">
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

async function PrepararListaSegunInput(){
    
    await ConsultarProductos2("", 0);
    await ConsultarProveedores();
    

    await ActualizarArraysDeIDsYCantidades();
    
    await ActualizarProveedores();
    
}

window.addEventListener('load', () => {
    PrepararListaSegunInput();
    MostrarModalDeErrores(CheckMostrarModalDeError.checked);
})

CheckMostrarModalDeError.addEventListener('change', () => {
    MostrarModalDeErrores(CheckMostrarModalDeError.checked);
})

async function MostrarModalDeErrores(valor){
    
    if(valor){
        ModalDeErroresDelPOST.style = "display: flex;";
    }else{
        ModalDeErroresDelPOST.style = "";
    }
}

async function ConsultarProveedores(){
    try{
        let respuesta = await fetch("http://"+ipserver+"/CleoInventory/API/API_Proveedores.php");

        if(respuesta.status === 200){
            ProveedoresRecibidos = await respuesta.json();

            if(ProveedoresRecibidos.objetos == undefined){
                ProveedoresRecibidos = [];
            }else{
                ProveedoresRecibidos = ProveedoresRecibidos.objetos;
            }

            TodosLosProveedores = ProveedoresRecibidos;
        }

    }catch(error){
        console.log(error);
    }
}



async function MostrarEnListaLosProductosConsultados(Descripcion, IdCategoria){
    await ConsultarProductos2(Descripcion, IdCategoria);

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

    var HayScroll = isScrollable(document.getElementById('ContenedorScrolleable'));

    ProdcutosDeUltimaConsulta.forEach(producto => {
        
        HTMLConLosProductosAMostrarEnLaLista = HTMLConLosProductosAMostrarEnLaLista + `
            <row class="RowProductoConsultado" id="RowProductoConsultado-${producto.id}">
                <celda class="ColumnaImagenP">
                    <img src="../../Imagenes/Productos/${((producto.img)?producto.img:'ImagenPredefinida_Productos.png')}" alt="">
                </celda>
                <celda class="ColumnaIDP">${producto.id}</celda>
                <celda class="${((HayScroll)?'ColumnaNombre2SS':'ColumnaNombre2CS')}">${producto.name}</celda>
                <celda class="ColumnaExistencia" title="${producto.existence} ${producto.unit} en el inventario">
                    ${((producto.statusID == 1)?'':((producto.statusID == 2)?'<span title="Este producto se encuentra bajo el nivel de alerta (' + producto.alertLevel + ' ' + producto.unit + ')" style="color: #FEA82F;" class="AlertaDeExistencia fi-sr-comment-exclamation"></span>':'<span title="Este producto se encuentra agotado." style="color: rgb(236, 49, 49);" class="AlertaDeExistencia fi-sr-comment-exclamation"></span>'))} ${producto.existence}
                </celda>
                <celda class="${((HayScroll)?'ColumnaAgregadoSS':'ColumnaAgregadoCS')}">x ${((ArrayConIDs.includes(producto.id))?ArrayConCantidades[ArrayConIDs.indexOf(producto.id)]:'0')}</celda>
            </row>
        `;
    });

    ListaDeProductosConsultados.innerHTML = HTMLConLosProductosAMostrarEnLaLista;
    

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

async function ActualizarProveedores(){

    ArrayConProveedores = [];
    ListaDeProveedores = "";

    if(InputProductosEnlistadosAlAlmacen.value!=''){
        InputProductosEnlistadosAlAlmacen.value.split('¿').forEach( ProdXCant => {    
            pedazos = ProdXCant.split('x');
            ArrayConProveedores.push(pedazos[0]);
    
            ProductoFiltrado = TodosLosProductos.filter( function(ProductoDeLaLista){
                return ProductoDeLaLista.id == pedazos[0];
            });
            
            
            ListaDeProveedores = ListaDeProveedores + ((ListaDeProveedores)?"x" + ProductoFiltrado[0].listaDeProveedores:ProductoFiltrado[0].listaDeProveedores);
        })
    
        let result = ListaDeProveedores.split('x').filter((item,index)=>{
            return ListaDeProveedores.split('x').indexOf(item) === index;
        })
    
        ArrayConProveedores = result;
        
        
        
        MostrarCartasDeProveedor();
    }
}

let CajaDeProveedores = document.getElementById('CajaDeProveedores');

async function MostrarCartasDeProveedor(){
    CajaDeProveedores.innerHTML  = "";

    
    
    ArrayConProveedores.forEach( (ID_Proveedor) => {
        if(ID_Proveedor > 0){
            

            ProveedorAMostrar = TodosLosProveedores.filter( function(ProductoDeLaLista){
                return ProductoDeLaLista.rif == ID_Proveedor;
            });

            ProveedorAMostrar = ProveedorAMostrar[0];

            
            HTMLDeProductos = "";

            ProveedorAMostrar.listaDeProductos.forEach( (ProductoDelProveedorAMostrar) => {
                
                if(ArrayConIDs.includes(ProductoDelProveedorAMostrar.id)){
                    
                    ProductoPaLaLista = ProductoDelProveedorAMostrar;

                    HTMLDeProductos = HTMLDeProductos + `
                    <div class="CardProductoProveido">
                        <celda class="CeldaImagenPP">
                            <img src="../../Imagenes/Productos/${((ProductoPaLaLista.ULRImagen)?ProductoPaLaLista.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                        </celda>
                        <celda class="CeldaNombrePP">${ProductoPaLaLista.nombre}</celda>
                    </div>
                    `;
                };
            })


            

            CajaDeProveedores.innerHTML = CajaDeProveedores.innerHTML + `
            <div class="CardProveedor">
                <a href="../../Proveedores/Proveedor?rif=${ProveedorAMostrar.rif}" target="_blank" class="DivDeImgYNombreDeProveedor">
                    <img src="../../Imagenes/Proveedores/${((ProveedorAMostrar.ULRImagen)?ProveedorAMostrar.ULRImagen:'ImagenPredefinida_Proveedores.png')}" alt="">
                    <div class="RifYNombre">
                    <span class="NombreProveedor">${ProveedorAMostrar.nombre}</span>
                        <span class="RifProveedor">${ProveedorAMostrar.tipoDeDocumento + '-' + ProveedorAMostrar.rif}</span>
                    </div>
                </a>
                <span class="TituloProductosDeProveedor"> <i class="fi-sr-package"></i> PRODUCTOS: </span>
                <div class="ContenedorDelFlexDeProveedores">
        
                    <div class="FlexDeProductosProveidos mostly-customized-scrollbar">
                    ` + HTMLDeProductos + `
                    </div>
                </div>
            </div>
            `;

        }
    });

    if(!CajaDeProveedores.innerHTML){
        CajaDeProveedores.innerHTML = `
        <div class="ProveedoresVacios">
                    No hay proveedores disponibles
                </div>
        `;
    }
}

function AgregarProductoALaLista(Producto, Cantidad){
    
    if(Cantidad > 0){
        HayMasProductos = InputProductosEnlistadosAlAlmacen.value;
        
        if(Producto.idcategoria == 2){
            Cantidad = Number(Cantidad);
        }
        InputProductosEnlistadosAlAlmacen.value = Producto.id + 'x' + Cantidad + ((InputProductosEnlistadosAlAlmacen.value)?'¿' + InputProductosEnlistadosAlAlmacen.value:'');    
        ActualizarArraysDeIDsYCantidades();

        EspacioDeRowsDeLaTabla.innerHTML = `
        <row id="RowDeProducto-${Producto.id}">
                        <celda class="ColumnaImagen"><img src="../../Imagenes/Productos/${((Producto.img)?Producto.img:'ImagenPredefinida_Productos.png')}" alt=""></celda>
                        <celda class="ColumnaID">${Producto.id}</celda>
                        <celda class="ColumnaProducto">${Producto.name}</celda>
                        <celda class="ColumnaCantidad">x ${Cantidad}</celda>
                        <div class="CeldaOculta">
                            <i title="Modificar la cantidad de este producto" id="BotonModificarProductoEspecifico-${Producto.id}" class="fi-rr-pencil"></i>
                            <i title="Eliminar este producto de la lista" id="BotonEliminarProductoEspecifico-${Producto.id}" class="fi-rr-trash"></i>
                        </div>
                    </row>
        ` + ((HayMasProductos)?EspacioDeRowsDeLaTabla.innerHTML:'');
        
    
        MostrarModalAgregarProducto(!CierreAutomaticoDelModalProductos);
        if(!CierreAutomaticoDelModalProductos){
            MostrarCartaDeProductoSeleccionado(false);
        }
        ActualizarProveedores();
    }else{
        Toast.fire({
            icon: 'warning',
            title: 'La cantidad debe ser mayor a 0'
        });
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

            if(Producto.idcategoria == 2){
                CantidadNueva = Number(CantidadNueva);
            }
            
            //Actualizo la interfaz
            document.getElementById('RowDeProducto-'+Producto.id).remove();
            EspacioDeRowsDeLaTabla.innerHTML = `
            <row id="RowDeProducto-${Producto.id}">
                        <celda class="ColumnaImagen"><img src="../../Imagenes/Productos/${((Producto.img)?Producto.img:'ImagenPredefinida_Productos.png')}" alt=""></celda>
                        <celda class="ColumnaID">${Producto.id}</celda>
                        <celda class="ColumnaProducto">${Producto.name}</celda>
                        <celda class="ColumnaCantidad">${((Producto.statusID == 2)?'<span class="fi-sr-comment-exclamation alertatipo2" title="Este producto se encuentra bajo su nivel de alerta"></span>':'<span class="fi-sr-comment-exclamation alertatipo3" title="Este producto está agotado"></span>')}x ${CantidadNueva}</celda>
                        <div class="CeldaOculta">
                            <i class="fi-rr-pencil"></i>
                            <i class="fi-rr-trash"></i>
                        </div>
                    </row>
        ` + EspacioDeRowsDeLaTabla.innerHTML;
            
            
            //Vuelvo o me quedo segun cierre automatico
            MostrarModalAgregarProducto(!CierreAutomaticoDelModalProductos);
            if(!CierreAutomaticoDelModalProductos){
                MostrarCartaDeProductoSeleccionado(false);
            }
        }else{
            alert('No se encontró el producto ' + Producto.id + ' en la lista');
        }
    }else{
        alert('La cantidad debe ser mayor a 0.')
    }   
}

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
        ActualizarProveedores();
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

async function ActualizarArraysDeIDsYCantidades(){
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

//FUNCIONES PARA DORMIR Y OTRAS COSAS
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
async function EsperarMS(Milisegundos) {
    for (let i = 0; i < 3; i++) {
        await sleep(i * Milisegundos);
    }
}

function isScrollable(e){
    if( e.scrollTopMax !== undefined )
        return e.scrollTopMax > 0; //All Hail Firefox and it's superior technology!

    if( e == document.scrollingElement ) //If what you're checking is BODY (or HTML depending on your css styles)
        return e.scrollHeight > e.clientHeight; //This is a special case.

    return e.scrollHeight > e.clientHeight && ["scroll", "auto"].indexOf(getComputedStyle(e).overflowY) >= 0
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


function onlyNumber(element, e){

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







function onlyNumber(element, e){
    if(isNaN(e.key)){
        return false;
    }
}




function onlyFloat(element, e){
    if(isNaN(e.key)){
        if(e.key != '.'){
            return false;
        }

        
        if(element.value.includes('.')){
            return false;
        }
    }else{
        if(element.value.includes('.')){
            pieces = element.value.split('.');
            if(pieces[1].length > 3){
                if(Number(element.value)>0){
                    return false;
                }
            }
        }
    }
}
