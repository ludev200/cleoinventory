<?php
include_once '../Otros/clases.php';
$BaseDeDatos = new conexion();


$ArrayDeRespuesta['objetos'] = array();

$Filtros = array(
    'idCot' => (isset($_GET['idCotizacion'])?$_GET['idCotizacion']:'')
);


//print_r($Filtros);




if(isset($_GET['idCotizacion'])){
    if(is_numeric($_GET['idCotizacion'])){
        $ResultadoDeBusqueda = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE `id` = ".$_GET['idCotizacion']);
        if(empty($ResultadoDeBusqueda)){
            echo json_encode(array('mensaje' => 'La cotización no existe'));
        }else{
            $Cotizacion = new cotizacion($_GET['idCotizacion']);
            //print_r($Cotizacion->ListarProductosDeCot());

            foreach($Cotizacion->ListarProductosDeCot() as $Row){
                $depreciacion = 1;
                if($Row['idCategoria'] == 2){
                    $search = $BaseDeDatos->consultar("SELECT * FROM `depreciacion` WHERE `idProducto` = ".$Row['id']);
                    if(!empty($search)){
                        $depreciacion = $search[0]['valor'];
                    }
                }

                $item = array(
                    'idProducto' => $Row['id'],
                    'nombre' => $Row['nombre'],
                    'idCategoria' => $Row['idCategoria'],
                    'nivelDeAlerta' => $Row['nivelDeAlerta'],
                    'ULRImagen' => $Row['ULRImagen'],
                    'cantidad' => $Row['cantidad'],
                    'depreciacion' => $depreciacion,
                    'precioUnitario' => $Row['precioUnitario'],
                    'precioMultiplicado' => $Row['precioMultiplicado']
                );


                array_push($ArrayDeRespuesta['objetos'], $item);
            }


            echo json_encode($ArrayDeRespuesta);
        }
    }else{
        echo json_encode(array('mensaje' => 'LA ID es inválida'));
    }
}else{
    echo json_encode(array('mensaje' => 'No se indicó la ID de cotizacion'));
}




?>