let InputBuscadorDeCot = document.getElementById('InputBuscadorDeCot');
let BotonBuscarCotizaciones = document.getElementById('BotonBuscarCotizaciones');
let EspacioDeCotSeleccionada = document.getElementById('EspacioDeCotSeleccionada');
let DivResultadosDeBusqueda = document.getElementById('DivResultadosDeBusqueda');
let IDCotSeleccionada = document.getElementById('IDCotSeleccionada');

let CotizacionesEnEspera;
let Cot_UltimaBusqueda;
let ProductosDeCot = [];
let ClientesDeCots = [];


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

window.addEventListener('load', async function(){
    PrepararRowCotEnLista();
    await ConsultarApiPorCot('');
    
    if(IDCotSeleccionada.value){
        MostrarCotPorID(IDCotSeleccionada.value);
    }
    
    if(this.document.querySelector('.AvisitoFijo')){
        Toast.fire({
            icon: 'success',
            title: 'La cotización #'+this.document.querySelector('.AvisitoFijo').getAttribute('id')+' ha sido rechazada exitosamente'
        });
    }
})

BotonBuscarCotizaciones.addEventListener('click', () => {
    BuscarSegunInput(InputBuscadorDeCot.value);
})

InputBuscadorDeCot.addEventListener('keyup', (e) => {
    
    if(e.keyCode == 13 || !InputBuscadorDeCot.value){
        BotonBuscarCotizaciones.click();
    }
})


async function BuscarSegunInput(TextoABuscar){
    document.getElementById('DivResultadosDeBusqueda').innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
    Cargando
</div>`;
    await ConsultarApiPorCot(TextoABuscar);
    DivResultadosDeBusqueda.innerHTML = `
    
    `;

    if(Cot_UltimaBusqueda.length){
        Cot_UltimaBusqueda.forEach( element => {
            DivResultadosDeBusqueda.innerHTML = DivResultadosDeBusqueda.innerHTML + `
            <div class="Row RowCotEnLista" id="RowDeCot-${element.id}">
                <span class="ColumnaID">${element.id}</span>
                <span class="ColumnaNombre text2lines">${element.nombre}</span>
                <span class="ColumnaNombre">${(element.cedulaCliente)? zerofill(element.cedulaCliente, 9):'<i style="color: gray;">Ninguno</i>'}</span>
            </div>
            `;
        });
    }else{
        DivResultadosDeBusqueda.innerHTML = `
        <div class="estebetavacio">
            <span>No hay cotizaciones en espera a mostrar</span>
        </div>
        `;  
    }

    

    

    PrepararRowCotEnLista();
    
}

function PrepararRowCotEnLista(){
    RowsDeCot = document.querySelectorAll('.RowCotEnLista');

    RowsDeCot.forEach(element => {
        element.addEventListener('click', () => {
            pedazos = element.id.split('-');
            MostrarCotPorID(pedazos[1]);
            
            if(element.className == 'Row RowCotEnLista'){
                RowsDeCot.forEach( element => {
                    element.className = 'Row RowCotEnLista';
                })

                element.className = 'Row RowCotEnLista RowSeleccionada';
            }
        });
    });
}

async function MostrarCotPorID(id){
    
    var CotzacionSeleccionada = CotizacionesEnEspera.find(function(element){
        return element.id == id;
    });

    if(CotzacionSeleccionada){
        console.log(CotzacionSeleccionada)

        if(CotzacionSeleccionada.cedulaCliente){
            HayCliente = true;

            if(!ClientesDeCots[CotzacionSeleccionada.cedulaCliente]){
                await ConsultarApiPorCliente(CotzacionSeleccionada.cedulaCliente);

            }
            //console.log(ClientesDeCots);

            console.log(ClientesDeCots[CotzacionSeleccionada.cedulaCliente]);

            Cliente = ClientesDeCots[CotzacionSeleccionada.cedulaCliente];
            
        }else{
            HayCliente = false;
        }

        if(CotzacionSeleccionada.fechaCreacion){
            HayCreacion = true;
            peda = CotzacionSeleccionada.fechaCreacion.split(' ');
            pedaz = peda[0].split('-');
            console.log(pedaz);
            fechaCreacion = pedaz[2]+' de '+pedaz[1]+' del '+pedaz[0];
        }else{
            HayCreacion = false;
        }


        if(CotzacionSeleccionada.fechaExpiracion){
            Expira = true;
            ped = CotzacionSeleccionada.fechaExpiracion.split('-');

            FechaExpiracion = ped[2] + ' de ' + ped[1] + ' del ' +ped[0];
        }else{
            Expira = false;
        }

        EspacioDeCotSeleccionada.innerHTML = `
        <div class="SiSeleccionado">
                    <span class="NombreDeCot">${CotzacionSeleccionada.nombre}</span>
                    <div class="EspacioDelCliente">
                        <img src="../../Imagenes/Clientes/${(HayCliente&&Cliente.ULRImagen?Cliente.ULRImagen:'ImagenPredefinida_Clientes.png')}" alt="">
                        <div class="CajaIDCliente">
                            ${(HayCliente?'<span>'+Cliente.nombre+'</span><span class="IDCliente">'+Cliente.tipoDeDocumento+'-'+zerofill(Cliente.rif, 9)+'</span>':'<span style="color: gray;">EN ESTA COTIZACIÓN NO</span><span style="color: gray;">SE ESPECIFICÓ UN CLIENTE</span>')}
                        </div>
                    </div>
                    <div class="CajaDetallitos">
                        <div class="Cajon">
                            <span>Creado</span>
                            <span>Vence</span>
                        </div>
                        <div class="Cajon">
                            <span>:</span>
                            <span>:</span>
                        </div>
                        <div class="Cajon">
                            <span>${(HayCreacion?fechaCreacion:'<i style="color: gray;">Sin fecha definida</i>')}</span>
                            <span>${(Expira?FechaExpiracion:'<i style="color: gray;">Sin fecha definida</i>')}</span>
                        </div>
                    </div>
                    <a href="../Venta/?id=${id}" target="_blank" class="LetreroMasInfo" title="Ver detalles"><i class="fi-sr-info"></i></a>
                    <span class="NomPro"><i class="fi-sr-package"></i> Productos cotizados:</span>
                    <div class="TablaDeCambios">
                        <div id="EspacioDeProductosDeCot" class="EspacioDeRowDeCambio mostly-customized-scrollbar">
                        
                        </div>
                    </div>
                    <div class="CajaPrecio">Total:   <span title="Costo de productos + CAS + Utilidades + IVA" id="SpanPrecioTotal">0.00</span>$</div>
                    <div class="CajaDeBotonCancelar">
                        <button title="Indicar esta venta como rechazada por el cliente" form="FormCancelarVenta">Rechazar venta</button>
                    </div>
                </div>
        `;

        IDCotSeleccionada.value = id;

        if(!ProductosDeCot[id]){
            await ConsultarApiPorProductosDeCot(id);
        }

        
        
        

        let EspacioDeProductosDeCot = document.getElementById('EspacioDeProductosDeCot');
        EspacioDeProductosDeCot.innerHTML = ``;

        PrecioPorProdXCant = 0;
        CostoAsociadoAlSalario = 0;
        ProductosDeCot[id].forEach( element => {
            PaLaMulti = element.cantidad;


            if(element.idCategoria == 1){
                CantidadAMostrar = element.cantidad
            }else{
                if(element.idCategoria == 2){
                    CantidadAMostrar = element.cantidad.toFixed(4);
                }else{
                    trozos = element.cantidad.toString().split('.');
                    CantidadAMostrar = trozos[0]+' x '+trozos[1];
                    
                    PaLaMulti = trozos[0]+' x '+trozos[1]+' días';

                    if(element.idCategoria == 3){
                        CostoAsociadoAlSalario = CostoAsociadoAlSalario + element.precioMultiplicado;
                    }
                }
            }



            //console.log(element);
            PrecioPorProdXCant = PrecioPorProdXCant + (element.precioMultiplicado);
            
            EspacioDeProductosDeCot.innerHTML = `
            <div class="RowDeCambio" title="${element.precioUnitario}$ x ${PaLaMulti} = ${element.precioMultiplicado}$">
                <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Imagen">
                    <img class="Imagen_ProductoDeCompra" src="../../Imagenes/Productos/${(element.ULRImagen)?element.ULRImagen:'ImagenPredefinida_Productos.png'}" alt="">
                </div>
                <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Nombre">
                    ${element.nombre}
                </div>
                <div class="Celda_ProductoDeCompra CeldaRowDeCambio_Cantidad">
                    ${CantidadAMostrar}
                </div>
            </div>
            ` + EspacioDeProductosDeCot.innerHTML;
        });

        SpanPrecioTotal = document.getElementById('SpanPrecioTotal');
        CostoAsociadoAlSalario = (CostoAsociadoAlSalario * CotzacionSeleccionada.CASalario * 0.01);
        
        CostoDeProductosConCAS = CostoAsociadoAlSalario + PrecioPorProdXCant;
        Utilidades = (CostoDeProductosConCAS * CotzacionSeleccionada.pUtilidades * 0.01);

        Subtotal = CostoDeProductosConCAS + Utilidades;
        iva = (Subtotal * CotzacionSeleccionada.pIVA * 0.01);

        TotalTotal = Subtotal + iva;

        SpanPrecioTotal.innerText = TotalTotal.toFixed(2);
    }else{
        console.log('No se encontró la cotizacion #'+id);
    }
}

async function ConsultarApiPorCot(descripcion){
    
    try{
        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Cotizaciones.php?descripcion='+descripcion+'&estado=33');

        if(respuesta.status === 200){
            PeticionConFiltros = await respuesta.json();

            if(PeticionConFiltros.objetos == undefined){
                Cot_UltimaBusqueda = [];
            }else{
                if(!CotizacionesEnEspera){
                    CotizacionesEnEspera = PeticionConFiltros.objetos;
                }else{
                    Cot_UltimaBusqueda = PeticionConFiltros.objetos;
                }
            }
        }
        
    }catch(error){
        console.log(error);
    }
}

async function ConsultarApiPorProductosDeCot(idCot){
    try{
        let respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_ProductosDeCot.php?idCotizacion='+idCot);

        if(respuesta.status === 200){
            PeticionConFiltros = await respuesta.json();

            if(PeticionConFiltros.objetos != undefined){
                ProductosDeCot[idCot] = PeticionConFiltros.objetos;
            }
        }
    }catch(error){
        console.log(error);
    }
}

async function ConsultarApiPorCliente(id){
    try{
        respuesta = await fetch('http://'+ipserver+'/CleoInventory/API/API_Clientes.php?id='+id);

        if(respuesta.status === 200){
            Peticion = await respuesta.json();

            if(Peticion.objetos == undefined){
                console.log('No se encontró el cliente');
            }else{
                ClientesDeCots[id] = Peticion.objetos[0];
            }
        }
    }catch(error){
        console.log(error);
    }
}

function zerofill(string, max){
    for (let index = 0; max > string.length; index++) {
        string = '0'+string;
    }
    return string;
}