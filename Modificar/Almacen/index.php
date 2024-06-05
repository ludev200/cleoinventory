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
    $store = new store($_GET['id']);
}else{
    header('Location: ../../error.php');
}

if($_POST){
    try{
        print_r($store->updateData($_POST));
        header('Location: ../../Almacenes/Almacen/?id='.$store->getId());
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
    <title><?php echo $GLOBALS['nombreCorto'];?>: Modificar Almacén</title>


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
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../../Almacenes" class="Barra">
            <p>Almacenes</p>
            <div class="Cuadrito" href="Almacenes"></div>
        </a>
        <a href="../../Almacenes/Almacen/?id=<?php echo $store->getId();?>" class="Barra">
            <p><?php echo $store->getName();?></p>
            <div class="Cuadrito" href="../"></div>
        </a>
        <a href="" class="Barra">
            <p>Modificar</p>
            <div class="Cuadrito" href=""></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    
    <form method="post" autocomplete="off">
        <h2 class="SubtituloCentral">
            <i class="fi-sr-clipboard-list Arreglito"></i>
            INFORMACIÓN
        </h2>
        <div class="inputsContainer">
            <b>ID:</b>
            <input type="text" id="idInput" disabled value="<?php echo $store->getId();?>" style="width: 100px">
            <b>Nombre:</b>
            <input type="text" name="name" id="nameInput" style="width: 400px" value="<?php echo $store->getName();?>">
            <b>Dirección:</b>
            <input type="text" name="address" id="addressInput" style="width: 400px" value="<?php echo $store->getAddress();?>">
        </div>
        <div class="nota">
            <p>Dirigáse a inventario para modificar el inventario actual de este almacén.</p>
            <a href="../../Inventario">Ir a inventario</a>
        </div>
        
        <button id="sendFormButton" hidden>Guardar ahora si</button>
        <div class="coolFinalButtons">
            <a href="../../Almacenes/Almacen/?id=<?php echo $store->getId();?>" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </form>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Almacenes.png">
                <b>Almacenes</b>
            </div>
            <a href="../../Almacenes/Almacen/?id=<?php echo $store->getId();?>" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>

    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>    
</body>
</html>