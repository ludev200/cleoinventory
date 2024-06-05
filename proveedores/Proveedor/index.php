<?php
include('../../Otros/clases.php');

$DatosAMostrar = array(
        "nombreDelCuadrito" => "No existe",
        "ULRImagen" => "ImagenPredefinida_Proveedores.png",
        "nombre" => "Este proveedor no existe",
        "tipoDeDocumento" => "",
        "rif" => "",
        "numeroCompleto1" => "<i>No especificado</i>",
        "numeroCompleto2" => "",
        "correo" => "<i>No especificado</i>",
        "direccion" => "<i>No especificado</i>"
    );

    if(isset($_GET['rif'])){
        $ProveedorAMostrar = new proveedor($_GET['rif']);
        $DatosDelProveedor = $ProveedorAMostrar->ObtenerDatos();
        
        if(!empty($DatosDelProveedor)){
            if(strlen($DatosDelProveedor['nombre'])<11){
                $nombreDelCuadrito = $DatosDelProveedor['nombre'];
            }else{
                $nombreDelCuadrito = substr($DatosDelProveedor['nombre'],0,10)."...";
            }

            $DatosAMostrar = array(
                "nombreDelCuadrito" => $nombreDelCuadrito,
                "ULRImagen" => ((empty($DatosDelProveedor['ULRImagen']))?"ImagenPredefinida_Proveedores.png":$DatosDelProveedor['ULRImagen']),
                "nombre" => $DatosDelProveedor['nombre'],
                "tipoDeDocumento" => $DatosDelProveedor['tipoDeDocumento'],
                "rif" => $DatosDelProveedor['rif'],
                "numeroCompleto1" => $DatosDelProveedor['numeroCompleto1'],
                "numeroCompleto2" => $DatosDelProveedor['numeroCompleto2'],
                "correo" => $DatosDelProveedor['correo'],
                "direccion" => $DatosDelProveedor['direccion'],
            );

            $ListaDeProductos = $ProveedorAMostrar->ObtenerListaDeProductos();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: <?php echo $DatosAMostrar['nombre'];?></title>
    <link rel="stylesheet" href="estilos_proveedor.css">
<?php include('../../Otros/cabecera_N3.php');
    
    

    

    
    
?>
    <div id="CajaDeBarras">
    <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../" class="Barra">
            <p>Proveedores</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p><?php echo $DatosAMostrar['nombreDelCuadrito'];?></p>
            <div class="Cuadrito" href="x"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p>Miercoles, 5 de septiembre de 2020</p>
    </div>
    <div id="CajaContenido">
        <div id="CajaDeProducto">
            <div id="BordeDelProducto" >
                <img src="../../Imagenes/Proveedores/<?php echo $DatosAMostrar['ULRImagen']?>" alt="">
            </div>
            <div id="CajaDeDatos">
                <div>
                    <div class="div-space-between">
                        <b id="NombreDeProducto"><?php echo $DatosAMostrar['nombre']?></b>
                    </div>
                    <div id="rayita"></div>
                    <b id="precio"><?php echo $DatosAMostrar['tipoDeDocumento']." - <span id='IDDeEntidad'>".$DatosAMostrar['rif']."</span>";?></b>
                </div>
                <div>
                    <p><i class=" fi-sr-circle-phone Arreglito"></i> INFORMACIÓN DE CONTACTO</p>
                    <p><b>Telefono: </b><?php echo ((empty($DatosAMostrar['numeroCompleto2']))?$DatosAMostrar['numeroCompleto1']:$DatosAMostrar['numeroCompleto1']." / ".$DatosAMostrar['numeroCompleto2'])?></p>
                    
                    <p><b>Correo: </b><?php echo $DatosAMostrar['correo'];?></p>
                    <p><b>Dirección: </b><?php echo $DatosAMostrar['direccion'];?></p>
                </div>
            </div>
        </div>
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> PRODUCTOS:</span>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">Imagen</celda>
                <celda class="ColumnaRIF">ID</celda>
                <celda class="ColumnaNombre">Nombre</celda>
                <celda class="ColumnaProductos">Categoría</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                if(empty($ListaDeProductos)){
                    echo '
                    <div class="FlexH-TablaDeProductos RowDeTabla TablaVacia">
                    <span>Este proveedor no tiene productos...</span>
                    </div>';
                }else{
                    foreach($ListaDeProductos as $ProductoDisponible){
                        echo '
                        <row>
                            <celda class="ColumnaID"><img src="../../Imagenes/Productos/'.((empty($ProductoDisponible['ULRImagen']))?'ImagenPredefinida_Productos.png':$ProductoDisponible['ULRImagen']).'"></celda>
                            <celda class="ColumnaRIF" style="justify-content: center;">'.$ProductoDisponible['id'].'</celda>
                            <celda class="ColumnaNombre" style="align-items: baseline;">'.$ProductoDisponible['nombre'].'</celda>
                            <celda class="ColumnaProductos" style="justify-content: center;">'.$ProductoDisponible['categoria'].'</celda>
                            <celda class="ColumnaDetalles"><a target="_blank" href="../../Productos/Producto/?id='.$ProductoDisponible['id'].'">Ver más</a></celda>
                        </row>
                        ';
                    }
                }
                ?>
            </div>
        </div>
        <br>
    </div>
    <div id="BarraLateral">
        <div id="contenidoDeLaBarraLateral">
            <div id="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Proveedores.png" alt="">
                <b>Proveedores</b>
            </div>
            <a id="BotonEditar" href="../../Modificar/Proveedor/?rif=<?php echo $_GET['rif'];?>"> <i class="fi-rr-pencil"></i> Modificar proveedor</a>
            <a id="BotonEliminar" style="cursor: pointer"> <i class="fi-rr-trash"></i> Eliminar proveedor</a>
        </div>
    </div>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
</body>
</html>
