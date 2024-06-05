const imagenInput = document.querySelector('.imgAndId label input');
const imagePreview = document.querySelector('.imgAndId img');
const SW_container = document.getElementById('SWAlert');
const hideOnWorkHand = document.querySelectorAll('.hideOnWorkHand');
const validateFormButton = document.querySelectorAll('.validateFormButton');
const searchValueButton = document.getElementById('searchValueButton');
const searchValueInput = document.getElementById('searchValueInput');
const searchResultList = document.getElementById('searchResultList');
const entityAddedOnListInput = document.getElementById('entityAddedOnListInput');
const yanosequenombreponer = document.getElementById('yanosequenombreponer');

let requestNumber = 0;
let allProducts = [];


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

    getProducts('');
    searchValueButton.addEventListener('click', getProducts);


    searchValueInput.addEventListener('keyup', (e) => {
        if(e.keyCode == 13){
            requestNumber++;
            searchValueButton.click();
        }
    })

    document.getElementById('validateFormButton').addEventListener('click', function(e){
        e.preventDefault();
        validateForm();
    })
})



async function validateForm(){
    let docType = document.getElementById('docType').value;
    let cedula = document.getElementById('idEntity').value;
    let nname = document.getElementById('name').value;
    let phone = document.getElementById('phoneInput').value;
    let phone2 = document.getElementById('phone2Input').value;
    let email = document.getElementById('emailInput').value;
    let address = document.getElementById('addressInput').value;
    let productsList = document.getElementById('entityAddedOnListInput').value;

    ulr = `http://${ipserver}/CleoInventory/API/publicFunctions.php?method=validateNewProvider&`+
    `docType=${docType}&cedula=${cedula}&name=${nname}&phone=${phone}&phone2=${phone2}&email=${email}&address=${address}&productsList=${productsList}`;
    

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

const getProducts = async () => {
    searchValue = searchValueInput.value;
    url = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getConsumableProducts&value=${searchValue}`;
    showLoading();

    fetch(url).then( (res) => res.json()).then((res) => {
        if(res.status == 200){
            let currentProducts = [];
            if(entityAddedOnListInput.value.trim() != ''){
                currentProducts = entityAddedOnListInput.value.split('¿');
            }


            if(res.result.length > 0){searchResultList.innerHTML = '';}else{
                searchResultList.innerHTML = '<div class="emptyRow"><span>No hay productos para mostrar</span></div>';
            }

            if(allProducts.length < 1){
                allProducts = res.result;
            }


            
            res.result.forEach(row => {
                isChecked = currentProducts.includes(row.id);
                
                searchResultList.innerHTML+= `<div class="row">
                    <span class="cell cellImage">
                        <img src="../../Imagenes/Productos/${(row.img? row.img:'ImagenPredefinida_Productos.png')}">
                    </span>
                    <span class="cell cellName">
                        ${row.name}
                    </span>
                    <span class="cell cellButton">
                        <label class="switch">
                            <input id="switchInput-${row.id}" identity="${row.id}" ${isChecked? 'checked':''} type="checkbox" class="switchInput">
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


const addProviderToList = (id) => {
    let currentProviders = [];
    if(entityAddedOnListInput.value.trim() != ''){
        currentProviders = entityAddedOnListInput.value.split('¿');
    }
    
    if(!currentProviders.includes(id)){
        currentProviders.push(id);
        
        let data = allProducts.find( (obj) => {
            if(obj.id == id) return true;
        });

        

        yanosequenombreponer.appendChild(createElementFromHTML(`<div class="rowCard" id="addedCard-${data.id}">
            <span class="piece pieceImage">
                <img src="../../Imagenes/Productos/${(data.img? data.img:'ImagenPredefinida_Productos.png')}">
            </span>
            <span class="piece pieceName">
                ${data.name}
            </span>
            <span class="piece pieceButton">
                <button type="button" class="deleteEntityButton" id="deleteEntityButton-${data.id}" identity="${data.id}">
                    <i class="fi-rr-cross-small"></i>
                </button>
            </span>
        </div>`));

        document.getElementById(`deleteEntityButton-${data.id}`).addEventListener('click', function(){
            removeProviderFromList(this.getAttribute('identity'))
            if(document.getElementById(`switchInput-${data.id}`)){
                document.getElementById(`switchInput-${data.id}`).checked = false;
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



const showLoading = () => {
    searchResultList.innerHTML = `<div class="did_loading">
    <div class="rotating"><span class="fi fi-rr-loading"></span></div>Cargando
</div>`;
};


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

function validateRifAndCedula(e, element){
    if(isNaN(e.key)){
        return false;
    }
    if(document.getElementById('docType').value=='V'){
        element.setAttribute('maxlength', '8');
    }else{
        element.setAttribute('maxlength', '9');
    }
}

function priceFormat(element){
    element.value = Number(element.value).toFixed(2);
}


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