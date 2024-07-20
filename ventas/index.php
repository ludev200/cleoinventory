<?php 
include('../Otros/clases.php');
$BaseDeDatos = new conexion();
$NroMaximoDeRowsMostradas = 8;

if(!isset($_GET['descripcion'])||!isset($_GET['estado'])){
    header('Location: ?descripcion=&estado=30&paginadebusqueda=1');
}


$DatosAMostrar=array(
    "descripcion" => $_GET['descripcion'],
    "estado" => ((empty($_GET['estado']))?"30":$_GET['estado'])
);

//Preparamos los SQL segun el formulario recibido del GET
$SQLDeEstado=(($DatosAMostrar['estado']==30)?"(`cotizaciones`.`idEstado` != 35 AND `cotizaciones`.`idEstado` != 36)":"`cotizaciones`.`idEstado` = ".$DatosAMostrar['estado']);
$SQLDeDescripcion = ((empty($_GET['descripcion']))?"":" AND (`cotizaciones`.`id` LIKE '%".$_GET['descripcion']."%' OR `cotizaciones`.`nombre` LIKE '%".$_GET['descripcion']."%' OR `cotizaciones`.`cedulaCliente` LIKE '%".$_GET['descripcion']."%')");


//Consultamos el numero maximo de paginas
$BusquedaDeNumeroDeRows = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `cotizaciones` WHERE (".$SQLDeEstado.$SQLDeDescripcion.")");
$NroDePaginasSegundDDBB = ceil($BusquedaDeNumeroDeRows[0][0] / $NroMaximoDeRowsMostradas);


//Si no se recibio ninguna pagina del GET, colocamos 1
if(!isset($_GET['paginadebusqueda'])){
    $NroDePagina = 1;
}else{
    $NroDePagina = $_GET['paginadebusqueda'];
}


//Preparamos el SQL del limite segun el numero de pagina actual
$SQLDeLimite = "LIMIT ".(($NroDePagina - 1) * $NroMaximoDeRowsMostradas).",".$NroMaximoDeRowsMostradas;


//Consultamos los productos segun el formulario
$SQLDeConsulta = "SELECT 
`cotizaciones`.`id`, 
`cotizaciones`.`nombre`, 
`estados`.`estado`, 
`cotizaciones`.`cedulaCliente` 

FROM `cotizaciones` 
INNER JOIN `estados` ON `cotizaciones`.`idEstado` = `estados`.`id` 

WHERE (".$SQLDeEstado.$SQLDeDescripcion.") ORDER BY `cotizaciones`.`id` DESC ".$SQLDeLimite;
$ListaDeCotizaciones = $BaseDeDatos->consultar($SQLDeConsulta);




$publicFunctions = new publicFunctions();
$publicFunctions->checkBudgetsAndPurchase();
?>

<!DOCTYPE html>
<head>
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Cotizaciones</title>
    <link rel="stylesheet" href="estilos_cotizaciones.css">
    <?php include('../Otros/cabecera_N2.php');?>
    <nav class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../">
            <p>Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Ventas</p>
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
                <a class="BotonNuevaC" href="NuevaCotizacion">
                    <div class="RellenoDelBoton">
                        <img src="../imagenes/new.png" alt="">
                        <h4>Nueva cotización</h4>
                        <span class="PequenaDescripcion">Calcula el presupuesto para un cliente según los productos indicados.</span>    
                    </div>
                </a>
                <a class="BotonConfirmarC" href="Confirmar">
                    <div class="RellenoDelBoton">
                        <img src="../imagenes/Sistema/RechazarVenta.png" alt="">
                        <h4>Confirmar venta</h4>
                        <span class="PequenaDescripcion">Indica la aceptación de una cotización en espera.</span>
                    </div>
                </a>
                <a class="BotonRechazarC" href="Cancelar">
                    <div class="RellenoDelBoton">
                        <img src="../imagenes/Sistema/AceptarVenta.png" alt="">
                        <h4>Rechazar venta</h4>
                        <span class="PequenaDescripcion">Indica el rechazo de una cotización en espera.</span>
                    </div>
                </a>
            </div>
        </section>
        <br>
        <span id="TopeDelListado" class="fi-rr-line-width test1"> BUSCAR VENTAS:</span>
        <section> 
            <form autocomplete="off" id="FormularioBuscador" href="#SeccionDeLista" method="get">
                <input value="<?php echo $DatosAMostrar['descripcion']?>" type="text" name="descripcion" id="" placeholder="Filtra por ID, nombre o cliente...">
                <button id="BotonBuscarCotizaciones" type="submit"><i class="fi-rr-search"></i></button>
                <select name="estado" id="SelectEstado">
                    <option value="30" <?php echo (($DatosAMostrar['estado']==30)?"selected=true":"")?>>Todos</option>
                    <option value="31" <?php echo (($DatosAMostrar['estado']==31)?"selected=true":"")?>>Aceptados</option>
                    <option value="32" <?php echo (($DatosAMostrar['estado']==32)?"selected=true":"")?>>Rechazados</option>
                    <option value="33" <?php echo (($DatosAMostrar['estado']==33)?"selected=true":"")?>>En espera</option>
                    <option value="34" <?php echo (($DatosAMostrar['estado']==34)?"selected=true":"")?>>Vencidos</option>
                </select>
            </form>
        </section>
        <section id="SeccionDeLista" class="TablaDeResultados">
            <div class="FlexH ColumnasDeTablas">
                <span class="ColumnaID">ID</span>
                <span class="ColumnaNombre">Descripción del servicio</span>
                <span class="ColumnaCliente">Cliente</span>
                <span class="ColumnaEstado">Estado</span>
                <span class="ColumnaDetalles">Detalles</span>
            </div>
            <div class="RowsDeTabla">
                <?php 
                    
                    if(empty($ListaDeCotizaciones)){
                        echo'
                        <div class="FlexH Row TablaVacia">
                        No hay ventas para mostrar...
                        </div>
                        ';
                    }else{
                        foreach($ListaDeCotizaciones as $Cotizacion){
                            if(empty($Cotizacion['cedulaCliente'])){
                                $Cotizacion ['cliente'] = '<i style="color: gray;">Ninguno</i>';
                            }else{
                                $Cotizacion ['XD'] = 'si tiene';
                                $DatosDelCliente = $BaseDeDatos->consultar("SELECT `rif`, `tipoDeDocumento` FROM `clientes` WHERE `rif` = ".$Cotizacion['cedulaCliente']);
                                $Cotizacion ['cliente'] = $DatosDelCliente[0]['tipoDeDocumento'].' - '.zerofill($DatosDelCliente[0]['rif'], 9);
                            }
                            
                            echo '<div class="FlexH Row">
                                    <span class="ColumnaID">'.zerofill($Cotizacion['id'], 7).'</span>
                                    <span class="ColumnaNombre">'.$Cotizacion['nombre'].'</span>
                                    <span class="ColumnaCliente">'.$Cotizacion['cliente'].'</span>
                                    <span class="ColumnaEstado PuntoDeEstado Estado'.$Cotizacion['estado'].'">'.$Cotizacion['estado'].'</span>
                                    <span class="ColumnaDetalles"><a href="Venta?id='.$Cotizacion['id'].'">Ver más <i class="fi-rr-arrow-alt-square-right"></i></a></span>
                                </div>';
                        }
                    }
                ?>
            </div>
        </section>
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
        <br>
        <span class="fi-rr-chart-histogram test1"> ESTADÍSTICAS:</span>
        <section id="SeccionDeEstadisticas" class="PanelDeEstadisticas">
            <div class="SelectorDeGrafico">
                <button id="BotonAnteriorGrafico"> <i class="fi-sr-angle-left"></i> </button>
                <b id="TitulosDeGraficos">
                    <span>Ventas por estado</span>
                    <span>Clientes más activos</span>
                </b>
                <button class="CambiarGraficoDisponible" id="BotonSiguienteGrafico"> <i class="fi-sr-angle-right"></i> </button>
            </div>
            <div class="CajaDeSelectDeTiempo">
                <select name="" id="SelectAnioDeGrafico">
                    <?php 
                        $ConsultaDeFechas = $BaseDeDatos->consultar("SELECT * FROM `historial` ORDER BY `fechaCreacion`");
                        $AnioMinimoDelSelect = 2020;
                        
                        foreach ($ConsultaDeFechas as $RegistroDeCotizacion) {
                            $pedazos = explode('-', $RegistroDeCotizacion['fechaCreacion']);    

                            if($pedazos[0] > 2000){
                                $AnioMinimoDelSelect = $pedazos[0];
                                break;
                            }
                        }
                        
                        while($AnioMinimoDelSelect <= date('Y')){
                            echo '<option '.(($AnioMinimoDelSelect == date('Y'))?'selected':'').' value="'.$AnioMinimoDelSelect.'">'.$AnioMinimoDelSelect.'</option>';
                            $AnioMinimoDelSelect++;
                        }
                    ?>
                </select>
                <select name="" id="SelectMesDeGrafico">
                    <option value="0">Mes</option>
                    <option <?php echo ((date('m') == 1)?'selected':'');?> value="1">Enero</option>
                    <option <?php echo ((date('m') == 2)?'selected':'');?> value="2">Febrero</option>
                    <option <?php echo ((date('m') == 3)?'selected':'');?> value="3">Marzo</option>
                    <option <?php echo ((date('m') == 4)?'selected':'');?> value="4">Abril</option>
                    <option <?php echo ((date('m') == 5)?'selected':'');?> value="5">Mayo</option>
                    <option <?php echo ((date('m') == 6)?'selected':'');?> value="6">Junio</option>
                    <option <?php echo ((date('m') == 7)?'selected':'');?> value="7">Julio</option>
                    <option <?php echo ((date('m') == 8)?'selected':'');?> value="8">Agosto</option>
                    <option <?php echo ((date('m') == 9)?'selected':'');?> value="9">Septiembre</option>
                    <option <?php echo ((date('m') == 10)?'selected':'');?> value="10">Octubre</option>
                    <option <?php echo ((date('m') == 11)?'selected':'');?> value="11">Noviembre</option>
                    <option <?php echo ((date('m') == 12)?'selected':'');?> value="12">Diciembre</option>
                </select>
            </div>
            <div class="ContenedorDeGraficos">
                <div class="Fondo">
                    <div class="DivisorPorcentaje">
                        <span>100%</span>
                    </div>
                    <div class="DivisorPorcentaje">
                        <span>80%</span>
                    </div>
                    <div class="DivisorPorcentaje">
                        <span>60%</span>
                    </div>
                    <div class="DivisorPorcentaje">
                        <span>40%</span>
                    </div>
                    <div class="DivisorPorcentaje">
                        <span>20%</span>
                    </div>
                    <div class="DivisorPorcentaje"></div>
                    <div class="xddd"></div>
                </div>
                <div class="ContenidoDelGrafico">
                    <div class="Barra1 Barras">
                        <span class="NombreDeBarra">Aceptadas</span>
                    </div>
                    <div class="Barra2 Barras">
                        <span class="NombreDeBarra">Rechazadas</span>
                    </div>
                    <div class="Barra3 Barras">
                        <span class="NombreDeBarra">En espera</span>
                    </div>
                    <div class="Barra4 Barras">
                        <span class="NombreDeBarra">Vencidas</span>
                    </div>
                </div>
                <div class="ElementosVariosDelGrafico">
                    <div class="PisoDeBarras"></div>
                    <div class="AvisoSinResultados">
                        No hay resultados
                    </div>
                </div>
            </div>
        </section>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Ventas.png" alt="">
                <b>Ventas</b>
            </div>
            <a id="AgregarNuevo" href="NuevaCotizacion"> <i class="fi-rr-add"></i> Nueva cotización</a>
            <a href="#TopeDelListado"> <i class="fi-rr-line-width"></i> Buscar ventas</a>
            <a href="#SeccionDeEstadisticas"> <i class="fi-rr-chart-histogram"></i> Gráficos estadísticos</a>
        </div>
    </aside>
    <?php include '../ipserver.php';?>
    <script src="../Otros/sweetalert.js"></script>
    <script src="cotizaciones.js"></script>
</body>
</html>