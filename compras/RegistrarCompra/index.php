<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();
$Tiempo = new AsistenteDeTiempo();

if(empty($BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE `idEstado` = 51"))){
    header('Location: ../../Error.php?desc=12');
}



$ID_AlmacenPredeterminado = 0;
$ConsultaDeAlmacenPredeterminado = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE `predeterminado` = true");
if(!empty($ConsultaDeAlmacenPredeterminado)){
    $ID_AlmacenPredeterminado = $ConsultaDeAlmacenPredeterminado[0][0];
    $AlmacenPredeterminado = new almacen($ID_AlmacenPredeterminado);
    $DatosSegunObjetoAlmPre = $AlmacenPredeterminado->ObtenerDatos();
}

if($_POST){
    $CompraFantasma = new compra(0);

    

    try{
        $ID_Compra = $CompraFantasma->ConfirmarCompra($_POST);
    }catch(Exception $Error){
        $Problemas = $Error->getMessage();
    }


    if(empty($Problemas)){
        header('Location: ../Compra?alert=Confirmada&id='.$ID_Compra);
    }

    //print_r($ID_Compra);
}

if(isset($_GET['CompraImportada'])){
    if(is_numeric($_GET['CompraImportada'])){
        $ConsultaDeCompraImportada = $BaseDeDatos->consultar("SELECT * FROM `ordenesdecompra` WHERE (`id` = ".$_GET['CompraImportada']." AND `idEstado` = 63)");
        if(empty($ConsultaDeCompraImportada) && !$_POST){
            header('Location: ../../Error.php?desc=13');
            //echo "SELECT * FROM `ordenesdecompra` WHERE (`id` = ".$_GET['CompraImportada']." AND `idEstado` = 63)";
        }else{

        }
    }else{
        header('Location: ../../Error.php?desc=11');
    }
}

$DatosAMostrar = array(
    'CompraImportada' => (($_POST)?$_POST['CompraImportada']:((isset($_GET['CompraImportada']))?$_GET['CompraImportada']:'')),
    'ProductosListados' => '',
    'EstiloModalDeErrores' => ((empty($Problemas))?'':'display: flex;')
);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="estilos_RegistrarCompra.css">
    <title><?php echo $GLOBALS['nombreCorto'];?>: Registrar compra</title>
    <?php include('../../Otros/cabecera_N3.php');?>
    <nav class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../../">
            <p>Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="../">
            <p>Compras</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Registrar</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <modal style="<?php echo $DatosAMostrar['EstiloModalDeErrores'];?>" id="Modal_VentanaDeErrores">
        <div id="VentadaModal_VentanaDeErrores" class="VentanaDeErrores">
            <img class="TextoCentro" src="../../Imagenes/TrianguloDeAdvertencia.png" alt="">
            <b class="TextoCentro">No se puede guardar</b>
            <p>Se han encontrado errores que impiden continuar con el guardado, por favor rectifique.</p>
            <b class="TextoIzquierda">Errores:</b>
            <div class="TextoCentro CajaDeErrores">
                <?php
                if(!empty($Problemas)){
                    print_r($Problemas);
                }
                ?>
            </div>
        </div>
    </modal>
    <modal id="Modal_AlmacenarProducto">
        <div id="VentadaModal_AlmacenarProducto" class="VentanaFlotante">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_AlmacenarProducto" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">ALMACENAR PRODUCTO COMPRADO</b>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorDeAlmacenes">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="DescripcionBuscadorDeAlmacenes" type="text" placeholder="Buscar almacén por ID o descripcion...">
                            <button type="button" id="BotonBuscarAlmacenes"> <i class="fi-rr-search"></i> </button>
                        </div>
                        <div class="TablaDeProdcutoS">
                            <div class="ColumnasDeTabla">
                                <celda class="ColumnaID">ID</celda>
                                <celda class="ColumnaDescripcion">Almacén</celda>
                                <celda class="ColumnaCantidad">Existencia</celda>
                            </div>
                            <div class="ContenedorDelFlexDeRows">
                                <div id="ListaDeAlmacenesConsultados" class="EspacioDeRows">
                                    <div class="did_loading">
                                        <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                                        Cargando
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input HIDDEN id="InputProductoAlmacenar" type="text">
                    <input HIDDEN id="InputAlmacenSeleccionado" type="text">
                    <div class="EspacioDePrevisualizacionDeAlmacen" id="PrevisualizacionDeAlmacen">
                        <!--Aqui actua Modal_AlmacenarProducto.js-->
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <modal id="Modal_ImportarCompra">
        <div id="VentadaModal_ImportarCompra" class="VentanaFlotante OcultarModal">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_ImportarCompra" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">IMPORTAR ORDEN DE COMPRA</b>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorDeCompras">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="InputBuscadorDeCompras" type="text" placeholder="Busca por ID o descripcion...">
                            <button type="button" id="BotonBuscarCompras"> <i class="fi-rr-search"></i> </button>
                        </div>
                        <div class="TablaDeProdcutos">
                            <div class="ColumnasDeTabla">
                                <celda class="ColumnaID">ID</celda>
                                <celda class="ColumnaDescripcion">Descripción</celda>
                                <celda class="ColumnaCantidad">Productos</celda>
                            </div>
                            <div class="ContenedorDelFlexDeRows">
                                <div id="ListaDeComprasConsultadas" class="EspacioDeRows">
                                    <div class="did_loading">
                                        <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                                        Cargando
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input HIDDEN id="InputCompraAPrevisualizar" type="text">
                    <div class="EspacioDePrevisualizacionDeCompra" id="PrevisualizacionDeCompra">
                        <!--Aqui actua Modal_ImportarCompra.js-->
                        <div class="CompraNoSeleccionada">
                            <img src="../../Imagenes/Sistema/ImagenPredefinida_Compras.png" alt="">
                            <span>Seleccione una orden de compra</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <modal id="Modal_ElegirAlmPred">
        <div id="VentadaModal_ElegirPredeterminado" class="VentanaFlotante" style="min-width: 650px; width: 60%;">
            <div class="ContenidoDeVentana">
                <b class="TituloDeModal">ALMACÉN PREDETERMINADO</b>
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_ElegirPredeterminado" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <div class="ContenidoDeModalDiferente">
                    <?php
                    if(isset($DatosSegunObjetoAlmPre)){
                        echo '
                        <div class="PseudoTarjetaDeAlmPre">
                            <img src="../../Imagenes/iconoDelMenu_Almacenes.png" alt="">
                            <div class="NombrePaEsteDiv">
                                <a target="_blank" href="../../Almacenes/Almacen?id='.$DatosSegunObjetoAlmPre['id'].'" class="VerMasDeAlmPre">Ver más...</a>
                                <span class="TituloAlmPre">'.$DatosSegunObjetoAlmPre['nombre'].'</span>
                                <div class="Datillos">
                                    <div class="Orden">
                                        <span>Dirección</span>
                                        <span>Nro de productos</span>
                                        <span>Fecha de creación</span>
                                    </div>
                                    <div class="Orden">
                                        <span>:</span>
                                        <span>:</span>
                                        <span>:</span>
                                    </div>
                                    <div class="Orden">
                                        <span>'.$DatosSegunObjetoAlmPre['direccion'].'</span>
                                        <span>'.$DatosSegunObjetoAlmPre['nroDeProductos'].'</span>
                                        <span>'.$Tiempo->ConvertirFormato($DatosSegunObjetoAlmPre['fCreacion'], 'BaseDeDatosConTiempo', 'UsuarioConTiempo').'</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="TituloInvAlmPre fi-sr-package"> INVENTARIO ACTUAL DEL ALMACÉN:</span>
                        <div class="CajaDeInventarioDeAlmPre">
                            <div class="MarcoDeTablaAlmPre">
                                <div class="CabeceraDeLaTabla Rositapls">
                                    <celda class="ColumnaImagenPred">Imagen</celda>
                                    <celda class="ColumnaIDPred">ID</celda>
                                    <celda class="ColumnaNombrePred">Producto</celda>
                                    <celda class="ColumnaExistenciaPred">Existencia</celda>
                                </div>
                                <div class="EspacioDeProductosAlmPre">
                                    <div class="FlexDeProductosEnAlmPre">';
                                    if(empty($DatosSegunObjetoAlmPre['ProductosDelAlmacen'])){
                                        echo '
                                        <row class="RowVacio_AlmPre">
                                        No hay productos en el inventario de este almacén
                                        </row>
                                        ';
                                    }else{
                                        foreach(explode('¿', $DatosSegunObjetoAlmPre['ProductosDelAlmacen']) as $ProductoEnAlmPre){
                                            $pedazos = explode('x', $ProductoEnAlmPre);
                                            $ProductoOBJ = new producto($pedazos[0]);
                                            $DatosDelProducto = $ProductoOBJ->ObtenerDatos();
                                            echo '
                                        <row>
                                            <celda class="ColumnaImagenPred">
                                                <img src="../../Imagenes/Productos/'.((empty($DatosDelProducto['ULRImagen']))?'ImagenPredefinida_Productos.png':$DatosDelProducto['ULRImagen']).'" alt="">
                                            </celda>
                                            <celda class="ColumnaIDPred">
                                                '.$DatosDelProducto['id'].'
                                            </celda>
                                            <celda class="ColumnaNombrePred">
                                                '.$DatosDelProducto['nombre'].'
                                            </celda>
                                            <celda title="'.$pedazos[1].' '.$DatosDelProducto['nombreUM'].'" class="ColumnaExistenciaPred">
                                                '.$pedazos[1].' '.$DatosDelProducto['simboloUM'].'
                                            </celda>
                                        </row>';    
                                        }
                                    }
                        
                        echo '
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="TextoParaIdiotas">
                            Puedes utilizar este almacén para almacenar todos los productos comprados de forma rápida. 
                            Elegir esta opción eliminará los otros productos que hayas agregado a la lista de almacenaje.
                        </div>
                        <div class="EspacioPalBotonDeEsto">
                            <button id="AlmacenPredeterminado='.$DatosSegunObjetoAlmPre['id'].'" class="BotonImportarOrden Boton_UsarAlmacenPredeterminado" style="width: 170px;">Utilizar este almacén</button>
                        </div>
                        ';
                    }else{
                        echo '
                        <div class="AlmPredNoEncontrado">
                            <img src="../../Imagenes/Sistema/Cleo5.png">
                            <span class="SpanProblema">Ups! parece que no cuentas con un almacén predeterminado</span>
                            <a href="x" class="PSolucion">¿Necesitas ayuda para resolver esto?</a>
                        </div>
                        ';
                    }

                    ?>
                    
                </div>        
            </div>
        </div>
    </modal>
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
                                <option value="1">Material</option>
                                <option value="2">Equipo</option>
                            </select>
                        </div>
                        <div class="TablaDeProdcutos">
                            <div class="ColumnasDeTabla">
                                <celda class="ColumnaImagen">Imagen</celda>
                                <celda class="ColumnaID">ID</celda>
                                <celda class="ColumnaNombre2">Nombre</celda>
                                <celda class="ColumnaCantidad">Cantidad</celda>
                            </div>
                            <div class="ContenedorDelFlexDeRows">
                                <div id="ListaDeProductosConsultados" class="EspacioDeRows">
                                    <div class="did_loading">
                                        <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                                        Cargando
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <input HIDDEN id="InputProductoAPrevisualizar" type="text">
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
        <div class="OcultadorDePaginas">
            <div id="ContenedorDePaginas" class="ContenedorDePaginas">
                <div class="Pagina">
                    <div class="LetreroPaso">
                        <b>P</b>
                        <b>A</b>
                        <b>S</b>
                        <b>O</b>
                        <b class="BVacio"></b>
                        <b>1</b>
                        <a class="BotonPaVolver" href="../"><i class="fi-sr-undo-alt"></i> Salir</a>
                    </div>
                    <section style="justify-content: space-between;">
                        <span class="TituloDeSection">Selecciona los productos</span>                        
                        <p>Para registrar una compra y asi hacer una entrada en el inventario primero indica los productos comprados con su respectiva cantidad. Tambien puedes indicar los produtos fácilmente al <span id="BotonTextoImportar" class="BotonTextoImportar">importar una orden de compra </span><span class="BotonTextoImportar fi-rr-comment-arrow-down"></span>.</p>
                        <br>
                        <input form="FormularioPalPost" name="NombreDeLaCompra" id="InputDeNombreDeCompra" autocomplete="off" class="InputTextConEstilo" type="text" placeholder="Descripción de la compra:">
                        <span class="PretituloDeTabla">
                            <i class="fi-sr-package"></i> PRODUCTOS COMPRADOS:
                            <input HIDDEN name="InputProductosListados" form="FormularioPalPost" id="InputProductosListados" type="text" placeholder="Productos en lista" autocomplete="off">
                            <input HIDDEN value="<?php echo $DatosAMostrar['CompraImportada'];?>" form="FormularioPalPost" name="CompraImportada" id="InputCompraImportada" type="text" placeholder="Compra importada" autocomplete="off">
                        </span>
                        <form method="post" id="FormularioPalPost">
                            
                        </form>
                        <div class="TablaDeResultados">
                            <div class="CabeceraDeLaTabla">
                                <celda class="ColumnaImagen">Imagen</celda>
                                <celda class="ColumnaID">ID</celda>
                                <celda class="ColumnaNombre">Nombre</celda>
                                <celda class="ColumnaCantidad">Cantidad</celda>
                            </div>
                            <div class="CuerpoDeLaTabla">
                                <div id="EspacioDeRowsDeLaTabla" class="ContenedorDeRows">
                                    <?php
                                        if(empty($DatosAMostrar['ProductosListados'])){
                                            echo '
                                                <row class="RowVacio">
                                                    <span>No hay productos en esta lista.</span>
                                                </row>
                                            ';
                                        }else{
                                            echo '
                                                <row>
                                                    <celda class="ColumnaImagen">
                                                        <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                                                    </celda>
                                                    <celda class="ColumnaID">00000008</celda>
                                                    <celda class="ColumnaNombre">alambrito duro</celda>
                                                    <celda class="ColumnaCantidad">x 8</celda>
                                                    <div class="CeldaOculta">
                                                        <i class="fi-rr-pencil"></i>
                                                        <i class="fi-rr-trash"></i>
                                                    </div>
                                                </row>
                                            ';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div id="Aviso_CompraImportada" class="Avisito">
                            <i class="fi-rr-info"> Productos importados de la orden de compra ID #000005.</i>
                        </div>
                        <div class="EspacioDeBotones">
                            <button id="ButtonIrAPaso2" class="BotonContinuarDisponible">Continuar <i class="fi-rr-arrow-small-right"></i></button>
                        </div>
                        <div class="BotonAbsoluto_AgregarProduto">
                            <div class="letrerito">Agregar producto</div>
                            <button id="BotonDelAsideAgregarProducto">
                                <img src="../../Imagenes/iconoDelMenu_Productos.png" alt="">
                            </button>
                        </div>
                        <div class="BotonAbsoluto_ImportarCompra">
                            <div class="letrerito">Importar compra</div>
                            <button id="BotonDelAside_ImportarCompra">
                                <img src="../../Imagenes/iconoDelMenu_Compras.png" alt="">
                            </button>
                        </div>
                    </section>
                </div>
                <div class="Pagina">
                    <div class="LetreroPaso">
                        <b>P</b>
                        <b>A</b>
                        <b>S</b>
                        <b>O</b>
                        <b class="BVacio"></b>
                        <b>2</b>
                        <a id="BotonVolverAPag1" class="BotonPaVolver"><i class="fi-sr-undo-alt"></i> Ir al paso 1</a>
                    </div>
                    <section style="justify-content: space-between;">
                        <span class="TituloDeSection">Almacena los productos</span>                        
                        <p>Utiliza la lista de productos comprados para indicar donde se alojará cada uno de los productos, con su respectiva 
                            cantidad. En caso de no indicar el almacenaje, puede seleccionar el 
                            <span id="BotonTexto_ElegirPredeterminado" class="BotonTextoImportar">Almacén predeterminado </span>
                            <span class="BotonTextoImportar fi-rr-garage"></span> para almacenar todos los productos en este de forma automática.
                            <input HIDDEN name="ListaDeAlmacenaje" form="FormularioPalPost" type="text" id="AlmacenajeEnFormato" style="width: 50%;">
                        </p>
                        <div class="EspacioDeListaYTabla">
                            <div class="EspacioDeProductosComprados">
                                <span class="fi-sr-ballot Titulillo"> Productos comprados</span>
                                <div class="TablaDeCambios2">
                                    <div id="DivListaDeProductosAAlmacenar" class="EspacioDeRowDeCambio mostly-customized-scrollbar">
                                        <!--
                                        
                                    --> 
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="EspacioDeTablaDeAlmacenaje">
                                <div class="CabeceraDeLaTabla">
                                    <celda class="ColumnaImagenAP">Imagen</celda>
                                    <celda class="ColumnaNombreAP">Producto</celda>
                                    <celda class="ColumnaNombreAP">Almacén</celda>
                                    <celda class="ColumnaCantidadAP">Cantidad</celda>
                                    <celda class="ColumnaResultadoAP">Resultado</celda>
                                </div>
                                <div class="CuerpoDeLaTabla2">
                                    <div class="ContenedorDeRows" id="ContenedorDeRowsDeProductosYaAlmacenados">
                                        <!--Aqui actua Modal_AlmacenarPRoducto.js-->
                                        <row class="RowVacioAlmacenaje">
                                            <span>No se ha indicado el almacenaje de ningún producto.</span>
                                        </row>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="EspacioDeBotones">
                            <button id="ButtonGuardar" class="BotonContinuarNoDisponible" title="Aùn hay productos comprados que no han sido almacenados.">Guardar compra <i class="fi-sr-bookmark"></i></button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </article>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="RegistrarCompra.js"></script>
    <script src="Modal_ImportarCompra.js"></script>
    <script src="Modal_AlmacenarProducto.js"></script>
    <script src="Modal_AlmacenPredeterminado.js"></script>
</body>
</html>