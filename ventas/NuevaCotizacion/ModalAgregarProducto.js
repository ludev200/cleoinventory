let ModalAgregarProducto = document.getElementById('ModalAgregarProducto');
let ContenidoDelModal = document.getElementById('ContenidoDelModal');
let BotonCerrarVentanaProductos = document.getElementById('BotonCerrarVentanaProductos');
let EspacioDeProductosConsultados = document.getElementById('EspacioDeProductosConsultados');
let InputBuscadorDeProductos = document.getElementById('BuscadorDeProductos');
let BotonBuscarProductos = document.getElementById('BotonBuscarProductos');
let SelectCategoriaDelProductoABuscar = document.getElementById('SelectCategoriaDelProductoABuscar');
let PrevisualizacionDeProducto = document.getElementById('PrevisualizacionDeProducto');
let BotonAgregarProductoMaterial = document.getElementById('BotonAgregarProductoMaterial');
let BotonAgregarProductoHerramienta = document.getElementById('BotonAgregarProductoHerramienta');
let BotonAgregarProductoMano = document.getElementById('BotonAgregarProductoMano');
let InputIdProductoAAgregar = document.getElementById('InputIdProductoAAgregar');
let BotonAbrirModalAgregarProducto = document.getElementById('BotonAbrirModalAgregarProducto');
let MaterialesAgregados = document.getElementById('MaterialesAgregados');
let HerramientasAgregados = document.getElementById('HerramientasAgregados');
let ManoDeObraAgregados = document.getElementById('ManoDeObraAgregados');
let CuerpoDeLaCotizacion = document.querySelector('.CuerpoDeLaCotizacion');


var ProdcutosObtenidosPaVerDespues;
var CierreAutomaticoDelModalProductos = true;

var ArrayConLasIDs = new Array();
var ArrayConLosPrecios = new Array();
var ArrayConLasIDsDeMateriales = new Array();
var ArrayConLasIDsDeMaquinas = new Array();
var ArrayConLasIDsDeMano = new Array();

let MaterialesCotizados = document.getElementById('ProductosCotizados');
let MaquinasCotizados = document.getElementById('MaquinasCotizados');
let ManosCotizados = document.getElementById('ManosCotizados');

//BOTONES LATERALES (EDITAR - ELIMINAR)
CuerpoDeLaCotizacion.addEventListener('click', function(event) {
    
    if(event.target.tagName.toLowerCase() == 'i') {
        pedazos = event.target.id.split('-',2);

        if(pedazos[0] == 'BotonEliminarProductoEspecifico'){
            if(pedazos.length == 2){
                if(isNumeric(pedazos[1])){
                    EliminarUnProductoDeLaCotizacion(pedazos[1]);
                }else{
                    alert('La id no es numerica');
                }
            }else{
                alert('La id es desconocida');
            }
        }else if(pedazos[0] == 'BotonModificarProductoEspecifico'){
            if(pedazos.length == 2){
                if(isNumeric(pedazos[1])){
                    
                    MostrarModalProductos('true');
                    InputIdProductoAAgregar.value = pedazos[1];
                    CambiarPrevisualizacion();
                    
                    var RowProductoAEditar = document.getElementsByClassName('ProductoSeleccionado');
                    

                    
                    
                    
                }else{
                    alert('La id no es numerica');
                }
            }else{
                alert('La id es desconocida');
            }
        }else{
            alert('Icono desconocido');
        }        
    }
});


function EliminarUnProductoDeLaCotizacion(IDABorrar){
    var CategoriaEncontrada = false;
    var PosicionDelProductoEnElArray = -1;
    console.log(`Borrando ${IDABorrar}`)
    
    //Busco el producto en los materiales
    MaterialesCotizados.value.split('¿').some(function(MaterialConPrecio) {
        pedazos = MaterialConPrecio.split('x');
        CategoriaEncontrada = (IDABorrar == pedazos[0]);
        if(CategoriaEncontrada){
            PosicionDelProductoEnElArray = MaterialesCotizados.value.split('¿').indexOf(MaterialConPrecio);
        }
        return CategoriaEncontrada;
    })

    //Si ya encontré el producto, lo elimino de materiales; sino, lo busco en maquinas
    if(CategoriaEncontrada){
        var ValorNuevoParaElInput = MaterialesCotizados.value.split('¿');
        ValorNuevoParaElInput.splice(PosicionDelProductoEnElArray, 1);
        MaterialesCotizados.value = ValorNuevoParaElInput.join('¿');

        if(MaterialesCotizados.value){
            document.getElementById('MaterialEnLista-' + IDABorrar).remove();
        }else{
            MaterialesAgregados.innerHTML = `
                    <row>
                        <span class="TablaVacia">Esta cotización no tiene materiales</span>
                    </row>
            `;
        }
        ActualizarTotales();
        
    }else{
        MaquinasCotizados.value.split('¿').some(function(MaquinaConPrecio) {
            pedazos = MaquinaConPrecio.split('x');
            CategoriaEncontrada = (IDABorrar == pedazos[0]);
            if(CategoriaEncontrada){
                PosicionDelProductoEnElArray = MaquinasCotizados.value.split('¿').indexOf(MaquinaConPrecio);
            }
            return CategoriaEncontrada;
        })
        
        //Si encuentro el producto lo elimino de maquinas; sino, lo busco en mano de obra
        if(CategoriaEncontrada){
            var ValorNuevoParaElInput = MaquinasCotizados.value.split('¿');
            ValorNuevoParaElInput.splice(PosicionDelProductoEnElArray, 1);
            MaquinasCotizados.value = ValorNuevoParaElInput.join('¿');

            if(MaquinasCotizados.value){
                document.getElementById('MaterialEnLista-' + IDABorrar).remove();
            }else{
                HerramientasAgregados.innerHTML = `
                        <row>
                            <span class="TablaVacia">Esta cotización no tiene herramientas ni maquinaria</span>
                        </row>
                `;
            }
            ActualizarTotales();

        }else{
            ManosCotizados.value.split('¿').some(function(ManoConPrecio) {
                pedazos = ManoConPrecio.split('x');
                CategoriaEncontrada = (IDABorrar == pedazos[0]);
                if(CategoriaEncontrada){
                    PosicionDelProductoEnElArray = ManosCotizados.value.split('¿').indexOf(ManoConPrecio);
                }
                return IDABorrar == pedazos[0];
            })

            //si encuentro el producto, lo elimino de mano de obra
            if(CategoriaEncontrada){
                var ValorNuevoParaElInput = ManosCotizados.value.split('¿');
                ValorNuevoParaElInput.splice(PosicionDelProductoEnElArray, 1);
                ManosCotizados.value = ValorNuevoParaElInput.join('¿');

                if(ManosCotizados.value){
                    document.getElementById('MaterialEnLista-' + IDABorrar).remove();
                }else{
                    ManoDeObraAgregados.innerHTML = `
                            <row>
                                <span class="TablaVacia">Esta cotización no tiene mano de obra</span>
                            </row>
                    `;
                }
                ActualizarTotales();
            }else{
                Toast.fire({
                    icon: 'warning',
                    title: 'No se encontró el producto de ID ' + IDABorrar
                });
            }
        }
        
    }
    
}
        
MaterialesCotizados.addEventListener('click', () => {
    MaterialesCotizados.blur();
})


let ConsultarProductos = async() => {
    document.getElementById('EspacioDeProductosConsultados').innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    try{
        var DescripcionABuscar = InputBuscadorDeProductos.value;
        var CategoriaABuscar = SelectCategoriaDelProductoABuscar.value;

        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?descripcion=' + DescripcionABuscar + '&categoria=' + CategoriaABuscar);

        if(respuesta.status === 200){
            PeticionConFiltros = await respuesta.json();

            
            if(!DescripcionABuscar && CategoriaABuscar == 0){
                ProdcutosObtenidosPaVerDespues = PeticionConFiltros;
            }
            

            let Productos = "";
            EspacioDeProductosConsultados.innerHTML =
            `<div class="Flex-gap2 HoverVino TablaDeproductosVacia">
                <span>No hay productos para mostrar...</span>
            </div>`;

            
            EspacioDeProductosConsultados.style.height = (Math.round(window.innerHeight * 0.8) - 145) + 'px';
            

            ActualizarArrays();

            if(PeticionConFiltros.objetos.length > 0){                
                TamanioMaxDelDiv = Math.round(window.innerHeight * 0.8) - 145;
                TamanioDeResultados = (PeticionConFiltros.objetos.length * 74) + (1 * (PeticionConFiltros.objetos.length - 1));
            }


            PeticionConFiltros.objetos.forEach(producto => {
                if(ArrayConLasIDs.indexOf(producto.id) > -1 ){
                    if(producto.idcategoria < 3){
                        var CantidadCotizada = ArrayConLosPrecios[ArrayConLasIDs.indexOf(producto.id)];
                    }else{
                        SupuestoFloat = ArrayConLosPrecios[ArrayConLasIDs.indexOf(producto.id)].split('.');

                        var CantidadCotizada = SupuestoFloat[0] + 'x' + SupuestoFloat[1];
                    }
                }else{
                    var CantidadCotizada = '0';
                }
    
                Productos = Productos + `
                    <div class="Flex-gap2 HoverVino Pointer ProductoEnRowDeBusqueda${((producto.id == InputIdProductoAAgregar.value)?' ProductoSeleccionado':'')}" idProducto="${producto.id}">
                        <span class="Celda ColumnaImagen">
                            <img src="../../Imagenes/Productos/${(producto.ULRImagen)?producto.ULRImagen:'ImagenPredefinida_Productos.png'}" alt="">
                        </span>
                        <span class="Celda ColumnaID">${producto.id}</span>
                        <span style="width: calc(100% - ${((TamanioMaxDelDiv < TamanioDeResultados)?'255':'275')}px);" class="Celda ColumnaNombre4">${producto.nombre}</span>
                        <div style="width: ${((TamanioMaxDelDiv < TamanioDeResultados)?'60px':'80px')};" class="Celda ColumnaCantidad">
                            ${CantidadCotizada}
                        </div>
                    </div>`;
            });
            EspacioDeProductosConsultados.innerHTML = Productos;


            if(TamanioMaxDelDiv < TamanioDeResultados){
                EspacioDeProductosConsultados.style.height = TamanioMaxDelDiv + 'px';
                EspacioDeProductosConsultados.style.overflow = 'auto'
            }else{
                EspacioDeProductosConsultados.style.height = TamanioDeResultados + 'px';
                EspacioDeProductosConsultados.style.overflow = 'unset';
            }

            let GrupoDeRows = document.querySelectorAll('.ProductoEnRowDeBusqueda');
            GrupoDeRows.forEach((ProductoEnRowDeBusqueda) => {
                ProductoEnRowDeBusqueda.addEventListener('click', () => {
                    GrupoDeRows.forEach((XDD) => {
                        XDD.classList = 'Flex-gap2 HoverVino Pointer ProductoEnRowDeBusqueda';
                    })
                    
                    ProductoEnRowDeBusqueda.classList = 'Flex-gap2 HoverVino Pointer ProductoEnRowDeBusqueda ProductoSeleccionado';
                    InputIdProductoAAgregar.value = ProductoEnRowDeBusqueda.getAttribute('idProducto');
                    CambiarPrevisualizacion();
                })
            })
        }
    }catch(error){
        console.log(error);
    }   
}


function CambiarPrevisualizacion(){    
    if(InputIdProductoAAgregar.value){
        ProductoSeleccionado = ProdcutosObtenidosPaVerDespues.objetos.filter(function(element){
            return element.id == InputIdProductoAAgregar.value;
        });
        ProductoSeleccionado = ProductoSeleccionado[0];


        if(ProductoSeleccionado.idcategoria == 1){
            PrevisualizacionDeProducto.innerHTML = `
                <div class="ProductoSiSeleccionado">
                    <img src="../../Imagenes/Productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                    <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${ProductoSeleccionado.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
                    <b class="NombreDelProductoSiSeleccionado">${ProductoSeleccionado.nombre}</b>
                    <b class="PrecioDelProductoSiSeleccionado">${ProductoSeleccionado.precio}$</b>
                    <div class="ElementosPaElegirCantidad">
                        <span>Cantidad:</span>
                        <div class="CantidadYUnidad TextToNumbre">
                            <span class="fi-rr-cross-small"></span>
                            <input onClick="this.select();" onkeypress="return SoloNumerosInt(event)" onpaste="return false" autocomplete="off" maxlength="8" type="text" id="InputDeMultiplicar">
                            <span title="${ProductoSeleccionado.nombredeunidad}">${ProductoSeleccionado.simbolo}</span>
                        </div>
                        <span id="PrecioMultiplicado" class="PrecioMultiplicado">Total: 0.00$</span>
                    </div>
                    <div id="CajaDeBotonBorrarYAgregar"></div>
                    <label title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
                        Cierre automático
                        <input type="checkbox" ${((CierreAutomaticoDelModalProductos)?'checked':'')} name="" id="CierreAutomaticoModalProductos">
                        <div class="slider round"></div>
                    </label>
                </div>
            `;
        }else if(ProductoSeleccionado.idcategoria == 2){
            PrevisualizacionDeProducto.innerHTML = `
            <div class="ProductoSiSeleccionado">
                <img src="../../Imagenes/Productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${ProductoSeleccionado.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
                <b class="NombreDelProductoSiSeleccionado">${ProductoSeleccionado.nombre}</b>
                <b class="PrecioDelProductoSiSeleccionado">${ProductoSeleccionado.precio}$</b>
                <div class="ElementosPaElegirCantidad">
                    <span>Cantidad:</span>
                    <div>
                        <div class="CantidadYUnidad">
                            <input style="width: 80px;" onClick="this.select();" onkeypress="return SoloNumerosInt(event)" onpaste="return false" autocomplete="off" maxlength="6" type="text" id="InputDeMultiplicar">
                        </div>
                        <div style="text-align: center; font-size: 13px; color: gray;" title="Depreciación del producto"><span>x </span><span id="spoilage">${ProductoSeleccionado.depreciacion}</span></div>
                    </div>
                    <span id="PrecioMultiplicado" class="PrecioMultiplicado">Total: 0.00$</span>
                </div>
                <div id="CajaDeBotonBorrarYAgregar"></div>
                <label title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
                    Cierre automático
                    <input type="checkbox" ${((CierreAutomaticoDelModalProductos)?'checked':'')} name="" id="CierreAutomaticoModalProductos">
                    <div class="slider round"></div>
                </label>
            </div>
            `;
        } else{
            PrevisualizacionDeProducto.innerHTML = `
                <div class="ProductoSiSeleccionado">
                    <img src="../../Imagenes/Productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                    <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${ProductoSeleccionado.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
                    <b class="NombreDelProductoSiSeleccionado">${ProductoSeleccionado.nombre}</b>
                    <b class="PrecioDelProductoSiSeleccionado">${ProductoSeleccionado.precio}$</b>
                    <div class="ElementosPaElegirCantidad">
                        <div class="CantidadYUnidad TextToNumbre">
                            <span class="fi-rr-cross-small"></span>
                            <input onClick="this.select();" onkeypress="return SoloIntParaPersonas(event)" onpaste="return false" autocomplete="off" maxlength="6" type="text" id="InputDePersonas">
                            <span title="">Personas</span>
                        </div>
                        <div class="CantidadYUnidad TextToNumbre">
                            <span class="fi-rr-cross-small"></span>
                            <input onClick="this.select();" onkeypress="return SoloIntParaDias(event)" onpaste="return false" autocomplete="off" maxlength="6" type="text" id="InputDeDias">
                            <span title="">Días</span>
                        </div>
                        <span id="PrecioMultiplicado" class="PrecioMultiplicado">Total: 0.00$</span>
                    </div>
                    <div id="CajaDeBotonBorrarYAgregar"></div>
                    <label title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
                        Cierre automático
                        <input type="checkbox" ${((CierreAutomaticoDelModalProductos)?'checked':'')} name="" id="CierreAutomaticoModalProductos">
                        <div class="slider round"></div>
                    </label>
                </div>
            `;
        }

        //Cuando el producto a mostrar es de categoria cualquiera
        let InputCierreAutomaticoModalProductos = document.getElementById('CierreAutomaticoModalProductos');
        let CajaDeBotonBorrarYAgregar = document.getElementById('CajaDeBotonBorrarYAgregar');
        let PrecioMultiplicado = document.getElementById('PrecioMultiplicado');

        var CantidadCotizada = ArrayConLosPrecios[ArrayConLasIDs.indexOf(ProductoSeleccionado.id)];

        

        InputCierreAutomaticoModalProductos.addEventListener('change', () => {
            CierreAutomaticoDelModalProductos = InputCierreAutomaticoModalProductos.checked
        })


        CajaDeBotonBorrarYAgregar.innerHTML = `            
            <button id="BotonParaAgregarElProductoSeleccionado" title="Agregar este producto a la cotización" class="BotonParaAgregarElProductoSeleccionado">Agregar</button>
        `;

        document.getElementById('InputDeMultiplicar')?.addEventListener('keyup', (e)=>{
            if(e.keyCode == 13){
                document.getElementById('BotonParaAgregarElProductoSeleccionado')?.click();
            }
        })
        document.getElementById('InputDePersonas')?.addEventListener('keyup', (e)=>{
            if(e.keyCode == 13){
                document.getElementById('InputDeDias')?.select();
            }
        })
        document.getElementById('InputDeDias')?.addEventListener('keyup', (e)=>{
            if(e.keyCode == 13){
                document.getElementById('BotonParaAgregarElProductoSeleccionado')?.click();
            }
        })

        

        if(ProductoSeleccionado.idcategoria < 3){
            if(ProductoSeleccionado.idcategoria == 1){
                //Cuando el producto a mostrar es Material o Equipo
                let InputDeMultiplicar = document.getElementById('InputDeMultiplicar');

                if(ProductoSeleccionado.simbolo == '?'){
                    InputDeMultiplicar.value = '0.0000';
                }else{
                    InputDeMultiplicar.value = '0';
        
                    InputDeMultiplicar.addEventListener('mouseenter', () => {
                        InputDeMultiplicar.type = 'number';
                    }) 
            
                    InputDeMultiplicar.addEventListener('mouseleave', () => {
                        InputDeMultiplicar.type = 'text';
                    })
                }

                
                

                PrecioMultiplicado.innerText = 'Total: ' + (InputDeMultiplicar.value * ProductoSeleccionado.precio).toFixed(2) + '$';

                InputDeMultiplicar.addEventListener('change', () => {
                    if(InputDeMultiplicar.value < 0){
                        InputDeMultiplicar.value = '0';
                    }
                    PrecioMultiplicado.innerText = 'Total: ' + (InputDeMultiplicar.value * ProductoSeleccionado.precio).toFixed(2) + '$';
                })
        
                InputDeMultiplicar.addEventListener('keyup', (event) => {
                    event.preventDefault();
                    if ((event.keyCode === 13)) {
                        BotonParaAgregarElProductoSeleccionado.click();
                    }
                })
        
                InputDeMultiplicar.addEventListener('blur', () => {
                    if(!InputDeMultiplicar.value){
                        InputDeMultiplicar.value = '0';
                    }
                })
        
                InputDeMultiplicar.addEventListener('keyup', () => {
                    PrecioMultiplicado.innerText = 'Total: ' + (InputDeMultiplicar.value * ProductoSeleccionado.precio).toFixed(2) + '$';
                })

                if(CantidadCotizada > 0){
                    InputDeMultiplicar.value = CantidadCotizada;
                    CajaDeBotonBorrarYAgregar.innerHTML = `
                        <span title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>
                        <button title="Modificar la cantidad cotizada de este producto" id="BotonParaAgregarElProductoSeleccionado" title="Agregar a la cotizacion" class="BotonParaAgregarElProductoSeleccionado">Modificar</button>
                    `;
        
                    
                }
            }else{
                spoilage = Number(document.getElementById('spoilage').innerText);
                price = Number(ProductoSeleccionado.precio);
                InputDeMultiplicar.addEventListener('keyup', () => {
                    quantity = Number(InputDeMultiplicar.value);
                    total = spoilage * price * quantity;
                    PrecioMultiplicado.innerText = `Total: ${total.toFixed(2)}$`;
                })


                
                
                if(CantidadCotizada > 0){
                    InputDeMultiplicar.value = CantidadCotizada;
                    CajaDeBotonBorrarYAgregar.innerHTML = `
                        <span title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>
                        <button title="Modificar la cantidad cotizada de este producto" id="BotonParaAgregarElProductoSeleccionado" title="Agregar a la cotizacion" class="BotonParaAgregarElProductoSeleccionado">Modificar</button>
                    `;
                }
                
            }

            

        }else{
            //Cuando el producto a mostrar es mano o comida:
            let InputDePersonas = document.getElementById('InputDePersonas');
            let InputDeDias = document.getElementById('InputDeDias');
            
            InputDePersonas.value = '0';
            InputDeDias.value = '0';
    
            //Transformar inputs
            InputDePersonas.addEventListener('mouseenter', () => {
                InputDePersonas.type = 'number';
            }) 
            InputDePersonas.addEventListener('mouseleave', () => {
                InputDePersonas.type = 'text';
            })

            InputDeDias.addEventListener('mouseenter', () => {
                InputDeDias.type = 'number';
            }) 
            InputDeDias.addEventListener('mouseleave', () => {
                InputDeDias.type = 'text';
            })

            //Mostrar lo que ya esta elegido, si es que lo hay
            if(CantidadCotizada > 0){
                Pedazos = CantidadCotizada.split('.');

                InputDePersonas.value = Pedazos[0];
                InputDeDias.value = Pedazos[1];

                CajaDeBotonBorrarYAgregar.innerHTML = `
                    <span title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>
                    <button title="Modificar la cantidad cotizada de este producto" id="BotonParaAgregarElProductoSeleccionado" title="Agregar a la cotizacion" class="BotonParaAgregarElProductoSeleccionado">Modificar</button>
                `;

                
            }

            
            //Mostrar total de lo elegido
            PrecioMultiplicado.innerText = 'Total: ' + (InputDePersonas.value * InputDeDias.value * ProductoSeleccionado.precio).toFixed(2) + '$';

            InputDePersonas.addEventListener('change', () => {
                PrecioMultiplicado.innerText = 'Total: ' + (InputDePersonas.value * InputDeDias.value * ProductoSeleccionado.precio).toFixed(2) + '$';
            });
            InputDePersonas.addEventListener('keyup', () => {
                PrecioMultiplicado.innerText = 'Total: ' + (InputDePersonas.value * InputDeDias.value * ProductoSeleccionado.precio).toFixed(2) + '$';
            });
            InputDeDias.addEventListener('change', () => {
                PrecioMultiplicado.innerText = 'Total: ' + (InputDePersonas.value * InputDeDias.value * ProductoSeleccionado.precio).toFixed(2) + '$';
            });
            InputDeDias.addEventListener('keyup', () => {
                PrecioMultiplicado.innerText = 'Total: ' + (InputDePersonas.value * InputDeDias.value * ProductoSeleccionado.precio).toFixed(2) + '$';
            });


            

            
        }


        
        document.getElementById('BotonRemoverProductoSeleccionado')?.addEventListener('click', () => {
                    console.log('AQUI')
                    EliminarUnProductoDeLaCotizacion(ProductoSeleccionado.id);
                    MostrarModalProductos(!CierreAutomaticoDelModalProductos);
                    LimpiarPrevisualizacionDelProductoSeleccionado();
                    InputIdProductoAAgregar.value = '';
                })

        //otra vez, cosas generales:
        let BotonParaAgregarElProductoSeleccionado  = document.getElementById('BotonParaAgregarElProductoSeleccionado');
        

        if(ProductoSeleccionado.idcategoria < 3){
            //otra vez, cosas para Materiales y Equipos

            
            BotonParaAgregarElProductoSeleccionado.addEventListener('click', () => {
                if(InputDeMultiplicar.value > 0){
                    //veo si el producto es material(1), maquina(2/3) o mano (4)
                    if(ProductoSeleccionado.idcategoria == 1){
                        var HayMaterialesAdeMasDelNuevo = MaterialesCotizados.value;
    
                        ArrayConLoDelInput = MaterialesCotizados.value.split('¿');
                        ArrayConLasIDsDeMateriales = [];
                        ArrayConLoDelInput.forEach( (MaterialConPrecio) => {
                            pedazos = MaterialConPrecio.split('x');
                            ArrayConLasIDsDeMateriales.push(pedazos[0]);
                        })
    
                        if(ArrayConLasIDs.indexOf(ProductoSeleccionado.id) > -1){
                            ArrayConLoDelInput[ArrayConLasIDs.indexOf(ProductoSeleccionado.id)] = ProductoSeleccionado.id + 'x' + InputDeMultiplicar.value;
                            MaterialesCotizados.value = ArrayConLoDelInput.join('¿');
        
                            var RowVieja = document.getElementById('MaterialEnLista-' + ProductoSeleccionado.id)
                            RowVieja.remove();
                        }else{
                            MaterialesCotizados.value = ProductoSeleccionado.id + 'x' + InputDeMultiplicar.value + ((MaterialesCotizados.value)? '¿' + MaterialesCotizados.value:'');    
                        }
    
                        MaterialesAgregados.innerHTML = `
                        <row id="MaterialEnLista-${ProductoSeleccionado.id}" class="Flex-gap2">
                            <div class="test9">
                                <i id="BotonModificarProductoEspecifico-${ProductoSeleccionado.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                                <i id="BotonEliminarProductoEspecifico-${ProductoSeleccionado.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">${ProductoSeleccionado.id}</span>
                            <span class="ColumnaNombre CeldaSinH">${ProductoSeleccionado.nombre}</span>
                            <span class="ColumnaCantidad CeldaSinH">x ${InputDeMultiplicar.value}</span>
                            <span title="${((ProductoSeleccionado.simbolo != '?')?ProductoSeleccionado.nombredeunidad:'')}" class="ColumnaUnidad CeldaSinH">${((ProductoSeleccionado.simbolo != '?')?ProductoSeleccionado.simbolo:'-')}</span>
                            <span class="ColumnaPrecio CeldaSinH">${ProductoSeleccionado.precio}$</span>
                            <span class="ColumnaTotal CeldaSinH">${(ProductoSeleccionado.precio * InputDeMultiplicar.value).toFixed(2)}$</span>
                        </row>
                        ` + ((HayMaterialesAdeMasDelNuevo)?MaterialesAgregados.innerHTML:'');
    
                    }else{
                        //Determino si hay maquinas cotizadas para luego de cambiar el valor del input
                        //saber si debere solo agregar este producto o tambien agregar los demas viejos
                        var HabianMaquinasCotizadasAdemasDeEsta = MaquinasCotizados.value;
    
                        //Actualizo el array global de IDs de maquinas
                        ArrayConLoDelInput = MaquinasCotizados.value.split('¿');
                        ArrayConLasIDsDeMaquinas = [];
                        ArrayConLoDelInput.forEach( (MaquinaConPrecio) => {
                            pedazos = MaquinaConPrecio.split('x');
                            ArrayConLasIDsDeMaquinas.push(pedazos[0]);
                        })
    
                        //Determino si ya el producto ya esta en la listade cotizados
                        if(ArrayConLasIDsDeMaquinas.indexOf(ProductoSeleccionado.id) > -1){
                            //sobreescribo el viejo producto por la misma ID con la nueva cantidad
                            ArrayConLoDelInput[ArrayConLasIDsDeMaquinas.indexOf(ProductoSeleccionado.id)] = ProductoSeleccionado.id + 'x' + InputDeMultiplicar.value;
                            MaquinasCotizados.value = ArrayConLoDelInput.join('¿')
    
                            //Borro la vieja row de la cotizacion visible
                            var RowVieja = document.getElementById('MaterialEnLista-' + ProductoSeleccionado.id)
                            RowVieja.remove();
                        }else{
                            //Agrego el nuevo a la lista y coloco los demas si hay
                            MaquinasCotizados.value = ProductoSeleccionado.id + 'x' + InputDeMultiplicar.value + ((MaquinasCotizados.value)? '¿' + MaquinasCotizados.value:'');    
                        }
    
                        HerramientasAgregados.innerHTML = `
                        <row id="MaterialEnLista-${ProductoSeleccionado.id}" class="Flex-gap2">
                            <div class="test9">
                                <i id="BotonModificarProductoEspecifico-${ProductoSeleccionado.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                                <i id="BotonEliminarProductoEspecifico-${ProductoSeleccionado.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">${ProductoSeleccionado.id}</span>
                            <span class="ColumnaNombre CeldaSinH">${ProductoSeleccionado.nombre}</span>
                            
                            <span class="ColumnaCantidad CeldaSinH">x ${InputDeMultiplicar.value}</span>
                            <span class="CeldaSinH ColumnaUnidad">${ProductoSeleccionado.depreciacion}</span>
                            <span class="ColumnaPrecio CeldaSinH">${ProductoSeleccionado.precio}$</span>
                            <span class="ColumnaTotal CeldaSinH">${(ProductoSeleccionado.precio * InputDeMultiplicar.value * ProductoSeleccionado.depreciacion).toFixed(2)}$</span>
                        </row>
                        ` + ((HabianMaquinasCotizadasAdemasDeEsta)?HerramientasAgregados.innerHTML:'');
    
                    }
    
                    //acciones a realizar cuando se agrega el producto
                    MostrarModalProductos(!CierreAutomaticoDelModalProductos);
                    LimpiarPrevisualizacionDelProductoSeleccionado();
                    InputIdProductoAAgregar.value = '';
                    ActualizarArrays()
                    ActualizarTotales();
                    
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'La cantidad debe ser mayor a 0'
                    });
                    InputDeMultiplicar.focus();
                    InputDeMultiplicar.select();
                }
            })

            

            InputDeMultiplicar.focus();
            InputDeMultiplicar.select();
        }else{
            //otra vez, cosas para Mano y Comida
            BotonParaAgregarElProductoSeleccionado.addEventListener('click', () => {
                if(InputDePersonas.value > 0){
                    if(InputDeDias.value > 0){
                        var HabianManosCotizadasAdemasDeEsta = ManosCotizados.value;
    
                        ArrayConLoDelInput = ManosCotizados.value.split('¿');
                        ArrayConLasIDsDeMano = [];
                        ArrayConLoDelInput.forEach( (ManoConPrecio) => {
                            pedazos = ManoConPrecio.split('x');
                            ArrayConLasIDsDeMano.push(pedazos[0]);
                        })
    
                        
                        
                        if(ArrayConLasIDsDeMano.indexOf(ProductoSeleccionado.id) > -1){
                            ArrayConLoDelInput[ArrayConLasIDsDeMano.indexOf(ProductoSeleccionado.id)] = ProductoSeleccionado.id + 'x' + InputDePersonas.value + '.' + InputDeDias.value;
                            ManosCotizados.value = ArrayConLoDelInput.join('¿');
        
                            var RowVieja = document.getElementById('MaterialEnLista-' + ProductoSeleccionado.id)
                            RowVieja.remove();
                        }else{
                            ManosCotizados.value = ProductoSeleccionado.id + 'x' + InputDePersonas.value + '.' + InputDeDias.value + ((ManosCotizados.value)? '¿' + ManosCotizados.value:'');    
                        }

                        ManoDeObraAgregados.innerHTML = `
                        <row id="MaterialEnLista-${ProductoSeleccionado.id}" class="Flex-gap2">
                            <div class="test9">
                                <i id="BotonModificarProductoEspecifico-${ProductoSeleccionado.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                                <i id="BotonEliminarProductoEspecifico-${ProductoSeleccionado.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">${ProductoSeleccionado.id}</span>
                            <span style="width: calc(100% - 475px);" class="ColumnaNombre69 CeldaSinH">${ProductoSeleccionado.nombre}</span>
                            
                            <span class="ColumnaCantidad CeldaSinH" title="${ProductoSeleccionado.nombredeunidad}">${InputDePersonas.value}</span>
                            <span style="align-items: center;" class="ColumnaPrecio CeldaSinH">x ${InputDeDias.value}</span>
                            <span class="ColumnaPrecio CeldaSinH">${ProductoSeleccionado.precio}$</span>                            
                            <span class="ColumnaTotal CeldaSinH">${(ProductoSeleccionado.precio * InputDePersonas.value * InputDeDias.value).toFixed(2)}$</span>
                        </row>
                        ` + ((HabianManosCotizadasAdemasDeEsta)?ManoDeObraAgregados.innerHTML:'');

                        //acciones a realizar cuando se agrega el producto
                        MostrarModalProductos(!CierreAutomaticoDelModalProductos);
                        LimpiarPrevisualizacionDelProductoSeleccionado();
                        InputIdProductoAAgregar.value = '';
                        ActualizarArrays()
                        ActualizarTotales();

                    }else{
                        Toast.fire({
                            icon: 'warning',
                            title: 'La cantidad de días debe ser mayor a 0'
                        });
                    }
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'La cantidad de personas debe ser mayor a 0'
                    });
                }
            })

            InputDePersonas.focus();
            InputDePersonas.select();
        }
        
    }else{
        LimpiarPrevisualizacionDelProductoSeleccionado();
    }
    
}

let PrecioSubTotalMaterial = document.getElementById('PrecioSubTotalMaterial');
let PrecioSubTotalMaquinaria = document.getElementById('PrecioSubTotalMaquinaria');
let PrecioSubTotalMano = document.getElementById('PrecioSubTotalMano');
let PrecioTotal = document.getElementById('PrecioTotal')


function ActualizarTotales(){
    
    var PrecioDeMaterialesAcumulado = 0;
    var PrecioDeMaquinasAcumulado = 0;
    var PrecioDeManoAcumulado = 0;

    if(MaterialesCotizados.value){
        MaterialesCotizados.value.split('¿').forEach( (MaterialConPrecio) => {
            pedazos = MaterialConPrecio.split('x');
            
            Producto = ProdcutosObtenidosPaVerDespues.objetos.filter(function(element){
                return element.id == pedazos[0];
            });
            
            PrecioDeMaterialesAcumulado = ((Producto[0].precio * pedazos[1]) + PrecioDeMaterialesAcumulado);
        })
        PrecioSubTotalMaterial.innerText = PrecioDeMaterialesAcumulado.toFixed(2);
    }else{
        PrecioSubTotalMaterial.innerText = "0.00";
    }


    if(MaquinasCotizados.value){
        
        MaquinasCotizados.value.split('¿').forEach( (MaquinaConPrecio) => {
            pedazos = MaquinaConPrecio.split('x');
            
            Producto = ProdcutosObtenidosPaVerDespues.objetos.filter(function(element){
                return element.id == pedazos[0];
            });
            
            PrecioDeMaquinasAcumulado = ((Producto[0].precio * pedazos[1] * Producto[0].depreciacion) + PrecioDeMaquinasAcumulado);
        })
        PrecioSubTotalMaquinaria.innerText = PrecioDeMaquinasAcumulado.toFixed(2);
    }else{
        PrecioSubTotalMaquinaria.innerText = "0.00";
    }

    var GastosEnComida = 0; 
    if(ManosCotizados.value){
        
        ManosCotizados.value.split('¿').forEach( (ManoConPrecio) => {
            pedazos = ManoConPrecio.split('x');
            CantYDia = pedazos[1].split('.');
            
            Producto = ProdcutosObtenidosPaVerDespues.objetos.filter(function(element){
                return element.id == pedazos[0];
            });
            
            if(Producto[0]['idcategoria'] == 4){
                GastosEnComida = ((Producto[0].precio * CantYDia[0] * CantYDia[1]) + GastosEnComida);
            }

            

            PrecioDeManoAcumulado = ((Producto[0].precio * CantYDia[0] * CantYDia[1]) + PrecioDeManoAcumulado);
        })
        PrecioSubTotalMano.innerText = PrecioDeManoAcumulado.toFixed(2);
    }else{
        PrecioSubTotalMano.innerText = "0.00";
    }

    let PrecioGeneral = document.getElementById('PrecioGeneral');
    let PrecioUtilidades = document.getElementById('PrecioUtilidades');
    let PrecioSubTotal = document.getElementById('PrecioSubTotal');
    let PrecioIVA = document.getElementById('PrecioIVA');
    let PrecioAsociadoAlSalario = document.getElementById('PrecioAsociadoAlSalario');


    PASCalculado = ((PrecioSubTotalMano.innerText - GastosEnComida) * InputCASalario.value / 100);
    PrecioAsociadoAlSalario.innerText = PASCalculado.toFixed(2);
    
    TotalSinCeros = (parseFloat(PrecioSubTotalMano.innerText) + parseFloat(PrecioSubTotalMaquinaria.innerText) + parseFloat(PrecioSubTotalMaterial.innerText) + parseFloat(PrecioAsociadoAlSalario.innerText));
    PrecioGeneral.innerText = TotalSinCeros.toFixed(2);
    PrecioUtilidades.innerText = (PrecioGeneral.innerText * Utilidades.value / 100).toFixed(2);

    PrecioSubTotal.innerText =  (parseFloat(PrecioGeneral.innerText) + parseFloat(PrecioUtilidades.innerText)).toFixed(2);
    PrecioIVA.innerHTML = (PrecioSubTotal.innerText * InputIVA.value / 100).toFixed(2);

    PrecioTotal.innerText = (parseFloat(PrecioSubTotal.innerText) + parseFloat(PrecioIVA.innerText)).toFixed(2);;
}

function ActualizarArrays(){
    ArrayConLasIDs = [];
    ArrayConLosPrecios = [];

    MaterialesCotizados.value.split('¿').forEach( (IDDelProductoConCantidad) => {
        var Pedazos = IDDelProductoConCantidad.split('x');
        
        ArrayConLasIDs.push(Pedazos[0]);
        ArrayConLosPrecios.push(Pedazos[1]);
    })

    MaquinasCotizados.value.split('¿').forEach( (IDDelProductoConCantidad) => {
        var Pedazos = IDDelProductoConCantidad.split('x');
        
        ArrayConLasIDs.push(Pedazos[0]);
        ArrayConLosPrecios.push(Pedazos[1]);
    })

    ManosCotizados.value.split('¿').forEach( (IDDelProductoConCantidad) => {
        var Pedazos = IDDelProductoConCantidad.split('x');
        
        ArrayConLasIDs.push(Pedazos[0]);
        ArrayConLosPrecios.push(Pedazos[1]);
    })
}

function LimpiarPrevisualizacionDelProductoSeleccionado() {
    PrevisualizacionDeProducto.innerHTML = `
        <div class="ProductoNoSeleccionado">
            <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
            <span>Seleccione un producto</span>
        </div>
    `;
}

window.addEventListener('keyup', (e) => {
    if(e.keyCode === 27){
        if(ModalAgregarProducto.style.display == 'flex'){
            MostrarModalProductos(false);
        }
    }
})

window.addEventListener('resize', () => {
    if(ModalAgregarProducto.style.display == 'flex'){
        TamanioMaxDelDiv = Math.round(window.innerHeight * 0.8) - 145;
        console.log('max de l div: ' + TamanioMaxDelDiv);
        console.log('resul: ' + TamanioDeResultados);

        if(TamanioMaxDelDiv < TamanioDeResultados){
            ResultadosDeLaBusqueda.style.height = TamanioMaxDelDiv + 'px';
        }else{
            ResultadosDeLaBusqueda.style.height = TamanioDeResultados + 'px';
        }
    }
})


SelectCategoriaDelProductoABuscar.addEventListener('change', () => {
    BotonBuscarProductos.click();
})

ContenidoDelModal.addEventListener('click', (e) => {
    e.stopPropagation();
})

ModalAgregarProducto.addEventListener('click', () => {
    MostrarModalProductos(false);
})

InputBuscadorDeProductos.addEventListener("keyup", function(event) {
    event.preventDefault();
    if ((event.keyCode === 13) || (InputBuscadorDeProductos.value === "")) {
        BotonBuscarProductos.click();
    }
});

BotonBuscarProductos.addEventListener('click', () => {
    ConsultarProductos(InputBuscadorDeProductos.value);
})

BotonCerrarVentanaProductos.addEventListener('click', () => {
    MostrarModalProductos(false);
})

BotonAbrirModalAgregarProducto.addEventListener('click', () => {
    SelectCategoriaDelProductoABuscar.value= '0';
    InputIdProductoAAgregar.value = "";
    MostrarModalProductos(true);
})

BotonAgregarProductoMaterial.addEventListener('click', () => {
    SelectCategoriaDelProductoABuscar.value= '1';
    InputIdProductoAAgregar.value = "";
    MostrarModalProductos(true);
})

BotonAgregarProductoHerramienta.addEventListener('click', () => {
    SelectCategoriaDelProductoABuscar.value= '2';
    InputIdProductoAAgregar.value = "";
    MostrarModalProductos(true);
})

BotonAgregarProductoMano.addEventListener('click', () => {
    SelectCategoriaDelProductoABuscar.value= '3';
    InputIdProductoAAgregar.value = "";
    MostrarModalProductos(true);
})

async function MostrarModalProductos(valor) {
    if(valor){        
        ModalAgregarProducto.style.display = 'flex';
        for (let i = 0; i < 1; i++) {
            await sleep(i * 100);
        }
        ContenidoDelModal.classList = 'ContenidoDeVentana';
        CambiarPrevisualizacion();
        BotonBuscarProductos.click();
        
    }else{
        ContenidoDelModal.classList = 'ContenidoDeVentana OcultarContenidoModal';
        for (let i = 0; i < 3; i++) {
            await sleep(i * 100);
        }
        ModalAgregarProducto.style.display = 'none';
    }
}







function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


function SoloIntParaPersonas(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8¬37¬38¬46";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    if(InputDePersonas.value.length > 5){
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

function SoloIntParaDias(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8¬37¬38¬46";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    if(InputDeDias.value.length > 5){
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

function SoloNumerosInt(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8¬37¬38¬46";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    if(InputDeMultiplicar.value.length > 5){
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

function SoloNumerosFloat(e){  
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
    if(tecla=="." && InputDeMultiplicar.value.includes(".")){
        return false;
    }
//solo permite dos numeros mas despues del punto
    if(InputDeMultiplicar.value.includes(".")){
        pedazos = InputDeMultiplicar.value.split(".",2);
        posicionDelPunto = InputDeMultiplicar.value.indexOf(".");
        posicionDelTarget = e.target.selectionStart;

        if(pedazos[1].length>3 && posicionDelTarget>posicionDelPunto){
            return false;
        }
    }

    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}


window.addEventListener('load', () => {
    ConsultarProductosPrimeraVez();

    

    
})

let ConsultarProductosPrimeraVez = async() => {
    
    try{
        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?descripcion=&categoria=');

        if(respuesta.status === 200){
            PeticionConFiltros = await respuesta.json();
            ProdcutosObtenidosPaVerDespues = PeticionConFiltros;

            if(MaterialesCotizados.value){
                var vuelta = 1;
                MaterialesCotizados.value.split('¿').forEach((ProductoConPrecio) => {
                    pedazos = ProductoConPrecio.split('x');
            
                    ProductoSeleccionado = PeticionConFiltros.objetos.filter(function(element){
                        return element.id == pedazos[0];
                    });
                    
                    ProductoSeleccionado = ProductoSeleccionado[0];
    
                    
                    MaterialesAgregados.innerHTML = `
                        <row id="MaterialEnLista-${ProductoSeleccionado.id}" class="Flex-gap2">
                            <div class="test9">
                                <i id="BotonModificarProductoEspecifico-${ProductoSeleccionado.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                                <i id="BotonEliminarProductoEspecifico-${ProductoSeleccionado.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">${ProductoSeleccionado.id}</span>
                            <span class="ColumnaNombre CeldaSinH">${ProductoSeleccionado.nombre}</span>
                            
                            <span class="ColumnaCantidad CeldaSinH">x ${pedazos[1]}</span>
                            <span class="ColumnaPrecio CeldaSinH">${ProductoSeleccionado.precio}$</span>
                            <span class="ColumnaTotal CeldaSinH">${(ProductoSeleccionado.precio * pedazos[1]).toFixed(2)}$</span>

                            
                        </row>
                        ` + ((vuelta == 1)?'':MaterialesAgregados.innerHTML);
                        vuelta++;
                })
            }
            
            
            if(MaquinasCotizados.value){
                var vuelta = 1;
                MaquinasCotizados.value.split('¿').forEach((ProductoConPrecio) => {
                    pedazos = ProductoConPrecio.split('x');
            
                    ProductoSeleccionado = PeticionConFiltros.objetos.filter(function(element){
                        return element.id == pedazos[0];
                    });
                    ProductoSeleccionado = ProductoSeleccionado[0];
    
                    HerramientasAgregados.innerHTML = `
                        <row id="MaterialEnLista-${ProductoSeleccionado.id}" class="Flex-gap2">
                            <div class="test9">
                                <i id="BotonModificarProductoEspecifico-${ProductoSeleccionado.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                                <i id="BotonEliminarProductoEspecifico-${ProductoSeleccionado.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">${ProductoSeleccionado.id}</span>
                            <span class="ColumnaNombre CeldaSinH">${ProductoSeleccionado.nombre}</span>
                            
                            <span class="ColumnaCantidad CeldaSinH">x ${pedazos[1]}</span>
                            <span class="ColumnaPrecio CeldaSinH">${ProductoSeleccionado.precio}$</span>
                            <span class="ColumnaTotal CeldaSinH">${(ProductoSeleccionado.precio * pedazos[1]).toFixed(2)}$</span>
                        </row>
                        ` + ((vuelta == 1)?'':HerramientasAgregados.innerHTML);
                        vuelta++;
                })
            }
            
            if(ManosCotizados.value){
                var vuelta = 1;

                ManosCotizados.value.split('¿').forEach((ProductoConPrecio) => {
                    pedazos = ProductoConPrecio.split('x');
            
                    ProductoSeleccionado = PeticionConFiltros.objetos.filter(function(element){
                        return element.id == pedazos[0];
                    });
                    ProductoSeleccionado = ProductoSeleccionado[0];

                    PrecioDividido = pedazos[1].split('.');
    
                    ManoDeObraAgregados.innerHTML = `
                        <row id="MaterialEnLista-${ProductoSeleccionado.id}" class="Flex-gap2">
                            <div class="test9">
                                <i id="BotonModificarProductoEspecifico-${ProductoSeleccionado.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
                                <i id="BotonEliminarProductoEspecifico-${ProductoSeleccionado.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/${((ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">${ProductoSeleccionado.id}</span>
                            <span style="width: calc(100% - 475px);" class="ColumnaNombre69 CeldaSinH">${ProductoSeleccionado.nombre}</span>
                            
                            <span class="ColumnaCantidad CeldaSinH" title="${ProductoSeleccionado.nombredeunidad}">${PrecioDividido[0]} ${ProductoSeleccionado.simbolo}</span>
                            <span class="ColumnaPrecio CeldaSinH" style="align-items: center;">x ${PrecioDividido[1]}</span>
                            <span class="ColumnaPrecio CeldaSinH">${ProductoSeleccionado.precio}$</span>
                            <span class="ColumnaTotal CeldaSinH">${(ProductoSeleccionado.precio * PrecioDividido[0] * PrecioDividido[1]).toFixed(2)}$</span>
                        </row>
                        ` + ((vuelta == 1)?'':ManoDeObraAgregados.innerHTML);
                        vuelta++;
                })
            }
            
        }
    }catch(error){
        console.log(error);
    }

    ActualizarTotales();
}