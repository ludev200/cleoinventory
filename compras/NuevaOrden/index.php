<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();
$Inventario = new inventario();
$DatosDelInventario = $Inventario->ObtenerDatos();
$Problemas = "";
$ConsultaDeCompraEnBorrador = $BaseDeDatos->consultar("SELECT * FROM `ordenesdecompra` WHERE `idEstado` = 65");


if(empty($ConsultaDeCompraEnBorrador)){
    $Compra = new compra(0);
}else{
    $Compra = new compra($ConsultaDeCompraEnBorrador[0][0]);
}

//Determino los datos a mostrar
$Alerta = "";
if($_POST){
    $DatosAMostrar = array(
        'ProductosListados' => $_POST['ProductosDelAlmacen'],
        'nombreDeOrden' => $_POST['nombreDeOrden'],
        'limiteDeTiempo' => $_POST['limiteDeTiempo']
    );

    
}else{
    $ConsultaDeComprasEnDB = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `ordenesdecompra` WHERE `idEstado` < 65");
    $ProximaID = ($ConsultaDeComprasEnDB[0][0] + 1);

    $DatosAMostrar = array(
        'ProductosListados' => '',
        'nombreDeOrden' => 'Orden de compra #'.$ProximaID,
        'limiteDeTiempo' => '0'
    );

    
    if(isset($_GET['modo'])){
        if($_GET['modo'] == 1){
            //mostrar recomendado
            $ProductosRecomendados = $Inventario->ObtenerCompraRecomendada();
            $DatosAMostrar['ProductosListados'] = $ProductosRecomendados;
            $DatosAMostrar['nombreDeOrden'] = 'Orden de compra #'.$ProximaID;
            $DatosAMostrar['limiteDeTiempo'] = '1';

            $Alerta = '<i class="fi-rr-info"> Orden de compra recomendada según los productos faltantes en el inventario.</i>';
        }
    }else{
        if(!empty($ConsultaDeCompraEnBorrador)){
            //mostrar borrador
            $DatosDeCompraEnBorrador = $Compra->ObtenerDatos();
            $DatosAMostrar['nombreDeOrden'] = $DatosDeCompraEnBorrador['nombre'];
            $DatosAMostrar['ProductosListados'] = $DatosDeCompraEnBorrador['productosEnFormato'];

            $Alerta = '<i class="fi-rr-info"> Orden de compra almacenada anteriormente en el borrador.</i>';
        }
    }
}

//Aciones a realizar
if($_POST){
    if(isset($_POST['guardar'])){
        try{
            if(empty($ConsultaDeCompraEnBorrador)){
                $Problemas = $Compra->CrearNuevo($_POST, 63);    
                
            }else{
                $Problemas = $Compra->Actualizar($_POST, 63);
            }
        }catch(Exception $Error){
            $Problemas = $Error->getMessage();
        }
        
        
        if(empty($Problemas)){
            $ConsultaDeUltimoID = $BaseDeDatos->consultar("SELECT * FROM `ordenesdecompra` WHERE `idEstado` != 65 ORDER BY `id` DESC LIMIT 0,1");
            header("Location: ../Compra?id=".$ConsultaDeUltimoID[0]['id']);
        }
    }


    if(isset($_POST['borrador'])){
        try{
            if(empty($ConsultaDeCompraEnBorrador)){
                $Problemas = $Compra->CrearNuevo($_POST, 65);    
            }else{
                $Problemas = $Compra->Actualizar($_POST, 65);    
            }
        }catch(Exception $Error){
            $Problemas = $Error->getMessage();
        }
        
        if(empty($Problemas)){
            header("Location: ../");
        }
    }

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos_NuevaOrden.css">
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Nueva orden</title>
    <?php include('../../Otros/cabecera_N3.php');?>
    <nav id="ZonaDeCliente" class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../../">
            <p >Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="../">
            <p>Compras</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Nueva orden</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
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
                                <option value="1">Disponibles</option>
                                <option value="2">En alerta</option>
                                <option value="3">Agotados</option>
                            </select>
                        </div>
                        <div class="TablaDeProdcutos">
                            <div class="ColumnasDeTabla">
                                <celda class="ColumnaImagenP">Imagen</celda>
                                <celda class="ColumnaIDP">ID</celda>
                                <celda class="ColumnaNombre2SS">Nombre</celda>
                                <celda class="ColumnaExistencia">Existencia</celda>
                                <celda class="ColumnaAgregadoSS">Agregado</celda>
                            </div>
                            <div class="ContenedorDelFlexDeRows" id="ContenedorScrolleable">
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
        <?php //print_r($_POST); echo '<br><br>'; print_r($Problemas);?>
        <!--<span class="fi-rr-clipboard-list TituloDeSection"> LISTA DE PRODUCTOS A COMPRAR:</span>-->
        <input hidden form="FormularioNuevaOrden" type="text" name="ProductosDelAlmacen" id="InputProductosEnlistadosAlAlmacen" value="<?php echo $DatosAMostrar['ProductosListados'];?>">
        <div class="test">            
            <form method="post" id="FormularioNuevaOrden" class="ListaDeProductos">
                <div class="cositaspalnombreguapo">
                    <input name="nombreDeOrden" autocomplete="off" placeholder="Escribe un nombre o descripción" id="InputDeNombre" type="text" value="<?php echo $DatosAMostrar['nombreDeOrden'];?>">
                    <div id="DivDelPalitoDinamico" class="OtroNombre">
                        <div class="PalitoDelNombre"></div>
                        <?php echo $DatosAMostrar['nombreDeOrden'];?>
                    </div>
                </div>
                <div class="DetallesDeOrden">
                    <b>Límite de tiempo : </b>
                    <select name="limiteDeTiempo" id="">
                        <option <?php echo (($DatosAMostrar['limiteDeTiempo'] == 0)?'selected':'')?> value="0">No</option>
                        <option <?php echo (($DatosAMostrar['limiteDeTiempo'] == 1)?'selected':'')?> value="1">1 día</option>
                        <option <?php echo (($DatosAMostrar['limiteDeTiempo'] == 3)?'selected':'')?> value="3">3 días</option>
                        <option <?php echo (($DatosAMostrar['limiteDeTiempo'] == 7)?'selected':'')?> value="7">7 días</option>
                        <option <?php echo (($DatosAMostrar['limiteDeTiempo'] == 15)?'selected':'')?> value="15">15 días</option>
                        <option <?php echo (($DatosAMostrar['limiteDeTiempo'] == 30)?'selected':'')?> value="30">30 días</option>
                    </select>
                </div>
                <div class="TablaDeProductos">
                    <span class="TituloDeTabla">Productos a comprar</span>
                    <header>
                        <celda class="ColumnaImagen">Imagen</celda>
                        <celda class="ColumnaID">ID</celda>
                        <celda class="ColumnaProducto">Producto</celda>
                        <celda class="ColumnaCantidad">Cantidad</celda>
                    </header>
                    <div id="EspacioDeRowsDeLaTabla" class="RowsDeTabla">
                        <?php
                            if(empty($DatosAMostrar['ProductosListados'])){
                                echo '
                                    <row class="RowVacio">
                                        <span>No hay productos en esta orden de compra</span>
                                    </row>
                                ';
                            }else{
                                foreach(explode('¿',$DatosAMostrar['ProductosListados']) as $ProdXCant){
                                    $pedazos = explode('x', $ProdXCant);
                                    $ProductoAMostrarEnLista = new producto($pedazos[0]);
                                    $DatosDelProducto = $ProductoAMostrarEnLista->ObtenerDatos();

                                    $Cantidad = (($DatosDelProducto['idCategoria'] == 1)?$pedazos[1]:number_format($pedazos[1], 4, '.', ""));
                                    
                                    echo '
                                        <row id="RowDeProducto-'.$DatosDelProducto['id'].'">
                                            <celda class="ColumnaImagen"><img src="../../Imagenes/Productos/'.((empty($DatosDelProducto['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProducto['ULRImagen']).'" alt=""></celda>
                                            <celda class="ColumnaID">'.$DatosDelProducto['id'].'</celda>
                                            <celda class="ColumnaProducto">'.$DatosDelProducto['nombre'].'</celda>
                                            <celda class="ColumnaCantidad"><span title="'.(($DatosDelProducto['idEstado'] == 2)?'Este producto se encuentra bajo su nivel de alerta':'Este producto está agotado').'" class="fi-sr-comment-exclamation alertatipo'.$DatosDelProducto['idEstado'].'"></span>x '.$Cantidad.'</celda>
                                            <div class="CeldaOculta">
                                                <i title="Modificar la cantidad de este producto" id="BotonModificarProductoEspecifico-'.$DatosDelProducto['id'].'" class="fi-rr-pencil"></i>
                                                <i title="Eliminar este producto de la lista" id="BotonEliminarProductoEspecifico-'.$DatosDelProducto['id'].'" class="fi-rr-trash"></i>
                                            </div>
                                        </row>
                                ';
                                }
                                
                            }
                        ?>
                    </div>
                    <div class="BotonDinamicoAgregar">
                        <span class="fi-rr-plus-small" id="BotonDesplegable_AgregarProducto"> Agregar producto</span>
                    </div>
                </div>
            </form>
            <?php
            if(!empty($Alerta)){
                echo '
                    <div class="Avisito">
                        '.$Alerta.'
                    </div>
                ';
            }
            ?>
        </div>
        <span class="fi-rr-users TituloDeSection"> PROVEEDORES DISPONIBLES:</span>
        <section id="CajaDeProveedores" class="CajaDeProveedores">
            <div class="ProveedoresVacios">
                No hay proveedores disponibles
            </div>
        </section>
        <div class="coolFinalButtons">
            <a href="../" class="hovershadow">Salir</a>
            <button form="FormularioNuevaOrden" name="guardar" value="guardar" id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Compras.png" alt="">
                <b>Compras</b>
            </div>
            <button style="width: 210px;" id="BotonAbrirModalAgregarProducto"> <i class="fi-sr-apps-add"></i> Agregar producto</button>
            <a href="../../Otros/funcion_EliminarCompraEnBorrador.php" id="VaciarFormulario" class="BotonesLaterales" > <i class="fi-sr-broom"></i> Vaciar formulario</a>
            <button style="width: 210px;" form="FormularioNuevaOrden" name="borrador" value="borrador" class="BotonesLaterales" > <i class="fi-sr-folder-minus"></i> Guardar en borrador</button>
        </div>
        <div class="LetreroDeExistencia">
            <div class="ImagenesDelLetrero">
                <div class="PaLaImagen">
                    <img src="../../Imagenes/iconoDelMenu_Productos.png" alt="">
                </div>
                <div class="PalTexto">
                    <p>En alerta : <?php echo $DatosDelInventario['ProductosEnAlerta'];?></p>
                    <p>Agotados: <?php echo $DatosDelInventario['ProductosAgotados'];?></p>
                </div>
            </div>
            <?php
            if($DatosDelInventario['ProductosAgotados']>0 || $DatosDelInventario['ProductosEnAlerta']>0){
                echo '
                <div class="PalBoton">
                    <a href="?modo=1">Orden de compra recomendada</a>
                </div>
                ';
            }
            ?>
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="NuevaOrden.js"></script>
</body>
</html>