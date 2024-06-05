let BotonBuscarCotizaciones = document.getElementById('BotonBuscarCotizaciones');
let SelectEstado = document.getElementById('SelectEstado');

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


SelectEstado.addEventListener('change', () => {
    BotonBuscarCotizaciones.click();
})

let FormularioBuscador = document.getElementById('FormularioBuscador');
let TopeDelListado = document.getElementById('TopeDelListado');


window.addEventListener('load', () => {
    EspecificacionesDeULR = window.location.search.split('&');

    EspecificacionesDeULR.forEach(element => {
        
        pedazos = element.split('=');
        if(pedazos[0] == 'paginadebusqueda'){
            if(pedazos[1] > 1){
                TopeDelListado.scrollIntoView();
            }
            
        }
        
        if(pedazos[0] == 'descripcion' || pedazos[0] == '?descripcion'){
            if(pedazos[1]){
                TopeDelListado.scrollIntoView();
            }
        }

        if(pedazos[0] == 'estado'){
            if(pedazos[1] != '30'){
                TopeDelListado.scrollIntoView();
            }else{
                
            }
        }
        
    });
})


let SelectMesDeGrafico = document.getElementById('SelectMesDeGrafico');
let SelectAnioDeGrafico = document.getElementById('SelectAnioDeGrafico');

SelectMesDeGrafico.addEventListener('change', () => {
    ConsultarNroDeCotizacionesPorEstado(SelectAnioDeGrafico.value, SelectMesDeGrafico.value);
    
    
})

SelectAnioDeGrafico.addEventListener('change', () => {
    ConsultarNroDeCotizacionesPorEstado(SelectAnioDeGrafico.value, SelectMesDeGrafico.value);
})


let Barra1 = document.querySelector('.Barra1');
let Barra2 = document.querySelector('.Barra2');
let Barra3 = document.querySelector('.Barra3');
let Barra4 = document.querySelector('.Barra4');

let AvisoSinResultados = document.querySelector('.AvisoSinResultados');


let ConsultarClientesMasActivos = async(Anio, Mes) =>{
    var Cliente1 = "";
    var Cliente2 = "";
    var Cliente3 = "";
    var Cliente4 = "";

    try{
        let respuesta = await fetch("http://"+ipserver+"/CleoInventory/API/API_Cotizaciones.php?count=clientes");
        if(respuesta.status === 200){
            DatosRecibidos = await respuesta.json();

            CotizacionesTotales = 0;

            if(DatosRecibidos.objetos){
                if(DatosRecibidos.objetos.length > 0){
                    CotizacionesTotales = DatosRecibidos.objetos[0]['contador'];
                    AvisoSinResultados.style = "display: none;"
                }else{
                    AvisoSinResultados.style = "display: flex;"
                }
                if(DatosRecibidos.objetos.length > 1){
                    CotizacionesTotales = DatosRecibidos.objetos[1]['contador'] + CotizacionesTotales;
                }
                if(DatosRecibidos.objetos.length > 2){
                    CotizacionesTotales = DatosRecibidos.objetos[2]['contador'] + CotizacionesTotales;
                }
                if(DatosRecibidos.objetos.length > 3){
                    CotizacionesTotales = DatosRecibidos.objetos[3]['contador'] + CotizacionesTotales;
                }
                
    
                
                if(DatosRecibidos.objetos.length > 0){
                    
                    CotizacionesBarra1 = DatosRecibidos.objetos[0]['contador'];
                    PorcentajeBarra1 = CotizacionesBarra1 * 100 / CotizacionesTotales;
                    PorcentajeBarra1 = (Number.isInteger(PorcentajeBarra1))?PorcentajeBarra1:PorcentajeBarra1.toFixed(2);
                    
                    Barra1.style = "height: " +PorcentajeBarra1+ "%;";
                    Barra1.innerHTML = `
                        <span class="NombreDeBarra">${DatosRecibidos.objetos[0]['cliente']}</span>
                        <div class="InfoDeLaBarra">
                            <span class="Porcentaje">${PorcentajeBarra1}%</span>
                            <span class="Descripcion">${CotizacionesBarra1} cotizaciones</span>
                            <div class="Flechita"></div>
                        </div>
                    `;
                    
                }else{
                    
                    Barra1.style = "height: 0%;";
                    Barra1.innerHTML = `
                        <span class="NombreDeBarra"></span>
                    `;
                }
    
                if(DatosRecibidos.objetos.length > 1){
                    CotizacionesBarra2 = DatosRecibidos.objetos[1]['contador'];
                    PorcentajeBarra2 = CotizacionesBarra2 * 100 / CotizacionesTotales;
                    PorcentajeBarra2 = (Number.isInteger(PorcentajeBarra2))?PorcentajeBarra2:PorcentajeBarra2.toFixed(2);
                    
                    Barra2.style = "height: " +PorcentajeBarra2+ "%;";
                    Barra2.innerHTML = `
                        <span class="NombreDeBarra">${DatosRecibidos.objetos[1]['cliente']}</span>
                        <div class="InfoDeLaBarra">
                            <span class="Porcentaje">${PorcentajeBarra2}%</span>
                            <span class="Descripcion">${CotizacionesBarra2} cotizaciones</span>
                            <div class="Flechita"></div>
                        </div>
                    `;
                }else{
                    
                    Barra2.style = "height: 0%;";
                    Barra2.innerHTML = `
                        <span class="NombreDeBarra"></span>
                    `;
                }
    
                if(DatosRecibidos.objetos.length > 2){
                    CotizacionesBarra3 = DatosRecibidos.objetos[2]['contador'];
                    PorcentajeBarra3 = CotizacionesBarra3 * 100 / CotizacionesTotales;
                    PorcentajeBarra3 = (Number.isInteger(PorcentajeBarra3))?PorcentajeBarra3:PorcentajeBarra3.toFixed(2);
                    
                    Barra3.style = "height: " +PorcentajeBarra3+ "%;";
                    Barra3.innerHTML = `
                        <span class="NombreDeBarra">${DatosRecibidos.objetos[2]['cliente']}</span>
                        <div class="InfoDeLaBarra">
                            <span class="Porcentaje">${PorcentajeBarra3}%</span>
                            <span class="Descripcion">${CotizacionesBarra3} cotizaciones</span>
                            <div class="Flechita"></div>
                        </div>
                    `;
                }else{
                    
                    Barra3.style = "height: 0%;";
                    Barra3.innerHTML = `
                        <span class="NombreDeBarra"></span>
                    `;
                }
    
                if(DatosRecibidos.objetos.length > 3){
                    CotizacionesBarra4 = DatosRecibidos.objetos[3]['contador'];
                    PorcentajeBarra4 = CotizacionesBarra4 * 100 / CotizacionesTotales;
                    PorcentajeBarra4 = (Number.isInteger(PorcentajeBarra4))?PorcentajeBarra4:PorcentajeBarra4.toFixed(2);
                    
                    Barra4.style = "height: " +PorcentajeBarra4+ "%;";
                    Barra4.innerHTML = `
                        <span class="NombreDeBarra">${DatosRecibidos.objetos[3]['cliente']}</span>
                        <div class="InfoDeLaBarra">
                            <span class="Porcentaje">${PorcentajeBarra4}%</span>
                            <span class="Descripcion">${CotizacionesBarra4} cotizaciones</span>
                            <div class="Flechita"></div>
                        </div>
                    `;
                }else{
                    
                    Barra4.style = "height: 0%;";
                    Barra4.innerHTML = `
                        <span class="NombreDeBarra"></span>
                    `;
                }
                
            }else{
                //No hay clientes registrados
                AvisoSinResultados.style = "display: flex;"
                Barra1.style = "height: 0%;";
                Barra2.style = "height: 0%;";
                Barra3.style = "height: 0%;";
                Barra4.style = "height: 0%;";
                Barra1.innerHTML = `
                    <span class="NombreDeBarra"></span>
                `;
                Barra2.innerHTML = `
                    <span class="NombreDeBarra"></span>
                `;
                Barra3.innerHTML = `
                    <span class="NombreDeBarra"></span>
                `;
                Barra4.innerHTML = `
                    <span class="NombreDeBarra"></span>
                `;

            }
            
            
        }else{
            alert('No se ha obtenidorespuesta al consultar los clientes mas activos. Respuesta: '+respuesta.status);
        }
    }catch(error){
        Toast.fire({
            title: 'warning',
            title: 'Algo ha salido mal al cargar los gráficos estadísticos'
        });
        console.log(error);
    }
    
}

let ConsultarNroDeCotizacionesPorEstado = async(Anio, Mes) =>{
    var CantidadAceptadas = 0;
    var CantidadRechazadas = 0;
    var CantidadEnEspera = 0;
    var CantidadVencidas = 0;

    var PorcentajeAceptadas = 0;
    var PorcentajeRechazadas = 0;
    var PorcentajeEnEspera = 0;
    var PorcentajeVencidas = 0;
    
    try{
        
        let respuesta1 = await fetch("http://"+ipserver+"/CleoInventory/API/API_Cotizaciones.php?mes="+Mes+"&anio="+Anio+"&estado=31");
        if(respuesta1.status === 200){
            DatosRecibidos = await respuesta1.json();

            
            try{
                CantidadAceptadas = DatosRecibidos.objetos.length;
            }catch(e){
                CantidadAceptadas = 0;
            }
        }else{
            alert("No se ha obtenido respuesta al consultar el numero de cotizaciones aceptadas. Respuesta: "+respuesta1.status)
        }


        let respuesta2 = await fetch("http://"+ipserver+"/CleoInventory/API/API_Cotizaciones.php?mes="+Mes+"&anio="+Anio+"&estado=32");
        if(respuesta2.status === 200){
            DatosRecibidos = await respuesta2.json();

            try{
                CantidadRechazadas = DatosRecibidos.objetos.length;
            }catch(e){
                CantidadRechazadas = 0;
            }
        }else{
            alert("No se ha obtenido respuesta al consultar el numero de cotizaciones rechazadas. Respuesta: "+respuesta2.status)
        }


        let respuesta3 = await fetch("http://"+ipserver+"/CleoInventory/API/API_Cotizaciones.php?mes="+Mes+"&anio="+Anio+"&estado=33");
        if(respuesta3.status === 200){
            DatosRecibidos = await respuesta3.json();

            try{
                CantidadEnEspera = DatosRecibidos.objetos.length;
            }catch(e){
                CantidadEnEspera = 0;
            }
        }else{
            alert("No se ha obtenido respuesta al consultar el numero de cotizaciones en espera. Respuesta: "+respuesta3.status)
        }


        let respuesta4 = await fetch("http://"+ipserver+"/CleoInventory/API/API_Cotizaciones.php?mes="+Mes+"&anio="+Anio+"&estado=34");
        if(respuesta4.status === 200){
            DatosRecibidos = await respuesta4.json();

            try{
                CantidadVencidas = DatosRecibidos.objetos.length;
            }catch(e){
                CantidadVencidas = 0;
            }
        }else{
            alert("No se ha obtenido respuesta al consultar el numero de cotizaciones vencidas. Respuesta: "+respuesta4.status)
        }

        

        
        if(CantidadAceptadas || CantidadRechazadas || CantidadEnEspera || CantidadVencidas){
            AvisoSinResultados.style = "display: none;"
            
            TotalDeResultados = CantidadAceptadas + CantidadRechazadas + CantidadEnEspera + CantidadVencidas;
            console.log('resultados encontrados: '+TotalDeResultados)
            PorcentajeAceptadas = CantidadAceptadas * 100 / TotalDeResultados;
            PorcentajeRechazadas = CantidadRechazadas * 100 / TotalDeResultados;
            PorcentajeEnEspera = CantidadEnEspera * 100 / TotalDeResultados;
            PorcentajeVencidas = CantidadVencidas * 100 / TotalDeResultados;



            Barra1.style = "height: "+PorcentajeAceptadas+"%;";
            Barra2.style = "height: "+PorcentajeRechazadas+"%;";
            Barra3.style = "height: "+PorcentajeEnEspera+"%;";
            Barra4.style = "height: "+PorcentajeVencidas+"%;";


            Barra1.innerHTML = `
                <span class="NombreDeBarra">Aceptadas</span>
                <div class="InfoDeLaBarra">
                    <span class="Porcentaje">${((Number.isInteger(PorcentajeAceptadas))?PorcentajeAceptadas:PorcentajeAceptadas.toFixed(2))}%</span>
                    <span class="Descripcion">${CantidadAceptadas} cotizaciones</span>
                    <div class="Flechita"></div>
                </div>
            `;

            Barra2.innerHTML = `
                <span class="NombreDeBarra">Rechazadas</span>
                <div class="InfoDeLaBarra">
                    <span class="Porcentaje">${((Number.isInteger(PorcentajeRechazadas))?PorcentajeRechazadas:PorcentajeRechazadas.toFixed(2))}%</span>
                    <span class="Descripcion">${CantidadRechazadas} cotizaciones</span>
                    <div class="Flechita"></div>
                </div>
            `;

            Barra3.innerHTML = `
                <span class="NombreDeBarra">En espera</span>
                <div class="InfoDeLaBarra">
                    <span class="Porcentaje">${((Number.isInteger(PorcentajeEnEspera))?PorcentajeEnEspera:PorcentajeEnEspera.toFixed(2))}%</span>
                    <span class="Descripcion">${CantidadEnEspera} cotizaciones</span>
                    <div class="Flechita"></div>
                </div>
            `;

            Barra4.innerHTML = `
                <span class="NombreDeBarra">Vencidas</span>
                <div class="InfoDeLaBarra">
                    <span class="Porcentaje">${((Number.isInteger(PorcentajeVencidas))?PorcentajeVencidas:PorcentajeVencidas.toFixed(2))}%</span>
                    <span class="Descripcion">${CantidadVencidas} cotizaciones</span>
                    <div class="Flechita"></div>
                </div>
            `;


        }else{
            Barra1.innerHTML = "";
            Barra1.style = "height: 0%;";
            Barra2.innerHTML = "";
            Barra2.style = "height: 0%;";
            Barra3.innerHTML = "";
            Barra3.style = "height: 0%;";
            Barra4.innerHTML = "";
            Barra4.style = "height: 0%;";
            
            AvisoSinResultados.style = "display: flex;"

        }
        

    }catch(error){
        Toast.fire({
            title: 'warning',
            title: 'Algo ha salido mal al cargar los gráficos estadísticos'
        });
        console.log(error);
    }
}

window.addEventListener('load', ConsultarNroDeCotizacionesPorEstado(SelectAnioDeGrafico.value, SelectMesDeGrafico.value));


let BotonAnteriorGrafico = document.getElementById('BotonAnteriorGrafico');
let BotonSiguienteGrafico = document.getElementById('BotonSiguienteGrafico');

var NroGraficoEnPantalla = 1;

BotonAnteriorGrafico.addEventListener('click', () => {

})

let TitulosDeGraficos = document.getElementById('TitulosDeGraficos');

BotonSiguienteGrafico.addEventListener('click', () => {
    
    if(NroGraficoEnPantalla == 1){
        CargarGrafico2();
        NroGraficoEnPantalla = 2;
        BotonSiguienteGrafico.classList = "";
        BotonAnteriorGrafico.classList = "CambiarGraficoDisponible";
        SelectAnioDeGrafico.style = "display: none;";
        SelectMesDeGrafico.style = "display: none;";
    }
})

BotonAnteriorGrafico.addEventListener('click', () => {
    
    if(NroGraficoEnPantalla == 2){
        CargarGrafico1();
        NroGraficoEnPantalla = 1;
        BotonAnteriorGrafico.classList = "";
        BotonSiguienteGrafico.classList = "CambiarGraficoDisponible";
        SelectAnioDeGrafico.style = "display: block;";
        SelectMesDeGrafico.style = "display: block;";
    }
})

function CargarGrafico1(){
    TitulosDeGraficos.classList = "";
    ConsultarNroDeCotizacionesPorEstado(SelectAnioDeGrafico.value, SelectMesDeGrafico.value)
}

function CargarGrafico2(){
    
    TitulosDeGraficos.classList = "MostrarGrafico2";
    ConsultarClientesMasActivos(SelectAnioDeGrafico.value, SelectMesDeGrafico.value);
}


