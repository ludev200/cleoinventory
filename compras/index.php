<?php
include_once('../Otros/clases.php');
$BaseDeDatos = new conexion();
$NroMaximoDeRowsMostradas = 7;


$DatosAMostrar = array(
    'descripcion' => ((isset($_GET['descripcion']))?$_GET['descripcion']:''),
    'estado' => ((isset($_GET['estado']))?$_GET['estado']:'0'),

);


$SQLDeDescripcion = ((empty($DatosAMostrar['descripcion']))?'':"(`id` LIKE '%".$DatosAMostrar['descripcion']."%' OR `nombre` LIKE '%".$DatosAMostrar['descripcion']."%') AND ");
$SQLDeEstado = (($DatosAMostrar['estado'] == 0)?'(`idEstado` != 65 AND `idEstado` != 66)':'(`idEstado` = '.$DatosAMostrar['estado'].')');


//Consultamos el numero maximo de paginas
$BusquedaDeNumeroDeRows = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `ordenesdecompra` 
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

$SQLDeConsulta = "SELECT * FROM `ordenesdecompra` WHERE (".$SQLDeDescripcion.$SQLDeEstado.") ORDER BY `id` DESC ".$SQLDeLimite;

$ResultadosDeBusqueda = $BaseDeDatos->consultar($SQLDeConsulta);


$publicFunctions = new publicFunctions();
$publicFunctions->checkBudgetsAndPurchase();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="estilos_Compras.css">
    <title><?php echo $GLOBALS['nombreCorto'];?>: Compras</title>
    <?php include('../Otros/cabecera_N2.php');?>
    <nav class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../">
            <p>Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Compras</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
        <section class="SeccionDeBotonNuevo">
            <span class="TituloDeCajaDeBotonesPrincipales">¿Qué quieres hacer?</span>
            <div class="CajaDeBotonesPincipales">
                <a class="BotonNuevaC" href="NuevaOrden/">
                    <div class="RellenoDelBoton">
                        <img src="../imagenes/Sistema/CrearOrden.png" alt="">
                        <h4>Crear nueva orden</h4>
                        <span class="PequenaDescripcion">Crea una lista con los productos que deseas comprar.</span>    
                    </div>
                </a>
                <a class="BotonConfirmarC" href="RegistrarCompra">
                    <div class="RellenoDelBoton">
                        <img src="../imagenes/Sistema/RegistrarCompra.png" alt="">
                        <h4>Registrar compra</h4>
                        <span class="PequenaDescripcion">Registra una nueva compra o confirma una orden de compra realizada anteriormente.</span>
                    </div>
                </a>
            </div>
        </section>
        <br>
        <?php //echo $SQLDeConsulta;?>
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSection"> BUSCAR COMPRAS:</span>
        <form autocomplete="off" id="FormularioBuscador" href="#SeccionDeLista" method="get">
            <input value="<?php echo $DatosAMostrar['descripcion'];?>" type="text" name="descripcion" id="" placeholder="Filtra por ID o nombre...">
            <button id="BotonRealizarBusqueda" type="submit"><i class="fi-rr-search"></i></button>
            <select name="estado" id="SelectEstado">
                <option <?php if($DatosAMostrar['estado'] == 0) echo 'selected';?> value="0">Todos</option>
                <option <?php if($DatosAMostrar['estado'] == 61) echo 'selected';?> value="61">Aceptadas</option>
                <option <?php if($DatosAMostrar['estado'] == 62) echo 'selected';?> value="62">Rechazadas</option>
                <option <?php if($DatosAMostrar['estado'] == 63) echo 'selected';?> value="63">En espera</option>
                <option <?php if($DatosAMostrar['estado'] == 64) echo 'selected';?> value="64">Vencidas</option>
            </select>
        </form>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaDescripcion">Descripción</celda>
                <celda class="ColumnaProductos">Productos</celda>
                <celda class="ColumnaEstado">Estado</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                    if(empty($ResultadosDeBusqueda)){
                        echo '
                            <row class="RowVacio">
                                <span>No hay compras a mostrar.</span>
                            </row>
                        ';
                    }else{
                        foreach($ResultadosDeBusqueda as $RowResultado){                            
                            $Compra = new compra($RowResultado['id']);
                            $DatosDeCompra = $Compra->ObtenerDatos();

                            //print_r($DatosDeCompra);

                            echo '
                            <row>
                                <celda class="ColumnaID">'.$DatosDeCompra['id'].'</celda>
                                <celda class="ColumnaDescripcion">'.$DatosDeCompra['nombre'].'</celda>
                                <celda class="ColumnaProductos">'.$DatosDeCompra['nroDeProductos'].'</celda>
                                <celda class="ColumnaEstado PuntoDeEstado PE'.$DatosDeCompra['idEstado'].'">'.$DatosDeCompra['estado'].'</celda>
                                <celda class="ColumnaDetalles"><a class="hovershadow" href="Compra/?id='.$DatosDeCompra['id'].'">Ver más</a></celda>
                            </row>
                            ';
                        }
                    }
                ?>
            </div>
        </div>
        <div class="BotonesDeConsulta">
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
                <img src="../Imagenes/iconoDelMenu_Compras.png" alt="">
                <b>Compras</b>
            </div>
            <a id="AgregarNuevo" href="NuevaOrden"> <i class="fi-rr-add"></i> Crear nueva orden</a>
            <a id="AgregarNuevo" href="Cambios"> <i class="fi-rr-clipboard-list-check"></i> Registrar una compra</a>
            <a href="#TopeDelListado"> <i class="fi-rr-line-width"></i> Buscar compras</a>
        </div>
    </aside>
    <?php include '../ipserver.php';?>
</body>
</html>