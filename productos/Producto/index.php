<?php
include('../../Otros/clases.php');

$DatosAMostrar = array(
    "nombreDelCuadrito" => "No existe",
    "nombre" => "Este producto no existe",
    "id" => "000000",
    "precio" => "0",
    "idCategoria" => "",
    "categoria" => "",
    "unidadDeMedida" => "",
    "simbolo" => "",
    "nivelDeAlerta" => "",
    "descripcion" => ""
);

if(isset($_GET['id'])){
    $product = new product($_GET['id']);
    $ProductoAMostrar = new producto($_GET['id']);
    $DatosDelProducto = $ProductoAMostrar->ObtenerDatos();
    $ListaDeProveedores = $ProductoAMostrar->ObtenerProveedoresDisponibles();

    if(strlen($DatosDelProducto['nombre'])<12){
        $nombreDelCuadrito = $DatosDelProducto['nombre'];
    }else{
        $nombreDelCuadrito = substr($DatosDelProducto['nombre'],0,11)."...";
    }

    if(!empty($DatosDelProducto)){
        $DatosAMostrar = array(
            "nombreDelCuadrito" => $nombreDelCuadrito,
            "nombre" => $DatosDelProducto['nombre'],
            "id" => $DatosDelProducto['id'],
            "precio" => $DatosDelProducto['precio'],
            "idCategoria" => $DatosDelProducto['idCategoria'],
            "categoria" => $DatosDelProducto['categoria'],
            "idUnidadDeMedida" => $DatosDelProducto['idUnidadDeMedida'],
            "nombreUM" => $DatosDelProducto['nombreUM'],
            "simboloConEstiloUM" => $DatosDelProducto['simboloConEstiloUM'],
            "nivelDeAlerta" => $DatosDelProducto['nivelDeAlerta'],
            "descripcion" => $DatosDelProducto['descripcion']
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreCorto'].': '.$DatosAMostrar['nombre'];?></title>
    <link rel="stylesheet" href="estilos_producto.css">
    <?php include('../../Otros/cabecera_N3.php');

    

    
    
    
    
?>
    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../" class="Barra">
            <p>Productos</p>
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
                <img src="../../Imagenes/Productos/<?php echo ((empty($DatosDelProducto['ULRImagen']))?"ImagenPredefinida_Productos.png":$DatosDelProducto['ULRImagen'])?>" alt="">
            </div>
            <div id="CajaDeDatos">
                <div>
                    <div class="div-space-between">
                        <b id="NombreDeProducto"><?php echo $DatosAMostrar['nombre']?></b>
                        <span>#<span id="IDDeEntidad"><?php echo $DatosAMostrar['id']?></span></span>
                    </div>
                    <div id="rayita"></div>
                    <b id="precio"><?php echo $DatosAMostrar['precio'];?>$</b>
                </div>
                <div>
                    <p><b>Categoria: </b><?php echo $DatosAMostrar['categoria'];?></p>
                    <?php echo (($DatosAMostrar['idCategoria']<3)?"<p><b>Unidad de medida: </b>".((empty($DatosAMostrar['idUnidadDeMedida']))?"Indefinido":$DatosAMostrar['nombreUM'])." (".$DatosAMostrar['simboloConEstiloUM'].")</p>":""); ?>
                    <?php echo (($DatosAMostrar['idCategoria']<3)?"<p><b>Nivel de alerta: </b>".((empty($DatosAMostrar['nivelDeAlerta']))?"Indefinido":$DatosAMostrar['nivelDeAlerta']).$DatosAMostrar['simboloConEstiloUM']."</p>":"");?>
                    <?php
                    if($product->getIdCategory()==2){
                        if(!empty($product->getDefaultSpoilage())){
                            echo '<p><b>Depreciación estándar: </b>'.$product->getDefaultSpoilage().'</p>';
                        }
                    }
                    ?>
                    <p><b>Descripción: </b><?php echo ((empty($DatosAMostrar['descripcion']))?"<span style='color: gray;'>No hay descripción de este producto</span>":$DatosAMostrar['descripcion'])?></p>
                </div>
            </div>
        </div>
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> EXISTENCIA:</span>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaNombre">Almacén</celda>
                <celda class="ColumnaNombre">Dirección</celda>
                <celda class="ColumnaDetalles">Cantidad</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                $disposicion = $ProductoAMostrar->obtenerDisposicion();
                if(empty($disposicion)){
                    echo '
                    <row class="RowVacio">
                        <span>No hay almacénes a mostrar.</span>
                    </row>
                    ';
                }else{
                    foreach($disposicion as $row){
                        echo '
                        <row>
                            <celda class="ColumnaID">'.$row['id'].'</celda>
                            <celda class="ColumnaNombre">'.$row['nombre'].'</celda>
                            <celda class="ColumnaNombre">'.$row['direccion'].'</celda>
                            <celda class="ColumnaDetalles" title="'.$row['existencia'].' '.$DatosAMostrar['nombreUM'].' existentes en este almacen">x '.$row['existencia'].'</celda>
                            <celda class="ColumnaDetalles"><a href="../../Almacenes/Almacen/?id='.$row['id'].'">Ver más</a></celda>
                        </row>
                        ';
                    }
                }
                ?>
                
            </div>
        </div>
        <br>
        <span id="TopeDelListado" class="fi-sr-users TituloDeSectionDelArticle"> PROVEEDORES:</span>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaImg">Imagen</celda>
                <celda class="ColumnaRif">RIF</celda>
                <celda class="ColumnaRS">Razón social</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                $proveedores = $ProductoAMostrar->ObtenerProveedoresDisponibles();
                if(empty($proveedores)){
                    echo '
                    <row class="RowVacio">
                        <span>No hay proveedores a mostrar.</span>
                    </row>
                    ';
                }else{
                    foreach($proveedores as $row){
                        echo '
                        <row>
                            <celda class="ColumnaImg">
                                <img src="../../Imagenes/Proveedores/'.(empty($row['ULRImagen'])? 'ImagenPredefinida_Proveedores.png':$row['ULRImagen']).'" alt="">
                            </celda>
                            <celda class="ColumnaRif">'.$row['tipoDeDocumento'].'-'.$row['idProveedor'].'</celda>
                            <celda class="ColumnaRS">'.$row['nombre'].'</celda>
                            <celda class="ColumnaDetalles"><a href="../../Proveedores/Proveedor/?rif='.$row['idProveedor'].'">Ver más</a></celda>
                        </row>
                        ';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div id="BarraLateral">
        <div id="contenidoDeLaBarraLateral">
            <div id="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Productos.png" alt="">
                <b>Productos</b>
            </div>
            <a id="BotonEditar" href="../../Modificar/Producto/?id=<?php echo $_GET['id'];?>"> <i class="fi-rr-pencil"></i> Modificar producto</a>
            <a id="BotonEliminar" style="cursor: pointer"> <i class="fi-rr-trash"></i> Eliminar producto</a>
        </div>
        
    </div>
    
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
</body>
</html>