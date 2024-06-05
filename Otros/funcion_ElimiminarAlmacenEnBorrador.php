<?php
include_once('clases.php');
$BaseDeDatos = new conexion();

if(isset($_GET['id'])){
    if(is_numeric($_GET['id'])){
        $id = $_GET['id'];

        $sql = array();
        $sql[] = "DELETE FROM `detallesdeajuste` WHERE `idAlmacenModificado` = $id";
        $sql[] = "DELETE FROM `ajustedeinventario` WHERE (`descripcion` = 'inventario inicial del Almacén #$id')";
        $sql[] = "DELETE FROM `detallesdeajuste` WHERE `idAlmacenModificado` = $id";
        $sql[] = "DELETE FROM `inventario` WHERE `idAlmacen` = $id";
        $sql[] = "DELETE FROM `almacenes` WHERE `idEstado` = 53";

        print_r($sql);

        foreach($sql as $xd){
            //$BaseDeDatos->ejecutar($xd);
        }

        
    }
}



header('Location: ../Almacenes/NuevoAlmacen/');
?>