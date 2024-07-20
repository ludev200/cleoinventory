<?php
include_once('../Otros/clases.php');
$BaseDeDatos = new conexion();

$Inventario = new inventario();
$publicFunctions = new publicFunctions();

$DatosAMostrar = array(
    'descripcion' => '',
    'estado' => '0'
);

if($_GET){
    $DatosAMostrar = array(
        'descripcion' => ((isset($_GET['descripcion']))?$_GET['descripcion']:''),
        'estado' => ((isset($_GET['estado']))?$_GET['estado']:'0'),
    );
}

$inventary = $publicFunctions->getInventary($DatosAMostrar);
$DatosDelInventario = $Inventario->ObtenerDatos();
?>

<!DOCTYPE html>
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Inventario</title>
    <link rel="stylesheet" href="estilos_inventario.css">
    <?php include('../Otros/cabecera_N2.php');?>
    <nav class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../">
            <p>Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Inventario</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <modal id="ModalSeleccionDeEntrada">
        <div id="CuerpoDeVentanaDeEntrada" class="VentanaFlotante OcultarModal">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentanaDeEntrada" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">ELEGIR TIPO DE MODIFICACIÓN</b>                
                <div class="CuerpoDeLaVentanaModal">
                    <div class="OpcionesPrincipales">
                        <a href="../Compras/RegistrarCompra/" class="TarjetaDeMetodo">
                            <img src="../Imagenes/iconoDelMenu_Compras.png" alt="">
                            <b>Registro de compra</b>
                            <p>Registra una compra y así realiza una entrada de productos e incrementa su existencia en los almacenes indicados.</p>
                        </a>
                        <a href="../Ventas/Confirmar/" class="TarjetaDeMetodo">
                            <img src="../Imagenes/iconoDelMenu_Ventas.png" alt="">
                            <b>Registro de venta</b>
                            <p>Registra una venta en el sistema para asi realizar una salida de productos de los almacenes indicados.</p>
                        </a>
                        <a href="NuevoAjuste" class="TarjetaDeMetodo">
                            <img src="../Imagenes/Otros/AjusteDeInventario.png" alt="">
                            <b>Ajuste de inventario</b>
                            <p>Modifica la existencia de los productos en los distintos almacénes sin especificar su origen o destino.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <article>
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> PRODUCTOS EN INVENTARIO:</span>
        <input hidden type="text" placeholder="Step" id="step" form="FormularioBuscador" value="1">
        <form autocomplete="off" id="FormularioBuscador" href="#SeccionDeLista" method="get">
            <input value="<?php echo $DatosAMostrar['descripcion'];?>" currentValue="<?php echo $DatosAMostrar['descripcion'];?>" type="text" name="descripcion" id="searchInput" placeholder="Filtra por ID o nombre...">
            <button id="BotonRealizarBusqueda" type="submit"><i class="fi-rr-search"></i></button>
            <select name="estado" id="SelectEstado" currentValue="<?php echo $DatosAMostrar['estado'];?>">
                <option <?php echo (($DatosAMostrar['estado'] == 0)?'selected':'');?> value="0">Todos</option>
                <option <?php echo (($DatosAMostrar['estado'] == 1)?'selected':'');?> value="1">Disponible</option>
                <option <?php echo (($DatosAMostrar['estado'] == 2)?'selected':'');?> value="2">En alerta</option>
                <option <?php echo (($DatosAMostrar['estado'] == 3)?'selected':'');?> value="3">Agotado</option>
            </select>
        </form>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaImagen">Imagen</celda>
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaNombre">Nombre</celda>
                <celda class="ColumnaExistencia">Existencia</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                if(empty($inventary['result'])){
                    echo '
                    <row class="RowVacio">
                        <span>No hay productos en el invenario a mostrar.</span>
                    </row>
                ';
                }else{
                    foreach($inventary['result'] as $Producto){
                        echo '
                        <row>
                            <celda class="ColumnaImagen">
                                <img src="../Imagenes/Productos/'.((empty($Producto['img']))?'ImagenPredefinida_Productos.png':$Producto['img']).'" alt="">
                            </celda>
                            <celda class="ColumnaID">'.$Producto['id'].'</celda>
                            <celda class="ColumnaNombre">'.$Producto['name'].'</celda>
                            <celda class="ColumnaExistencia">'.(($Producto['idState'] == 3)?'<span title="Este producto se encuentra agotado." style="color: rgb(236, 49, 49);" class="fi-sr-comment-exclamation"></span>':(($Producto['idState'] == 2)?'<span title="Este producto se encuentra bajo el nivel de alerta establecido ('.$Producto['alertLevel'].' '.$Producto['unit'].')" style="color: #FEA82F;" class="fi-sr-comment-exclamation"></span>':'')).'x '.$Producto['existence'].'</celda>
                            <celda class="ColumnaDetalles">
                                <a target="_blank" class="hovershadow" href="../Productos/Producto/?id='.$Producto['id'].'">Ver más</a>
                            </celda>
                        </row>
                        ';
                    }
                }
                ?>
            </div>
        </div>
        <div id="loadingResults">
            Buscando más resultados
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Inventario.png" alt="">
                <b>Inventario</b>
            </div>
            <button id="BotonAgregarEntrada" style="width: 210px;"> <i class="fi-sr-exchange-alt"></i> Modificar inventario</button>
            <a id="AgregarNuevo" href="Cambios"> <i class="fi-rr-folder-times"></i> Historial de cambios</a>
        </div>
        <?php
        if($DatosDelInventario['ProductosEnAlerta']>0||$DatosDelInventario['ProductosAgotados']>0){
            echo '
            <div class="LetreroDeExistencia">
                <div class="ImagenesDelLetrero">
                    <div class="PaLaImagen">
                        <img src="../Imagenes/iconoDelMenu_Productos.png" alt="">
                    </div>
                    <div class="PalTexto">
                        <p>En Alerta : '.$DatosDelInventario['ProductosEnAlerta'].'</p>
                        <p>Agotados: '.$DatosDelInventario['ProductosAgotados'].'</p>
                    </div>
                </div>
                <div class="PalBoton">
                    <a href="../Compras/NuevaOrden/?modo=1"> <i class="fi-rr-shopping-cart"></i> Generar Orden de compra</a>
                </div>
            </div>
            ';
        }
        ?>
        
        
    </aside>
    <?php include '../ipserver.php';?>
    <script src="inventario.js"></script>
</body>
</html>