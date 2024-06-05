<?php
    if(empty($_SESSION)){
        session_start();
    }
    
    if(!isset($_SESSION["nombreDeUsuario"])){
        header('Location: login.php');
    }else{
        include_once('clases.php');
        $BaseDeDatos = new conexion();

        $Usuario = unserialize($_SESSION["UsuarioLogeado"]);
        $DatosDelUsuario = $Usuario->ObtenerDatos();
    }
?>
    <link rel="stylesheet" href="Otros/colores.css?<?php echo rand();?>">
    <link rel="stylesheet" href="Otros/estilos_cabecera.css">
    <link href="Iconos/css/uicons-solid-rounded.css" rel="stylesheet">
    <link href="Iconos/css/uicons-regular-rounded.css" rel="stylesheet">
    <link href="Imagenes/Logo.png" rel="shortcut icon" >
</head>
<body>
    <div id="TopLane">
        <div id="FiguraFondo">
            <img src="Imagenes/Logo.png" alt="">
        </div>
        <h1><?php echo $GLOBALS['nombreDelSoftware'];?></h1>
    </div>
    <div id="BarraSuperior">
        <div class="EspacioDelBotonPaVolverAlMenu">
            <input hidden type="checkbox" name="" id="MostrarBotonOcultoPalMenu">    
            <a title="Ir al menú" class="BotonOcultoPalMenu" href="index.php"> <i class="fi-sr-home"></i> <?php echo $GLOBALS['nombreDelSoftware'];?></a>
            <script src="Otros/EventosGlobales.js"></script>
        </div>
        <nav class="EspacioDeLosBotonesDelNav">
            <a href="Otros/funcion_CerrarSesion.php">Salir <i class="fi-sr-exit"></i></a>
            <a href="Ayuda">Ayuda  <i class="fi-rr-interrogation"></i></a>
            <a href="Perfil.php?pagina=1"><?php echo $DatosDelUsuario['nombres'].' ('.$DatosDelUsuario['nivelDeUsuario'].')';?> <i class="fi-sr-user"></i></a>
        </nav>
        
    </div>
    