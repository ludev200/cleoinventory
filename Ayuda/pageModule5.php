<div class="pageModule" id="5">
    <h3 class="moduleTitle" id="titulo5">
        <img src="../Imagenes/iconoDelMenu_Productos.png" alt="">
        <strong>1 - PRODUCTOS</strong>
    </h3>
    <p>
        Un producto, en el contexto de un software de inventario, se refiere a un artículo físico o digital que se encuentra 
        en el inventario de una empresa o entidad. Puede ser cualquier artículo tangible como equipos, materiales, productos 
        terminados, o intangible como licencias de software, documentos digitales, etc. En CLEO INVENTORY, al igual que en 
        cualquier otro software de inventario, los productos son la base sobre la que se construye el inventario, las entradas 
        y las salidas del sistema.
    </p>
    <h4 id="6">1.1 - Categorías de productos</h4><span id="indice1"></span>
    <p id="7">
        En CLEO INVENTORY los productos se clasifican en cuatro categorías diferentes y cada una cuenta con un comportamiento 
        distintivo. <b id="productos-consumibles">Los productos consumibles</b> son lo que cuentan con una existencia física en el inventario, es decir, estos 
        pueden realizar una salida o entrada del inventario; a diferencia de los productos no consumibles, los cuales, a pesar 
        de no existir en el inventario, forman parte del catálogo de productos y pueden ser considerados al momento de realizar 
        una venta o cotización.
    </p>
    <ul>
        <li id="8"><b id="17">1.1.1 - Material:</b> Es un producto consumible, está asociado a una <b>unidad de medida</b> que lo 
        cuantifica (metros, kilogramos, unidades) y cuenta con un <b id="18">nivel de alerta</b> 
        que establece un margen de existencia en el inventario que determina la suficiente existencia del mismo.</li>
        <li id="9"><b>1.1.2 - Equipo:</b> Comprende los productos de equipamiento y maquinaria, producto consumible cuya existencia 
        puede ser decimal ya que la utilización de estos comprende un desgaste o depreciación.</li>
        <li id="10"><b>1.1.3 - Mano de obra:</b> Producto no consumible, representa el recurso humano que puede ser cotizado 
        (indicando el número de días y de personas) en un presupuesto o al realizar una venta, añadiendo un costo asociado 
        al salario al total, este CAS es indicado por el usuario en forma de porcentaje.</li>
        <li id="11"><b>1.1.4 - Comida:</b> Producto no consumible, representa el alimento del personal obrero que puede ser añadido 
        en el presupuesto, es cotizado indicando el número de días y de personas. Nota: Este producto no interviene en el CAS.</li>
    </ul>
    <h4 id="12">1.2 - Elementos de la pantalla inicial del módulo productos</h4><span id="indice2"></span>
    <ul>
        <li><b>A - Barra de búsqueda:</b> Permite buscar productos según su ID, nombre o descripción, cuyos resultados aparecerán
        ordenados según la característica seleccionada (nombre, precio o estado).</li>
        <li><b>B - Filtros:</b> Agrega filtros a tu búsqueda para obtener productos de una categoría o un estado específico.</li>
        <li><b>C - Caja de productos:</b> Productos obtenidos como resultado de la búsqueda.</li>
        <li><b>D - Menú lateral:</b> Contiene el botón para agregar un nuevo producto al catálogo.</li>
    </ul>
    <img src="../Imagenes/manual/productos_1.png" alt="">
    <h4 id="13">1.3 Registro de productos</h4><span id="indice3"></span>
    <p>
        Accede al módulo productos, ubícate en el menú lateral y da clic al botón <b>Nuevo producto</b>. Llena los campos del formulario 
        y seguidamente haz clic en el botón guardar. Una vez que indiques la categoría, el formulario se adaptará, mostrando los campos 
        necesarios para el registro del producto. Nota: Como se puede observar, al momento de registrar un producto nuevo, solo se agrega 
        al catálogo, mas no se indica la cantidad existente en el inventario, esta acción comprende a los módulos 
        <a href="#31">Inventario</a> y <a href="#41">Compras</a>.
    </p>
    <ul>
        <li><b>A - Formulario:</b> Contiene los datos del producto a registrar, incluyendo, imagen, nombre, categoría, precio, 
        unidad de medida, nivel de alerta, descripción y la lista de proveedores de dicho producto.</li>
        <li><b>B - Botones de acción:</b> Botón para salir o guardar el producto con los datos indicados.</li>
        <li><b>C - Menú lateral:</b> Contiene un botón para dirigirse al manual y obtener ayuda sobre los productos y otro botón
        para salir sin guardar.</li>
    </ul>
    <img src="../Imagenes/manual/productos_2.png">
    <h4>1.4 Perfil detallado de un producto</h4><span id="indice4"></span>
    <p>
        Aquí se presentan los detalles del producto, incluyendo el ID (identificador), nombre, precio, categoría, unidad de 
        medida (usada para cuantificar) la existencia del mismo, nivel de alerta y descripción. Nota: la característica 'unidad 
        de medida' es aprovechada solo por los productos materiales, y solo estos junto con los productos de equipo, son los 
        considerados como <span hook="productos-consumibles" class="hooker">productos consumibles</span>.
    </p>
    <ul>
        <li id="14">
            <b>A - Tabla de disposición:</b> Indica la cantidad existente del producto en cada uno de los almacenes registrados
            , además de incluir un acceso directo al perfil detallado del almacén en el módulo de <a href="#22">almacenes</a>.
        </li>
        <li>
            <b>B - Tabla de proveedores:</b> Contiene los proveedores que proveen/disponen de este producto, indicando su respectiva 
            información básica y un acceso directo a su perfil detallado en el módulo de <a href="#54">proveedores</a>.
        </li>
        <li id="15">
            <b id="16">C - Menú lateral:</b> Permite las acciones de modificar o eliminar el producto.
        </li>
    </ul>
    <img src="../Imagenes/manual/productos_3.png">
</div>