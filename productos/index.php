<?php
    include('../Otros/clases.php');

    if(!isset($_GET['descripcion'])||!isset($_GET['estado'])||!isset($_GET['categoria'])||!isset($_GET['orden'])){
        header('Location: ?descripcion=&estado=0&categoria=0&orden=nombre');
    }

    $valorDeEstado="";
    $valorDeCategoria="";
    $valorDeOrden="";
    $valorDeBusqueda="";
    
    
    
    if($_GET){
        $valorDeBusqueda = $_GET['descripcion'];
        $valorDeEstado   = $_GET['estado'];
        $valorDeCategoria= $_GET['categoria'];
        $valorDeOrden    = $_GET['orden'];
        

        $SQLDeEstado=($valorDeEstado==0)?"`idEstado` != 4 AND `idEstado` != 5":"`idEstado` = ".$valorDeEstado;

        $SQLDeCategoria=($valorDeCategoria==0)?"":"`idCategoria` = ".$valorDeCategoria;

        $SQLDeDescripcion=(empty($valorDeBusqueda))?"":'(`id` LIKE "%'.$valorDeBusqueda.'%" OR `nombre` LIKE "%'.$valorDeBusqueda.'%" OR  `descripcion` LIKE "%'.$valorDeBusqueda.'%")';
  
        if(empty($SQLDeEstado)||empty($SQLDeCategoria)){
            $FiltroDeEstadoYCategoria=$SQLDeEstado.$SQLDeCategoria;
        }else{
            $FiltroDeEstadoYCategoria=$SQLDeEstado." AND ".$SQLDeCategoria;
        }

        if(empty($FiltroDeEstadoYCategoria)||empty($SQLDeDescripcion)){
            $Filtros=$FiltroDeEstadoYCategoria.$SQLDeDescripcion;
        }else{
            $Filtros=$FiltroDeEstadoYCategoria." AND ".$SQLDeDescripcion;
        }

        $Filtros = (empty($Filtros))?"":" WHERE (".$Filtros.")";

        $SQLDeOrden=" ORDER BY `".(($valorDeOrden=="estado")?"idEstado":$valorDeOrden)."`";

        $ConsultaSegunBuscador="SELECT * FROM `productos`".$Filtros.$SQLDeOrden;
        
        
    }


    $public = new publicFunctions();
    $public->checkProductImagesExistence();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Productos</title>
    <link rel="stylesheet" href="estilos_productos.css">

    <?php include('../Otros/cabecera_N2.php');?>
    <div id="CajaDeBarras">
        <a href="../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p>Productos</p>
            <div class="Cuadrito" href="x"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article id="CajaContenido">
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> BUSCAR PRODUCTO:</span>
        <form action="" method="get" autocomplete="off" id="buscador">
            <input class="inputBuscador" name="descripcion" value="<?php echo $valorDeBusqueda;?>" autofocus placeholder="Busca un producto por id, nombre, descripción...">
            <button class="BotonBuscador" type="submit"><i class="fi-rr-search"></i></button>
            <select style="width: 140px;" name="orden" class="SelectBuscador" id="SelectorDeOrden" form="buscador">
                <option value="nombre" <?php echo ($valorDeOrden=="nombre")?'selected=true':'';?>>Ordenar por nombre</option>
                <option value="precio" <?php echo ($valorDeOrden=="precio")?'selected=true':'';?>>Ordenar por precio</option>
                <option value="estado" <?php echo ($valorDeOrden=="estado")?'selected=true':'';?>>Ordenar por estado</option>
            </select>
        </form>
        <div id="CajaDeFiltros">
                <span>Mostrar: </span>
                
                    <select name="estado" id="SelectorDeEstado" form="buscador">
                        <option value="0" <?php echo ($valorDeEstado=="0")?'selected=true':'';?>>Todos</option>    
                        <option value="1" <?php echo ($valorDeEstado=="1")?'selected=true':'';?>>Disponible</option>
                        <option value="2" <?php echo ($valorDeEstado=="2")?'selected=true':'';?>>En alerta</option>
                        <option value="3" <?php echo ($valorDeEstado=="3")?'selected=true':'';?>>Agotado</option>
                    </select>
                
                
                    <select name="categoria" id="SelectorDeCategoria" form="buscador">
                        <option value="0" <?php echo ($valorDeCategoria=="0")?'selected=true':'';?>>Categoría</option>    
                        <?php 
                        $listaDeCategorias = $BaseDeDatos->consultar("SELECT * FROM `categorias`");
                        foreach($listaDeCategorias as $Categoria){
                            echo '<option '.(($valorDeCategoria==$Categoria['id'])?'selected=true ':'').'value="'.$Categoria['id'].'">'.$Categoria['nombre'].'</option>';
                        }?>
                    </select>
                
            </div>
        <div id="CajaResultados">
            <?php
            $listaDeProductos = $BaseDeDatos->consultar($ConsultaSegunBuscador);
            
            if(empty($listaDeProductos)){
                echo '
                <div class="TablaVacia">
                    NO HAY PRODUCTOS PARA MOSTRAR...
                </div>';
            }else{
                $prods = '';
                foreach($listaDeProductos as $rowProducto){
                    $ProductoAMostrar = new producto($rowProducto['id']);
                    $DatosDelProducto = $ProductoAMostrar->ObtenerDatos();
    
                    $prods = '
                    <a href="Producto?id='.$DatosDelProducto['id'].'" class="TarjetaProducto">
                        <div class="CajaDeImagen">
                            <img src="../Imagenes/Productos/'.((empty($DatosDelProducto['ULRImagen']))?"ImagenPredefinida_Productos.png":$DatosDelProducto['ULRImagen']).'" alt="">
                        </div>
                        <div class="CajaDeCaractaresiticas">
                            <b class="titulo">'.$DatosDelProducto['nombre'].'</b>
                            <div></div>
                            <p class="tipo">'.$DatosDelProducto['categoria'].'</p>
                            <p class="descripcion" >'.$DatosDelProducto['descripcion'].'</p>
                        </div>
                        <b class="precioFlotador" >'.$DatosDelProducto['precio'].'$</b>
                        <div class="TapaDeTextoSobrante"></div>
                    </a>'.$prods;
                }

                echo $prods;
            }
            ?>
            
        </div>
    </article>
    <div id="BarraLateral">
        <div id="contenidoDeLaBarraLateral">
            <div id="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Productos.png" alt="">
                <b>Productos</b>
            </div>
            <a id="AgregarNuevo" href="Nuevo"> <i class="fi-rr-add"></i> Nuevo producto</a>
        </div>
    </div>
    <?php include '../ipserver.php';?>
<script src="productos.js"></script>

</body>
</html>