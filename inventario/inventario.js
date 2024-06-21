let ModalSeleccionDeEntrada = document.getElementById('ModalSeleccionDeEntrada');
let CuerpoDeVentanaDeEntrada = document.getElementById('CuerpoDeVentanaDeEntrada');
let BotonCerrarVentanaDeEntrada = document.getElementById('BotonCerrarVentanaDeEntrada');
let BotonAgregarEntrada = document.getElementById('BotonAgregarEntrada');


const loadingResults = document.getElementById('loadingResults');
//VISIBILIDAD DEL MODAL DE ENTRADA
window.addEventListener('scroll', e=>{
    if(checkvisible(document.getElementById('loadingResults')) && loadingResults.innerText=='Buscando más resultados'){
        loadingResults.innerHTML = `<div><i class="fi fi-rr-loading"></i></div>`;
        loadingResults.firstChild.classList.add('rotating')
        getMoreResult(Number(document.getElementById('step').value) + 1, document.getElementById('searchInput').getAttribute('currentValue'), document.getElementById('SelectEstado').getAttribute('currentValue'))
    }
})
CuerpoDeVentanaDeEntrada.addEventListener('click', (e) => {
    e.stopPropagation();
})
ModalSeleccionDeEntrada.addEventListener('click', () => {
    BotonCerrarVentanaDeEntrada.click();
})
BotonCerrarVentanaDeEntrada.addEventListener('click', () => {
    MostrarModalDeEntrada(false);
})
BotonAgregarEntrada.addEventListener('click', () => {
    MostrarModalDeEntrada(true);
})

function getMoreResult(step, search, status){    
    fetch(`http://${ipserver}/CleoInventory/API/publicFunctions.php?method=getInventary&step=${step}&status=${status}&search=${search}`)
    .then(res => res.json())
    .then(petition => {
        if(petition.status == 200){
            
            if(petition.result.isNextStepPossible){
                document.getElementById('step').value = Number(document.getElementById('step').value) + 1;
                loadingResults.innerText = 'Buscando más resultados';
            }else{
                loadingResults.remove()
            }


            
            if(petition.result.result.length > 0){
                const CuerpoDeLaTabla = document.querySelector('.CuerpoDeLaTabla');
                
                petition.result.result.forEach(element => {
                    CuerpoDeLaTabla.innerHTML+= `<row>
                        <celda class="ColumnaImagen">
                            <img src="../Imagenes/Productos/${element.img? element.img:'ImagenPredefinida_Productos.png'}" alt="">
                        </celda>
                        <celda class="ColumnaID">${element.id}</celda>
                        <celda class="ColumnaNombre">${element.name}</celda>
                        <celda class="ColumnaExistencia">${(element.idState==3? '<span title="Este producto se encuentra agotado." style="color: rgb(236, 49, 49);" class="fi-sr-comment-exclamation"></span>':(element.idState==2? '<span title="Este producto se encuentra bajo el nivel de alerta establecido ('+element.alertLevel+' '+element.unit+')" style="color: #FEA82F;" class="fi-sr-comment-exclamation"></span>':''))}x ${element.existence}</celda>
                        <celda class="ColumnaDetalles">
                            <a class="hovershadow" href="../Productos/Producto/?id=${element.id}">Ver más</a>
                        </celda>
                    </row>`;
                });
            }
        }
    })
}

async function MostrarModalDeEntrada(valor){
    
    if(valor){
        ModalSeleccionDeEntrada.style = "display: flex;";
        await EsperardS(2);
        CuerpoDeVentanaDeEntrada.className ="VentanaFlotante";
    }else{
        CuerpoDeVentanaDeEntrada.className ="VentanaFlotante OcultarModal";
        await EsperardS(3);
        ModalSeleccionDeEntrada.style = "";
    }
}




let BotonRealizarBusqueda = document.getElementById('BotonRealizarBusqueda');
let SelectEstado = document.getElementById('SelectEstado');

SelectEstado.addEventListener('change', () => {
    BotonRealizarBusqueda.click();
})





function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function EsperardS(Tiempo) {
    for (let i = 0; i < Tiempo; i++) {
        await sleep(i * 100);
    }
}






function posY(elm) {
    var test = elm, top = 0;

    while(!!test && test.tagName.toLowerCase() !== "body") {
        top += test.offsetTop;
        test = test.offsetParent;
    }

    return top;
}

function viewPortHeight() {
    var de = document.documentElement;

    if(!!window.innerWidth)
    { return window.innerHeight; }
    else if( de && !isNaN(de.clientHeight) )
    { return de.clientHeight; }
    
    return 0;
}

function scrollY() {
    if( window.pageYOffset ) { return window.pageYOffset; }
    return Math.max(document.documentElement.scrollTop, document.body.scrollTop);
}

function checkvisible( elm ) {
    var vpH = viewPortHeight(), // Viewport Height
        st = scrollY(), // Scroll Top
        y = posY(elm);
    
    return (y <= (vpH + st));
}