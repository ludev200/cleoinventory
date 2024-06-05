<?php
include('Otros/clases.php');

//DESCRIPCION DEL ERROR
    if(isset($_GET['desc'])){
        switch($_GET['desc']){
            case 1:
                $descripcion = "La ID no corresponde a una cotización en la base de datos :("; break;

            case 2:
                $descripcion = "No se obtuvo ninguna ID a buscar :("; break;
            
            case 3:
                $descripcion = "El tipo de moneda no fue descrito en la petición :("; break;

            case 4:
                $descripcion = "El tipo de moneda(modo) tiene un valor inválido :("; break;

            case 5:
                $descripcion = "La tasa de cambio no fue descrita en la petición :("; break;

            case 6:
                $descripcion = "La tasa de cambio no fue descrita en la petición :("; break;

            case 7:
                $descripcion = "La tasa de cambio no tiene un valor válido :("; break;
            
            case 8:
                $descripcion = "La tasa de cambio no tiene un valor válido :("; break;
            case 9:
                $descripcion= "El usuario no cuenta con los permisos necesarios para continuar :("; break;
            case 10:
                $descripcion = "La ID no corresponde a un almacén en la base de datos :("; break;
            case 11:
                $descripcion = "El valor de la ID es inválido :("; break;
            case 12:
                $descripcion = "No cuentas con ningún almacén para registrar la compra :("; break;
            case 13:
                $descripcion = "No se encontró la compra buscada :("; break;
            case 14:
                $descripcion = "No se encontró el usuario en la base de datos :("; break;
            default:
                $descripcion = "Ha ocurrido un error desconocido :(";
        }
    }else{
        $descripcion = "Ha ocurrido un error desconocido :(";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Error</title>
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
        <div class="DisplayDeError">
            <div class="VentanitaDeInfo">
                <img src="Imagenes/CLEO_Error.png" alt="">
                <h2>Parece que algo ha ido mal...</h2>
                <p><?php echo $descripcion;?></p>
            </div>
        </div>
    </div>
</body>
</html>