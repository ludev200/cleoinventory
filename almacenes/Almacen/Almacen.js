let BotonBuscar = document.getElementById('BotonBuscar');
let SelectDeColumna = document.getElementById('SelectDeColumna');
let SelectDeOrden = document.getElementById('SelectDeOrden');
let TopeDeTablaDeProductos = document.getElementById('TopeDeTablaDeProductos');

SelectDeColumna.addEventListener('change', () => {
    BotonBuscar.click();
})
SelectDeOrden.addEventListener('change', () => {
    BotonBuscar.click();
})

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


document.addEventListener('DOMContentLoaded', () => {
    PalaULR();
    document.getElementById('deleteEntityButton').addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar este almacén?",
            html: 'Esto eliminará el almacén pero conservará los registros de las acciones realizadas en este.<br><br><b>¡Atención!</b> esta acción no se puede deshacer.',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed){
                deleteEntity(document.getElementById('IDDeEntidad').innerText);
            }
        });
    })
})

function PalaULR(){
    

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    
    if(location.hash && (urlParams.has('Columna') || urlParams.has('Orden'))) {
        console.log('mi loco quitame la almohada: ' + window.location.search)
        window.location.href = window.location.search;
    }


    if(!location.hash && (urlParams.has('Columna') || urlParams.has('Orden'))) {
        TopeDeTablaDeProductos.scrollIntoView();
    }
}


async function deleteEntity(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=1&idEntity=${id}&method=delete`);

        if(response.status == 200){
            petition = await response.json();
            console.log(petition)
            if(petition.status == 200){
                Toast.fire({
                    icon: 'success',
                    title: 'Se eliminado el almacén correctamente'
                })

                await sleep(2000);
                window.location.href = `http://${ipserver}/CleoInventory/Almacenes`;
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
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