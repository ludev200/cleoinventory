<?php 
    include('clases.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Algo ha salido mal :(</title>
    <link rel="stylesheet" href="../estilos_index.css">
    <link rel="stylesheet" href="../Otros/estilos_cabecera.css">
    <link href="../Iconos/css/uicons-solid-rounded.css" rel="stylesheet">
    <link href="../Iconos/css/uicons-regular-rounded.css" rel="stylesheet">
    <meta charset="UTF-8">
</head>
<body>
    <div id="TopLane">
        <div id="FiguraFondo">
            <img src="../Imagenes/Logo.png" alt="">
        </div>
        <h1>Nombre</h1>
    </div>
    <div id="BarraSuperior">
        <a href="../Otros/funcion_CerrarSesion.php">Salir <i class="fi-sr-exit"></i></a>
        <a href="">Perfil  <i class="fi-sr-user"></i></a>
        <a href="">Notificaciones  <i class="fi-sr-bell"></i></a>
    </div>
    <div id="CajaDeBarras">
        <a href="../index.php" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>       
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <div id="CajaContenido">
    <?php
        
        $BaseDeDatos = new conexion();
        $ListaDeProductosEnBorrador = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE `idEstado` = 5");
    
        try{
            foreach($ListaDeProductosEnBorrador as $DatosDeProductoEnBorrador){
                print_r($DatosDeProductoEnBorrador);
                $ProductoEnBorrador = new producto($DatosDeProductoEnBorrador['id']);
                $ProductoEnBorrador->Eliminar();
                header('Location: ../Productos/NuevoProducto.php');
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
    ?>
    </div>
</body>
</html>