const Input_busqueda = document.getElementById('Input_busqueda');
const Button_busqueda = document.getElementById('Button_busqueda');

Input_busqueda.addEventListener('keyup', function(e){
    if(e.keyCode == 13){
        Button_busqueda.click();
    }
})


window.addEventListener('load', function(){
    this.document.querySelector('.spaceSelectInputs').querySelectorAll('select').forEach( select => {
        select.addEventListener('change', function(){
            Button_busqueda.click();
        })
    })

    this.document.querySelectorAll('row').forEach( row => {
        row.addEventListener('click', function(){
            lol = document.querySelectorAll('.ProductoSeleccionado');
            if(lol != undefined){
                lol.forEach( xd => {
                    xd.classList.remove('ProductoSeleccionado');
                })
            }
            row.classList.add('ProductoSeleccionado');

            console.log(row);
            
            
            RegistroSeleccionado = document.querySelector('.RegistroSeleccionado');

            var identidad = row.getAttribute('identidad');
            var entityName = '';
            var imagen = row.getAttribute('imagenurl');
            var hacheref = '';
            switch(row.getAttribute('tipodeentidad')){
                case '1':
                    entityName = 'Producto';
                    hacheref = 'Productos/Producto/?id='+identidad;
                break;

                case '2':
                    entityName = 'Proveedor';
                    hacheref = 'Proveedores/Proveedor/?rif='+identidad;
                break;

                case '3':
                    entityName = 'Cliente';
                    hacheref = 'Clientes/Cliente/?rif='+identidad;
                break;

                case '4':
                    entityName = 'Venta';
                    hacheref = 'Ventas/Venta/?id='+identidad;
                break;

                case '5':
                    entityName = 'Usuario';
                break;

                case '6':
                    entityName = 'Almacén';
                    hacheref = 'Almacenes/Almacen/?id='+identidad;
                break;

                case '7':
                    entityName = 'Ajuste de inventario';
                    hacheref = 'Inventario/Cambios/?descripcion='+identidad+'&tipo=0';
                break;

                case '8':
                    entityName = 'Compra';
                    hacheref = 'Compras/Compra/?id='+identidad;
                break;
                
                default:
            }

            var color = '';
            var explicacion = '';
            switch(row.getAttribute('huella')){
                case '1':
                    color = 'verde';
                    explicacion = 'Creado';
                break;

                case '2':
                    color = 'amarillo';
                    explicacion = 'Modificado';
                break;

                case '3':
                    color = 'gris';
                    explicacion = 'Eliminado';
                break;
            }


            if(row.getAttribute('entidadvisible')=='true' && entityName && identidad){
                RegistroSeleccionado.innerHTML = `
                <img src="${imagen}" alt="" ${(row.getAttribute('extraStyleP')=='true'? 'style="padding: 5px; width: 60px; height: 60px;"':'')}>
                <div class="DatosRegistroSeleccionado">
                    <p>${entityName} #${identidad}</p>
                    <span class="SpanTipo" ${(explicacion == 'Eliminado'? 'style="transform: translateX(20px);"':'')}><div class="circulito ${color}"></div> ${explicacion}</span>
                    ${(row.getAttribute('huella')<3? '<a target="_blank" href="../'+hacheref+'">Ver</a>':'')}
                </div>
                `;
            }else{
                RegistroSeleccionado.innerHTML = `
                <div class="Vacio">
                    No hay información a mostrar
                </div>
                `;
            }
        })
    })
})