<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();
$NroMaximoDeResultadosAMostrar = 10;


$Iventario = new inventario();


$DatosAMostrar = array(
    'descripcion' => ((isset($_GET['descripcion']))?$_GET['descripcion']:''),
    'tipo' => ((isset($_GET['tipo']))?$_GET['tipo']:'0'),
    'NroDePaginaDeResultados' => ((isset($_GET['paginadebusqueda']))?$_GET['paginadebusqueda']:'1'),
    'NroMaximoDeResultadosAMostrar' => $NroMaximoDeResultadosAMostrar
);


$ResultadoDeBusqueda = $Iventario->ConsultarCambiosEnInventario($DatosAMostrar);

$NroMaximoDePaginas = ceil($ResultadoDeBusqueda['NroDeResultadosCoincidentes'] / $NroMaximoDeResultadosAMostrar);

?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $GLOBALS['nombreCorto'];?>: Cambios en inventario</title>
    <link rel="stylesheet" href="estilos_Cambios.css">
    <?php include('../../Otros/cabecera_N3.php');?>
    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../" class="Barra">
            <p>Inventario</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p>Cambios</p>
            <div class="Cuadrito" href=""></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
        <?php //print_r($ResultadoDeBusqueda);?>
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> BUSCAR CAMBIO:</span>
        <form autocomplete="off" id="FormularioBuscador" href="#SeccionDeLista" method="get">
            <input value="<?php echo $DatosAMostrar['descripcion'];?>" type="text" name="descripcion" id="" placeholder="Filtra por descripción...">
            <button id="BotonRealizarBusqueda" type="submit"><i class="fi-rr-search"></i></button>
            <select name="tipo" id="SelectEstado">
                <option value="0">Todos</option>
                <option <?php echo (($DatosAMostrar['tipo'] == 1)?'selected':'');?> value="1">Compra</option>
                <option <?php echo (($DatosAMostrar['tipo'] == 2)?'selected':'');?> value="2">Venta</option>
                <option <?php echo (($DatosAMostrar['tipo'] == 3)?'selected':'');?> value="3">Ajuste</option>
            </select>
        </form>
        <div class="CajaDeResultados">
            <?php
                if(empty($ResultadoDeBusqueda['AjustesLimitados'])){
                    echo '
                    <div class="CentrarAvisoDeVacio">
                        <span>No hay cambios en el inventario a mostrar</span>
                    </div>
                    ';
                }else{
                    $UltimoDia = '0';
                    $Tiempo = new AsistenteDeTiempo();
                    foreach($ResultadoDeBusqueda['AjustesLimitados'] as $AjusteAMostrar){
                        $FechaYHora = explode(' ', $AjusteAMostrar['fechaCreacion']);
                        $PedazosDeFecha = explode('-', $FechaYHora[0]);
                        $Ajuste_Dia = $PedazosDeFecha[2];
                        $Ajuste_Mes = $PedazosDeFecha[1];

                        $Tiempo->EstablecerFecha($AjusteAMostrar['fechaCreacion'], 'BaseDeDatosConTiempo');
                        

                        if($UltimoDia != $Ajuste_Dia.'-'.$Ajuste_Mes && $UltimoDia != '0'){
                            echo '<br>';
                        }
                        echo '
                            <div class="DivAjuste">';
                        if($UltimoDia != $Ajuste_Dia.'-'.$Ajuste_Mes){
                            echo '
                                <div class="DivFlotante">
                                    <span class="Mes">'.$Tiempo->ConvertirMes_NumAText($Ajuste_Mes).'</span>
                                    <span class="Dia">'.$Ajuste_Dia.'</span>
                                </div>
                            ';
                        }

                        echo '
                                <span class="TituloDeAjuste">Cambio en inventario #'.$AjusteAMostrar['id'].' <span class="Grisesito">('.$AjusteAMostrar['tipoDeAjuste'].')</span></span>
                                <p class="DetallesDelAjuste">'.$AjusteAMostrar['descripcion'].'</p>
                                <div class="CambiosDelAjuste">
                                    <div class="CabeceraDeTabla">
                                        <span class="CeldaFlechita Celda"></span>
                                        <span class="CeldaImagen Celda">Imagen</span>
                                        <span class="CeldaProducto Celda">Producto</span>
                                        <span class="CeldaAlmacen Celda">Almacén</span>
                                        <span class="CeldaCambio Celda">Cambio</span>
                                        <span class="CeldaResultado Celda">Resultado</span>
                                    </div>
                                    <div class="RowsDeTabla">';
                                //<span class="CeldaFlechita Celda"><i class="fi-rr-caret-'.(($ProductoAlterado['cambio'] > 0)?'up':'down').'"></i></span>
                                //<span class="CeldaCambio Celda">'.(($ProductoAlterado['cambio'] > 0)?'+':'').$ProductoAlterado['cambio'].'</span>
                                foreach($AjusteAMostrar['productosAlterados'] as $ProductoAlterado){
                                    echo '
                                        <row>
                                            <span class="CeldaFlechita Celda"><i class="fi-rr-caret-'.(($AjusteAMostrar['tipoDeAjuste']=='Venta')? 'down':(($ProductoAlterado['cambio'] > 0)?'up':'down')).'"></i></span>
                                            <span class="CeldaImagen Celda"> <img src="../../Imagenes/Productos/'.((empty($ProductoAlterado['ImagenProducto']))?'ImagenPredefinida_Productos.png':$ProductoAlterado['ImagenProducto']).'" alt=""> </span>
                                            <span class="CeldaProducto Celda">'.$ProductoAlterado['nombreProducto'].'</span>
                                            <span class="CeldaAlmacen Celda">'.$ProductoAlterado['nombreAlmacen'].'</span>
                                            <span class="CeldaCambio Celda">'.(($AjusteAMostrar['tipoDeAjuste']=='Venta')? '-':(($ProductoAlterado['cambio'] > 0)?'+':'')).$ProductoAlterado['cambio'].'</span>
                                            <span title="'.$ProductoAlterado['resultado'].' '.$ProductoAlterado['nombreUM'].' en el inventario" class="CeldaResultado Celda">'.$ProductoAlterado['resultado'].' '.$ProductoAlterado['UnidadDeMedida'].'</span>
                                        </row>
                                    ';
                                } 
                        echo       '</div>
                                </div>
                                <div class="InfoDelAjuste">
                                    <p>Realizado el '.$Tiempo->ConvertirFormato($AjusteAMostrar['fechaCreacion'], 'BaseDeDatosConTiempo', 'UsuarioConTiempo').' por '.$AjusteAMostrar['usuarioCreador'].'</p>
                                </div>
                            </div>
                        ';
                        $UltimoDia = $Ajuste_Dia.'-'.$Ajuste_Mes;
                    }
                }
            ?>
            
        </div>
        <div id="FondoDeLaBusqueda" class="BotonesDeConsulta">
            <div class="SeparadorDeBotones">
                <button value="1" type="<?php echo (($DatosAMostrar['NroDePaginaDeResultados'] > 1)?'submit':'button');?>" name="paginadebusqueda" form="FormularioBuscador" title="Ir a la primera página"> <i class="fi-rr-angle-double-small-left"></i> </button>
                <button value="<?php echo ($DatosAMostrar['NroDePaginaDeResultados'] - 1);?>" type="<?php echo (($DatosAMostrar['NroDePaginaDeResultados'] > 1)?'submit':'button');?>" name="paginadebusqueda" form="FormularioBuscador" title="Ir a la página anterior"> <i class="fi-rr-angle-small-left"></i> </button>
            </div>
            <div class="SeparadorDeBotones">
                <span class="NroPag">
                    <?php echo $DatosAMostrar['NroDePaginaDeResultados'];?>
                </span>
            </div>
            <div class="SeparadorDeBotones">
                <button value="<?php echo ($DatosAMostrar['NroDePaginaDeResultados'] + 1);?>" type="<?php echo (($NroMaximoDePaginas > $DatosAMostrar['NroDePaginaDeResultados'])?'submit':'button');?>" name="paginadebusqueda" form="FormularioBuscador" title="Ir a la página siguente"> <i class="fi-rr-angle-small-right"></i> </button>
                <button value="<?php echo $NroMaximoDePaginas;?>" type="<?php echo (($DatosAMostrar['NroDePaginaDeResultados'] != $NroMaximoDePaginas)?'submit':'button') ?>" name="paginadebusqueda" form="FormularioBuscador" title="Ir a la última página"> <i class="fi-rr-angle-double-small-right"></i> </button>
            </div>
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Inventario.png" alt="">
                <b>Inventario</b>
            </div>
            <a href="../"><i class="fi-rr-undo-alt"></i> Volver a inventario</a>
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
</body>
</html>
