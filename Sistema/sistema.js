window.addEventListener('load', function(){
    this.document.querySelectorAll('.editPhoto_button').forEach( button => {
        button.addEventListener('click', function(){
            document.getElementById(button.getAttribute('idrow')).classList.add('active');
        })
    })


    this.document.querySelectorAll('.listaImagenEditable .back').forEach( button => {
        button.addEventListener('click', function(){
            document.getElementById(button.getAttribute('idrow')).classList.remove('active');
        })
    })

    this.document.querySelectorAll('.listaImagenEditable input').forEach( input => {
        
        input.addEventListener('change', function(){
            var newImage = input.files[0];
            
            const reader = new FileReader();
            reader.onload = (e) => { document.querySelector(`#${input.getAttribute('idrow')} .showInActive img`).src = e.target.result; };
            reader.readAsDataURL(newImage);
            document.querySelector(`#${input.getAttribute('idrow')} label span`).innerText = `Subir imagen: ${newImage.name}`;
        })
    })


    extraOptionsEvents();
    getUnits();
    

    if(document.getElementById('CLEOATButton') != undefined){
        CLEOATEvents();
    }

    
})

let units = [];

function extraOptionsEvents(){

    document.querySelectorAll('#selectsEditables .row').forEach( row => {
        const addNew_div = row.querySelector('.addNewDiv');

        row.querySelector('.showAddNewOptionButton').addEventListener('click', function(){
            addNew_div.classList.add('active');

            addNew_div.querySelectorAll('input').forEach( input => {
                input.value = '';
            })
        })


        row.querySelector('.hideAddNewQuestionButton').addEventListener('click', function(){
            addNew_div.classList.remove('active');
        })


        

        
        row.querySelector('#hideEditModeButton').addEventListener('click', function(){
            row.classList.remove('editMode');
        })
    })


    document.getElementById('saveNewQuestionButton').addEventListener('click', function(){
        value = document.getElementById('newQuestionInput').value;

        addNewQuestion(value);
    })

    document.getElementById('saveNewUnitButton').addEventListener('click', function(){
        nname = document.getElementById('unitNameInput').value;
        simbol = document.getElementById('unitSymbolInput').value;

        addNewUnit(nname, simbol);
    })
    


    document.getElementById('deleteUnitButton').addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar esta unidad de medición?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, eliminar"
          }).then((result) => {
            if (result.isConfirmed) {
              deleteUnit(document.querySelector('#units select').value);
            }
          });
    })

    document.getElementById('deleteQuestionButton').addEventListener('click', function(){
        Swal.fire({
            title: "¿Deseas eliminar esta pregunta de seguridad?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, eliminar"
          }).then((result) => {
            if (result.isConfirmed) {
              deleteQuestion(document.querySelector('#questions select').value);
            }
          });
    })


    document.getElementById('showQuestionEditModeButton').addEventListener('click', function(){
        value = document.querySelector('#questions select').options[document.querySelector('#questions select').selectedIndex].innerText;
        document.querySelector('#questions .showOnEdit input').value = value;
        document.getElementById('questions').classList.add('editMode');
    })

    document.getElementById('showUnitEditModeButton').addEventListener('click', function(){
        value = document.querySelector('#units select').options[document.querySelector('#units select').selectedIndex].value;
        
        
        document.querySelector('#editNameUnitInput').value = units[value]['name'];
        document.querySelector('#editSimbolUnitInput').value = units[value]['simbol'];
        document.getElementById('units').classList.add('editMode');
    })

    document.getElementById('saveUpdateUnitButton').addEventListener('click', function(){
        idUnit = document.querySelector('#units select').options[document.querySelector('#units select').selectedIndex].value;
        nname = document.getElementById('editNameUnitInput').value;
        simbol = document.getElementById('editSimbolUnitInput').value;

        updateUnit(idUnit, nname, simbol);
    })
    

    document.getElementById('saveUpdateQuestionButton').addEventListener('click', function(){
        idQ = document.querySelector('#questions select').options[document.querySelector('#questions select').selectedIndex].value;
        value = document.getElementById('editQuestionInput').value;
        
        updateQuestion(idQ, value);
    })
}

async function updateQuestion(idQ, value){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=updateQuestion&id=${idQ}&value=${value}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: "success",
                    title: 'Se ha actualizado correctamente'
                });


                document.querySelector('#questions select').options[document.querySelector('#questions select').selectedIndex].innerText = value;
                document.getElementById('questions').classList.remove('editMode');

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
}


async function updateUnit(idUnit, nname, simbol){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=updateUnit&idUnit=${idUnit}&name=${nname}&simbol=${simbol}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: "success",
                    title: 'Se ha actualizado correctamente'
                });

                document.querySelector('#units select').options[document.querySelector('#units select').selectedIndex].innerText = nname;
                units[idUnit]['name'] = nname;
                units[idUnit]['simbol'] = simbol;


                document.getElementById('units').classList.remove('editMode');
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
}

async function deleteUnit(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=deleteUnit&id=${id}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: "success",
                    title: 'Se ha eliminado la unidad de medición'
                });

                node = document.querySelector('#units select');
                node.options[node.selectedIndex].remove();
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
}


async function deleteQuestion(id){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=deleteQuestion&id=${id}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: "success",
                    title: 'Se ha eliminado la pregunta de seguridad'
                });

                node = document.querySelector('#questions select');
                node.options[node.selectedIndex].remove();
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
}

async function addNewUnit(nname, simbol){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=addNewUnit&name=${nname}&simbol=${simbol}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: "success",
                    title: 'Se ha añadido la unidad de medida'
                });

                document.querySelector('#units .addNewForm button.back').click();
                
                var div = document.createElement('div');
                div.innerHTML = `<option value="0">${nname}</option>`;

                document.querySelector('#units select').appendChild(div.firstChild);
                
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
}

async function addNewQuestion(value){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=addNewQuestion&value=${value}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                Toast.fire({
                    icon: "success",
                    title: 'Se ha añadido la pregunta de seguridad'
                });

                document.querySelector('#questions .addNewForm button.back').click();
                
                var div = document.createElement('div');
                div.innerHTML = `<option value="0">${value}</option>`;

                document.querySelector('#questions select').appendChild(div.firstChild);
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
}


async function getUnits(){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getUnits`);

        if(response.status == 200){
            petition = await response.json();

            
            petition.result.forEach( unit => {
                
                units[unit.id] = [];
                units[unit.id]['name'] = unit.nombre;
                units[unit.id]['simbol'] = unit.simbolo;
            })
        }
    }catch(error){
        console.log(error)
    }
}

async function CLEOATEvents(){
    const CLEOATButton = document.getElementById('CLEOATButton');
    CLEOATButton.innerText = 'Abriendo.';
    await sleep(600);
    CLEOATButton.innerText = 'Abriendo..';
    await sleep(600);
    CLEOATButton.innerText = 'Abriendo...';
    await sleep(600);
    this.window.location.href = this.window.location.origin+this.window.location.pathname;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}