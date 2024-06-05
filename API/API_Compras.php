<?php
include_once '../Otros/clases.php';

$Compra = new compra(0);
$Resultado = array();
$Resultado['objetos'] = array();

$Filtros = array(
    'descripcion' => ((isset($_GET['descripcion']))?$_GET['descripcion']:''),
    'idEstado' => ((isset($_GET['idEstado']))?$_GET['idEstado']:'0')
);

//print_r($Filtros);

$ListaDeCompras = $Compra->ListarCompras($Filtros);
//echo '<br><br><br>';
//print_r($ListaDeCompras);

if(empty($ListaDeCompras)){
    echo json_encode(array('mensaje' => 'No hay compras para mostrar.'));
}else{
    $Resultado['objetos'] = $ListaDeCompras;
    echo json_encode($Resultado);
}

?>