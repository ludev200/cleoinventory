const Modal_AlmacenarProducto = document.getElementById('Modal_AlmacenarProducto');
const VentadaModal_AlmacenarProducto = document.getElementById('VentadaModal_AlmacenarProducto');
const BotonCerrarVentana_AlmacenarProducto = document.getElementById('BotonCerrarVentana_AlmacenarProducto');
const BotonBuscarAlmacenes = document.getElementById('BotonBuscarAlmacenes');
const DescripcionBuscadorDeAlmacenes = document.getElementById('DescripcionBuscadorDeAlmacenes');
const ListaDeAlmacenesConsultados = document.getElementById('ListaDeAlmacenesConsultados');
const ListaDeExtraccion = document.getElementById('ListaDeExtraccion');
const InputAlmacenSeleccionado = document.getElementById('InputAlmacenSeleccionado')
const PrevisualizacionDeAlmacen = document.getElementById('PrevisualizacionDeAlmacen');
const ButtonGuardar = document.getElementById('ButtonGuardar');
const ContenedorDeRowsDeProductosYaAlmacenados = document.getElementById('ContenedorDeRowsDeProductosYaAlmacenados');

var TodosLosAlmacenes = [];
var AlmacenesDeUltimaConsulta = [];
var CierreAutomaticoDelModalAlmacenaje = true;
var ProductosYaExtraidos = [];

BotonBuscarAlmacenes.addEventListener('click', async function(){
    await ConsultarAPIPorAlmacenes(DescripcionBuscadorDeAlmacenes.value);
    MostrarEnListaAlmacenesDeUltimaBusqueda();
})
Modal_AlmacenarProducto.addEventListener('click', function(){
    MostrarModal_AlmacenarProducto(false);
})
BotonCerrarVentana_AlmacenarProducto.addEventListener('click', function(){
    MostrarModal_AlmacenarProducto(false);
})
VentadaModal_AlmacenarProducto.addEventListener('click', function(e){
    e.stopPropagation();
})


async function MostrarModal_AlmacenarProducto(valor){
    if(valor){
        Modal_AlmacenarProducto.style = 'display: flex';
        MostrarEnListaAlmacenesDeUltimaBusqueda();
        PrevisualizarProductoAExtraer();
        await sleep(100);
        VentadaModal_AlmacenarProducto.className = 'VentanaFlotante';
    }else{
        VentadaModal_AlmacenarProducto.className = 'VentanaFlotante OcultarModal';
        await sleep(500);
        Modal_AlmacenarProducto.style = '';
    }
}



async function MostrarEnListaAlmacenesDeUltimaBusqueda(){
    ListaDeAlmacenesConsultados.innerHTML = '';

    if(AlmacenesDeUltimaConsulta.length){
        AlmacenesDeUltimaConsulta.forEach( element => {
            lol = element.productos.find( (element) => {
                return element.idProducto == InputProductoAlmacenar.value;
            })
    
            if(lol == undefined){
                Existencia = '0';
                nombreUM = 'Unidades';
            }else{
                Existencia = lol.existencia;
                nombreUM = lol.nombreUM;
            }
            
    
            ListaDeAlmacenesConsultados.innerHTML = ListaDeAlmacenesConsultados.innerHTML + `
            <row id="RowAlmacenResultado-${element.id}" class="RowAlmacenResultado">
                <celda class="ColumnaID">${element.id}</celda>
                <celda class="ColumnaDescripcion">${element.nombre}</celda>
                <celda title="${Existencia} ${nombreUM} existentes en este almacén." class="ColumnaCantidad">x ${Existencia}</celda>
            </row>
            `;
        })
    }else{
        ListaDeAlmacenesConsultados.innerHTML = `
        <div class="estebetavacio">
            <span>No hay almacenes a mostrar</span>
        <div>
        `;
    }

    

    document.querySelectorAll('.RowAlmacenResultado').forEach( element => {
        element.addEventListener('click', function(){
            document.querySelectorAll('.RowAlmacenResultado').forEach( el => {
                el.classList.remove('ProductoSeleccionado');
            })
            element.classList.add('ProductoSeleccionado');

            ped = element.id.split('-');
            PrevisualizarProductoAExtraer(ped[1]);
        })

        
    })
}



function PrevisualizarProductoAExtraer(idAlmacen){
    if(idAlmacen>0){
        InputAlmacenSeleccionado.value = idAlmacen;
        ProductoAExtraer = TodosLosProductos[InputProductoAlmacenar.value];
        
        AlmacenAExtraer = TodosLosAlmacenes.find( (element) => {
            return element.id == InputAlmacenSeleccionado.value;
        })

        
        CantidadEnEsteAlmacen = '';
        if(ListaDeExtraccion.value){
            Array_AlmProCant = ListaDeExtraccion.value.split('¿');
            
            String_AlmProCant = Array_AlmProCant.find( (element) => {
                xd = element.split(':');
                return xd[0] == AlmacenAExtraer.id;
            })

            if(String_AlmProCant != undefined){
                
                mmm = String_AlmProCant.split(':');
                String_Productos = mmm[1];
                
                xax = String_Productos.split(',');
                ProdXCant = String_Productos.split(',').find( (element) => {
                    wasd = element.split('x');
                    
                    return wasd[0] == ProductoAExtraer.id;
                })


                if(ProdXCant != undefined){
                    ped = ProdXCant.split('x');
                    
                    CantidadEnEsteAlmacen = ped[1];
                }
            }
        }

        PrevisualizacionDeAlmacen.innerHTML = `
        <div class="AlmacenSiSeleccionado">
            <div class="Weas">
                <span class="TituloDeAlmacen">${AlmacenAExtraer.nombre}</span>
                <span class="DireccionDeAlmacen">${AlmacenAExtraer.direccion}</span>
            </div>
            <div class="TituloDeProductoAAlmacenar">
                <span class="titulillo fi-sr-package"> ${ProductoAExtraer.id} ${ProductoAExtraer.nombre}</span>
                <div class="asdf">
                    <div>
                        <span>${ProductosYaExtraidos[ProductoAExtraer.id]}</span>
                        /
                        <span>${ProductosDelPaso2[ProductoAExtraer.id]}</span>
                    </div>
                    <div>
                        ${ProductosYaExtraidos[ProductoAExtraer.id]} Unidades ya extraídas de ${ProductosDelPaso2[ProductoAExtraer.id]} vendidas
                    </div>
                </div>
            </div>
            
            <div class="Weas" style="padding: 20px 0 40px 0;">
                <div class="DivDeAlmacenaje">
                    <img src="../../Imagenes/iconoDelMenu_Almacenes.png" alt="">
                    <span class="fi-rr-caret-right"></span>
                    <img class="ImagenDelProductoAAlmacenar" src="../../Imagenes/Productos/${(ProductoAExtraer.ULRImagen? ProductoAExtraer.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                </div>
                <input value="" onkeypress="return ${(ProductoAExtraer.idcategoria==2? 'SoloInt':'SoloInt')}ParaExtraccion(event);" autocomplete="off" placeholder="Cantidad a extraer" class="InputDeCantidadAAlmacenar" id="CantidadDelProductoAAlmacenar" type="text">
            </div>
            <div class="Weas">
                <div class="FH">
                    ${(CantidadEnEsteAlmacen? '<span title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>':'')}
                    <button id="BotonAlmacenarProductoSeleccionado" class="BotonAlmacenarProductoSeleccionado">${(CantidadEnEsteAlmacen? 'Actualizar':'Extraer')}</button>
                    <i id="AvisitoDeQueTePasas" class="IconoConInfo fi-sr-comment-exclamation"><div class="TextoDeIconoConInfo2" id="TextoDeQueTePasas">Extraer 8 unidades de las 6 existentes en este almacén provocará como resultado una existencia de -3 unidades. Las existencias negativas no son recomendadas!</div></i>
                </div>
                <label style="align-self: center;" title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
                    Cierre automático
                    <input type="checkbox" ${(CierreAutomaticoDelModalAlmacenaje? 'checked':'')} name="" id="CierreAutomaticoModalExtraer">
                    <div class="sliderrr round"></div>
                </label>
            </div>
        </div>
        `;

        var BotonAlmacenarProductoSeleccionado = document.getElementById('BotonAlmacenarProductoSeleccionado');
        var CantidadDelProductoAAlmacenar = document.getElementById('CantidadDelProductoAAlmacenar');
        var CierreAutomaticoModalExtraer = document.getElementById('CierreAutomaticoModalExtraer');
        var AvisitoDeQueTePasas = document.getElementById('AvisitoDeQueTePasas');
        

        if(CantidadEnEsteAlmacen){
            var BotonRemoverProductoSeleccionado = document.getElementById('BotonRemoverProductoSeleccionado');

            BotonRemoverProductoSeleccionado.addEventListener('click', function(){
                EliminarProductoExtraido(idAlmacen, ProductoAExtraer.id);
            })
        }

        
        CantidadDelProductoAAlmacenar.value = CantidadEnEsteAlmacen;
        CantidadDelProductoAAlmacenar.focus();


        BotonAlmacenarProductoSeleccionado.addEventListener('click', function(){
            if(BotonAlmacenarProductoSeleccionado.className == 'BotonAlmacenarProductoSeleccionado'){
                AgregaProductoExtraido(InputAlmacenSeleccionado.value, InputProductoAlmacenar.value, CantidadDelProductoAAlmacenar.value);
            }
            
        })
        
        CierreAutomaticoModalExtraer.addEventListener('change', () => {
            CierreAutomaticoDelModalAlmacenaje = CierreAutomaticoModalExtraer.checked;
        })

        CantidadDelProductoAAlmacenar.addEventListener('keyup', function(e){
            if(CantidadDelProductoAAlmacenar.value){
                if((Number(ProductosYaExtraidos[InputProductoAlmacenar.value]) + Number(CantidadDelProductoAAlmacenar.value))>(Number(ProductosDelPaso2[InputProductoAlmacenar.value]) + Number(CantidadEnEsteAlmacen))){
                    BotonAlmacenarProductoSeleccionado.className ="BotonPrincipalDeModalDesactivado";
                    BotonAlmacenarProductoSeleccionado.setAttribute('title', 'La cantidad a extraer debe ser mayor que 0 y menor a la cantidad vendida ('+ProductosDelPaso2[InputProductoAlmacenar.value]+')');
                }else{
                    BotonAlmacenarProductoSeleccionado.className ="BotonAlmacenarProductoSeleccionado";
                    BotonAlmacenarProductoSeleccionado.setAttribute('title', '');
                }


                AlmacenAExtraer = AlmacenesDeUltimaConsulta.find( (element) => {
                    return element.id == InputAlmacenSeleccionado.value;
                })
                ExistenciaDeProductoAExtraerObj = AlmacenAExtraer.productos.find( (element) => {
                    return element.idProducto == InputProductoAlmacenar.value;
                })
                
                if(ExistenciaDeProductoAExtraerObj == undefined){
                    ExistenciaDeProductoAExtraer = 0;
                }else{
                    ExistenciaDeProductoAExtraer = Number(ExistenciaDeProductoAExtraerObj.existencia);
                }

                
                if(CantidadDelProductoAAlmacenar.value>ExistenciaDeProductoAExtraer){
                    AvisitoDeQueTePasas.style = 'display: block';
                    document.getElementById('TextoDeQueTePasas').innerText = `Extraer ${CantidadDelProductoAAlmacenar.value} unidades de las ${ExistenciaDeProductoAExtraer} existentes en este almacén provocará como resultado una existencia de ${(ExistenciaDeProductoAExtraer - Number(CantidadDelProductoAAlmacenar.value))} unidades. Las existencias negativas no son recomendadas!`;
                }else{
                    AvisitoDeQueTePasas.style = '';
                }

                if(e.keyCode == 13){
                    BotonAlmacenarProductoSeleccionado.click();
                }

            }else{
                BotonAlmacenarProductoSeleccionado.className ="BotonPrincipalDeModalDesactivado";
            }
        })
        
        
    }else{
        InputAlmacenSeleccionado.value = '';

        PrevisualizacionDeAlmacen.innerHTML = `
        <div class="ProductoNoSeleccionado">
            <img src="../../Imagenes/Sistema/ImagenPredefinida_Almacen.png" alt="">
            <span>Seleccione un almacén</span>
        </div>
        `;
    }
    
}

function EliminarProductoExtraido(idAlmacen, idProducto){
    
    document.getElementById('RowDeProductoAlmacenado-'+idAlmacen+'_'+idProducto).remove();
    Almacenes_pros = ListaDeExtraccion.value.split('¿');

    PedazoDeAlmAEditar = Almacenes_pros.find( (element) => {
        pieces = element.split(':');
        return pieces[0] == idAlmacen;
    })

    
    ped = PedazoDeAlmAEditar.split(':');
    idAlmacen_pedazo = ped[0];
    productos_pedazo = ped[1];
    var ProductosDelAlmacenSeleccionado = productos_pedazo.split(',');

    ProdXCant_Buscado = ProductosDelAlmacenSeleccionado.find( (element) => {
        pieces = element.split('x');
        return pieces[0] == idProducto;
    })
    
    

    
    if(ProdXCant_Buscado != undefined){
        if(ProductosDelAlmacenSeleccionado.indexOf(ProdXCant_Buscado)>= 0){
            
            ProductosDelAlmacenSeleccionado.splice(ProductosDelAlmacenSeleccionado.indexOf(ProdXCant_Buscado), 1)
            NuevoPedazoDeProductos = ProductosDelAlmacenSeleccionado.join(',');
            NuevoPedazoDeAlmAEditar = (NuevoPedazoDeProductos? idAlmacen_pedazo+':'+NuevoPedazoDeProductos:'');
            Almacenes_pros[Almacenes_pros.indexOf(PedazoDeAlmAEditar)] = NuevoPedazoDeAlmAEditar;
            Almacenes_pros_Limpiecito = Almacenes_pros.filter( (element) => {
                return element;
            })

            ListaDeExtraccion.value = Almacenes_pros_Limpiecito.join('¿');


            xd = ProdXCant_Buscado.split('x');
            Cantidad = xd[1];
            ProductosYaExtraidos[idProducto] = (Number(ProductosYaExtraidos[idProducto]) - Number(Cantidad));
            
            CalcularSiVentaComplacida();
            
            if(!VentadaModal_AlmacenarProducto.classList.contains('OcultarModal')){
                if(CierreAutomaticoDelModalAlmacenaje){
                    MostrarModal_AlmacenarProducto(false)
                }else{
                    PrevisualizarProductoAExtraer(0);
                }
            }
            
        }
        
    }
}

function AgregaProductoExtraido(idAlmacen, idProducto, cantidad){
    if(cantidad){
        
        if(ListaDeExtraccion.value){
            var index_almacenAModificar = -1;
            Array_alm = ListaDeExtraccion.value.split('¿');

            PedConAlm = Array_alm.find( (element) => {
                pieces = element.split(':');
                return pieces[0] == idAlmacen;
            })
            
            if(PedConAlm == undefined){
                ListaDeExtraccion.value = ListaDeExtraccion.value+'¿'+idAlmacen+':'+idProducto+'x'+cantidad;
            }else{
                index_almacenAModificar = Array_alm.indexOf(PedConAlm);
                Alm_Prod = PedConAlm.split(':');
                Array_prod = Alm_Prod[1].split(',');
                
                PedConProd = Array_prod.find( (element) => {
                    pieces = element.split('x');
                    return pieces[0] == idProducto;
                })
                
                if(PedConProd == undefined){
                    Array_prod.push(idProducto+'x'+cantidad);
                }else{
                    Array_prod[Array_prod.indexOf(PedConProd)] = idProducto+'x'+cantidad;
                    
                    palaresta = PedConProd.split('x');
                    ProductosYaExtraidos[idProducto] = Number(ProductosYaExtraidos[idProducto])-Number(palaresta[1]);
                    document.getElementById('RowDeProductoAlmacenado-'+idAlmacen+'_'+idProducto).remove();
                }

                Array_alm[index_almacenAModificar] = idAlmacen+':'+Array_prod.join(',');
                ListaDeExtraccion.value = Array_alm.join('¿');
            }

        }else{
            ListaDeExtraccion.value = idAlmacen+':'+idProducto+'x'+cantidad;
            ContenedorDeRowsDeProductosYaAlmacenados.innerHTML = '';
        }

        ProductosYaExtraidos[idProducto] = Number(ProductosYaExtraidos[idProducto])+Number(cantidad);
        CalcularSiVentaComplacida();
        
        if(CierreAutomaticoDelModalAlmacenaje){
            MostrarModal_AlmacenarProducto(false)
        }else{
            PrevisualizarProductoAExtraer(0);
        }

        ProductoObj = TodosLosProductos[idProducto];
        AlmacenObj = TodosLosAlmacenes.find( (element) => {
            return element.id == idAlmacen;
        })

        

        ExistenciaResultado = 0;
        if(AlmacenObj.productos){
            Exis = AlmacenObj.productos.find( (element) => {
                return element.idProducto = idProducto;
            })
            ExistenciaResultado = Exis.existencia;
        }

        ExistenciaResultado = ExistenciaResultado - cantidad;

        ContenedorDeRowsDeProductosYaAlmacenados.innerHTML = `
        <row id="RowDeProductoAlmacenado-${idAlmacen}_${idProducto}">
            <celda class="ColumnaImagenAP">
                <img src="../../Imagenes/Productos/${(ProductoObj.ULRImagen? ProductoObj.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
            </celda>
            <celda class="ColumnaNombreAP">${ProductoObj.nombre}</celda>
            <celda class="ColumnaNombreAP">${AlmacenObj.nombre}</celda>
            <celda class="ColumnaCantidadAP" title="${cantidad} ${ProductoObj.nombredeunidad} a extraer de este almacén"> -${cantidad}</celda>
            <celda class="ColumnaResultadoAP" title="${ExistenciaResultado} ${ProductoObj.nombredeunidad} resultantes en este almacén"> ${ExistenciaResultado}</celda>
            <div class="CeldaOculta">
                <i id="BotonModificarProductoAlmacenadoEspecifico-${idAlmacen}_${idProducto}" title="Modificar este producto." class="fi-rr-pencil"></i>
                <i id="BotonEliminarProductoAlmacenadoEspecifico-${idAlmacen}_${idProducto}" title="Eliminar este producto." class="fi-rr-trash"></i>
            </div>
        </row>
        `+ContenedorDeRowsDeProductosYaAlmacenados.innerHTML;




    }else{
        Toast.fire({
            icon: 'warning',
            title: 'La cantidad a extraer debe ser mayor a 0'
        });
    }
}

ContenedorDeRowsDeProductosYaAlmacenados.addEventListener('click', function(evento){
    if(evento.target.tagName.toLowerCase() == 'i'){
        if(evento.target.id.includes('-')){
            pedazos = evento.target.id.split('-');

            data = pedazos[1].split('_');
            if(pedazos[0]=='BotonModificarProductoAlmacenadoEspecifico'){
                InputProductoAlmacenar.value = data[1];
                MostrarModal_AlmacenarProducto(true);
                PrevisualizarProductoAExtraer(data[0]);
            }else if(pedazos[0]=='BotonEliminarProductoAlmacenadoEspecifico'){
                EliminarProductoExtraido(data[0], data[1]);
            }
        }
    }
})

function CalcularSiVentaComplacida(){
    BichosListos = 0;
    BichosAContar = 0;


    ProductosAVender.value.split('¿').forEach( element => {
        Valores = element.split('x');
        Producto = TodosLosProductos[Valores[0]];

        if(Producto.idcategoria<3){
            BichosAContar++;
            document.getElementById('CantidadAlmacenadaActualmente-'+Producto.id).innerText = ProductosYaExtraidos[Producto.id];
            document.getElementById('CeldaDeCantidadAlma/Comp-'+Producto.id).setAttribute('title', 'Extraídos '+ProductosYaExtraidos[Producto.id]+' Unidades de '+ProductosDelPaso2[Producto.id]+' vendidos')

            var BotonPaAbrirModalExtraer = document.getElementById('BotonAlmacenar-'+Producto.id);
            if(ProductosYaExtraidos[Producto.id] < ProductosDelPaso2[Producto.id]){
                BotonPaAbrirModalExtraer.className = 'BotonAlmacenarProductoDisponible';
                BotonPaAbrirModalExtraer.setAttribute('title', 'Extraer este producto');
            }else{
                BotonPaAbrirModalExtraer.className = 'BotonAlmacenarProductoNoDisponible';
                BotonPaAbrirModalExtraer.setAttribute('title', '');
                BichosListos++;
            }
        }
    })

    if(BichosListos == BichosAContar){
        ButtonGuardar.className = 'BotonContinuarDisponible';
        ButtonGuardar.setAttribute('title', '')
    }else{
        ButtonGuardar.className = 'BotonContinuarNoDisponible';
        ButtonGuardar.setAttribute('title', 'Aún hay productos vendidos que no han sido extraídos del inventario.')
    }
}

async function ConsultarAPIPorAlmacenes(descripcion){
    try{
        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Almacenes.php?descripcion='+descripcion+'&idEstado=51');

        if(respuesta.status === 200){
            ObjetosRecibidos = await respuesta.json();
            

            if(ObjetosRecibidos.objetos == undefined){
                AlmacenesRecibidos = [];
            }else{
                AlmacenesRecibidos = ObjetosRecibidos.objetos;
            }

            if(TodosLosAlmacenes.length == 0){
                TodosLosAlmacenes = AlmacenesRecibidos;
            }
            
            AlmacenesDeUltimaConsulta = AlmacenesRecibidos;
        }else{
            alert('Error al consultar la API de compras. Status: ' + respuesta.status);
        }
    }catch(error){
        console.log(error)
    }
}



function SoloIntParaExtraccion(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8373846";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    if(CantidadDelProductoAAlmacenar.value.length > 5){
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



function SoloFloatParaExtraccion(e){  
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
    if(tecla=="." && CantidadDelProductoAAlmacenar.value.includes(".")){
        return false;
    }
//solo permite dos numeros mas despues del punto
    if(CantidadDelProductoAAlmacenar.value.includes(".")){
        pedazos = CantidadDelProductoAAlmacenar.value.split(".",2);
        posicionDelPunto = CantidadDelProductoAAlmacenar.value.indexOf(".");
        posicionDelTarget = e.target.selectionStart;

        if(pedazos[1].length>3 && posicionDelTarget>posicionDelPunto){
            return false;
        }
    }

    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}



const Modal_ElegirAlmPred = document.getElementById('Modal_ElegirAlmPred');
const VentadaModal_ElegirPredeterminado = document.getElementById('VentadaModal_ElegirPredeterminado');
const BotonCerrarVentana_ElegirPredeterminado = document.getElementById('BotonCerrarVentana_ElegirPredeterminado');


async function MostrarModal_ElegirAlmPred(valor){
    if(valor){
        Modal_ElegirAlmPred.style = 'display: flex';        
        await sleep(100);
        VentadaModal_ElegirPredeterminado.className = 'VentanaFlotante';
    }else{
        VentadaModal_ElegirPredeterminado.className = 'VentanaFlotante OcultarModal';
        await sleep(500);
        Modal_ElegirAlmPred.style = '';
    }
}

document.getElementById('BotonTexto_ElegirPredeterminado').addEventListener('click', function(){
    MostrarModal_ElegirAlmPred(true);
})

Modal_ElegirAlmPred.addEventListener('click', function(){
    MostrarModal_ElegirAlmPred(false);
})
BotonCerrarVentana_ElegirPredeterminado.addEventListener('click', function(){
    MostrarModal_ElegirAlmPred(false);
})
VentadaModal_ElegirPredeterminado.addEventListener('click', function(e){
    e.stopPropagation();
})

const Boton_UsarAlmacenPredeterminado = document.querySelector('.Boton_UsarAlmacenPredeterminado');

Boton_UsarAlmacenPredeterminado.addEventListener('click', function(){
    lol = Boton_UsarAlmacenPredeterminado.id.split('=');
    
    ContenedorDeRowsDeProductosYaAlmacenados.innerHTML = '';
    ProductosAVender.value.split('¿').forEach ( element => {
        pieces = element.split('x');
        Producto = TodosLosProductos[pieces[0]];
        

        if(Producto.idcategoria<3){
            ProductosYaExtraidos[pieces[0]] = '0';
            AgregaProductoExtraido(lol[1], pieces[0], pieces[1]);
        }
    })

    MostrarModal_ElegirAlmPred(false);
    CalcularSiVentaComplacida();
})