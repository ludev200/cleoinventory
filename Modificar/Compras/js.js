const InputDeNombre = document.getElementById('InputDeNombre');
const SelectTiempoLimitado = document.getElementById('SelectTiempoLimitado');
const CampoNumeroDeDias = document.getElementById('CampoNumeroDeDias');
const listaDeProductos = document.getElementById('listaDeProductos');
const EspacioDeRowsDeLaTabla = document.getElementById('EspacioDeRowsDeLaTabla');

const ModalAgregarProducto = document.getElementById('ModalAgregarProducto');
const VentadaModalAgregarProducto = document.getElementById('VentadaModalAgregarProducto');
const BotonCerrarVentanaProductos = document.getElementById('BotonCerrarVentanaProductos');
const showAddProductModalButton = document.getElementById('showAddProductModalButton');
const BotonBuscarProductos = document.getElementById('BotonBuscarProductos');
const BuscadorDeProductos = document.getElementById('BuscadorDeProductos');
const ListaDeProductosConsultados = document.getElementById('ListaDeProductosConsultados');
const PrevisualizacionDeProducto = document.getElementById('PrevisualizacionDeProducto');
const CajaDeProveedores = document.getElementById('CajaDeProveedores');



const SW_container = document.getElementById('SWAlert');
const hoy = new Date();
var downloadProducts = [];
var downloadProviders = [];
var productsOnList = [];
var autoClose = true;



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


window.addEventListener('load', function(){
    if(SW_container){
        Toast.fire({
            icon: "error",
            title: SW_container.innerText
        });
    }

    this.document.getElementById('validateFormButton').addEventListener('click', function(){
        validateForm();
    })

    
    BotonBuscarProductos.addEventListener('click', function(){
        searchProduct(BuscadorDeProductos.value);
    })
    BuscadorDeProductos.addEventListener('keyup', function(e){
        if(e.keyCode == 13){
            searchProduct(BuscadorDeProductos.value);
        }
    })
    
    

    VentadaModalAgregarProducto.addEventListener('click', function(e){
        e.stopPropagation();
    })
    ModalAgregarProducto.addEventListener('click', async function(){
        showAddProductModal(false);
    })
    BotonCerrarVentanaProductos.addEventListener('click', async function(){
        showAddProductModal(false);
    })

    this.document.getElementById('BotonDesplegable_AgregarProducto').addEventListener('click', function(){
        showAddProductModalButton.click();
    })
    showAddProductModalButton.addEventListener('click', async function(){
        BuscadorDeProductos.value = '';
        showAddProductModal(true);
        showProductSelected(0);
        searchProduct(BuscadorDeProductos.value);
    })


    InputDeNombre.addEventListener('keyup', function(){
        if(InputDeNombre.value == ''){
            document.getElementById('DivDelPalitoDinamico').innerHTML = `<div class="PalitoDelNombre"></div> Escribe un nombre o descripción `;
        }else{
            document.getElementById('DivDelPalitoDinamico').innerHTML = `<div class="PalitoDelNombre"></div> ${InputDeNombre.value} `;
        }
        
    })

    

    checkCalendarStatus();
    SelectTiempoLimitado.addEventListener('change', function(){
        checkCalendarStatus();
    })
    CalendarioFlotante.addEventListener('change', function(){
        calculateDurationDays();
    })
    CampoNumeroDeDias.addEventListener('keyup', function(){
        updateCalendar();
    })


    listaDeProductos.value.split('¿').forEach(idXquan => {
        pieces = idXquan.split('x');
        addProduct(pieces[0], pieces[1]);
    });


    lookForProviders();
})

async function searchProduct(value){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getResultsProductsOnStockSearch&value=${value}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                
                if(petition.result.length > 0){
                    ListaDeProductosConsultados.innerHTML = '';
                    

                    
                    petition.result.forEach( row => {
                        quantity=0;
                        if(productsOnList[row.id]){
                            quantity = productsOnList[row.id];
                        }
                    
                        ListaDeProductosConsultados.innerHTML+= `<row idProduct="${row.id}" class="RowProductoConsultado" id="RowProductoConsultado-${row.id}">
                            <celda class="ColumnaImagenP">
                                <img src="../../Imagenes/Productos/${(row.img? row.img:'ImagenPredefinida_Productos.png')}" alt="">
                            </celda>
                            <celda class="ColumnaIDP">${row.id}</celda>
                            <celda class="ColumnaNombre2CS">${row.name}</celda>
                            <celda class="ColumnaExistencia" title="${row.stockExistence} ${row.unitName} en el inventario">
                                ${(row.idStatus>1? `<span title="Este producto se encuentra ${(row.idStatus==2? `bajo el nivel de alerta (${row.alertLvl} ${row.unitName})`:'agotado')}" style="color: ${(row.idStatus==2? '#FEA82F':'rgb(236, 49, 49)')};" class="AlertaDeExistencia fi-sr-comment-exclamation"></span>`:'')} ${row.stockExistence}
                            </celda>
                            <celda class="ColumnaAgregadoCS">x ${quantity}</celda>
                        </row>`;
                    })


                    document.querySelectorAll('.RowProductoConsultado').forEach( row => {
                        row.addEventListener('click', function(){
                            showProductSelected(this.getAttribute('idProduct'));
                        })
                    })
                }else{
                    ListaDeProductosConsultados.innerHTML = `<div class="Flex-gap2 HoverVino TablaDeproductosVacia"><span>No hay productos para mostrar...</span></div>`;
                }
            }
        }
    }catch(error){
        console.log(error)
    }
}

async function showProductSelected(id){
    document.querySelector('.ProductoSeleccionado')?.classList.remove('ProductoSeleccionado');
    

    if(id > 0){
        product = await getProduct(id);
        document.getElementById(`RowProductoConsultado-${id}`)?.classList.add('ProductoSeleccionado');

        
        quantity=0;
        if(productsOnList[id]){
            quantity = productsOnList[id];
        }

        PrevisualizacionDeProducto.innerHTML = `<div class="ProductoSiSeleccionado">
            <img src="../../Imagenes/Productos/${product.img? product.img:'ImagenPredefinida_Productos.png'}" alt="">
            <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${product.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
            <b class="NombreDelProductoSiSeleccionado">${product.name}</b>
            <b class="PrecioDelProductoSiSeleccionado">${product.price}$</b>
            <div class="ElementosPaElegirCantidad">
                <span>Cantidad:</span>
                <div class="CantidadYUnidad TextToNumbre">
                    <span class="fi-rr-cross-small"></span>
                    <input value="${quantity>0? quantity:''}" onkeypress="return ${(product.idCategory==1)? 'onlyNumber':'onlyFloat'}(this, event)" onclick="this.select();" onpaste="return false" autocomplete="off" maxlength="9" id="InputCantidad" type="text">
                    <span title="">${product.unit}</span>
                </div>
                <span id="PrecioMultiplicado" class="PrecioMultiplicado">Total: 0.00$</span>
            </div>
            <div id="CajaDeBotonBorrarYAgregar">
                ${(quantity>0)? 
                `<span idProduct="${product.id}" title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>
                <button idProduct="${product.id}" title="Modificar la cantidad cotizada de este producto" id="BotonParaModificarElProductoSeleccionado" class="BotonParaAgregarElProductoSeleccionado">Modificar</button>`:
                `<button idProduct="${product.id}" id="BotonParaAgregarElProductoSeleccionado" title="Agregar este producto a la cotización" class="BotonParaAgregarElProductoSeleccionado">Agregar</button>`}
                
            </div>
            <label title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
                Cierre automático
                <input type="checkbox" ${autoClose? 'checked':''} id="CierreAutomaticoModalProductos">
                <div class="slider round"></div>
            </label>
        </div>`;

        document.getElementById('InputCantidad').focus();
        document.getElementById('CierreAutomaticoModalProductos').addEventListener('change', function(){
            autoClose = this.checked;
        })

        document.getElementById('InputCantidad').addEventListener('keyup', function(e){
            if(e.keyCode == 13){
                document.getElementById('BotonParaAgregarElProductoSeleccionado')?.click();
                document.getElementById('BotonParaModificarElProductoSeleccionado')?.click();
            }
        })

        document.getElementById('BotonParaModificarElProductoSeleccionado')?.addEventListener('click', function(){
            removeProduct(this.getAttribute('idProduct'));
            addProduct(this.getAttribute('idProduct'), document.getElementById('InputCantidad').value);
            addProductOnInput(this.getAttribute('idProduct'), document.getElementById('InputCantidad').value);

            if(autoClose){
                showAddProductModal(false);
            }
        })

        document.getElementById('BotonParaAgregarElProductoSeleccionado')?.addEventListener('click', function(){
            addProductOnInput(this.getAttribute('idProduct'), document.getElementById('InputCantidad').value);
            addProduct(this.getAttribute('idProduct'), document.getElementById('InputCantidad').value);
            lookForProviders();


            if(autoClose){
                showAddProductModal(false);
            }
        })

        document.getElementById('BotonRemoverProductoSeleccionado')?.addEventListener('click', function(){
            removeProduct(this.getAttribute('idProduct'));


            if(autoClose){
                showAddProductModal(false);
            }else{
                showProductSelected(0);
            }
        })


        
    }else{
        PrevisualizacionDeProducto.innerHTML = `<div class="ProductoNoSeleccionado">
            <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
            <span>Seleccione un producto</span>
        </div>`;
    }
}


function addProductOnInput(id, quantity){
    listaDeProductos.value+= (listaDeProductos.value? '¿':'')+id+'x'+quantity;
}


async function showAddProductModal(value){
    if(value){
        ModalAgregarProducto.style = 'display: flex;';
        await sleep(300);
        VentadaModalAgregarProducto.classList.remove('OcultarModal');
    }else{
        VentadaModalAgregarProducto.classList.add('OcultarModal');
        await sleep(300);
        ModalAgregarProducto.style = '';
    }
}


async function addProduct(id, quantity){
    productsOnList[id] = quantity;
    product = await getProduct(id);
    
    
    if(EspacioDeRowsDeLaTabla.innerHTML == '<row class="RowVacio"><span>No hay productos en esta orden de compra</span></row>'){
        EspacioDeRowsDeLaTabla.innerHTML = '';
    }

    EspacioDeRowsDeLaTabla.appendChild(createElementFromHTML(`<row id="RowDeProducto-${product.id}">
        <celda class="ColumnaImagen"><img src="../../Imagenes/Productos/${(product.img? product.img:'ImagenPredefinida_Productos.png')}" alt=""></celda>
        <celda class="ColumnaID">${product.id}</celda>
        <celda class="ColumnaProducto">${product.name}</celda>
        <celda class="ColumnaCantidad">x ${quantity}</celda>
        <div class="CeldaOculta">
            <i idProduct="${product.id}" title="Modificar la cantidad de este producto" id="BotonModificarProductoEspecifico-${product.id}" class="fi-rr-pencil"></i>
            <i idProduct="${product.id}" title="Eliminar este producto de la lista" id="BotonEliminarProductoEspecifico-${product.id}" class="fi-rr-trash"></i>
        </div>
    </row>`));

    document.querySelector(`#RowDeProducto-${product.id} .fi-rr-trash`).addEventListener('click', function(){
        removeProduct(this.getAttribute('idProduct'));
    })
    document.querySelector(`#RowDeProducto-${product.id} .fi-rr-pencil`).addEventListener('click', async function(){
        showAddProductModalButton.click();
        showProductSelected(this.getAttribute('idProduct'));
    })
}


function removeProduct(id){
    productsOnList[id] = 0;

    prodXquan_onInput = listaDeProductos.value.split('¿');
    raul = prodXquan_onInput.find( prodXquan => {
        pieces = prodXquan.split('x');
        return pieces[0]==id;
    })
    prodXquan_onInput.splice(prodXquan_onInput.indexOf(raul), 1);
    listaDeProductos.value = prodXquan_onInput.join('¿');


    document.getElementById(`RowDeProducto-${id}`).remove();
    if(EspacioDeRowsDeLaTabla.innerHTML == ''){
        EspacioDeRowsDeLaTabla.innerHTML = `<row class="RowVacio"><span>No hay productos en esta orden de compra</span></row>`;
    }

    lookForProviders();
}



async function lookForProviders(){
    value = '';
    
    if(listaDeProductos.value == ''){
        CajaDeProveedores.innerHTML = `<div class="ProveedoresVacios">No hay proveedores disponibles</div>`;
    }else{
        listaDeProductos.value.split('¿').forEach( idXQuan => {
            pieces = idXQuan.split('x');
            value+=(value? '¿':'')+pieces[0];
        })


        try{
            response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=lookForProviders&value=${value}`);

            if(response.status == 200){
                petition = await response.json();

                if(petition.status == 200){
                    
                    if(petition.result.length == 0){
                        CajaDeProveedores.innerHTML = `<div class="ProveedoresVacios">No hay proveedores disponibles</div>`;
                    }else{
                        CajaDeProveedores.innerHTML = '';

                        for (const [idProvider, products] of Object.entries(petition.result)) {
                            productosProveidos = '';
                            provider = await getProvider(idProvider);

                            products.forEach( idProduct => {                                
                                product = downloadProducts[idProduct];

                                productosProveidos+= `<div class="CardProductoProveido">
                                    <celda class="CeldaImagenPP">
                                        <img src="../../Imagenes/Productos/${(product.img)? product.img:'ImagenPredefinida_Productos.png'}" alt="">
                                    </celda>
                                    <celda class="CeldaNombrePP">${product.name}</celda>
                                </div>`;
                            })


                            CajaDeProveedores.innerHTML+= `<div class="CardProveedor">
                                <a href="../../Proveedores/Proveedor?rif=${provider.id}" target="_blank" class="DivDeImgYNombreDeProveedor">
                                    <img src="../../Imagenes/Proveedores/${(provider.img)? provider.img:'ImagenPredefinida_Proveedores.png'}" alt="">
                                    <div class="RifYNombre">
                                    <span class="NombreProveedor">${provider.name}</span>
                                        <span class="RifProveedor">${provider.docType}-${provider.id}</span>
                                    </div>
                                </a>
                                <span class="TituloProductosDeProveedor"> <i class="fi-sr-package"></i> PRODUCTOS: </span>
                                <div class="ContenedorDelFlexDeProveedores">
                                    <div class="FlexDeProductosProveidos mostly-customized-scrollbar">
                                    ${productosProveidos}                                    
                                    </div>
                                </div>
                            </div>`;
                        }
                    }
                }
            }else{

            }
        }catch(error){
            console.log(error)
        }
    }
}

async function getProvider(id){
    if(downloadProviders[id]){
        return downloadProviders[id];
    }

    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getProvider&id=${id}`);
        
        if(response.status == 200){
            petition = await response.json();


            if(petition.status == 200){
                downloadProducts[id] = petition.result;

                return petition.result;
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.result
                })
            }
        }
    }catch(error){
        console.log(error)
    }
}

async function getProduct(id){
    if(downloadProducts[id]){
        return downloadProducts[id];
    }

    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getProduct&id=${id}`);
        
        if(response.status == 200){
            petition = await response.json();


            if(petition.status == 200){
                downloadProducts[id] = petition.result;

                return petition.result;
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.result
                })
            }
        }
    }catch(error){
        console.log(error)
    }
}


async function validateForm(){
    idEntity = document.getElementById('idEntity').value;
    nname = document.getElementById('InputDeNombre').value;
    expireDate = CalendarioFlotante.value;
    products = listaDeProductos.value;

    
    try{
        
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=6&idEntity=${idEntity}&method=validateData&`+
        `expireDate=${expireDate}&products=${products}&name=${nname}`);

        if(response.status == 200){
            petition = await response.json();

            
            if(petition.status == 200){
                document.querySelector('form').submit();
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                });
            }
        }
    }catch(error){
        console.log(error)
    }
}




function onlyNumber(element, e){
    if(isNaN(e.key)){
        return false;
    }
}
function zeroFormat(value){
    if(value < 10 && value > -10){
        return `0${value}`;
    }else{
        return value;
    }
}


function updateCalendar(){
    tomorrow = new Date(hoy.getTime() + 86400000);
    selectedDay = new Date(hoy.getTime() + (86400000 * CampoNumeroDeDias.value));

    if(CampoNumeroDeDias.value > 0){
        CalendarioFlotante.value = `${selectedDay.getFullYear()}-${zeroFormat(selectedDay.getMonth()+1)}-${zeroFormat(selectedDay.getDate())}`
    }else{
        CalendarioFlotante.value = `${tomorrow.getFullYear()}-${zeroFormat(tomorrow.getMonth()+1)}-${zeroFormat(tomorrow.getDate())}`
    }

}

function calculateDurationDays(){
    pieces = CalendarioFlotante.value.split('-');
    
    
    calendar = new Date(pieces[0], (pieces[1] - 1), pieces[2])
    diferencia = diferenciaEnDias(calendar, hoy);
    tomorrow = new Date(hoy.getTime() + 86400000);
     

    
    if(diferencia>0){
        CampoNumeroDeDias.value = diferencia;
    }else{
        CampoNumeroDeDias.value = '1';
        CalendarioFlotante.value = `${tomorrow.getFullYear()}-${zeroFormat(tomorrow.getMonth()+1)}-${zeroFormat(tomorrow.getDate())}`
    }
}

function checkCalendarStatus(){
    const CampoNumeroDeDias = document.getElementById('CampoNumeroDeDias');
    const LabelDias = document.getElementById('LabelDias');
    


    if(SelectTiempoLimitado.value > 0){
        CampoNumeroDeDias.value = '1';
        CampoNumeroDeDias.style.opacity = '1';
        CampoNumeroDeDias.disabled = false;
        LabelDias.style.opacity = '1';
        CalendarioFlotante.disabled = false;
        CampoNumeroDeDias.setAttribute('placeholder', '1');
        

        
        calculateDurationDays();
    }else{
        CampoNumeroDeDias.value = '';
        CampoNumeroDeDias.style.opacity = '0.7';
        CampoNumeroDeDias.disabled = true;
        LabelDias.style.opacity = '0.7';
        CalendarioFlotante.disabled = true;
        CampoNumeroDeDias.setAttribute('placeholder', '');
    }
}

function diferenciaEnDias(fecha1, fecha2) {
    const fecha1_ms = fecha1.getTime();
    const fecha2_ms = fecha2.getTime();

    // Calcular la diferencia en milisegundos
    const diferencia_ms = (fecha1_ms - fecha2_ms);

    // Convertir la diferencia de milisegundos a días
    const dias = Math.ceil(diferencia_ms / (1000 * 60 * 60 * 24));

    return dias;
}










function createElementFromHTML(htmlString) {
    var div = document.createElement('div');
    div.innerHTML = htmlString.trim();
    return div.firstChild;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
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
                return false;
            }
        }
    }
}