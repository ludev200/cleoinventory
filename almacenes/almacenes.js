let SelectEstado = document.getElementById('SelectEstado');
let BotonRealizarBusqueda = document.getElementById('BotonRealizarBusqueda');

SelectEstado.addEventListener('change', () => {
    BotonRealizarBusqueda.click();
})

let TopeDelListado = document.getElementById('TopeDelListado');
let FondoDeLaBusqueda = document.getElementById('FondoDeLaBusqueda');

window.addEventListener('load', () => {
    EspecificacionesDeULR = window.location.search.split('&');

    EspecificacionesDeULR.forEach(element => {
        
        pedazos = element.split('=');
        if(pedazos[0] == 'paginadebusqueda'){
            if(pedazos[1] > 1){
                FondoDeLaBusqueda.scrollIntoView();
            }
            
        }
        
        if(pedazos[0] == 'descripcion' || pedazos[0] == '?descripcion'){
            if(pedazos[1]){
                FondoDeLaBusqueda.scrollIntoView();
            }
        }

        if(pedazos[0] == 'estado'){
            if(pedazos[1] != '30'){
                FondoDeLaBusqueda.scrollIntoView();
            }else{
                
            }
        }
        
    });
})

