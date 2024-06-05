const imagenInput = document.querySelector('.imgAndId label input');
const imagePreview = document.querySelector('.imgAndId img');
const SW_container = document.getElementById('SWAlert');
const categoryInput = document.getElementById('categoryInput');
const alertLevelInput = document.getElementById('alertLevelInput');
const unitInput = document.getElementById('unitInput');
const hideOnWorkHand = document.querySelectorAll('.hideOnWorkHand');
const validateFormButton = document.querySelectorAll('.validateFormButton');


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


const searchValueButton = document.getElementById('searchValueButton');
const searchValueInput = document.getElementById('searchValueInput');
const searchResultList = document.getElementById('searchResultList');
const entityAddedOnListInput = document.getElementById('entityAddedOnListInput');
const yanosequenombreponer = document.getElementById('yanosequenombreponer');

let requestNumber = 0;
let allProviders = [];

window.addEventListener('load', () => {
    if(SW_container){
        Toast.fire({
            icon: "error",
            title: SW_container.innerText
        });
    }

    imagenInput.addEventListener('change', function(){
        var newImage = imagenInput.files[0];
                
        const reader = new FileReader();
        reader.onload = (e) => { imagePreview.src = e.target.result; };
        reader.readAsDataURL(newImage);
    })


    getProviders();
    searchValueButton.addEventListener('click', getProviders);


    searchValueInput.addEventListener('keyup', (e) => {
        if(e.keyCode == 13){
            requestNumber++;
            searchValueButton.click();
        }
    })



    showInputForCategory();
    categoryInput.addEventListener('change', function(){
        showInputForCategory();

    })

    showUnit();
    unitInput.addEventListener('change', function(){
        showUnit();
    })

    document.getElementById('validateFormButton').addEventListener('click', function(e){
        e.preventDefault();
        validateForm();
    })
})


async function validateForm(){
    nname = document.querySelector('input#name').value;
    price = document.querySelector('input#priceInput').value;
    idCategory = document.getElementById('categoryInput').value;
    unit = document.getElementById('unitInput').value;
    alertLevel = document.getElementById('alertLevelInput').value;
    deafultSpoilage = document.getElementById('deafultSpoilage').value;
    description = document.getElementById('descriptionInput').value;
    providers = document.getElementById('entityAddedOnListInput').value;

    ulr = `http://${ipserver}/CleoInventory/API/publicFunctions.php?method=validateNewProduct&`+
    `deafultSpoilage=${deafultSpoilage}&price=${price}&idCategory=${idCategory}&idUnit=${unit}&alertLevel=${alertLevel}&providers=${providers}&description=${description}&name=${nname}`;
    

    fetch(ulr).then( (res)=>res.json()).then((res)=>{
        if(res.status == 200){
            document.querySelector('form').submit();
        }else{
            Toast.fire({
                icon: 'warning',
                title: res.result
            })
        }
    })
}

const getProviders = async () => {
    searchValue = searchValueInput.value;
    url = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getProviders&value=${searchValue}`;

    showLoading();

    fetch(url).then( (res) => res.json()).then((res) => {
        if(res.status == 200){
            let currentProviders = [];
            if(entityAddedOnListInput.value.trim() != ''){
                currentProviders = entityAddedOnListInput.value.split('¿');
            }


            if(res.result.length > 0){searchResultList.innerHTML = '';}else{
                searchResultList.innerHTML = '<div class="emptyRow"><span>No hay proveedores para mostrar</span></div>';
            }
            

            if(allProviders.length < 1){
                allProviders = res.result;
            }
            
            res.result.forEach(row => {
                isChecked = currentProviders.includes(row.rif);
                
                searchResultList.innerHTML+= `<div class="row">
                    <span class="cell cellImage">
                        <img src="../../Imagenes/Proveedores/${(row.ULRImagen? row.ULRImagen:'ImagenPredefinida_Proveedores.png')}">
                    </span>
                    <span class="cell cellName">
                        ${row.nombre}
                    </span>
                    <span class="cell cellButton">
                        <label class="switch">
                            <input id="switchInput-${row.rif}" identity="${row.rif}" ${isChecked? 'checked':''} type="checkbox" class="switchInput">
                            <div class="slider round"></div>
                        </label>
                    </span>
                </div>`;
            });

            
            if(requestNumber > 0){
                window.scrollTo(0, document.body.scrollHeight);
            }

            addEventToSwitches();
        }else{
            Toast.fire({
                icon: 'warning',
                title: res.message
            })
        }
    }).catch( (err) => {
        Toast.fire({
            icon: 'error',
            title: 'Un error ha impedido continuar'
        })
    });
}

const showLoading = () => {
    searchResultList.innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>Cargando
</div>`;
};

const addEventToSwitches = () => {
    document.querySelectorAll('.switchInput').forEach(input => {
        input.addEventListener('change', function(){
            if(input.checked){
                addProviderToList(this.getAttribute('identity'));
            }else{
                removeProviderFromList(this.getAttribute('identity'));
            }
        })
    })
}

const addProviderToList = (id) => {
    let currentProviders = [];
    if(entityAddedOnListInput.value.trim() != ''){
        currentProviders = entityAddedOnListInput.value.split('¿');
    }
    
    if(!currentProviders.includes(id)){
        currentProviders.push(id);
        
        let data = allProviders.find( (obj) => {
            if(obj.rif == id) return true;
        });


        yanosequenombreponer.appendChild(createElementFromHTML(`<div class="rowCard" id="addedCard-${data.rif}">
            <span class="piece pieceImage">
                <img src="../../Imagenes/Proveedores/${(data.ULRImagen? data.ULRImagen:'ImagenPredefinida_Proveedores.png')}">
            </span>
            <span class="piece pieceName">
                ${data.nombre}
            </span>
            <span class="piece pieceButton">
                <button type="button" class="deleteEntityButton" id="deleteEntityButton-${data.rif}" identity="${data.rif}">
                    <i class="fi-rr-cross-small"></i>
                </button>
            </span>
        </div>`));

        document.getElementById(`deleteEntityButton-${data.rif}`).addEventListener('click', function(){
            removeProviderFromList(this.getAttribute('identity'))
            if(document.getElementById(`switchInput-${data.rif}`)){
                document.getElementById(`switchInput-${data.rif}`).checked = false;
            }
        })

        entityAddedOnListInput.value = currentProviders.join('¿');
    }
}

const removeProviderFromList = (id) => {
    let currentProviders = [];
    if(entityAddedOnListInput.value.trim() != ''){
        currentProviders = entityAddedOnListInput.value.split('¿');
    }


    if(currentProviders.includes(id)){        
        currentProviders.splice(currentProviders.indexOf(id), 1);
        document.getElementById('addedCard-'+id).remove();

        entityAddedOnListInput.value = currentProviders.join('¿');
    }
}





function showInputForCategory(){
    
    hideOnWorkHand.forEach( element => {
        if(categoryInput.value < 3){
            element.style.display = "";
        }else{
            element.style.display = "none";
        }
    })

    if(categoryInput.value == 1){
        alertLevelInput.setAttribute('onkeypress','return validate_intNumber(this, event)');
        
    }else{
        alertLevelInput.setAttribute('onkeypress','return validate_4floatNumber(this, event)');
    }


    
    
    if(categoryInput.value == 2){
        unitInput.setAttribute('disabled', 'disabled');
        unitInput.value = 2;
        document.getElementById('unitSimbol').innerText = 'u';
        document.querySelectorAll('.showForEquipment').forEach( node => {
            node.style.display = '';
        })
    }else{
        unitInput.removeAttribute('disabled');
        document.querySelectorAll('.showForEquipment').forEach( node => {
            node.style.display = 'none';
        })
    }
}

function showUnit(){
    document.getElementById('unitSimbol').innerText = unitInput.options[unitInput.selectedIndex].getAttribute('simbol');
}



function createElementFromHTML(htmlString) {
    var div = document.createElement('div');
    div.innerHTML = htmlString.trim();
    return div.firstChild;
}


function validate_intNumber(element, e){
    if(isNaN(e.key)){
        return false;
    }
}

function validate_price(element, e){
    if(e.key == '.'){
        if(element.value.includes('.')){
            return false;
        }else{
             
        }
    }else{
        if(isNaN(e.key)){
            return false;
        }else{
            if(element.value.indexOf('.') > 0){
                decimals = Number(element.value.length) - Number(element.value.indexOf('.'));
                if(decimals > 2){
                    return false;
                }
            }
        }
    }
}

function validate_4floatNumber(element, e){
    
    
    if(isNaN(e.key)){
        if(e.key != '.'){
            return false;
        }

        
        if(element.value.includes('.')){
            return false;
        }
    }else{
        if(element.value.includes('.')){
            pieces = element.value.split('.');
            if(pieces[1].length > 3){
                if(Number(element.value)>0){
                    selectionSize = element.selectionEnd - element.selectionStart;
                    if(selectionSize != element.value.length){
                        return false;
                    }
                }
            }
        }
    }

/*
    console.log(element.value.toString() + e.key)
    
    if((element.value.toString() + e.key) == 0){
        element.setAttribute('maxlength', '6');
    }else{
        element.setAttribute('maxlength', '');
    }
    console.log(element.getAttribute('maxlength'))
    */
}

function validate_2floatNumber(element, e){
    if(isNaN(e.key)){
        if(e.key != '.'){
            return false;
        }

        
        if(element.value.includes('.')){
            return false;
        }
    }else{
        if(element.value.includes('.')){
            pieces = element.value.split('.');
            if(pieces[1].length > 1){
                if(Number(element.value)>0){
                    selectionSize = element.selectionEnd - element.selectionStart;
                    if(selectionSize != element.value.length){
                        return false;
                    }
                }
            }
        }
    }
}

function priceFormat(element){
    element.value = Number(element.value).toFixed(2);
}

function validateNoHashtag(element, e){
    if(e.key == '#'){
        Toast.fire({
            icon: 'warning',
            title: "El uso del caractér '#' no está permitido"
        })
        return false;
    }
}



function spoilageFormat(element){
    element.value = Number(element.value).toFixed(4);
}