let FormularioBuscador = document.getElementById("FormularioBuscador");
let SelectorDeEstado = document.getElementById("SelectorDeEstado");
let SelectorDeOrden = document.getElementById("SelectorDeOrden");

SelectorDeOrden.addEventListener("change", ()=>{
    FormularioBuscador.submit();
})

