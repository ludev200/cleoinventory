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
    $budget = new budget($_GET['id']);
}else{
    header('Location: ../../error.php');
}


if($_POST){
    try{
        $result = $budget->updateData($_POST);
        print_r($result);
        header('Location: ../../Ventas/Venta/?id='.$budget->getId());
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
    <title><?php echo $GLOBALS['nombreCorto'];?>: Modificar Venta</title>


    <link rel="stylesheet" href="../../Otros/colores.css?<?php echo rand();?>">
    <link rel="stylesheet" href="../../Otros/estilos_cabecera.css?<?php echo rand();?>">
    <link href="../../Iconos/css/uicons-solid-rounded.css" rel="stylesheet">
    <link href="../../Iconos/css/uicons-regular-rounded.css" rel="stylesheet">
    <link href="../../Imagenes/Logo.png" rel="shortcut icon" >
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <modal id="ModalAgregarProducto">
        <div class="VentanaFlotanteProductos">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentanaProductos" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">AGREGAR PRODUCTOS</b>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorProductos">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="BuscadorDeProductos" type="text" placeholder="Busca por ID, nombre o descripcion...">
                            <button type="button" id="BotonBuscarProductos"> <i class="fi-rr-search"></i> </button>
                            <select id="SelectCategoriaDelProductoABuscar">
                                <option value="0">Todos</option>
                                <option value="1">Material</option><option value="2">Equipo</option><option value="3">Mano de obra</option><option value="4">Comida</option>                            </select>
                        </div>
                        <div class="TablaDeProdcutos">
                            <div class="ColumnasDeTabla Flex-gap2">
                                <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                                <span class="NombreDeColumna ColumnaID">ID</span>
                                <span class="NombreDeColumna ColumnaNombre4">Nombre</span>
                                <span class="NombreDeColumna ColumnaCantidad">Cotizado</span>
                            </div>
                            <div class="EspacioDeRows" id="EspacioDeProductosConsultados">
                                <div class="Flex-gap2 HoverVino TablaDeproductosVacia">
                                    <span>No hay productos para mostrar...</span>
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
    <modal class="ModalAgregarCliente" id="addClientModal">
        <div class="VentanaFlotante OcultarContenidoModal">
            <div class="ContenidoDeVentana" >
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentanaAgregarCliente" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">INDICAR CLIENTE</b>
                <div class="EspacioDelBuscador">
                    <input autocomplete="off" type="text" class="Buscador" placeholder="Filtrar por RIF o nombre..." id="InputDeBuscadorDeClientes">
                    <button type="button" class="BotonBuscar" id="BotonFiltrarCliente"> <i class="fi-rr-search"></i> </button>
                </div>

                <div class="EspacioDeTablaDeClientes">
                    <div class="ColumnasDeTabla Flex-gap2">
                        <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                        <span class="NombreDeColumna ColumnaRIF">RIF</span>
                        <span class="NombreDeColumna ColumnaNombre3">Nombre</span>
                        <div class="NombreDeColumna ColumnaSeleccionar">Seleccionar</div>
                    </div>
                    <div class="EspacioDeRows">
                        <div class="Flex-gap2 HoverVino">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Clientes/ImagenPredefinida_Clientes.png" alt="">
                            </span>
                            <span class="Celda ColumnaRIF">V - 234525</span>
                            <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">FD FSGSDFDSF </span>
                            <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                                <button title="Seleccionar cliente" class="BontonSeleccionar" rif="234525">
                                    <i class="fi-rr-user-add" rif="234525"></i>
                                </button>
                            </div>
                        </div>
                        <div class="Flex-gap2 HoverVino">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Clientes/ImagenPredefinida_Clientes.png" alt="">
                            </span>
                            <span class="Celda ColumnaRIF">V - 234525</span>
                            <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">FD FSGSDFDSF </span>
                            <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                                <button title="Seleccionar cliente" class="BontonSeleccionar" rif="234525">
                                    <i class="fi-rr-user-add" rif="234525"></i>
                                </button>
                            </div>
                        </div>
                        <div class="Flex-gap2 HoverVino">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Clientes/ImagenPredefinida_Clientes.png" alt="">
                            </span>
                            <span class="Celda ColumnaRIF">V - 234525</span>
                            <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">FD FSGSDFDSF </span>
                            <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                                <button title="Seleccionar cliente" class="BontonSeleccionar" rif="234525">
                                    <i class="fi-rr-user-add" rif="234525"></i>
                                </button>
                            </div>
                        </div>
                        <div class="Flex-gap2 HoverVino">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Clientes/ImagenPredefinida_Clientes.png" alt="">
                            </span>
                            <span class="Celda ColumnaRIF">V - 234525</span>
                            <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">FD FSGSDFDSF </span>
                            <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                                <button title="Seleccionar cliente" class="BontonSeleccionar" rif="234525">
                                    <i class="fi-rr-user-add" rif="234525"></i>
                                </button>
                            </div>
                        </div>
                        <div class="Flex-gap2 HoverVino">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Clientes/ImagenPredefinida_Clientes.png" alt="">
                            </span>
                            <span class="Celda ColumnaRIF">V - 234525</span>
                            <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">FD FSGSDFDSF </span>
                            <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                                <button title="Seleccionar cliente" class="BontonSeleccionar" rif="234525">
                                    <i class="fi-rr-user-add" rif="234525"></i>
                                </button>
                            </div>
                        </div>
                        <div class="Flex-gap2 HoverVino">
                            <span class="Celda ColumnaImagen">
                                <img src="../../Imagenes/Clientes/ImagenPredefinida_Clientes.png" alt="">
                            </span>
                            <span class="Celda ColumnaRIF">V - 234525</span>
                            <span class="Celda ColumnaNombre3" style="width: calc(100% - 280px);">FD FSGSDFDSF </span>
                            <div class="Celda ColumnaSeleccionar" style="width: 80px;">
                                <button title="Seleccionar cliente" class="BontonSeleccionar" rif="234525">
                                    <i class="fi-rr-user-add" rif="234525"></i>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </modal>
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
        <a href="../../Ventas" class="Barra">
            <p>Ventas</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Ventas/Venta/?id=<?php echo $budget->getId();?>" class="Barra">
            <p>#<?php echo $budget->getId();?></p>
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

    <form autocomplete="off" method="post">
        <input hidden type="text" id="idEntity" value="<?php echo $budget->getId();?>">
        <span id="ZonaDeCliente" class="SubtituloCentral"><i class="fi-sr-user Arreglito"></i> CLIENTE:</span>
        <div id="EspacioDeTarjetaDeCliente" style="height: 190px;">
            <input HIDDEN type="text" value="<?php echo $budget->getClientCedula();?>" id="idClientInput">
            <div class="NoCliente">
                <div class="BotonesParaSeleccionarCliente">
                    <button type="button" id="BotonBuscarCliente"> <i class="fi-rr-search-alt"></i> Buscar cliente</button>
                    <a target="_blank" href="../../Clientes/NuevoCliente"> <i class="fi-sr-user-add"></i> Crear nuevo </a>
                </div>
                <div class="CajaDeClienteVacio">
                    <p>No hay ningún cliente seleccionado.</p>
                </div>
            </div>
            <div class="SiCliente">
                <section class="SeccionDelCliente">
                    <img src="../../Imagenes/clientes/ImagenPredefinida_Clientes.png" alt="" id="ImagenDelCliente">
                    <div class="EspacioDeLetras">
                        <b>RIF   :</b>
                        <b>Nombre:</b>
                        <b>Teléfono:</b>
                        <b>Correo:</b>
                        <b>Dirección:</b>
                    </div>
                    <div class="CamposDelCliente">
                        <div class="FlexH">
                            <input type="text" id="TipoDeRifCliente" class="EspacioDeTipoDeID" maxlength="1">
                            <b class="GuionSeparador"> - </b>
                            <input type="text"  id="IdCliente" class="EspacioDeID" maxlength="9">
                            <input HIDDEN type="text" name="idClient" id="REAL_IdCliente">
                        </div>
                        <div class="FlexH">
                            <input type="text" id="NombreCliente" class="EspacioDeNombre">
                        </div>
                        <div class="FlexH">
                            <input type="text" id="TelefonoCliente" class="EspacioDeTelefono">
                        </div>
                        <div class="FlexH">
                            <input type="text" id="CorreoCliente" class="EspacioDeCorreo" placeholder="No especificado">
                        </div>
                        <div class="FlexH">
                            <input type="text" id="DireccionCliente" class="EspacioDeDireccion">
                        </div>
                    </div>
                </section>
                <div class="BotonesParaQuitarCliente">
                    <button type="button" id="BotonRemoverCliente"> <i class="fi-rr-remove-user"></i> Remover </button>
                </div>
            </div>
        </div>

        <br>
        <span class="SubtituloCentral"><i class="fi-sr-list Arreglito"></i> DETALLES:</span>
        <section class="CajaDeDetalles">
            <div class="expiracion">
                <div>
                    <b>Descripción del servicio</b>
                    <b>Límite de tiempo</b>
                    <b>Tiempo de vigencia</b>
                    <b>Fecha de vencimiento</b>
                    <b>Costo asociado al salario</b>
                    <b>Porcentaje de utilidades</b>
                    <b>Porcentaje de I.V.A.</b>
                </div>
                <div class="ColumnaDeDosPuntos">
                    <b>:</b>
                    <b>:</b>
                    <b>:</b>
                    <b>:</b>
                    <b>:</b>
                    <b>:</b>
                    <b>:</b>
                </div>
                <div class="CajaDeInputsDeDetalles">
                    <input name="name" maxlength="50" value="<?php echo $budget->getName();?>" type="text"  id="InputNombreDeLaCot" class="Targeteable RayitaVino">
                    <select id="SelectTiempoLimitado">
                        <option <?php echo (empty($budget->getExpireDate())? 'selected':'');?> value="0">No</option>
                        <option <?php echo (!empty($budget->getExpireDate())? 'selected':'');?> value="1">Si</option>
                    </select>
                    <xd class="FlexH-NoGap">
                        <input maxlength="3" value="" type="text" class="targeteable" id="CampoNumeroDeDias" onkeypress="return onlyNumber(this, event)" disabled="" style="opacity: 0.7;">
                        <label for="CampoNumeroDeDias" id="LabelDias" style="opacity: 0.7;">Días</label>
                        <input type="date" name="expireDate" id="CalendarioFlotante" value="<?php echo $budget->getExpireDate();?>">
                    </xd>
                    <p class="Targeteable" id="FechaVencimiento" style="opacity: 0.7;">Esta cotización no tiene una fecha de vencimiento.</p>
                    <xd class="FlexH-NoGap">
                        <input value="<?php echo $budget->getPCASalario();?>" type="text" name="percentCAS" class="targeteable TARight" id="InputCASalario" onkeypress="return onlyNumber(this, event)" onpaste="return false">
                        <label class="Porcentaje" id="" for="InputCASalario">%</label>
                    </xd>
                    <xd class="FlexH-NoGap">
                        <input value="<?php echo $budget->getPUtilidades();?>" type="text" name="percentUti" class="targeteable TARight" id="Utilidades" onkeypress="return onlyNumber(this, event)" onpaste="return false">
                        <label class="Porcentaje" id="" for="Utilidades">%</label>
                    </xd>
                    <xd class="FlexH-NoGap">
                        <input value="<?php echo $budget->getPIVA();?>" type="text" name="percentIVA" class="targeteable TARight" id="InputIVA" onkeypress="return onlyNumber(this, event)" onpaste="return false">
                        <label class="Porcentaje" id="" for="InputIVA">%</label>
                    </xd>
                </div>
            </div>
            
        </section>

        <br>
        <span class="SubtituloCentral"><i class="fi-sr-ballot Arreglito"></i> PRODUCTOS:</span>
        <section class="CuerpoDeLaCotizacion">
            <input HIDDEN value="<?php echo implode('¿', $budget->getProductIDsOnCategory(array(1)));?>" name="materialProducts" type="text" id="ProductosCotizados" style="width: 100%;">
            <span class="TituloDeCuerpo">Materiales</span>
            <div class="Flex-gap2 ColumnasDeTabla">
                <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                <span class="NombreDeColumna ColumnaID">ID</span>
                <span class="NombreDeColumna ColumnaNombre">Nombre</span>
                <span class="NombreDeColumna ColumnaCantidad">Cantidad</span>
                <span class="NombreDeColumna ColumnaUnidad">Unidad</span>
                <span class="NombreDeColumna ColumnaPrecio">Precio</span>
                <span class="NombreDeColumna ColumnaTotal">Total</span>
            </div>
            <div class="EspacioDeRows" id="MaterialesAgregados"><?php
                if(empty($budget->getProductIDsOnCategory(array(1)))){
                    echo '<row>
                        <span class="TablaVacia">Esta cotización no tiene materiales</span>
                    </row>';
                }else{
                    foreach($budget->getProductsOnCategory(array(1)) as $productOnList){
                        echo '<row id="MaterialEnLista-'.$productOnList['id'].'" class="Flex-gap2">
                            <div class="test9">
                                <i idProduct="'.$productOnList['id'].'" title="Modificar este producto." class="fi-rr-pencil BotonModificarProductoEspecifico"></i>
                                <i idProduct="'.$productOnList['id'].'" idCategory="1" title="Eliminar este producto." class="fi-rr-trash BotonEliminarProductoEspecifico"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/'.$productOnList['img'].'" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">'.$productOnList['id'].'</span>
                            <span class="ColumnaNombre CeldaSinH">'.$productOnList['name'].'</span>
                            <span class="ColumnaCantidad CeldaSinH">'.$productOnList['quantity'].'</span>
                            <span title="'.$productOnList['unitName'].'" class="ColumnaUnidad CeldaSinH">'.$productOnList['unitSymbol'].'</span>
                            <span class="ColumnaPrecio CeldaSinH">'.$productOnList['price'].'$</span>
                            <span class="ColumnaTotal CeldaSinH productOnBudgetTotalPrice" price="'.$productOnList['total'].'" idCategory="1">'.$productOnList['total'].'$</span>
                        </row>';
                    }
                }
                ?></div>
            <div class="BotonDinamicoAgregar">
                <span class="fi-rr-plus-small" id="BotonAgregarProductoMaterial"> Agregar producto</span>
           </div>
            <input HIDDEN value="<?php echo implode('¿', $budget->getProductIDsOnCategory(array(2)));?>" name="equipProducts" type="text" id="MaquinasCotizados" style="width: 100%;">
            <span class="TituloDeCuerpo">Equipamiento</span>
            <div class="Flex-gap2 ColumnasDeTabla">
                <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                <span class="NombreDeColumna ColumnaID">ID</span>
                <span class="NombreDeColumna ColumnaNombre">Nombre</span>
                <span class="NombreDeColumna ColumnaCantidad">Cantidad</span>
                <span class="NombreDeColumna ColumnaUnidad">Desgaste</span>
                <span class="NombreDeColumna ColumnaPrecio">Precio</span>
                <span class="NombreDeColumna ColumnaTotal">Total</span>
            </div>
            <div class="EspacioDeRows" id="HerramientasAgregados"><?php
            if(empty($budget->getProductIDsOnCategory(array(2)))){
                echo '<row>
                    <span class="TablaVacia">Esta cotización no tiene maquinaria ni herramientas</span>
                </row>';
            }else{
                foreach($budget->getProductsOnCategory(array(2)) as $productOnList){
                    echo '<row id="MaterialEnLista-'.$productOnList['id'].'" class="Flex-gap2">
                        <div class="test9">
                            <i idProduct="'.$productOnList['id'].'" title="Modificar este producto." class="fi-rr-pencil BotonModificarProductoEspecifico"></i>
                            <i idProduct="'.$productOnList['id'].'" idCategory="2" title="Eliminar este producto." class="fi-rr-trash BotonEliminarProductoEspecifico"></i>
                        </div>
                        <span class="ColumnaImagen CeldaSinH">
                            <img src="../../Imagenes/productos/'.$productOnList['img'].'" alt="">
                        </span>
                        <span class="ColumnaID CeldaSinH">'.$productOnList['id'].'</span>
                        <span class="ColumnaNombre CeldaSinH">'.$productOnList['name'].'</span>
                        <span class="ColumnaCantidad CeldaSinH">'.$productOnList['quantity'].'</span>
                        <span class="CeldaSinH ColumnaUnidad">'.$productOnList['defaultSpoilage'].'</span>
                        <span class="ColumnaPrecio CeldaSinH">'.$productOnList['price'].'$</span>
                        <span class="ColumnaTotal CeldaSinH productOnBudgetTotalPrice" price="'.$productOnList['total'].'" idCategory="2">'.$productOnList['total'].'$</span>
                    </row>';
                }
            }
            ?></div>
            <div class="BotonDinamicoAgregar">
                <span class="fi-rr-plus-small" id="BotonAgregarProductoHerramienta"> Agregar producto</span>
           </div>
            <input HIDDEN value="<?php echo implode('¿', $budget->getProductIDsOnCategory(array(3, 4)));?>" name="personalProducts" type="text" id="ManosCotizados" style="width: 100%;">
            <span class="TituloDeCuerpo">Mano de obra</span>
            <div class="Flex-gap2 ColumnasDeTabla">
                <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                <span class="NombreDeColumna ColumnaID">ID</span>
                <span class="NombreDeColumna ColumnaNombre">Nombre</span>
                <span class="NombreDeColumna ColumnaCantidad">Cantidad</span>
                <span class="NombreDeColumna ColumnaPrecio">Días</span>
                <span class="NombreDeColumna ColumnaPrecio">Precio</span>
                <span class="NombreDeColumna ColumnaTotal">Total</span>
            </div>
            <div class="EspacioDeRows" id="ManoDeObraAgregados"><?php
                if(empty($budget->getProductIDsOnCategory(array(3,4)))){
                    echo '<row>
                        <span class="TablaVacia">Esta cotización no tiene mano de obra</span>
                    </row>';
                }else{
                    foreach($budget->getProductsOnCategory(array(3,4)) as $productOnList){
                        echo '<row id="MaterialEnLista-'.$productOnList['id'].'" class="Flex-gap2">
                            <div class="test9">
                                <i idProduct="'.$productOnList['id'].'" title="Modificar este producto." class="fi-rr-pencil BotonModificarProductoEspecifico"></i>
                                <i idProduct="'.$productOnList['id'].'" idCategory="3" title="Eliminar este producto." class="fi-rr-trash BotonEliminarProductoEspecifico"></i>
                            </div>
                            <span class="ColumnaImagen CeldaSinH">
                                <img src="../../Imagenes/productos/'.$productOnList['img'].'" alt="">
                            </span>
                            <span class="ColumnaID CeldaSinH">'.$productOnList['id'].'</span>
                            <span class="ColumnaNombre CeldaSinH">'.$productOnList['name'].'</span>
                            <span class="ColumnaCantidad CeldaSinH" title="'.$productOnList['unitName'].'">'.$productOnList['quantityPerson'].' '.$productOnList['unitSymbol'].'</span>
                            <span style="align-items: center;" class="ColumnaPrecio CeldaSinH">'.$productOnList['quantityDays'].'</span>
                            <span class="ColumnaPrecio CeldaSinH">'.$productOnList['price'].'$</span>                            
                            <span class="ColumnaTotal CeldaSinH productOnBudgetTotalPrice" price="'.$productOnList['total'].'" idCategory="'.$productOnList['idCategory'].'">'.$productOnList['total'].'$</span>
                        </row>';
                    }
                }
                ?></div>
            <div class="BotonDinamicoAgregar">
                <span class="fi-rr-plus-small" id="BotonAgregarProductoMano"> Agregar producto</span>
           </div>
            <footer class="FinalDeCotizacion">
                <div class="SubTotal">
                    <div class="TitulosDelTotal ColumnaDelFinalDeCot">
                        <b>Costo en materiales</b>
                        <b>Costo en equipo</b>
                        <b>Costo en mano de obra</b>
                        <b id="TituloSalario">Asociado al salario (<?php echo $budget->getPCASalario();?>%)</b>
                        <div class="PalitoDeSuma"></div>
                        <b>Costo de productos</b>
                        <b id="TituloUtilidades">Utilidades (<?php echo $budget->getPUtilidades();?>%)</b>
                        <div class="PalitoDeSuma"></div>
                        <b>Sub Total</b>
                        <b id="TituloIVA">I.V.A. (<?php echo $budget->getPIVA();?>%)</b>
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
                    <div class="PreciosDelTotal ColumnaDelFinalDeCot">
                        <p id="PrecioSubTotalMaterial">0.00</p>
                        <p id="PrecioSubTotalMaquinaria">0.00</p>
                        <p id="PrecioSubTotalMano">0.00</p>
                        <p id="PrecioAsociadoAlSalario">0.00</p>
                        <div class="PalitoDeSuma"></div>
                        <p id="PrecioGeneral" class="NumeroConColor">0.00</p>
                        <p id="PrecioUtilidades">0.00</p>
                        <div class="PalitoDeSuma"></div>
                        <p id="PrecioSubTotal" class="NumeroConColor">0.00</p>
                        <p id="PrecioIVA">0.00</p>
                    </div>
                    <div class="ColumnaDelFinalDeCot">
                        <b>$</b>
                        <b>$</b>
                        <b>$</b>
                        <b>$</b>
                        <div class="PalitoDeSuma"></div>
                        <b class="NumeroConColor">$</b>
                        <b>$</b>
                        <div class="PalitoDeSuma"></div>
                        <b class="NumeroConColor">$</b>
                        <b>$</b>
                    </div>
                </div>
                <div class="Total">
                    <p>Total: </p>
                    <p id="PrecioTotal">0.00</p>
                    <p>$</p>
                </div>
            </footer>
        </section>

        <div class="coolFinalButtons">
            <a href="../../Ventas/Venta/?id=<?php echo $budget->getId();?>" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </form>

    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Ventas.png">
                <b>Ventas</b>
            </div>
            <button HIDDEN id="showAddClientModalButton" style="width: 210px"> <i class="fi-sr-user"></i> Agregar cliente</button>
            <button id="showAddProductModalButton" style="width: 210px"> <i class="fi-sr-apps-add"></i> Agregar producto</button>
            <a href="../../Ventas/Venta/?id=<?php echo $budget->getId();?>" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>



    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
</body>