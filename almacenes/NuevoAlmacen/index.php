<?php
session_start();

include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();



//Reviso si hay en el borrador
$ListaDeBorrador = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE `idEstado` = 53");
$ExisteBorrador = !empty($ListaDeBorrador);
if($ExisteBorrador){
    $AlmacenEnBorrador = new almacen($ListaDeBorrador[0]['id']);
    $DatosDeAlmacenEnBorrador = $AlmacenEnBorrador->ObtenerDatos();
}else{
    $AlmacenFantasma = new almacen(0);
}

$DatosDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `almacenes` ORDER BY `id` DESC LIMIT 0,1");
//Inicializo los datos predeterminados a mostrar en los inputs
$DatosAMostrar = array(
    'nombre' => 'Almacén #'.((empty($DatosDeConsulta))?'1':($DatosDeConsulta[0]['id'] + 1)),
    'direccion' => '',
    'ProductosDelAlmacen' => ''
);

if($_POST){
    //Almaceno los datos recibidos por el formulario
    $DatosAMostrar  = array(
        'nombre' => $_POST['Nombre'],
        'direccion' => $_POST['Direccion'],
        'ProductosDelAlmacen' => $_POST['ProductosDelAlmacen']
    );


    if(isset($_POST['Guardar'])){
        try{
            if($ExisteBorrador){
                $Problemas = $AlmacenEnBorrador->ActualizarDatosDeAlmacen($_POST, 51);
            }else{
                $Problemas = $AlmacenFantasma->CrearNuevoAlmacen($_POST, 51);
            }
        }catch(Exception $Error){
            $Problemas = $Error->getMessage();
        }
        
        if(empty($Problemas)){
            $ConsultaDeUltimoID = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE `idEstado` = 51 ORDER BY `id` DESC LIMIT 0,1");
            header("Location: ../Almacen?id=".$ConsultaDeUltimoID[0]['id']);
        }
    }


    if(isset($_POST['Borrador'])){
        try{
            if($ExisteBorrador){
                $Problemas = $AlmacenEnBorrador->ActualizarDatosDeAlmacen($_POST, 53);
            }else{
                $Problemas = $AlmacenFantasma->CrearNuevoAlmacen($_POST, 53);
            }
        }catch(Exception $Error){
            $Problemas = $Error->getMessage();
        }
        
        if(empty($Problemas)){
            header("Location: ../");
        }
    }
}else{
    if($ExisteBorrador){
        $DatosAMostrar = array(
            'nombre' => $DatosDeAlmacenEnBorrador['nombre'],
            'direccion' => $DatosDeAlmacenEnBorrador['direccion'],
            'ProductosDelAlmacen' => $DatosDeAlmacenEnBorrador['ProductosDelAlmacen']
        );
    }
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Nuevo almacén</title>
    <link rel="stylesheet" href="estilos_NuevoAlmacen.css">
    <?php include('../../Otros/cabecera_N3.php');?>
    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../" class="Barra">
            <p>Almacenes</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p>Nuevo</p>
            <div class="Cuadrito" href=""></div>
        </a>
    </div>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <label id="ModalDeErroresDelPOST" class="ModalDeError">
        <input hidden <?php echo ((empty($Problemas))?"":"checked");?> type="checkbox" name="" id="CheckMostrarModalDeError">
        <div class="TarjetaDeWarning">
            <img class="TextoCentro" src="../../Imagenes/TrianguloDeAdvertencia.png" alt="">
            <b class="TextoCentro">No se puede guardar</b>
            <p>Se han encontrado errores que impiden continuar. Rectifique e intentelo de nuevo.</p>
            <b class="TextoIzquierda">Errores:</b>
            <div class="TextoCentro CajaDeErrores">
                <?php
                    if(!empty($Problemas)){
                        foreach(explode("¿", $Problemas) as $ErrorDeFormato){
                            echo "<span>".$ErrorDeFormato.((empty($ErrorDeFormato))?"":".")."</span>";
                        }
                    }
                ?>
            </div>
        </div>
    </label>
    <modal id="ModalAgregarProducto">
        <div id="VentadaModalAgregarProducto" class="VentanaFlotante OcultarModal">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentanaProductos" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">AGREGAR PRODUCTOS</b>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorProductos">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="BuscadorDeProductos" type="text" placeholder="Busca por ID, nombre o descripcion...">
                            <button type="button" id="BotonBuscarProductos"> <i class="fi-rr-search"></i> </button>
                            <select id="SelectCategoriaABuscar" id="SelectCategoriaDelProductoABuscar">
                                <option value="0">Todos</option>
                                <option value="1">Material</option>
                                <option value="2">Equipo</option>
                            </select>
                        </div>
                        <div class="TablaDeProdcutos">
                            <div class="ColumnasDeTabla">
                                <celda class="ColumnaImagen">Imagen</celda>
                                <celda class="ColumnaID">ID</celda>
                                <celda class="ColumnaNombre2">Nombre</celda>
                                <celda class="ColumnaExistencia">Existencia</celda>
                            </div>
                            <div class="ContenedorDelFlexDeRows">
                                <div id="ListaDeProductosConsultados" class="EspacioDeRows">
                                    <div class="did_loading">
                                        <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                                        Cargando
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <input hidden id="InputProductoAPrevisualizar" type="text">
                    <div class="EspacioDePrevisualizacionDeProducto" id="PrevisualizacionDeProducto">
                        <div class="ProductoNoSeleccionado">
                            <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                            <span>Seleccione un producto</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <article>
        <form id="FormularioNuevoAlmacen" autocomplete="off" method="post">
            <div class="ContenedorDelInputNombre">
                <input class="InputDeNombre" type="text" name="Nombre" id="InputDeNombre" value="<?php echo $DatosAMostrar['nombre'];?>">
                <div id="DivPaMostrarLoDelInput" class="PruebaXD">
                    <div class="Aychamo"></div>
                    <?php echo $DatosAMostrar['nombre'];?>
                </div>
            </div>
            <input value="<?php echo $DatosAMostrar['direccion'];?>" class="InputDeDireccion" type="text" name="Direccion" id="" placeholder="Dirección:">
        </form>
        <br>
        <div class="CajaDeSubTitulo">
            <i class="fi-sr-package"></i>
            <span>INVENTARIO:</span>
            <div class="ContenidoDelInventario">
                <p>Puedes establecer el inventario inicial de este almacén indicando los productos que contiene y su existencia.
                    Una vez creado el almacén, la entrada de los productos a este será mediante Ordenes de compra, Envíos y ajustes de inventario.
                </p>
                <input hidden form="FormularioNuevoAlmacen" type="text" name="ProductosDelAlmacen" id="InputProductosEnlistadosAlAlmacen" value="<?php echo $DatosAMostrar['ProductosDelAlmacen'];?>">
                <div class="TablaDeProductos">
                    <div class="TituloDeTabla">Productos</div>
                    <div class="CabeceraDeLaTabla">
                        <celda class="ColumnaImagen">Imagen</celda>
                        <celda class="ColumnaID">ID</celda>
                        <celda class="ColumnaNombre">Nombre</celda>
                        <celda class="ColumnaExistencia">Existencia</celda>
                        <celda title="Unidad de medida" class="ColumnaCategoria">Unidad</celda>
                    </div>
                    <div id="EspacioDeRowsDeLaTabla" class="CuerpoDeLaTabla">
                        <?php
                        if(empty($DatosAMostrar['ProductosDelAlmacen'])){
                            echo '
                            <row class="RowVacio">
                                <span>No hay productos en el inventario de este almacén</span>
                            </row>
                            ';
                        }else{
                            
                            foreach(explode('¿', $DatosAMostrar['ProductosDelAlmacen']) as $Producto){
                                $pedazos = explode('x', $Producto);
                                $ProductoDelRow = new producto($pedazos[0]);
                                $DatosDelProducto = $ProductoDelRow->ObtenerDatos();

                                echo '
                                <row id="RowDeProducto-'.$DatosDelProducto['id'].'">
                                    <celda class="ColumnaImagen">
                                        <img src="../../Imagenes/Productos/'.((empty($DatosDelProducto['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProducto['ULRImagen']).'" alt="">
                                    </celda>
                                    <celda class="ColumnaID">'.$DatosDelProducto['id'].'</celda>
                                    <celda class="ColumnaNombre">'.$DatosDelProducto['nombre'].'</celda>
                                    <celda class="ColumnaExistencia">x '.$pedazos[1].'</celda>
                                    <celda title="'.((empty($DatosDelProducto['simboloConEstiloUM'])?'':$DatosDelProducto['nombreUM'])).'" class="ColumnaCategoria">'.((empty($DatosDelProducto['simboloConEstiloUM'])?'-':$DatosDelProducto['simboloConEstiloUM'])).'</celda>
                                    <div class="CeldaOculta">
                                        <i id="BotonModificarProductoEspecifico-'.$DatosDelProducto['id'].'" title="Modificar este producto." class="fi-rr-pencil"></i>
                                        <i id="BotonEliminarProductoEspecifico-'.$DatosDelProducto['id'].'" title="Eliminar este producto." class="fi-rr-trash"></i>
                                    </div>
                                </row>
                                ';
                            }
                        }
                        ?>
                    </div>
                    <div class="BotonDinamicoAgregar">
                        <span class="fi-rr-plus-small" id="BotonComoTalXD"> Agregar producto</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="coolFinalButtons">
            <a href="../" class="hovershadow">Salir</a>
            <button form="FormularioNuevoAlmacen" name="Guardar" id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Almacenes.png" alt="">
                <b>Almacenes</b>
            </div>
            <button id="BotonDelAsideAgregarProducto" style="width: 210px;"> <i class="fi-sr-apps-add"></i> Agregar producto</button>
            <a href="../../Ayuda/#22" target="_blank"><i class="fi-rr-interrogation"></i> Obtener ayuda</a>
            <a href="../" id=""> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
            
            <a HIDDEN href="../NuevoAlmacen/"> <i class="fi-sr-broom"></i> Limpiar formulario</a>
            
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="NuevoAlmacen.js"></script>
    <script src="ModalAgregarProducto.js"></script>
</body>
</html>