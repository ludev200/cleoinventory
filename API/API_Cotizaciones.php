<?php
include_once '../Otros/clases.php';

$Cotizacion = new cotizacion(0);
//$ArrayCotizaciones = array();
$ArrayCotizaciones['objetos'] = array();



if($_GET){
    $Filtros = array(
        'descripcion' => ((empty($_GET['descripcion']))?'':$_GET['descripcion']),
        'mes' => ((empty($_GET['mes']))?'':$_GET['mes']),
        'anio' => ((empty($_GET['anio']))?'':$_GET['anio']),
        'estado' => ((empty($_GET['estado']))?'':$_GET['estado']),
        'count' => ((empty($_GET['count']))?'':$_GET['count'])
    );

}else{
    $Filtros = array(
        'descripcion' => '',
        'mes' => '',
        'anio' => '',
        'estado' => '',
        'count' => ''
    );
}

//print_r($Filtros);
//echo '<br><br>';
//echo $Cotizacion->ListarCotizaciones($Filtros);
//print_r($Cotizacion->ListarCotizaciones($Filtros));

$ResultadosDeConsulta = $Cotizacion->ListarCotizaciones($Filtros);

//print_r($ResultadosDeConsulta);

if(!empty($ResultadosDeConsulta)){
    foreach($ResultadosDeConsulta as $Row){
        
        if(empty($_GET['count'])){
            $item = array(
                'id' => $Row['id'],
                'nombre' => $Row['nombre'],
                'cedulaCliente' => $Row['cedulaCliente'],
                'fechaExpiracion' => $Row['fechaExpiracion'],
                'idEstado' => $Row['idEstado'],
                'fechaCreacion' => $Row['fechaCreacion'],
                'CASalario' => $Row['pCASalario'],
                'pUtilidades' => $Row['pUtilidades'],
                'pIVA' => $Row['pIVA'],
            );
        }else{
            $item = array(
                'cliente' => $Row['nombre'],
                'contador' => $Row['contador']
            );
        }

        array_push($ArrayCotizaciones['objetos'], $item);
    }
    echo json_encode($ArrayCotizaciones);
}else{
    echo json_encode(array('mensaje' => 'No hay productos para mostrar.'));
}
?>