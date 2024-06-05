window.addEventListener('load', async function(){
    await getProviders('');
    if(document.getElementById('entityAddedOnList').value != ''){
        showPreLoadedEntity(document.getElementById('entityAddedOnList').value.split('¿'));
    }
    

    this.document.getElementById('searchValueButton').addEventListener('click', function(){
        getProviders(document.getElementById('searchValueInput').value);
    })

    document.getElementById('searchValueInput').addEventListener('keyup', function(e){
        if(e.keyCode == 13){
            document.getElementById('searchValueButton').click();
        }
    })
})

const searchResultList = document.getElementById('searchResultList');
let selectedProviders = [];



if(document.getElementById('entityAddedOnList').value != ''){
    selectedProviders = document.getElementById('entityAddedOnList').value.split('¿');
}

let allProviders = [];


function showPreLoadedEntity(IDs){    
    if(IDs.length > 0){
        IDs.forEach( ID => {
            entity = allProviders[ID];
    
            document.getElementById('yanosequenombreponer').innerHTML += `<div class="rowCard" id="addedCard-${ID}">
            <span class="piece pieceImage">
                <img src="../../Imagenes/Proveedores/${entity['ULRImagen']? entity['ULRImagen']:'ImagenPredefinida_Proveedores.png'}">
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
        })
    }
    


    document.querySelectorAll('.deleteEntityButton').forEach( button => {
        button.addEventListener('click', function(){
            try{
                document.getElementById(`switchInput-${this.getAttribute('idEntity')}`).checked = false;    
            }catch(error){

            }
            
            removeFromEntityList(this.getAttribute('idEntity'));
            document.getElementById('entityAddedOnList').value = selectedProviders.join('¿');
        })
    })
}

async function getProviders(searchValue){

    ulr = `http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getProviders&value=${searchValue}`;

    
    try{
        response = await fetch(ulr);

        if(response.status == 200){
            petition = await response.json();

            
            if(petition.status == 200){

                

                searchResultList.innerHTML = '';
                if(petition.result.length == '0'){
                    searchResultList.innerHTML = `<div class="emptyRow"><span>No hay proveedores para mostrar</span></div>`;
                }else{
                    tempPackage = [];
                    petition.result.forEach(row => {
                        isChecked = document.getElementById('entityAddedOnList').value.split('¿').includes(row['rif']);

                        searchResultList.innerHTML+= `<div class="row">
                        <span class="cell cellImage">
                            <img src="../../Imagenes/Proveedores/${(row['ULRImagen']? row['ULRImagen']:'ImagenPredefinida_Proveedores.png')}">
                        </span>
                        <span class="cell cellName">
                            ${row['nombre']}
                        </span>
                        <span class="cell cellButton">
                            <label class="switch">
                                <input id="switchInput-${row['rif']}" idEntity="${row['rif']}" type="checkbox" ${isChecked? 'checked':''} class="switchInput"/>
                                <div class="slider round"></div>
                            </label>
                        </span>
                    </div>`;

                    tempPackage[row['rif']] = [];
                    tempPackage[row['rif']]['name'] = row['nombre'];
                    tempPackage[row['rif']]['ULRImagen'] = row['ULRImagen'];
                    });

                    if(allProviders.length == '0'){
                        allProviders = tempPackage;
                    }
                }
            }else{

            }


            
            document.querySelectorAll('.switchInput').forEach( switchInput => {
                switchInput.addEventListener('change', function(){
                    if(this.checked){
                        addInEntityList(this.getAttribute('idEntity'));
                    }else{
                        removeFromEntityList(this.getAttribute('idEntity'));
                    }


                    console.log(selectedProviders)
                    document.getElementById('entityAddedOnList').value = selectedProviders.join('¿');
                    
                    document.querySelectorAll('.deleteEntityButton').forEach( button => {
                        button.addEventListener('click', function(){
                            try{
                                document.getElementById(`switchInput-${this.getAttribute('idEntity')}`).checked = false;    
                            }catch(error){

                            }
                            
                            removeFromEntityList(this.getAttribute('idEntity'));
                            document.getElementById('entityAddedOnList').value = selectedProviders.join('¿');
                            
                        })
                    })
                })
            })
        }
    }catch(error){
        console.log(error);
    }
}

function addInEntityList(id){
    selectedProviders.push(id);
    entity = allProviders[id];
    
    document.getElementById('yanosequenombreponer').innerHTML+= `<div class="rowCard" id="addedCard-${id}">
        <span class="piece pieceImage">
            <img src="../../Imagenes/Proveedores/${entity['ULRImagen']? entity['ULRImagen']:'ImagenPredefinida_Proveedores.png'}">
        </span>
        <span class="piece pieceName">
            ${entity['name']}
        </span>
        <span class="piece pieceButton">
            <button type="button" class="deleteEntityButton" id="deleteEntityButton-${id}" idEntity="${id}">
                <i class="fi-rr-cross-small"></i>
            </button>
        </span>
    </div>`;
}

function removeFromEntityList(id){
    selectedProviders.splice(selectedProviders.indexOf(id), 1);

    document.getElementById(`addedCard-${id}`).remove();
}