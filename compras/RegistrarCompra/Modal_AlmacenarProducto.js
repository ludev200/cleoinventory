let Modal_AlmacenarProducto = document.getElementById('Modal_AlmacenarProducto');
let BotonCerrarVentana_AlmacenarProducto = document.getElementById('BotonCerrarVentana_AlmacenarProducto');
let VentadaModal_AlmacenarProducto = document.getElementById('VentadaModal_AlmacenarProducto');
let InputProductoAlmacenar = document.getElementById('InputProductoAlmacenar');
let BotonBuscarAlmacenes = document.getElementById('BotonBuscarAlmacenes');
let DescripcionBuscadorDeAlmacenes = document.getElementById('DescripcionBuscadorDeAlmacenes');
let ListaDeAlmacenesConsultados = document.getElementById('ListaDeAlmacenesConsultados');
let InputAlmacenSeleccionado = document.getElementById('InputAlmacenSeleccionado');
let divPrevisualizacionDeAlmacen = document.getElementById('PrevisualizacionDeAlmacen');
let AlmacenajeEnFormato = document.getElementById('AlmacenajeEnFormato');
let ContenedorDeRowsDeProductosYaAlmacenados = document.getElementById('ContenedorDeRowsDeProductosYaAlmacenados');
let BotonPaGuardarTodo = document.getElementById('ButtonGuardar');
let FormularioPalPost = document.getElementById('FormularioPalPost')

var VisibilidadDelModal_AlmacenarProducto = false;
var TodosLosAlmacenes = "";
var AlmacenesDeUltimaConsulta = "";
var CierreAutomaticoDelModalAlmacenaje = true;

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

DescripcionBuscadorDeAlmacenes.addEventListener('keyup', function(e){
    if(e.keyCode == 13){
        BotonBuscarAlmacenes.click();
    }
})

BotonPaGuardarTodo.addEventListener('click', function(){
    if(BotonPaGuardarTodo.className == 'BotonContinuarDisponible'){
        FormularioPalPost.submit();
    }
})



BotonBuscarAlmacenes.addEventListener('click', () => {
    MostrarEnListaAlmacenesEncontradosEnLaBusqueda(DescripcionBuscadorDeAlmacenes.value);
})

async function ConsultarAlmacenes(Descripcion){
    document.getElementById('ListaDeAlmacenesConsultados').innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    try{
        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Almacenes.php?descripcion='+Descripcion+'&idEstado=51');

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
        console.log(error);
    }
}

async function MostrarEnListaAlmacenesEncontradosEnLaBusqueda(Descripcion){
    await ConsultarAlmacenes(Descripcion);

    Obj_ProductoAAlmacenar = TodosLosProductos.filter(function(ProductoDeLaLista){
        return ProductoDeLaLista.id == InputProductoAlmacenar.value;
    });
    Obj_ProductoAAlmacenar = Obj_ProductoAAlmacenar[0];

    ListaDeAlmacenesConsultados.innerHTML = ``;


    if(AlmacenesDeUltimaConsulta.length == 0){
        ListaDeAlmacenesConsultados.style = "height: calc(80vh - 140px);";
        ListaDeAlmacenesConsultados.innerHTML = `
            <div class="Flex-gap2 HoverVino TablaDeproductosVacia">
                <span>No hay ordenes de compra para mostrar...</span>
            </div>
        `;
    }else{
        ListaDeAlmacenesConsultados.style = "";
        ListaDeAlmacenesConsultados.innerHTML = ``;
    }

    AlmacenesDeUltimaConsulta.forEach(element => {
        
        ProductoEnAlmacen = element.productos.filter(function(ProductoAlmacenado){
            return ProductoAlmacenado.idProducto == InputProductoAlmacenar.value;
        });

        
        if(ProductoEnAlmacen.length){
            CantidadEnEsteAlmacen = ProductoEnAlmacen[0].existencia;
        }else{
            CantidadEnEsteAlmacen = 0;
        }


        ListaDeAlmacenesConsultados.innerHTML = ListaDeAlmacenesConsultados.innerHTML + `
            <row id="RowAlmacenResultado-${element.id}" class="RowAlmacenResultado">
                <celda class="ColumnaID">${element.id}</celda>
                <celda class="ColumnaDescripcion">${element.nombre}</celda>
                <celda title="${CantidadEnEsteAlmacen} ${Obj_ProductoAAlmacenar.nombredeunidad} existentes en este almacén." class="ColumnaCantidad">x ${CantidadEnEsteAlmacen}</celda>
            </row>
        `;
    });


    document.querySelectorAll('.RowAlmacenResultado').forEach(Row => {
        Row.addEventListener('click', () => {
            document.querySelectorAll('.RowAlmacenResultado').forEach(Row2 => {
                Row2.className = "RowAlmacenResultado";
            });
            Row.className = "RowAlmacenResultado ProductoSeleccionado";

            pedazos = Row.id.split('-');
            InputAlmacenSeleccionado.value = pedazos[1];

            PrevisualizacionDeAlmacen(true);
        })
    })

    
}

function PrevisualizacionDeAlmacen(valor){
    if(valor){
        //Determino cositas que necesito
        AlmacenClickado = TodosLosAlmacenes.find(function(elementoIterado){
            return elementoIterado.id == InputAlmacenSeleccionado.value;
        });

        ProductoAAlmacenarAqui = TodosLosProductos.find(function(elementoIterado){
            return elementoIterado.id == InputProductoAlmacenar.value;
        });

        if(ProductoAAlmacenarAqui.idcategoria == 1){
            CantidadComprada = CantidadesCargadosAlPaso2[ProductosCargadosAlPaso2.indexOf(InputProductoAlmacenar.value)];
        }else{
            CantidadComprada = Number(CantidadesCargadosAlPaso2[ProductosCargadosAlPaso2.indexOf(InputProductoAlmacenar.value)]).toFixed(4);
        }

        CantidadTotalYaAlmacenada = CantidadesAlmacenadasEnPaso2[ProductosCargadosAlPaso2.indexOf(ProductoAAlmacenarAqui.id)];

        
        

        TrozoDeAlmacenQueContieneElProducto = AlmacenajeEnFormato.value.split('¿').find( elemento => {
            pedazo = elemento.split(':');
            return pedazo[0] == InputAlmacenSeleccionado.value;
        })


        ProductoYaAlmacenadoEnEsteAlmacen = false;
        CantidadYaAlmacenada = 0;
        if(TrozoDeAlmacenQueContieneElProducto != undefined){
            lol = TrozoDeAlmacenQueContieneElProducto.split(':');
            ProductosEnEsteAlmacen = lol[1].split(',');

            ProdXCantYaAlmacenada = ProductosEnEsteAlmacen.find( elemento => {
                jsjs = elemento.split('x');
                return jsjs[0] == InputProductoAlmacenar.value;
            })


            if(ProdXCantYaAlmacenada != undefined){
                ProductoYaAlmacenadoEnEsteAlmacen = true;
                CantidadYaAlmacenada = ProdXCantYaAlmacenada.split('x');
                CantidadYaAlmacenada = CantidadYaAlmacenada[1];
            }
        }




        //Armo el contenido del div
        divPrevisualizacionDeAlmacen.innerHTML = `
        <div class="AlmacenSiSeleccionado">
            <div class="Weas">
                <span class="TituloDeAlmacen">${AlmacenClickado.nombre}</span>
                <span class="DireccionDeAlmacen">${AlmacenClickado.direccion}</span>
            </div>
            <div class="TituloDeProductoAAlmacenar">
                <span class="titulillo fi-sr-package"> ${ProductoAAlmacenarAqui.id} ${ProductoAAlmacenarAqui.nombre}</span>
                <div class="asdf">
                    <div>
                        <span>${CantidadTotalYaAlmacenada}</span>
                        /
                        <span>${CantidadComprada}</span>
                    </div>
                    <div>
                        ${CantidadTotalYaAlmacenada} ${ProductoAAlmacenarAqui.nombredeunidad} ya almacenados de ${CantidadComprada} ${ProductoAAlmacenarAqui.simbolo} comprados
                    </div>
                </div>
            </div>
            
            <div class="Weas" style="padding: 20px 0 40px 0;">
                <div class="DivDeAlmacenaje">
                    <img class="ImagenDelProductoAAlmacenar" src="../../Imagenes/Productos/${((ProductoAAlmacenarAqui.ULRImagen)?ProductoAAlmacenarAqui.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                    <span class="fi-rr-caret-right"></span>
                    <img src="../../Imagenes/iconoDelMenu_Almacenes.png" alt="">
                </div>
                <input value="${((ProductoYaAlmacenadoEnEsteAlmacen)?CantidadYaAlmacenada:'')}" onkeypress="return ${((ProductoAAlmacenarAqui.idcategoria == 1)?'SoloIntParaAlmacenaje':'SoloFloatParaAlmacenaje')}(event);" autocomplete="off" placeholder="Cantidad a almacenar" class="InputDeCantidadAAlmacenar" id="CantidadDelProductoAAlmacenar" type="text">
            </div>
            <div class="Weas">
                <div class="FH">
                    ${((ProductoYaAlmacenadoEnEsteAlmacen)?'<button id="BotonEliminarProductoAlmacenadoSeleccionado"><i class="fi-sr-trash"></i></button>':'')}
                    <button id="BotonAlmacenarProductoSeleccionado" class="BotonAlmacenarProductoSeleccionado">${((ProductoYaAlmacenadoEnEsteAlmacen)?'Modificar':'Almacenar')}</button>
                </div>
                <label style="align-self: center;" title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
                    Cierre automático
                    <input type="checkbox" ${((CierreAutomaticoDelModalAlmacenaje)?'checked':'')} name="" id="CierreAutomaticoModalAlmacenar">
                    <div class="sliderrr round"></div>
                </label>
            </div>
        </div>
        `;

        let CierreAutomaticoModalAlmacenar = document.getElementById('CierreAutomaticoModalAlmacenar');
        let InputCantidadDelProductoAAlmacenar = document.getElementById('CantidadDelProductoAAlmacenar');
        let BotonAlmacenarProductoSeleccionado = document.getElementById('BotonAlmacenarProductoSeleccionado');

        if(ProductoYaAlmacenadoEnEsteAlmacen){
            document.getElementById('BotonEliminarProductoAlmacenadoSeleccionado').addEventListener('click', () => {
                EliminarProductoDeListaDeAlmacenados(InputAlmacenSeleccionado.value+'_'+InputProductoAlmacenar.value);

                if(CierreAutomaticoDelModalAlmacenaje){
                    MostrarModalAlmacenarProducto(false);
                }else{
                    PrevisualizacionDeAlmacen(false);
                    InputAlmacenSeleccionado.value = "";
                }
            })
        }

        

        CierreAutomaticoModalAlmacenar.addEventListener('change', () => {
            CierreAutomaticoDelModalAlmacenaje = CierreAutomaticoModalAlmacenar.checked;
        })
        InputCantidadDelProductoAAlmacenar.addEventListener('keyup', (e) => {
            if(e.keyCode == 13){
                BotonAlmacenarProductoSeleccionado.click();
            }else{
                if(InputCantidadDelProductoAAlmacenar.value == 0){
                    BotonAlmacenarProductoSeleccionado.className = "BotonPrincipalDeModalDesactivado";
                    BotonAlmacenarProductoSeleccionado.setAttribute('title', 'La cantidad deb');
                }else{
                    indice= ProductosCargadosAlPaso2.indexOf(ProductoAAlmacenarAqui.id);
                    CantidadDisponibleParaAlmacenar = Number(CantidadesCargadosAlPaso2[indice]) - CantidadesAlmacenadasEnPaso2[indice];


                    if(ProductoYaAlmacenadoEnEsteAlmacen){
                        if(Number(InputCantidadDelProductoAAlmacenar.value) <= (Number(CantidadDisponibleParaAlmacenar) + Number(CantidadYaAlmacenada))){
                            BotonAlmacenarProductoSeleccionado.className = "BotonAlmacenarProductoSeleccionado";
                        }else{
                            BotonAlmacenarProductoSeleccionado.className = "BotonPrincipalDeModalDesactivado";
                        }
                        
                    }else{
                        if((Number(InputCantidadDelProductoAAlmacenar.value) <= Number(CantidadDisponibleParaAlmacenar))){
                            BotonAlmacenarProductoSeleccionado.className = "BotonAlmacenarProductoSeleccionado";
                            BotonAlmacenarProductoSeleccionado.removeAttribute('title');
                        }else{
                            BotonAlmacenarProductoSeleccionado.className = "BotonPrincipalDeModalDesactivado";
                            BotonAlmacenarProductoSeleccionado.setAttribute('title', 'La cantidad a almacenar debe ser menor a ' + CantidadDisponibleParaAlmacenar);                        
                        }
                    }
                    
                }
            }
        })
        BotonAlmacenarProductoSeleccionado.addEventListener('click', () => {
            if(InputCantidadDelProductoAAlmacenar.value <= 0){
                Toast.fire({
                    icon: 'warning',
                    title: 'La cantidad debe ser mayor a 0'
                });
            }else{
                if(BotonAlmacenarProductoSeleccionado.className == "BotonAlmacenarProductoSeleccionado"){
                    if(BotonAlmacenarProductoSeleccionado.innerText == "Almacenar"){
                        AgregarRowALaTablaEnInterfaz(AlmacenClickado, ProductoAAlmacenarAqui, InputCantidadDelProductoAAlmacenar.value);
                        AgregarProductoAListaDeAlmacenados(InputCantidadDelProductoAAlmacenar.value);
                    }else{
                        ModificarProductoAlmacenado(ProductoAAlmacenarAqui, AlmacenClickado, InputCantidadDelProductoAAlmacenar.value);
                    }
                }
            }
        })

        InputCantidadDelProductoAAlmacenar.focus();
    }else{
        InputAlmacenSeleccionado.value = "";
        divPrevisualizacionDeAlmacen.innerHTML = `
        <div class="ProductoNoSeleccionado">
                            <img src="../../Imagenes/Sistema/ImagenPredefinida_Almacen.png" alt="">
                            <span>Seleccione un almacén</span>
                        </div>
        `;
    }
}

function ModificarProductoAlmacenado(Producto, Almacen, Cantidad){
    EliminarProductoDeListaDeAlmacenados(Almacen.id+'_'+Producto.id);
    AgregarRowALaTablaEnInterfaz(Almacen, Producto, document.getElementById('CantidadDelProductoAAlmacenar').value);
    AgregarProductoAListaDeAlmacenados(document.getElementById('CantidadDelProductoAAlmacenar').value);

    if(CierreAutomaticoDelModalAlmacenaje){
        MostrarModalAlmacenarProducto(false);
    }else{
        PrevisualizacionDeAlmacen(false);
        InputAlmacenSeleccionado.value = "";
    }
}





ContenedorDeRowsDeProductosYaAlmacenados.addEventListener('click', (evento) => {
    if(evento.target.tagName.toLowerCase() == 'i'){
        
        if(evento.target.id.includes('-')){
            pedazos = evento.target.id.split('-');

            if(pedazos[0] == 'BotonEliminarProductoAlmacenadoEspecifico'){
                EliminarProductoDeListaDeAlmacenados(pedazos[1]);
            }else if(pedazos[0] == 'BotonModificarProductoAlmacenadoEspecifico'){
                Alm_Prod = pedazos[1].split('_');
                
                InputProductoAlmacenar.value = Alm_Prod[1];
                MostrarModalAlmacenarProducto(true);
                InputAlmacenSeleccionado.value = Alm_Prod[0];
                PrevisualizacionDeAlmacen(true);
            }else{

            }
            
        }else{
            alert('El boton no cuenta con una ID de formato válido.');
        }
    }
});

function EliminarProductoDeListaDeAlmacenados(Alm_Pro){
    pedazos = Alm_Pro.split('_');
    AlmacenesConSusCosas = AlmacenajeEnFormato.value.split('¿');

    var TrozoDeAlmacenSeleccionado = AlmacenesConSusCosas.find( elemento => {
        ped = elemento.split(':');

        return ped[0] == pedazos[0];
    })

    if(TrozoDeAlmacenSeleccionado == undefined){
        alert('Algo ha ido mal al borrar el almacenaje de este producto')
    }else{
        IDAlmacen_ProductosConCantidad = TrozoDeAlmacenSeleccionado.split(':');
        
        Array_ProdXCant = IDAlmacen_ProductosConCantidad[1].split(',');
        ProdXCant_ABorrar = Array_ProdXCant.find( elemento => {
            lol = elemento.split('x');
            return lol[0] == pedazos[1];
        })

        //
        

        indiceABorrar = Array_ProdXCant.indexOf(ProdXCant_ABorrar);
        Array_ProdXCant.splice(indiceABorrar, 1);

        if(Array_ProdXCant.length){
            TrozoNuevoDeProductosXCantidad = Array_ProdXCant.join(',');
            NuevoTrozo = pedazos[0] + ':' + TrozoNuevoDeProductosXCantidad;
            AlmacenesConSusCosas[AlmacenesConSusCosas.indexOf(TrozoDeAlmacenSeleccionado)] = NuevoTrozo;
            AlmacenajeEnFormato.value = AlmacenesConSusCosas.join('¿');
        }else{
            AlmacenesConSusCosas.splice(AlmacenesConSusCosas.indexOf(TrozoDeAlmacenSeleccionado), 1);
            AlmacenajeEnFormato.value = AlmacenesConSusCosas.join('¿');
        }

        ActualizoArrayDeCantidades(pedazos[1]);
        document.getElementById('RowDeProductoAlmacenado-' + pedazos[0] +'_'+ pedazos[1]).remove();

        if(!AlmacenajeEnFormato.value){
            ContenedorDeRowsDeProductosYaAlmacenados.innerHTML = `
                <row class="RowVacioAlmacenaje">
                    <span>No se ha indicado el almacenaje de ningún producto.</span>
                </row>
            `;
        }

        
        document.getElementById('RowAlmacenResultado-' + pedazos[0]).className = "RowAlmacenResultado ";
    }
}

function AgregarRowALaTablaEnInterfaz(Almacen, Producto, Cantidad){
    
    InventarioDeAlmacen = Almacen.productos.find( element => {
        return element.idProducto == Producto.id;
    })
    if(InventarioDeAlmacen == undefined){
        Existencia = 0;
    }else{
        Existencia = InventarioDeAlmacen.existencia;
    }



    if(!AlmacenajeEnFormato.value){
        ContenedorDeRowsDeProductosYaAlmacenados.innerHTML = "";
    }
    
    ContenedorDeRowsDeProductosYaAlmacenados.innerHTML = `
    <row id="RowDeProductoAlmacenado-${Almacen.id}_${Producto.id}">
        <celda class="ColumnaImagenAP">
            <img src="../../Imagenes/Productos/${((Producto.ULRImagen)?Producto.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
        </celda>
        <celda class="ColumnaNombreAP">${Producto.nombre}</celda>
        <celda class="ColumnaNombreAP">${Almacen.nombre}</celda>
        <celda class="ColumnaCantidadAP" title="${Cantidad} ${Producto.nombredeunidad} a almacenar en este almacén">+ ${Cantidad}</celda>
        <celda class="ColumnaResultadoAP" title="${Number(Existencia) + Number(Cantidad)} ${Producto.nombredeunidad} en este almacén"> ${Number(Existencia) + Number(Cantidad)}</celda>
        <div class="CeldaOculta">
            <i id="BotonModificarProductoAlmacenadoEspecifico-${Almacen.id}_${Producto.id}" title="Modificar este producto." class="fi-rr-pencil"></i>
    <i id="BotonEliminarProductoAlmacenadoEspecifico-${Almacen.id}_${Producto.id}" title="Eliminar este producto." class="fi-rr-trash"></i>
        </div>
    </row>
    ` + ContenedorDeRowsDeProductosYaAlmacenados.innerHTML;
}

function AgregarProductoAListaDeAlmacenados(Cantidad){

    TrozoDelAlmacenSeleccionado = AlmacenajeEnFormato.value.split('¿').find( (almacenConSusCosas) => {
        pedazos = almacenConSusCosas.split(':');
        return pedazos[0] == InputAlmacenSeleccionado.value;
    })

    if(TrozoDelAlmacenSeleccionado == undefined){
        AlmacenajeEnFormato.value = AlmacenajeEnFormato.value + ((AlmacenajeEnFormato.value)?'¿':'') + `${InputAlmacenSeleccionado.value}:${InputProductoAlmacenar.value}x${Cantidad}`;
    }else{
        ArrayDeAlmacenesConSusWeas = AlmacenajeEnFormato.value.split('¿');
        ArrayDeAlmacenesConSusWeas[ArrayDeAlmacenesConSusWeas.indexOf(TrozoDelAlmacenSeleccionado)] = TrozoDelAlmacenSeleccionado + ',' + InputProductoAlmacenar.value + 'x' + Cantidad;
        AlmacenajeEnFormato.value = ArrayDeAlmacenesConSusWeas.join('¿');
    }

    ActualizoArrayDeCantidades(InputProductoAlmacenar.value);

    //Determino si cerrar modal o no
    if(CierreAutomaticoDelModalAlmacenaje){
        MostrarModalAlmacenarProducto(false);
    }else{
        PrevisualizacionDeAlmacen(false);
        InputAlmacenSeleccionado.value = "";
    }

}

function ActualizoArrayDeCantidades(IDProductoAletarado){
    if(IDProductoAletarado > 0){
        TodosLosProdXCant = '';

        AlmacenajeEnFormato.value.split('¿').forEach( element => {
            ped = element.split(':');
            TodosLosProdXCant = TodosLosProdXCant + ((TodosLosProdXCant)?',':'') + ped[1];
        })

        CantidadAlmacenadaDeEsteProducto = 0;
        TodosLosProdXCant.split(',').forEach( ProdXCant => {
            lol = ProdXCant.split('x');
            if(lol[0] == IDProductoAletarado){
                CantidadAlmacenadaDeEsteProducto = CantidadAlmacenadaDeEsteProducto + Number(lol[1]);
            }
        })


        CantidadesAlmacenadasEnPaso2[ProductosCargadosAlPaso2.indexOf(IDProductoAletarado)] = CantidadAlmacenadaDeEsteProducto;
    
        

        document.getElementById('CantidadAlmacenadaActualmente-'+IDProductoAletarado).innerText = CantidadAlmacenadaDeEsteProducto;
        CeldaAEditar = document.getElementById('CeldaDeCantidadAlma/Comp-'+IDProductoAletarado);
        CeldaAEditar.setAttribute('title', `${CantidadAlmacenadaDeEsteProducto} ${CeldaAEditar.getAttribute('unidadm')} almacenado de ${CantidadesCargadosAlPaso2[ProductosCargadosAlPaso2.indexOf(IDProductoAletarado)]} ${CeldaAEditar.getAttribute('unidadm')} comprado`)

        BotonPaAbrirModalDeEsteProd = document.getElementById('BotonAlmacenar-'+IDProductoAletarado);
        if(CantidadAlmacenadaDeEsteProducto >= CantidadesCargadosAlPaso2[ProductosCargadosAlPaso2.indexOf(IDProductoAletarado)]){
            BotonPaAbrirModalDeEsteProd.className = "BotonAlmacenarProductoNoDisponible";
            BotonPaAbrirModalDeEsteProd.removeAttribute('title');
        }else{
            BotonPaAbrirModalDeEsteProd.className = "BotonAlmacenarProductoDisponible";
            BotonPaAbrirModalDeEsteProd.setAttribute('title', 'Almacenar este producto');
            
        }
    }

    
    EstanTodosLosProductosAlmacenados = false;
    for (let index = 0; index < CantidadesCargadosAlPaso2.length; index++) {
        const element = CantidadesCargadosAlPaso2[index];

        if(Number(element) > Number(CantidadesAlmacenadasEnPaso2[index])){
            break;
        }else{

            if(index == (Number(CantidadesCargadosAlPaso2.length) - 1)){
                EstanTodosLosProductosAlmacenados = true;
            }
        }
    }

    
    if(EstanTodosLosProductosAlmacenados){
        BotonPaGuardarTodo.className ="BotonContinuarDisponible";
        BotonPaGuardarTodo.removeAttribute('title');
    }else{
        BotonPaGuardarTodo.className ="BotonContinuarNoDisponible";
        BotonPaGuardarTodo.setAttribute('title', 'Aùn hay productos comprados que no han sido almacenados.');
    }
    
}

//VISIBILIDAD DEL MODAL
async function MostrarModalAlmacenarProducto(valor){
    VisibilidadDelModal_AlmacenarProducto = !VisibilidadDelModal_AlmacenarProducto;

    if(valor){        
        Modal_AlmacenarProducto.style = "display: flex";
        BotonBuscarAlmacenes.click();
        PrevisualizacionDeAlmacen(false);
        await EsperarMS(50);
        VentadaModal_AlmacenarProducto.className = "VentanaFlotante";
    }else{
        VentadaModal_AlmacenarProducto.className = "VentanaFlotante OcultarModal";
        await EsperarMS(100);
        Modal_AlmacenarProducto.style = "";
        
    }
}

Modal_AlmacenarProducto.addEventListener('click', () => {
    MostrarModalAlmacenarProducto(false);
})
VentadaModal_AlmacenarProducto.addEventListener('click', (e) => {
    e.stopPropagation();
})
BotonCerrarVentana_AlmacenarProducto.addEventListener('click', () => {
    MostrarModalAlmacenarProducto(false);
})


function SoloIntParaAlmacenaje(e){
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





function SoloFloatParaAlmacenaje(e){  
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