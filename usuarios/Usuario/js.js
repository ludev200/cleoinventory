const SW_container = document.getElementById('SWAlert');

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
    this.document.getElementById('BotonEliminar')?.addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar inhabilitar este usuario?",
            html: 'Esta acción prohibirá el acceso al sistema a este usuario',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, inhabilitar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed){
                disableUser(document.getElementById('username').innerText);
            }
        });
    })


    this.document.getElementById('BotonHabilitar')?.addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas habilitar este usuario?",
            html: 'Esta acción permitirá el acceso al sistema a este usuario',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, habilitar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if(result.isConfirmed){
                enableUser(document.getElementById('username').innerText);
            }
        });
    })
})


async function enableUser(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=9&idEntity=${id}&method=enableUser`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: 'success',
                    title: 'Se ha habilitado al usuario correctamente'
                });

                await sleep(2500);
                location.reload();
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                });
            }
        }
    }catch(error){
        console.log(error);
    }
}

async function disableUser(id){
    try{
        console.log(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=9&idEntity=${id}&method=disableUser`)
        response = await fetch(`http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=9&idEntity=${id}&method=disableUser`)

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: 'success',
                    title: 'Se ha inhabilitado al usuario correctamente'
                });

                await sleep(2500);
                window.location.href = `http://${ipserver}/CleoInventory/Usuarios`;
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                });
            }
        }
    }catch(error){
        console.log(error);
    }
}



function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}