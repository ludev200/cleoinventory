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


if(isset($_GET['rif'])){
    $provider = new provider($_GET['rif']);
}else{
    header('Location: ../../error.php');
}


if($_POST){
    try{
        print_r($_POST);
        $result = $provider->updateData($_POST, $_FILES);
        header('Location: ../../Proveedores/Proveedor/?rif='.$provider->getId());
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
    <title><?php echo $GLOBALS['nombreCorto'];?>: Modificar Proveedor</title>


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
        <a href="../../Proveedores" class="Barra">
            <p>Proveedores</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Proveedores/Proveedor/?rif=<?php echo $provider->getId();?>" class="Barra">
            <p><?php echo $provider->getName();?></p>
            <div class="Cuadrito" href="../"></div>
        </a>
        <a class="Barra">
            <p>Modificar</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>

    <form method="post" autocomplete="off" enctype="multipart/form-data">
        <h2 class="SubtituloCentral">
            <i class="fi-sr-clipboard-list Arreglito"></i>
            INFORMACIÓN PERSONAL
        </h2>
        <div class="imgAndId">
            <label title="Cambiar imagen">
                <img src="../../Imagenes/Proveedores/<?php echo empty($provider->getImg())? 'ImagenPredefinida_Proveedores.png':$provider->getImg();?>" alt="">
                <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" HIDDEN>
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
                    <input disabled type="text" id="idEntity" value="<?php echo $provider->getId();?>">
                </div>
                <br>
                <b>Nombre o Razón Social</b>
                <input id="name" maxlength="40" name="name" type="text" value="<?php echo $provider->getName();?>">
            </div>
        </div>

        <h2 class="SubtituloCentral">
            <i class="fi-sr-circle-phone Arreglito"></i>
            INFORMACIÓN DE CONTACTO
        </h2>
        <div class="inputsContainer">
            <div style="margin-bottom: 10px;">
                <b>Telefono 1:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="phone" id="phoneInput" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="<?php echo $provider->getPhone();?>">
            <div style="margin-bottom: 10px;">
                <b>Telefono 2:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="phone2" id="phone2Input" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="<?php echo $provider->getPhone2();?>">
            <div style="margin-bottom: 10px;">
                <b>Correo:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="email" id="emailInput" maxlength="50" type="email" style="width: 400px;" value="<?php echo $provider->getEmail();?>">
            <div style="margin-bottom: 10px;">
                <b>Dirección:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input name="address" id="addressInput" maxlength="60" type="text" style="width: 400px;" value="<?php echo $provider->getAddress();?>">
        </div>




        <h2 class="SubtituloCentral">
            <i class="fi-sr-users Arreglito"></i>
            PRODUCTOS
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
                </div>
            </div>
            
            <div class="addedList">
                <span>Productos Agregados</span>
                <div id="yanosequenombreponer" class="rowResultContainer mostly-customized-scrollbar" style="height: calc(100% - 20px);"></div>
            </div>
        </div>

        <input hidden type="text" name="products" id="entityAddedOnList" value="<?php echo $provider->getProductsID();?>">
        <div class="coolFinalButtons">
            <a href="../../Proveedores/Proveedor/?rif=<?php echo $provider->getId();?>" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
        <button hidden id="sendFormButton">enviar form</button>
    </form>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Proveedores.png">
                <b>Proveedores</b>
            </div>
            <a href="../../Proveedores/Proveedor/?rif=<?php echo $provider->getId();?>" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>


    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
    <script src="providerAddingList.js"></script>
</body>