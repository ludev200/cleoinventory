<?php
session_start();
if(!isset($_SESSION["nombreDeUsuario"])){
    header('Location: ../login.php');
}else{
    include_once('../Otros/clases.php');
    $BaseDeDatos = new conexion();
}

$simboloMoneda = '$';
$tasa = 1;

if(empty($_GET['moneda'])){
    header('Location: ../Error.php');
}else{
    if($_GET['moneda']!='1' && $_GET['moneda']!='2'){
        header('Location: ../Error.php');
    }else{
        if($_GET['moneda'] == '2'){
            $search = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'SimboloMonedaNacional';");

            if(!empty($search)){
                $simboloMoneda = $search[0]['valor'];
            }
        }
    }
}

if(!empty($_GET['tasa'])){
    if(!is_numeric($_GET['tasa'])){
        header('Location: ../Error.php');
    }else{
        $tasa = $_GET['tasa'];
    }
}

if(empty($_GET['id'])){
    header('Location: ../Error.php');
}else{
    $purchase = new purchase($_GET['id']);
    $public = new publicFunctions();
}


ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $purchase->getName();?></title>
    <link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/CleoInventory/Reportes/colores.css">
    <link href="http://<?php echo $_SERVER['HTTP_HOST'];?>/CleoInventory/Imagenes/Logo.png" rel="shortcut icon" >
</head>
<body>
    <header>
        <b>RIF <?php echo $public->getCompany_rif();?></b>
        <p><?php echo $public->getCompany_name();?></p>
        <p><?php echo $public->getCompany_address();?></p>
        <p><?php echo $public->getCompany_cityData();?></p>
        <p><?php echo $public->getCompany_phone();?></p>
        <p><?php echo $public->getCompany_email();?></p>
        <img src="http://<?php echo $_SERVER['HTTP_HOST'].'/CleoInventory/Imagenes/Sistema/'.$public->getCompanyLogo();?>" alt="">
    </header>
    <br>
    <b class="NombreDeLaHoja">ORDEN DE COMPRA #<?php echo $purchase->getId();?>:</b>

    <div class="aber1">
        <div class="EspacioDeCliente">
            <b class="TituloCentrado">Datos</b>
            <div class="TituloDelDato">
                <b>Motivo</b>
                <b>N° productos</b>
                <b>Estado</b>
            </div>
            <div class="PuntosSeparadores">
                <b>:</b>
                <b>:</b>
                <b>:</b>
            </div>
            <div class="RespuestaDelDato">
                <span><?php echo $purchase->getName();?></span>
                <span><?php echo $purchase->getPublicQuantity();?></span>
                <span><?php echo $purchase->getStatusName();?></span>
            </div>
        </div>
        <div class="EspacioDeFecha">
            <b class="TituloCentrado">Fecha</b>
            <div class="TituloDelDatoParaFecha">
                <b>Creado</b>
                <b>Modificado</b>
                <b>Vence</b>
            </div>
            <div class="PuntosSeparadoresParaF">
                <b>:</b>
                <b>:</b>
                <b>:</b>
            </div>
            <div class="RespuestaDelDatoParaF">
                <span><?php echo $purchase->getCreationDate();?></span>
                <span><?php echo $purchase->getModifyDate();?></span>
                <span><?php echo $purchase->getExpirationDate();?></span>
            </div>
        </div>
    </div>

    <?php
    $materialProducts = $purchase->getMaterialProducts();
    

    if(!empty($materialProducts)){
        $materialProductsHTML = '';

        foreach($materialProducts as $row){
            $materialProductsHTML.='<div class="row">
                <span class="quantity">x '.$row['cantidad'].'</span>
                <span class="product">'.$row['name'].'</span>
            </div>';
        }
        echo '<div class="CategoriaDeProd">Materiales</div>    
        <div class="listaDeCompras">
            '.$materialProductsHTML.'
        </div>';
    }

    $equipmentProducts = $purchase->getEquipmentProducts();
    

    if(!empty($equipmentProducts)){
        $equipmentProductsHTML = '';

        foreach($equipmentProducts as $row){
            $equipmentProductsHTML.='<div class="row">
                <span class="quantity">x '.$row['cantidad'].'</span>
                <span class="product">'.$row['name'].'</span>
            </div>';
        }

        echo '<div class="CategoriaDeProd">Equipo y maquinaria</div>
        <div class="listaDeCompras">
        '.$equipmentProductsHTML.'
        </div>';
    }


    ?>
    
    
    <br>
    <div class="Tabla2">
        <div class="TituloDeTabla">Proveedores disponibles</div>
        <?php
        $providers = $purchase->getProviders();
        
        
        if(empty($providers)){
            echo '<div class="grisesillo">No hay proveedores disponibles</div>';
        }else{
            $counter = 1;
            foreach($providers as $providerData){
                $provider = new provider($providerData['idProveedor']);

                $providedProductsHTML = '' ;
                foreach($providerData['products'] as $id){
                    $product = new product($id);

                    $providedProductsHTML.='<div class="rowPP">
                        <span class="giunsillo">-</span>
                        '.$product->getName().'
                    </div>';
                }

                echo '<div class="rowProvider">
                    <div class="providerName">
                        <span class="numerillo">'.$counter.') </span>
                        <span>'.$provider->getName().'</span>
                        <span class="grisesillo">'.(empty($provider->getPhoneToUser())? '':' ('.$provider->getPhoneToUser().')').'</span>
                        <div class="providedProducts">
                            '.$providedProductsHTML.'
                        </div>
                    </div>
                </div>';

                $counter++;
            }
            
        }
        ?>
    </div>
    

    <br>
    <footer>
        <div class="EspacioDeFirma">
            <img src="http://<?php echo $_SERVER['HTTP_HOST'].'/CleoInventory/Imagenes/Sistema/'.$public->getCompanyBossSing();?>" alt="">
            <div class="PalitoYNombre">
                <b>__________________</b>
                <b>Fernández Richard</b>
                <b>V 7.238.898</b>
            </div>
            
        </div>
        <img class="Sello" src="http://<?php echo $_SERVER['HTTP_HOST'].'/CleoInventory/Imagenes/Sistema/'.$public->getCompanySeal();?>" alt="">
    </footer>
</body>
</html>




<?php
    $html = ob_get_clean();
    require_once('../Librerias/dompdf/autoload.inc.php');
    use Dompdf\Dompdf;
    $dompdf = new Dompdf();

    $options = $dompdf->getOptions();
    $options->set(array('isRemoteEnabled' => true));
    $dompdf->setOptions($options);

    $dompdf->loadHtml($html);

    $dompdf->setPaper('letter');
    $dompdf->render();
    
    $dompdf->stream("Orden de compra #".$purchase->getId().".pdf", array('Attachment' => false));
?>


