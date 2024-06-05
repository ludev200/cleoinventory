<?php
    if(empty($_SESSION)){
        session_start();
    }
    
    if(!isset($_SESSION["nombreDeUsuario"])){
        header('Location: ../../../login.php');
    }else{
        include_once('clases.php');
        $BaseDeDatos = new conexion();
        $Usuario = unserialize($_SESSION["UsuarioLogeado"]);
        print_r($_SERVER['PHP_SELF']);
        print_r($Usuario->MostrarListaDePermisos());
        if(!$Usuario->VerificarPermisoSegunModulo(explode('/',$_SERVER['PHP_SELF'])[2])){
            //header('Location: ../../Error.php?error=401&desc=9');
        }
        $Usuario = unserialize($_SESSION["UsuarioLogeado"]);
        $DatosDelUsuario = $Usuario->ObtenerDatos();
    }
?>
    <link rel="stylesheet" href="../../../Otros/colores.css?<?php echo rand();?>">
    <link rel="stylesheet" href="../../../Otros/estilos_cabecera.css?<?php echo rand();?>">
    <link href="../../../Iconos/css/uicons-solid-rounded.css" rel="stylesheet">
    <link href="../../../Iconos/css/uicons-regular-rounded.css" rel="stylesheet">
    <link href="../../../Imagenes/Logo.png" rel="shortcut icon" >
</head>
<body>
    <div href="#TopLane" id="TopLane">
        <div id="FiguraFondo">
            <img src="../../../Imagenes/Logo.png" alt="">
        </div>
        <h1><?php echo $GLOBALS['nombreDelSoftware'];?></h1>
    </div>
    <div id="BarraSuperior">
        <div class="EspacioDelBotonPaVolverAlMenu">
            <input hidden type="checkbox" name="" id="MostrarBotonOcultoPalMenu">    
            <a class="BotonOcultoPalMenu" href="../../../index.php"> <i class="fi-sr-cube"></i> <?php echo $GLOBALS['nombreDelSoftware'];?></a>
            <script src="../../../Otros/EventosGlobales.js"></script>
        </div>
        <nav class="EspacioDeLosBotonesDelNav">
            <a href="../../../Otros/funcion_CerrarSesion.php">Salir <i class="fi-sr-exit"></i></a>
            <a href="Ayuda">Ayuda  <i class="fi-rr-interrogation"></i></a>
            <a href="../../../Perfil.php?pagina=1"><?php echo $DatosDelUsuario['nombres'].' ('.$DatosDelUsuario['nivelDeUsuario'].')';?> <i class="fi-sr-user"></i></a>
            <a href="">aaaaa  <i class="fi-sr-bell"></i></a>
        </nav>
    </div>
    