<?php
    include('../Otros/clases.php');

    
    $searchData = array(
        'search' => (isset($_GET['descripcion'])? $_GET['descripcion']:''),
        'order' => (isset($_GET['orden'])? $_GET['orden']:'nombre'),
        'status' => (isset($_GET['estado'])? $_GET['estado']:'0'),
        'category' => (isset($_GET['categoria'])? $_GET['categoria']:'0'),
    );
    
    
    

    $public = new publicFunctions();
    $public->checkProductImagesExistence();
    $productsList = $public->getProductsList($searchData);
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
        <input hidden type="text" placeholder="Step" id="step" form="FormularioBuscador" value="1">
        <form action="" method="get" autocomplete="off" id="buscador">
            <input currentValue="<?php echo $searchData['search'];?>" class="inputBuscador" name="descripcion" value="<?php echo $searchData['search'];?>" autofocus placeholder="Busca un producto por id, nombre, descripción...">
            <button class="BotonBuscador" type="submit"><i class="fi-rr-search"></i></button>
            <select currentValue="<?php echo $searchData['order'];?>" style="width: 140px;" name="orden" class="SelectBuscador" id="SelectorDeOrden" form="buscador">
                <option value="nombre" <?php echo ($searchData['order']=="nombre")?'selected=true':'';?>>Ordenar por nombre</option>
                <option value="precio" <?php echo ($searchData['order']=="precio")?'selected=true':'';?>>Ordenar por precio</option>
                <option value="estado" <?php echo ($searchData['order']=="estado")?'selected=true':'';?>>Ordenar por estado</option>
            </select>
        </form>
        <div id="CajaDeFiltros">
                <span>Mostrar: </span>
                
                    <select currentValue="<?php echo $searchData['status'];?>" name="estado" id="SelectorDeEstado" form="buscador">
                        <option value="0" <?php echo ($searchData['status']=="0")?'selected=true':'';?>>Todos</option>    
                        <option value="1" <?php echo ($searchData['status']=="1")?'selected=true':'';?>>Disponible</option>
                        <option value="2" <?php echo ($searchData['status']=="2")?'selected=true':'';?>>En alerta</option>
                        <option value="3" <?php echo ($searchData['status']=="3")?'selected=true':'';?>>Agotado</option>
                    </select>
                
                
                    <select currentValue="<?php echo $searchData['category'];?>" name="categoria" id="SelectorDeCategoria" form="buscador">
                        <option value="0" <?php echo ($searchData=="0")?'selected=true':'';?>>Categoría</option>    
                        <?php 
                        $listaDeCategorias = $BaseDeDatos->consultar("SELECT * FROM `categorias`");
                        foreach($listaDeCategorias as $Categoria){
                            echo '<option '.(($searchData['category']==$Categoria['id'])?'selected=true ':'').'value="'.$Categoria['id'].'">'.$Categoria['nombre'].'</option>';
                        }?>
                    </select>
                
            </div>
        <div id="CajaResultados">
            <?php
            if(empty($productsList['result'])){
                echo '
                <div class="TablaVacia">
                    NO HAY PRODUCTOS PARA MOSTRAR...
                </div>';
            }else{
                $prods = '';
                foreach($productsList['result'] as $row){
                    echo '
                    <a href="Producto?id='.$row['id'].'" class="TarjetaProducto">
                        <div class="CajaDeImagen">
                            <img src="../Imagenes/Productos/'.((empty($row['img']))?"ImagenPredefinida_Productos.png":$row['img']).'" alt="">
                        </div>
                        <div class="CajaDeCaractaresiticas">
                            <b class="titulo">'.$row['name'].'</b>
                            <div></div>
                            <p class="tipo">'.($row['idCategory']==4? 'Comida':($row['idCategory']==3? 'Mano de obra':($row['idCategory']==2? 'Equipo':'Material'))).'</p>
                            <p class="descripcion" >'.$row['desc'].'</p>
                        </div>
                        <b class="precioFlotador" >'.$row['price'].'$</b>
                        <div class="TapaDeTextoSobrante"></div>
                    </a>';
                }
                

                
            }
            ?>
            
        </div>
        <?php
        if($productsList['isNextStepPossible']){
            echo '<div id="loadingResults">
            Buscando más resultados
            </div>';
        }
        ?>
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