/////////////////ELEMENTOS//////////////
let PanelDeCambiosARealizar = document.getElementById('PanelDeCambiosARealizar');
let InputMostrarProductosAgotados = document.getElementById('InputMostrarProductosAgotados');
let IconoDeOjo = document.getElementById('IconoDeOjo');
let FormularioDeInventario = document.getElementById('FormularioDeInventario');
let CajaDeCambiosListados = document.getElementById('CajaDeCambiosListados');

let CheckMostrarModalDeError = document.getElementById('CheckMostrarModalDeError');
let ModalDeErroresDelPOST = document.getElementById('ModalDeErroresDelPOST');

let InputsNumber_INT;
let BotonesRehacer;
let BotonesAgregar;

let CantidadOriginalEnInput = [];
let Productos;
let Almacenes;


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

/////////////////EVENTOS//////////////
window.addEventListener('scroll', () => {
    AjustarTamanioDelPanelDeCambios();
})

CheckMostrarModalDeError.addEventListener('change', () => {
    RevisarEstadoDelModalDeErrores();
})

function RevisarEstadoDelModalDeErrores(){
    if(CheckMostrarModalDeError.checked){
        ModalDeErroresDelPOST.style = "display: flex;";
    }else{
        ModalDeErroresDelPOST.style = "";
    }
}

window.addEventListener('load', () => {
    document.getElementById('botonFakeSubmit').addEventListener('click', (e) => {
        e.preventDefault();
        if(document.getElementById('InputDeCambios').value == ''){
            Toast.fire({
                icon: 'warning',
                title: 'No se indicó ningún cambio en el inventario'
            });
        }else if(document.getElementById('textarealol').value == ''){
            Toast.fire({
                icon: 'warning',
                title: 'No se especificó una descripción'
            });
        }else{
            document.getElementById('botonSubmit').click();
        }
    })
    InputsNumber_INT = document.querySelectorAll('.InputINT');
    BotonesRehacer = document.querySelectorAll('.BotonRehacer');
    BotonesAgregar = document.querySelectorAll('.BotonAgregarCambio');

    ConsultarAPIAlmacenes();
    ConsultarAPIProductos();
    RevisarEstadoDelModalDeErrores();


    BotonesRehacer.forEach(BotonRehacer => {
        BotonRehacer.addEventListener('click', () => {
            pedazos = BotonRehacer.id.split('-');
            let InputDeEsteBoton = document.getElementById('InputINT-'+pedazos[1]);
            var BotonAgregarAModificar = document.getElementById('BotonAgregar-' + pedazos[1]);

            InputDeEsteBoton.value = CantidadOriginalEnInput[pedazos[1]];
            BotonRehacer.className = "BotonRehacer BotonInactivo";
            BotonRehacer.removeAttribute('title');

            BotonAgregarAModificar.className = "BotonAgregarCambio BotonInactivo";
            BotonAgregarAModificar.removeAttribute('title');
        })
    })


    BotonesAgregar.forEach(BotonAgregar => {
        BotonAgregar.addEventListener('click', () => {
            
            if(BotonAgregar.className == 'BotonAgregarCambio'){
                

                pedazos = BotonAgregar.id.split('-');
                AlmaXProd = pedazos[1];

                var InputConCantidad = document.getElementById('InputINT-' + AlmaXProd);

                Diferencia = InputConCantidad.value - CantidadOriginalEnInput[AlmaXProd];

                if(InputConCantidad.getAttribute('categoria') == 2){
                    Diferencia = Diferencia.toFixed(4);
                }

                
                AgregarCambioALaLista(AlmaXProd, Diferencia);
                BotonAgregar.className = "BotonInactivo";
            }
        })
    })
    

    InputsNumber_INT.forEach(InputPalSoloINT => {     
        //Guardo los valores originales de los input
        pedazos = InputPalSoloINT.id.split('-');
        CantidadOriginalEnInput[pedazos[1]] = InputPalSoloINT.value;

        //Agrego evento de enter
        InputPalSoloINT.addEventListener('keyup', (event) => {
            if(event.keyCode == 13){
                pedazos = InputPalSoloINT.id.split('-');

                var BotonAgegarTemporal = document.getElementById('BotonAgregar-' + pedazos[1]);
                BotonAgegarTemporal.click();
            }
        })

        //Agrego evento poner 0 si no hay nada
        InputPalSoloINT.addEventListener('blur', () => {
            if(!InputPalSoloINT.value){
                InputPalSoloINT.value = "0";
            }
        })

        //Agrego evento para activar y desactivar boton rehacer
        InputPalSoloINT.addEventListener('keyup', () => {
            pedazos = InputPalSoloINT.id.split('-');
            var BotonRehacerAModificar = document.getElementById('BotonRehacer-' + pedazos[1])
            var BotonAgregarAModificar = document.getElementById('BotonAgregar-' + pedazos[1]);

            if(InputPalSoloINT.value == CantidadOriginalEnInput[pedazos[1]]){
                BotonRehacerAModificar.className = "BotonRehacer BotonInactivo";
                BotonRehacerAModificar.removeAttribute('title');

                BotonAgregarAModificar.className = "BotonAgregarCambio BotonInactivo";
                BotonAgregarAModificar.removeAttribute('title');
            }else{
                BotonRehacerAModificar.className = "BotonRehacer";
                BotonRehacerAModificar.setAttribute('title', 'Volver a la cantidad original')

                BotonAgregarAModificar.className = "BotonAgregarCambio";
                BotonAgregarAModificar.setAttribute('title', 'Agregar cambio')
            }
        })
    })


    
    
})

let InputDeCambios = document.getElementById('InputDeCambios');

function AgregarCambioALaLista(AlmaXProd, Cantidad){
    
    Array_AlmacenYProducto = AlmaXProd.split('x');
    ProductoRepetido = false;

    InputDeCambios.value.split('¿').forEach(CambioEnLista => {
        pedazos = CambioEnLista.split('x');
        AlmaXProd_EnLista = pedazos[0] + 'x' + pedazos[1];

        
        
        if(AlmaXProd_EnLista == AlmaXProd){
            ProductoRepetido = true;
        }
    })


    if(ProductoRepetido){
        PosicionEnArray = -1;

        InputDeCambios.value.split('¿').forEach(CambioListado => {
            PedazosDelCambio = CambioListado.split('x');
            
            ID_CambioListado = PedazosDelCambio[0] + 'x' + PedazosDelCambio[1];
            
            if(ID_CambioListado == AlmaXProd){
                PosicionEnArray = InputDeCambios.value.split('¿').indexOf(CambioListado);
            }
        })

        ArrayDeCambios = InputDeCambios.value.split('¿');

        ArrayDeCambios.splice(PosicionEnArray, 1);
        ArrayDeCambios.unshift(AlmaXProd + 'x' + Cantidad);

        EliminarCambioDeLaLista(AlmaXProd);

        InputDeCambios.value = ArrayDeCambios.join('¿');

    }else{
        Cambio = AlmaXProd + 'x' + Cantidad;

        InputDeCambios.value = Cambio + ((InputDeCambios.value)?'¿' + InputDeCambios.value:'');
    }

    AgregarTarjetitaALaLista(AlmaXProd, Cantidad);
}

function EliminarCambioDeLaLista(AlmaXProd){
    document.getElementById('CambioListado-' + AlmaXProd).remove();

    var BotonTemporal_Rehacer = document.getElementById('BotonRehacer-' + AlmaXProd);
    //BotonTemporal_Rehacer.click();
}

function AgregarTarjetitaALaLista(AlmaXProd, Cantidad){
    pedazos = AlmaXProd.split('x');
    ID_Almacen = pedazos[0];
    ID_Producto = pedazos[1];

    ProductoSeleccionado = Productos.filter( function(ProductoDeLaLista){
        return ProductoDeLaLista.id == ID_Producto;
    });
    ProductoSeleccionado = ProductoSeleccionado[0];

    AlmacenSeleccionado = Almacenes.filter( function(AlmacenDeLaLista){
        return AlmacenDeLaLista.id == ID_Almacen;
    });
    AlmacenSeleccionado = AlmacenSeleccionado[0];


    CajaDeCambiosListados.innerHTML = `
        <div class="RowDeCambio" id="CambioListado-${AlmaXProd}">
            <celda class="CeldaRowDeCambio_Flechita"> <i class="fi-rr-caret-${((Cantidad > 0)?'up':'down')}"></i> </celda>
            <celda class="CeldaRowDeCambio_Imagen"><img src="../../Imagenes/Productos/${(ProductoSeleccionado.ULRImagen)?ProductoSeleccionado.ULRImagen:'ImagenPredefinida_Productos.png'}" alt=""></celda>
            <celda class="CeldaRowDeCambio_Almacen">
                <span class="SpanDeNombre">${ProductoSeleccionado.nombre}</span>
                <span class="SpanDeAlmacen">(${AlmacenSeleccionado.nombre})</span>
            </celda>
            <celda class="CeldaRowDeCambio_Cantidad">${((Cantidad > 0)?'+':'')}${Cantidad}</celda>
            <celda id="BotonQuitar-${AlmaXProd}" class="CeldaRowDeCambio_Quitar"> <i class="fi-rr-cross-small"></i> </celda>
        </div>
    ` + CajaDeCambiosListados.innerHTML;

    var Rows = document.querySelectorAll('.CeldaRowDeCambio_Quitar');

    Rows.forEach(Row => {
        Row.addEventListener('click', () => {            
            pedazos = Row.id.split('-');

            EliminarCambio(pedazos[1])
        })
    })    
}

function EliminarCambio(AlmaXProd){
    ArrayDeCambios = InputDeCambios.value.split('¿');
    PosicionEnArray = -1;

    ArrayDeCambios.forEach(CambioListado => {
        PedazosDelCambio = CambioListado.split('x');
        
        ID_CambioListado = PedazosDelCambio[0] + 'x' + PedazosDelCambio[1];
        
        if(ID_CambioListado == AlmaXProd){
            PosicionEnArray = InputDeCambios.value.split('¿').indexOf(CambioListado);
        }
    })


    ArrayDeCambios.splice(PosicionEnArray, 1);
    InputDeCambios.value = ArrayDeCambios.join('¿');

    EliminarCambioDeLaLista(AlmaXProd);
}

InputMostrarProductosAgotados.addEventListener('change', () => {
    if(InputMostrarProductosAgotados.checked){
        IconoDeOjo.className = "fi-rr-eye";
    }else{
        IconoDeOjo.className = "fi-rr-eye-crossed";
    }
    FormularioDeInventario.submit();
})



/////////////////FUNCIONES//////////////
async function ConsultarAPIProductos(){
    try{
        let respuesta1 = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?categoria=1');
        let respuesta2 = await fetch('http://'+ipserver+'/CleoInventory/API/API_Productos.php?categoria=2');

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
            
            Productos = MaterialesRecibidos.concat(EquiposRecibidos);

            if(InputDeCambios.value){
                InputDeCambios.value.split('¿').forEach(CambioQueYaEstaba => {
                    pedazos = CambioQueYaEstaba.split('x');

                    AlmaXProd = pedazos[0] + 'x' + pedazos[1];
                    AgregarTarjetitaALaLista(AlmaXProd, pedazos[2]);
                })
            }
            
        }else{
            alert('Error al consultar API por productos materiales y equipo. Status: ' + respuesta1.status + '/' + respuesta2.status)
        }
    }catch(error){
        console.log(error);
    }
}


async function ConsultarAPIAlmacenes(){
    try{
        let respuesta = await fetch("http://"+ipserver+"/CleoInventory/API/API_Almacenes.php");

        if(respuesta.status === 200){
            AlmacenesRecibidos = await respuesta.json();

            if(AlmacenesRecibidos.objetos == undefined){
                alert('La API no ha devuelto ningun almacen');
            }else{
                Almacenes = AlmacenesRecibidos.objetos;
            }
        }
    }catch(error){
        console.log(error);
    }

}


function AjustarTamanioDelPanelDeCambios(){
    if(window.scrollY > 100){
        
        PanelDeCambiosARealizar.style = "height: calc(100vh - 55px);";
    }else{
        Diferencia = 155 - window.scrollY;
        PanelDeCambiosARealizar.style = "height: calc(100vh - " + Diferencia + "px);"
    }
}





/////////////////OTROS//////////////
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
async function EsperardS(Tiempo) {
    for (let i = 0; i < Tiempo; i++) {
        
        await sleep(i * 100);
    }
}




function SoloInt(e, idDelINput){
    var InputClickeado = document.getElementById(idDelINput);

    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8¬37¬38¬46";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    if(InputClickeado.value.length > 8){
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



function SoloFloat(e, idDelINput){  
    var InputClickeado = document.getElementById(idDelINput);

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
    if(tecla=="." && InputClickeado.value.includes(".")){
        return false;
    }
//solo permite dos numeros mas despues del punto
    if(InputClickeado.value.includes(".")){
        pedazos = InputClickeado.value.split(".",2);
        posicionDelPunto = InputClickeado.value.indexOf(".");
        posicionDelTarget = e.target.selectionStart;

        if(pedazos[1].length>3 && posicionDelTarget>posicionDelPunto){
            return false;
        }
    }

    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}