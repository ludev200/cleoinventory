const searchResultList = document.getElementById('searchResultList');
const yanosequenombreponer = document.getElementById('yanosequenombreponer');
const entityAddedOnList = document.getElementById('entityAddedOnList');




let allProducts = [];
let listedProducts = [];

window.addEventListener('load', async function(){
    if(document.getElementById('entityAddedOnList').value != ''){
        listedProducts = document.getElementById('entityAddedOnList').value.split('¿');
    }

    await getProducts('');
    
    

    showListedProducts();

    document.getElementById('searchValueInput').addEventListener('keyup', function(e){
        if(e.keyCode == 13){
            document.getElementById('searchValueButton').click();
        }
    })

    document.getElementById('searchValueButton').addEventListener('click', function(){
        getProducts(document.getElementById('searchValueInput').value);
    })

    document.getElementById('validateFormButton').addEventListener('click', function(e){
        e.preventDefault();
        validateForm();
    })
})


async function validateForm(){
    idEntity = document.getElementById('idEntity').value;
    docType = document.getElementById('docType').value;
    nname = document.getElementById('name').value;
    phone = document.getElementById('phoneInput').value;
    phone2 = document.getElementById('phone2Input').value;
    email = document.getElementById('emailInput').value;
    address = document.getElementById('addressInput').value;
    products = document.getElementById('entityAddedOnList').value;

    ulr = `http://${ipserver}/CleoInventory/API/entityfunctions.php?idModulo=7&idEntity=${idEntity}&method=validateData&`+
    `docType=${docType}&phone=${phone}&phone2=${phone2}&email=${email}&products=${products}&name=${nname}&address=${address}`;
    console.log(ulr);
    try{
        response = await fetch(ulr);
        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                document.getElementById('sendFormButton').click();
            }else{
                Toast.fire({
                    icon: 'error',
                    title: petition.message
                })
            }
        }else{
            console.log(`Error de response: ${response.status}`)    
        }
    }catch(error){
        console.log(error);
    }
}


async function getProducts(searchValue){
    try{
        url = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getConsumableProducts&value=${searchValue}`;
        //response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getProducts&value=${searchValue}`);
        response = await fetch(url);

        if(response.status == 200){
            petition = await response.json();

            
            if(petition.status == 200){
                searchResultList.innerHTML = '';
                if(petition.result.length == '0'){
                    searchResultList.innerHTML = `<div class="emptyRow"><span>No hay proveedores para mostrar</span></div>`;
                }else{
                    tempPackage = [];
                    
                    petition.result.forEach(row => {
                        isChecked = listedProducts.includes(row['id']);
                        

                        
                        searchResultList.innerHTML+= `<div class="row">
                            <span class="cell cellImage">
                                <img src="../../Imagenes/Productos/${(row.img? row.img:'ImagenPredefinida_Productos.png')}">
                            </span>
                            <span class="cell cellName">
                                ${row.name}
                            </span>
                            <span class="cell cellButton">
                                <label class="switch">
                                    <input id="switchInput-${row['id']}" idEntity="${row['id']}" type="checkbox" ${isChecked? 'checked':''} class="switchInput"/>
                                    <div class="slider round"></div>
                                </label>
                            </span>
                        </div>`;
                        

                        tempPackage[row['id']] = [];
                        tempPackage[row['id']]['name'] = row['name'];
                        tempPackage[row['id']]['ULRImagen'] = row['img'];
                    });

                    if(allProducts.length == '0'){
                        allProducts = tempPackage;
                    }

                    
                    document.querySelectorAll('.switchInput').forEach( button => {
                        button.addEventListener('change', function(){
                            if(this.checked){
                                addProductToList(this.getAttribute('idEntity'))
                            }else{
                                removeProductFromList(this.getAttribute('idEntity'))
                            }
                        })
                    })
                }
            }
        }
    }catch(error){
        console.log(error)
    }
}

function showListedProducts(){
    yanosequenombreponer.innerHTML = '';
    
    listedProducts.forEach(ID => {
        
        entity = allProducts[ID];
        

        yanosequenombreponer.innerHTML+= `<div class="rowCard" id="addedCard-${ID}">
            <span class="piece pieceImage">
                <img src="../../Imagenes/Productos/${entity['ULRImagen']? entity['ULRImagen']:'ImagenPredefinida_Productos.png'}">
            </span>
            <span class="piece pieceName">
                ${entity['name']}
            </span>
            <span class="piece pieceButton">
                <button type="button" class="deleteEntityButton" id="deleteEntityButton-${ID}" idEntity="${ID}">
                    <i class="fi-rr-cross-small"></i>
                </button>
            </span>
        </div>`;
    });

    document.querySelectorAll('.deleteEntityButton').forEach( button => {
        button.addEventListener('click', function(){
            removeProductFromList(this.getAttribute('idEntity'));
        })
    })
}


function removeProductFromList(ID){
    listedProducts.splice(listedProducts.indexOf(ID), 1)
    entityAddedOnList.value = listedProducts.join('¿');

    
    document.getElementById('addedCard-'+ID).remove();
    try{
        document.getElementById('switchInput-'+ID).checked = false;
    }catch(xd){}
}


function addProductToList(ID){
    listedProducts.push(ID);
    entityAddedOnList.value = listedProducts.join('¿');
    showListedProducts();
}