let Modal_AgregarProducto = document.getElementById('Modal_AgregarProducto');
let VentanaModal_AgregarProducto = document.getElementById('VentanaModal_AgregarProducto');
let BotonCerrarVentana_AgregarProducto = document.getElementById('BotonCerrarVentana_AgregarProducto');
let BotonDelAsideAgregarProducto = document.getElementById('BotonDelAsideAgregarProducto');
let ListaDeProductosConsultados = document.getElementById('ListaDeProductosConsultados');
let BotonBuscarProductos = document.getElementById('BotonBuscarProductos');
let BuscadorDeProductos = document.getElementById('BuscadorDeProductos');
let SelectCategoriaABuscar = document.getElementById('SelectCategoriaABuscar');
let InputProductoAPrevisualizar = document.getElementById('InputProductoAPrevisualizar');
let PrevisualizacionDeProducto = document.getElementById('PrevisualizacionDeProducto');

let CerrarModalAuto = true;

BotonBuscarProductos.addEventListener('click', async function(){
    await ConsultarAPIPorProductos(BuscadorDeProductos.value, SelectCategoriaABuscar.value);
    EnlistarProductosDeUltimaBuqueda();
})
BuscadorDeProductos.addEventListener('keyup', function(e){  
    if(e.keyCode == 13){
        BotonBuscarProductos.click();
    }else{
        if(!BuscadorDeProductos.value){
            BotonBuscarProductos.click();
        }
    }
})


BotonDelAsideAgregarProducto.addEventListener('click', function() {
    MostrarModal_AgregarProducto(true);
})
BotonCerrarVentana_AgregarProducto.addEventListener('click', function(){
    MostrarModal_AgregarProducto(false);
})
VentanaModal_AgregarProducto.addEventListener('click', (e) => {
    e.stopPropagation();
})
Modal_AgregarProducto.addEventListener('click', function(){
    MostrarModal_AgregarProducto(false);
})

async function ConsultarAPIPorProductos(descripcion, idCat){
    document.getElementById('ListaDeProductosConsultados').innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    try{
        let consulta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?descripcion='+descripcion+'&categoria='+idCat);

        if(consulta.status === 200){
            ObjetosRecibidos = await consulta.json();

            if(ObjetosRecibidos.objetos == undefined){
                ProductosRecibidos = [];
            }else{
                ProductosRecibidos = ObjetosRecibidos.objetos;
            }

            if(!TodosLosProductos.length){
                ProductosRecibidos.forEach( element => {
                    TodosLosProductos[element.id] = element;
                })
            }
            ProductosDeUltimaConsulta = ProductosRecibidos;
        }
        
    }catch(error){
        console.log(error);
    }
}






async function EnlistarProductosDeUltimaBuqueda(){
    ListaDeProductosConsultados.innerHTML = '';
    
    if(ProductosDeUltimaConsulta.length){
        ProductosDeUltimaConsulta.forEach( element => {
            if(CantidadDeProductoEnCoti[element.id]){
                if(element.idcategoria == 3 || element.idcategoria == 4){
                    tro = CantidadDeProductoEnCoti[element.id].split('.');
                    CantidadEnLista = tro[0] + ' x '+ tro[1];
                }else{
                    CantidadEnLista = 'x '+CantidadDeProductoEnCoti[element.id];
                }
            }else{
                CantidadEnLista = 'x 0';
            }
    
            ListaDeProductosConsultados.innerHTML = ListaDeProductosConsultados.innerHTML + `
            <row class="RowProductoConsultado" id="RowProductoConsultado-${element.id}">
                <celda class="ColumnaImagen">
                    <img src="../../Imagenes/Productos/${(element.ULRImagen?element.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                </celda>
                <celda class="ColumnaID">${element.id}</celda>
                <celda class="ColumnaNombre2">${element.nombre}</celda>
                <celda class="ColumnaCantidad">${CantidadEnLista}</celda>
            </row>
            `;
        })
    }else{
        ListaDeProductosConsultados.innerHTML = `
        <div class="estebetavacio">
            <span>No hay productos a mostrar</span>
        <div>
        `;
    }

    
    
    document.querySelectorAll('.RowProductoConsultado').forEach( element => {
        element.addEventListener('click', function(){
            document.querySelectorAll('.ProductoSeleccionado').forEach( el => {
                el.classList.remove('ProductoSeleccionado');
            })

            element.className = 'RowProductoConsultado ProductoSeleccionado';
            
            ped = element.id.split('-');
            InputProductoAPrevisualizar.value = ped[1];
            PrevisualizarProductoSeleccionado(InputProductoAPrevisualizar.value);


            
        })
    })
}

function EliminarProductoDeLaLista(idProducto){
    ProdXCantXPrecio = ProductosAVender.value.split('¿');

    delete CantidadDeProductoEnCoti[idProducto];
    console.log(CantidadDeProductoEnCoti);

    EstadoAcualDelProd = ProdXCantXPrecio.find( ele => {
        ped = ele.split('x');
        return idProducto == ped[0];
    })

    ProdXCantXPrecio.splice(ProdXCantXPrecio.indexOf(EstadoAcualDelProd), 1);
    ProductosAVender.value = ProdXCantXPrecio.join('¿');

    document.getElementById('RowDeProducto-'+idProducto).remove();
    ActualizarTotalSegunTotalesEnLista();
    CheckSiPaso2Disponible();
    
    
}



function PrevisualizarProductoSeleccionado(id){
    Producto = TodosLosProductos[id];

    if(Producto.idcategoria == 3 || Producto.idcategoria == 4){
        ElementosPaElegirCantidad = `
        <div class="CantidadYUnidad TextToNumbre">
            <span class="fi-rr-cross-small"></span>
            <input onclick="this.select();" onkeypress="return SoloIntParaPersonas(event)" onpaste="return false" autocomplete="off" maxlength="6" type="number" id="InputDePersonas">
            <span title="">Personas</span>
        </div>
        <div class="CantidadYUnidad TextToNumbre">
            <span class="fi-rr-cross-small"></span>
            <input onclick="this.select();" onkeypress="return SoloIntParaDias(event)" onpaste="return false" autocomplete="off" maxlength="6" type="number" id="InputDeDias">
            <span title="">Días</span>
        </div>
        `;
    }else{
        if(Producto.idcategoria == 1){
            ElementosPaElegirCantidad = `
            <span>Cantidad:</span>
            <div class="CantidadYUnidad TextToNumbre">
                <span class="fi-rr-cross-small"></span>
                <input onkeypress="return ${(Producto.idcategoria==1?'SoloInt(event)':'SoloInt(event)')}" onclick="this.select();" onpaste="return false" autocomplete="off" maxlength="9" id="InputCantidadPS">
                <span title="${Producto.nombredeunidad}">${Producto.simbolo}</span>
            </div>
            `;
        }else{
            ElementosPaElegirCantidad = `
            <span>Cantidad:</span>
            <div>
                <div class="CantidadYUnidad TextToNumbre">
                    <input style="width: 80px;" onkeypress="return ${(Producto.idcategoria==1?'SoloInt(event)':'SoloInt(event)')}" onclick="this.select();" onpaste="return false" autocomplete="off" maxlength="9" id="InputCantidadPS">
                </div>
                <div style="text-align: center; font-size: 13px; color: gray;" title="Depreciación del producto"><span>x </span><span id="spoilage">${Producto.depreciacion}</span></div>
            </div>
            `;
        }
        
    }

    PrevisualizacionDeProducto.innerHTML = `
    <div class="ProductoSiSeleccionado">
        <img src="../../Imagenes/Productos/${Producto.ULRImagen?Producto.ULRImagen:'ImagenPredefinida_Productos.png'}" alt="">
        <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${Producto.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
        <b class="NombreDelProductoSiSeleccionado">${Producto.nombre}</b>
        <b class="PrecioDelProductoSiSeleccionado">${Producto.precio}$</b>
        <div class="ElementosPaElegirCantidad">
            ${ElementosPaElegirCantidad}
        </div>
        <div id="C<ajaDeBotonBorrarYAgregar">
        ${(CantidadDeProductoEnCoti[id]?'<span title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>':'')}
            <button id="BotonParaAgregarElProductoSeleccionado" title="Agregar este producto a la cotización" class="BotonParaAgregarElProductoSeleccionado">Agregar</button>
        </div>
        <label title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
            Cierre automático
            <input ${(CerrarModalAuto?'checked':'')} type="checkbox"  name="" id="CierreAutomaticoModalProductos">
            <div class="slider round"></div>
        </label>
    </div>
    `;

    var CierreAutomaticoModalProductos = document.getElementById('CierreAutomaticoModalProductos');
    var BotonParaAgregarElProductoSeleccionado = document.getElementById('BotonParaAgregarElProductoSeleccionado');
    
    CierreAutomaticoModalProductos.addEventListener('change', function(){
        console.log('estaba: '+CerrarModalAuto)
        CerrarModalAuto = CierreAutomaticoModalProductos.checked;
        console.log('y ahora: '+CerrarModalAuto)
    })

    if(CantidadDeProductoEnCoti[id]){
        document.getElementById('BotonRemoverProductoSeleccionado').addEventListener('click', function(){
            EliminarProductoDeLaLista(id);
            document.getElementById('PrevisualizacionDeProducto').innerHTML = `
            <div class="ProductoNoSeleccionado">
                <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                <span>Seleccione un producto</span>
            </div>
            `;
            MostrarModal_AgregarProducto(!CerrarModalAuto);
        });
    }

    if(Producto.idcategoria == 3 || Producto.idcategoria == 4){
        var InputDePersonas = document.getElementById('InputDePersonas');
        var InputDeDias = document.getElementById('InputDeDias');
        
        InputDePersonas.focus();

        if(CantidadDeProductoEnCoti[id]){
            troz = CantidadDeProductoEnCoti[id].split('.');
            InputDePersonas.value = troz[0];
            InputDeDias.value = troz[1];
        }

        InputDeDias.addEventListener('keyup', function(e){
            if(e.keyCode == 13){
                BotonParaAgregarElProductoSeleccionado.click();
            }
        })

        BotonParaAgregarElProductoSeleccionado.addEventListener('click', function(){
            if(InputDePersonas.value && InputDeDias.value){
                AgregarProductoAlCuerpoDeCoti(InputProductoAPrevisualizar.value, InputDePersonas.value+'.'+InputDeDias.value);
                document.getElementById('PrevisualizacionDeProducto').innerHTML = `
                <div class="ProductoNoSeleccionado">
                    <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                    <span>Seleccione un producto</span>
                </div>
                `;
            }else{
                Toast.fire({
                    icon: 'warning',
                    title: 'La cantidad días y personas debe ser mayor a 0'
                });
            }
        })
    }else{
        var InputCantidadPS = document.getElementById('InputCantidadPS');

        InputCantidadPS.focus();

        if(CantidadDeProductoEnCoti[id]){
            InputCantidadPS.value = CantidadDeProductoEnCoti[id];
        }

        InputCantidadPS.addEventListener('keyup', function(e){
            if(e.keyCode == 13){
                BotonParaAgregarElProductoSeleccionado.click();
            }
        })
        BotonParaAgregarElProductoSeleccionado.addEventListener('click', function(){
            if(InputCantidadPS.value){
                AgregarProductoAlCuerpoDeCoti(InputProductoAPrevisualizar.value, InputCantidadPS.value);
                document.getElementById('PrevisualizacionDeProducto').innerHTML = `
                <div class="ProductoNoSeleccionado">
                    <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                    <span>Seleccione un producto</span>
                </div>
                `;
            }else{
                Toast.fire({
                    icon: 'warning',
                    title: 'La cantidad debe ser mayor a 0'
                });
            }
            
        })
    }
}


function CheckSiPaso2Disponible(){
    if(ProductosAVender.value &&
        ID_CotiAConfirmar.value &&
        InputTituloDeCoti.value){
            ButtonIrAPaso2.className = 'BotonContinuarDisponible';
    }else{
        ButtonIrAPaso2.className = 'BotonContinuarNoDisponible';
    }
    
}

function AgregarProductoAlCuerpoDeCoti(idProducto, cantidad){
    var Producto = TodosLosProductos[idProducto];
    CantidadDeProductoEnCoti[idProducto] = cantidad;
    
    if(Indexed_CuerpoDeCotiOriginal[idProducto]){
        ProductoNuevoALaCoti = false;
        Precio = Indexed_CuerpoDeCotiOriginal[idProducto].precioUnitario;

    }else{
        ProductoNuevoALaCoti = true;
        Precio = Producto.precio;
    }


    ProdXCantXPrecio = ProductosAVender.value.split('¿');

    ProductoRepetido = '';
    ProductoRepetido = ProdXCantXPrecio.find( ele => {
        ped = ele.split('x');
        return idProducto == ped[0];
    })

    if(ProductoRepetido){
        ProdXCantXPrecio.splice(ProdXCantXPrecio.indexOf(ProductoRepetido), 1);

        document.getElementById('RowDeProducto-'+Producto.id).remove();
    }

    ProdXCantXPrecio.unshift(idProducto+'x'+cantidad+'x'+Precio);
    ProductosAVender.value = ProdXCantXPrecio.join('¿');
    
    MostrarModal_AgregarProducto(!CerrarModalAuto);


    CantidadTitle = cantidad+' '+Producto.nombredeunidad;
    PrecioMultiplicado = cantidad*Precio;
    switch(Producto.idcategoria){
        case 1:
            CantidadProducto = 'x ' + cantidad;
        break;

        case 2:
            CantidadProducto = 'x ' + Number(cantidad);
        break;

        default:
            ped = cantidad.toString().split('.');
            CantidadProducto = ped[0] + ' x ' + ped[1];
            CantidadTitle = ped[0] + ' unidades x ' + ped[1]+' días';

            PrecioMultiplicado = Number(Precio) * Number(ped[0]) * Number(ped[1]);
        break;
    }

    


    EspacioDeRowsDeLaTabla.innerHTML = `
    <row id="RowDeProducto-${Producto.id}">
        <celda class="ColumnaImagen">
            <img src="../../Imagenes/Productos/${(Producto.ULRImagen?Producto.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
        </celda>
        <celda class="ColumnaID">${Producto.id}</celda>
        <celda class="ColumnaNombre">${Producto.nombre}</celda>
        <celda class="ColumnaCantidad" title="${CantidadTitle}">${CantidadProducto}</celda>
        <celda class="ColumnaPrecio" title="${(ProductoNuevoALaCoti?'':'Precio sujeto al momento de crear la cotización')}">${Precio}$</celda>
        <celda class="ColumnaTotal"><span class="TotalSumable" categoria="${Producto.idcategoria}">${PrecioMultiplicado.toFixed(2)}</span>$</celda>
        <div class="CeldaOculta">
            <i id="BotonModificarProductoEspecifico-${Producto.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
            <i id="BotonEliminarProductoEspecifico-${Producto.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
        </div>
    </row>
    ` + EspacioDeRowsDeLaTabla.innerHTML;

    ActualizarTotalSegunTotalesEnLista();
    CheckSiPaso2Disponible();
}

async function MostrarModal_AgregarProducto(valor){
    if(valor){
        Modal_AgregarProducto.style = "display: flex";
        EnlistarProductosDeUltimaBuqueda();
        await EsperarMS(50);
        VentanaModal_AgregarProducto.className ="VentanaFlotante";
    }else{
        VentanaModal_AgregarProducto.className ="VentanaFlotante OcultarModal";
        await EsperarMS(100);
        Modal_AgregarProducto.style = "";
    }
    
}


function SoloInt(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8373846";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    if(InputCantidadPS.value.length > 5){
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
    if(tecla=="." && InputCantidadPS.value.includes(".")){
        return false;
    }
//solo permite dos numeros mas despues del punto
    if(InputCantidadPS.value.includes(".")){
        pedazos = InputCantidadPS.value.split(".",2);
        posicionDelPunto = InputCantidadPS.value.indexOf(".");
        posicionDelTarget = e.target.selectionStart;

        if(pedazos[1].length>3 && posicionDelTarget>posicionDelPunto){
            return false;
        }
    }

    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}