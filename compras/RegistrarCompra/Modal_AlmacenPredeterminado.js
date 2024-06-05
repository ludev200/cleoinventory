let BotonTexto_ElegirPredeterminado = document.getElementById('BotonTexto_ElegirPredeterminado');
let VentadaModal_ElegirPredeterminado = document.getElementById('VentadaModal_ElegirPredeterminado');
let Modal_ElegirAlmPred = document.getElementById('Modal_ElegirAlmPred');
let BotonCerrarVentana_ElegirPredeterminado = document.getElementById('BotonCerrarVentana_ElegirPredeterminado');

var VisibilidadDelModal_ElegirPredeterminado = false;
var CierreAutomatico_ElegirPredeterminado = true;

Modal_ElegirAlmPred.addEventListener('click', function() {
    MostrarModal_ElegirAlmPred(false);
})
VentadaModal_ElegirPredeterminado.addEventListener('click', (e) => {
    e.stopPropagation();
})
BotonCerrarVentana_ElegirPredeterminado.addEventListener('click', function(){
    MostrarModal_ElegirAlmPred(false);
})
BotonTexto_ElegirPredeterminado.addEventListener('click', function(){
    MostrarModal_ElegirAlmPred(true);
})



async function MostrarModal_ElegirAlmPred(valor){
    if(valor){
        Modal_ElegirAlmPred.style = "display: flex;";
        await EsperarMS(50);
        VentadaModal_ElegirPredeterminado.className = "VentanaFlotante";
    }else{
        VentadaModal_ElegirPredeterminado.className = "VentanaFlotante OcultarModal";
        await EsperarMS(100);
        Modal_ElegirAlmPred.style = "";
    }
}

function PrepararAlmacenPredeterminado(){
    Boton_UsarAlmPred = document.querySelector('.Boton_UsarAlmacenPredeterminado');

    Boton_UsarAlmPred.addEventListener('click', function(){
        pedazos = Boton_UsarAlmPred.id.split('=');
        ArrayTemp = [];

        AlmacenEncontrado = TodosLosAlmacenes.find( element => {
            return element.id == pedazos[1];
        })

            
        for (let index = 0; index < ProductosCargadosAlPaso2.length; index++) {
            
            ArrayTemp.push(ProductosCargadosAlPaso2[index] + 'x' + CantidadesCargadosAlPaso2[index]);
            
        }
        MostrarModal_ElegirAlmPred(false);
        
        AlmacenajeEnFormato.value = pedazos[1] + ":" + ArrayTemp.join(',');
        
        ContenedorDeRowsDeProductosYaAlmacenados.innerHTML = "";
        for (let index = 0; index < ProductosCargadosAlPaso2.length; index++) {
            ActualizoArrayDeCantidades(ProductosCargadosAlPaso2[index]);

            
            ProductoEncontrado = TodosLosProductos.find( element => {
                return element.id == ProductosCargadosAlPaso2[index];
            });
            
            AgregarRowALaTablaEnInterfaz(AlmacenEncontrado, ProductoEncontrado, CantidadesCargadosAlPaso2[index])
        }      
    })
}

window.addEventListener('load', function(){
    
})