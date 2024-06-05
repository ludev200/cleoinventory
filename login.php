<?php
include('Otros/clases.php');
$BaseDeDatos = new conexion();

$txtUsuario="";
$txtContrasenia="";
$ErrorDeInicio ="";

if($_POST){
    $txtUsuario=(isset($_POST['nombreDeUsuario']))?$_POST['nombreDeUsuario']:"" ;
    $txtContrasenia=(isset($_POST['contrasenia']))?$_POST['contrasenia']:"";

    if(!empty($_POST['nombreDeUsuario'])){
        try{
            $Usuario = new usuario($_POST['nombreDeUsuario']);
            if($Usuario->IniciarSesion($_POST['contrasenia'])){
                $publicFunctions = new publicFunctions();
                $publicFunctions->checkBudgetsAndPurchase();
                header('Location: index.php');
            }else{
                throw new Exception("No se pudo inciar sesión");
            }
        }catch(Exception $e){
            $ErrorDeInicio = $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Otros/colores.css?<?php echo rand();?>">
    <link rel="stylesheet" href="estilos_login.css">
    <link href="Imagenes/Logo.png" rel="shortcut icon" >
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Iniciar sesión</title>
    <link href="Iconos/css/uicons-solid-rounded.css" rel="stylesheet">
    <link href="Iconos/css/uicons-regular-rounded.css" rel="stylesheet">
</head>
<body>
    <label id="VentanaDeErrores" <?php echo (empty($ErrorDeInicio)?"hidden":"")?> class="modal">
        <input hidden <?php echo (empty($ErrorDeInicio)?"":"checked")?> type="checkbox" name="" id="VisibilidadModal">
        <div class="CentroHorizontal">
            <div class="CentroVertical">
                <div class="CuerpoDeModal">
                    <img class="TextoCentro" src="Imagenes/CirculoDeError.png" alt="">
                    <b class="TextoCentro">No se puede inciar sesión</b>
                    <p><?php echo (empty($ErrorDeInicio)?"":$ErrorDeInicio)?>.</p>
                </div>
            </div>
        </div>
    </label>
    <h1><?php echo $GLOBALS['nombreDelSoftware'];?></h1>
    
    <div id="divLogo">
        <div id="semicirculo"></div>
        <img src="Imagenes/Logo.png" alt="">
    </div>
    <div id="divLogin">
        
        <form action="login.php" method="post" autocomplete="off">
            <h2>INICIAR SESIÓN</h2>
            <p>Usuario:</p>
            <input required class="CampoDeTexto" type="text" name="nombreDeUsuario" value="<?php echo $txtUsuario?>">
            <p>Contraseña:</p>
            <input required class="CampoDeTexto" type="password" name="contrasenia" value="<?php echo $txtContrasenia?>">
            <input id="BotonEntrar" type="submit" value="Entrar">
            <p class="recuperar">¿Has olvidado tu contraseña?</p>
            <a class="recuperar" id="resetPassword">Restablecer contraseña</a>
            

        </form>
    </div>
    <div class="resetPassword_modal" style="">
        <div class="modalWindowContainer OcultarModal">
            <div class="modalWindowBody">
                <button title="Cerrar ventana" class="closeModal"><i style="display: flex;" class="fi-rr-cross-small"></i></button>
                <b class="TituloDeModal">RESTABLECER CONTRASEÑA</b>
                <div class="content">
                    <p style="text-align: center;">Si has olvidado tu contraseña, puedes restablecerla respondiendo correctamente tus preguntas de seguridad. Completa los siguientes pasos.</p>
                    <div class="stepTitle"> <span class="stepNumber">1</span> Indica tu nombre de usuario:</div>
                    <div class="stepContent">
                        <input id="searchUser_input" type="text" class="CampoDeTexto" autocomplete="off">
                        <button id="searchUser"><i class="fi-rr-search"></i></button>
                    </div>

                    <div class="stepTitle step2component"> <span class="stepNumber">2</span> Responde tus preguntas de seguridad:</div>
                    <div class="stepContent step2component">
                        <div class="question">
                            <small>¿adskjaksjdas?</small>
                            <input type="text" class="CampoDeTexto">
                        </div>
                        <div class="question">
                            <small>¿adskjaksjdas?</small>
                            <input type="text" class="CampoDeTexto">
                        </div>
                        <div class="question">
                            <small>¿adskjaksjdas?</small>
                            <input type="text" class="CampoDeTexto">
                        </div>
                        
                    </div>
                    <button class="step2component checkAnswers" id="checkAnswers">Comprobar respuestas</button>

                    <div class="stepTitle step3component"> <span class="stepNumber">3</span> Indica tu nueva contraseña:</div>
                    <div class="stepContent step3component" >
                        <div class="question" >
                            <small>Nueva contraseña</small>
                            <input id="newPassword_input" type="password" class="CampoDeTexto">
                        </div>
                        <div class="question">
                            <small>Comprobar contraseña</small>
                            <input id="checkPassword_input" type="password" class="CampoDeTexto">
                        </div>
                    </div>
                    <button id="savePassword" class="step3component checkAnswers">Guardar y Entrar</button>
                </div>
            </div>
        </div>
    </div>
    <?php include 'ipserver.php';?>
    <script src="Otros/sweetalert.js"></script>
    <script src="login.js"></script>
</body>
</html>