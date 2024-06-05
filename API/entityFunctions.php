<?php
$status = '900';
$message = '';
$result = array();


include("../Otros/clases.php");
$db = new conexion();

session_start();
if(empty($_SESSION)){
    $status = '400';
    $message = 'No hay ningun usuario logeado';
}else{
    if(empty($_GET['idEntity'])){
        $status = '400';
        $message = 'No se especificó el ID de la entidad';
    }else{
        if(empty($_GET['method'])){
            $status = '400';
            $message = 'No se especificó el metodo a ejecutar';
        }else{
            if(!empty($_GET['idModulo'] && is_numeric($_GET['idModulo']))){
                $sql = "SELECT * FROM `modulospermitidos` WHERE `usuario` = '".$_SESSION['nombreDeUsuario']."' AND `idModulo` = ".$_GET['idModulo'];
                $search = $db->consultar($sql);
                if(empty($search)){
                    $status = '400';
                    $message = 'No cuentas con el permiso necesario para realizar esta acción';
                }else{
                    switch($_GET['idModulo']){
                        case '1': $className = "store"; break;
                        case '2': $className = "budget"; break;
                        case '3': $className = "customer"; break;
                        case '5': $className = "product"; break;
                        case '6': $className = "purchase"; break;
                        case '7': $className = "provider"; break;
                        case '9': $className = "user"; break;
        
                        default: $status = '400'; $message = 'No se encontró la clase del modulo especificado';
                    }
        
                    if(isset($className)){
                        try{
                            $entity = new $className($_GET['idEntity']);
                            
                            if(method_exists($entity, $_GET['method'])){
                                $method = $_GET['method'];
                                $dataToSent = $_GET;

                                unset($dataToSent['idModulo']);
                                unset($dataToSent['idEntity']);
                                unset($dataToSent['method']);

                                $result = $entity->$method($dataToSent);
                                $status = '200';
                                $message = 'Se ejecutó correctamente';
                            }else{
                                $status = '400';
                                $message = 'El método no existe en la clase '.$className;
                            }
                        }catch(Exception $error){
                            $status = '400';
                            $message = $error->getMessage();
                        }
                    }
                }
            }else{
                $status = '400';
                $message = 'No se especificó el modulo o es inválido';
            }
        }
    }
    
    
}

$jsonMode = true;
if($jsonMode){
    $json = json_encode(array(
        'status' => $status,
        'message' => $message,
        'result' => $result,
    ));
    echo $json;
}else{
    print_r(array(
        'status' => $status,
        'message' => $message,
        'result' => $result,
    ));
}
?>