<?php

    include_once '../Otros/clases.php';

    $Proveedor = new proveedor(0);
    $Proveedores = array();
    $Proveedores['objetos'] = array();


    if($_GET){
        $Filtros = array(
            "nombre" => ((empty($_GET['nombre']))?"":$_GET['nombre'])
        );
    }else{
        $Filtros = array(
            "nombre" => ""
        );
    }

    //print_r($Filtros);

    $ListaDeProveedores = $Proveedor->ListarProveedores($Filtros);
    
    //print_r($ListaDeProveedores);

    if(empty($ListaDeProveedores)){
        echo json_encode(array('mensaje' => 'No hay proveedores para mostrar.'));
    }else{
        foreach($ListaDeProveedores as $RowProveedor){
            $item = array(
                'rif' => $RowProveedor['rif'],
                'nombre' => $RowProveedor['nombre'],
                "tipoDeDocumento" => $RowProveedor['tipoDeDocumento'],
                'ULRImagen' => $RowProveedor['ULRImagen'],
                'listaDeProductos' => $RowProveedor[6]
            );
            array_push($Proveedores['objetos'], $item);
        }

        echo json_encode($Proveedores);
    }

?>