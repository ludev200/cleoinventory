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
    $user = new user($_GET['id']);
}else{
    header('Location: ../../error.php');
}


if($_POST){
    try{
        
        $result = $user->updateData($_POST);
        print_r($result);
        header('Location: ../../Usuarios/Usuario/?id='.$user->getUsername());
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
    <title><?php echo $GLOBALS['nombreCorto'];?>: Modificar Usuario</title>


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
        <a href="../../Usuarios" class="Barra">
            <p>Usuarios</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../../Usuarios/Usuario/?id=<?php echo $user->getUsername();?>" class="Barra">
            <p><?php echo $user->getName();?></p>
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
    
    <form method="post" autocomplete="off" enctype="multipart/form-data" id="form">
        <h2 class="SubtituloCentral">
            <i class="fi-sr-clipboard-list Arreglito"></i>
            DATOS PERSONAL
        </h2>

        <ul class="personales">
            <li>
                <b>Cédula</b>
                <div class="CajaPalInput">
                    <select name="docType" id="docTypeSelector">
                        <option <?php echo ($user->getDocType()=='V'? 'selected':'');?> value="V">V</option>
                        <option <?php echo ($user->getDocType()=='J'? 'selected':'');?> value="J">J</option>
                        <option <?php echo ($user->getDocType()=='E'? 'selected':'');?> value="E">E</option>
                        <option <?php echo ($user->getDocType()=='G'? 'selected':'');?> value="G">G</option>
                        <option <?php echo ($user->getDocType()=='P'? 'selected':'');?> value="P">P</option>
                    </select>
                    <input type="number" class="noarrows" name="cedula" value="<?php echo $user->getCedula();?>" id="cedulaInput">
                </div>
            </li>
            <li>
                <b>Nombre y apellido</b>
                <div class="CajaPalInput">
                    <input type="text" name="name" maxlength="50" value="<?php echo $user->getName();?>" id="nameInput">
                </div>
            </li>
            <li>
                <b>Sexo</b>
                <div class="CajaPalInput">
                    <select name="sex" id="sexSelector">
                        <option <?php echo ($user->getSex()=='M'? 'selected':'');?> value="M">Masculino</option>
                        <option <?php echo ($user->getSex()=='F'? 'selected':'');?> value="F">Femenino</option>
                    </select>
                </div>
            </li>
        </ul>

        <h2 class="SubtituloCentral">
            <i class="fi-sr-shield Arreglito"></i>
            DATOS DE LA CUENTA Y SEGURIDAD
        </h2>
        <ul class="cuenta">
            <li>
                <b>Nombre de usuario</b>
                <div class="CajaPalInput">
                    <input disabled type="text" maxlength="30" value="<?php echo $user->getUsername();?>" id="usernameInput">
                </div>
            </li>
            <li>
                <b>Contraseña</b>
                <div class="CajaPalInput">
                    <input type="password" name="password" maxlength="20" placeholder="**********" id="passwordInput">
                </div>
            </li>
            <li>
                <b>Pregunta de recuperación 1</b>
                <div class="CajaPalInput CajaDePregRecup">
                    <select disabled>
                        <option value="as">¿asdasd?</option>
                    </select>
                    <input type="text" maxlength="20" value="*******" disabled>
                </div>
            </li>
            <li>
                <b>Pregunta de recuperación 2</b>
                <div class="CajaPalInput CajaDePregRecup">
                    <select disabled>
                        <option value="as">¿asdasd?</option>
                    </select>
                    <input type="text" maxlength="20" value="*******" disabled>
                </div>
            </li>
            <li>
                <b>Pregunta de recuperación 3</b>
                <div class="CajaPalInput CajaDePregRecup">
                    <select disabled>
                        <option value="as">¿asdasd?</option>
                    </select>
                    <input type="text" maxlength="20" value="*******" disabled>
                </div>
            </li>
            <small>Nota: Las preguntas y respuestas de seguridad solo pueden ser modificadas por el usuario mismo.</small>
        </ul>

        <h2 class="SubtituloCentral">
            <i class="fi-sr-user Arreglito"></i>
            PERFIL DE USUARIO
        </h2>
        <ul class="seguridad">
            <li>
                <div class="CajaPalInput">
                    <b>Perfil de usuario:</b>
                    <select name="profileLevel" id="profileselector">
                        <option <?php echo ($user->getIdUserLevel()=='3'? 'selected':'');?> value="3">Analista</option>
                        <option <?php echo ($user->getIdUserLevel()=='2'? 'selected':'');?> value="2">Contador</option>
                        <option <?php echo ($user->getIdUserLevel()=='1'? 'selected':'');?> value="1">Administrador</option>
                    </select>
                </div>
            </li>
        </ul>
        
        <b>Permisos:</b>
        <div class="permisos">
            <?php
            $search = $BaseDeDatos->consultar("SELECT * FROM `modulos`");
            foreach($search as $row){

                $permisos = '';

                if(!empty($BaseDeDatos->consultar("SELECT * FROM `modulospredeterminados` WHERE `idModulo` = ".$row['id']." AND `idNivelDeUsuario` = 1;"))){
                    $permisos.= ' lvl1';
                }
                if(!empty($BaseDeDatos->consultar("SELECT * FROM `modulospredeterminados` WHERE `idModulo` = ".$row['id']." AND `idNivelDeUsuario` = 2;"))){
                    $permisos.= ' lvl2';
                }
                if(!empty($BaseDeDatos->consultar("SELECT * FROM `modulospredeterminados` WHERE `idModulo` = ".$row['id']." AND `idNivelDeUsuario` = 3;"))){
                    $permisos.= ' lvl3';
                }

                echo '<label>
                    <input '.($user->isModuloAble($row['id'])? 'checked':'').' hidden="" type="checkbox" name="permiso-'.$row['nombre'].'" class="checkboxmodulo '.$permisos.'">
                    <div class="modulocard">
                        <img src="../../imagenes/'.$row['nombreDeImagen'].'" alt="">
                        <span>'.$row['nombre'].'</span>
                    </div>
                </label>';
            }
            ?>




                              
        </div>
        <div class="coolFinalButtons">
            <a href="../../Usuarios/Usuario/?id=<?php echo $user->getUsername();?>" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </form>

    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Usuario.png">
                <b>Usuarios</b>
            </div>
            <a href="../../Usuarios/Usuario/?id=<?php echo $user->getUsername();?>" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>

    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
</body>