<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();
$Tiempo = new AsistenteDeTiempo();

if(isset($_GET['id'])){
    if(is_numeric($_GET['id'])){
        $ConsultaDeCompra = $BaseDeDatos->consultar("SELECT * FROM `ordenesdecompra` WHERE (`id` = ".$_GET['id']." AND (`idEstado` != 65 OR `idEstado` != 66))");
        if(empty($ConsultaDeCompra)){
            header('Location: ../../Error.php?desc=13');
        }else{
            $Obj_Compra = new compra($ConsultaDeCompra[0][0]);
            $DatosRecibidos = $Obj_Compra->ObtenerDatos();

            
            if(empty($DatosRecibidos['fechaExpiracion'])){
                $DatosRecibidos['fechaExpiracion'] = '<i>Esta cotización no tiene fecha de expiración.</i>';
            }else{
                $ArrayRevertido = array_reverse(explode('/', $DatosRecibidos['fechaExpiracion']));
                $DatosRecibidos['fechaExpiracion'] = implode('-', $ArrayRevertido);
                $DatosRecibidos['fechaExpiracion'] = 'Hasta el '.$Tiempo->ConvertirFormato($DatosRecibidos['fechaExpiracion'], 'BaseDeDatos', 'Usuario');
            }

            if(empty($DatosRecibidos['fechaCreacion'])){
                $DatosRecibidos['fechaCreacion'] = '<i>Desconocido.</i>:';
            }else{
                $ArrayRevertido = array_reverse(explode('/', $DatosRecibidos['fechaCreacion']));
                $DatosRecibidos['fechaCreacion'] = implode('-', $ArrayRevertido);
                $DatosRecibidos['fechaCreacion'] = 'Creado el '.$Tiempo->ConvertirFormato($DatosRecibidos['fechaCreacion'], 'BaseDeDatos', 'Usuario');
            }
            

            $DatosAMostrar = array(
                'nombre' => $DatosRecibidos['nombre'],
                'idEstado' => $DatosRecibidos['idEstado'],
                'nroProductos' => $DatosRecibidos['nroDeProductos'],
                'vigencia' => $DatosRecibidos['fechaExpiracion'],
                'productosDeOrden' => $DatosRecibidos['productosEnFormato'],
                'fechaCreacion' => $DatosRecibidos['fechaCreacion'],
                'estado' => $DatosRecibidos['estado'],
                'idEstado' => $DatosRecibidos['idEstado']
            );
        }
    }else{
        header('Location: ../../Error.php?desc=11');
    }
}else{
    header('Location: ../../Error.php?desc=2');
}


?>

<!DOCTYPE html>
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: <?php echo $DatosAMostrar['nombre'];?></title>
    <link rel="stylesheet" href="estilos_Compra.css">
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
            <p><?php echo "#".$DatosRecibidos['id'];?></p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <modal id="ModalGenerarPDF">
        <div class="Ventanita">
            <span class="TituloDelModal">GENERAR REPORTE PDF</span>
            <button class="BotonCerrar" id="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
            <p><span class="numerillo">1</span> Indica la moneda:</p>
            <div class="OtroDivPorqueSoyIdiota">
                <div class="CajaDeOpciones">
                    <a id="BotonMonedaInternacional" class="Opcion Pulsable">
                        <span class="TituloDeMoneda">Moneda internacional</span>
                        <img src="../../Imagenes/Dolar.png" alt="">
                    </a>

                    <div class="VolverASeleccion">
                        <input hidden="" id="InputMostrarSeleccionDeTasa" type="checkbox" name="">
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
                                    <input id="InputTasa" onkeypress="return priceDollarFilter(this, event)" onpaste="return false" maxlength="9" type="text">
                                    <div>Bs</div>
                                </div>
                            </div>
                            <button id="BotonGenerarReporteConTasaEspecifica" class="BotonDeAqui">Generar reporte</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <modal id="Modal_Rechazar">
        <div id="VentanitaRechazar" class="VentanaDeRechazar ModalArriba">
            <span class="TituloDeVentanaModal">RECHAZAR COMPRA</span>
            <div class="ContenidoDeVentanitaModal">
                <img src="../../Imagenes/Sistema/Cleo5.png" alt="">
                <p>¿Estas seguro de que deseas indicar esta compra como rechazada? Esta acción es permanente y por lo tanto, no se puede deshacer.</p>
                <a href="../../Otros/funcion_RechazarCompra.php?id=<?php echo $DatosRecibidos['id'];?>">Rechazar</a>
                <span id="Span_VolverDeRechazar">Volver</span>
            </div>
        </div>
    </modal>
    <article>
        
        <b class="TituloNombreDeLaCot"><?php echo $DatosAMostrar['nombre'];?></b>
        <p class="RespuestaDelCliente">Estado de la compra: <span class="PuntoDeEstado PE<?php echo $DatosAMostrar['idEstado'];?>"><?php echo $DatosAMostrar['estado'];?></span></p>
        <div class="DatosDeCompra">
            <div class="DivisorDeDatos">
                <b>Nro de productos</b>
                <b>Vigencia</b>
                <b>Creación</b>
                <b>ID</b>
            </div>
            <div class="DivisorDeDatos">
                <b>:</b>
                <b>:</b>
                <b>:</b>
                <b>:</b>
            </div>
            <div class="DivisorDeDatos">
                <span><?php echo $DatosAMostrar['nroProductos'];?></span>
                <span><?php echo $DatosAMostrar['vigencia'];?></span>
                <span><?php echo $DatosAMostrar['fechaCreacion'];?></span>
                <span id="idEntity"><?php echo $_GET['id'];?></span>
            </div>
        </div>
        <div class="TablaDeProductos">
            <span class="TituloDeTabla">Lista de productos</span>
            <header>
                <celda class="ColumnaImagen">Imagen</celda>
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaProducto">Producto</celda>
                <celda class="ColumnaCantidad">Cantidad</celda>
            </header>
            <div id="EspacioDeRowsDeLaTabla" class="RowsDeTabla">
                <?php
                if(empty($DatosAMostrar['productosDeOrden'])){
                    echo '
                    <row>
                    Esta compra no tiene productos.
                    </row>
                    ';
                }else{
                    foreach(explode('¿', $DatosAMostrar['productosDeOrden']) as $ProXCant){
                        $pedazos = explode('x', $ProXCant);
    
                        $Obj_Producto  = new producto($pedazos[0]);
                        $DatosDelProducto = $Obj_Producto->ObtenerDatos();
    
                        echo '
                        <row>
                            <celda class="ColumnaImagen"><img src="../../Imagenes/Productos/'.((empty($DatosDelProducto['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProducto['ULRImagen']).'"></celda>
                            <celda class="ColumnaID">'.$DatosDelProducto['id'].'</celda>
                            <celda class="ColumnaProducto">'.$DatosDelProducto['nombre'].'</celda>
                            <celda title="x '.$pedazos[1].' '.$DatosDelProducto['nombreUM'].'" class="ColumnaCantidad">x '.$pedazos[1].'</celda>
                        </row>
                        ';
                    }
                }
                ?>
            </div>
        </div>
        <?php
        if($DatosAMostrar['idEstado'] != 61){
            //Compra en espera, asi que muestro los proveedores disponibles
            $RellenoPorProducto = "";

            foreach(explode('¿', $DatosAMostrar['productosDeOrden']) as $ProXCant){
                $ped = explode('x', $ProXCant);
                $RellenoPorProducto = $RellenoPorProducto.((empty($RellenoPorProducto))?'':' OR ')."`idProducto` = ".$ped[0];
            }

            $SQLPreparado = "SELECT DISTINCT `idProveedor` FROM `productosdeproveedor` 
            INNER JOIN `proveedores` ON `productosdeproveedor`.`idProveedor` = `proveedores`.`rif` 
            WHERE (`proveedores`.`idEstado` = 7 AND ($RellenoPorProducto))";

            $ConsultaDeProveedoresDisponibles = $BaseDeDatos->consultar($SQLPreparado);

            echo '
            <span id="TopeDeMovimientos" class="fi-rr-users TituloDeSectionDelArticle"> PROVEEDORES DISPONIBLES:</span>
            <section id="CajaDeProveedores" class="CajaDeProveedores">
            ';

            if(empty($ConsultaDeProveedoresDisponibles)){
                echo '
                <div class="ProveedoresVacios">
                    No hay proveedores disponibles
                </div>
                ';
            }else{
                $Array_SoloProductos = array();
                
                foreach(explode('¿', $DatosAMostrar['productosDeOrden']) as $ProXCant){
                    $pedazos = explode('x', $ProXCant);
                    $Array_SoloProductos [] = $pedazos[0];
                }

                foreach($ConsultaDeProveedoresDisponibles as $RowConProveedor){
                    $Obj_Proveedor = new proveedor($RowConProveedor[0]);
                    $DatosDelProveedor = $Obj_Proveedor->ObtenerDatos();
    
                    $Array_ProductosListadosProveeidos = array_intersect($Array_SoloProductos, explode('¿', $DatosDelProveedor['ProductosSeleccionados']));
    
                    $HTMLDeLista = "";
                    foreach($Array_ProductosListadosProveeidos as $ProductoPaPonerEnListaDeProveedor){
                        $Obj_ProductoProveeido = new producto($ProductoPaPonerEnListaDeProveedor);
                        $DatosDelProveeido = $Obj_ProductoProveeido->ObtenerDatos();
    
                        $HTMLDeLista = $HTMLDeLista.'
                        <div class="CardProductoProveido">
                            <celda class="CeldaImagenPP">
                                <img src="../../Imagenes/Productos/'.((empty($DatosDelProveeido['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProveeido['ULRImagen']).'" alt="">
                            </celda>
                            <celda class="CeldaNombrePP">'.$DatosDelProveeido['nombre'].'</celda>
                        </div>
                        ';
                    }
    
                    echo '
                    <div class="CardProveedor">
                        <a href="../../Proveedores/Proveedor?rif='.$DatosDelProveedor['rif'].'" target="_blank" class="DivDeImgYNombreDeProveedor">
                            <img src="../../Imagenes/Proveedores/'.((empty($DatosDelProveedor['ULRImagen']))?'ImagenPredefinida_Proveedores.png':$DatosDelProveedor['ULRImagen']).'" alt="">
                            <div class="RifYNombre">
                            <span class="NombreProveedor">'.$DatosDelProveedor['nombre'].'</span>
                                <span class="RifProveedor">'.$DatosDelProveedor['tipoDeDocumento'].'-'.$DatosDelProveedor['rif'].'</span>
                            </div>
                        </a>
                        <span class="TituloProductosDeProveedor"> <i class="fi-sr-package"></i> PRODUCTOS: </span>
                        <div class="ContenedorDelFlexDeProveedores">
                            <div class="FlexDeProductosProveidos mostly-customized-scrollbar">
                                '.$HTMLDeLista.'
                            </div>
                        </div>
                </div>
                    ';
                }
            }
            echo '
            </section>
            ';

            if($DatosAMostrar['idEstado'] == 63){
                echo '
                <section class="SectionDeBotonesDeConfirmacion">
                    <button class="Agrupacion_BotonMostrarModalRechazar"> <i class="fi-rr-cross-small"></i> Rechazar</button>
                    <a href="../RegistrarCompra?CompraImportada='.$DatosRecibidos['id'].'"> <i class="fi-rr-check"></i> Confirmar compra</a>
                </section>
                ';
            }

        }else{
            //Compra confirmada, muestro el almacenaje
            echo '
            <span id="TopeDeMovimientos" class="fi-rr-home TituloDeSectionDelArticle"> ALMACENAJE DE PRODUCTOS:</span>
            ';

            

            $Iventario = new inventario();
            $ConsultaDeAjuste = $BaseDeDatos->consultar("SELECT * FROM `ajustedeinventario` WHERE `id` = ".$DatosRecibidos['idAjusteDeEntradaEnInventario']);
            $ConsultaDeAjuste = $ConsultaDeAjuste[0];

            $ConsultaDeAlmacenesUtilizados = $BaseDeDatos->consultar("SELECT DISTINCT `idAlmacenModificado`, `almacenes`.`nombre`, `almacenes`.`direccion` 
            FROM `detallesdeajuste` INNER JOIN `almacenes` ON `detallesdeajuste`.`idAlmacenModificado` = `almacenes`.`id`  
            WHERE `idListaDeAjuste` = ".$DatosRecibidos['idAjusteDeEntradaEnInventario']);

            foreach($ConsultaDeAlmacenesUtilizados as $AlmacenUtilizado){
                $ConsultaDeProductosAAlmacenSeleccionado = $BaseDeDatos->consultar("SELECT * FROM `detallesdeajuste` WHERE (`idAlmacenModificado` = ".$AlmacenUtilizado['idAlmacenModificado']." AND `idListaDeAjuste` = ".$DatosRecibidos['idAjusteDeEntradaEnInventario'].")");

                $ListaDeProductos = "";

                foreach($ConsultaDeProductosAAlmacenSeleccionado as $ProductoDeAlmacenSeleccionado){
                    $Producto = new producto($ProductoDeAlmacenSeleccionado['idProducto']);
                    $DatosDelProducto = $Producto->ObtenerDatos();

                    $ListaDeProductos = $ListaDeProductos.'
                        <row>
                            <celda class="Cel_Imagen">
                                <img src="../../Imagenes/Productos/'.((empty($DatosDelProducto['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProducto['ULRImagen']).'" alt="">
                            </celda>
                            <celda class="Cel_ID">'.$ProductoDeAlmacenSeleccionado['idProducto'].'</celda>
                            <celda class="Cel_Producto">'.$DatosDelProducto['nombre'].'</celda>
                            <celda class="Cel_Cantidad" title="'.$ProductoDeAlmacenSeleccionado['cantidad'].' '.$DatosDelProducto['nombreUM'].' fueron almacenadas en este almacén.">'.$ProductoDeAlmacenSeleccionado['cantidad'].$DatosDelProducto['simboloUM'].'</celda>
                        </row>
                    ';
                }

                echo '
                <div class="CartaDeAlmacenaje">
                    <img src="../../Imagenes/iconoDelMenu_Almacenes.png" class="ImagenDeAlmacen" alt="">
                        <a href="../../Almacenes/Almacen?id='.$AlmacenUtilizado['idAlmacenModificado'].'" class="IDDeAlmacen">Ver almacén</a>
                    </img>
                    <div class="CajaDeCosasDeAlmacen">
                        <span class="TituloDelAlmacen">'.$AlmacenUtilizado['nombre'].'</span>
                        <div class="SubtituloDelAlmacen">'.$AlmacenUtilizado['direccion'].'</div>
                        <header class="HeaderTablaDeAlmacenaje">
                            <celda class="Cel_Imagen">Imagen</celda>
                            <celda class="Cel_ID">ID</celda>
                            <celda class="Cel_Producto">Producto</celda>
                            <celda class="Cel_Cantidad">Cantidad</celda>
                        </header>
                        <div class="CuerpoDeTabla">
                            '.$ListaDeProductos.'
                        </div>
                    </div>
                </div>
                ';
            }
        }
        ?>
        
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Compras.png" alt="">
                <b>Compras</b>
            </div>
            <?php
            if($DatosAMostrar['idEstado'] == 63){
                echo '
                <a href="../RegistrarCompra?CompraImportada='.$DatosRecibidos['id'].'"> <i class="fi-rr-shopping-cart-check"></i> Confirmar compra</a>
                <button class="Agrupacion_BotonMostrarModalRechazar" style="width: 210px;" href="x"> <i class="fi-rr-cross-circle"></i> Rechazar compra</button>
                <a id="BotonEditar" href="../../Modificar/Compras/?id='.$_GET['id'].'"> <i class="fi-rr-pencil"></i> Modificar compra</a>
                <a id="BotonEliminar"> <i class="fi-rr-trash"></i> Eliminar compra</a>
                ';
            }
            ?>
            <a id="BotonAbrirMenuDeCrearReporte" > <i class="fi-rr-print"></i> Generar reporte PDF</a>
        </div>
    </aside>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="Compra.js"></script>
</body>
</html>