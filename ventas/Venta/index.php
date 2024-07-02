<?php

include_once('../../Otros/clases.php');

$BaseDeDatos = new conexion;
$Tiempo = new AsistenteDeTiempo();
$PrecioAcumuladoDeMateriales = 0;
$PrecioAcumuladoDeMaquinas = 0;
$PrecioAcumuladoDeManos = 0;


if(isset($_GET['id'])){
    $RespuestaDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE ( (`id` = ".$_GET['id'].") AND (`idEstado` != 35))");
    
    if(!empty($RespuestaDeConsulta)){
        $budget = new budget($_GET['id']);
        //Obtengo los datos de la coti y de cada categoria de productos
        $CotizacionAMostrar = new cotizacion($_GET['id']);
        $DatosDeLaCoti = $CotizacionAMostrar->ObtenerDatos();
        $MaterialesAMostrar = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` WHERE ( (`idCotizacion` = ".$_GET['id'].") AND 
        ( (SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 1 ) )");
        $MaquinasAMostrar = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` WHERE ( (`idCotizacion` = ".$_GET['id'].") AND 
        ( (SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 2 ) )");
        $ManosAMostrar = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` WHERE ( (`idCotizacion` = ".$_GET['id'].") AND 
        ( ((SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 3) OR ((SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 4)) );");
        

        //Si tengo cliente, obtengo sus datos
        if(!empty($DatosDeLaCoti['cedulaCliente'])){
            $ClienteAMostrar = new cliente($DatosDeLaCoti['cedulaCliente']);
            $DatosDelCliente = $ClienteAMostrar->ObtenerDatos();
        }

        //Si tengo fecha de expiracion, preparo el texto
        $FechaDeExpiracion = "";
        if(!empty($DatosDeLaCoti['fechaExpiracion'])){
            $FechaDeExpiracion = $Tiempo->ConvertirFormato($DatosDeLaCoti['fechaExpiracion'], 'BaseDeDatos', 'Usuario');
        }

        //Si tengo fech ade creacion, lo preparo tambien
        $FechaDeCreacion = "";
        if(!empty($DatosDeLaCoti['creado'])){
            $FechaDeCreacion = "El ".$Tiempo->ConvertirFormato($DatosDeLaCoti['creado'], 'BaseDeDatosConTiempo', 'UsuarioConTiempo');
        }

        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'SimboloMonedaNaciona';");

        if(empty($DatosDelSistema)){
            $simboloModenaNacional = 'Bs';
        }else{
            $simboloModenaNacional = $DatosDelSistema[0]['valor'];
        }

        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'NombreMonedaNacional';");

        if(empty($DatosDelSistema)){
            $NombreMonedaNacional = 'Bolivar';
        }else{
            $NombreMonedaNacional = $DatosDelSistema[0]['valor'];
        }
        

        $DatosAMostrar = array(
            'id' => $DatosDeLaCoti['id'],
            'nombre' => $DatosDeLaCoti['nombre'],
            'respuesta' => $DatosDeLaCoti['estado'],
            'tipoDeDocumento' => ((empty($DatosDeLaCoti['cedulaCliente']))?'':$DatosDelCliente['tipoDeDocumento']),
            'rif' => $DatosDeLaCoti['cedulaCliente'],
            'razonSocial' => ((empty($DatosDeLaCoti['cedulaCliente']))?'':$DatosDelCliente['nombre']),
            'direccion' => ((empty($DatosDeLaCoti['cedulaCliente']))?'':$DatosDelCliente['direccion']),
            'telefono1' => ((empty($DatosDeLaCoti['cedulaCliente']))?'':$DatosDelCliente['numeroCompleto1']),
            'telefono2' => ((empty($DatosDeLaCoti['cedulaCliente']))?'':$DatosDelCliente['numeroCompleto2']),
            'correo' => ((empty($DatosDeLaCoti['cedulaCliente']))?'':$DatosDelCliente['correo']),
            'fechaExpiracion' => $FechaDeExpiracion,
            'creado' => $FechaDeCreacion,
            'pUtilidades' => $DatosDeLaCoti['pUtilidades'],
            'pIVA' => $DatosDeLaCoti['pIVA'],
            'pCASalario' => $DatosDeLaCoti['pCASalario'],
            'nombreMonedaNacional' => $NombreMonedaNacional,
            'simboloModenaNacional' => $simboloModenaNacional
        );
    }else{
        //La ID del GET no corresponde a ninguna cotizacion en la base de datos
        $DatosAMostrar = array(
            'id' => 'No existe',
            'nombre' => 'Esta cotización no existe',
            'respuesta' => 'desconocido',
            'tipoDeDocumento' => '',
            'rif' => '',
            'razonSocial' => '',
            'direccion' => '',
            'telefono1' => '',
            'telefono2' => '',
            'fechaExpiracion' => '',
            'pUtilidades' => '0',
            'pIVA' => '0',
            'pCASalario' => '0',
            'nombreMonedaNacional' => 'Bolivar',
            'simboloModenaNacional' => 'Bs'
        );
    }
}else{
    //No recibo ninguna id del GET
    $DatosAMostrar = array(
        'id' => 'No existe',
        'nombre' => 'Esta cotización no existe',
        'respuesta' => 'desconocido',
        'tipoDeDocumento' => '',
        'rif' => '',
        'razonSocial' => '',
        'direccion' => '',
        'telefono1' => '',
        'telefono2' => '',
        'fechaExpiracion' => '',
        'pUtilidades' => '0',
        'pIVA' => '0',
        'pCASalario' => '0',
        'nombreMonedaNacional' => 'Bolivar',
        'simboloModenaNacional' => 'Bs'
    );
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: <?php echo $DatosAMostrar['nombre'];?></title>
    <link rel="stylesheet" href="estilos_cotizacion.css">
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
            <p>#<?php echo $DatosAMostrar['id'];?></p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <modal id="ModalGenerarPDF">
        <div class="Ventanita">
            <button class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
            <span class="TituloDelModal">GENERAR REPORTE PDF</span>
            <p><span class="numerillo">1</span> Indica la cantidad a cotizar:</p>
            <div class="cantidadContainer">
                <span>Cantidad:</span>
                <input id="cantidadACotizar_input" type="text" value="1" onkeypress="return soloNumerosPositivos(this, event)" maxlength="8" onclick="this.select()">
            </div>
            <p><span class="numerillo">2</span> Indica la moneda:</p>
            <div class="OtroDivPorqueSoyIdiota">
                <div class="CajaDeOpciones">
                    <a id="BotonMonedaInternacional" class="Opcion Pulsable">
                        <span class="TituloDeMoneda">Moneda internacional</span>
                        <img src="../../Imagenes/Dolar.png" alt="">
                    </a>
                    <div class="VolverASeleccion">
                        <input hidden id="InputMostrarSeleccionDeTasa" type="checkbox" name="" id="">
                        <button id="BotonVolverASeleccion"> <i class="fi-sr-angle-left"></i> </button>
                    </div>
                    <div id="BotonMonedaNacional" class="Opcion Pulsable">
                        <span class="TituloDeMoneda">Moneda nacional</span>
                        <img src="../../Imagenes/Bolivar.png" alt="">
                    </div>
                    <div class="CajaDeSeleccionDeTasa">
                        <div class="ParaEsoEstanLosDivPaUsarlos">
                            <span class="TituloDeCambioDeTasa"> <i class="fi-rr-coins"></i> Tasa de cambio actual</span>
                            <div class="Cambio">
                                <span>1<span style="color: green;">$</span></span>
                                <i class="fi-rr-exchange"></i>
                                <div class="DatosDelCambio">
                                    <input id="InputTasa" onkeypress="return SoloNumerosFloat(event)" onfocus="this.select()" maxlength="9" type="text">
                                    <div><?php echo $DatosAMostrar['simboloModenaNacional'];?></div>
                                </div>
                                <div class="apiInfo">
                                    <i class="fi fi-rr-info"></i>
                                    <div class="info">Consultando Tasa de cambio establecida por el BCV..</div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <div style="position: relative;">
                                <!-- <div  style="text-align: center; margin-bottom: 5px;">Opción de moneda:</div>
                                <div class="twinButtons">
                                    <button id="BotonGenerarReporteConTasaEspecifica" class="BotonDeAqui">Única</button>
                                    <button id="newOptionButton" class="BotonDeAqui">Múltiple</button>
                                </div> -->
                                <div class="twinButtons2">
                                    <button id="uniqueDivisaButton" class=""><i class="fi fi-rr-coin"></i><span>Moneda</span><span>única</span></button>
                                    <button id="multyDivisaButton" class=""><i class="fi fi-rr-coins"></i><span>Moneda</span><span>múltiple</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <article>
        <?php
            if(isset($_GET['alert'])){
                if($_GET['alert'] == 'nuevo'){
                    echo '
                    <div id="AlertaGuardadoConExito" class="Alerta">
                        <p><i class="fi-sr-bookmark"></i> Se ha guardado exitosamente!</p>
                        <button id="BotonEliminarAlertaGuardadoConExito"> <i class="fi-rr-cross-small"></i> </button>
                    </div>
                    ';
                }
            }
        ?>
        <b class="TituloNombreDeLaCot"> <?php echo $DatosAMostrar['nombre'];?> </b>
        <p class="RespuestaDelCliente">Respuesta del cliente: <?php echo '<span class="PuntoDeEstado '.$DatosAMostrar['respuesta'].'">'.$DatosAMostrar['respuesta'].'</span>';?></p>
        <div class="DatosDelCliente">
            <div class="FilaDato">
                <b>Cliente</b>
                <span>:</span>
                <a title="Ver cliente" <?php echo ((empty($DatosAMostrar['rif']))?'':'href="../../Clientes/Cliente?rif='.$DatosAMostrar['rif'].'"')?> class="RespuestaDeFila" target="_blank">
                    <?php 
                        if(empty($DatosAMostrar['rif'])){
                            echo '<i style="color: gray;">Ningún cliente fue indicado para esta cotización.</i>';
                        }else{
                            echo '
                            <p>'.$DatosAMostrar['tipoDeDocumento'].' - '.zerofill($DatosAMostrar['rif'], 9).'</p>
                            -
                            <p>'.$DatosAMostrar['razonSocial'].'</p>
                            ';
                        }
                    ?>
                </a>
           </div>
            <?php
                if(!empty($DatosAMostrar['rif'])){
                    echo '
                        <div class="FilaDato">
                            <b>Dirección</b>
                            <span>:</span>
                            <span class="RespuestaDeFila">
                                '.$DatosAMostrar['direccion'].'
                            </span>
                        </div>
                        <div class="FilaDato">
                            <b>Teléfono</b>
                            <span>:</span>
                            <span class="RespuestaDeFila">
                                '.$DatosAMostrar['telefono1'].((empty($DatosAMostrar['telefono2']))?'':' / '.$DatosAMostrar['telefono2']).'
                            </span>
                        </div>
                    ';
                }
            ?>
        </div>
        <label class="LabelMostrarMasInfo">
            <p class="pejemplo">Más información</p>
            <input hidden type="checkbox" name="" id="BotonPaMostrarMas" class="CheckMostrarMasInfo"> 
            <div class="DivMostrarMasInfo">
                <div <?php echo ((!empty($DatosAMostrar['rif']))?'':'style="display: none;"')?> class="FilaDato">
                    <b>Correo</b>
                    <span>:</span>
                    <span class="RespuestaDeFila">
                        <?php echo ((empty($DatosAMostrar['correo']))?'<i>Correo no especificado</i>':$DatosAMostrar['correo']);?>
                    </span>
                </div>
                <div class="FilaDato">
                    <b>ID</b>
                    <span>:</span>
                    <span class="RespuestaDeFila" id="idEntity"><?php echo $_GET['id'];?></span>
                </div>
                <div class="FilaDato">
                    <b>Vigencia</b>
                    <span>:</span>
                    <span class="RespuestaDeFila">
                        <?php echo ((empty($DatosAMostrar['fechaExpiracion']))?'<i>Esta cotización no tiene fecha de expiración.</i>':'Hasta el '.$DatosAMostrar['fechaExpiracion']);?>
                    </span>
                </div>
                <div class="FilaDato">
                    <b>Creado</b>
                    <span>:</span>
                    <span class="RespuestaDeFila">
                        <?php echo ((empty($DatosAMostrar['creado']))?'<i>No se registró la fecha de creación.</i>':$DatosAMostrar['creado']);?>
                    </span>
                </div>
            </div>
        </label>
        <i class="Expiracion"> <?php if(!empty($DatosAMostrar['fechaExpiracion'])&&$RespuestaDeConsulta[0]['idEstado']==33){ echo 'Esta cotización es válida hasta el '.$DatosAMostrar['fechaExpiracion'];} ?> </i>
        <div class="EspacioParaLaCotiComoTal">
            <span class="TituloDeTipoDeProducto">Material</span>
            <div class="ColumnasDeTabla">
                <span class="TituloDeColumna ColumnaImagen">Imagen</span>
                <span class="TituloDeColumna ColumnaID">ID</span>
                <span class="TituloDeColumna ColumnaNombre8">Nombre</span>
                
                <span class="TituloDeColumna ColumnaCantidad">Cantidad</span>
                <span class="TituloDeColumna ColumnaDias">Unidad</span>
                <span class="TituloDeColumna ColumnaPrecio">Precio</span>
                <span class="TituloDeColumna ColumnaSubTotal">Total</span>
            </div>
            <div class="ContenedorDeRows">
                <?php
                    if(isset($_GET['id'])){
                        if(empty($MaterialesAMostrar)){
                            echo '
                            <row class="RowSinProductos">
                                Esta cotización no tiene materiales
                            </row>';
                        }else{
                            foreach($MaterialesAMostrar as $Row){
                                $ProductoDelRow = new producto($Row['idProducto']);
                                $DatosDelProductoDelRow = $ProductoDelRow->ObtenerDatos();
                                echo '
                                <row>
                                    <span class="ColumnaImagen CeldaDeRow">
                                        <img src="../../Imagenes/Productos/'.((empty($DatosDelProductoDelRow['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProductoDelRow['ULRImagen']).'" alt="">
                                    </span>
                                    <span class="ColumnaID CeldaDeRow">'.$Row['idProducto'].'</span>
                                    <span class="ColumnaNombre8 CeldaDeRow">'.$DatosDelProductoDelRow['nombre'].'</span>
                                    <span class="ColumnaCantidad CeldaDeRow"><span>'.$Row['cantidad'].'</span></span>
                                    <span class="ColumnaDias CeldaDeRow" title="'.$DatosDelProductoDelRow['nombreUM'].'">'.$DatosDelProductoDelRow['simboloUM'].'</span>
                                    <span class="ColumnaPrecio CeldaDeRow">'.$Row['precioUnitario'].'$</span>
                                    <span class="ColumnaSubTotal CeldaDeRow">'.$Row['precioMultiplicado'].'$</span>
                                </row>
                                ';
                                $PrecioAcumuladoDeMateriales = $PrecioAcumuladoDeMateriales + number_format($Row['precioMultiplicado'], 2);
                            }
                        }   
                    }
                ?>
            </div>
            <span class="TituloDeTipoDeProducto">equipo y maquinaria</span>
            <div class="ColumnasDeTabla">
                <span class="TituloDeColumna ColumnaImagen">Imagen</span>
                <span class="TituloDeColumna ColumnaID">ID</span>
                <span class="TituloDeColumna ColumnaNombre8">Nombre</span>
                
                <span class="TituloDeColumna ColumnaCantidad">Cantidad</span>
                <span class="TituloDeColumna ColumnaDias">Desgaste</span>
                <span class="TituloDeColumna ColumnaPrecio">Precio</span>
                <span class="TituloDeColumna ColumnaSubTotal">Total</span>
            </div>
            <div class="ContenedorDeRows">
                <?php
                    if(isset($_GET['id'])){
                        if(empty($MaquinasAMostrar)){
                            echo '
                            <row class="RowSinProductos">
                                Esta cotización no tiene maquinarias ni herramientas
                            </row>';
                        }else{
                            foreach($MaquinasAMostrar as $Row){
                                    $ProductoDelRow = new producto($Row['idProducto']);
                                    $DatosDelProductoDelRow = $ProductoDelRow->ObtenerDatos();
                                    $product = new product($Row['idProducto']);
                                    echo '
                                    <row>
                                        <span class="ColumnaImagen CeldaDeRow">
                                            <img src="../../Imagenes/Productos/'.((empty($DatosDelProductoDelRow['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProductoDelRow['ULRImagen']).'" alt="">
                                        </span>
                                        <span class="ColumnaID CeldaDeRow">'.$Row['idProducto'].'</span>
                                        <span class="ColumnaNombre8 CeldaDeRow">'.$DatosDelProductoDelRow['nombre'].'</span>
                                        <span class="ColumnaCantidad CeldaDeRow"><span>'.$Row['cantidad'].'</span></span>
                                        <span class="CeldaDeRow ColumnaDias">'.$product->getDefaultSpoilage().'</span>
                                        <span class="ColumnaPrecio CeldaDeRow">'.$Row['precioUnitario'].'$</span>
                                        <span class="ColumnaSubTotal CeldaDeRow">'.$Row['precioMultiplicado'].'$</span>
                                    </row>
                                    ';
                                    $PrecioAcumuladoDeMaquinas = $PrecioAcumuladoDeMaquinas + number_format($Row['precioMultiplicado'], 2);
                            }
                        }
                    }
                ?>
            </div>
            <span class="TituloDeTipoDeProducto">Mano de obra</span>
            <div class="ColumnasDeTabla">
                <span class="TituloDeColumna ColumnaImagen">Imagen</span>
                <span class="TituloDeColumna ColumnaID">ID</span>
                <span class="TituloDeColumna ColumnaNombre8">Nombre</span>
                
                <span class="TituloDeColumna ColumnaCantidad">Cantidad</span>
                <span class="TituloDeColumna ColumnaDias">Días</span>
                <span class="TituloDeColumna ColumnaPrecio">Precio</span>
                <span class="TituloDeColumna ColumnaSubTotal">Total</span>
            </div>
            
            <div class="ContenedorDeRows">
                <?php
                    if(isset($_GET['id'])){
                        $PrecioDeManosSoloManos = 0;
                        if(empty($ManosAMostrar)){
                            echo '
                            <row class="RowSinProductos">
                                Esta cotización no tiene mano de obra
                            </row>';
                        }else{
                            
                            foreach($ManosAMostrar as $Row){
                                $ProductoDelRow = new producto($Row['idProducto']);
                                $DatosDelProductoDelRow = $ProductoDelRow->ObtenerDatos();
                                $pedazos = explode('.',$Row['cantidad']);
                                
                                echo '
                                <row>
                                    <span class="ColumnaImagen CeldaDeRow">
                                        <img src="../../Imagenes/Productos/'.((empty($DatosDelProductoDelRow['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProductoDelRow['ULRImagen']).'" alt="">
                                    </span>
                                    <span class="ColumnaID CeldaDeRow">'.$Row['idProducto'].'</span>
                                    <span class="ColumnaNombre8 CeldaDeRow">'.$DatosDelProductoDelRow['nombre'].'</span>
                                    
                                    <span class="ColumnaCantidad CeldaDeRow"><span title="'.$DatosDelProductoDelRow['nombreUM'].'">'.$pedazos[0].' '.$DatosDelProductoDelRow['simboloConEstiloUM'].'</span></span>
                                    <span class="CeldaDeRow ColumnaDias">x '.$pedazos[1].'</span>
                                    <span class="ColumnaPrecio CeldaDeRow">'.$Row['precioUnitario'].'$</span>
                                    <span class="ColumnaSubTotal CeldaDeRow">'.$Row['precioMultiplicado'].'$</span>
                                </row>
                                ';
                                
                                $PrecioAcumuladoDeManos = $PrecioAcumuladoDeManos + $Row['precioMultiplicado'];
                                
                                if($DatosDelProductoDelRow['idCategoria'] == 3){
                                    $PrecioDeManosSoloManos = $PrecioDeManosSoloManos + number_format($Row['precioMultiplicado'], 2);
                                }

                            }
                        }
                    }
                    
                ?>
            </div>
            <footer>
            <div class="SubTotal">
                    <div class="TitulosDelTotal ColumnaDelFinalDeCot">
                        <b>Costo en materiales</b>
                        <b>Costo en equipo</b>
                        <b>Costo en mano de obra</b>
                        <b title="Costo asociado al salario. Aplicado al personal descrito en Mano de obra.">Asociado al salario (<?php echo $DatosAMostrar['pCASalario'];?>%)</b>
                        <div class="PalitoDeSuma"></div>
                        <b>Costo de productos</b>
                        <b>Utilidades (<?php echo $DatosAMostrar['pUtilidades'];?>%)</b>
                        <div class="PalitoDeSuma"></div>
                        <b>Sub Total</b>
                        <b>I.V.A (<?php echo $DatosAMostrar['pIVA'];?>%)</b>
                    </div>
                    <div class="ColumnaDelFinalDeCot PuntosSeparadores">
                        <b>:</b>
                        <b>:</b>
                        <b>:</b>
                        <b>:</b>
                        <div class="PalitoDeSuma"></div>
                        <b>:</b>
                        <b>:</b>
                        <div class="PalitoDeSuma"></div>
                        <b>:</b>
                        <b>:</b>
                    </div>
                    <?php
                        $PrecioAsociadoAlSalario = $PrecioDeManosSoloManos * $DatosAMostrar['pCASalario'] / 100;
                        $PrecioGeneral = $PrecioAcumuladoDeMateriales + $PrecioAcumuladoDeMaquinas + $PrecioAcumuladoDeManos + $PrecioAsociadoAlSalario;
                        $PreioUtilidades = $PrecioGeneral * $DatosAMostrar['pUtilidades'] / 100;
                        $SubTotal = $PrecioGeneral + $PreioUtilidades;
                        $PrecioIVA = $SubTotal * $DatosAMostrar['pIVA'] / 100;
                        $TotalTotal = $SubTotal + $PrecioIVA;
                    ?>
                    <div class="PreciosDelTotal ColumnaDelFinalDeCot">
                        <p id="PrecioSubTotalMaterial"><?php echo number_format($PrecioAcumuladoDeMateriales, 2, '.', "");?></p>
                        <p id="PrecioSubTotalMaquinaria"><?php echo number_format($PrecioAcumuladoDeMaquinas, 2, '.', "");?></p>
                        <p id="PrecioSubTotalMano"><?php echo number_format($PrecioAcumuladoDeManos, 2, '.', "");?></p>
                        <p><?php echo number_format($PrecioAsociadoAlSalario, 2, '.', "");?></p>
                        <div class="PalitoDeSuma"></div>
                        <b><?php echo number_format($PrecioGeneral, 2, '.', "");?></b>
                        <b><?php echo number_format($PreioUtilidades, 2, '.', "");?></b>
                        <div class="PalitoDeSuma"></div>
                        <b><?php echo number_format($SubTotal, 2, '.', "");?></b>
                        <b><?php echo number_format($PrecioIVA, 2, '.', "");?></b>
                    </div>
                    <div class="ColumnaDelFinalDeCot">
                        <b>$</b>
                        <b>$</b>
                        <b>$</b>
                        <b>$</b>
                        <div class="PalitoDeSuma"></div>
                        <b>$</b>
                        <b>$</b>
                        <div class="PalitoDeSuma"></div>
                        <b>$</b>
                        <b>$</b>
                    </div>
                </div>
                <div class="Total">
                    <p>Total: </p>
                    <p id="PrecioTotal"><?php echo number_format($TotalTotal, 2, '.', "");?></p>
                    <p>$</p>
                </div>
            </footer>
        </div>
        <i class="Expiracion"> <?php if(!empty($DatosAMostrar['fechaExpiracion'])&&$RespuestaDeConsulta[0]['idEstado']==33){ echo 'Esta cotización es válida hasta el '.$DatosAMostrar['fechaExpiracion'];} ?> </i>
        <?php
        if($RespuestaDeConsulta[0]['idEstado']==33){
            echo '
            <section class="SectionDeBotonesDeConfirmacion">
                <a href="../Cancelar/?id='.$_GET['id'].'" class="Agrupacion_BotonMostrarModalRechazar"> <i class="fi-rr-cross-small"></i> Rechazar</a>
                <a href="../Confirmar/?id='.$_GET['id'].'"> <i class="fi-rr-check"></i> Confirmar compra</a>
            </section>
            ';
        }
        ?>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Ventas.png" alt="">
                <b>Ventas</b>
            </div>
            <?php
            if($budget->getIdStatus()=='33'){
                echo '<a id="BotonEditar" href="../../Modificar/Ventas/?id='.$budget->getId().'"> <i class="fi-rr-pencil"></i> Modificar cotización</a>';
                echo '<a id="BotonEliminar"> <i class="fi-rr-trash"></i> Eliminar cotización</a>';
            }
            ?>
            <a id="BotonAbrirMenuDeCrearReporte" > <i class="fi-rr-print"></i> Generar reporte PDF</a>
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="cotizacion.js"></script>
</body>
</html>