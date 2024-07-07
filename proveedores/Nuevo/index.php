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
        $createdID = $userConnected->createProvider($_POST, $_FILES);
        header("Location: ../Proveedor/?rif=$createdID");
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
    <title><?php echo $GLOBALS['nombreCorto'];?>: Nuevo proveedor</title>
    <link rel="stylesheet" href="estilo.css">
    <?php include('../../Otros/cabecera_N3.php');?>
    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Proveedores" class="Barra">
            <p>Proveedores</p>
            <div class="Cuadrito"></div>
        </a>
        <a class="Barra">
            <p>Nuevo</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>

    <form method="post" autocomplete="off" enctype="multipart/form-data">
        <span class="SubtituloCentral"><i class="fi-sr-clipboard-list Arreglito"></i> INFORMACIÓN PERSONAL:</span>
        <div class="imgAndId">
            <label title="Cambiar imagen">
                <img src="../../Imagenes/Proveedores/ImagenPredefinida_Proveedores.png" alt="">
                <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" hidden="">
            </label>
            <div class="inputsContainer">
                <b>RIF o Cédula:</b>
                <div class="gap10">
                    <select name="docType" id="docType">
                        <option value="V">V</option>
                        <option value="J">J</option>
                        <option value="G">G</option>
                        <option value="E">E</option>
                        <option value="P">P</option>
                    </select>
                    <input type="text" id="idEntity" onkeypress="return validateRifAndCedula(event, this)" name="cedula">
                </div>
                <br>
                <b>Nombre o Razón Social</b>
                <input id="name" maxlength="40" name="name" type="text">
            </div>
        </div>

        <span class="SubtituloCentral"><i class="fi-sr-circle-phone Arreglito"></i> INFORMACIÓN DE CONTACTO:</span>
        <div class="inputsContainer">
            <div style="margin-bottom: 10px;">
                <b>Telefono 1:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="phone" id="phoneInput" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="">
            <div style="margin-bottom: 10px;">
                <b>Telefono 2:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="phone2" id="phone2Input" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="">
            <div style="margin-bottom: 10px;">
                <b>Correo:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="email" id="emailInput" maxlength="50" type="email" style="width: 400px;" value="">
            <div style="margin-bottom: 10px;">
                <b>Dirección:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="address" id="addressInput" maxlength="60" type="text" style="width: 400px;" value="">
        </div>

        <span class="SubtituloCentral"><i class="fi-sr-users Arreglito"></i> PRODUCTOS VINCULADOS:</span>
        <input HIDDEN type="text" id="entityAddedOnListInput" placeholder="Productos vinculados" name="productsList">
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
                    <div class="did_loading">
                        <div class="rotating"><span class="fi fi-rr-loading"></span></div>Cargando
                    </div>
                </div>
            </div>
            
            <div class="addedList">
                <span>Productos Agregados</span>
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
                <img src="../../Imagenes/iconoDelMenu_Proveedores.png">
                <b>Proveedores</b>
            </div>
            <a href="../../Ayuda/#54" target="_blank"><i class="fi-rr-interrogation"></i> Obtener ayuda</a>
            <a href="../" id="" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>

    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="script.js"></script>
</body>
</html>