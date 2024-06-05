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
    $product = new product($_GET['id']);
}else{
    header('Location: ../../error.php');
}

$units = $BaseDeDatos->consultar("SELECT * FROM `unidadesdemedida` WHERE `id` > 1");

if($_POST){
    try{
        $result = $product->updateData($_POST, $_FILES);
        header('Location: ../../Productos/Producto/?id='.$product->getId());
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
    <title><?php echo $GLOBALS['nombreCorto'];?>: Modificar Producto</title>


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
            <input hidden type="checkbox" name="" id="MostrarBotonOcultoPalMenu">    
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
        <a href="../../Productos" class="Barra">
            <p>Productos</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Productos/Producto/?id=<?php echo $product->getId();?>" class="Barra">
            <p><?php echo $product->getName();?></p>
            <div class="Cuadrito" href="../"></div>
        </a>
        <a href="" class="Barra">
            <p>Modificar</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>


    <form method="post" autocomplete="off" enctype="multipart/form-data">
        <h2 class="SubtituloCentral">
            <i class="fi-sr-hastag Arreglito"></i>
            PRODUCTO
            <span id="idEntity"><?php echo $product->getId();?></span>
            <input type="text" name="idEntity" value="<?php echo $product->getId();?>" hidden>
        </h2>
        
        <div class="imgAndId">
            <label title="Cambiar imagen">
                <img src="../../Imagenes/Productos/<?php echo (empty($product->getImage())? 'ImagenPredefinida_Productos.png':$product->getImage());?>" alt="">
                <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" hidden="">
            </label>
            <div class="inputsContainer">
                <b>Nombre:</b>
                <input type="text" id="name" name="name" value="<?php echo $product->getName();?>" maxlength="30">
                <br>
                <b>Precio:</b>
                <div class="sufixContainer">
                    <input type="text" onblur="priceFormat(this)" onkeypress="return validate_2floatNumber(this, event)" id="priceInput" name="price" value="<?php echo $product->getPrice();?>" placeholder="0.00">
                    <b>$</b>
                </div>
            </div>
        </div>

        <h2 class="SubtituloCentral">
            <i class="fi-sr-list Arreglito"></i>
            DETALLES
        </h2>
        <div class="inputsContainer">
            <div style="margin-bottom: 10px;">
                <b>Categoría:</b> <a href="../../Ayuda/#indice1" class="AyudaEnNavegacion"><i class="fi-sr-interrogation"></i> <span>Ir a información de las categorías</span></a>
            </div>
            <select name="idCategory" id="categoryInput" style="width: 200px">
                <option value="1" <?php echo ($product->getIdCategory()==1? 'selected':'');?>>Material</option>
                <option value="2" <?php echo ($product->getIdCategory()==2? 'selected':'');?>>Equipo</option>
                <option value="3" <?php echo ($product->getIdCategory()==3? 'selected':'');?>>Mano de Obra</option>
                <option value="4" <?php echo ($product->getIdCategory()==4? 'selected':'');?>>Comida</option>
            </select>

            <b class="hideOnWorkHand">Unidad de medida:</b>
            <select name="idUnit" id="unitInput" style="width: 200px" class="hideOnWorkHand">
                <?php
                foreach($units as $row){
                    echo '<option value="'.$row['id'].'" '.($row['id']==$product->getIdUnit()? 'selected':'').' simbol="'.$row['simbolo'].'">'.$row['nombre'].'</option>';
                }
                ?>
            </select>

            <b class="hideOnWorkHand">Nivel de alerta:</b>
            <div id="sufixContainer" class="hideOnWorkHand sufixContainer">
                <input type="text" name="alertLevel" onkeypress="return validate_intNumber(this, event)" id="alertLevelInput" value="<?php echo $product->getAlertLevel();?>" placeholder="0" maxlength="9" onfocus="this.select()">
                <b id="unitSimbol">$</b>
            </div>

            <b class="hideOnWorkHand showForEquipment">Depreciación estándar:</b>
            <div class="hideOnWorkHand sufixContainer showForEquipment">
                <input maxlength="6" name="deafultSpoilage" type="text" id="deafultSpoilage" value="<?php echo $product->getDefaultSpoilage();?>" placeholder="0" onfocus="this.select()" onkeypress="return validate_4floatNumber(this, event)">
                <b id="unitSimbol">u</b>
            </div>

            <div style="margin-bottom: 10px;">
                <b>Descripción:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <textarea name="description" onkeypress="return validateNoHashtag(this, event)" id="descriptionInput" cols="30" rows="10" placeholder="Puedes añadir una descripción del producto" maxlength="150"><?php echo $product->getDescription();?></textarea>
        </div>

        <h2 class="SubtituloCentral">
            <i class="fi-sr-users Arreglito"></i>
            PROVEEDORES VINCULADOS
        </h2>
        
        <div class="searchForm">
            <input type="text" id="searchValueInput" placeholder="Buscar" maxlength="20" form="ningunoxd">
            <button type="button" id="searchValueButton"><i class="fi-rr-search"></i></button>
        </div>
        <div class="twinContainer">
            <div class="searchList">
                <div class="tableHeader">
                    <span class="cell cellImage">
                        Imagen
                    </span>
                    <span class="cell cellName">
                        Nombre
                    </span>
                    <span class="cell cellButton">
                        Agregar
                    </span>
                </div>
                <div id="searchResultList" class="rowResultContainer mostly-customized-scrollbar" style="height: calc(100% - 29px);">
                cargando
                </div>
            </div>
            
            <div class="addedList">
                <span>Proveedores Agregados</span>
                <div id="yanosequenombreponer" class="rowResultContainer mostly-customized-scrollbar" style="height: calc(100% - 20px);"></div>
            </div>
        </div>

        <input hidden type="text" name="providers" id="entityAddedOnList" value="<?php echo $product->getProvidersID();?>">
        
        
        <button hidden id="sendFormButton">enviar form</button>
        <div class="coolFinalButtons">
            <a href="../../Productos/Producto/?id=<?php echo $product->getId();?>" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </form>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Productos.png">
                <b>Productos</b>
            </div>
            <a href="../../Productos/Producto/?id=<?php echo $product->getId();?>" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>

    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
    <script src="providerAddingList.js"></script>
</body>