<?php
include('Otros/clases.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $GLOBALS['nombreDelSoftware'];?></title>
    <link rel="stylesheet" href="estilos_index.css">
<?php include('Otros/cabecera.php');?>
    <div id="CajaDeBarras">
        <a href="index.php" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>       
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <div class="Contenido">
        <div class="CajaDeModulos">
            <?php 
                $user = new user($_SESSION['nombreDeUsuario']);
            
                foreach($user->getPermissionsList() as $modulo){
                    echo '
                    <a class="BotonesInicio" href="'.$modulo['nombre'].'"> 
                        <img src="Imagenes/'.$modulo['nombreDeImagen'].'">
                        <b>'.$modulo['nombre'].'</b>
                    </a>';
                }
            ?>
        </div>
        
    </div>
    
</body>
</html>