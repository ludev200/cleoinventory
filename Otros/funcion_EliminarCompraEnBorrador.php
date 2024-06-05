<?php

session_start();
include_once('clases.php');

$BaseDeDatos = new conexion();

foreach($BaseDeDatos->consultar("SELECT * FROM `ordenesdecompra` WHERE `idEstado` = 65") as $RowCompraEnBorrador){
    $Objeto_CompraEnBorrador = new compra($RowCompraEnBorrador['id']);

    $Objeto_CompraEnBorrador->Eliminar(65);
}

header('Location: ../Compras/NuevaOrden/?modo=2');

?>