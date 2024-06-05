<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();

//Reviso si hay alguna cotizacion en el borrador
$listaDeBorrador = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE `idEstado` = 36");
$ExisteBorrador = !empty($listaDeBorrador);
if($ExisteBorrador){
    $CotizacionEnBorrador = new cotizacion($listaDeBorrador[0]['id']);
    $DatosDeCotizacionEnBorrador = $CotizacionEnBorrador->ObtenerDatos();
}else{
    $CotizacionFantasma = new cotizacion(0);
}

//Inicializo los datos predeterminados a mostrar en los inputs
$DatosAMostrar = array(
    'RifCliente' => '',
    'Nombre' => '',
    'FechaExpiracion' => '',
    'DiasDeVigencia' => '',
    'Utilidades' => '30',
    'IVA' => '16',
    'CASalario' => '335',
    'IDsDeMateriales' => '',
    'IDsDeMaquinaria' => '',
    'IDsDeManoDeObra' => ''
);

if($_POST){
    
    //Almaceno los datos recibidos por el formulario
    $DatosAMostrar = array(
        'RifCliente' => $_POST['RifCliente'],
        'Nombre' => $_POST['Nombre'],
        'FechaExpiracion' => ((empty($_POST['FechaExpiracion']))?'':$_POST['FechaExpiracion']),
        'DiasDeVigencia' => ((empty($_POST['DiasDeVigencia']))?'0':$_POST['DiasDeVigencia']),
        'Utilidades' => $_POST['Utilidades'],
        'IVA' => $_POST['IVA'],
        'CASalario' => $_POST['CASalario'],
        'IDsDeMateriales' => $_POST['IDsDeMateriales'],
        'IDsDeMaquinaria' => $_POST['IDsDeMaquinaria'],
        'IDsDeManoDeObra' => $_POST['IDsDeManoDeObra']
    );
    
    if(isset($_POST['Guardar'])){
        if($ExisteBorrador){
            try{
                $Problemas = $CotizacionEnBorrador->Actualizar($_POST,33);
            }catch(Exception $Error){
                $Problemas = $Error->getMessage();
            }
        }else{
            try{
                $Problemas = $CotizacionFantasma->CrearNuevo($_POST,33);
            }catch(Exception $Error){
                $Problemas = $Error->getMessage();
            }
        }
        if(empty($Problemas)){
            $ConsultaDeUltimoID = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE `idEstado` = 33 ORDER BY `id` DESC LIMIT 0,1");
            header("Location: ../Venta?id=".$ConsultaDeUltimoID[0]['id']."&alert=nuevo");
        }
    }

    if(isset($_POST['Borrador'])){
        if($ExisteBorrador){
            try{
                $Problemas = $CotizacionEnBorrador->Actualizar($_POST,36);
            }catch(Exception $Error){
                $Problemas = $Error->getMessage();
            }
        }else{
            try{
                $Problemas = $CotizacionFantasma->CrearNuevo($_POST,36);
            }catch(Exception $Error){
                $Problemas = $Error->getMessage();
            }
        }
        if(empty($Problemas)){
            header("Location: ../");
        }
    }
}else{
    if($ExisteBorrador){
        $DatosAMostrar = array(
            'RifCliente' => $DatosDeCotizacionEnBorrador['cedulaCliente'],
            'Nombre' => $DatosDeCotizacionEnBorrador['nombre'],
            'FechaExpiracion' => $DatosDeCotizacionEnBorrador['fechaExpiracion'],
            'DiasDeVigencia' => $DatosDeCotizacionEnBorrador['DiasDeVigencia'],
            'Utilidades' => $DatosDeCotizacionEnBorrador['pUtilidades'],
            'IVA' => $DatosDeCotizacionEnBorrador['pIVA'],
            'CASalario' => $DatosDeCotizacionEnBorrador['pCASalario'],
            'IDsDeMateriales' => $DatosDeCotizacionEnBorrador['MaterialesCotizados'],
            'IDsDeMaquinaria' => $DatosDeCotizacionEnBorrador['MaquinasCotizados'],
            'IDsDeManoDeObra' => $DatosDeCotizacionEnBorrador['ManoCotizados']
        );
    }
}
?>

<!DOCTYPE html>
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Nueva cotización</title>
    <link rel="stylesheet" href="estilos_nuevaCotizacion.css">
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
            <p>Nueva</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <label class="ModalDeError yava">
        <input hidden <?php echo ((empty($Problemas))?"":"checked");?> type="checkbox" class="MostrarModalDeError">
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
    <modal id="ModalAgregarCliente" class="Modal">
        <div id="VentanaFlotanteAgregarCliente" class="VentanaFlotante">
            <div class="ContenidoDeVentana">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentanaAgregarCliente" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <B class="TituloDeModal" >SELECCIÓN DE CLIENTE</B>
                <div class="EspacioDelBuscador">
                    <input autocomplete="off" type="text" class="Buscador"  placeholder="Filtrar por RIF o nombre..." id="InputDeBuscadorDeClientes">
                    <button type="button" class="BotonBuscar" id="BotonFiltrarCliente"> <i class="fi-rr-search"></i> </button>
                </div>
                <div class="EspacioDeTablaDeClientes" id="test11">
                    <div class="ColumnasDeTabla Flex-gap2">
                        <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                        <span class="NombreDeColumna ColumnaRIF">RIF</span>
                        <span class="NombreDeColumna ColumnaNombre3">Nombre</span>
                        <div class="NombreDeColumna ColumnaSeleccionar">Seleccionar</div>
                    </div>
                    <div class="EspacioDeRows" id="ResultadosDeLaBusqueda">
                        <div class="did_loading">
                            <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                            Cargando
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <modal id="ModalAgregarProducto">
        <div class="VentanaFlotanteProductos">
            <div class="ContenidoDeVentana" id="ContenidoDelModal">
                <button type="button" title="Cerrar ventana" id="BotonCerrarVentanaProductos" class="BotonCerrar"> <i class="fi-rr-cross-small"></i> </button>
                <B class="TituloDeModal" >SELECCIÓN DE PRODUCTOS</B>
                <div class="DivisorDelBuscadorDeProductos">
                    <div class="EspacioDeTablaBuscadorProductos">
                        <div class="EspacioDeBuscadorDeProductos">
                            <input autocomplete="off" id="BuscadorDeProductos" type="text" placeholder="Busca por ID, nombre o descripcion...">
                            <button type="button" id="BotonBuscarProductos"> <i class="fi-rr-search"></i> </button>
                            <select name="" id="SelectCategoriaDelProductoABuscar">
                                <option value="0">Todos</option>
                                <?php
                                    $ResultadosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `categorias`");

                                    foreach($ResultadosDeLaConsulta as $Categoria){
                                        echo '<option value="'.$Categoria['id'].'">'.$Categoria['nombre'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="TablaDeProdcutos">
                            <div class="ColumnasDeTabla Flex-gap2">
                                <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                                <span class="NombreDeColumna ColumnaID">ID</span>
                                <span class="NombreDeColumna ColumnaNombre4">Nombre</span>
                                <span class="NombreDeColumna ColumnaCantidad">Cotizado</span>
                            </div>
                            <div class="EspacioDeRows" id="EspacioDeProductosConsultados">
                                <div class="did_loading">
                                    <div class="rotating"><span class="fi fi-rr-loading"></span></div>
                                    Cargando
                                </div>
                            </div>
                        </div>
                    </div>
                    <input hidden type="text" name="" id="InputIdProductoAAgregar">
                    <div class="EspacioDePrevisualizacionDeProducto" id="PrevisualizacionDeProducto">
                        <!--Aqui actua ModalAgregarProducto.js-->
                    </div>
                </div>
            </div>
        </div>
    </modal>
    <form autocomplete="off" method="post">
    <article>
        <span id="ZonaDeCliente" class="SubtituloCentral"><i class="fi-sr-user Arreglito"></i> CLIENTE:</span>
        <!--<input type="text" id="InputIDDelCliente">-->
        <div id="EspacioDeTarjetaDeCliente">
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
                            <input type="text" name="" id="TipoDeRifCliente" class="EspacioDeTipoDeID" maxlength="1">
                            <b class="GuionSeparador"> - </b>
                            <input value="<?php echo $DatosAMostrar['RifCliente'];?>" type="text" name="RifCliente" id="IdCliente" class="EspacioDeID" maxlength="9">
                        </div>
                        <div class="FlexH">
                            <input type="text" name="" id="NombreCliente" class="EspacioDeNombre">
                        </div>
                        <div class="FlexH">
                            <input type="text" name="" id="TelefonoCliente" class="EspacioDeTelefono">
                        </div>
                        <div class="FlexH">
                            <input type="text" name="" id="CorreoCliente" class="EspacioDeCorreo" placeholder="No especificado">
                        </div>
                        <div class="FlexH">
                            <input type="text" name="" id="DireccionCliente" class="EspacioDeDireccion">
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
                    <input value="<?php echo $DatosAMostrar['Nombre']?>" type="text" name="Nombre" id="InputNombreDeLaCot" class="Targeteable RayitaVino" maxlength="50">
                    <select name="" id="SelectTiempoLimitado">
                        <option value="No">No</option>
                        <option <?php echo ((empty($DatosAMostrar['FechaExpiracion']))?'':'selected ');?>value="Si">Si</option>
                    </select>
                    <xd class="FlexH-NoGap">
                        <input maxlength="3" value="<?php echo $DatosAMostrar['DiasDeVigencia'];?>" type="text" name="DiasDeVigencia" class="targeteable" id="CampoNumeroDeDias"  onkeypress="return SoloNumerosInt(event)" onpaste="return false">
                        <label for="CampoNumeroDeDias" id="LabelDias">Días</label>
                        <input type="date" name="FechaExpiracion" id="CalendarioFlotante">
                    </xd>
                    <p class="Targeteable" id="FechaVencimiento"><?php echo ((empty($DatosAMostrar['FechaExpiracion']))?'00/00/0000':$DatosAMostrar['FechaExpiracion']);?></p>
                    <xd class="FlexH-NoGap">
                        <input value="<?php echo $DatosAMostrar['CASalario'];?>" type="text" name="CASalario" class="targeteable TARight" id="InputCASalario"  onkeypress="return SoloDosNumeros3(event)" onpaste="return false">
                        <label class="Porcentaje" id="" for="InputCASalario">%</label>
                    </xd>
                    <xd class="FlexH-NoGap">
                        <input value="<?php echo $DatosAMostrar['Utilidades'];?>" type="text" name="Utilidades" class="targeteable TARight" id="Utilidades"  onkeypress="return SoloDosNumeros(event)" onpaste="return false">
                        <label class="Porcentaje" id="" for="Utilidades">%</label>
                    </xd>
                    <xd class="FlexH-NoGap">
                        <input value="<?php echo $DatosAMostrar['IVA'];?>" type="text" name="IVA" class="targeteable TARight" id="InputIVA"  onkeypress="return SoloDosNumeros2(event)" onpaste="return false">
                        <label class="Porcentaje" id="" for="InputIVA">%</label>
                    </xd>
                </div>
            </div>
            
        </section>
        <br>
        <span class="SubtituloCentral"><i class="fi-sr-ballot Arreglito"></i> PRODUCTOS:</span>
        <section class="CuerpoDeLaCotizacion">
            <input hidden value="<?php echo $DatosAMostrar['IDsDeMateriales'];?>" name="IDsDeMateriales" type="text" id="ProductosCotizados" style="width: 100%;">
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
            <div class="EspacioDeRows" id="MaterialesAgregados">
                <!--Aqui actua ModalAgregarProducto.js-->
                <row>
                    <span class="TablaVacia">Esta cotización no tiene materiales</span>
                </row>
            </div>
            <div class="BotonDinamicoAgregar">
                <span class="fi-rr-plus-small" id="BotonAgregarProductoMaterial"> Agregar producto</span>
           </div>
            <input hidden value="<?php echo $DatosAMostrar['IDsDeMaquinaria'];?>" name="IDsDeMaquinaria"  type="text" id="MaquinasCotizados" style="width: 100%;">
            <span class="TituloDeCuerpo">Equipo</span>
            <div class="Flex-gap2 ColumnasDeTabla">
                <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                <span class="NombreDeColumna ColumnaID">ID</span>
                <span class="NombreDeColumna ColumnaNombre">Nombre</span>
                <span class="NombreDeColumna ColumnaCantidad">Cantidad</span>
                <span class="NombreDeColumna ColumnaUnidad">Desgaste</span>
                <span class="NombreDeColumna ColumnaPrecio">Precio</span>
                <span class="NombreDeColumna ColumnaTotal">Total</span>
            </div>
            <div class="EspacioDeRows" id="HerramientasAgregados">
                <row>
                    <span class="TablaVacia">Esta cotización no tiene maquinaria ni herramientas</span>
                </row>
            </div>
            <div class="BotonDinamicoAgregar">
                <span class="fi-rr-plus-small" id="BotonAgregarProductoHerramienta"> Agregar producto</span>
           </div>
            <input hidden value="<?php echo $DatosAMostrar['IDsDeManoDeObra'];?>" name="IDsDeManoDeObra"  type="text" id="ManosCotizados" style="width: 100%;">
            <span class="TituloDeCuerpo">Mano de obra</span>
            <div class="Flex-gap2 ColumnasDeTabla">
                <span class="NombreDeColumna ColumnaImagen">Imagen</span>
                <span class="NombreDeColumna ColumnaID">ID</span>
                <span class="NombreDeColumna ColumnaNombre">Nombre</span>
                <span class="NombreDeColumna ColumnaCantidad">Cantidad</span>
                <span class="NombreDeColumna ColumnaUnidad">Días</span>
                <span class="NombreDeColumna ColumnaPrecio">Precio</span>
                <span class="NombreDeColumna ColumnaTotal">Total</span>
            </div>
            <div class="EspacioDeRows" id="ManoDeObraAgregados">
                <row>
                    <span class="TablaVacia">Esta cotización no tiene mano de obra</span>
                </row>
            </div>
            <div class="BotonDinamicoAgregar">
                <span class="fi-rr-plus-small" id="BotonAgregarProductoMano"> Agregar producto</span>
           </div>
            <footer class="FinalDeCotizacion">
                <div class="SubTotal">
                    <div class="TitulosDelTotal ColumnaDelFinalDeCot">
                        <b>Costo en materiales</b>
                        <b>Costo en equipo</b>
                        <b>Costo en mano de obra</b>
                        <b id="TituloSalario">Asociado al salario (<?php echo $DatosAMostrar['CASalario'];?>%)</b>
                        <div class="PalitoDeSuma"></div>
                        <b>Costo de productos</b>
                        <b id="TituloUtilidades">Utilidades (<?php echo $DatosAMostrar['Utilidades'];?>%)</b>
                        <div class="PalitoDeSuma"></div>
                        <b>Sub Total</b>
                        <b id="TituloIVA">I.V.A. (<?php echo $DatosAMostrar['IVA'];?>%)</b>
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
            <a href="../" class="hovershadow">Salir</a>
            <button type="submit" name="Guardar" id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </article>
    </form>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Ventas.png" alt="">
                <b>Ventas</b>
            </div>
            <label id="BotonAbrirModalAgregarProducto"> <i class="fi-sr-apps-add"></i> Agregar producto</label>
            <a href="../../Ayuda/#63" target="_blank"><i class="fi-rr-interrogation"></i> Obtener información</a>
            <a href="../" id=""> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
            <a HIDDEN href="../../Otros/funcion_EliminarCotizacionEnBorrador.php" id="VaciarFormulario" class="BotonesLaterales" > <i class="fi-sr-broom"></i> Vaciar formulario</a>
            <label HIDDEN for="BotonBorrador" class="BotonesLaterales" > <i class="fi-sr-folder-minus"></i> Guardar en borrador</label>
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="NuevaCotizacion.js"></script>
    <script src="ModalAgregarCliente.js"></script>
    <script src="ModalAgregarProducto.js"></script>
</body>
</html>