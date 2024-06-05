window.addEventListener('load', function(){
    CrearModalDeErrores();

    CrearCajasDePreguntasDeRecuperacion();
    ProfileSelectorEvent();

    var modulosdefinidos = false;
    document.querySelectorAll('.checkboxmodulo').forEach( chbx => {
        if(chbx.checked){
            modulosdefinidos = true;
        }
    })
    if(!modulosdefinidos){
        document.querySelectorAll('.perfil'+document.getElementById('profileselector').value).forEach( chbx => {
            chbx.checked = true;
        })
    }
    
    changeInputIDForDocType();
    this.document.getElementById('tipoDeDocumento').addEventListener('change', function(){
        changeInputIDForDocType();
    })
})

function changeInputIDForDocType(){
    if(document.getElementById('tipoDeDocumento').value == 'V'){
        document.getElementById('inputCedula').setAttribute('maxlength', '8');
    }else{
        document.getElementById('inputCedula').setAttribute('maxlength', '9');
    }
}

function CrearModalDeErrores(){
    var ModalDeErrores = document.getElementById('ModalDeErrores');

    var CajaDeErrores = document.querySelector('.CajaDeErrores');

    if(CajaDeErrores.innerText.length){
        ModalDeErrores.classList.remove('dplaynn');
    }

    ModalDeErrores.addEventListener('click', function(){
        ModalDeErrores.classList.add('dplaynn');
    })
}

function CrearCajasDePreguntasDeRecuperacion(){
    
    cajas = document.querySelectorAll('.CajaDePregRecup');
    cajas.forEach(caja => {
        var preguntapersonalizada = caja.querySelector('#preguntapersonalizada');
        var selectordepregunta = caja.querySelector('#selectordepregunta');
        selectordepregunta.addEventListener('change', function(){
            if(this.value == 0){
                preguntapersonalizada.classList.remove('dplaynn');
            }else{
                preguntapersonalizada.classList.add('dplaynn');
            }
        })


        
        if(selectordepregunta.value == 0){
            preguntapersonalizada.classList.remove('dplaynn');
        }else{
            preguntapersonalizada.classList.add('dplaynn');
        }
    });
}

function ProfileSelectorEvent(){
    var ProfileSelectorEvent = document.getElementById('profileselector');

    ProfileSelectorEvent.addEventListener('change', function(){
        console.log(this.value)
        document.querySelectorAll('.checkboxmodulo').forEach( chbx => {
            chbx.checked = false;
        })

        document.querySelectorAll('.perfil'+this.value).forEach( chbx => {
            chbx.checked = true;
        })
    })
}

function soloInt(element, e){
    if(isNaN(e.key)){
        return false;
    }
}