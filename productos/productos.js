

let Formulario = document.getElementById("buscador");
let SelectEstado = document.getElementById("SelectorDeEstado");
let SelectCategoria = document.getElementById("SelectorDeCategoria");
let SelectOrden = document.getElementById("SelectorDeOrden");
const inputBuscador = document.querySelector('.inputBuscador');


SelectEstado.addEventListener("change",()=>{
    Formulario.submit();
})

SelectCategoria.addEventListener("change",()=>{
    Formulario.submit();
})

SelectOrden.addEventListener("change",()=>{
    Formulario.submit();
})



const loadingResults = document.getElementById('loadingResults');

window.addEventListener('scroll', e=>{
    if(checkvisible(document.getElementById('loadingResults')) && loadingResults.innerText=='Buscando más resultados'){
        loadingResults.innerHTML = `<div><i class="fi fi-rr-loading"></i></div>`;
        loadingResults.firstChild.classList.add('rotating')
        
        getMoreResult(Number(document.getElementById('step').value) + 1, inputBuscador.getAttribute('currentValue'), SelectEstado.getAttribute('currentValue'), SelectCategoria.getAttribute('currentValue'), SelectOrden.getAttribute('currentValue'))
    }
})




function getMoreResult(step, search, status, category, order){
    fetch(`http://${ipserver}/CleoInventory/API/publicFunctions.php?method=getProductsList&step=${step}&order=${order}&category=${category}&status=${status}&search=${search}`)
    .then(res => res.json())
    .then(petition => {
        console.log(petition)
        if(petition.status == 200){
            
            if(petition.result.isNextStepPossible){
                document.getElementById('step').value = Number(document.getElementById('step').value) + 1;
                loadingResults.innerText = 'Buscando más resultados';
            }else{
                loadingResults.remove()
            }


            if(petition.result.result.length > 0){
                const CajaResultados = document.getElementById('CajaResultados');

                petition.result.result.forEach(element => {
                    CajaResultados.innerHTML+= `<a href="Producto?id=${element.id}" class="TarjetaProducto">
                    <div class="CajaDeImagen">
                        <img src="../Imagenes/Productos/${element.img? element.img:'ImagenPredefinida_Productos.png'}" alt="">
                    </div>
                    <div class="CajaDeCaractaresiticas">
                        <b class="titulo">${element.name}</b>
                        <div></div>
                        <p class="tipo">${element.idCategory==4? 'Comida':(element.idCategory==3? 'Mano de obra':(element.idCategory==2? 'Equipo':'Material'))}</p>
                        <p class="descripcion">${element.desc? element.desc:''}</p>
                    </div>
                    <b class="precioFlotador">${element.price}$</b>
                    <div class="TapaDeTextoSobrante"></div>
                </a>`;
                })
            }
        }
    })
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