const helpInput = document.getElementById('helpInput');
const resultsContainer = document.getElementById('resultsContainer');

window.addEventListener('load', function(){
    this.document.querySelectorAll('.optionParent').forEach( optionParent => {
        if(!optionParent.classList.contains('disabled')){
            optionParent.addEventListener('click', function(){
                this.classList.toggle('open');
            })
        }
    })

    this.document.querySelectorAll('.childs').forEach( childs => {
        childs.addEventListener('click', function(e){
            e.stopPropagation();
        })
    })


    this.document.querySelectorAll('.hooker').forEach( hooker => {
        hooker.addEventListener('click', function(){
            document.getElementById(this.getAttribute('hook')).scrollIntoView({behavior: 'smooth', block: 'center', inline: 'center'});
        })
    })



    this.document.querySelectorAll('.optionName a').forEach( a => {
        a.addEventListener('click', function(e){
            e.stopPropagation()
        })
    })

    
    let raul;

    helpInput.addEventListener('keyup', function(e){
        search = helpInput.value.trim();
        
        if(search!=''){
            if(e.keyCode > 40 || e.keyCode < 33){
                showLoading();

                try{
                    clearTimeout(raul);
                }catch(error){}
    
                raul = setTimeout(() => {
                    getManualHelp(search, this.getAttribute('username'));
                }, 1000);
            }
        }else{
            hideLoading();
        }
    })
})

function showLoading(){
    resultsContainer.classList.remove('close');
    resultsContainer.innerText = 'Buscando...';
}

function hideLoading(){
    resultsContainer.classList.add('close');
}

function showNoResult(){
    resultsContainer.classList.remove('close');
    resultsContainer.innerHTML = `<p>No encontramos ningun resultado para tu busqueda <span class="fi-rr-sad"></span></p>`;
}


async function getManualHelp(search, user){
    try{
        response = await fetch(`http://${ipserver}/CleoInventory/API/publicfunctions.php?method=getManualHelp&search=${search}&user=${user}`);

        if(response.status == 200){
            petition = await response.json();

            if(petition.status == 200){
                if(petition.result.length > 0){
                    resultsContainer.innerHTML = '';

                    petition.result.forEach( row => {
                        resultsContainer.innerHTML+= `<a ${row.unlock? `href="#${row.id}"`:''}>
                            <span class="fi-rr-${row.unlock? 'interrogation':'lock'}"></span>
                            ${row.name}
                        </a>`;
                    })

                }else{
                    showNoResult();
                }
            }else{
                alert(petition.result)
            }
        }else{
            alert(`Error de consultar #${response.status}`);
        }
    }catch(error){
        console.log(error);
    }
}