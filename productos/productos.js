

let Formulario = document.getElementById("buscador");
let SelectEstado = document.getElementById("SelectorDeEstado");
let SelectCategoria = document.getElementById("SelectorDeCategoria");
let SelectOrden = document.getElementById("SelectorDeOrden");


SelectEstado.addEventListener("change",()=>{
    Formulario.submit();
})

SelectCategoria.addEventListener("change",()=>{
    Formulario.submit();
})

SelectOrden.addEventListener("change",()=>{
    Formulario.submit();
})

