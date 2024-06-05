<?php
include('../Otros/clases.php');
if(empty($_SESSION)){
    session_start();
}

if(!isset($_SESSION["nombreDeUsuario"])){
    header('Location: ../login.php');
}else{
    $BaseDeDatos = new conexion();
    $Usuario = unserialize($_SESSION["UsuarioLogeado"]);
    $DatosDelUsuario = $Usuario->ObtenerDatos();
    
}


$publicFunctions = new publicFunctions();
$user = new user($_SESSION["nombreDeUsuario"]);
$modulesID = $user->getModulesID();


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $GLOBALS['nombreCorto'];?>: Manual de usuario</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../Otros/colores.css?<?php echo rand();?>">
    <link rel="stylesheet" href="../Otros/estilos_cabecera.css?<?php echo rand();?>">
    <link href="../Iconos/css/uicons-solid-rounded.css" rel="stylesheet">
    <link href="../Iconos/css/uicons-regular-rounded.css" rel="stylesheet">
    <link href="../Imagenes/Logo.png" rel="shortcut icon" >
</head>
<body>
    <div href="#TopLane" id="TopLane">
        <div id="FiguraFondo">
            <img src="../Imagenes/Logo.png" alt="">
        </div>
        <h1><?php echo $GLOBALS['nombreDelSoftware'];?></h1>
    </div>
    <div id="BarraSuperior">
        <div class="EspacioDelBotonPaVolverAlMenu">
            <input hidden type="checkbox" name="" id="MostrarBotonOcultoPalMenu">    
            <a class="BotonOcultoPalMenu" href="../index.php"> <i class="fi-sr-home"></i> <?php echo $GLOBALS['nombreDelSoftware'];?></a>
            <script src="../Otros/EventosGlobales.js"></script>
        </div>
        <nav class="EspacioDeLosBotonesDelNav">
            <a href="../Otros/funcion_CerrarSesion.php">Salir <i class="fi-sr-exit"></i></a>
            <a href="../Ayuda">Ayuda  <i class="fi-rr-interrogation"></i></a>
            <a href="../Perfil.php?pagina=1"><?php echo $DatosDelUsuario['nombres'].' ('.$DatosDelUsuario['nivelDeUsuario'].')';?> <i class="fi-sr-user"></i></a>
        </nav>
    </div>
    <section>
        <div class="asideContainer mostly-customized-scrollbar">
            <aside>
                <a href="#Buscador" class="optionSimple">
                    <span class="fi-rr-home"></span>
                    Inicio
                </a>
                <?php
                foreach($publicFunctions->getManualIndexTitles() as $row){
                    $isAble = in_array($row['id'], $modulesID);
                    $childs = '';
                    $subtitles = $publicFunctions->getSubtitlesFor($row['id']);
                    if(!empty($subtitles)){
                        foreach($subtitles as $row2){
                            $childs.= '<span><a href="#indice'.$row2['id'].'">'.$row2['name'].'</a></span>';
                        }
                    }
                    
                    echo '<div id="optionParent-'.$row['id'].'" class="optionParent '.($isAble? '':'disabled').'">
                        <div class="optionName">
                            <span class="fi-rr-'.($isAble? 'arrows':'lock').'"></span>
                            <a '.($isAble? 'href="#titulo'.$row['id'].'"':'').'>'.$row['name'].'</a>
                        </div>
                        <div class="childs">
                            '.$childs.'
                        </div>
                    </div>';
                }
                
                if($user->getIdUserLevel() == 1){
                    echo '<div id="optionParent-x" class="optionParent" hidden>
                        <div class="optionName">
                            <span class="fi-rr-arrows"></span>
                            <a href="">Recomendaciones</a>
                        </div>
                        <div class="childs">
                            <span>tal</span>
                            <span>tal</span>
                            <span>tal</span>
                            <span>tal</span>
                        </div>
                    </div>';
                }
                ?>
                
                
            </aside>
        </div>
        <article>
            <section id="Buscador" class="searchSection">
                <h2 >Manual de usuario</h2>
                <p>
                    <small>Fecha: 07 de marzo de 2024</small>
                    Este manual de usuario tiene como finalidad documentar todas las funcionalidades y capacidades del sistema de inventario
                    y cotización CLEO INVENTORY. Brindando una herramienta de alta disponibilidad, rápida 
                    y fácil de usar que funcione como guía o asistente y orientando así a los usuarios en la correcta utilización, gestión y mantenimiento
                    del mismo. Utiliza el menu del lado izquierdo para navegar en los módulos en los que tengas acceso y sus contenidos, o utiliza
                    la barra de busqueda para ir directamente al contenido que necesitas.
                    <br>
                    <small>Versión: 1.1</small> 
                </p>
                <div class="searchContainer">
                    <label class="lupitaContainer">
                        <input id="helpInput" type="text" placeholder="¿En qué necesitas ayuda?" username="<?php echo $_SESSION["nombreDeUsuario"];?>">
                        <span  class="fi-rr-search lupita"></span>
                    </label>
                    <div id="resultsContainer" class="resultsContainer close">
                        <a href="#a" hidden>
                            <span class="fi-rr-interrogation"></span>
                            como respirar
                        </a>
                        <p>No encontramos ningun resultado para tu busqueda <span class="fi-rr-sad"></span></p>
                    </div>
                </div>
            </section>
            <?php
                foreach($publicFunctions->getManualIndexTitles() as $row){
                    if(in_array($row['id'], $modulesID)){
                        include('pageModule'.$row['id'].'.php');
                    }
                }
            ?>
        </article>
    </section>

    <?php include '../ipserver.php';?>
    <script src="../Otros/sweetalert.js"></script>
    <script src="script.js"></script>
</body>
</html>