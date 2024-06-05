let FormularioBuscador = document.getElementById("FormularioBuscador");
let SelectorDeOrden = document.getElementById("SelectorDeOrden");

SelectorDeOrden.addEventListener("change", ()=>{
    FormularioBuscador.submit();
})
