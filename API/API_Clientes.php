<?php

include_once '../Otros/clases.php';
$Cliente = new cliente(0);
$Clientes = array();
$Clientes['objetos'] = array();

if($_GET){
    $Filtros = array(
        "descripcion" => ((empty($_GET['descripcion']))?"":$_GET['descripcion']),
        'id' => (isset($_GET['id'])?$_GET['id']:'')
    );
}else{
    $Filtros = array(
        "descripcion" => "",
        'id' => ''
    );
}
//print_r($Cliente->ListarClientes($Filtros));
//echo $Cliente->ListarClientes($Filtros);
$ListaDeClientes = $Cliente->ListarClientes($Filtros);

if(!empty($ListaDeClientes)){
    foreach($ListaDeClientes as $RowCliente){
        $item = array(
            'ULRImagen' => $RowCliente['ULRImagen'],
            'tipoDeDocumento' => $RowCliente['tipoDeDocumento'],
            'rif' => $RowCliente['rif'],
            'nombre' => $RowCliente['nombre'],
            'direccion' => $RowCliente['direccion'],
            'correo' => $RowCliente['correo'],
            'telefono1' => $RowCliente['telefono1'],
            'telefono2' => $RowCliente['telefono2']
        );
        array_push($Clientes['objetos'], $item);
    }
    echo json_encode($Clientes);

}else{
    echo json_encode(array('mensaje' => 'No hay productos para mostrar.'));
}

?>