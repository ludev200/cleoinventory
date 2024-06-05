<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();

$DatosAMostrar = array(
    'descripcion' => (isset($_GET['descripcion'])?$_GET['descripcion']:'')
);




if($_POST){
    
    $cotARechazar = new cotizacion($_POST['IDCot']);
    print_r($cotARechazar->RechazarCot());
    header('Location: ?Rechazada='.$_POST['IDCot']);
}


$idCotiACancelar = ((empty($_GET['id']))? '':$_GET['id']);


?>



<!DOCTYPE html>
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Rechazar venta</title>
    <link rel="stylesheet" href="estilos_rechazar.css">
    <?php include('../../Otros/cabecera_N3.php');?>
    <nav id="ZonaDeCliente" class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../../">
            <p >Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="../">
            <p>Ventas</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Rechazar</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
        <?php
        if(isset($_GET['Rechazada'])){
            echo '
            <div class="AvisitoFijo" id="'.$_GET['Rechazada'].'">
                <i class="fi-rr-comment-check"></i>
                Cotización #'.$_GET['Rechazada'].' ha sido rechazada!
            </div>
            ';
        }
        ?>
        <span class="fi-rr-hourglass-end TituloDeSection"> COTIZACIONES EN ESPERA</span>
        <section class="CotEnEsperaYCotSelec">
            <div class="TablaYBuscador">
                <div class="FormBuscador">
                    <input value="" type="text" name="descripcion" autocomplete="off" id="InputBuscadorDeCot" placeholder="Filtra por ID, descripción o rif del cliente..." class="InputBuscador">
                    <button id="BotonBuscarCotizaciones" class="BotonBuscador"><i class="fi-rr-search"></i></button>
                </div>
                <div class="TablaDeContizaciones">
                    <div class="FlexH ColumnasDeTablas">
                        <span class="ColumnaID">ID</span>
                        <span class="ColumnaNombre">Descripción</span>
                        <span class="ColumnaNombre">Cliente</span>
                    </div>
                    <div class="ContenedorDeRowsDeTabla">
                        <div id="DivResultadosDeBusqueda" class="RowsDeTabla">
                            <?php
                            $resultadosDeBusqueda = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE `idEstado` = 33");
                            if(empty($resultadosDeBusqueda)){
                                echo '
                                <div class="estebetavacio">
                                    <span>No hay cotizaciones en espera a mostrar</span>
                                </div>
                                ';
                            }else{
                                foreach($resultadosDeBusqueda as $cot){
                                    if(empty($cot['cedulaCliente'])){
                                        $textoDeCedula = '<i style="color: gray;">Ninguno</i>';
                                    }else{
                                        $cliente = $BaseDeDatos->consultar("SELECT * FROM `clientes` WHERE (`rif` = ".$cot['cedulaCliente']." AND `idEstado` = 11)");
                                        
                                        $textoDeCedula = $cliente[0]['tipoDeDocumento'].'-'.zerofill($cliente[0]['rif'], 9);
                                    }
                                    echo '
                                    <div class="Row RowCotEnLista '.($idCotiACancelar==$cot['id']? 'RowSeleccionada':'').'" id="RowDeCot-'.$cot['id'].'">
                                        <span class="ColumnaID">'.$cot['id'].'</span>
                                        <span class="ColumnaNombre">'.$cot['nombre'].'</span>
                                        <span class="ColumnaNombre">'.$textoDeCedula.'</span>
                                    </div>
                                    ';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="SeparadorGris">
                <form method="post" id="FormCancelarVenta"><input value="<?php echo $idCotiACancelar;?>" style="display: none;" type="text" name="IDCot" id="IDCotSeleccionada"></form>
            </div>
            <div class="CotSeleccionada" id="EspacioDeCotSeleccionada">
                <div class="CompraNoSeleccionada">
                    <img src="../../Imagenes/Sistema/ImagenPredefinida_Ventas.png" alt="">
                    <span>Seleccione una cotización en espera</span>
                </div>
            </div>
        </section>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Ventas.png" alt="">
                <b>Rechazar venta</b>
            </div>
            <a href="../"><i class="fi-rr-undo-alt"></i> Volver a ventas</a>
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="cositas.js"></script>
</body>
</html>