<?php
include_once('../Otros/clases.php');
$public = new publicFunctions();




$status = '000';
$message = '';
$givenData = $_GET;
$toSentData = array();
$result = array();


if(!isset($_GET['method'])){
    $status = '417';
    $message = 'no se especificó el nombre del método';
}else{
    if(!method_exists($public, $_GET['method'])){
        $status = '404';
        $message = 'El método '.$_GET['method'].' no existe en la clase '.get_class($public);
    }else{
        $status = '801';
        $message = 'public instanciado y listo para su uso';
        unset($givenData['method']);
        $keys = array_keys($givenData);

        foreach($keys as $keyName){
            $toSentData[$keyName] = $givenData[$keyName];
        }
    }
}




if($status == '801'){
    $status = '202';
    $message = 'El objeto public ejecutará el método '.$_GET['method'].' de la clase '.get_class($public).' con los datos recibidos a para enviar';
    try{
        $result = call_user_func(array($public, $_GET['method']), $toSentData);
        $status = '200';
        $message = 'El objeto public ha ejecutado correctamente el método '.$_GET['method'].' de la clase '.get_class($public);
    }catch(Exception $error){
        $status = '400';
        $message = 'Ha ocurrido un error en la ejecución del método';
        $result = $error->getMessage();
    }
}




if(true){
    $json = json_encode(array(
        'status' => $status,
        'message' => $message,
        'givenData' => $givenData,
        'toSentData' => $toSentData,
        'result' => $result
    ));

    echo $json;
}else{
    print_r(array(
        'status' => $status,
        'message' => $message,
        'givenData' => $givenData,
        'toSentData' => $toSentData,
        'result' => $result
    ));
}
?>