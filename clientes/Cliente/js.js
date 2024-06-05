const SelectEstado = document.getElementById('SelectEstado');
const FormularioBuscador = document.getElementById('FormularioBuscador');


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

SelectEstado.addEventListener('change', function(){
    FormularioBuscador.submit();
})

window.addEventListener('load', function(){
    this.document.getElementById('BotonEliminar').addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar este cliente?",
            html: 'Esto eliminará al cliente pero conservará los registros de sus acciones realizadas.<br><br><b>¡Atención!</b> esta acción no se puede deshacer.',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed){
                deleteCustomer(document.getElementById('idCliente').getAttribute('idCliente'));
            }
        });
    })
})

async function deleteCustomer(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=3&idEntity=${id}&method=delete`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: 'success',
                    title: 'Eliminado correctamente'
                })

                await sleep(2000);
                window.location.href = `http://${ipserver}/CleoInventory/Clientes/?descripcion=&orden=nombre`;
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