<?php
include_once '../Otros/clases.php';

$Producto = new producto(0);
$Productos = array();
$Productos['objetos'] = array();

if($_GET){
    $Filtros = array(
        "descripcion" => ((empty($_GET['descripcion']))?"":$_GET['descripcion']),
        "categoria" => ((empty($_GET['categoria']))?"":$_GET['categoria']),
        "existencia" => ((empty($_GET['existencia']))?"0":$_GET['existencia'])
    );
}else{
    $Filtros = array(
        "descripcion" => "",
        "categoria" => "",
        "Existencia" => "0"
    );
}

//print_r($Filtros);
//echo '<br><br>';
$ListaDeProductos = $Producto->ListarProductos($Filtros);

//print_r($ListaDeProductos);



if(!empty($ListaDeProductos)){
    foreach($ListaDeProductos as $RowProductos){
        $spoilage = 1;
        if($RowProductos['idCategoria'] == 2){
            $product = new product($RowProductos['id']);
            $spoilage = $product->getDefaultSpoilage();
        }

        $item = array(
            'id' => $RowProductos['id'],
            'nombre' => $RowProductos['nombre'],
            'ULRImagen' => $RowProductos['ULRImagen'],
            'descripcion' => $RowProductos['descripcion'],
            'precio' => $RowProductos['precio'],
            'simbolo' => $RowProductos['simbolo'],
            'nombredeunidad' => $RowProductos['nombredeunidad'],
            'idcategoria' => $RowProductos['idCategoria'],
            'idEstado' => $RowProductos['idEstado'],
            'existencia' => $RowProductos['existencia'],
            'nivelDeAlerta' => $RowProductos['nivelDeAlerta'],
            'listaDeProveedores' => $RowProductos['listaDeProveedores'],
            'depreciacion' => $spoilage
        );
        array_push($Productos['objetos'], $item);
    }

    echo json_encode($Productos);
}else{
    echo json_encode(array('mensaje' => 'No hay productos para mostrar.'));
}



?>

