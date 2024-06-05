//MODAL
let CheckBoxModal = document.getElementById("VisibilidadModal");
let VentanaDeErrores = document.getElementById("VentanaDeErrores");

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


CheckBoxModal.addEventListener("click", () => {
    if(CheckBoxModal.checked==false){
        VentanaDeErrores.hidden = true;
    }
})

//COMPROBAR CHECKBOXES LUEGO DE CARGAR
window.addEventListener('load', function() {
    if(CheckBoxModal.checked==false){
        VentanaDeErrores.hidden = true;
    }

    setModalEvents();
    setResetPasswordEvents();
});

let searchUser = document.getElementById('searchUser');
let searchUser_input = document.getElementById('searchUser_input');
let checkAnswers_button = document.getElementById('checkAnswers');
let savePassword_button = document.getElementById('savePassword');
let newPassword_input = document.getElementById('newPassword_input');
let checkPassword_input = document.getElementById('checkPassword_input');

async function trycheckAnswers(user, answer1, answer2, answer3){
    checkAnswers_button.innerHTML = '<i class="fi-rr-loading"></i>';
    
    try{
        response = await fetch("http://"+ipserver+"/CleoInventory/API/publicfunctions.php?method=comprobarRespuestas&usuario="+user+`&respuesta1=${answer1}&respuesta2=${answer2}&respuesta3=${answer3}`);

        if(response.status == 200){
            petition = await response.json();

            

            if(petition.status == 200){
                
                showStep3Component(true);
            }else{
                Toast.fire({
                    icon: "error",
                    title: petition.result
                });
                showStep3Component(false);
            }
        }else{

        }
    }catch(error){
        console.log(error)
    }





    checkAnswers_button.innerHTML = 'Comprobar respuestas';
}

async function trySaveNewPassword(user, answer1, answer2, answer3, password){
    savePassword_button.innerHTML = '<i class="fi-rr-loading"></i>';

    try{
        response = await fetch("http://"+ipserver+"/CleoInventory/API/publicfunctions.php?method=restablecerContrasenia&usuario="+user+`&respuesta1=${answer1}&respuesta2=${answer2}&respuesta3=${answer3}&contrasenia=${password}`);

        if(response.status == 200){
            petition = await response.json()   ;

            
            if(petition.status == 200){

                Toast.fire({
                    icon: "success",
                    title: 'Se ha restablecido tu contraseña'
                });
                await sleep(700);
                window.location = 'index.php';
            }else{
                Toast.fire({
                    icon: "error",
                    title: petition.result
                });
            }
        }
    }catch(error){
        console.log(error);
    }
    
    savePassword_button.innerHTML = 'Guardar y Entrar';
}

function setResetPasswordEvents(){
    savePassword.addEventListener('click', function(){
        if(savePassword.innerHTML == 'Guardar y Entrar'){
            if(newPassword_input.value == checkPassword_input.value){
                user = searchUser_input.value;
                answer1 = document.querySelector('.stepContent.step2component .question:nth-child(1) input').value;
                answer2 = document.querySelector('.stepContent.step2component .question:nth-child(2) input').value;
                answer3 = document.querySelector('.stepContent.step2component .question:nth-child(3) input').value;
                password = document.getElementById('newPassword_input').value;

                trySaveNewPassword(user, answer1, answer2, answer3, password);
            }else{
                Toast.fire({
                    icon: "error",
                    title: "La comprobación de la contraseña no coincide con la nueva contraseña"
                });
                checkPassword_input.focus();
            }
            
        }
    })


    checkAnswers_button.addEventListener('click', function(){
        if(checkAnswers_button.innerHTML == 'Comprobar respuestas'){
            user = searchUser_input.value;
            answer1 = document.querySelector('.stepContent.step2component .question:nth-child(1) input').value;
            answer2 = document.querySelector('.stepContent.step2component .question:nth-child(2) input').value;
            answer3 = document.querySelector('.stepContent.step2component .question:nth-child(3) input').value;

            trycheckAnswers(user, answer1, answer2, answer3);
        }
    })


    document.getElementById('resetPassword').addEventListener('click', function(){
        showResetPassword_modal(true);
    })

    searchUser.addEventListener('click', function(){
        if(searchUser.innerHTML == '<i class="fi-rr-search"></i>'){
            tryGetSecurityQuestions(searchUser_input.value);
        }
    })
    searchUser_input.addEventListener('keyup', function(e){
        if(e.keyCode == 13){
            searchUser.click();
        }
    })


    
}

function showStep2Component(valor){
    if(valor){
        document.querySelectorAll('.step2component').forEach( coso => {
            coso.style="";
        })
        document.querySelector('.stepContent.step2component .question:nth-child(1) input').focus();
    }else{
        document.querySelectorAll('.step2component').forEach( coso => {
            coso.style="display: none";
        })
    }
}

function showStep3Component(valor){
    if(valor){
        document.querySelectorAll('.step3component').forEach( coso => {
            coso.style="";
        })
        document.querySelectorAll('.stepContent.step3component input').forEach( input => {
            input.value = '';
        })
        document.querySelector('.stepContent.step3component .question:nth-child(1) input').focus();
    }else{
        document.querySelectorAll('.step3component').forEach( coso => {
            coso.style="display: none";
        })
    }
}


function showQuestions(questions){
    counter = 0;
    document.querySelectorAll('.stepContent.step2component .question small').forEach( small => {
        small.innerText = `¿${questions[counter]}?`;
        counter++;
    })
    document.querySelectorAll('.stepContent.step2component .question input').forEach( input => {
        input.value = '';
    })
}



async function tryGetSecurityQuestions(value){
    searchUser.innerHTML = '<i class="fi-rr-loading"></i>';
    try{
        let respuesta = await fetch("http://"+ipserver+"/CleoInventory/API/publicfunctions.php?method=obtenerPreguntasDeSeguridad&usuario="+value);

        if(respuesta.status == 200){
            petition = await respuesta.json();

            
            if(petition.status == 200){
                showQuestions(petition.result);
                showStep2Component(true);
                showStep3Component(false);
            }else{
                Toast.fire({
                    icon: "error",
                    title: petition.result
                });
            }
        }


        searchUser.innerHTML = '<i class="fi-rr-search"></i>';
    }catch(error){
        console.log(error)
    }
}

function setModalEvents(){
    document.querySelector('.resetPassword_modal').addEventListener('click', function(){
        showResetPassword_modal(false);
    })
    document.querySelector('.closeModal').addEventListener('click', function(){
        showResetPassword_modal(false);
    })

    document.querySelector('.modalWindowContainer').addEventListener('click', function(e){
        e.stopImmediatePropagation();
    })
    
}

async function showResetPassword_modal(value){
    if(value){
        showStep2Component(false);
        showStep3Component(false);

        document.querySelector('.resetPassword_modal').style = 'display: flex;';
        await sleep(200);
        document.querySelector('.modalWindowContainer').classList.remove('OcultarModal');
        searchUser_input.focus();
    }else{
        document.querySelector('.modalWindowContainer').classList.add('OcultarModal');
        await sleep(400);
        document.querySelector('.resetPassword_modal').style = '';
    }
}


function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}