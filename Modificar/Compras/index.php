<?php
include('../../Otros/clases.php');

if(empty($_SESSION)){
    session_start();
}

if(!isset($_SESSION["nombreDeUsuario"])){
    header('Location: ../../login.php');
}else{
    $BaseDeDatos = new conexion();
    $Usuario = unserialize($_SESSION["UsuarioLogeado"]);
    $DatosDelUsuario = $Usuario->ObtenerDatos();
}


if(isset($_GET['id'])){
    $purchase = new purchase($_GET['id']);
}else{
    header('Location: ../../error.php');
}



if($_POST){
    try{
        $result = $purchase->updateData($_POST);
        print_r($result);
        header('Location: ../../Compras/Compra/?id='.$purchase->getId());
    }catch(Exception $error){
        $SWAlertMessage = $error->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $GLOBALS['nombreCorto'];?>: Modificar Compra</title>


    <link rel="stylesheet" href="../../Otros/colores.css?<?php echo rand();?>">
    <link rel="stylesheet" href="../../Otros/estilos_cabecera.css?<?php echo rand();?>">
    <link href="../../Iconos/css/uicons-solid-rounded.css" rel="stylesheet">
    <link href="../../Iconos/css/uicons-regular-rounded.css" rel="stylesheet">
    <link href="../../Imagenes/Logo.png" rel="shortcut icon" >
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<div href="#TopLane" id="TopLane">
        <div id="FiguraFondo">
            <img src="../../Imagenes/Logo.png" alt="">
        </div>
        <h1><?php echo $GLOBALS['nombreDelSoftware'];?></h1>
    </div>
    <div id="BarraSuperior">
        <div class="EspacioDelBotonPaVolverAlMenu">
            <input hidden type="checkbox" id="MostrarBotonOcultoPalMenu">    
            <a class="BotonOcultoPalMenu" href="../../index.php"> <i class="fi-sr-home"></i> <?php echo $GLOBALS['nombreDelSoftware'];?></a>
            <script src="../../Otros/EventosGlobales.js"></script>
        </div>
        <nav class="EspacioDeLosBotonesDelNav">
            <a href="../../Otros/funcion_CerrarSesion.php">Salir <i class="fi-sr-exit"></i></a>
            <a href="../../Ayuda">Ayuda  <i class="fi-rr-interrogation"></i></a>
            <a href="../../Perfil.php?pagina=1"><?php echo $DatosDelUsuario['nombres'].' ('.$DatosDelUsuario['nivelDeUsuario'].')';?> <i class="fi-sr-user"></i></a>
        </nav>
    </div>

    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Compras" class="Barra">
            <p>Compras</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Compras/Compra/?id=<?php echo $purchase->getId();?>" class="Barra">
            <p>#<?php echo $purchase->getId();?></p>
            <div class="Cuadrito" href="../"></div>
        </a>
        <a class="Barra">
            <p>Modificar</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>

    <modal id="ModalAgregarProducto">
        <div id="VentadaModalAgregarProducto" class="VentanaFlotante">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentanaProductos" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">AGREGAR PRODUCTO</b>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorProductos">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="BuscadorDeProductos" type="text" placeholder="Busca por ID, nombre o descripcion...">
                            <button type="button" id="BotonBuscarProductos"> <i class="fi-rr-search"></i> </button>
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
                                <div id="ListaDeProductosConsultados" class="EspacioDeRows" style="height: calc(80vh - 145px);">
                                    <div class="Flex-gap2 HoverVino TablaDeproductosVacia">
                                        <span>No hay productos para mostrar...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        <form method="post" id="FormularioNuevaOrden" class="ListaDeProductos" autocomplete="off">
            <input HIDDEN type="text" id="idEntity" value="<?php echo $purchase->getId()?>">
            <div class="cositaspalnombreguapo">
                <input name="name" placeholder="Escribe un nombre o descripción" id="InputDeNombre" type="text" value="<?php echo $purchase->getName();?>">
                <div id="DivDelPalitoDinamico" class="OtroNombre">
                    <div class="PalitoDelNombre"></div> <?php echo $purchase->getName();?> </div>
            </div>
            <div class="DetallesDeOrden">
                <b>Límite de tiempo : </b>
                <select class="asdasggadjk" id="SelectTiempoLimitado">
                    <option <?php echo (empty($purchase->getExpireDate())? 'selected':'');?> value="0">No</option>
                    <option <?php echo (empty($purchase->getExpireDate())? '':'selected');?> value="1">Si</option>
                </select>
            </div>
            <div class="DetallesDeOrden">
                <b>Tiempo de vigencia : </b>
                <xd class="FlexH-NoGap">
                    <input maxlength="3" value="" type="text" class="targeteable" id="CampoNumeroDeDias" onkeypress="return onlyNumber(this, event)" disabled="" style="opacity: 0.7;" placeholder="">
                    <label for="CampoNumeroDeDias" id="LabelDias" style="opacity: 0.7;">Días</label>
                    <input type="date" name="expireDate" id="CalendarioFlotante" value="<?php echo $purchase->getExpireDate();?>" disabled="">
                </xd>
            </div>
            
            <input HIDDEN name="products" type="text" id="listaDeProductos" value="<?php echo $purchase->getProductList();?>">
            <div class="TablaDeProductos">
                <span class="TituloDeTabla">Productos a comprar</span>
                <header>
                    <celda class="ColumnaImagen">Imagen</celda>
                    <celda class="ColumnaID">ID</celda>
                    <celda class="ColumnaProducto">Producto</celda>
                    <celda class="ColumnaCantidad">Cantidad</celda>
                </header>
                <div id="EspacioDeRowsDeLaTabla" class="RowsDeTabla"><row class="RowVacio"><span>No hay productos en esta orden de compra</span></row></div>
                <div class="BotonDinamicoAgregar">
                    <span class="fi-rr-plus-small" id="BotonDesplegable_AgregarProducto"> Agregar producto</span>
                </div>
            </div>
        </form>

        <span class="fi-rr-users TituloDeSection"> PROVEEDORES DISPONIBLES:</span>
        <section id="CajaDeProveedores" class="CajaDeProveedores">
            <div class="CardProveedor">
                <a href="../../Proveedores/Proveedor?rif=465476756" target="_blank" class="DivDeImgYNombreDeProveedor">
                    <img src="../../Imagenes/Proveedores/ImagenPredefinida_Proveedores.png" alt="">
                    <div class="RifYNombre">
                    <span class="NombreProveedor">FGDHJGKHG</span>
                        <span class="RifProveedor">V-465476756</span>
                    </div>
                </a>
                <span class="TituloProductosDeProveedor"> <i class="fi-sr-package"></i> PRODUCTOS: </span>
                <div class="ContenedorDelFlexDeProveedores">
        
                    <div class="FlexDeProductosProveidos mostly-customized-scrollbar">
                    
                    <div class="CardProductoProveido">
                        <celda class="CeldaImagenPP">
                            <img src="../../Imagenes/Productos/1707451304_Screenshot_4.png" alt="">
                        </celda>
                        <celda class="CeldaNombrePP">Plastic PBC 4.5</celda>
                    </div>
                    
                    </div>
                </div>
            </div>
        </section>
        <div class="coolFinalButtons">
            <a href="../../Compras/Compra/?id=<?php echo $purchase->getId();?>" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </article>



    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Compras.png">
                <b>Compras</b>
            </div>
            <button id="showAddProductModalButton" style="width: 210px"> <i class="fi-sr-apps-add"></i> Agregar producto</button>
            <a href="../../Compras/Compra/?id=<?php echo $purchase->getId();?>" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>



    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
</body>