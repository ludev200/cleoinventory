<div class="pageModule" id="31">
    <h3 class="moduleTitle" id="titulo4">
        <img src="../Imagenes/iconoDelMenu_Inventario.png">
        <strong>3 - INVENTARIO</strong>
    </h3>
    <p>
        El inventario es núcleo del sistema, este módulo gestiona la cantidad de <span hook="productos-consumibles" class="hooker">productos consumibles</span>
        existentes en los almacenes. La cantidad de unidades de cada producto determina el estado del mismo, de tal manera que: si 
        la existencia de un producto es menor o igual al <span hook="18" class="hooker">nivel de alerta</span> definido para el mismo, se 
        considera como <b id="34">producto en alerta</b>; si la existencia es equivalente a cero, se considera como 
        <b id="32">producto agotado</b>; de cualquier otra manera, se considera como <b id="33">producto disponible</b>.
    </p>
    <h4>3.1 - ELEMENTOS DE LA PANTALLA INICIAL DEL MÓDULO INVENTARIO</h4><span id="indice28"></span>
    <ul>
        <li><b>A - Barra de búsqueda: </b>permite buscar productos en el inventario por nombre o ID con la posibilidad de filtrar 
        por estado (disponible, en alerta o agotado).</li>
        <li><b>B - Lista de productos: </b>indica los productos y su existencia, además de mostrar una alerta en caso de que este 
        se encuentre agotado o en estado de alerta. Incluye un acceso directo al perfil detallado de cada producto.</li>
        <li><b>C - Menú lateral: </b>contiene un botón para ver el historial de cambios del inventario y un botón para modificar 
        el inventario, el cual nos presenta 3 opciones: <span hook="35" class="hooker">realizar ajuste de inventario</span>, <span hook="69" class="hooker">registrar venta</span>
        y <span hook="49" class="hooker">registrar compra</span>.</li>
        <li><b>D - Aviso de alerta: </b>Este aviso se presenta cuando uno o más productos del inventario ha alcanzado su nivel de 
        alerta o se ha agotado, e indica el número de dichos productos, además, ofrece la opción de generar una <b>orden de compra 
        recomendada</b> en el módulo de <a href="#41">compras</a>.</li>
    </ul>
    <img src="../Imagenes/manual/inventario_1.png">
    <h4 id="35">3.2 - REALIZA UN AJUSTE DE INVENTARIO</h4><span id="indice29"></span>
    <p>
        Accede al módulo inventario, ubícate en el menú lateral, da clic al botón <b>Modificar inventario</b> y selecciona la opción 
        <b>ajuste de inventario</b>. Indica los cambios a realizar, añade una descripción que pueda ofrecer un contexto del ajuste a 
        realizar y da clic a <b>Guardar cambios</b>.
    </p>
    <ul>
        <li><b>A - Botón de visualización de productos: </b>permite alternar la visualización de los productos en los inventarios 
        de los almacenes, mostrando u ocultado los productos que nunca hayan sido almacenados en estos.</li>
        <li><b>B - Contenedor de almacenes: </b>presenta una carta por cada almacén en el sistema, indicando su nombre, dirección 
        y una lista con sus productos contenidos, cada producto en la lista presenta la cantidad existente en dicho almacén, la 
        cual puede ser modificada lo que habilita dos botones: uno que permite volver a la cantidad original, y otro que agrega el
        cambio anotado a la <b>lista de cambios por realizar</b>.</li>
        <li><b>C - Lista de cambios por realizar: </b>Contiene la descripción del ajuste y la lista de cambios a realizar, 
        indicando la cantidad de cada producto a modificar y el almacén correspondiente.</li>
        <li><b>D - Botones de acción: </b> Contiene el botón para volver atrás y el botón para guardar los cambios indicados.</li>
    </ul>
    <img src="../Imagenes/manual/inventario_2.png">
    <h4 id="36">3.3 - HISTORIAL DE CAMBIOS DEL INVENTARIO</h4><span id="indice30"></span>
    <p>
        Los cambios realizados al inventario quedan registrados y pueden ser observados por cualquier usuario con acceso al módulo clientes. 
        Este historial permite tener un registro no solo de cada cambio que se realiza sino también de cómo y que usuario lo realiza.
    </p>
    <ul>
        <li><b>A - Barra de búsqueda: </b>permite filtrar los resultados por nombre, descripción y/o tipo de movimiento.</li>
        <li><b>B - Resultados de búsqueda: </b>muestra los movimientos resultados de la búsqueda, indicando los productos alterados 
        y su resultado en el inventario, además de indicar la fecha de realización y el usuario responsable.</li>
        <li><b>C - Menú lateral: </b> presenta un botón para volver al inventario.</li>
    </ul>
    <img src="../Imagenes/manual/inventario_3.png">
</div>
