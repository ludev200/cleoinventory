

/////FUNCIONES
function DesactivarScroll() {
    window.scrollTo(0, 0);
    document.body.classList.add("stop-scrolling");
}
    
function ActivarScroll() {
    document.body.classList.remove("stop-scrolling");
}

function AlternarCheckBox1(){
    if(CheckBox1.checked==true){
        Separador1.innerText = "+";
        CodigoDeArea1.style = "display: none";
        Numero1.maxLength = "12";
        Icono1.className = "fi-sr-map-marker";
        Icono1.title = "Cambiar a formato nacional";

        if(CodigoDeArea1.value=="0000"){
            CodigoDeArea1.value = "";
        }else{
            Numero1.value = "58" + CodigoDeArea1.value.slice(1,4) + Numero1.value;
        }
    }else{
        Separador1.innerText = "-";
        CodigoDeArea1.style = "display: inline";
        Numero1.maxLength = "7";
        Icono1.className = "fi-sr-map-marker-home";
        Icono1.title = "Cambiar a formato internacional";

        if(Numero1.value.slice(0,2)=="58"){
            if(Numero1.value.length>2){
                CodigoDeArea1.value = "0" + Numero1.value.slice(2,5);
                Numero1.value = Numero1.value.slice(5);
            }else{
                CodigoDeArea1.value = "";
                Numero1.value = "";
            }
        }else{
            CodigoDeArea2.value = "";
            Numero2.value = "";
        }
    }
}

function AlternarCheckBox2(){

    if(CheckBox2.checked==true){
        
        Separador2.innerText = "+";
        CodigoDeArea2.style = "display: none";
        Numero2.maxLength = "12";
        Icono2.className = "fi-sr-map-marker";
        Icono2.title = "Cambiar a formato nacional";
        
        if(CodigoDeArea2.value=="0000"){
            CodigoDeArea2.value = "";
        }else{
            Numero2.value = "58" + CodigoDeArea2.value.slice(1,4) + Numero2.value;
        }
    }else{
        Separador2.innerText = "-";
        CodigoDeArea2.style = "display: inline";
        Numero2.maxLength = "7";
        Icono2.className = "fi-sr-map-marker-home";
        Icono2.title = "Cambiar a formato internacional";

        if(Numero2.value.slice(0,2)=="58"){
            if(Numero2.value.length>2){
                CodigoDeArea2.value = "0" + Numero2.value.slice(2,5);
                Numero2.value = Numero2.value.slice(5);
            }else{
                CodigoDeArea2.value = "";
                Numero2.value = "";
            }
        }else{
            CodigoDeArea2.value = "";
            Numero2.value = "";
        }
    }
}

//////////////////MOSTRAR MINIATURA DE LA IMAGEN SELECCIONADA
let inputDeLaImagen = document.getElementById("inputHidden");
let ImagenQueSeMuestra = document.getElementById("Spoiler");

inputDeLaImagen.addEventListener("change",()=>{
    
    const ArrayDeArchivos = inputDeLaImagen.files; 
    ImagenQueSeMuestra.file=ArrayDeArchivos[0];

    const reader = new FileReader();
    reader.onload = (e) => { ImagenQueSeMuestra.src = e.target.result; };
    reader.readAsDataURL(ArrayDeArchivos[0]);
})

////////////CHECKBOXES TELEFONO 1
let CodigoDeArea1 = document.getElementById("CodigoDeArea1");
let Separador1 = document.getElementById("Separador1");
let Numero1 = document.getElementById("Numero1");
let CheckBox1 = document.getElementById("CheckBox1");
let Icono1 = document.getElementById("Icono1");

CheckBox1.addEventListener("change",()=>{
    AlternarCheckBox1();
})

////////////CHECKBOX TELEFONO 2
let CodigoDeArea2 = document.getElementById("CodigoDeArea2");
let Separador2 = document.getElementById("Separador2");
let Numero2 = document.getElementById("Numero2");
let CheckBox2 = document.getElementById("CheckBox2");
let Icono2 = document.getElementById("Icono2");

CheckBox2.addEventListener("change",()=>{
    AlternarCheckBox2();
})



function validateRifAndCedula(e, element){
    if(isNaN(e.key)){
        return false;
    }
    if(document.getElementById('InputDeTipoDoc').value=='V'){
        element.setAttribute('maxlength', '8');
    }else{
        element.setAttribute('maxlength', '9');
    }
}

////////////SOLO NUMEROS
function SoloNumeros(e){
    entrada = e.keyCode || e.which;
    tecla = String.fromCharCode(entrada);
    numeros = "0123456789";
    especiales = "8-37-38-46";//borrar-flecha izquierda-flecha derecha-suprimir

    teclado_especial = false;

    for(i in especiales){
        if(tecla==especiales[i]){
            teclado_especial = true;
        }
    }
    if(numeros.indexOf(tecla)==-1 && !teclado_especial){
        return false;
    }
}

//COMPROBAR CHECKBOXES LUEGO DE CARGAR
window.addEventListener('load', function() {
    if(CheckBox1.checked==true){
        AlternarCheckBox1();
    }
    if(CheckBox2.checked==true){
        AlternarCheckBox2();
    } 

    if(CheckBoxModal.checked==false){
        VentanaDeErrores.hidden = true;
    }else{
        DesactivarScroll();
        
    }
    
});

//MODAL
let CheckBoxModal = document.getElementById("VisibilidadModal");
let VentanaDeErrores = document.getElementById("VentanaDeErrores");

CheckBoxModal.addEventListener("click", () => {
    if(CheckBoxModal.checked==false){
        
        VentanaDeErrores.hidden = true;
        ActivarScroll();
    }else{
        
    }
})






function valida_phoneFormat(element, event){
    if(element.value.charAt(4)!='-'){
        element.setAttribute('maxlength', '13');
    }else{
        element.setAttribute('maxlength', '12');
    }

    if(isNaN(event.key)){
        if(event.keyCode != 43){
            return false;
        }else{
            if(element.value != ''){
                return false;
            }
        }
    }else{
        value = element.value+event.key;
        
        if(value.toString().length == 4 && element.value.charAt(0)!='+'){
            element.value = value+'-';
            
            return false;
        }else{
            
        }
    }
}