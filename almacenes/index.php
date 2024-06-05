
<?php
include_once('../Otros/clases.php');
$BaseDeDatos = new conexion();
$NroMaximoDeRowsMostradas = 7;


$DatosAMostrar = array(
    'descripcion' => '',
    'estado' => '51'
);

if($_GET){
    $DatosAMostrar = array(
        'descripcion' => ((isset($_GET['descripcion']))? $_GET['descripcion']:''),
        'estado' => ((isset($_GET['estado']))?$_GET['estado']:'51')
    );
}




$SQLDeDescripcion = ((empty($DatosAMostrar['descripcion']))?'':"(`almacenes`.`id` LIKE '%".$DatosAMostrar['descripcion']."%' OR `almacenes`.`nombre` LIKE '%".$DatosAMostrar['descripcion']."%' OR `almacenes`.`direccion` LIKE '%".$DatosAMostrar['descripcion']."%') AND ");
$SQLDeEstado = (($DatosAMostrar['estado'] == '0')?'(`almacenes`.`idEstado` != 53 AND `almacenes`.`idEstado` != 54)':'(`almacenes`.`idEstado` = '.$DatosAMostrar['estado'].')');

//Consultamos el numero maximo de paginas

$BusquedaDeNumeroDeRows = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `almacenes` 
WHERE (".$SQLDeDescripcion.$SQLDeEstado.")");
$NroDePaginasSegundDDBB = ceil($BusquedaDeNumeroDeRows[0][0] / $NroMaximoDeRowsMostradas);


//Si no se recibio ninguna pagina del GET, colocamos 1
if(!isset($_GET['paginadebusqueda'])){
    $NroDePagina = 1;
}else{
    $NroDePagina = $_GET['paginadebusqueda'];
}

//Preparamos el SQL del limite segun el numero de pagina actual
$SQLDeLimite = "LIMIT ".(($NroDePagina - 1) * $NroMaximoDeRowsMostradas).",".$NroMaximoDeRowsMostradas;


$SQLDeConsulta = "SELECT `almacenes`.`id`
FROM `almacenes` 
WHERE (".$SQLDeDescripcion.$SQLDeEstado.") ORDER BY `almacenes`.`predeterminado` DESC ".$SQLDeLimite."
";

$ResultadosDeBusqueda = $BaseDeDatos->consultar($SQLDeConsulta);


?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Almacenes</title>
    <link rel="stylesheet" href="estilos_almacenes.css">
    <?php include('../Otros/cabecera_N2.php');?>
    <nav class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../">
            <p>Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Almacenes</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>        
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> BUSCAR ALMACÉN:</span>
        <form autocomplete="off" id="FormularioBuscador" href="#SeccionDeLista" method="get">
            <input value="<?php echo $DatosAMostrar['descripcion'];?>" type="text" name="descripcion" id="" placeholder="Filtra por ID, nombre o dirección...">
            <button id="BotonRealizarBusqueda" type="submit"><i class="fi-rr-search"></i></button>
            <select name="estado" id="SelectEstado" hidden>
                <option <?php echo (($DatosAMostrar['estado'] == 51)?'selected':'');?> value="51">Activos</option>
                <option <?php echo (($DatosAMostrar['estado'] == 52)?'selected':'');?> value="52">Inactivos</option>
                <option <?php echo (($DatosAMostrar['estado'] == 0)?'selected':'');?> value="0">Todos</option>
            </select>
        </form>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaNombre">Nombre</celda>
                <celda class="ColumnaNombre">Dirección</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                //echo $SQLDeConsulta;
                    if(empty($ResultadosDeBusqueda)){
                        echo '
                            <row class="RowVacio">
                                <span>No hay almacénes a mostrar.</span>
                            </row>
                        ';
                    }else{
                        foreach($ResultadosDeBusqueda as $RowResultado){
                            
                            $Almacen = new almacen($RowResultado['id']);
                            $DatosDelAlmacen = $Almacen->ObtenerDatos();
                            
                            echo '
                                <row>
                                    <celda class="ColumnaID">'.$DatosDelAlmacen['id'].'</celda>
                                    <celda class="ColumnaNombre">'.$DatosDelAlmacen['nombre'].'</celda>
                                    <celda class="ColumnaNombre">'.$DatosDelAlmacen['direccion'].'</celda>
                                    <celda class="ColumnaDetalles"><a href="Almacen/?id='.$DatosDelAlmacen['id'].'">Ver más</a></celda>
                                </row>
                            ';
                        }
                    }
                ?>
            </div>
        </div>
        <div id="FondoDeLaBusqueda" class="BotonesDeConsulta">
            <div class="SeparadorDeBotones">
                <button value="1" <?php echo (($NroDePagina > 1)?'type="submit"':'type="button"') ?> name="paginadebusqueda" form="FormularioBuscador" title="Ir a la primera página"> <i class="fi-rr-angle-double-small-left"></i> </button>
                <button <?php echo 'value="'.($NroDePagina - 1).'"'.(($NroDePagina > 1)?'type="submit"':'type="button"') ?> name="paginadebusqueda" form="FormularioBuscador" title="Ir a la página anterior"> <i class="fi-rr-angle-small-left"></i> </button>
            </div>
            <div class="SeparadorDeBotones">
                <span class="NroPag">
                    <?php echo $NroDePagina;?>
                </span>
            </div>
            <div class="SeparadorDeBotones">
                <button <?php echo 'value="'.($NroDePagina + 1).'"'.(($NroDePaginasSegundDDBB > $NroDePagina)?'type="submit"':'type="button"') ?> name="paginadebusqueda" form="FormularioBuscador" title="Ir a la página siguente"> <i class="fi-rr-angle-small-right"></i> </button>
                <button <?php echo 'value="'.$NroDePaginasSegundDDBB.'" '.(($NroDePaginasSegundDDBB > $NroDePagina)?'type="submit"':'type="button"') ?> name="paginadebusqueda" form="FormularioBuscador" title="Ir a la última página"> <i class="fi-rr-angle-double-small-right"></i> </button>
            </div>
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Almacenes.png" alt="">
                <b>Almacenes</b>
            </div>
            <a id="AgregarNuevo" href="NuevoAlmacen"> <i class="fi-rr-add"></i> Nuevo almacén</a>
        </div>
    </aside>
    <?php include '../ipserver.php';?>
    <script src="almacenes.js"></script>
</body>
</html>