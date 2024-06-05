<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();



$ID_AlmacenPredeterminado = 0;
$ConsultaDeAlmacenPredeterminado = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE `predeterminado` = true");
if(!empty($ConsultaDeAlmacenPredeterminado)){
    $ID_AlmacenPredeterminado = $ConsultaDeAlmacenPredeterminado[0][0];
    $AlmacenPredeterminado = new almacen($ID_AlmacenPredeterminado);
    $DatosSegunObjetoAlmPre = $AlmacenPredeterminado->ObtenerDatos();
}
/*
echo 'POST: ';
print_r($_POST);
*/
if($_POST){
    $VentaFantasma = new cotizacion(0);

    echo '<br><br>';
    try{
        $ID_Venta = $VentaFantasma->ConfirmarVenta($_POST);
        //echo 'ConfirmarVenta: ';
        //print_r($ID_Compra);
        header('Location: ../Venta?id='.$ID_Venta);
    }catch(Exception $Error){
        $Problemas = $Error->getMessage();
        echo 'Problemas: ';
        print_r($Problemas);
    }
}

$idCotiAConfirmar = ((empty($_GET['id']))? '':$_GET['id']);

?>



<!DOCTYPE html>
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Confirmar venta</title>
    <link rel="stylesheet" href="estilos_confirmar.css">
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
            <p>Confirmar</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <modal id="Modal_SeleccionarCliente">
        <div id="VentadaModal_SeleccionarCliente" class="VentanaFlotante OcultarModal">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_SeleccionarCliente" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">SELECCIÓN DE CLIENTE</b>
                <div class="EspacioDelBuscador">
                    <input autocomplete="off" type="text" class="Buscador" placeholder="Filtrar por RIF o nombre..." id="InputDeBuscadorDeClientes">
                    <button type="button" class="BotonBuscar" id="BotonFiltrarCliente"> <i class="fi-rr-search"></i> </button>
                </div>
                <div class="ColumnasTablaCliente">
                        <celda class="ColumnaImagen">Imagen</celda>
                        <celda class="ColumnaRIF">RIF</celda>
                        <celda class="ColumnaNombre3">Nombre</celda>
                        <celda class="ColumnaSeleccionar">Seleccionar</celda>
                    </div>
                <div class="TablaDeClientes">                    
                    <div class="EspacioRowClientes" id="AquiSePonenLosClientes">
                        <div class="did_loading">
                            <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                            Cargando
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <modal id="Modal_AgregarProducto">
        <div id="VentanaModal_AgregarProducto" class="VentanaFlotante OcultarModal">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_AgregarProducto" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">SELECCIÓN DE PRODUCTO</b>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorProductos">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="BuscadorDeProductos" type="text" placeholder="Busca por ID, nombre o descripcion...">
                            <button type="button" id="BotonBuscarProductos"> <i class="fi-rr-search"></i> </button>
                            <select id="SelectCategoriaABuscar">
                                <option value="0">Todos</option>
                                <option value="1">Material</option>
                                <option value="2">Equipo</option>
                                <option value="3">Mano de obra</option>
                                <option value="4">Comida</option>
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
                                <div id="ListaDeProductosConsultados" class="EspacioDeRows" style="">
                                    ...
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
    <modal id="Modal_ImportarCompra">
        <div id="VentadaModal_ImportarCompra" class="VentanaFlotante OcultarModal">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_ImportarCompra" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">SELECCIÓN DE COTIZACIÓN</b>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorDeCompras">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="Input_BuscadorDeCoti" type="text" placeholder="Busca por ID o descripcion...">
                            <button type="button" id="BotonBuscarCotis"> <i class="fi-rr-search"></i> </button>
                        </div>
                        <div class="TablaDeProdcutos">
                            <div class="ColumnasDeTabla">
                                <celda class="ColumnaID">ID</celda>
                                <celda class="ColumnaDescripcion">Descripción</celda>
                                <celda class="ColumnaCantidad">Cliente</celda>
                            </div>
                            <div class="ContenedorDelFlexDeRows">
                                <div id="ListaDeComprasConsultadas" class="EspacioDeRows" style="">
                                    <div class="did_loading">
                                        <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                                        Cargando
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input HIDDEN value="<?php echo $idCotiAConfirmar;?>" type="number" id="Input_CotiSeleccionada">
                    <div class="EspacioDePrevisualizacionDeCompra" id="PrevisualizacionDeCoti">
                        <div class="CompraNoSeleccionada">
                            <img src="../../Imagenes/Sistema/ImagenPredefinida_Ventas.png" alt="">
                            <span>Seleccione una cotización</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <modal id="Modal_AlmacenarProducto">
        <div id="VentadaModal_AlmacenarProducto" class="VentanaFlotante">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_AlmacenarProducto" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <b class="TituloDeModal">SELECCIONAR ALMACÉN A EXTRAER</b>
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
    <modal id="Modal_ElegirAlmPred">
        <div id="VentadaModal_ElegirPredeterminado" class="VentanaFlotante" style="min-width: 650px; width: 60%;">
            <div class="ContenidoDeVentana">
                <b class="TituloDeModal">ALMACÉN PREDETERMINADO</b>
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentana_ElegirPredeterminado" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <div class="ContenidoDeModalDiferente">
                <?php
                if(!isset($DatosSegunObjetoAlmPre)){
                    echo '
                    <div class="AlmPredNoEncontrado">
                            <img src="../../Imagenes/Sistema/Cleo5.png">
                            <span class="SpanProblema">Ups! parece que no cuentas con un almacén predeterminado</span>
                            <p style="color: gray;">Esto puede deberse a que aún no has registrado ningún almacén en el sistema.</p>
                            <a href="../../Ayuda/#25" class="PSolucion">¿Necesitas ayuda para resolver esto?</a>
                        </div>    
                    ';
                }else{
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
                }
                ?>                           
                </div>        
            </div>
        </div>
    </modal>
    <article>
        <div class="OcultadorDePaginas">
            <div class="ContenedorDePaginas" id="ContenedorDePaginas">
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
                    <section style="justify-content: space-between; overflow-y: hidden; overflow-x: clip;">
                        <span class="TituloDeSection">Selecciona la cotización</span>
                        <p>Para confirmar una venta y asi realizar una salida de productos del inventario primero <span id="BotonTextoImportar" class="BotonTextoImportar">importa una cotización </span><span class="BotonTextoImportar fi-rr-comment-arrow-down"></span> para indicar el cliente y los productos vendidos.</p>
                        <div class="TituloYCliente">
                            <form class="CampoDeInputs" id="FormularioPalPost" method="post">
                                <input id="InputTituloDeCoti" type="text" class="TituloDeCoti" placeholder="Descripción de la venta" name="tituloDeVenta">
                                <div class="datito">
                                    <span title="Costo asociado al salario">CAS:</span>
                                    <input id="Input_CAS" type="number" placeholder="0" name="CAS">
                                    <span class="porcento">%</span>
                                </div>
                                <div class="datito">
                                    <span>Utilidades:</span>
                                    <input id="Input_Utilidades" type="number" placeholder="0" name="Utilidades">
                                    <span class="porcento">%</span>
                                </div>
                                <div class="datito">
                                    <span>IVA:</span>
                                    <input id="Input_IVA" type="number" placeholder="0" name="IVA">
                                    <span class="porcento">%</span>
                                </div>
                                <input HIDDEN type="number" name="IDCoti" placeholder="ID coti" id="ID_CotiAConfirmar">
                                <input HIDDEN type="number" name="IDCliente" placeholder="ID cliente" id="ID_ClienteEnCoti">
                                <input HIDDEN type="text"  name="ListaProductosConPrecio" placeholder="Productos" id="ProductosAVender" style="grid-column: 1/4;">
                            </form>
                            <div class="CardCliente" id="CartaDeCliente">
                                <div class="ClienteVacio">
                                    <span>CLIENTE NO ESPECIFICADO</span>
                                </div>
                                <!--SeleccionarCliente.js-->
                            </div>
                        </div>
                        <div class="TablaDeResultados">
                            <div class="CabeceraDeLaTabla">
                                <celda class="ColumnaImagen">Imagen</celda>
                                <celda class="ColumnaID">ID</celda>
                                <celda class="ColumnaNombre">Nombre</celda>
                                <celda class="ColumnaCantidad">Cantidad</celda>
                                <celda class="ColumnaPrecio">Precio</celda>
                                <celda class="ColumnaTotal">Total</celda>
                            </div>
                            <div class="CuerpoDeLaTabla">
                                <div id="EspacioDeRowsDeLaTabla" class="ContenedorDeRows">
                                    <row class="RowVacio">
                                        <span>No hay productos en esta cotización.</span>
                                    </row>
                                    <!--
                                    ImportarCoti.js
                                    -->
                                </div>
                            </div>
                        </div>
                        <div class="CajaTotal">
                            <b>Total:</b>
                            <span id="Span_totaltotal">0.00</span>
                            <span>$</span>
                        </div>
                        <div class="EspacioDeBotones">
                            <button id="ButtonIrAPaso2" class="BotonContinuarNoDisponible">Continuar <i class="fi-rr-arrow-small-right"></i></button>
                        </div>
                        <div id="PanelBotonesAbsolutos" class="BtnAbsolutos soloCoti">
                            <div class="BotonAbsoluto_AgregarProduto">
                                <div class="letrerito">Agregar producto</div>
                                <button id="BotonDelAsideAgregarProducto">
                                    <img src="../../Imagenes/iconoDelMenu_Productos.png" alt="">
                                </button>
                            </div>
                            <div class="BotonAbsoluto_CambiarCliente">
                                <div class="letrerito">Seleccionar cliente</div>
                                <button id="BotonDelAsideSeleccionarCliente">
                                    <img src="../../Imagenes/iconoDelMenu_Clientes.png" alt="">
                                </button>
                            </div>
                            <div class="BotonAbsoluto_ImportarCoti">
                                <div class="letrerito">Importar cotización</div>
                                <button id="BotonDelAside_ImportarCompra">
                                    <img src="../../Imagenes/iconoDelMenu_Ventas.png" alt="">
                                </button>
                            </div>
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
                        <span class="TituloDeSection">Extrae los productos</span>
                        <p>
                            Utiliza la lista de productos vendidos para indicar de que almacén se extraerán los materiales y el equipamiento cotizado con
                            su respectiva cantidad. Tambien puedes extraer todos los productos del  
                            <span id="BotonTexto_ElegirPredeterminado" class="BotonTextoImportar">Almacén predeterminado </span>
                            <span class="BotonTextoImportar fi-rr-garage"></span> de forma rápida.
                            <input HIDDEN name="ListaDeExtraccion" form="FormularioPalPost" type="text" id="ListaDeExtraccion" style="width: 50%;">
                        </p>
                        <div class="EspacioDeListaYTabla">
                            <div class="EspacioDeProductosComprados">
                                <span class="fi-sr-ballot Titulillo"> Productos consumibles</span>
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
                                            <span>No se ha indicado la extracción de ningún producto.</span>
                                        </row>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="EspacioDeBotones">
                            <button id="ButtonGuardar" class="BotonContinuarNoDisponible" title="Aún hay productos vendidos que no han sido extraídos del inventario.">Guardar venta <i class="fi-sr-bookmark"></i></button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </article>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="ImportarCoti.js"></script>
    <script src="SeleccionarCliente.js"></script>
    <script src="AgregarProducto.js"></script>
    <script src="ExtraerProducto.js"></script>
</body>
</html>