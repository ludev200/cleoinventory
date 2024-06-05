<div class="pageModule" id="41">
    <h3 class="moduleTitle" id="titulo6">
        <img src="../Imagenes/iconoDelMenu_Compras.png">
        <strong>4 - COMPRAS</strong>
    </h3>
    <p>
        Las compras del sistema son el principal método de entrada de producto al inventario. CLEO INVENTORY permite al usuario 
        la creación de <b id="42">órdenes de compra</b>, proceso opcional y previo al registro de compras. La realización de una orden de compra es 
        solo un listado de lo que se aspira comprar, de esta forma el sistema otorga una lista de los proveedores disponibles para 
        los productos seleccionados, por otra parte, el registro de una compra se puede realizar indicando los productos comprados o 
        desde una orden de compra previamente generada.
    </p>
    <h4>4.1 - ELEMENTOS DE LA PANTALLA INICIAL DEL MÓDULO COMPRAS</h4><span id="indice37"></span>
    <ul>
        <li><b>A - Acción principal: </b>Botones de llamada a la acción, contiene el acceso a crear una nueva orden de compra y 
        el de registrar una compra.</li>
        <li id="44"><b id="47">B - Lista de compras: </b>presenta las compras/órdenes de compra con la cantidad de productos que las comprende y el
        estado en el que se encuentran.</li>
        <li><b>C - Menú lateral: </b>contiene botones de acceso directo a <span hook="43" class="hooker">crear nueva orden de compra</span>, <span hook="49" class="hooker">registrar una compra</span>
        y <b>buscar compra</b>.</li>
    </ul>
    <img src="../Imagenes/manual/compras_1.png">
    <h4 id="43">4.2 - CREAR ORDEN DE COMPRA</h4><span id="indice38"></span>
    <p>
        Accede al módulo compras y da clic al botón <b>crear nueva orden</b>. Selecciona los productos y seguidamente haz clic 
        en el botón guardar. La creación de una orden de compra <b>no</b> representa una entrada de productos, es decir, <b>no</b> 
        incrementa la existencia de los productos seleccionados. Nota: al crear una orden de compra, si existe uno o más productos cuya 
        existencia sea menor o igual que su respectivo nivel de alerta, se contará con la opción de <b>crear automáticamente una 
        orden de compra</b> recomendada por el sistema.
    </p>
    <ul>
        <li><b>A - Cuerpo de orden de compra: </b>representa la orden en sí, con un nombre referencial, límite de tiempo vigente 
        (opcional) y los productos con su respectiva cantidad.</li>
        <li><b>B - Lista de proveedores: </b>indica los proveedores disponibles para la orden de compra indicada, cada uno con los 
        productos que proveen de los indicados.</li>
        <li><b>C - Botones de acción: </b>permite volver a la <b>pantalla inicial del módulo de compras</b> o guardar la orden de 
        compra indicada.</li>
        <li><b>D - Menú lateral: </b>contiene un botón <b>Agregar producto</b> utilizado para abrir la ventana de selección de productos 
        desde la que podemos agregar productos a la orden de compra.</li>
        <li><b>E - Aviso de alerta: </b>presenta la cantidad de productos agotados y productos en alerta,
        y ofrece la opción de crear una orden de compra recomendada por el sistema.</li>
    </ul>
    <img src="../Imagenes/manual/compras_2.png">
    <h4>4.3 - DETALLES DE UNA COMPRA/ORDEN DE COMPRA</h4><span id="indice39"></span>
    <p>
        Esta pantalla indica la información detallada de una compra u orden de compra (compra en espera), incluyendo el nombre 
        referencial, el estado de la compra, número de productos enlistados, el tiempo de vigencia y el ID de la compra, además de 
        la lista de los productos con sus respectivas cantidades, y finalmente presenta los proveedores disponibles para la orden de compra.
    </p>
    <ul>
        <li><b>A - Caja de proveedores: </b>presenta los proveedores disponibles, indicando los productos enlistados proveídos 
        por cada uno.</li>
        <li><b>B - Botones de respuesta: </b>permite dar respuesta a la orden de compra como <b>rechazada</b> o <b>confirmada</b>.</li>
        <li><b>C - Menú lateral: </b>contiene un botón para <b id="48">imprimir un reporte PDF de la compra</b> y cuatro botones adicionales 
        que solo están disponibles para las órdenes de compra en espera, los cuales permiten <b id="45">modificar</b>, <b id="46">eliminar</b>, <b id="50">rechazar</b> 
        o <span class="hooker" hook="49">confirmar la compra</span>.</li>
    </ul>
    <img src="../Imagenes/manual/compras_3.png">
    <h4 id="49">4.4 - CONFIRMAR UNA ORDEN DE COMPRA / REGISTRAR UNA COMPRA</h4><span id="indice40"></span>
    <p>
        Accede al módulo compras y da clic en <b>Registrar compra</b>. Al registrar una compra, se realiza una entrada de productos en 
        el inventario. Esta compra puede preceder de una orden de compra creada previamente. CLEO INVENTORY es un software que cuenta 
        con la posibilidad de distribuir el inventario en múltiples almacenes. Por lo tanto, el proceso de compra se realiza siguiendo 
        dos pasos
    </p>
    <p>
        <b>PASO 1:</b> En este se indican los productos y las respectivas cantidades compradas, lo cual se puede realizar de forma 
        individual o se puede <b>importar una orden de compra</b> creada previamente, esta puede ser modificada si así se requiere.
    </p>
    <ul>
        <li><b>A - Cuerpo de la compra: </b>incluye el nombre referencial (descripción) de la compra y la lista de productos comprados con 
        sus respectivas cantidades.</li>
        <li><b>B - Botón de salida: </b>Botón para salir sin guardar ningún cambio. Regresa a la pantalla inicial del módulo de compras.</li>
        <li><b>C - Botones de selección: </b>Incluye un botón para abrir la ventana de selección de producto donde es posible agregar 
        productos a la compra; y un botón para abrir la ventana de selección de orden de compra que permite importar una orden de compra 
        la cual puede ser modificada a selección del usuario.</li>
        <li><b>D - Botón continuar: </b>permite proceder al <b>paso 2</b> del registro de compra.</li>
    </ul>
    <img src="../Imagenes/manual/compras_4.png">
    <b>PASO 2:</b>
    <p>
        Para concluir con el registro de una compra, se especifica el almacenaje de los productos, es decir, se indica en cual 
        almacén, se alojarán los productos comprados con sus respectivas cantidades.
    </p>
    <ul>
        <li><b>A - Lista de productos comprados: </b>presenta los productos agregados a la compra con un botón para abrir la ventana de 
        selección de almacén, además de indicar la cantidad ya almacenada en comparación con la cantidad comprada.</li>
        <li><b>B - Botón volver: </b> permite volver al paso 1</li>
        <li><b>C - Botoón de almacén predeterminado: </b> permite almacenar todos los productos automáticamente en el <span hook="23" class="hooker">almacén 
        predeterminado</span>.</li>
        <li><b>D - Lista de almacenaje: </b>muestra el almacenaje de los productos indicado por el usuario, junto con el almacén de 
        destino la cantidad de cada producto y la cantidad total luego del almacenaje.</li>
        <li><b>E - Botón para guardar: </b>Guarda los cambios, confirmando así la compra en el sistema.</li>
    </ul>
    <img src="../Imagenes/manual/compras_5.png">
</div>