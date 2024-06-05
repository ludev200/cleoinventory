<?php
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();
$Tiempo = new AsistenteDeTiempo();
$Auditoria = new historial();

$NroDeMovimiento = 1;
$TipoDeMovimiento = 1;

if(isset($_GET['NroDeMovimiento'])){
    if(is_numeric($_GET['NroDeMovimiento'])){
        $NroDeMovimiento = ceil($_GET['NroDeMovimiento']);
    }
}
if(isset($_GET['TipoDeMovimiento'])){
    if(is_numeric($_GET['TipoDeMovimiento'])){
        $TipoDeMovimiento = ceil($_GET['TipoDeMovimiento']);
    }
}

//Verifico que recibi una id a buscar y consulto si existe como almacen activo o inactivo
if(isset($_GET['id'])){
    if($_GET['id'] > 0 && is_numeric($_GET['id'])){
        $ResultadoDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE ( (`id` = ".$_GET['id'].") AND (`idEstado` != 53 AND `idEstado` != 54) )");

        if(empty($ResultadoDeConsulta)){
            header('Location: ../../Error.php?error=404&desc=10');
        }else{
            //Una vez comprado la validez del almacen, creo el objeto y cargo sus datos
            $AlmacenAMostrar = new almacen($_GET['id']);
            $DatosAMostrar = $AlmacenAMostrar->ObtenerDatos();

            $FiltroColumna = ((isset($_GET['Columna']))?((!empty($_GET['Columna']))?((is_numeric($_GET['Columna']))?(($_GET['Columna'] > 0 && $_GET['Columna'] < 4)?$_GET['Columna']:'1'):'1'):'1'):'1');
            $FiltroColumna = intval($FiltroColumna);
            $_GET['Columna'] = $FiltroColumna;
            $FiltroOrden = ((isset($_GET['Orden']))?((!empty($_GET['Orden']))?((is_numeric($_GET['Orden']))?(($_GET['Orden'] > 0 && $_GET['Orden'] < 3)?$_GET['Orden']:'1'):'1'):'1'):'1');
            $FiltroOrden = intval($FiltroOrden);
            $_GET['Orden'] = $FiltroOrden;

            $FiltroColumna = (($FiltroColumna == 3)?'`inventario`.`existencia`':(($FiltroColumna == 2)?'`productos`.`nombre`':'`inventario`.`idProducto`'));
            $FiltroOrden = (($FiltroOrden == 2)?'DESC':'ASC');

            $lol = "Columna(".$FiltroColumna.") y Orden(".$FiltroOrden.")";
        }

        
        
        


        $NombreTipoMovimiento = (($TipoDeMovimiento == 3)?'Ajuste de inventario':(($TipoDeMovimiento == 2)?'Venta':'Compra'));

        //echo 'Quiero ver '.$NombreTipoMovimiento.' numero '.$NroDeMovimiento.'<br>';
        $MovimientosDelTipoSeleccionado = $AlmacenAMostrar->ObtenerAjusteDeInventarioPorTipo($TipoDeMovimiento);
        
        
        
        $HayMovimientosAMostrar = (!empty($MovimientosDelTipoSeleccionado));
        if($HayMovimientosAMostrar){
            $NroMovEncontrados = count($MovimientosDelTipoSeleccionado);
            //echo 'Encontrados '.$NroMovEncontrados.' movimientos: ';
            //print_r($MovimientosDelTipoSeleccionado);
            if($NroMovEncontrados < $NroDeMovimiento){
                $NroDeMovimiento = 1;
            }
            $idMovABuscar = $MovimientosDelTipoSeleccionado[($NroDeMovimiento - 1)];
            //echo '<br>Buscando datos de ajustedeinventario #'.$MovimientosDelTipoSeleccionado[($NroDeMovimiento - 1)];
            $search = $BaseDeDatos->consultar("SELECT * FROM `ajustedeinventario` WHERE (`ajustedeinventario`.`id` = $idMovABuscar)");
            if(empty($search)){
                $HayMovimientosAMostrar = false;
            }else{
                $DatosDelMovimiento = $search[0];
                //echo '<br>Datos del mov: '; print_r($DatosDelMovimiento);
                $DatosDeHistorial = $Auditoria->BuscarRegistro(1, 7, $idMovABuscar);
                if(!empty($DatosDeHistorial)){
                    $DatosDeHistorial = $DatosDeHistorial[0];
                }
                //echo '<br>Datos historial del mov: '; print_r($DatosDeHistorial);
                
                $ProductosDelMov = $BaseDeDatos->consultar("SELECT `detallesdeajuste`.*, `productos`.`nombre`, `productos`.`idCategoria`, `productos`.`idUnidadDeMedida`, `productos`.`ULRImagen` FROM `detallesdeajuste` iNNER JOIN `productos` ON `detallesdeajuste`.`idProducto` = `productos`.`id` WHERE (`idAlmacenModificado` =$AlmacenAMostrar->id AND `idListaDeAjuste` = $idMovABuscar)");
                //echo '<br>Datos productos del mov: '; print_r($ProductosDelMov);
            }
            
        }else{
            //echo 'No hay movimientos a mostrar';
        }
        

    }else{
        header('Location: ../../Error.php?error=404&desc=11');   
    }
}else{
    header('Location: ../../Error.php?error=402&desc=2');
}



$SQLDeConsulta = "SELECT `inventario`.* FROM `inventario` 
INNER JOIN `productos` ON `inventario`.`idProducto` = `productos`.`id` 
WHERE `idAlmacen` = ".$_GET['id']." ORDER BY ".$FiltroColumna." ".$FiltroOrden;
$lol = $SQLDeConsulta;
$ProductosDelAlmacen = $BaseDeDatos->consultar($SQLDeConsulta);

?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $GLOBALS['nombreCorto'];?>: <?php echo $DatosAMostrar['nombre'];?></title>
    <link rel="stylesheet" href="estilos_Almacen.css">
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
            <p><?php echo $DatosAMostrar['nombre'];?></p>
            <div class="Cuadrito" href=""></div>
        </a>
    </div>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER();?></p>
    </div>
    <article>
        <b class="TituloNombreDelAlmacen"><?php echo $DatosAMostrar['nombre'];?></b>
        <p hidden id="TopeDeTablaDeProductos" class="RespuestaDelCliente">Estado: <span class="PuntoDeEstado En espera"><?php echo (($DatosAMostrar['idEstado'] == 51)?'Activo':'Inactivo');?></span></p>
        <div class="DatosDelCliente">
            <div class="FilaDato">
                <b>ID</b>
                <span>:</span>
                <span class="RespuestaDeFila" id="IDDeEntidad">
                    <?php echo $DatosAMostrar['id'];?>
                </span>
           </div>
           <div class="FilaDato">
                <b>Dirección</b>
                <span>:</span>
                <span class="RespuestaDeFila">
                    <?php echo $DatosAMostrar['direccion'];?>
                </span>
           </div>
           <div class="FilaDato">
                <b>Productos</b>
                <span>:</span>
                <span class="RespuestaDeFila">
                    <?php echo $DatosAMostrar['nroDeProductos'];?>
                </span>
           </div>
           <div class="FilaDato">
                <b>Creado</b>
                <span>:</span>
                <span class="RespuestaDeFila">
                    <?php echo $Tiempo->ConvertirFormato($DatosAMostrar['fCreacion'], 'BaseDeDatosConTiempo', 'UsuarioConTiempo');?>
                </span>
           </div>
        </div>
        <br>
        <form method="get" class="CajaDeFiltros">
            <button hidden id="BotonBuscar">Buscar</button>
            <input hidden type="text" name="id" value="<?php echo $_GET['id'];?>">
            <b>Orden: </b>
            <select name="Columna" id="SelectDeColumna">
                <option value="1" <?php if($_GET['Columna'] == 1) echo 'selected';?>>ID</option>
                <option value="2" <?php if($_GET['Columna'] == 2) echo 'selected';?>>Nombre</option>
                <option value="3" <?php if($_GET['Columna'] == 3) echo 'selected';?>>Existencia</option>
            </select>
            <select name="Orden" id="SelectDeOrden">
                <option <?php if($_GET['Orden'] == 1) echo 'selected';?> value="1">Ascendente</option>
                <option <?php if($_GET['Orden'] == 2) echo 'selected';?> value="2">Descendente</option>
            </select>
        </form>
        <div class="TablaDeProductos">
            <div class="TituloDeTabla">Productos</div>
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaImagen">Imagen</celda>
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaNombre">Nombre</celda>
                <celda class="ColumnaExistencia" title="Cantidad existente en este almacén">En almacén</celda>
                <celda class="ColumnaCategoria" title="Cantidad existente en el inventario">En inventario</celda>
                <celda class="ColumnaVerMas">Detalles</celda>
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
                    foreach($ProductosDelAlmacen as $ProductoConCantidad){
                        $ProductoADelAlmacen = new producto($ProductoConCantidad['idProducto']);
                        $DatosDelProducto = $ProductoADelAlmacen->ObtenerDatos();

                        $ContultaDeInventario = $BaseDeDatos->consultar("SELECT `existencia` FROM `inventario` WHERE `idProducto` = ".$ProductoConCantidad['idProducto']);
                        $CantidadEnInventario = 0;
                        foreach($ContultaDeInventario as $rowDeExistencia){
                            $CantidadEnInventario = $rowDeExistencia['existencia'] + $CantidadEnInventario;
                        }
                        
                        echo '
                        <row id="RowDeProducto-000002">
                            <celda class="ColumnaImagen">
                                <img src="../../Imagenes/Productos/'.((empty($DatosDelProducto['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProducto['ULRImagen']).'" alt="">
                            </celda>
                            <celda class="ColumnaID">'.$ProductoConCantidad['idProducto'].'</celda>
                            <celda class="ColumnaNombre">'.$DatosDelProducto['nombre'].'</celda>
                            <celda title="'.$DatosDelProducto['nombreUM'].'" class="ColumnaExistencia">'.(($DatosDelProducto['idCategoria'] == 1)?$ProductoConCantidad['existencia']:$ProductoConCantidad['existencia']).'</celda>
                            <celda title="'.$DatosDelProducto['nombreUM'].'" class="ColumnaCategoria">'.(($DatosDelProducto['idCategoria'] == 1)?$CantidadEnInventario:$CantidadEnInventario).'</celda>
                            <celda class="ColumnaVerMas">
                                <a target="_blank" href="../../Productos/Producto/?id='.$ProductoConCantidad['idProducto'].'">Abrir</a>
                            </celda>
                        </row>
                        ';
                        
                    }

                }
                
                ;?>
            </div>
        </div>
        <br>
        <span id="TopeDeMovimientos" class="fi-rr-exchange TituloDeSectionDelArticle"> MOVIMIENTOS:</span>
        <form method="get" class="BotonesTipoDeMovimientoAMostrar" id="FormularioDeMovimiento">
            <input hidden name="id" type="number" value="<?php echo $_GET['id'];?>">
            <a href="<?php echo '?id='.$_GET['id'].'&TipoDeMovimiento=1&NroDeMovimiento=1#TopeDeMovimientos';?>" <?php echo (($TipoDeMovimiento == 1)?'class="TipoDeMovimientoSeleccionado"':'');?>>Compras</a>
            <a href="<?php echo '?id='.$_GET['id'].'&TipoDeMovimiento=2&NroDeMovimiento=1#TopeDeMovimientos';?>" <?php echo (($TipoDeMovimiento == 2)?'class="TipoDeMovimientoSeleccionado"':'');?>>Ventas</a>
            <a href="<?php echo '?id='.$_GET['id'].'&TipoDeMovimiento=3&NroDeMovimiento=1#TopeDeMovimientos';?>" <?php echo (($TipoDeMovimiento == 3)?'class="TipoDeMovimientoSeleccionado"':'');?>>Ajustes de inventario</a>
        </form>
        <div class="Flex">
            <div class="CartaMovimiento">
                <span class="TituloDeMovimiento"><?php echo ((empty($MovimientoAMostrar))?'':$NombreTipoMovimiento.' #'.$MovimientoAMostrar['id']);?></span>
                <div class="DescripcionDelMovimiento">
                    
                    <?php
                    if(!$HayMovimientosAMostrar || empty($ProductosDelMov)){
                        echo '
                            <div class="MovimientoVacio">No hay movimiento a mostrar</div>
                        ';
                    }else{
                        echo '<div class="RowsDeMovimientos">';
                        foreach($ProductosDelMov as $ProductoEnMovimiento){
                            $flechita = 'up';
                            $minusvalido = '';
                            if(isset($_GET['TipoDeMovimiento']) && $_GET['TipoDeMovimiento']==2){
                                $flechita = 'down';
                                $minusvalido = '-';
                            }else{
                                $flechita = ($ProductoEnMovimiento['cantidad'] > 0? 'up':'down');
                            }
                            echo '
                            <div class="RowMovimiento">
                                <span class="FlechitaDelPM"> <i class="fi-rr-caret-'.$flechita.'"></i></span>
                                <span class="EspacioImagenMovimiento">
                                    <img src="../../Imagenes/Productos/'.((empty($ProductoEnMovimiento['ULRImagen']))?'ImagenPredefinida_Productos.png':$ProductoEnMovimiento['ULRImagen']).'" alt="">
                                </span>
                                <span class="NombreDelPM">'.$ProductoEnMovimiento['nombre'].'</span>
                                <span class="CantidadDelPM">'.$minusvalido.$ProductoEnMovimiento['cantidad'].'</span>
                            </div>
                            ';
                        }

                        if(count($ProductosDelMov) > 8){
                            echo '<div class="rellenoxd">a</div>';
                        }

                        echo '</div>';
                    }
                    ?>
                </div>
                <?php
                    if($HayMovimientosAMostrar){
                        echo '
                        <div class="EspacioDelBotonParaMasInfoDelMovimiento">
                            <a target="_blank" href="../../Inventario/Cambios?descripcion='.$idMovABuscar.'">Ver información detallada</a>
                        </div>
                    ';
                    }
                ?>
            </div>

            <?php
            if(!empty($DatosDelMovimiento)){
                echo '
                <div class="DetallesDelMovimientoEnSi">
                    <span class="TitulloDetalles">Descripción</span>
                    <p>'.$DatosDelMovimiento['descripcion'].'</p>
                    <div class="CositasDelMovmiento">
                        <div>
                            <b>N° productos alterados:</b>
                            <span>'.count($ProductosDelMov).'</span>
                        </div>
                        <div>
                            <b>Usuario:</b>
                            <span>'.$DatosDeHistorial['nombreDeUsuario'].'</span>
                        </div>
                        <div>
                            <b>Fecha:'.$NroMovEncontrados.'</b>
                            <span>'.$Tiempo->ConvertirFormato($DatosDeHistorial['fechaCreacion'], 'BaseDeDatosConTiempo', 'UsuarioConTiempo').'</span>
                        </div>
                    </div>
                    <div class="BotonesNavDeMov">
                        <a  '.($NroDeMovimiento>1? 'class="BotonesNavDeMovDisponible" href="?id='.$_GET['id'].'&TipoDeMovimiento='.$TipoDeMovimiento.'&NroDeMovimiento='.($NroDeMovimiento - 1).'#TopeDeMovimientos"':'').'> <span class="fi-sr-angle-circle-left"></span> Ir a más reciente</a>
                        <a '.($NroMovEncontrados>$NroDeMovimiento? 'class="BotonesNavDeMovDisponible" href="?id='.$_GET['id'].'&TipoDeMovimiento='.$TipoDeMovimiento.'&NroDeMovimiento='.($NroDeMovimiento + 1).'#TopeDeMovimientos"':'').'>Ir a más antigua <span class="fi-sr-angle-circle-right"></span></a>
                    </div>
                </div>
                ';
            }
            ?>
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Almacenes.png" alt="">
                <b>Almacenes</b>
            </div>
            <a href="../../Modificar/Almacen/?id=<?php echo $_GET['id'];?>"><i class="fi-rr-pencil"></i> Modificar almacén</a>
            <a href="../../Inventario"><i class="fi-rr-package"></i> Gestionar inventario</a>
            <a href="#TopeDeMovimientos"><i class="fi-rr-exchange"></i> Ver movimientos</a>
            <button id="deleteEntityButton" style="width: 210px;"> <i class="fi-rr-trash"></i> Eliminar almacén</button>
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="Almacen.js"></script>
</body>
</html>