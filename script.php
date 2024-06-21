<?php
include('Otros/clases.php');
$BaseDeDatos = new conexion();



$maxProd = 30;
$maxProv = 0;
$maxCust = 0;
$lorem = "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore voluptatem ratione exercitationem rerum consequuntur amet architecto non, asperiores nisi perspiciatis nulla adipisci est consectetur aut in debitis quibusdam, temporibus soluta.";


$proportions = array(
    'material' => 0.4,
    'equipo' => 0.3,
    'personal' => 0.2,
    'comida' => 0.1,
);

$quantities = array(
    'material' => $proportions['material']*$maxProd,
    'equipo' => $proportions['equipo']*$maxProd,
    'personal' => $proportions['personal']*$maxProd,
    'comida' => $proportions['comida']*$maxProd,
);

echo "Registrando productos...";
for ($i=0; $i < $maxProd; $i++) { 
    $unit = "NULL";
    $desc = "NULL";
    $alert = "NULL";
    
    if($i < $quantities['material']){
        $idCategory = 1;
    }else if($i < ($quantities['equipo'] + $quantities['material'])){
        $idCategory = 2;
    }else if($i < ($quantities['equipo'] + $quantities['material'] + $quantities['personal'])){
        $idCategory = 3;
    }else{
        $idCategory = 4;
    }


    switch($idCategory){
        case 1:
            $categoryName = "material";
            $min = 0; $max = 200;
            $unit = rand(2, 4);
            $alert = $unit * 4;
        break;
        
        case 2:
            $categoryName = "Equipo";
            $min = 300; $max = 2000;
            $alert = rand(5, 20);
        break;

        case 3:
            $categoryName = "Mano de obra";
            $min = 80; $max = 35;
        break;

        case 4:
            $categoryName = "Comida";
            $min = 3; $max = 8;
        break;
    }

    $price = rand($min, $max);
    $multiplyer = 1 / rand(1, 5);
    $price = $price * $multiplyer;
    $price = number_format($price, 2,'.','');

    if($multiplyer<0.6){
        $desc = "'".substr($lorem, 0, rand(10, 200))."'";
    }
    

    $name = "Pro_ejemplo_".($i+1)." - $categoryName";
    $sql = "INSERT INTO `productos`(`nombre`, `idCategoria`, `precio`, `idUnidadDeMedida`, `descripcion`, `nivelDeAlerta`, `ULRImagen`, `idEstado`) VALUES 
    ('$name', $idCategory, $price, $unit, $desc, $alert, NULL, 2)";
    

    try{
        $BaseDeDatos->ejecutar($sql);
        echo "Guardado $name";
    }catch(PDOException $error){
        echo "Error en ";
        echo $sql;
    }

    echo "<br>";
}

$docTypes = array('V', 'J', 'E', 'G');
$opers = array('0412', '0414', '0416', '0424');
$mail = array('gmail', 'hotmail', 'gmail');

for ($i=0; $i < $maxProv; $i++) { 
    $rif = rand(4000000, 444000000);
    $docType = ($rif>40000000? 'J':'V');
    $name = "Prov ejemplo #".($i+1);

    $address = "'".substr($lorem, 0, rand(10, 50))."'";
    $phone1 = $opers[rand(0, 3)].'-'.rand(1111111,9999999);
    $phone2 = "NULL";
    $email = 'NULL';

    if(rand(0,5)>3){
        $phone2 = "'".$opers[rand(0, 3)].'-'.rand(1111111,9999999)."'";
    }

    if(rand(0,5)>3){
        $email = "'ramdom".rand(111,999)."@".$mail[rand(0,2)].".com'";
    }

    $sqlContact = "INSERT INTO `contactos`(`direccion`, `telefono1`, `telefono2`, `correo`) VALUES 
    ($address, '$phone1', $phone2, $email)";
    
    
    $idContact = $BaseDeDatos->ejecutar($sqlContact);



    $sqlProv = "INSERT INTO `proveedores`(`rif`, `tipoDeDocumento`, `nombre`, `idContacto`, `idEstado`, `ULRImagen`) VALUES 
    ('$rif', '$docType', '$name', $idContact, 7, NULL);";
    //echo $sqlProv;
    $idProv = $BaseDeDatos->ejecutar($sqlProv);
    echo "<br>";

    $search = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE (`idCategoria` = 1 OR `idCategoria` = 2) order by rand() limit ".rand(0,5));
    

    if(!empty($search)){
        foreach($search as $row){
            $idProd = $row[0];
            $sqlPP = "INSERT INTO `productosdeproveedor`(`idProducto`, `idProveedor`) VALUES ($idProd, '$rif')";
            $idProv = $BaseDeDatos->ejecutar($sqlPP);
        }
    }
    
    echo "<br>";
}


for ($i=0; $i < $maxCust; $i++) { 
    $rif = rand(4000000, 444000000);
    $docType = ($rif>40000000? 'J':'V');
    $name = "Prov ejemplo #".($i+1);

    $address = "'".substr($lorem, 0, rand(10, 50))."'";
    $phone1 = $opers[rand(0, 3)].'-'.rand(1111111,9999999);
    $phone2 = "NULL";
    $email = 'NULL';

    if(rand(0,5)>3){
        $phone2 = "'".$opers[rand(0, 3)].'-'.rand(1111111,9999999)."'";
    }

    if(rand(0,5)>3){
        $email = "'ramdom".rand(111,999)."@".$mail[rand(0,2)].".com'";
    }

    $sqlContact = "INSERT INTO `contactos`(`direccion`, `telefono1`, `telefono2`, `correo`) VALUES 
    ($address, '$phone1', $phone2, $email)";

    $idContact = $BaseDeDatos->ejecutar($sqlContact);



    $sqlClie = "INSERT INTO `clientes`(`rif`, `tipoDeDocumento`, `nombre`, `idContacto`, `idEstado`, `ULRImagen`) VALUES 
    ('$rif', '$docType', '$name', $idContact, 11, NULL);";
    $BaseDeDatos->ejecutar($sqlClie);
    echo "<br>";
}




?>

