let Modal_Rechazar = document.getElementById('Modal_Rechazar');
let VentanitaRechazar = document.getElementById('VentanitaRechazar');
let Span_VolverDeRechazar = document.getElementById('Span_VolverDeRechazar');

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

Span_VolverDeRechazar.addEventListener('click', () => {
    MostrarModalRechazar(false);
})
Modal_Rechazar.addEventListener('click', () => {
    MostrarModalRechazar(false);
})

VentanitaRechazar.addEventListener('click', (e) => {
    e.stopPropagation();
})

async function MostrarModalRechazar(valor){
    if(valor){
        Modal_Rechazar.style = "display: flex;";
        await  EsperarMS(50);
        VentanitaRechazar.className = "VentanaDeRechazar";
    }else{
        VentanitaRechazar.className = "VentanaDeRechazar ModalArriba";
        await  EsperarMS(100);
        Modal_Rechazar.style = "";
    }
}

const ModalGenerarPDF = document.getElementById('ModalGenerarPDF');

window.addEventListener('load', () => {
    document.getElementById('BotonEliminar')?.addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar esta orden de compra?",
            html: '<b>¡Atención!</b> esta acción no se puede deshacer.',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed){
                deleteEntity(document.getElementById('idEntity').innerText);
            }
        });
    })


    document.querySelectorAll('.Agrupacion_BotonMostrarModalRechazar').forEach( (element) => {
        console.log('lol')
        element.addEventListener('click', () => {
            MostrarModalRechazar(true);
        })
    })

    document.getElementById('BotonAbrirMenuDeCrearReporte').addEventListener('click', function(){
        //mostrarConversionDeMoneda(false);
        //mostrarModalPDF(true);
        generarPDF(1, 1, document.getElementById('idEntity').innerText);
    })
    document.getElementById('ModalGenerarPDF').addEventListener('click', function(){
        mostrarModalPDF(false);
    })
    ModalGenerarPDF.querySelector('.Ventanita').addEventListener('click', function(e){
        e.stopPropagation();
    })
    document.getElementById('BotonCerrar').addEventListener('click', function(){
        mostrarModalPDF(false);
    })


    document.getElementById('BotonMonedaNacional').addEventListener('click', function(){
        mostrarConversionDeMoneda(true);
    })
    document.getElementById('BotonVolverASeleccion').addEventListener('click', function(){
        mostrarConversionDeMoneda(false);
    })
    

    document.getElementById('InputTasa').addEventListener('keyup', function(e){
        
        valorFinal = document.getElementById('InputTasa').value;
        if(!isNaN(e.key)){
            valorFinal+=e.key;
        }

        
        if(valorFinal == '' || isNaN(valorFinal)){
            document.getElementById('BotonGenerarReporteConTasaEspecifica').classList.remove('botonjover');
        }else{
            document.getElementById('BotonGenerarReporteConTasaEspecifica').classList.add('botonjover');
        }
    })



    document.getElementById('BotonMonedaInternacional').addEventListener('click', function(){
        generarPDF(1, 1, document.getElementById('idEntity').innerText);
    })
    document.getElementById('BotonGenerarReporteConTasaEspecifica').addEventListener('click', function(){
        if(document.getElementById('BotonGenerarReporteConTasaEspecifica').classList.contains('botonjover')){
            generarPDF(2, document.getElementById('InputTasa').value, document.getElementById('idEntity').innerText);
        }
    })
})




async function deleteEntity(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=6&idEntity=${id}&method=delete`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: 'success',
                    title: 'Se ha eliminado la orden de compra #'+id
                })
                
                await sleep(2500);
                window.location.href = `http://${ipserver}/CleoInventory/Compras`;
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                })
            }
        }
    }catch(error){
        console.log(error);
    }
}

function mostrarConversionDeMoneda(valor){
    document.getElementById('InputMostrarSeleccionDeTasa').checked = valor;
    if(valor){
        document.getElementById('BotonMonedaNacional').style = "transform: translateX(-30px);cursor: default;";
        document.querySelector('.OtroDivPorqueSoyIdiota .CajaDeOpciones').classList.add('MostrarSeleccionDeTasa');
    }else{
        document.getElementById('BotonMonedaNacional').style = "";
        document.querySelector('.OtroDivPorqueSoyIdiota .CajaDeOpciones').classList.remove('MostrarSeleccionDeTasa');
        document.getElementById('InputTasa').value = '';
    }
}

async function mostrarModalPDF(valor){
    if(valor){
        ModalGenerarPDF.style = "display: flex;";
        await sleep(100);
        ModalGenerarPDF.querySelector('.Ventanita').classList.add('BajarVentanita');
    }else{
        ModalGenerarPDF.querySelector('.Ventanita').classList.remove('BajarVentanita');
        await sleep(300);
        ModalGenerarPDF.style = "";
    }
}




function generarPDF(moneda, tasa, id){
    url = `http://${ipserver}/CleoInventory/Reportes/CompraPDF.php?moneda=${moneda}&tasa=${tasa}&id=${id}`;
    window.open(url, '_blank');
}







function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}





//FUNCIONES PARA DORMIR Y OTRAS COSAS
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
async function EsperarMS(Milisegundos) {
    for (let i = 0; i < 3; i++) {
        await sleep(i * Milisegundos);
    }
}


function priceDollarFilter(element, e){
    
    
    if(isNaN(e.key)){
        if(e.keyCode != '46'){
            return false;
        }else{
            console.log(element.value)
            if(element.value.includes('.')){
                return false;
            }
        }
    }
    
    
    
    
    
}
