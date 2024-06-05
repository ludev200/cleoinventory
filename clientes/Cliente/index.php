<?php
include('../../Otros/clases.php');
$Tiempo = new AsistenteDeTiempo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Cliente</title>
    <link rel="stylesheet" href="estilos_cliente.css">
<?php include('../../Otros/cabecera_N3.php');
    
    $DatosAMostrar = array(
        "nombreDelCuadrito" => "No existe",
        "ULRImagen" => "ImagenPredefinida_Clientes.png",
        "nombre" => "Este proveedor no existe",
        "tipoDeDocumento" => "",
        "rif" => "",
        "numeroCompleto1" => "<i>No especificado</i>",
        "numeroCompleto2" => "",
        "correo" => "<i>No especificado</i>",
        "direccion" => "<i>No especificado</i>",

        'descripcion' => '',
        'estado' => '',

    );

    if(isset($_GET['rif'])){
        $ClienteAMostrar = new cliente($_GET['rif']);
        $DatosDelProveedor = $ClienteAMostrar->ObtenerDatos();
        
        if(!empty($DatosDelProveedor)){
            if(strlen($DatosDelProveedor['nombre'])<11){
                $nombreDelCuadrito = $DatosDelProveedor['nombre'];
            }else{
                $nombreDelCuadrito = substr($DatosDelProveedor['nombre'],0,10)."...";
            }

            $DatosAMostrar = array(
                "nombreDelCuadrito" => $nombreDelCuadrito,
                "ULRImagen" => ((empty($DatosDelProveedor['ULRImagen']))?"ImagenPredefinida_Clientes.png":$DatosDelProveedor['ULRImagen']),
                "nombre" => $DatosDelProveedor['nombre'],
                "tipoDeDocumento" => $DatosDelProveedor['tipoDeDocumento'],
                "rif" => $DatosDelProveedor['rif'],
                "numeroCompleto1" => $DatosDelProveedor['numeroCompleto1'],
                "numeroCompleto2" => $DatosDelProveedor['numeroCompleto2'],
                "correo" => $DatosDelProveedor['correo'],
                "direccion" => $DatosDelProveedor['direccion'],

                'descripcion' => (empty($_GET['descripcion'])? '':$_GET['descripcion']),
                'estado' => (empty($_GET['estado'])? '':$_GET['estado']),
            );

            
        }
    }

    $Filtros = array(
        'descripcion' => ((isset($_GET['descripcion']))?$_GET['descripcion']:''),
        'estado' => ((isset($_GET['estado']))?$_GET['estado']:'0')
    );

    
    $CotizacionesDelCliente = $ClienteAMostrar->ObtenerCotizaciones($Filtros);


?>
    <div id="CajaDeBarras">
    <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../" class="Barra">
            <p>Clientes</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p><?php echo $DatosAMostrar['nombreDelCuadrito'];?></p>
            <div class="Cuadrito" href="x"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article id="CajaContenido">
        <div id="CajaDeProducto">
            <div id="BordeDelProducto" >
                <img src="../../Imagenes/Clientes/<?php echo $DatosAMostrar['ULRImagen']?>" alt="">
            </div>
            <div id="CajaDeDatos">
                <div>
                    <div class="div-space-between">
                        <b id="NombreDeProducto"><?php echo $DatosAMostrar['nombre']?></b>
                    </div>
                    <div id="rayita"></div>
                    <b><?php echo $DatosAMostrar['tipoDeDocumento'].' - <span id="idCliente" idCliente="'.$DatosAMostrar['rif'].'">'.zerofill($DatosAMostrar['rif'], 9)."</span>";?></b>
                </div>
                <div>
                    <p><i class=" fi-sr-circle-phone Arreglito"></i> INFORMACIÓN DE CONTACTO</p>
                    <p><b>Telefono: </b><?php echo ((empty($DatosAMostrar['numeroCompleto2']))?$DatosAMostrar['numeroCompleto1']:$DatosAMostrar['numeroCompleto1']." / ".$DatosAMostrar['numeroCompleto2'])?></p>
                    
                    <p><b>Correo: </b><?php echo $DatosAMostrar['correo'];?></p>
                    <p><b>Dirección: </b><?php echo $DatosAMostrar['direccion'];?></p>
                </div>
            </div>
        </div>
        <span id="TopeDelListado" class="fi-rr-clipboard-list TituloDeSectionDelArticle"> COTIZACIONES Y VENTAS:</span>
        
        <form id="FormularioBuscador" href="#SeccionDeLista" method="get" autocomplete="off">
            <input HIDDEN name="rif" type="text" value="<?php echo $DatosAMostrar['rif'];?>">
            <input value="<?php echo $DatosAMostrar['descripcion'];?>" type="text" name="descripcion" id="" placeholder="Filtra por ID o descripcion...">
            <button id="BotonBuscarCotizaciones" type="submit"><i class="fi-rr-search"></i></button>
            <select name="estado" id="SelectEstado">
                <option value="0" selected="true">Todos</option>
                <option <?php echo ($DatosAMostrar['estado']==31? 'selected':'');?> value="31">Confirmadas</option>
                <option <?php echo ($DatosAMostrar['estado']==32? 'selected':'');?> value="32">Rechazadas</option>
                <option <?php echo ($DatosAMostrar['estado']==33? 'selected':'');?> value="33">En espera</option>
                <option <?php echo ($DatosAMostrar['estado']==34? 'selected':'');?> value="34">Vencidas</option>
            </select>
            
        </form>
        <section id="SeccionDeLista" class="TablaDeResultados">
            <div class="FlexH ColumnasDeTablas">
                <span class="ColumnaID">ID</span>
                <span class="ColumnaNombre">Descripción del servicio</span>
                <span class="ColumnaCliente">Expira</span>
                <span class="ColumnaEstado">Estado</span>
                <span class="ColumnaDetalles">Detalles</span>
            </div>
            <div class="RowsDeTabla">
                <?php
                if(empty($CotizacionesDelCliente)){
                    echo '
                    <div class="Row RowVacio">
                        <span>No hay cotizaciones para mostrar</span>
                    </div>
                    ';
                }else{
                    foreach($CotizacionesDelCliente as $coti){
                        $estado = 'todavia';
                        $puntoEstado = 'xd';
                        switch($coti['idEstado']){
                            case 31:
                                $estado = 'Confirmada';
                                $puntoEstado = 'EstadoAceptado';
                            break;
                            case 32:
                                $estado = 'Rechazada';
                                $puntoEstado = 'EstadoRechazado';
                            break;
                            case 33:
                                $estado = 'En espera';
                                $puntoEstado = '';
                            break;
                            case 34:
                                $estado = 'Vencida';
                                $puntoEstado = 'EstadoVencido';
                            break;
                        }
                        echo '
                        <div class="FlexH Row">
                            <span class="ColumnaID">'.zerofill($coti['id'], 7).'</span>
                            <span class="ColumnaNombre">'.$coti['nombre'].'</span>
                            <span class="ColumnaCliente">'.(empty($coti['fechaExpiracion'])? '<span style="color: gray;">No</span>':$Tiempo->ConvertirFormato($coti['fechaExpiracion'], 'BaseDeDatos', 'MaracayXD')).'</span>
                            <span class="ColumnaEstado PuntoDeEstado '.$puntoEstado.'">'.$estado.'</span>
                            <span class="ColumnaDetalles"><a class="hovershadow" href="../../Ventas/Venta?id='.$coti['id'].'">Ver más</a></span>
                        </div>
                        ';
                    }
                }
                ?>
            </div>
        </section>
        <br>
    </article>
    <div id="BarraLateral">
        <div id="contenidoDeLaBarraLateral">
            <div id="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Clientes.png" alt="">
                <b>Clientes</b>
            </div>
            <a id="BotonEditar" href="../../Modificar/Cliente/?id=<?php echo $_GET['rif'];?>"> <i class="fi-rr-pencil"></i> Modificar cliente</a>
            <a id="BotonEliminar"> <i class="fi-rr-trash"></i> Eliminar cliente</a>
        </div>
    </div>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
</body>
</html>
