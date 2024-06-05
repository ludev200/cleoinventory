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


window.addEventListener('load', function(){
    this.document.getElementById('BotonEliminar').addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar este producto?",
            html: 'La entidad será eliminada pero se conservará un registro de las acciones realizadas. <br><br><b>¡Atención!</b> esta acción no se puede deshacer.',
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

async function deleteEntity(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=5&idEntity=${id}&method=delete`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: 'success',
                    title: 'Se ha eliminado el producto #'+id
                })
                await sleep(2000);
                window.location.href = `http://${ipserver}/CleoInventory/Productos/?descripcion=&estado=0&categoria=0&orden=nombre`;
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

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}