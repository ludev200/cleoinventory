<?php
include_once '../Otros/clases.php';

$Almacen = new almacen(0);

$Almacenes = array();
$Almacenes['objetos'] = array();

$Filtros = array(
    'idEstado' => '0',
    'descripcion' => ''
);

if(isset($_GET['idEstado'])){
    $Filtros = array(
        'idEstado' => (($_GET['idEstado'] == 51 || $_GET['idEstado'] == 52)?$_GET['idEstado']:'0'),
        'descripcion' => ((isset($_GET['descripcion']))?$_GET['descripcion']:'')
    );
    
}

$ListaDeAlmacenes = $Almacen->ListarAlmacenes($Filtros);


//print_r($ListaDeAlmacenes);

if(empty($ListaDeAlmacenes)){
    echo json_encode(array('mensaje' => 'No hay proveedores para mostrar.'));
}else{
    foreach($ListaDeAlmacenes as $RowAlmacen){
        $item = array(
            'id' => $RowAlmacen['id'],
            'nombre' => $RowAlmacen['nombre'],
            'direccion' => $RowAlmacen['direccion'],
            'productos' => $RowAlmacen['productos']
        );
        array_push($Almacenes['objetos'], $item);
    }

    echo json_encode($Almacenes);
}

?>