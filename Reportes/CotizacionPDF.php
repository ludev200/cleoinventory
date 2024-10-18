<?php
    //Verificar si hay sesion
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

    //Reviso si hay problemas
    $Problema = ""; 
    if(isset($_GET['id'])){
        $ResultadoDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE `id` = ".$_GET['id']);
        if(empty($ResultadoDeConsulta)){
            $Problema = "1";
            $Error = "404";
        }else{
            if(isset($_GET['modo'])){
                if($_GET['modo'] != 1 && $_GET['modo'] != 2){
                    $Problema = "4";
                    $Error = "406";
                }else{
                    if($_GET['modo'] == 2 && !isset($_GET['tasa'])){
                        $Problema = "5";
                        $Error = "409";
                    }else{
                        if($_GET['modo'] == 2 && empty($_GET['tasa'])){
                            $Problema = "6";
                            $Error = "406";
                        }else{
                            if($_GET['modo'] == 2 && !is_numeric($_GET['tasa'])){
                                $Problema = "8";
                                $Error = "406";
                            }else{
                                if($_GET['modo'] == 2 && intval($_GET['tasa']) <= 0){
                                    $Problema = "7";
                                    $Error = "406";
                                }
                            }
                        }
                    }
                }
            }else{
                $Problema = "3";
                $Error = "409";
            }
        }
    }else{
        $Problema = "2";
        $Error = "404";
    }

    

    //Si hay problema envio a pantalla de error; si no, continuo
    if(!empty($Problema)){
        header('Location: ../Error.php?error='.$Error.'&desc='.$Problema);
    }else{
        $Tasa = floatval($_GET['tasa']);
        $CotizacionAMostrar = new cotizacion($_GET['id']);
        $DatosDeLaCotizacion = $CotizacionAMostrar->ObtenerDatos();

        //Si tiene cliente cargo sus datos
        if(!empty($DatosDeLaCotizacion['cedulaCliente'])){
            $Cliente = new cliente($DatosDeLaCotizacion['cedulaCliente']);
            $DatosDelCliente = $Cliente->ObtenerDatos();
        }

        //Preparamos las fechas a mostrar
        if(!empty($DatosDeLaCotizacion['creado'])){
            $pedazos = explode(' ', $DatosDeLaCotizacion['creado']);
            $pedazos = explode('-', $pedazos[0]);
            $FechaCreado = $pedazos[2].'/'.$pedazos[1].'/'.$pedazos[0];
        }else{
            $FechaCreado = '<span style="color: #bbb;">- - - - - - - - -</span>';
        }

        if(!empty($DatosDeLaCotizacion['modificado'])){
            $pedazos = explode(' ', $DatosDeLaCotizacion['modificado']);
            $pedazos = explode('-', $pedazos[0]);
            $FechaModificado = $pedazos[2].'/'.$pedazos[1].'/'.$pedazos[0];
        }else{
            $FechaModificado = '<span style="color: #bbb;">- - - - - - - - -</span>';
        }

        if(!empty($DatosDeLaCotizacion['fechaExpiracion'])){
            $pedazos = explode(' ', $DatosDeLaCotizacion['fechaExpiracion']);
            $pedazos = explode('-', $pedazos[0]);
            $FechaVence = $pedazos[2].'/'.$pedazos[1].'/'.$pedazos[0];
        }else{
            $FechaVence = '<span style="color: #bbb;">- - - - - - - - -</span>';
        }

        //Consulto los productos de la cotizacion
        $MaterialesAMostrar = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` WHERE ( (`idCotizacion` = ".$_GET['id'].") AND 
        ( (SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 1 ) )");
        $MaquinasAMostrar = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` WHERE ( (`idCotizacion` = ".$_GET['id'].") AND 
        ( (SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 2 ) )");
        $ManosAMostrar = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` WHERE ( (`idCotizacion` = ".$_GET['id'].") AND 
        ( ((SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 3) OR ((SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 4)) );");
        

        //Consulto los datos del sistema
        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'RifDeEmpresa';");
        $RifDeEmpresa = ((empty($DatosDelSistema))?'X-00000000-0':$DatosDelSistema[0]['valor']);
        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'NombreDeEmpresa';");
        $NombreDeEmpresa = ((empty($DatosDelSistema))?'Desconocido':$DatosDelSistema[0]['valor']);
        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'DireccionDeEmpresa';");
        $DireccionDeEmpresa = ((empty($DatosDelSistema))?'Desconocido':$DatosDelSistema[0]['valor']);
        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'CiudadEstadoYZonaPos';");
        $CiudadEstadoYZonaPos = ((empty($DatosDelSistema))?'Desconocido 0000':$DatosDelSistema[0]['valor']);
        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'TelefonoDeEmpresa';");
        $TelefonoDeEmpresa = ((empty($DatosDelSistema))?'0000-0000000':$DatosDelSistema[0]['valor']);
        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'CorreoDeEmpresa';");
        $CorreoDeEmpresa = ((empty($DatosDelSistema))?'Desconocido':$DatosDelSistema[0]['valor']);
        $DatosDelSistema = $BaseDeDatos->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'SimboloMonedaNacional';");
        $SimboloMonedaNaciona = ((empty($DatosDelSistema))? 'Bs':$DatosDelSistema[0]['valor']);

        $DatosAMostrar = array(
            'idCot' => $DatosDeLaCotizacion['id'],
            'rif' => ((empty($DatosDeLaCotizacion['cedulaCliente']))?'<i style="color: #bbb;">No especificó ningún cliente para esta cotización</i>':$DatosDelCliente['tipoDeDocumento']." - ".$DatosDelCliente['rif']),
            'razonSocial' => ((empty($DatosDeLaCotizacion['cedulaCliente']))?'':$DatosDelCliente['nombre']),
            'direccion' => ((empty($DatosDeLaCotizacion['cedulaCliente']))?'':$DatosDelCliente['direccion']),
            'creado' => $FechaCreado,
            'modificado' => $FechaModificado,
            'fechaExpiracion' => $FechaVence,
            'nombre' => $DatosDeLaCotizacion['nombre'],
            'NumeroDePeticion' => $DatosDeLaCotizacion['NumeroDePeticion'],
            
            'pUtilidades' => $DatosDeLaCotizacion['pUtilidades'],
            'pIVA' => $DatosDeLaCotizacion['pIVA'],
            'pCASalario' => $DatosDeLaCotizacion['pCASalario'],
            'RifDeEmpresa' => $RifDeEmpresa,
            'NombreDeEmpresa' => $NombreDeEmpresa,
            'DireccionDeEmpresa' => $DireccionDeEmpresa,
            'CiudadEstadoYZonaPos' => $CiudadEstadoYZonaPos,
            'TelefonoDeEmpresa' => $TelefonoDeEmpresa,
            'CorreoDeEmpresa' => $CorreoDeEmpresa,
            'SimboloMoneda' => (($_GET['modo']==1)?'$':$SimboloMonedaNaciona)
        );
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

    


    ob_start();
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $DatosAMostrar['nombre'];?></title>
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
    <b class="NombreDeLaHoja">ESTRUCTURA DE COSTOS N° <?php echo zerofill($_GET['id'], 7);?> :</b>
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
                <span><?php echo $DatosAMostrar['rif'];?></span>
                <span><?php echo $DatosAMostrar['razonSocial'];?></span>
                <span style="height: 30px;"><?php echo $DatosAMostrar['direccion'];?></span>
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
                <span><?php echo $DatosAMostrar['creado'];?></span>
                <span><?php echo $DatosAMostrar['modificado'];?></span>
                <span><?php echo $DatosAMostrar['fechaExpiracion'];?></span>
            </div>
        </div>
    </div>
    <br>
    <div class="EspacioDeTitulo"><?php echo $DatosAMostrar['nombre'];?></div>
    <div style="text-align: center; color: gray;"><?php echo (empty($DatosAMostrar['NumeroDePeticion'])? '':'N° de petición: '.$DatosAMostrar['NumeroDePeticion']);?></div>
    
    <br>
    <div class="Tabla">
        <div class="TituloDeTabla">Materiales</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre">Nombre</span>
            <span class="NombreDeTituloDeTabla ColumnaUnidad">UM</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio">Precio U.</span>
            <span class="NombreDeTituloDeTabla ColumnaCantidad">Cantidad</span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total</span>
        </div>
        <div class="EspacioDeRows">
            <?php
                $CostoDeMateriales = 0;
                if(empty($MaterialesAMostrar)){
                    echo '
                    <div class="Row">
                        <div class="RowVacia">Esta cotización no incluye materiales</div>
                    </div>
                    ';
                }else{
                    foreach($MaterialesAMostrar as $Producto){
                        $ProductoAMostrar = new producto($Producto['idProducto']);
                        $DatosDelProducto = $ProductoAMostrar->ObtenerDatos();
                        echo '
                        <div class="Row">
                            <span class="CeldaDeTabla ColumnaID TACenter">'.$Producto['idProducto'].'</span>
                            <span class="CeldaDeTabla ColumnaNombre">'.$DatosDelProducto['nombre'].'</span>
                            <span class="CeldaDeTabla ColumnaUnidad TACenter">'.$DatosDelProducto['simboloConEstiloUM'].'</span>
                            <span class="CeldaDeTabla ColumnaPrecio TARight">'.number_format($Producto['precioUnitario'] * $Tasa, 2, '.', "").$DatosAMostrar['SimboloMoneda'].'</span>
                            <span class="CeldaDeTabla ColumnaCantidad TACenter">'.$Producto['cantidad'].'</span>
                            <span class="CeldaDeTabla ColumnaTotal TARight">'.number_format($Producto['precioMultiplicado'] * $Tasa, 2, '.', "").$DatosAMostrar['SimboloMoneda'].'</span>
                        </div>
                        ';
                        $CostoDeMateriales = $CostoDeMateriales + number_format($Producto['precioMultiplicado'], 2);
                        
                    }
                    $CostoDeMateriales = $CostoDeMateriales * $Tasa;
                }
            ?>
        </div>
        <div class="TituloDeTabla">Equipamiento</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre">Nombre</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio2">Precio U.</span>
            <span class="NombreDeTituloDeTabla ColumnaDepreciacion">Depreciación</span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total</span>
        </div>
            <?php
                $CostoDeEquipo = 0;
                if(empty($MaquinasAMostrar)){
                    echo '
                    <div class="Row">
                        <div class="RowVacia">Esta cotización no incluye equipamiento</div>
                    </div>
                    ';
                }else{
                    foreach($MaquinasAMostrar as $Producto){
                        $ProductoAMostrar = new producto($Producto['idProducto']);
                        $DatosDelProducto = $ProductoAMostrar->ObtenerDatos();
                        echo '
                        <div class="Row">
                            <span class="CeldaDeTabla ColumnaID TACenter">'.$Producto['idProducto'].'</span>
                            <span class="CeldaDeTabla ColumnaNombre">'.$DatosDelProducto['nombre'].'</span>
                            <span class="CeldaDeTabla ColumnaPrecio2 TARight">'.number_format($Producto['precioUnitario'] * $Tasa, 2, '.', "").$DatosAMostrar['SimboloMoneda'].'</span>
                            <span class="CeldaDeTabla ColumnaDepreciacion TACenter">'.$Producto['cantidad'].'</span>
                            <span class="CeldaDeTabla ColumnaTotal TARight">'.number_format($Producto['precioMultiplicado'] * $Tasa, 2, '.', "").$DatosAMostrar['SimboloMoneda'].'</span>
                        </div>
                        ';
                        $CostoDeEquipo = $CostoDeEquipo + number_format($Producto['precioMultiplicado'], 2);
                        
                    }
                    $CostoDeEquipo = $CostoDeEquipo * $Tasa;
                }
            ?>
        
        <div class="TituloDeTabla">Mano de obra</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre">Nombre</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio2">Precio U.</span>
            <span class="NombreDeTituloDeTabla ColumnaCantidad2">Cantidad</span>
            <span class="NombreDeTituloDeTabla ColumnaDias">Días</span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total</span>
        </div>
            <?php
            $CostoDeManoDeObra = 0;
            $CostoDeManoSoloMano = 0;
            if(empty($ManosAMostrar)){
                echo '
                <div class="Row">
                    <div class="RowVacia">Esta cotización no incluye mano de obra</div>
                </div>
                ';
            }else{
                
                foreach($ManosAMostrar as $Producto){
                    $ProductoAMostrar = new producto($Producto['idProducto']);
                    $DatosDelProducto = $ProductoAMostrar->ObtenerDatos();
                    $CantidadYDias = explode('.', $Producto['cantidad']);
                    echo '
                    <div class="Row">
                        <span class="CeldaDeTabla ColumnaID TACenter">'.$Producto['idProducto'].'</span>
                        <span class="CeldaDeTabla ColumnaNombre">'.$DatosDelProducto['nombre'].'</span>
                        <span class="CeldaDeTabla ColumnaPrecio2 TARight">'.number_format($Producto['precioUnitario'] * $Tasa, 2, '.', "").$DatosAMostrar['SimboloMoneda'].'</span>
                        <span class="CeldaDeTabla ColumnaCantidad2 TACenter">'.$CantidadYDias[0].' '.$DatosDelProducto['simboloConEstiloUM'].'</span>
                        <span class="CeldaDeTabla ColumnaDias TACenter">'.$CantidadYDias[1].'</span>
                        <span class="CeldaDeTabla ColumnaTotal TARight">'.number_format($Producto['precioMultiplicado'] * $Tasa, 2, '.', "").$DatosAMostrar['SimboloMoneda'].'</span>
                    </div>
                    ';

                    if($DatosDelProducto['idCategoria'] == 3){
                        $CostoDeManoSoloMano = $CostoDeManoSoloMano + number_format($Producto['precioMultiplicado'], 2);
                    }
                    

                    $CostoDeManoDeObra = $CostoDeManoDeObra + number_format($Producto['precioMultiplicado'], 2);
                    
                }
                $CostoDeManoDeObra = $CostoDeManoDeObra * $Tasa;
                $CostoDeManoSoloMano = $CostoDeManoSoloMano * $Tasa;
            }
            ?>
        </div>
    </div>
    <div class="EspacioDeLosTotales">
        <img class="intentodemejoracion" src="http://<?php echo $_SERVER['HTTP_HOST'].'/CleoInventory/Imagenes/Sistema/'.$public->getCompanyBossSing();?>" alt="">
        <img class="SelloXDDD" src="http://<?php echo $_SERVER['HTTP_HOST'].'/CleoInventory/Imagenes/Sistema/'.$public->getCompanySeal();?>" alt="">
        <div class="SubTotales">
            <b>Costo de Materiales</b>
            <b>Costo de Equipo</b>
            <b>Costo de Mano de obra</b>
            <b>Asociado al salario (<?php echo $DatosAMostrar['pCASalario'];?>%)</b>
            <div class="Palito"></div>
            <b>Costo general de productos</b>
            <b>Utilidades (<?php echo $DatosAMostrar['pUtilidades'];?>%)</b>
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
            $CostoAsociadoAlSalario = $CostoDeManoSoloMano * $DatosAMostrar['pCASalario'] / 100;
            $CostoGeneral = $CostoDeMateriales + $CostoDeEquipo + $CostoDeManoDeObra + $CostoAsociadoAlSalario;
            $Utilidades = $CostoGeneral * $DatosAMostrar['pUtilidades'] / 100;
            $SubTotal = $CostoGeneral + $Utilidades;
            $SubTotalMultiplicadoPorCantidad = $SubTotal * $cantidadACotizar;
            $IVA = $SubTotalMultiplicadoPorCantidad * $DatosAMostrar['pIVA'] / 100;
            $Total = $SubTotalMultiplicadoPorCantidad + $IVA;
        ?>
        <div class="PreciosDeSubTotal TARight">
            <p><?php echo number_format($CostoDeMateriales, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <p><?php echo number_format($CostoDeEquipo, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <p><?php echo number_format($CostoDeManoDeObra, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <p><?php echo number_format($CostoAsociadoAlSalario, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <div class="Palito"></div>
            <p class="Coloreado"><?php echo number_format($CostoGeneral, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <p><?php echo number_format($Utilidades, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <div class="Palito"></div>
        </div>
        <div class="EspacioDelTotal">
            <b class="">Total:</b>
            <p class="Coloreado"><?php echo number_format($SubTotal, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
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
    <b class="NombreDeLaHoja">COTIZACIÓN N° <?php echo zerofill($_GET['id'], 7);?> :</b>
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
                <span><?php echo $DatosAMostrar['rif'];?></span>
                <span><?php echo $DatosAMostrar['razonSocial'];?></span>
                <span style="height: 30px;"><?php echo $DatosAMostrar['direccion'];?></span>
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
                <span><?php echo $DatosAMostrar['creado'];?></span>
                <span><?php echo $DatosAMostrar['modificado'];?></span>
                <span><?php echo $DatosAMostrar['fechaExpiracion'];?></span>
            </div>
        </div>
    </div>
    <?php
    $SubTotalMultiplicadoPorCantidad = $SubTotal*$cantidadACotizar;
    ?>
    <br>
    <div class="Tabla">
        <div class="TituloDeTabla">Servicio</div>
        <div class="NombresDeColumnas">
            <span class="NombreDeTituloDeTabla ColumnaID2">ID</span>
            <span class="NombreDeTituloDeTabla ColumnaNombre3">Descripción</span>
            <span class="NombreDeTituloDeTabla ColumnaUnidad">UM</span>
            <span class="NombreDeTituloDeTabla ColumnaPrecio">Precio U.</span>
            <span class="NombreDeTituloDeTabla ColumnaCantidad">Cantidad</span>
            <span class="NombreDeTituloDeTabla ColumnaTotal">Total</span>
        </div>
        <div class="EspacioDeRows">
            <div class="Row">
                <span class="CeldaDeTabla ColumnaID2 TACenter"><?php echo $DatosAMostrar['idCot'];?></span>
                <span class="CeldaDeTabla ColumnaNombre3"><?php echo $DatosAMostrar['nombre'];?></span>
                <span class="CeldaDeTabla ColumnaUnidad TACenter">U</span>
                <span class="CeldaDeTabla ColumnaPrecio TARight"><?php echo number_format($SubTotal, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></span>
                <span class="CeldaDeTabla ColumnaCantidad TACenter"><?php echo $cantidadACotizar;?></span>
                <span class="CeldaDeTabla ColumnaTotal TARight"><?php echo (number_format($SubTotalMultiplicadoPorCantidad, 2, '.', "") * 1).$DatosAMostrar['SimboloMoneda'];?></span>
            </div>
        </div>
    </div>
    <div class="EspacioDeLosTotales2">
        <div class="TitulosATotalizar">
            <b>Sub Total</b>
            <b>I.V.A. (<?php echo $DatosAMostrar['pIVA'];?>%)</b>
            <div class="Palito2"></div>
        </div>
        <div class="DosPuntosxd">
            <b>:</b>
            <b>:</b>
            <div class="Palito2"></div>
        </div>
        <div class="PreciosATotalizar TARight">
            <p><?php echo number_format($SubTotalMultiplicadoPorCantidad, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <p><?php echo number_format($IVA, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            <div class="Palito2"></div>
        </div>
        <div class="TotalDeLaCot">
            <div class="TituloTotal">
                <b>Total:</b>
            </div>
            <div class="PrecioTotal Coloreado">
                <p><?php echo number_format($Total, 2, '.', "").$DatosAMostrar['SimboloMoneda'];?></p>
            </div>
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
    
    $dompdf->stream("Estructura de costos y cotizacion #".$DatosAMostrar['idCot'].".pdf", array('Attachment' => false));
    
?>



