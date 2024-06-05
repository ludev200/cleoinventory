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
    $userConnected = new user($DatosDelUsuario['nombreDeusuario']);
}

if($_POST){
    
    try{
        $createdID = $userConnected->createProduct($_POST, $_FILES);
        header("Location: ../Producto/?id=$createdID");
    }catch(Exception $error){
        $SWAlertMessage = $error->getMessage();
    }
}


$units = $BaseDeDatos->consultar("SELECT * FROM `unidadesdemedida` WHERE `id` > 1");


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $GLOBALS['nombreCorto'];?>: Nuevo producto</title>
    <link rel="stylesheet" href="estilo.css">
    <?php include('../../Otros/cabecera_N3.php');?>
    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Productos" class="Barra">
            <p>Productos</p>
            <div class="Cuadrito"></div>
        </a>
        <a class="Barra">
            <p>Nuevo</p>
            <div class="Cuadrito" href="../"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>


    <form method="post" autocomplete="off" enctype="multipart/form-data">
        <span class="SubtituloCentral"><i class="fi-sr-clipboard-list Arreglito"></i> INFORMACIÓN:</span>
        <div class="imgAndId">
            <label title="Cambiar imagen">
                <img src="../../Imagenes/Productos/ImagenPredefinida_Productos.png" alt="">
                <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" hidden="">
            </label>
            <div class="inputsContainer">
                <b>Nombre:</b>
                <input type="text" id="name" name="name" value="" maxlength="30">
                <br>
                <b>Precio:</b>
                <div id="sufixContainer">
                    <input type="text" onblur="priceFormat(this)" onkeypress="return validate_4floatNumber(this, event)" id="priceInput" name="price" value="" placeholder="0.00">
                    <b>$</b>
                </div>
            </div>
        </div>

        <span class="SubtituloCentral" style="margin-top: 20px;"><i class="fi-sr-list Arreglito"></i> DETALLES:</span>
        <div class="inputsContainer">
            <div style="margin-bottom: 10px;">
                <b>Categoría:</b> <a href="../../Ayuda/#indice1" target="_blank" class="AyudaEnNavegacion"><i class="fi-sr-interrogation"></i> <span>Ir a información de las categorías</span></a>
            </div>
            <select name="idCategory" id="categoryInput" style="width: 200px">
                <option value="1" selected="">Material</option>
                <option value="2">Equipo</option>
                <option value="3">Mano de Obra</option>
                <option value="4">Comida</option>
            </select>

            <b class="hideOnWorkHand">Unidad de medida:</b>
            <select name="idUnit" id="unitInput" style="width: 200px" class="hideOnWorkHand">
            <?php
                foreach($units as $row){
                    echo '<option value="'.$row['id'].'" simbol="'.$row['simbolo'].'">'.$row['nombre'].'</option>';
                }
            ?>
            </select>

            <b class="hideOnWorkHand">Nivel de alerta:</b>
            <div id="sufixContainer" class="hideOnWorkHand">
                <input type="text" name="alertLevel" onkeypress="return validate_intNumber(this, event)" id="alertLevelInput" value="" placeholder="0" maxlength="9" onfocus="this.select()">
                <b id="unitSimbol">m</b>
            </div>

            <b class="hideOnWorkHand showForEquipment" style="display: none;">Depreciación estándar:</b>
            <div class="hideOnWorkHand sufixContainer showForEquipment"  style="display: none;">
                <input name="deafultSpoilage" maxlength="6" type="text" id="deafultSpoilage" onblur="spoilageFormat(this)" placeholder="0" onfocus="this.select()" onkeypress="return validate_4floatNumber(this, event)">
                <b id="unitSimbol">u</b>
            </div>

            <div style="margin-bottom: 10px;">
                <b>Descripción:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <textarea onkeypress="return validateNoHashtag(this, event)" name="description" id="descriptionInput" cols="30" rows="10" placeholder="Puedes añadir una descripción del producto" maxlength="150"></textarea>
        </div>

        <span class="SubtituloCentral hideOnWorkHand" style="margin-top: 20px;"><i class="fi-sr-users Arreglito"></i> PROVEEDORES VINCULADOS:</span>
        <input HIDDEN type="text" name="providers" id="entityAddedOnListInput">
        <div class="searchForm hideOnWorkHand">
            <input type="text" id="searchValueInput" placeholder="Buscar" maxlength="20" form="ningunoxd">
            <button type="button" id="searchValueButton"><i class="fi-rr-search"></i></button>
        </div>
        <div class="twinContainer hideOnWorkHand">
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
                    <div class="did_loading">
                        <div class="rotating"><span class="fi fi-rr-loading"></span></div>Cargando
                    </div>
                </div>
            </div>
            
            <div class="addedList">
                <span>Proveedores Agregados</span>
                <div id="yanosequenombreponer" class="rowResultContainer mostly-customized-scrollbar" style="height: calc(100% - 20px);"></div>
            </div>
        </div>

        <div class="coolFinalButtons">
            <a href="../" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </form>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Productos.png">
                <b>Productos</b>
            </div>
            <a href="../../Ayuda/#5" target="_blank"><i class="fi-rr-interrogation"></i> Obtener información</a>
            <a href="../" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>

    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="script.js"></script>
</body>
</html>