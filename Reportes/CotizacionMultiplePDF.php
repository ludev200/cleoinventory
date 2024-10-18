<?php
session_start();
if(!isset($_SESSION["nombreDeUsuario"])){
    header('Location: ../login.php');
}else{
    include_once('../Otros/clases.php');
    $BaseDeDatos = new conexion();
}


$cantidadACotizar = 1;
if(!isset($_GET['cantidad'])){
    header('Location: ../Error.php');
}else{
    if(!is_numeric($_GET['cantidad'])){
        header('Location: ../Error.php');
    }else{
        $cantidadACotizar = $_GET['cantidad'];
    }
}

$public = new publicFunctions();
$budgetQuantity = 1;
$tasa = 1;

try{
    $budget = new budget($_GET['id']);
}catch(Exception $err){
    header('Location: ../Error.php?error=2&desc=404');
}

if(!empty($_GET['cantidad']) && is_numeric($_GET['cantidad']) && $_GET['cantidad']>0){
    $budgetQuantity = floor($_GET['cantidad']);
}
if(!empty($_GET['tasa']) && is_numeric($_GET['tasa']) && $_GET['tasa']>0){
    $tasa = number_format($_GET['tasa'], 2, '.', '');
}


if($budget->getClientCedula()){
    $customer = new customer($budget->getClientCedula());
}

copy('plantilla.css', 'colores.css');
$contenido = file_get_contents('../Otros/colores.css');

    $variable = '--VinotintoOscuro: ';
    $pos_inicio = strpos($contenido, $variable)+strlen($variable);
    $pos_fin = strpos($contenido, ';', $pos_inicio);
    $color = substr($contenido, $pos_inicio , $pos_fin - $pos_inicio);
    $VinotintoOscuro = $color;

    $variable = '--Vinotinto: ';
    $pos_inicio = strpos($contenido, $variable)+strlen($variable);
    $pos_fin = strpos($contenido, ';', $pos_inicio);
    $color = substr($contenido, $pos_inicio , $pos_fin - $pos_inicio);
    $Vinotinto = $color;
    
    $variable = '--VinotintoClarito: ';
    $pos_inicio = strpos($contenido, $variable)+strlen($variable);
    $pos_fin = strpos($contenido, ';', $pos_inicio);
    $color = substr($contenido, $pos_inicio , $pos_fin - $pos_inicio);
    $VinotintoClarito = $color;

    $variable = '--RositaOscuro: ';
    $pos_inicio = strpos($contenido, $variable)+strlen($variable);
    $pos_fin = strpos($contenido, ';', $pos_inicio);
    $color = substr($contenido, $pos_inicio , $pos_fin - $pos_inicio);
    $RositaOscuro = $color;
    
    $variable = '--Rosita: ';
    $pos_inicio = strpos($contenido, $variable)+strlen($variable);
    $pos_fin = strpos($contenido, ';', $pos_inicio);
    $color = substr($contenido, $pos_inicio , $pos_fin - $pos_inicio);
    $Rosita = $color;

    $coloresContent = file_get_contents('colores.css');
    $coloresContent = str_replace('var(--VinotintoClarito)', $VinotintoClarito, $coloresContent);
    $coloresContent = str_replace('var(--Vinotinto)', $Vinotinto, $coloresContent);
    $coloresContent = str_replace('var(--VinotintoOscuro)', $VinotintoOscuro, $coloresContent);
    $coloresContent = str_replace('var(--RositaOscuro)', $RositaOscuro, $coloresContent);
    $coloresContent = str_replace('var(--Rosita)', $Rosita, $coloresContent);
    file_put_contents('colores.css', $coloresContent);


    $localCurrencySymbol = $public->getNationalCurrency_simbol();
    
    
    $materialsTotalPrice = 0;
    $equipmentTotalPrice = 0;
    $personalTotalPrice = 0;
    $foodTotalPrice = 0;

    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $budget->getName();?></title>
    <link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/CleoInventory/Reportes/colores.css">
    <link href="http://<?php echo $_SERVER['HTTP_HOST'];?>/CleoInventory/Imagenes/Logo.png" rel="shortcut icon" >
    <link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/CleoInventory/Reportes/formatoMultiple.css">
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
    <b class="NombreDeLaHoja">ESTRUCTURA DE COSTOS N° <?php echo zerofill($budget->getId(), 7);?> :</b>
    <div class="aber1">
        <div class="EspacioDeCliente">
            <b class="TituloCentrado">Cliente</b>
            <div class="TituloDelDato">
                <b>RIF</b>
                <b>Razón social</b>
                <b>Dirección</b>
            </div>
            <div class="PuntosSeparadores">
                <b>:</b>
                <b>:</b>
                <b>:</b>
            </div>
            <div class="RespuestaDelDato">
                <span><?php echo (isset($customer)? $customer->getDocType().'-'.zerofill($customer->getId(), 9):'<span style="color: gray;">No especificó ningún cliente para esta cotización</span>');?></span>
                <span><?php echo (isset($customer)? $customer->getName():'');?></span>
                <span style="height: 30px;"><?php echo (isset($customer)? $customer->getAddress():'');?></span>
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
                <span><?php echo (empty($budget->getCreationDate())? '<span style="color:gray;"> - - - - - - - -</span>':$budget->getCreationDate());?></span>
                <span><?php echo (empty($budget->getUpdatedDate())? '<span style="color:gray;"> - - - - - - - -</span>':$budget->getUpdatedDate());?></span>
                <span><?php echo (empty($budget->getExpireDateReverse())? '<span style="color:gray;"> - - - - - - - -</span>':$budget->getExpireDateReverse());?></span>
            </div>
        </div>
    </div>
    <br>
    <div class="EspacioDeTitulo"><?php echo $budget->getName();?></div>
    <div style="text-align: center; color: gray;"><?php echo (empty($budget->getRequestNumber())? '':'N° de petición: '.$budget->getRequestNumber());?></div>
    <br>
    <div class="TasaAvisito">Tasa de cambio comprendida: <?php echo $tasa.$localCurrencySymbol;?></div>
    <div class="Tabla">
        <div class="TituloDeTabla">Materiales</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre">Nombre</span>
            <span class="NombreDeTituloDeTabla ColumnaCantidad">Cantidad</span>
            <span class="NombreDeTituloDeTabla ColumnaUnidad">Unid.</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio" style="font-size: 12px;">Precio U. <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaPrecioUSD">Precio U. $</span>
            <span class="NombreDeTituloDeTabla ColumnaTotalUSD">Total $</span>
        </div>
        <div class="EspacioDeRows">
            <?php
            $materialsProducts = $budget->getProductsOnCategory(array(1));
            if(empty($materialsProducts)){
                echo '
                <div class="Row">
                    <div class="RowVacia">Esta cotización no incluye materiales</div>
                </div>
                ';
            }else{
                foreach($materialsProducts as $row){
                    $price_BS = $tasa * $row['price'];
                    $total_BS = $price_BS * $row['quantity'];

                    echo '
                    <div class="Row">
                        <span class="CeldaDeTabla ColumnaID TACenter">'.$row['id'].'</span>
                        <span class="CeldaDeTabla ColumnaNombre">'.$row['name'].'</span>
                        <span class="CeldaDeTabla ColumnaCantidad TACenter">'.$row['quantity'].'</span>
                        <span class="CeldaDeTabla ColumnaUnidad TACenter">'.$row['unitSymbol'].'</span>
                        <span class="CeldaDeTabla ColumnaPrecio TARight">'.$price_BS.$localCurrencySymbol.'</span>
                        <span class="CeldaDeTabla ColumnaTotal TARight">'.$total_BS.$localCurrencySymbol.'</span>
                        <span class="CeldaDeTabla ColumnaPrecioUSD TARight">'.number_format($row['price'], 2, '.', '').'$</span>
                        <span class="CeldaDeTabla ColumnaTotalUSD TARight">'.number_format($row['total'], 2, '.', '').'$</span>
                    </div>';
                    $materialsTotalPrice+= number_format($row['total'], 2);
                }
            }
            ?>
        </div>
        <div class="TituloDeTabla">Equipamiento</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre">Nombre</span>
            <span class="NombreDeTituloDeTabla ColumnaCantidad">Cantidad</span>
            <span class="NombreDeTituloDeTabla ColumnaUnidad">Depr.</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio" style="font-size: 12px;">Precio U. <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaPrecioUSD">Precio U. $</span>
            <span class="NombreDeTituloDeTabla ColumnaTotalUSD">Total $</span>
        </div>
        <div class="EspacioDeRows">
            <?php
            $equipmentProducts = $budget->getProductsOnCategory(array(2));
            if(empty($equipmentProducts)){
                echo '
                <div class="Row">
                    <div class="RowVacia">Esta cotización no incluye equipamiento</div>
                </div>
                ';
            }else{
                foreach($equipmentProducts as $row){
                    $product = new product($row['id']);
                    $price_BS = $tasa * $row['price'];
                    $total_BS = $price_BS * $row['quantity'];

                    echo '
                    <div class="Row">
                        <span class="CeldaDeTabla ColumnaID TACenter">'.$row['id'].'</span>
                        <span class="CeldaDeTabla ColumnaNombre">'.$row['name'].'</span>
                        <span class="CeldaDeTabla ColumnaCantidad TACenter">'.$row['quantity'].'</span>
                        <span class="CeldaDeTabla ColumnaUnidad TACenter">'.$product->getDefaultSpoilage().'</span>
                        <span class="CeldaDeTabla ColumnaPrecio TARight">'.$price_BS.$localCurrencySymbol.'</span>
                        <span class="CeldaDeTabla ColumnaTotal TARight">'.$total_BS.$localCurrencySymbol.'</span>
                        <span class="CeldaDeTabla ColumnaPrecioUSD TARight">'.number_format($row['price'], 2, '.', '').'$</span>
                        <span class="CeldaDeTabla ColumnaTotalUSD TARight">'.number_format($row['total'], 2, '.', '').'$</span>
                    </div>';
                    $equipmentTotalPrice+= number_format($row['total'], 2);
                }
            }
            ?>
        </div>
        <div class="TituloDeTabla">Mano de obra</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre">Nombre</span>
            <span class="NombreDeTituloDeTabla ColumnaCantidad">Cantidad</span>
            <span class="NombreDeTituloDeTabla ColumnaUnidad">Días</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio" style="font-size: 12px;">Precio U. <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaPrecioUSD">Precio U. $</span>
            <span class="NombreDeTituloDeTabla ColumnaTotalUSD">Total $</span>
        </div>
        <div class="EspacioDeRows">
            <?php
            $personalProducts = $budget->getProductsOnCategory(array(3));
            $foodProducts = $budget->getProductsOnCategory(array(4));
            if(empty($personalProducts) && empty($foodProducts)){
                echo '
                <div class="Row">
                    <div class="RowVacia">Esta cotización no incluye mano de obra</div>
                </div>
                ';
            }else{
                if(!empty($personalProducts)){
                    foreach($personalProducts as $row){
                        $price_BS = $tasa * $row['price'];
                        $pieces = explode('.', $row['quantity']);
                        $quantity = $pieces[0];
                        $days = $pieces[1];
                        $total_BS = $price_BS * $quantity * $days;
                        
                        echo '
                        <div class="Row">
                            <span class="CeldaDeTabla ColumnaID TACenter">'.$row['id'].'</span>
                            <span class="CeldaDeTabla ColumnaNombre">'.$row['name'].'</span>
                            <span class="CeldaDeTabla ColumnaCantidad TACenter">'.$quantity.'</span>
                            <span class="CeldaDeTabla ColumnaUnidad TACenter">'.$days.'</span>
                            <span class="CeldaDeTabla ColumnaPrecio TARight">'.$price_BS.$localCurrencySymbol.'</span>
                            <span class="CeldaDeTabla ColumnaTotal TARight">'.$total_BS.$localCurrencySymbol.'</span>
                            <span class="CeldaDeTabla ColumnaPrecioUSD TARight">'.number_format($row['price'], 2, '.', '').'$</span>
                            <span class="CeldaDeTabla ColumnaTotalUSD TARight">'.number_format($row['total'], 2, '.', '').'$</span>
                        </div>';
                        $personalTotalPrice+= number_format($row['total'], 2);
                    }
                }
                if(!empty($foodProducts)){
                    foreach($foodProducts as $row){
                        $price_BS = $tasa * $row['price'];
                        $pieces = explode('.', $row['quantity']);
                        $quantity = $pieces[0];
                        $days = $pieces[1];
                        $total_BS = $price_BS * $quantity * $days;

                        echo '
                        <div class="Row">
                            <span class="CeldaDeTabla ColumnaID TACenter">'.$row['id'].'</span>
                            <span class="CeldaDeTabla ColumnaNombre">'.$row['name'].'</span>
                            <span class="CeldaDeTabla ColumnaCantidad TACenter">'.$quantity.'</span>
                            <span class="CeldaDeTabla ColumnaUnidad TACenter">'.$days.'</span>
                            <span class="CeldaDeTabla ColumnaPrecio TARight">'.$price_BS.$localCurrencySymbol.'</span>
                            <span class="CeldaDeTabla ColumnaTotal TARight">'.$total_BS.$localCurrencySymbol.'</span>
                            <span class="CeldaDeTabla ColumnaPrecioUSD TARight">'.number_format($row['price'], 2, '.', '').'$</span>
                            <span class="CeldaDeTabla ColumnaTotalUSD TARight">'.number_format($row['total'], 2, '.', '').'$</span>
                        </div>';
                        $foodTotalPrice+= number_format($row['total'], 2);
                    }
                }
            }
            ?>
        </div>
    </div>
    <div class="EspacioDeLosTotales_Mixto">
        <img class="intentodemejoracion" src="http://<?php echo $_SERVER['HTTP_HOST'].'/CleoInventory/Imagenes/Sistema/'.$public->getCompanyBossSing();?>" alt="">
        <img class="SelloXDDD" src="http://<?php echo $_SERVER['HTTP_HOST'].'/CleoInventory/Imagenes/Sistema/'.$public->getCompanySeal();?>" alt="">
        <div class="SubTotales">
            <b>Costo de Materiales</b>
            <b>Costo de Equipo</b>
            <b>Costo de Mano de obra</b>
            <b>Asociado al salario (<?php echo $budget->getPCASalario();?>%)</b>
            <div class="Palito"></div>
            <b>Costo general de productos</b>
            <b>Utilidades (<?php echo $budget->getPUtilidades();?>%)</b>
            <div class="Palito"></div>
        </div>
        <div class="PuntosSeparadoresDelSubTotal">
            <b>:</b>
            <b>:</b>
            <b>:</b>
            <b>:</b>
            <div class="Palito"></div>
            <b>:</b>
            <b>:</b>
            <div class="Palito"></div>
        </div>
        <?php
        $personalAndFoodTotalPrice = $personalTotalPrice + $foodTotalPrice;
        $casTotalPrice = $personalTotalPrice * $budget->getPCASalario() / 100;
        $generalTotalPrice = $materialsTotalPrice + $equipmentTotalPrice + $personalAndFoodTotalPrice + $casTotalPrice;
        $utilityTotalPrice = $generalTotalPrice * $budget->getPUtilidades() / 100;
        $totalPrice = $generalTotalPrice + $utilityTotalPrice;
        ?>
        <div class="PreciosDeSubTotal_BS TARight">
            <p><?php echo number_format($materialsTotalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <p><?php echo number_format($equipmentTotalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <p><?php echo number_format($personalAndFoodTotalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <p><?php echo number_format($casTotalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <div class="Palito"></div>
            <p class="Coloreado"><?php echo number_format($generalTotalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <p class=""><?php echo number_format($utilityTotalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <div class="Palito"></div>
        </div>
        <div class="PreciosDeSubTotal_USD TARight">
            <p><?php echo number_format($materialsTotalPrice, 2, '.', '');?>$</p>
            <p><?php echo number_format($equipmentTotalPrice, 2, '.', '');?>$</p>
            <p><?php echo number_format($personalAndFoodTotalPrice, 2, '.', '');?>$</p>
            <p><?php echo number_format($casTotalPrice, 2, '.', '');?>$</p>
            <div class="Palito"></div>
            <p class="Coloreado"><?php echo number_format($generalTotalPrice, 2, '.', '');?>$</p>
            <p class=""><?php echo number_format($utilityTotalPrice, 2, '.', '');?>$</p>
            <div class="Palito"></div>
        </div>
        <div class="EspacioDelTotal">
            <b>Total :</b>
            <p class="TotalBS Coloreado"><?php echo number_format($totalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <p class="TotalUSD Coloreado"><?php echo number_format($totalPrice, 2, '.', '');?>$</p>
        </div>
    </div>
    <br>
    <div style="page-break-after:always;"></div>

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
    <b class="NombreDeLaHoja">COTIZACIÓN N° <?php echo zerofill($budget->getId(), 7);?> :</b>
    <div class="aber1">
        <div class="EspacioDeCliente">
            <b class="TituloCentrado">Cliente</b>
            <div class="TituloDelDato">
                <b>RIF</b>
                <b>Razón social</b>
                <b>Dirección</b>
            </div>
            <div class="PuntosSeparadores">
                <b>:</b>
                <b>:</b>
                <b>:</b>
            </div>
            <div class="RespuestaDelDato">
                <span><?php echo (isset($customer)? $customer->getDocType().'-'.zerofill($customer->getId(), 9):'<span style="color: gray;">No especificó ningún cliente para esta cotización</span>');?></span>
                <span><?php echo (isset($customer)? $customer->getName():'');?></span>
                <span style="height: 30px;"><?php echo (isset($customer)? $customer->getAddress():'');?></span>
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
                <span><?php echo (empty($budget->getCreationDate())? '<span style="color:gray;"> - - - - - - - -</span>':$budget->getCreationDate());?></span>
                <span><?php echo (empty($budget->getUpdatedDate())? '<span style="color:gray;"> - - - - - - - -</span>':$budget->getUpdatedDate());?></span>
                <span><?php echo (empty($budget->getExpireDateReverse())? '<span style="color:gray;"> - - - - - - - -</span>':$budget->getExpireDateReverse());?></span>
            </div>
        </div>
    </div>
    <br>
    <?php echo (empty($budget->getRequestNumber())? '':'<div class="EspacioDeTitulo">N° de petición: '.$budget->getRequestNumber().'</div>');?>
    
    <div style="font-size: 14px;">Después de un cordial saludo, nos es grato dirigirnos a ustedes con la finalidad de someter a consideración la cotización referente su petición.</div>
    <br>
    <div class="TasaAvisito">Tasa de cambio comprendida: <?php echo $tasa.$localCurrencySymbol;?></div>
    <div class="Tabla TablaDeServicio">
        <div class="TituloDeTabla">Servicio</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID_cotiMixto">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre_cotiMixto">Descripción</span>
            <span class="NombreDeTituloDeTabla ColumnaCantidad_cotiMixto">Cantidad</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio" style="font-size: 12px;">Precio U. <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total <?php echo $localCurrencySymbol;?></span>
            <span class="NombreDeTituloDeTabla ColumnaPrecioUSD">Precio U. $</span>
            <span class="NombreDeTituloDeTabla ColumnaTotalUSD">Total $</span>
        </div>
        <div class="EspacioDeRows">
            <div class="Row rowlarguito">
                <span class="CeldaDeTabla ColumnaID_cotiMixto TACenter"><?php echo zerofill($budget->getId(), 7);?></span>
                <span class="CeldaDeTabla ColumnaNombre_cotiMixto"><?php echo $budget->getName();?></span>
                <span class="CeldaDeTabla ColumnaCantidad_cotiMixto TACenter"><?php echo $budgetQuantity;?></span>
                <span class="CeldaDeTabla ColumnaPrecio TARight" style="font-size: 12px;"><?php echo number_format($totalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></span>
                <span class="CeldaDeTabla ColumnaTotal TARight"><?php echo number_format($totalPrice * $budgetQuantity * $tasa, 2, '.', '').$localCurrencySymbol;?></span>
                <span class="CeldaDeTabla ColumnaPrecioUSD TARight"><?php echo number_format($totalPrice, 2, '.', '');?>$</span>
                <span class="CeldaDeTabla ColumnaTotalUSD TARight"><?php echo number_format($totalPrice * $budgetQuantity, 2, '.', '');?>$</span>
            </div>
        </div>
    </div>
    <?php
    $budgetSubTotalPrice = $totalPrice * $budgetQuantity;
    $ivaPrice = $budgetSubTotalPrice * $budget->getPIVA() / 100;
    $totalTotal = $budgetSubTotalPrice + $ivaPrice;
    ?>
    <div class="EspacioDeLosTotales2 TARight">
        <div class="SubTotales PalitoAbajo">
            <b>Sub Total</b>
            <b>I.V.A. (<?php echo $budget->getPIVA();?>%)</b>
            <div class="Palito"></div>
        </div>
        <div class="PuntosSeparadoresDelSubTotal PalitoAbajo">
            <b>:</b>
            <b>:</b>
            <div class="Palito"></div>
        </div>
        <div class="PreciosDeSubTotal_BS PalitoAbajo TARight">
            <p><?php echo number_format($budgetSubTotalPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <p><?php echo number_format($ivaPrice * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
        </div>
        <div class="PreciosDeSubTotal_USD PalitoAbajo TARight">
            <p><?php echo number_format($budgetSubTotalPrice, 2, '.', '');?>$</p>
            <p><?php echo number_format($ivaPrice, 2, '.', '');?>$</p>
        </div>
        <div class="EspacioDelTotal">
            <b>Total :</b>
            <p class="TotalBS Coloreado"><?php echo number_format($totalTotal * $tasa, 2, '.', '').$localCurrencySymbol;?></p>
            <p class="TotalUSD Coloreado"><?php echo number_format($totalTotal, 2, '.', '');?>$</p>
        </div>
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
    
    $dompdf->stream("Estructura de costos y cotizacion #".$budget->getId().".pdf", array('Attachment' => false));
    
?>