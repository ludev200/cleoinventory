<?php
include_once('clases.php');
$BaseDeDatos = new conexion();

if(isset($_GET['id'])){
    if(is_numeric($_GET['id'])){
        $ConsultaDeCompra = $BaseDeDatos->consultar("SELECT * FROM `ordenesdecompra` WHERE (`id` = ".$_GET['id']." AND `idEstado` = 63)");
        if(empty($ConsultaDeCompra)){
            header('Location: ../');    
        }else{
            try{
                $BaseDeDatos->ejecutar("UPDATE `ordenesdecompra` SET `idEstado`= 62 WHERE `id` = ".$_GET['id']);
                header('Location: ../Compras/Compra?alert=rechazada&id='.$_GET['id']);
            }catch(Exception $Error){

            }
        }
    }else{
        header('Location: ../');    
    }
}else{
    header('Location: ../');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    xd
</body>
</html>