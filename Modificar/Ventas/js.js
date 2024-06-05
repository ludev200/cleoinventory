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


const SW_container = document.getElementById('SWAlert');
hoy = new Date();
downloadProducts = [];
productsOnList = [];
var autoClose = true;
var rowWithEvents = [];

const idClientInput = document.getElementById('idClientInput');
const EspacioDeTarjetaDeCliente = document.getElementById('EspacioDeTarjetaDeCliente');
const InputDeBuscadorDeClientes = document.getElementById('InputDeBuscadorDeClientes');
const BotonFiltrarCliente = document.getElementById('BotonFiltrarCliente');
const SelectTiempoLimitado = document.getElementById('SelectTiempoLimitado');
const CalendarioFlotante = document.getElementById('CalendarioFlotante');



const BuscadorDeProductos = document.getElementById('BuscadorDeProductos');
const BotonBuscarProductos = document.getElementById('BotonBuscarProductos');
const SelectCategoriaDelProductoABuscar = document.getElementById('SelectCategoriaDelProductoABuscar');
const PrevisualizacionDeProducto = document.getElementById('PrevisualizacionDeProducto');


const ProductosCotizados = document.getElementById('ProductosCotizados');
const MaquinasCotizados = document.getElementById('MaquinasCotizados');
const ManosCotizados = document.getElementById('ManosCotizados');

  
  
window.addEventListener('load', function(){
    if(SW_container){
        Toast.fire({
            icon: "error",
            title: SW_container.innerText
        });
    }

    this.document.getElementById('validateFormButton').addEventListener('click', function(e){
        e.preventDefault();
        validateForm();
    })

    this.document.getElementById('InputCASalario').addEventListener('keyup', function(){
        document.getElementById('TituloSalario').innerText = `Asociado al salario (${this.value}%)`;
        updateTotalAccount();
    })
    this.document.getElementById('Utilidades').addEventListener('keyup', function(){
        document.getElementById('TituloUtilidades').innerText = `Asociado al salario (${this.value}%)`;
        updateTotalAccount();
    })
    this.document.getElementById('InputIVA').addEventListener('keyup', function(){
        document.getElementById('TituloIVA').innerText = `I.V.A. (${this.value}%)`;
        updateTotalAccount();
    })
    

    updateProductsOnList();


    if(idClientInput.value != ''){
        loadClient(idClientInput.value);
    }

    BotonBuscarProductos.addEventListener('click', function(){
        trySearchProduct(BuscadorDeProductos.value);
    })
    BuscadorDeProductos.addEventListener('keyup', function(e){
        if(e.keyCode == 13){
            BotonBuscarProductos.click();
        }
    })

    this.document.getElementById('BotonRemoverCliente').addEventListener('click', function(){
        removeClient()
    });

    this.document.getElementById('BotonBuscarCliente').addEventListener('click', function(){
        showAddClientModal(true); 
    })

    this.document.getElementById('showAddClientModalButton').addEventListener('click', function(){
        showAddClientModal(true); 
    })

    SelectCategoriaDelProductoABuscar.addEventListener('click', function(){
        BotonBuscarProductos.click();
    })

    this.document.getElementById('BotonAgregarProductoMaterial').addEventListener('click', function(){
        SelectCategoriaDelProductoABuscar.value = '1';
        showAddProductModal(true);
    })
    this.document.getElementById('BotonAgregarProductoHerramienta').addEventListener('click', function(){
        SelectCategoriaDelProductoABuscar.value = '2';
        showAddProductModal(true);
    })
    this.document.getElementById('BotonAgregarProductoMano').addEventListener('click', function(){
        SelectCategoriaDelProductoABuscar.value = '3';
        showAddProductModal(true);
    })


    this.document.getElementById('addClientModal').addEventListener('click', function(){
        showAddClientModal(false);
    })
    this.document.getElementById('ModalAgregarProducto').addEventListener('click', function(){
        showAddProductModal(false);
    })


    this.document.getElementById('showAddProductModalButton').addEventListener('click', function(){
        SelectCategoriaDelProductoABuscar.value = '0';
        showAddProductModal(true);
    })

    this.document.querySelector('#addClientModal .VentanaFlotante').addEventListener('click', function(e){
        e.stopPropagation();
    })
    this.document.querySelector('#ModalAgregarProducto .VentanaFlotanteProductos').addEventListener('click', function(e){
        e.stopPropagation();
    })

    this.document.getElementById('BotonCerrarVentanaAgregarCliente').addEventListener('click', function(){
        showAddClientModal(false);
    })
    this.document.getElementById('BotonCerrarVentanaProductos').addEventListener('click', function(){
        showAddProductModal(false);
    })

    BotonFiltrarCliente.addEventListener('click', function(){
        trySearchClient(InputDeBuscadorDeClientes.value);
    })

    InputDeBuscadorDeClientes.addEventListener('keyup', function(e){
        if(e.keyCode == 13){
            BotonFiltrarCliente.click();
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

    setRowsOnListEvents();
    updateTotalAccount();
})


async function validateForm(){
    idEntity = document.getElementById('idEntity').value;

    idClient = document.getElementById('REAL_IdCliente').value;
    nname = document.getElementById('InputNombreDeLaCot').value;
    expireDate = document.getElementById('CalendarioFlotante').value;
    percentCAS = document.getElementById('InputCASalario').value;
    percentUti = document.getElementById('Utilidades').value;
    percentIVA = document.getElementById('InputIVA').value;
    materialProducts = document.getElementById('ProductosCotizados').value;
    equipProducts = document.getElementById('MaquinasCotizados').value;
    personalProducts = document.getElementById('ManosCotizados').value;

    

    try{
        console.log(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=2&idEntity=${idEntity}&method=validateData&`+
        `idClient=${idClient}&expireDate=${expireDate}&percentCAS=${percentCAS}&percentUti=${percentUti}&percentIVA=${percentIVA}&materialProducts=${materialProducts}&equipProducts=${equipProducts}&personalProducts=${personalProducts}&name=${nname}`);
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=2&idEntity=${idEntity}&method=validateData&`+
        `idClient=${idClient}&expireDate=${expireDate}&percentCAS=${percentCAS}&percentUti=${percentUti}&percentIVA=${percentIVA}&materialProducts=${materialProducts}&equipProducts=${equipProducts}&personalProducts=${personalProducts}&name=${nname}`);

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




function updateProductsOnList(){
    var ProductosCotizadosValue = document.getElementById('ProductosCotizados').value;
    var MaquinasCotizadosValue = document.getElementById('MaquinasCotizados').value;
    var ManosCotizadosValue = document.getElementById('ManosCotizados').value;
    productsOnList = [];

    
    if(ProductosCotizadosValue != ''){
        ProductosCotizadosValue.split('¿').forEach( prodXquan => {
            pieces = prodXquan.split('x');
            productsOnList[pieces[0]] = pieces[1];
        })
    }
    if(MaquinasCotizadosValue != ''){
        MaquinasCotizadosValue.split('¿').forEach( prodXquan => {
            pieces = prodXquan.split('x');
            productsOnList[pieces[0]] = pieces[1];
        })
    }
    if(ManosCotizadosValue != ''){
        ManosCotizadosValue.split('¿').forEach( prodXquan => {
            pieces = prodXquan.split('x');
            productsOnList[pieces[0]] = pieces[1];
        })
    }
}



function checkCalendarStatus(){
    const CampoNumeroDeDias = document.getElementById('CampoNumeroDeDias');
    const LabelDias = document.getElementById('LabelDias');
    const FechaVencimiento = document.getElementById('FechaVencimiento');
    


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

        FechaVencimiento.innerText = 'Esta cotización no tiene una fecha de vencimiento.';
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


    pieces = CalendarioFlotante.value.split('-');
    calendar = new Date(pieces[0], (pieces[1] - 1), pieces[2])
    FechaVencimiento.innerText = `${calendar.getDate()}/${calendar.getMonth()}/${calendar.getFullYear()}`;
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

    pieces = CalendarioFlotante.value.split('-');
    calendar = new Date(pieces[0], (pieces[1] - 1), pieces[2])
    FechaVencimiento.innerText = `${calendar.getDate()}/${Number(calendar.getMonth())+1}/${calendar.getFullYear()}`;
}


async function showAddProductModal(value){
    const ModalAgregarProducto = document.getElementById('ModalAgregarProducto');

    if(value){
        BuscadorDeProductos.value = '';
        
        BotonBuscarProductos.click();
        ModalAgregarProducto.style = 'display: flex';
        await sleep(200);
        ModalAgregarProducto.querySelector('.VentanaFlotanteProductos').classList.remove('OcultarContenidoModal');
    }else{
        ModalAgregarProducto.querySelector('.VentanaFlotanteProductos').classList.add('OcultarContenidoModal');
        await sleep(200);
        ModalAgregarProducto.style = '';
    }
}

async function showAddClientModal(value){    
    if(value){
        InputDeBuscadorDeClientes.value = '';
        BotonFiltrarCliente.click();
        document.getElementById('addClientModal').style = 'display: flex';
        await sleep(200);
        document.getElementById('addClientModal').querySelector('.VentanaFlotante').classList.remove('OcultarContenidoModal');
    }else{
        document.getElementById('addClientModal').querySelector('.VentanaFlotante').classList.add('OcultarContenidoModal');
        await sleep(200);
        document.getElementById('addClientModal').style = '';
    }
}


const EspacioDeProductosConsultados = document.getElementById('EspacioDeProductosConsultados');

async function trySearchProduct(value){
    category = SelectCategoriaDelProductoABuscar.value;
    
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getResultsProductsSearch&value=${value}&category=${category}`);

        if(response.status == 200){
            petition = await response.json();


            if(petition.status == 200){
                if(petition.result.length > 0){
                    EspacioDeProductosConsultados.innerHTML = '';
                    
                    petition.result.forEach( product => {
                        cantidadYaCotizada = 'x 0';
                        if(productsOnList[product.id]){
                            if(product.idCategoria < 3){
                                cantidadYaCotizada = `x ${productsOnList[product.id]}`;
                            }else{
                                pieces = productsOnList[product.id].split('.');
                                cantidadYaCotizada = `${pieces[0]} x ${pieces[1]}`;
                            }
                        }

                        EspacioDeProductosConsultados.innerHTML+= `<div id="ProductoEnRowDeBusqueda-${product.id}" class="Flex-gap2 HoverVino Pointer ProductoEnRowDeBusqueda" idProducto="${product.id}">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Productos/${(product.ULRImagen? product.ULRImagen:'ImagenPredefinida_Productos.png')}" alt="">
                            </span>
                            <span class="Celda ColumnaID">${product.id}</span>
                            <span style="width: calc(100% - 255px);" class="Celda ColumnaNombre4">${product.nombre}</span>
                            <div style="width: 60px;" class="Celda ColumnaCantidad">
                                ${cantidadYaCotizada}
                            </div>
                        </div>`;


                        document.querySelectorAll('.ProductoEnRowDeBusqueda').forEach( row => {
                            row.addEventListener('click', function(){
                                if(!row.classList.contains('ProductoSeleccionado')){
                                    document.querySelector('.ProductoSeleccionado')?.classList.remove('ProductoSeleccionado');
                                    this.classList.add('ProductoSeleccionado');
                                    setProductSelected(row.getAttribute('idProducto'));
                                }
                            })
                        })
                    })
                    
                }else{
                    EspacioDeProductosConsultados.innerHTML = `<div class="Flex-gap2 HoverVino TablaDeproductosVacia">
                        <span>No hay productos para mostrar...</span>
                    </div>`;
                }
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.result
                })
            }
        }
    }catch(error){
        console.log(error);
    }
}




async function setProductSelected(id){
    product = await getProduct(id);
    

    currentValue = '';
    currentPersonValue = '';
    currentDaysValue = '';
    
    
    if(product.idCategory < 3){
        if(productsOnList[product.id]){
            currentValue = `value="${productsOnList[product.id]}"`
        }
        
        if(product.idCategory==1){
            ElementosPaElegirCantidad = `<span>Cantidad:</span>
            <div class="CantidadYUnidad TextToNumbre">
                <span class="fi-rr-cross-small"></span>
                <input ${currentValue} onclick="this.select();" onkeypress="return onlyNumber(this, event)" onpaste="return false" autocomplete="off" maxlength="9" type="text" id="InputDeMultiplicar">
                <span>${product.unit}</span>
            </div>`;
        }else{

            ElementosPaElegirCantidad = `<span>Cantidad:</span>
            <div>
                <div class="CantidadYUnidad">
                    <input ${currentValue} style="width: 80px;" onclick="this.select();" onkeypress="return onlyNumber(this, event)" onpaste="return false" autocomplete="off" maxlength="6" type="text" id="InputDeMultiplicar">
                </div>
                <div style="text-align: center; font-size: 13px; color: gray;" title="Depreciación del producto"><span>x </span><span id="spoilage">${product.defaultSpoilage}</span></div>
            </div>`;
        }
        
    }else{
        if(productsOnList[product.id]){
            pieces = productsOnList[product.id].split('.');
            currentPersonValue = `value="${pieces[0]}"`;
            currentDaysValue = `value="${pieces[1]}"`;
        }

        ElementosPaElegirCantidad = `<div class="CantidadYUnidad TextToNumbre">
            <span class="fi-rr-cross-small"></span>
            <input ${currentPersonValue} onclick="this.select();" onkeypress="return onlyNumber(this, event)" onpaste="return false" autocomplete="off" maxlength="6" type="text" id="InputDePersonas">
            <span title="">Personas</span>
        </div>
        <div class="CantidadYUnidad TextToNumbre">
            <span class="fi-rr-cross-small"></span>
            <input ${currentDaysValue} onclick="this.select();" onkeypress="return onlyNumber(this, event)" onpaste="return false" autocomplete="off" maxlength="6" type="text" id="InputDeDias">
            <span title="">Días</span>
        </div>
        `;
    }
    
    

    PrevisualizacionDeProducto.innerHTML = `<div class="ProductoSiSeleccionado">
        <img src="../../Imagenes/Productos/${(product.img? product.img:'ImagenPredefinida_Productos.png')}" alt="">
        <a class="clasexd" target="_blank" href="../../Productos/Producto/?id=${product.id}"><span class="fi-sr-info" title="Ver más información de este producto"></span></a>                            
        <b class="NombreDelProductoSiSeleccionado">${product.name}</b>
        <b class="PrecioDelProductoSiSeleccionado">${product.price}$</b>
        <div class="ElementosPaElegirCantidad">
            ${ElementosPaElegirCantidad}
            <span id="PrecioMultiplicado" class="PrecioMultiplicado">Total: 0.00$</span>
        </div>
    <div id="CajaDeBotonBorrarYAgregar">
        ${(currentValue == ''? 
        `<button idProduct="${product.id}" idCategory="${product.idCategory}" id="BotonParaAgregarElProductoSeleccionado" title="Agregar este producto a la cotización" class="BotonParaAgregarElProductoSeleccionado">Agregar</button>`:
        `<span idProduct="${product.id}" title="Borrar este producto de la cotización" class="fi-sr-trash" id="BotonRemoverProductoSeleccionado"></span>
        <button idProduct="${product.id}" title="Modificar la cantidad cotizada de este producto" id="BotonModificarCantidadProductoSeleccionado" class="BotonParaAgregarElProductoSeleccionado">Modificar</button>`)}
    </div>
        <label title="Cerrar automaticamente al agregar un producto" class="DesactivarCierreAutomatico switch">
            Cierre automático
            <input type="checkbox" ${autoClose? 'checked':''} name="" id="CierreAutomaticoModalProductos">
            <div class="slider round"></div>
        </label>
    </div>`;

    if(document.getElementById('InputDeMultiplicar')){
        if(document.getElementById('spoilage')){
            calculateSelectedProductTotal3(product.price, document.getElementById('InputDeMultiplicar').value, product.defaultSpoilage);
        }else{
            calculateSelectedProductTotal(product.price, document.getElementById('InputDeMultiplicar').value);    
        }
    }else{
        calculateSelectedProductTotal2(product.price, document.getElementById('InputDePersonas').value, document.getElementById('InputDeDias').value);
    }
    
    document.getElementById('InputDeMultiplicar')?.addEventListener('keyup', function(e){
        if(e.keyCode == 13){
            document.getElementById('BotonModificarCantidadProductoSeleccionado')?.click();
        }
    });

    setProductPreviewEvents(product.idCategory);
}

function setProductPreviewEvents(idCategory){
    const BotonParaAgregarElProductoSeleccionado = document.getElementById('BotonParaAgregarElProductoSeleccionado');
    const BotonModificarCantidadProductoSeleccionado = document.getElementById('BotonModificarCantidadProductoSeleccionado');
    price = document.querySelector('.PrecioDelProductoSiSeleccionado').innerText.slice(0, -1);

    document.getElementById('CierreAutomaticoModalProductos').addEventListener('change', function(){
        autoClose = this.checked;
    })

    if(document.getElementById('InputDeMultiplicar')){
        document.getElementById('InputDeMultiplicar').focus();
    }else{
        document.getElementById('InputDePersonas').focus();
    }
    
    document.getElementById('InputDeMultiplicar')?.addEventListener('keyup', function(e){
        
        
        if(e.keyCode == 13){
            BotonParaAgregarElProductoSeleccionado?.click();
        }else{
            if(document.getElementById('spoilage')){
                calculateSelectedProductTotal3(price, this.value, document.getElementById('spoilage').innerText);
            }else{
                calculateSelectedProductTotal(price, this.value);
            }
        }
    })

    document.getElementById('InputDePersonas')?.addEventListener('keyup', function(e){
        
        if(e.keyCode == 13){
            document.getElementById('InputDeDias').focus();
        }else{
            calculateSelectedProductTotal2(price, this.value, document.getElementById('InputDeDias').value);
        }
    })
    document.getElementById('InputDeDias')?.addEventListener('keyup', function(e){
        
        if(e.keyCode == 13){
            BotonParaAgregarElProductoSeleccionado?.click();
        }else{
            calculateSelectedProductTotal2(price, document.getElementById('InputDePersonas').value, this.value);
        }
    })


    BotonModificarCantidadProductoSeleccionado?.addEventListener('click', function(){
        if(idCategory < 3){
            quantity = document.getElementById('InputDeMultiplicar').value;

            if(quantity > 0){
                updateProductOnBudget(this.getAttribute('idProduct'), quantity);    
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'La cantidad a agregar debe ser mayor a cero'
                })

                document.getElementById('InputDeMultiplicar').focus();
            }
        }else{

        }
    })


    BotonParaAgregarElProductoSeleccionado?.addEventListener('click', function(){
        if(idCategory < 3){
            quantity = document.getElementById('InputDeMultiplicar').value;
            if(quantity > 0){
                addProductToBudget(this.getAttribute('idProduct'), quantity);    
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'La cantidad a agregar debe ser mayor a cero'
                })

                document.getElementById('InputDeMultiplicar').focus();
            }
        }else{
            quantity = document.getElementById('InputDePersonas').value;
            days = document.getElementById('InputDeDias').value;

            if(quantity > 0){
                if(days > 0){
                    addProductToBudget(this.getAttribute('idProduct'), quantity+'.'+days);    
                }else{
                    Toast.fire({
                        icon: 'error',
                        title: 'La cantidad de días debe ser mayor a cero'
                    })
                    document.getElementById('InputDeDias').focus();
                }
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'La cantidad de personas debe ser mayor a cero'
                })
                document.getElementById('InputDePersonas').focus();
            }
        }  
    })


    document.getElementById('BotonRemoverProductoSeleccionado')?.addEventListener('click', function(){
        removeProduct(this.getAttribute('idProduct'), idCategory);
        console.log(autoClose)
        if(autoClose){
            showAddProductModal(false);
        }else{
            unselectProduct();
        }
        
    })
}

function calculateSelectedProductTotal3(price, quantity, spoilage){
    if(price == '' || isNaN(price)){
        price = 0;
    }
    if(quantity == '' || isNaN(quantity)){
        quantity = 0;
    }
    if(spoilage == '' || isNaN(spoilage)){
        spoilage = 0;
    }

    total = quantity * price * spoilage;
    document.getElementById('PrecioMultiplicado').innerText = `Total: ${total.toFixed(2)}$`;
}

function calculateSelectedProductTotal2(price, quantity, days){
    if(price == '' || isNaN(price)){
        price = 0;
    }
    if(quantity == '' || isNaN(quantity)){
        quantity = 0;
    }
    if(days == '' || isNaN(days)){
        days = 0;
    }

    total = quantity * price * days;
    document.getElementById('PrecioMultiplicado').innerText = `Total: ${total.toFixed(2)}$`;
}

function calculateSelectedProductTotal(price, quantity){
    if(price == '' || isNaN(price)){
        price = 0;
    }
    if(quantity == '' || isNaN(quantity)){
        quantity = 0;
    }
    total = quantity * price;

    document.getElementById('PrecioMultiplicado').innerText = `Total: ${total.toFixed(2)}$`;
}


function setRowsOnListEvents(){
    document.querySelectorAll('.BotonModificarProductoEspecifico').forEach(button => {
        button.addEventListener('click', async function(){
            await showAddProductModal(true);
            await sleep(600);
            document.getElementById(`ProductoEnRowDeBusqueda-${this.getAttribute('idProduct')}`).click();
        })
    });

    document.querySelectorAll('.BotonEliminarProductoEspecifico').forEach(button => {
        button.addEventListener('click', function(){
            removeProduct(this.getAttribute('idProduct'), this.getAttribute('idCategory'));
        })
    });
}


function removeProduct(id, idCategory){
    InputToDeleteProduct = (idCategory == 1? ProductosCotizados: (idCategory == 2? MaquinasCotizados:ManosCotizados));
    EspacioDeRows = (idCategory == 1? MaterialesAgregados: (idCategory == 2? HerramientasAgregados:ManoDeObraAgregados));
    prodXquan_onInput = InputToDeleteProduct.value.split('¿');


    raul = prodXquan_onInput.find( prodXquan => {
        pieces = prodXquan.split('x');
        return pieces[0]==id;
    })

    prodXquan_onInput.splice(prodXquan_onInput.indexOf(raul), 1);
    InputToDeleteProduct.value = prodXquan_onInput.join('¿');

    document.getElementById(`MaterialEnLista-${id}`)?.remove();


    if(EspacioDeRows.innerHTML == ''){
        EspacioDeRows.innerHTML = `<row><span class="TablaVacia">Esta cotización no tiene ${(idCategory == 1? 'materiales': (idCategory == 2? 'maquinaria ni herramientas':'mano de obra'))}</span></row>`;
    }



    productsOnList[id] = 0;

    updateTotalAccount();
}

async function updateProductOnBudget(id, quantity){
    product = await getProduct(id);

    removeProduct(id, product.idCategory);
    addProductToBudget(id, quantity);
     

    if(autoClose){
        showAddProductModal(false);
    }
}

async function addProductToBudget(id, quantity){
    product = await getProduct(id);
    arrayMaterial = [];
    arrayEquipo = [];
    arrayManos = [];


    if(product.idCategory < 3){
        if(product.idCategory < 2){
            if(ProductosCotizados.value != ''){
                arrayMaterial = ProductosCotizados.value.split('¿');
            }
    
            arrayMaterial.push(`${id}x${quantity}`);
            ProductosCotizados.value = arrayMaterial.join('¿');
        }else{
            if(MaquinasCotizados.value != ''){
                arrayEquipo = MaquinasCotizados.value.split('¿');
            }
    
            arrayEquipo.push(`${id}x${quantity}`);
            MaquinasCotizados.value = arrayEquipo.join('¿');
        }
    }else{
        if(ManosCotizados.value != ''){
            arrayManos = ManosCotizados.value.split('¿');
        }

        arrayManos.push(`${id}x${quantity}`);
        ManosCotizados.value = arrayManos.join('¿');
    }

    updateProductsOnList();
    
    switch(product.idCategory){
        case 1:
            await addMaterialOnInterfaceList(id, quantity);
        break;
        case 2:
            await addEquipOnInterfaceList(id, quantity);
        break;
        default:
            pieces = quantity.split('.');
            await addPersonalOnInterfaceList(id, pieces[0], pieces[1]);
    }



    setRowsOnListEvents();

    if(autoClose){
        showAddProductModal(false);
    }else{
        unselectProduct();
    }


    updateTotalAccount();
}

function unselectProduct(){
    PrevisualizacionDeProducto.innerHTML = `
    <div class="ProductoNoSeleccionado">
                        <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                        <span>Seleccione un producto</span>
                    </div>
    `;
}

const MaterialesAgregados = document.getElementById('MaterialesAgregados');
const HerramientasAgregados = document.getElementById('HerramientasAgregados');
const ManoDeObraAgregados = document.getElementById('ManoDeObraAgregados');



async function addMaterialOnInterfaceList(id, quantity){
    product = await getProduct(id);
    total = quantity * product.price;


    if(MaterialesAgregados.innerHTML == `<row><span class="TablaVacia">Esta cotización no tiene materiales</span></row>`){
        MaterialesAgregados.innerHTML = '';
    }

    
    MaterialesAgregados.innerHTML+= `<row id="MaterialEnLista-${product.id}" class="Flex-gap2">
        <div class="test9">
            <i idproduct="${product.id}" title="Modificar este producto." class="fi-rr-pencil BotonModificarProductoEspecifico"></i>
            <i idproduct="${product.id}" idCategory="${product.idCategory}" title="Eliminar este producto." class="fi-rr-trash BotonEliminarProductoEspecifico"></i>
        </div>
        <span class="ColumnaImagen CeldaSinH">
            <img src="../../Imagenes/productos/${(product.img? product.img:'ImagenPredefinida_Productos.png')}" alt="">
        </span>
        <span class="ColumnaID CeldaSinH">${product.id}</span>
        <span class="ColumnaNombre CeldaSinH">${product.name}</span>
        <span class="ColumnaCantidad CeldaSinH">${quantity}</span>
        <span title="${product.unitName}" class="ColumnaUnidad CeldaSinH">${product.unit}</span>
        <span class="ColumnaPrecio CeldaSinH">${product.price}$</span>
        <span class="ColumnaTotal CeldaSinH productOnBudgetTotalPrice" idCategory="${product.idCategory}" price="${total}">${total.toFixed(2)}$</span>
    </row>`;
}


async function addEquipOnInterfaceList(id, quantity){
    product = await getProduct(id);
    total = quantity * product.price * product.defaultSpoilage;


    if(HerramientasAgregados.innerHTML == `<row><span class="TablaVacia">Esta cotización no tiene maquinaria ni herramientas</span></row>`){
        HerramientasAgregados.innerHTML = '';
    }

    
    
    HerramientasAgregados.innerHTML+= `<row id="MaterialEnLista-${product.id}" class="Flex-gap2">
        <div class="test9">
            <i idproduct="${product.id}" title="Modificar este producto." class="fi-rr-pencil BotonModificarProductoEspecifico"></i>
            <i idproduct="${product.id}" idcategory="${product.idCategory}" title="Eliminar este producto." class="fi-rr-trash BotonEliminarProductoEspecifico"></i>
        </div>
        <span class="ColumnaImagen CeldaSinH">
            <img src="../../Imagenes/productos/${(product.img? product.img:'ImagenPredefinida_Productos.png')}" alt="">
        </span>
        <span class="ColumnaID CeldaSinH">${product.id}</span>
        <span class="ColumnaNombre CeldaSinH">${product.name}</span>
        <span class="ColumnaCantidad CeldaSinH">${Number(quantity)}</span>
        <span class="ColumnaUnidad CeldaSinH">${product.defaultSpoilage}</span>
        <span class="ColumnaPrecio CeldaSinH">${product.price}$</span>
        <span class="ColumnaTotal CeldaSinH productOnBudgetTotalPrice" idCategory="${product.idCategory}" price="${total}">${total.toFixed(2)}$</span>
    </row>`;
}

async function addPersonalOnInterfaceList(id, quantityPerson, quantityDays){
    product = await getProduct(id);

    total = quantityPerson * quantityDays * product.price;


    if(ManoDeObraAgregados.innerHTML == `<row><span class="TablaVacia">Esta cotización no tiene mano de obra</span></row>`){
        ManoDeObraAgregados.innerHTML = '';
    }

    ManoDeObraAgregados.innerHTML+= `<row id="MaterialEnLista-0000007" class="Flex-gap2">
        <div class="test9">
            <i idproduct="0000007" title="Modificar este producto." class="fi-rr-pencil BotonModificarProductoEspecifico"></i>
            <i idproduct="0000007" idcategory="3" title="Eliminar este producto." class="fi-rr-trash BotonEliminarProductoEspecifico"></i>
        </div>
        <span class="ColumnaImagen CeldaSinH">
            <img src="../../Imagenes/productos/ImagenPredefinida_Productos.png" alt="">
        </span>
        <span class="ColumnaID CeldaSinH">${product.id}</span>
        <span class="ColumnaNombre CeldaSinH">${product.name}</span>
        
        <span class="ColumnaCantidad CeldaSinH" title="${quantityPerson} ${product.unitName}">${quantityPerson}</span>
        <span style="align-items: center;" class="ColumnaUnidad CeldaSinH">x ${quantityDays}</span>
        <span class="ColumnaPrecio CeldaSinH">${product.price.toFixed(2)}$</span>
        <span class="ColumnaTotal CeldaSinH productOnBudgetTotalPrice" idCategory="${product.idCategory}" price="${total}">${total.toFixed(2)}$</span>
    </row>`;
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

async function trySearchClient(value){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getResultsClientsSearch&value=${value}`);

        if(response.status == 200){
            petition = await response.json();


            if(petition.status == 200){
                if(petition.result.length > 0){
                    document.querySelector('#addClientModal .EspacioDeRows').innerHTML = '';

                    petition.result.forEach(element => {
                        document.querySelector('#addClientModal .EspacioDeRows').innerHTML+= `<div class="Flex-gap2 HoverVino">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Clientes/${(element.ULRImagen)? element.ULRImagen:'ImagenPredefinida_Clientes.png'}" alt="">
                            </span>
                            <span class="Celda ColumnaRIF">${element.tipoDeDocumento} - ${zerofill(element.rif, 9)}</span>
                            <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">${element.nombre}</span>
                            <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                                <button title="Seleccionar cliente" class="BontonSeleccionar" idClient="${element.rif}">
                                    <i class="fi-rr-user-add"></i>
                                </button>
                            </div>
                        </div>`;
                    });

                    document.querySelectorAll('.BontonSeleccionar').forEach( button => {
                        button.addEventListener('click', function(){
                            showAddClientModal(false);
                            loadClient(button.getAttribute('idClient'));
                        })
                    })
                }else{
                    document.querySelector('#addClientModal .EspacioDeRows').innerHTML = 
                    `<div class="Flex-gap2 HoverVino TablaDeClientesVacia">
                        <span>No hay clientes para mostrar...</span>
                    </div>`;
                }
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.result
                })
            }
        }
    }catch(error){
        console.log(error);
    }
}


async function loadClient(id){
    idClientInput.value = id;
    data = await getClientData(id);

    document.getElementById('TipoDeRifCliente').value = data.docType;
    document.getElementById('IdCliente').value = zerofill(data.id, 9);
    document.getElementById('REAL_IdCliente').value = data.id;
    document.getElementById('NombreCliente').value = data.name;
    document.getElementById('TelefonoCliente').value = data.phone;
    document.getElementById('CorreoCliente').value = data.email
    document.getElementById('DireccionCliente').value = data.address;
    
    EspacioDeTarjetaDeCliente.style = 'height: 270px;';
    EspacioDeTarjetaDeCliente.querySelector('.NoCliente').classList.add('Subir');
    EspacioDeTarjetaDeCliente.querySelector('.SiCliente').classList.add('Subir');
}


function removeClient(){
    idClientInput.value = '';
    EspacioDeTarjetaDeCliente.style = 'height: 190px;';
    EspacioDeTarjetaDeCliente.querySelector('.NoCliente').classList.remove('Subir');
    EspacioDeTarjetaDeCliente.querySelector('.SiCliente').classList.remove('Subir');
}


async function getClientData(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getClient&id=${id}`);
        
        if(response.status == 200){
            petition = await response.json();


            if(petition.status == 200){
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










function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
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


function zeroFormat(value){
    if(value < 10 && value > -10){
        return `0${value}`;
    }else{
        return value;
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
                return false;
            }
        }
    }
}




function updateTotalAccount(){
    const PrecioSubTotalMaterial = document.getElementById('PrecioSubTotalMaterial');
    const PrecioSubTotalMaquinaria = document.getElementById('PrecioSubTotalMaquinaria');
    const PrecioSubTotalMano = document.getElementById('PrecioSubTotalMano');
    const PrecioAsociadoAlSalario = document.getElementById('PrecioAsociadoAlSalario');
    const PrecioGeneral = document.getElementById('PrecioGeneral');
    const PrecioUtilidades = document.getElementById('PrecioUtilidades');
    const PrecioSubTotal = document.getElementById('PrecioSubTotal');
    const PrecioIVA = document.getElementById('PrecioIVA');
    const PrecioTotal = document.getElementById('PrecioTotal');
    
    const InputCASalario = document.getElementById('InputCASalario');
    const InputUtilidades = document.getElementById('Utilidades');
    const InputIVA = document.getElementById('InputIVA');

    subTotal_material = 0;
    subTotal_equip = 0;
    subTotal_personal = 0;
    subTotal_food = 0;

    percent_CAS = 0;
    percent_Util = 0;
    percent_IVA = 0;
    if(InputCASalario.value != ''){
        percent_CAS = Number(InputCASalario.value);
    }
    if(InputCASalario.value != ''){
        percent_Util = Number(InputUtilidades.value);
    }
    if(InputCASalario.value != ''){
        percent_IVA = Number(InputIVA.value);
    }
    
    
    document.querySelectorAll('.productOnBudgetTotalPrice').forEach( celda => {
        
        switch(celda.getAttribute('idCategory')){
            case '1':
                subTotal_material+= Number(celda.getAttribute('price'));
            break;
            case '2':
                subTotal_equip+= Number(celda.getAttribute('price'));
            break;
            case '3':
                subTotal_personal+= Number(celda.getAttribute('price'));
            break;
            case '4':
                subTotal_food+= Number(celda.getAttribute('price'));
            break;
        }
    })

    

    CAS = subTotal_personal * percent_CAS / 100;
    CP = subTotal_material + subTotal_equip + subTotal_personal + subTotal_food + CAS;
    Uti = CP * percent_Util / 100;
    ST = CP + Uti;
    IVA = ST * percent_IVA / 100;
    TOTAL = ST + IVA;



    PrecioSubTotalMaterial.innerText = subTotal_material.toFixed(2);
    PrecioSubTotalMaquinaria.innerText = subTotal_equip.toFixed(2);
    PrecioSubTotalMano.innerText = (subTotal_personal + subTotal_food).toFixed(2);
    PrecioAsociadoAlSalario.innerText = CAS.toFixed(2);
    PrecioGeneral.innerText = CP.toFixed(2);
    PrecioUtilidades.innerText = Uti.toFixed(2);
    PrecioSubTotal.innerText = ST.toFixed(2);
    PrecioIVA.innerText = IVA.toFixed(2);
    PrecioTotal.innerText = TOTAL.toFixed(2);
}


function zerofill(string, max){
    for (let index = 0; max > string.length; index++) {
        string = '0'+string;
    }
    return string;
}