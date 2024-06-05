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
    $customer = new customer($_GET['id']);
}else{
    header('Location: ../../error.php');
}

if($_POST){
    try{
        print_r($customer->updateData($_POST, $_FILES));
        header('Location: ../../Clientes/Cliente/?rif='.$customer->getId());
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
    <title><?php echo $GLOBALS['nombreCorto'];?>: Modificar Cliente</title>


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
            <input hidden type="checkbox" id="MostrarBotonOcultoPalMenu">    
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
        <a href="../../Clientes" class="Barra">
            <p>Clientes</p>
            <div class="Cuadrito" href="Clientes"></div>
        </a>
        <a href="../../Clientes/Cliente/?rif=<?php echo $customer->getId();?>" class="Barra">
            <p><?php echo $customer->getName();?></p>
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

    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <h2 class="SubtituloCentral">
            <i class="fi-sr-clipboard-list Arreglito"></i>
            INFORMACIÓN PERSONAL
        </h2>
        <div class="imgAndId">
            <label title="Cambiar imagen">
                <img src="../../Imagenes/Clientes/<?php echo empty($customer->getImg())? 'ImagenPredefinida_Clientes.png':$customer->getImg();?>" alt="">
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
                    <input disabled type="text" value="<?php echo $customer->getId();?>" id="idEntity">
                </div>
                <br>
                <b>Nombre o Razón Social</b>
                <input id="name" maxlength="40" name="name" type="text" value="<?php echo $customer->getName();?>">
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
            <input id="phone_input" name="phone" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="<?php echo $customer->getPhone();?>">
            <div style="margin-bottom: 10px;">
                <b>Telefono 2:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input id="phone2_input" name="phone2" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="<?php echo $customer->getPhone2();?>">
            <div style="margin-bottom: 10px;">
                <b>Correo:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input id="email_input" name="email" maxlength="50" type="email" style="width: 400px;" value="<?php echo $customer->getEmail();?>">
            <div style="margin-bottom: 10px;">
            <b>Dirección:</b> <small style="color: gray;">(Opcional)</small>
            </div>
            <input id="address_input" name="address" maxlength="60" type="text" style="width: 400px;" value="<?php echo $customer->getAddress();?>">
        </div>
        <div class="buttonsContainer">
            <a href="../../Clientes/Cliente/?rif=<?php echo $customer->getId();?>" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow" type="button">Guardar</button>
        </div>
        <button id="sendFormButton" hidden>Guardar ahora si</button>
    </form>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Clientes.png">
                <b>Clientes</b>
            </div>
            <a href="../../Clientes/Cliente/?rif=<?php echo $customer->getId();?>" id="AgregarNuevo" href="Cambios"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
        </div>
    </aside>


    <?php if(isset($SWAlertMessage)){echo '<div id="SWAlert" hidden>'.$SWAlertMessage.'</div>';}?>

    

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="script.js"></script>    
</body>
</html>