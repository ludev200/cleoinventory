<?php
$GLOBALS['nombreDelSoftware'] = "CLEO INVENTORY";
$GLOBALS['nombreCorto'] = "CLEO";


function clean($string){
    return htmlentities(trim($string));
}

function zerofill($string, $max){
    for ($i=0; $max > strlen($string); $i++) { 
        $string = '0'.$string;
    }
    return $string;
}

///////////COMPRA///////////
class compra{
    private $id;
    private $nombre;
    private $fechaExpiracion;
    private $idEstado;
    private $idAjusteDeEntradaEnInventario;

    public function ConfirmarCompra($DatosAGuardar){
        $ListaDeErrores = array();
        $BaseDeDatos = new conexion();

        
        //Empezamos con la validacion de datos
        if(empty($DatosAGuardar['NombreDeLaCompra'])){
            $ListaDeErrores [] = 'Descripción de la compra está vacío¿';
        }

        if(!empty($DatosAGuardar['CompraImportada'])){
            if(is_numeric($DatosAGuardar['CompraImportada'])){
                $ConsultaDeOrdenDeCompraEnEspera = $BaseDeDatos->consultar('SELECT * FROM `ordenesdecompra` WHERE (`id` = '.$DatosAGuardar['CompraImportada'].' AND `idEstado` = 63)');

                if(empty($ConsultaDeOrdenDeCompraEnEspera)){
                    $ListaDeErrores [] = "No se encontró la orden de compra en espera de ID '".$DatosAGuardar['CompraImportada']."'";
                }
            }else{
                $ListaDeErrores [] = "El ID '".$DatosAGuardar['CompraImportada']."' de la orden de compra importada no es válido¿";
            }
        }

        
        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            if(empty($DatosAGuardar['ListaDeAlmacenaje'])){
                $ListaDeErrores [] = 'No se encontraron productos a almacenar¿';
            }else{
                foreach(explode('¿', $DatosAGuardar['ListaDeAlmacenaje']) as $AlmacenConSusCosas){
                    $IDAlm_Cosas = explode(':', $AlmacenConSusCosas);

                    if(count($IDAlm_Cosas) == 2){
                        if(is_numeric($IDAlm_Cosas[0])){
                            $ConsultaDeAlmacen = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE (`id` = ".$IDAlm_Cosas[0]." AND `idEstado` = 51)");

                            if(empty($ConsultaDeAlmacen)){
                                $ListaDeErrores [] = "No se encontró el almacén de ID '".$IDAlm_Cosas[0]."'¿";
                            }else{
                                //Ya que veo que el almacen está bueno, empiezo a ver los productos
                                foreach(explode(',', $IDAlm_Cosas[1]) as $ProdXCant){
                                    $Array_ProdXCant = explode('x', $ProdXCant);
                                    if(count($Array_ProdXCant) == 2){
                                        if(is_numeric($Array_ProdXCant[0])){
                                            $ConsultaDelProducto = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE (`id` = ".$Array_ProdXCant[0]." AND (`idEstado` = 1 OR `idEstado` = 2 OR `idEstado` = 3))");

                                            if(empty($ConsultaDelProducto)){
                                                $ListaDeErrores [] = "No se encontró el producto de ID '".$Array_ProdXCant['0']."'¿";
                                            }else{
                                                if(is_numeric($Array_ProdXCant[1])){
                                                    if($ConsultaDelProducto[0]['idCategoria'] == 1){
                                                        $XD = explode('.', $Array_ProdXCant[1]);

                                                        if(count($XD) == 2){
                                                            $ListaDeErrores [] = "La cantidad '".$Array_ProdXCant[1]."' del producto de ID '".$Array_ProdXCant[0]."' no puede ser decimal¿";
                                                        }
                                                    }
                                                }else{
                                                    $ListaDeErrores [] = "La cantidad '".$Array_ProdXCant[1]."' del producto de ID ".$Array_ProdXCant[0]." del almacén de ID ".$IDAlm_Cosas[0]." es inválida¿";
                                                }
                                            }
                                        }else{
                                            $ListaDeErrores [] = "El ID '".$Array_ProdXCant[0]."' de un producto del almacén de ID ".$IDAlm_Cosas[0]." es inválido¿";
                                        }
                                    }else{
                                        $ListaDeErrores [] =  "Un producto del almacén ".$IDAlm_Cosas[0]." tiene un formato inválido (".$ProdXCant.")¿";
                                    }
                                }
                            }
                        }else{
                            $ListaDeErrores [] = "La ID '".$IDAlm_Cosas[0]."' de almacén no es válida¿";
                        }
                    }else{
                        $ListaDeErrores [] = "El formato del almacén '".$IDAlm_Cosas[0]."' no es válido (".$AlmacenConSusCosas.")¿";
                    }
                }
            }
        }

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //En este punto está todo validado, procedemos a guardar
            

            $StringEnFormatoParaNuevoAjuste = "";
            foreach(explode('¿', $DatosAGuardar['ListaDeAlmacenaje']) as $AlmacenConSusCosas){
                $Array_AlmacenConSusCosas = explode(':', $AlmacenConSusCosas);
                $ID_Almacen = $Array_AlmacenConSusCosas[0];
                $Cosas = $Array_AlmacenConSusCosas[1];

                foreach(explode(',', $Cosas) as $ProdXCant){
                    $Array_ProdXCant = explode('x', $ProdXCant);
                    $ID_Producto = $Array_ProdXCant[0];
                    $Cantidad = $Array_ProdXCant[1];
                    
                    $CambioPreparado = $ID_Almacen."x$ID_Producto"."x$Cantidad";
                    $StringEnFormatoParaNuevoAjuste = $StringEnFormatoParaNuevoAjuste.((empty($StringEnFormatoParaNuevoAjuste))?'':'¿').$CambioPreparado;
                }
            }

            

            $DatosAUtilizar = array(
                'descripcion' => $DatosAGuardar['NombreDeLaCompra'],
                'CambiosListados' => $StringEnFormatoParaNuevoAjuste
            );

            $debug = array();
            $Auditoria = new historial();
            if(!empty($DatosAGuardar['CompraImportada'])){
                //tiene orden previa
                $BaseDeDatos->ejecutar("UPDATE `ordenesdecompra` SET `idEstado`= 61, `fechaExpiracion` = NULL WHERE `id` = ".$DatosAGuardar['CompraImportada']);
                $ID_OrdenDeCompra = $DatosAGuardar['CompraImportada'];
                
                $Auditoria->CrearNuevoRegistro(2, 8, $ID_OrdenDeCompra, 'Se ha confirmado la orden de compra #'.$ID_OrdenDeCompra);

                $BaseDeDatos->ejecutar("DELETE FROM `cuerpoorden` WHERE `idOrden` = ".$ID_OrdenDeCompra);

                foreach(explode('¿', $DatosAGuardar['InputProductosListados']) as $ProdXCant){
                    $pedazos = explode('x', $ProdXCant);
                    $BaseDeDatos->ejecutar("INSERT INTO `cuerpoorden`(`idProducto`, `idOrden`, `cantidad`) 
                        VALUES (".$pedazos[0].", ".$ID_OrdenDeCompra.", ".$pedazos[1].")");
                }

            }else{
                //no tiene orden previa
                $ID_OrdenDeCompra = $BaseDeDatos->ejecutar("INSERT INTO `ordenesdecompra`(`nombre`, `idEstado`) VALUES ('".$DatosAGuardar['NombreDeLaCompra']."', 61)");
                $pie = explode('¿', $DatosAGuardar['ListaDeAlmacenaje']);
                $cuerpo_id = array();
                $cuerpo_quantity = array();
                foreach($pie as $yqs){
                    $al_st = explode(':', $yqs);
                    
                    foreach(explode(',', $al_st[1]) as $prodXcant){
                        $prod_cant = explode('x', $prodXcant);
                        $idProd = $prod_cant[0];
                        $quant = $prod_cant[1];

                        if(in_array($idProd, $cuerpo_id)){
                            $pos = array_search($idProd, $cuerpo_id);
                            //throw new Exception("aqui tenes ".$cuerpo_quantity[$pos]);
                            $cuerpo_quantity[$pos] = $cuerpo_quantity[$pos] + intval($quant);
                        }else{
                            $cuerpo_id[] = $idProd;
                            $cuerpo_quantity[] = $quant;
                        }

                        
                    }
                    
                }
                
                    
                foreach($cuerpo_id as $key => $idProd){
                    $BaseDeDatos->ejecutar("INSERT INTO `cuerpoorden`(`idProducto`, `idOrden`, `cantidad`) VALUES ($idProd, $ID_OrdenDeCompra, ".$cuerpo_quantity[$key].")");
                }
                
                
                $Auditoria->CrearNuevoRegistro(1, 8, $ID_OrdenDeCompra, 'Se ha creado la compra #'.$ID_OrdenDeCompra);
            }

            $Inventario = new inventario();
            $ID_AjusteDeInventario = $Inventario->CrearNuevoAjusteDeInventario($DatosAUtilizar, 1);

            $BaseDeDatos->ejecutar("UPDATE `ordenesdecompra` SET `idAjusteDeEntrada`= ".$ID_AjusteDeInventario." WHERE `id` = ".$ID_OrdenDeCompra);

            return $ID_OrdenDeCompra;
        }

    }

    public function __construct($idACargar){
        $BaseDeDatos = new conexion();
        $Tiempo = new AsistenteDeTiempo();

        if($idACargar > 0){
            $DatosDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `ordenesdecompra` WHERE `id` = ".$idACargar);
            $DatosACargar = $DatosDeConsulta[0];

            $this->id = $DatosACargar['id'];
            $this->nombre = $DatosACargar['nombre'];
            $this->fechaExpiracion = (($DatosACargar['fechaExpiracion'] == null)?null:$Tiempo->ConvertirFormato($DatosACargar['fechaExpiracion'],'BaseDeDatos', 'MaracayXD'));
            $this->idEstado = $DatosACargar['idEstado'];
            $this->idAjusteDeEntradaEnInventario = $DatosACargar['idAjusteDeEntrada'];
        }
    }

    public function ListarCompras($Filtros){
        $BaseDeDatos = new conexion();
        $ResultadoARetornar = array();

        //Valido los datos
        if($Filtros['idEstado'] != 61 && $Filtros['idEstado'] != 62 && $Filtros['idEstado'] != 63 && $Filtros['idEstado'] != 64){
            $Filtros['idEstado'] = 0;
        }

        //Preparo los SQL
        if(empty($Filtros['descripcion'])){
            $SQLDeDescripcion = "";
        }else{
            $SQLDeDescripcion = "(`id` LIKE '%".$Filtros['descripcion']."%' OR `nombre` LIKE '%".$Filtros['descripcion']."%') AND";
        }
        
        if($Filtros['idEstado'] == 0){
            $SQLDeEstado = "(`idEstado` != 65 AND `idEstado` != 66)";
        }else{
            $SQLDeEstado = "(`idEstado` = ".$Filtros['idEstado'].")";
        }
        
        $SQLDeConsulta = "SELECT * FROM `ordenesdecompra` WHERE(".$SQLDeDescripcion.$SQLDeEstado.")";
        $ConsultaDeCompras = $BaseDeDatos->consultar($SQLDeConsulta);

        foreach($ConsultaDeCompras as $RowResultado){
            $Compra_Objeto = new compra($RowResultado['id']);
            $ResultadoARetornar [] = $Compra_Objeto->ObtenerDatos();
        }

        return $ResultadoARetornar;
    }

    public function ObtenerDatos(){
        $BaseDeDatos = new conexion();
        $ConsultaDeProductosDeCompra = $BaseDeDatos->consultar("SELECT * FROM `cuerpoorden` WHERE `idOrden` = ".$this->id);

        $NroDeProductos = 0;
        $ProductosEnFormato = "";
        foreach($ConsultaDeProductosDeCompra as $ProductoOrdenado){
            $ProductosEnFormato = $ProductosEnFormato.((empty($ProductosEnFormato))?'':'¿').$ProductoOrdenado['idProducto'].'x'.$ProductoOrdenado['cantidad'];
            $NroDeProductos++;
        }

        switch($this->idEstado){
            case 61: $Estado = 'Confirmada'; break;
            case 62: $Estado = 'Rechazada'; break;
            case 63: $Estado = 'En espera'; break;
            case 64: $Estado = 'Vencida'; break;

            default: $Estado = 'Desconocido';
        }

        $Auditoria = new historial();

        if($this->idEstado == 65){
            $FechaCreado = "";
        }else{
            $FechaCreado = $Auditoria->BuscarRegistro(1, 8, $this->id);
            $FechaCreado = $FechaCreado[0];
            $FechaCreado = $FechaCreado['fechaCreacion'];
    
            $Tiempo = new AsistenteDeTiempo();
            $FechaCreado = $Tiempo->ConvertirFormato($FechaCreado,'BaseDeDatosConTiempo', 'MaracayXD');
        }
        
        

        return array(
            'id' => $this->id,
            'nombre' => $this->nombre,
            'fechaExpiracion' => $this->fechaExpiracion,
            'idEstado' => $this->idEstado,
            'idAjusteDeEntradaEnInventario' => $this->idAjusteDeEntradaEnInventario,
            'nroDeProductos' => $NroDeProductos,
            'productosEnFormato' => $ProductosEnFormato,
            'estado' => $Estado,
            'fechaCreacion' => $FechaCreado
        );
    }

    public function Actualizar($DatosNuevos, $NuevoEstado){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = $this->ValidarDatos($DatosNuevos, $NuevoEstado);

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Determino la fecha de expiracion
            if($DatosNuevos['limiteDeTiempo'] == 0 || $NuevoEstado == 65){
                $FechaDeExp = "NULL";
            }else{                
                $FechaActual = date("Y-m-d");
                $FechaDeExp = date("Y-m-d", strtotime($FechaActual."+ ".$DatosNuevos['limiteDeTiempo']." days"));
                $FechaDeExp = "'".$FechaDeExp."'";
            }

            //Actualizo la orden de compra
            $BaseDeDatos->ejecutar("UPDATE `ordenesdecompra` SET 
            `nombre`= '".$DatosNuevos['nombreDeOrden']."',
            `fechaExpiracion`=".$FechaDeExp.",
            `idEstado`=".$NuevoEstado." WHERE `id` = ".$this->id);

            $BaseDeDatos->ejecutar("DELETE FROM `cuerpoorden` WHERE `idOrden` = ".$this->id);

            foreach(explode('¿', $DatosNuevos['ProductosDelAlmacen']) as $ProdXCant){
                $pedazos = explode('x', $ProdXCant);
                $BaseDeDatos->ejecutar("INSERT INTO `cuerpoorden`(`idProducto`, `idOrden`, `cantidad`) VALUES (".$pedazos[0].", ".$this->id.", ".$pedazos[1].")");
            }
        }
    }

    public function Eliminar($Estado){
        $BaseDeDatos = new conexion();

        if($Estado == 65){
            $BaseDeDatos->ejecutar("DELETE FROM `cuerpoorden` WHERE `idOrden` = ".$this->id);
            $BaseDeDatos->ejecutar("DELETE FROM `ordenesdecompra` WHERE `id` = ".$this->id);
        }
    }

    public function ValidarDatos($DatosAValidar, $EstadoATenerEnCuenta){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = array();

        if(empty($DatosAValidar['nombreDeOrden'])){
            $ListaDeErrores [] = "Nombre no puede estar vacío¿";
        }

        if(empty($DatosAValidar['ProductosDelAlmacen'])){
            $ListaDeErrores [] = "No se ha añadido ningún producto a la lista¿";
        }

        if(!is_numeric($DatosAValidar['limiteDeTiempo'])){
            $ListaDeErrores [] = "El limite de tiempo no es numérico¿";
        }else{
            $Redondeado = ceil($DatosAValidar['limiteDeTiempo']);
            $NoRedondenado = $DatosAValidar['limiteDeTiempo'];
            if($Redondeado != $NoRedondenado){
                $ListaDeErrores [] = "El limite de tiempo no es entero¿";
            }
        }
        

        
        if(!empty($ListaDeErrores)){
            return $ListaDeErrores;
        }else{
            $Array_IDsDeProductos = array();
            foreach(explode('¿', $DatosAValidar['ProductosDelAlmacen']) as $ProdXCant){
                
                $Valores = explode('x', $ProdXCant);
                

                

                if(count($Valores) != 2){
                    $ListaDeErrores [] = 'Una orden en la lista de productos tiene un formato inválido ('.$ProdXCant.')¿';
                }else{
                    //Ya verificado que cada Cambio tiene tres pedazos, procedo a verificar cada pedazo.
                    //ID de producto
                    if(empty($Valores[0])){
                        $ListaDeErrores [] = 'No se encontró la ID de un producto en la lista de cantidad '.$Valores[1].'¿';
                    }else{
                        if(!is_numeric($Valores[0])){
                            $ListaDeErrores [] = 'La ID de producto '.$Valores[0].' no es válida¿';
                        }
                    }


                    //Cantidad
                    if(empty($Valores[1])){
                        $ListaDeErrores [] = 'No se encontró la cantidad a comprar del producto de ID '.$Valores[0].'¿';
                    }else{
                        if(!is_numeric($Valores[1])){
                            $ListaDeErrores [] = 'La cantidad del producto de ID '.$Valores[0].' no es válida¿';
                        }
                    }   

                }
            
                $Array_IDsDeProductos [] = $Valores[0];
                
            }

            //comprobar la existencia de cada vaina en el array ese
            foreach($Array_IDsDeProductos as $ID_Prod){
                $ConsultaDeProducto = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE (`id` = ".$ID_Prod." AND (`idEstado` = 1 OR `idEstado` = 2 OR `idEstado` = 3))");
                if(empty($ConsultaDeProducto)){
                    $ListaDeErrores [] = "El producto de ID ".$ID_Prod." no existe¿";
                }
            }

            return $ListaDeErrores;
        }
    }

    public function CrearNuevo($DatosAGuardar, $EstadoAGuardar){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = $this->ValidarDatos($DatosAGuardar, $EstadoAGuardar);

        

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Determino la fecha de expiracion
            if($DatosAGuardar['limiteDeTiempo'] == 0 || $EstadoAGuardar == 65){
                $FechaDeExp = "NULL";
            }else{                
                $FechaActual = date("Y-m-d");
                $FechaDeExp = date("Y-m-d", strtotime($FechaActual."+ ".$DatosAGuardar['limiteDeTiempo']." days"));
                $FechaDeExp = "'".$FechaDeExp."'";
            }
            //Creo la orden de compra
            $ID_OrdenCreada = $BaseDeDatos->ejecutar("INSERT INTO `ordenesdecompra`(`nombre`, `fechaExpiracion`, `idEstado`) VALUES ('".$DatosAGuardar['nombreDeOrden']."', ".$FechaDeExp.", ".$EstadoAGuardar.")");

            //Agrego los productos a la orden de compra
            foreach(explode('¿', $DatosAGuardar['ProductosDelAlmacen']) as $ProdXCant){
                $pedazos = explode('x', $ProdXCant);
                $BaseDeDatos->ejecutar("INSERT INTO `cuerpoorden`(`idProducto`, `idOrden`, `cantidad`) VALUES (".$pedazos[0].", ".$ID_OrdenCreada.", ".$pedazos[1].")");
            }

            
            //Creo registro en historial
            if($EstadoAGuardar != 65){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 8, $ID_OrdenCreada, 'Se ha creado la orden de compra #'.$ID_OrdenCreada);
            }

            
        }        
    }

    
}

///////////INVENTARIO///////////
class inventario {
    public function ObtenerDatos(){
        $BaseDeDatos = new conexion();
        $ConsultaDeDisponibles = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `productos` WHERE `idEstado` = 1");
        $ConsultaDeEnAlerta = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `productos` WHERE `idEstado` = 2");
        $ConsultaDeAgotados = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `productos` WHERE `idEstado` = 3");
        
        return array(
            'ProductosDisponibles' => $ConsultaDeDisponibles[0][0],
            'ProductosEnAlerta' => $ConsultaDeEnAlerta[0][0],
            'ProductosAgotados' => $ConsultaDeAgotados[0][0]
        );
    }

    public function ObtenerCompraRecomendada(){
        $BaseDeDatos = new conexion();
        $Respuesta = "";
        $Filtros = array(
            'descripcion' => '',
            'estado' => '0'
        );

        $ProductosDelInventario = $this->ConsultarProductos($Filtros);

        foreach($ProductosDelInventario as $ProductoEnInv){
            if($ProductoEnInv['idEstado'] > 1){
                $Multiplacador = (($ProductoEnInv['idEstado'] == 3)?6:4);
                $CantidadRecomenda = ($ProductoEnInv['nivelDeAlerta'] * $Multiplacador);
                $FormatoListo = $ProductoEnInv['id'].'x'.$CantidadRecomenda;

                $Respuesta = $Respuesta.((empty($Respuesta))?$FormatoListo:'¿'.$FormatoListo);
            }
        }

        return $Respuesta;
    }

    public function ObtenerAlmacenesConProductos($MostrarNoAlmacenados){
        $BaseDeDatos = new conexion();

        if($MostrarNoAlmacenados){
            $ConsultaDeAlmacenes = $BaseDeDatos->consultar("SELECT DISTINCT `idAlmacen`, `almacenes`.* 
            FROM `inventario` INNER JOIN `almacenes` ON `inventario`.`idAlmacen` = `almacenes`.`id`;");

        }else{
            $ConsultaDeAlmacenes = $BaseDeDatos->consultar("SELECT DISTINCT `idAlmacen`, `almacenes`.* 
            FROM `inventario` INNER JOIN `almacenes` ON `inventario`.`idAlmacen` = `almacenes`.`id` WHERE `existencia` >= 0;");
        }

        $ConsultaDeAlmacenes = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE (`idEstado` = 51 OR `idEstado` = 52) ORDER BY `predeterminado` DESC");


        $AlmacenesConProductos = array();
        foreach($ConsultaDeAlmacenes as $RowAlmacen){
            
            $ProductosAlmacenados = $BaseDeDatos->consultar("SELECT `inventario`.`existencia`, `productos`.* 
            FROM `inventario` INNER JOIN `productos` ON `inventario`.`idProducto` = `productos`.`id` 
            WHERE `idAlmacen` = ".$RowAlmacen['id']);

            
            if($MostrarNoAlmacenados && $RowAlmacen['idEstado'] == 51){
                
                if(empty($ProductosAlmacenados)){
                    $ListaDeProductos = $BaseDeDatos->consultar("SELECT '0' as 'existencia',`productos`.* FROM `productos` WHERE ((`idCategoria` = 1 OR `idCategoria` = 2) AND (`idEstado` = 1 OR `idEstado` = 2 OR `idEstado` = 3))");
                }else{
                    //throw new Exception("AAAA");
                    $ProductosNoAlmacenados = $BaseDeDatos->consultar("SELECT '0' as 'existencia',`productos`.* FROM `productos` 
                    WHERE ( (`id` NOT IN (SELECT `productos`.`id` FROM `inventario` INNER JOIN `productos` ON `inventario`.`idProducto` = `productos`.`id` WHERE `idAlmacen` = ".$RowAlmacen['id'].")) AND (`idEstado` = 1 OR `idEstado` = 2 OR `idEstado` = 3) AND (`idCategoria` = 1 OR `idCategoria` = 2)) ORDER BY `id`;");
                    
                    $ListaDeProductos = array_merge($ProductosAlmacenados, $ProductosNoAlmacenados); 
                }
            }else{
                
                $ListaDeProductos = $ProductosAlmacenados;
            }
            
            $AlmacenesConProductos [] = array(
                'idAlmacen' => $RowAlmacen['id'],
                'nombre' => $RowAlmacen['nombre'],
                'direccion' => $RowAlmacen['direccion'],
                'idEstado' => $RowAlmacen['idEstado'],
                'productosAlmacenados' => $ListaDeProductos
            );
        }


        

        return $AlmacenesConProductos;
    }

    public function ActualizarEstadoDeProductosEnAlmacen($ID_Almacen){
        //actualizo el estado (Disponible, en alerta o agotado) de los productos dentro del almacen recibido
        $BaseDeDatos = new conexion();
        $ProductosDelAlmacen = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE `idAlmacen` = ".$ID_Almacen);

        foreach($ProductosDelAlmacen as $ProductoEnInventario){
            $CantidadesDelProductoEvaluado = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE `idProducto` = ".$ProductoEnInventario['idProducto']);

            $ExistenciaDelProductoEvaluado = 0;
            foreach($CantidadesDelProductoEvaluado as $ExistenciaEnCadaAlmacen){
                $ExistenciaDelProductoEvaluado = $ExistenciaEnCadaAlmacen['existencia'] + $ExistenciaDelProductoEvaluado;
            }

            $ProductoTarget = new producto($ProductoEnInventario['idProducto']);
            $DatosDelProducto = $ProductoTarget->ObtenerDatos();

            if($ExistenciaDelProductoEvaluado <= 0){
                $BaseDeDatos->ejecutar("UPDATE `productos` SET `idEstado`='3' WHERE `id` = ".$ProductoEnInventario['idProducto']);
            }else{
                if($ExistenciaDelProductoEvaluado <= $DatosDelProducto['nivelDeAlerta']){
                    $BaseDeDatos->ejecutar("UPDATE `productos` SET `idEstado`='2' WHERE `id` = ".$ProductoEnInventario['idProducto']);
                }else{
                    $BaseDeDatos->ejecutar("UPDATE `productos` SET `idEstado`='1' WHERE `id` = ".$ProductoEnInventario['idProducto']);
                }
            }   
        }
    }

    public function ConsultarProductos($FiltrosRecibidos){
        $BaseDeDatos = new conexion();
        $Filtros = array(
            'descripcion' => ((isset($FiltrosRecibidos['descripcion']))?$FiltrosRecibidos['descripcion']:''),
            'estado' => ((isset($FiltrosRecibidos['estado']))?((empty($FiltrosRecibidos['estado']))?'0':((is_numeric($FiltrosRecibidos['estado']))?(($FiltrosRecibidos['estado'] >= 0 && $FiltrosRecibidos['estado'] < 4)?intval($FiltrosRecibidos['estado']):'0'):'0')):'0')
        );

        $SQLDeDescripcion = "";
        if(!empty($Filtros['descripcion'])){
            $SQLDeDescripcion = " AND (`productos`.`id` LIKE '%".$Filtros['descripcion']."%' OR `productos`.`nombre` LIKE '%".$Filtros['descripcion']."%')";
        }

        $SQLDeEstado = "";
        if($Filtros['estado'] == 0){
            $SQLDeEstado = "`productos`.`idEstado` != 4 AND `productos`.`idEstado` != 5";
        }else{
            $SQLDeEstado = "(`productos`.`idEstado` = ".$Filtros['estado'].")";
        }

        $SQLDeConsulta = "SELECT DISTINCT `inventario`.`idProducto` FROM `inventario` 
        INNER JOIN `productos` ON `inventario`.`idProducto` = `productos`.`id` 
        WHERE (".$SQLDeEstado.$SQLDeDescripcion.")";

        $ResultadoDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE (".$SQLDeEstado.$SQLDeDescripcion." AND (`idCategoria` = 1 OR `idCategoria` = 2))");
        

        if(empty($ResultadoDeConsulta)){
            return $ResultadoDeConsulta;
        }else{
            $ArrayARetornar = array();

            foreach($ResultadoDeConsulta as $ProductoEnExistencia){
                $ProductoExistente = new producto($ProductoEnExistencia['id']);
                $DatosDelProducto = $ProductoExistente->ObtenerDatos();

                $ConsultaDeExistencia = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE `idProducto` = ".$DatosDelProducto['id']);
                $ExistenciaAcumulada = 0;
                foreach($ConsultaDeExistencia as $ProductoEnUnAlmacen){
                    $ExistenciaAcumulada = $ExistenciaAcumulada + $ProductoEnUnAlmacen['existencia'];
                }

                $ArrayARetornar[] = array(
                    'id' => $DatosDelProducto['id'],
                    'nombre' => $DatosDelProducto['nombre'],
                    'idCategoria' => $DatosDelProducto['idCategoria'],
                    'unidadDeMedida' => $DatosDelProducto['nombreUM'],
                    'nivelDeAlerta' => $DatosDelProducto['nivelDeAlerta'],
                    'existencia' => $ExistenciaAcumulada,
                    'ULRImagen' => $DatosDelProducto['ULRImagen'],
                    'idEstado' => $DatosDelProducto['idEstado'],
                );

            }

            return $ArrayARetornar;
        }
    }

    private function AjustarExistenciaDeProductoEnAlmacen($ID_Almacen, $ID_Producto, $Cantidad){
        //Aumento o disminuyo la cantidad de un producto en un invetario
        $BaseDeDatos = new conexion();
        $ConsultaDeExistenciaDeAlmacen = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE (`idAlmacen` = ".$ID_Almacen." AND `idProducto` = ".$ID_Producto.")");

        

        if(empty($ConsultaDeExistenciaDeAlmacen)){
            $BaseDeDatos->ejecutar("INSERT INTO `inventario`(`idAlmacen`, `idProducto`, `existencia`) 
            VALUES (".$ID_Almacen.", ".$ID_Producto.", ".$Cantidad.")");
        }else{
            $CantidadNueva = $Cantidad + $ConsultaDeExistenciaDeAlmacen[0]['existencia'];

            $BaseDeDatos->ejecutar("UPDATE `inventario` SET `existencia`= ".$CantidadNueva." WHERE (`idAlmacen` = ".$ID_Almacen." AND `idProducto` = ".$ID_Producto.")");
        }

        return true;
    }

    public function Entrada_InventarioInicialDeAlmacen($IDDeAlmacen, $ProductosEnFormatoXD){
        $BaseDeDatos = new conexion();
        $Auditoria = new historial();

        //Creo el ajuste de inventario
        $ID_AjusteDeInventario = $BaseDeDatos->ejecutar("INSERT INTO `ajustedeinventario`(`descripcion`, `idTipoDeAjuste`) VALUES ('Inventario inicial del Almacén #".$IDDeAlmacen."', 3)");
        $Auditoria->CrearNuevoRegistro(1, 7, $ID_AjusteDeInventario,'Se ha registrado el inventario inicial del almacén #'.$ID_AjusteDeInventario);


        //Inserto los productos en el inventario y creo los rows del ajuste 
        foreach(explode('¿', $ProductosEnFormatoXD) as $ProductoConCantidad){
            if(str_contains($ProductoConCantidad, 'x')){
                $pedazos = explode('x', $ProductoConCantidad);
                $IDProducto = $pedazos[0];
                $CantidadNueva = $pedazos[1];

                $this->InsertarRowDetalleDeAjuste($IDProducto, $IDDeAlmacen, $CantidadNueva, $ID_AjusteDeInventario);

                $BaseDeDatos->ejecutar("INSERT INTO `inventario`(`idAlmacen`, `idProducto`, `existencia`) 
                VALUES (".$IDDeAlmacen.", ".$IDProducto.", ".$CantidadNueva.")");   
            }   
        }
    }

    public function InsertarRowDetalleDeAjuste($ID_Producto, $idAlmacenModificado, $Cantidad, $ID_AjusteDeInventario){
        $BaseDeDatos = new conexion();
        $ConsultaDeExistenciaEnInventario = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE `idProducto` = ".$ID_Producto);

        if(empty($ConsultaDeExistenciaEnInventario)){
            $CantidadEnInventario = 0;
        }else{
            $CantidadEnInventario = 0;
            foreach($ConsultaDeExistenciaEnInventario as $ExistenciaEnUnAlmacen){
                $CantidadEnInventario = $CantidadEnInventario + $ExistenciaEnUnAlmacen['existencia'];
            }
        }

        $ResultadoDelCambio = $CantidadEnInventario + $Cantidad;

        $BaseDeDatos->ejecutar("INSERT INTO `detallesdeajuste`(`idProducto`, `cantidad`, `resultado`, `idAlmacenModificado`, `idListaDeAjuste`) 
        VALUES (".$ID_Producto.", ".$Cantidad.", ".$ResultadoDelCambio.", ".$idAlmacenModificado.", ".$ID_AjusteDeInventario.")");
    }

    public function CrearNuevoAjusteDeInventario($DatosAGuardar, $TipoDeAjuste){
        //VALIDACION DE LOS DATOS RECIBIDOS
        $ListaDeErrores = array();
        $BaseDeDatos = new conexion();
        $Auditoria = new historial();

        

        if(empty($DatosAGuardar['descripcion'])){
            $ListaDeErrores [] = 'Descripción está vacío';
        }

        if(!isset($DatosAGuardar['CambiosListados']) || empty($DatosAGuardar['CambiosListados'])){
            $ListaDeErrores [] = 'No se ha añadido ningún cambio';
        }else{
            
            //Verifico el formato del string
            foreach(explode('¿', $DatosAGuardar['CambiosListados']) as $CambioEnLista){
                $Valores = explode('x', $CambioEnLista);

                if(count($Valores) != 3){
                    $ListaDeErrores [] = 'Un ajuste en la lista de cambios tiene un formato inválido ('.$CambioEnLista.')';
                }else{
                    //Ya verificado que cada Cambio tiene tres pedazos, procedo a verificar cada pedazo.
                    //ID de producto
                    if(empty($Valores[0])){
                        $ListaDeErrores [] = 'No se encontró la ID de un producto del almacén de ID '.$Valores[1];
                    }else{
                        if(!is_numeric($Valores[0])){
                            $ListaDeErrores [] = 'La ID de producto '.$Valores[0].' no es válida';
                        }
                    }

                    //ID de almacen
                    if(empty($Valores[1])){
                        $ListaDeErrores [] = 'No se encontró la ID de un almacén para un producto de ID '.$Valores[0];
                    }else{
                        if(!is_numeric($Valores[1])){
                            $ListaDeErrores [] = 'La ID del almacén '.$Valores[1].' no es válida';
                        }
                    }

                    //Cantidad
                    if(empty($Valores[2])){
                        $ListaDeErrores [] = 'No se encontró la cantidad a ajustar del producto de ID'.$Valores[0].' del almacén de ID '.$Valores[1];
                    }else{
                        if(!is_numeric($Valores[1])){
                            $ListaDeErrores [] = 'La cantidad del producto de ID '.$Valores[0].' del almacén de ID '.$Valores[1].' no es válida';
                        }
                    }    
                }    
            }

            


            //Si hay problemas con el formato del string de cambios lo lanzo; sino, procedo a crear una lista
            //con los productos y almacenes mencionados en los cambios
            if(!empty($ListaDeErrores)){
                throw new Exception(implode('¿', $ListaDeErrores));
            }else{
                $AlmacenesSolicitados = array();
                $ProductosSolicitados = array();

                foreach(explode('¿', $DatosAGuardar['CambiosListados']) as $CambioEnLista){
                    $Valores = explode('x', $CambioEnLista);

                    if(!(array_search($Valores[0], $AlmacenesSolicitados) > -1)){
                        $AlmacenesSolicitados [] = $Valores[0];
                    }

                    if(!(array_search($Valores[1], $ProductosSolicitados) > -1)){
                        $ProductosSolicitados [] = $Valores[1];
                    }
                };

                //Ya con los productos y almacenes almacenados en los array, procedemos a comprobar su existencia en la DDBB
                foreach($AlmacenesSolicitados as $ID_AlmacenAModificar){
                    $ConsultaDeExistenciaDeAlmacen = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE (`idEstado` = 51 AND `id` = ".$ID_AlmacenAModificar.")");
                    
                    if(empty($ConsultaDeExistenciaDeAlmacen)){
                        $ListaDeErrores [] = "El almacén de ID ".$ID_AlmacenAModificar." no existe o no está disponible";
                    }
                }
                foreach($ProductosSolicitados as $ID_ProductoAModificar){
                    $ConsultaDeExistenciaDeProducto = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE (`id` = ".$ID_ProductoAModificar." AND (`idEstado` = 1 OR `idEstado` = 2 OR `idEstado` = 3))");

                    if(empty($ConsultaDeExistenciaDeProducto)){
                        $ListaDeErrores [] = "El producto de ID ".$ID_ProductoAModificar." no existe o no está disponible";
                    }
                }
            }


            //En este punto si no hay errores, se garantiza la integridad del string y se puede proceder al INSERT
            if(!empty($ListaDeErrores)){
                throw new Exception(implode('¿', $ListaDeErrores));
            }else{
                $ID_AjusteDeInventario = $BaseDeDatos->ejecutar("INSERT INTO `ajustedeinventario`(`descripcion`, `idTipoDeAjuste`) VALUES ('".$DatosAGuardar['descripcion']."', ".$TipoDeAjuste.")");
                $Auditoria->CrearNuevoRegistro(1, 7, $ID_AjusteDeInventario,'Se ha hecho un ajuste de inventario #'.$ID_AjusteDeInventario);
                
                foreach(explode('¿', $DatosAGuardar['CambiosListados']) as $AjusteAInsertar){
                    $Valores = explode('x', $AjusteAInsertar);

                    $this->InsertarRowDetalleDeAjuste($Valores[1], $Valores[0], $Valores[2], $ID_AjusteDeInventario);
                    $this->AjustarExistenciaDeProductoEnAlmacen($Valores[0], $Valores[1], $Valores[2]);
                }

                $Inventario = new inventario();

                foreach($AlmacenesSolicitados as $ID_Almacen){
                    $Inventario->ActualizarEstadoDeProductosEnAlmacen($ID_Almacen);
                }

                return $ID_AjusteDeInventario;
            }
        }

        
    }

    public function ConsultarCambiosEnInventario($ParametrosDeBusqueda){
        $BaseDeDatos = new conexion();
        $Auditoria = new historial();
        $SQLDeDescripcion = "";
        $SQLDeTipo = "";

        
        if(isset($ParametrosDeBusqueda['descripcion'])){
            if(!empty($ParametrosDeBusqueda['descripcion'])){
                $SQLDeDescripcion = '(`ajustedeinventario`.`id` LIKE "%'.$ParametrosDeBusqueda['descripcion'].'%" OR `ajustedeinventario`.`descripcion` LIKE "%'.$ParametrosDeBusqueda['descripcion'].'%")';
            }
        }
        if(isset($ParametrosDeBusqueda['tipo'])){
            if(!empty($ParametrosDeBusqueda['tipo'])){
                $SQLDeTipo = (($ParametrosDeBusqueda['tipo'] == 0)?'':"`ajustedeinventario`.`idTipoDeAjuste` = ".$ParametrosDeBusqueda['tipo']);
            }
        }


        if(!empty($SQLDeDescripcion) && !empty($SQLDeTipo)){
            $SQLDelWHERE = $SQLDeDescripcion." AND ".$SQLDeTipo;
        }else{
            $SQLDelWHERE = $SQLDeDescripcion.$SQLDeTipo;
        }
        if(!empty($SQLDelWHERE)){
            $SQLDelWHERE = " WHERE ( ".$SQLDelWHERE.")";
        }

        $SQLDeLimite = " LIMIT ".(($ParametrosDeBusqueda['NroDePaginaDeResultados'] - 1) * $ParametrosDeBusqueda['NroMaximoDeResultadosAMostrar']).", ".$ParametrosDeBusqueda['NroMaximoDeResultadosAMostrar'];

        $SQLDeBusqueda = "SELECT `ajustedeinventario`.*, `tipodeajuste`.`descripcion` AS 'tipoDeAjuste' FROM `ajustedeinventario` INNER JOIN `tipodeajuste` ON `ajustedeinventario`.`idTipoDeAjuste` = `tipodeajuste`.`idTipoDeAjuste` ".$SQLDelWHERE." ORDER BY `id` DESC".$SQLDeLimite;
        
        
        
        $Consulta_AjustesCoincidentes = $BaseDeDatos->consultar($SQLDeBusqueda);
        $NroDeResultadosCoincidentes = count($BaseDeDatos->consultar("SELECT * FROM `ajustedeinventario` ".$SQLDelWHERE));

        //Ya con la lista de ajustes que coinciden, procedemos a buscar sus datos y ordentarlos en el array a entregar
        $AjustesARetornar = array();

        

        foreach($Consulta_AjustesCoincidentes as $AjusteCoincidente){
            //throw new Exception('aaaaaa'.$AjusteCoincidente['idTipoDeAjuste']); PAPUSEÑAL
            $ConsultaDeHistorial = $Auditoria->BuscarRegistro(1, 7, $AjusteCoincidente['id']);

            $ConsultaDeHistorial = (empty($ConsultaDeHistorial)? array('fechaCreacion' => 'Desconocido', 'nombreDeUsuario' => 'Desconocido'):$ConsultaDeHistorial[0]);
            
            
            


            $SQLConsultaDeProductosAlterados = "SELECT `detallesdeajuste`.*, `almacenes`.`nombre` AS 'nombreAlmacen', `productos`.`nombre` AS 'nombreProducto', `productos`.`ULRImagen`, `productos`.`idCategoria`, `productos`.`idUnidadDeMedida` 
            FROM `detallesdeajuste` 
            INNER JOIN `almacenes` ON `detallesdeajuste`.`idAlmacenModificado` = `almacenes`.`id` 
            INNER JOIN `productos` ON `detallesdeajuste`.`idProducto` = `productos`.`id` 
            WHERE `idListaDeAjuste` = ".$AjusteCoincidente['id'];

            $ListaDeProductosAlterados = array();

            foreach($BaseDeDatos->consultar($SQLConsultaDeProductosAlterados) as $ProuctoAlterado){

                $UnidadDeMedida = new unidadDeMedida($ProuctoAlterado['idUnidadDeMedida']);
                $DatosDeUM = $UnidadDeMedida->ObtenerDatos();

                $ListaDeProductosAlterados [] = array(
                    'idProducto' => $ProuctoAlterado['idProducto'],
                    'nombreProducto' => $ProuctoAlterado['nombreProducto'],
                    'ImagenProducto' => $ProuctoAlterado['ULRImagen'],
                    'idCategoriaProducto' => $ProuctoAlterado['idCategoria'],
                    'idCategoriaProducto' => $ProuctoAlterado['idCategoria'],
                    'idAlmacen' => $ProuctoAlterado['idAlmacenModificado'],
                    'nombreAlmacen' => $ProuctoAlterado['nombreAlmacen'],
                    'cambio' => $ProuctoAlterado['cantidad'],
                    'resultado' => $ProuctoAlterado['resultado'],
                    'UnidadDeMedida' => $DatosDeUM['simboloConEstilo'],
                    'nombreUM' => $DatosDeUM['nombre']
                );
            }

            $AjustesARetornar [] = array(
                'id' => $AjusteCoincidente['id'],
                'descripcion' => $AjusteCoincidente['descripcion'],
                'idTipoDeAjuste' => $AjusteCoincidente['idTipoDeAjuste'],
                'tipoDeAjuste' => $AjusteCoincidente['tipoDeAjuste'],
                'fechaCreacion' => $ConsultaDeHistorial['fechaCreacion'],
                'usuarioCreador' => $ConsultaDeHistorial['nombreDeUsuario'],
                'productosAlterados' => $ListaDeProductosAlterados
            );
        }
        
        return array(
            'NroDeResultadosCoincidentes' => $NroDeResultadosCoincidentes,
            'AjustesLimitados' => $AjustesARetornar,
            'SQL' => $SQLDeBusqueda
        );
    }
}

////////////LOCALIZACION/////////////
class localizacion{
    private $idLocalizacion;
    private $nombre;
    private $direccion;
    private $tipo;

    public function __construct($idACargar){
        if($idACargar > 0){
            $BaseDeDatos = new conexion();
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `localizaciones` WHERE `id` = ".$idACargar);
            $DatosACargar = $DatosDeLaConsulta[0];

            $this->idLocalizacion = $DatosACargar['id'];
            $this->nombre = $DatosACargar['nombre'];
            $this->direccion = $DatosACargar['direccion'];
            $this->tipo = $DatosACargar['tipo'];
        }

    }

    public function ObtenerDatos(){
        return array(
            'idLocalizacion' => $this->idLocalizacion,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'tipo' => $this->tipo
        );
    }

    public function ActualizarDatosDeLoc($DatosNuevos, $IDLoc){
        $BaseDeDatos = new conexion();
        $DatosDelUPDATE = array(
            'nombre' => "'".$DatosNuevos['Nombre']."'",
            'direccion' => "'".$DatosNuevos['Direccion']."'",
        );

        $BaseDeDatos->ejecutar("UPDATE `localizaciones` SET `nombre`= ".$DatosDelUPDATE['nombre'].",`direccion`= ".$DatosDelUPDATE['direccion']." WHERE `id` = ".$IDLoc);
    }

    public function CrearNuevo($DatosAGuardar, $Estado, $Tipo){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = $this->ValidarDatos($DatosAGuardar, $Estado);

        $DatosDelInsert = array(
            
        );

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            return $BaseDeDatos->ejecutar("INSERT INTO `localizaciones`(`nombre`, `direccion`, `tipo`) VALUES 
            (".$DatosDelInsert['nombre'].", ".$DatosDelInsert['direccion'].", ".$DatosDelInsert['tipo'].")");
        }
    }
    
    public function ValidarDatos($DatosAGuardar, $Estado){
        $ListaDeErrores = array();

        

        return $ListaDeErrores;
    }
}

////////////ALMACEN/////////////
class almacen {
    public $id;
    private $nombre;
    private $direccion;
    private $predeterminado;
    private $idEstado;

    public function __construct($idACargar){
        if($idACargar > 0){
            $BaseDeDatos = new conexion();
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE `almacenes`.`id` = ".$idACargar);
            $DatosACargar = $DatosDeLaConsulta[0];

            $this->id = $DatosACargar['id'];
            $this->predeterminado = $DatosACargar['predeterminado'];
            $this->idEstado = $DatosACargar['idEstado'];
            $this->nombre = $DatosACargar['nombre'];
            $this->direccion = $DatosACargar['direccion'];
        }
    }

    

    public function ListarAlmacenes($Filtros){
        $BaseDeDatos = new conexion();
        
        if($Filtros['idEstado'] == '0' ){
            $SQLDeEstado = "(`idEstado` = 51 OR `idEstado` = 52)";
        }else{
            $SQLDeEstado = "(`idEstado` = 51)";
        }

        $SQLDescripcion = "";
        if($Filtros['descripcion']){
            $SQLDescripcion = "(`id` LIKE '%".$Filtros['descripcion']."%' OR `nombre` LIKE '%".$Filtros['descripcion']."%' OR `direccion` LIKE '%".$Filtros['descripcion']."%') AND ";
        }

        $SQLDeConsulta = "SELECT * FROM `almacenes` WHERE ".$SQLDescripcion.$SQLDeEstado;

        $ArrayDeRetorno = array();
        foreach($BaseDeDatos->consultar($SQLDeConsulta) as $AlmacenEnTabla){
            $Almacen = new almacen($AlmacenEnTabla['id']);

            $ConsultaDeInventario = $BaseDeDatos->consultar("SELECT `idProducto`, `existencia` FROM `inventario` WHERE `idAlmacen` = ".$AlmacenEnTabla['id']);

            $ProductosEnInventario = array();
            foreach($ConsultaDeInventario as $DatosDeInventario){
                $Producto = new producto($DatosDeInventario['idProducto']);
                $DatosDelProducto = $Producto->ObtenerDatos();

                
                $ProductosEnInventario [] = array(
                    'idProducto' => $DatosDeInventario['idProducto'],
                    'existencia' => $DatosDeInventario['existencia'],
                    'nombreUM' => $DatosDelProducto['nombreUM']
                );
            }
            
            $ArrayDeRetorno [] = array(
                'id' => $AlmacenEnTabla['id'],
                'nombre' => $AlmacenEnTabla['nombre'],
                'direccion' => $AlmacenEnTabla['direccion'],
                'productos' => $ProductosEnInventario
            );
        }

        return $ArrayDeRetorno;
    }

    public function ObtenerAjusteDeInventarioPorTipo($idTipo){
        $BaseDeDatos = new conexion();
        $result = array();
        $search = $BaseDeDatos->consultar("SELECT `ajustedeinventario`.`id` FROM `ajustedeinventario` 
        INNER JOIN `detallesdeajuste` ON `ajustedeinventario`.`id` =  `detallesdeajuste`.`idListaDeAjuste` 
        WHERE (`ajustedeinventario`.`idTipoDeAjuste` = $idTipo AND `detallesdeajuste`.`idAlmacenModificado` = $this->id) GROUP by `ajustedeinventario`.`id` DESC");

        if(empty($search)){
            return array();
        }else{
            foreach($search as $row){
                $result[] = $row['id'];
            }
        }

        return $result;
    }

    public function ObtenerAjustesDeInventario($NroDeMovimiento){
        $BaseDeDatos = new conexion();
        $AjustesInvolucrados = $BaseDeDatos->consultar("SELECT DISTINCT `idListaDeAjuste` FROM `detallesdeajuste` WHERE (`idAlmacenModificado` = ".$this->id.") ORDER BY `idListaDeAjuste` DESC");

        $Respuesta = array();
        foreach($AjustesInvolucrados as $Ajuste){
            $Historial = new historial();
            $DatosDeCreacion = $Historial->BuscarRegistro(1, 7, $Ajuste[0]);

            $DatosDelAjuste = $BaseDeDatos->consultar("SELECT * FROM `ajustedeinventario` WHERE `id` = ".$Ajuste[0]);
            $DatosDelAjuste = $DatosDelAjuste[0];
            $DatosDelAjuste['fechaCreacion'] = $DatosDeCreacion[0]['fechaCreacion'];
            $DatosDelAjuste['2'] = $DatosDeCreacion[0]['fechaCreacion'];
            $DatosDelAjuste['nombreDeUsuario'] = $DatosDeCreacion[0]['nombreDeUsuario'];
            $DatosDelAjuste['3'] = $DatosDeCreacion[0]['nombreDeUsuario'];
            
            $ConsultaDeNroDeProductos = $BaseDeDatos->consultar("SELECT `detallesdeajuste`.*, `productos`.`nombre`, `productos`.`ULRImagen` 
            FROM `detallesdeajuste` INNER JOIN `productos` ON `detallesdeajuste`.`idProducto` = `productos`.`id` 
            WHERE (`idListaDeAjuste` = ".$Ajuste[0]." AND (`idAlmacenModificado` = ".$this->id."))");
            $DatosDelAjuste['NroDeProductos'] = count($ConsultaDeNroDeProductos);
            $DatosDelAjuste['4'] = count($ConsultaDeNroDeProductos);

            $DatosDelAjuste['listaDeProductos'] = array();

            foreach($ConsultaDeNroDeProductos as $Detalle){
                $DatosDelAjuste['listaDeProductos'][] = array(
                    'idProducto' => $Detalle['idProducto'],
                    'nombre' => 'xd',
                    'cantidad' => $Detalle['cantidad'],
                    'nombre' => $Detalle['nombre'],
                    'ULRImagen' => $Detalle['ULRImagen']
                );

            }
            

            $Respuesta [] = $DatosDelAjuste;
        }


        return $Respuesta;
    }

    public function ObtenerDatos(){
        
        $BaseDeDatos = new conexion();
        $ListaDeProductosDelAlmacen = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE `idAlmacen` = ".$this->id);
        $ProductosDelAlmacen = "";

        foreach($ListaDeProductosDelAlmacen as $ProductoEnLista){
            $ProductosDelAlmacen = ((empty($ProductosDelAlmacen))?'':$ProductosDelAlmacen.'¿').$ProductoEnLista['idProducto'].'x'.$ProductoEnLista['existencia'];
        }

        $Auditoria = new historial();
        $FechaCreado = $Auditoria->BuscarRegistro(1, 6, $this->id);
        if(empty($FechaCreado)){
            $FechaCreado = "";
        }else{
            $FechaCreado = $FechaCreado[0]['fechaCreacion'];
        }

        $NroDeProductosEnAlmacen = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `inventario` WHERE `idAlmacen` = ".$this->id);
        $NroDeProductosEnAlmacen = $NroDeProductosEnAlmacen[0][0];


        return array(
            'id' => $this->id,
            'predeterminado' => $this->predeterminado,
            'idEstado' => $this->idEstado,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'predeterminado' => $this->predeterminado,
            'ProductosDelAlmacen' => $ProductosDelAlmacen,
            'fCreacion' => $FechaCreado,
            'nroDeProductos' => $NroDeProductosEnAlmacen
        );
    }

    public function ActualizarDatosDeAlmacen($DatosNuevos, $Estado){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = $this->ValidarDatosDeAlmacen($DatosNuevos, $Estado);

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //ACtualizo los datos de la loc y el estado del almacen
            $this->ActualizarDatosDeLoc($DatosNuevos, $this->idLocalizacion);
            $BaseDeDatos->ejecutar("UPDATE `almacenes` SET `idEstado`=".$Estado." WHERE `id` = ".$this->id);

            //borro sus viejos productos del inventario y creo los nuevos
            $BaseDeDatos->ejecutar("DELETE FROM `inventario` WHERE `idAlmacen` = ".$this->id);
            if(!empty($DatosNuevos['ProductosDelAlmacen'])){
                foreach(explode('¿', $DatosNuevos['ProductosDelAlmacen']) as $ProductoConCantidad){
                    $pedazos = explode('x', $ProductoConCantidad);
                    $BaseDeDatos->ejecutar("INSERT INTO `inventario`(`idAlmacen`, `idProducto`, `existencia`) 
                    VALUES (".$this->id.", ".$pedazos[0].", ".$pedazos[1].")");
                }
            }
        }
    }

    public function CrearNuevoAlmacen($DatosAGuardar, $Estado){
        $BaseDeDatos = new conexion();
        $Inventario = new inventario();
        $ListaDeErrores = $this->ValidarDatos($DatosAGuardar, $Estado);

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Creo la localizacion y busco si hay almacen predeterminado
            $ResultadoDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE `predeterminado` = 1");

            //Preparo los datos del insert
            $DatosDelInsert = array(
                'predeterminado' => ((empty($ResultadoDeConsulta) && $Estado == 51)?'true':'false'),
                'idEstado' => $Estado,
                'nombre' => "'".$DatosAGuardar['Nombre']."'",
                'direccion' => "'".$DatosAGuardar['Direccion']."'"
            );

            
            //Registro el almacen
            $IDDelAlmacen = $BaseDeDatos->ejecutar("INSERT INTO `almacenes`(`nombre`, `direccion`, `predeterminado`, `idEstado`) 
            VALUES (".$DatosDelInsert['nombre'].", ".$DatosDelInsert['direccion'].", ".$DatosDelInsert['predeterminado'].", ".$DatosDelInsert['idEstado'].")");

            //Guardo historial
            if($Estado != 53){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 6, $IDDelAlmacen, 'Se ha creado el almacén #'.$IDDelAlmacen);
            }

            //Registro el inventario inicial
            if(!empty($DatosAGuardar['ProductosDelAlmacen'])){
                $Inventario->Entrada_InventarioInicialDeAlmacen($IDDelAlmacen, $DatosAGuardar['ProductosDelAlmacen']);

                //Actualizo el estado de los productos introducidos ahora en este almacen tambien
                $Inventario->ActualizarEstadoDeProductosEnAlmacen($IDDelAlmacen);
            }
            
        }
        
    }

    public function ValidarDatos($DatosAGuardar, $Estado){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = array();

        if(empty($DatosAGuardar['Nombre'])){
            $ListaDeErrores [] = "Nombre no puede estar vacío¿";
        }

        if(empty($DatosAGuardar['Direccion']) && ($Estado == 51 || $Estado == 52)){
            $ListaDeErrores [] = "Dirección no puede estar vacío¿";
        }

        if(!empty($DatosAGuardar['ProductosDelAlmacen'])){
            $ArrayProductosConCantidad = explode('¿', $DatosAGuardar['ProductosDelAlmacen']);

            foreach($ArrayProductosConCantidad as $ProductoConCantidad){
                $pedazos = explode('x', $ProductoConCantidad);
                $DatosDeConsulta = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE (`id` = ".$pedazos[0]." AND `idEstado` != 4 AND `idEstado` != 5)");
                if(empty($DatosDeConsulta)){
                    $ListaDeErrores [] = 'El producto '.$pedazos[0].' no existe¿';
                }
            }
        }

        return $ListaDeErrores;
    }
}

////////////COTIZACION/////////////
class cotizacion{
    private $id;
    private $nombre;
    private $cedulaCliente;
    private $Cliente;
    private $fechaExpiracion;
    private $idEstado;
    private $pUtilidades;
    private $pIVA;
    private $pCASalario;

    public function ConfirmarVenta($DatosAGuardar){
        $result = array();
        $issues = array();
        $sql = array();
        $BaseDeDatos = new conexion();
        $Historial = new historial();


        
        
        

        if(empty($DatosAGuardar['IDCoti'])){
            $issues[] = 'No se ha seleccionado ninguna cotización';
        }else{
            if(!is_numeric($DatosAGuardar['IDCoti'])){
                $issues[] = 'La ID de cotización '.$DatosAGuardar['IDCoti'].' es inválida';
            }else{
                $search = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE (`id` = ".$DatosAGuardar['IDCoti']." AND `idEstado` = 33)");
                if(empty($search)){
                    $issues[] = 'No se encontró la cotización en espera #'.$DatosAGuardar['IDCoti'];
                }
            }
        }

        if(!empty($DatosAGuardar['IDCliente'])){
            if(!is_numeric($DatosAGuardar['IDCliente'])){
                $issues[] = 'La ID de cliente'.$DatosAGuardar['IDCliente'].' es inválida';
            }else{
                $search = $BaseDeDatos->consultar("SELECT * FROM `clientes` WHERE (`rif` = ".$DatosAGuardar['IDCliente']." AND `idEstado` = 11)");
                if(empty($search)){
                    $issues[] = "No se encontró el cliente #".$DatosAGuardar['IDCliente'];
                }
            }
        }else{
            $DatosAGuardar['IDCliente'] = "NULL";
        }

        if(empty($DatosAGuardar['tituloDeVenta'])){
            $issues[] = 'El título de la venta está vacío';
        }else{
            if(strlen($DatosAGuardar['tituloDeVenta'])>50){
                $issues[] = 'El título de la venta debe contar con máximo 50 caractéres';
            }
        }


        if(!empty($DatosAGuardar['CAS'])){
            if(!is_numeric($DatosAGuardar['CAS'])){
                $issues[] = 'El costo asoaciado al salario '.$DatosAGuardar['CAS'].' es inválido';
            }
        }else{
            $DatosAGuardar['CAS'] = '0';
        }
        
        if(!empty($DatosAGuardar['Utilidades'])){
            if(!is_numeric($DatosAGuardar['Utilidades'])){
                $issues[] = 'Las utilidades '.$DatosAGuardar['Utilidades'].' es inválido';
            }
        }else{
            $DatosAGuardar['Utilidades'] = '0';
        }
        
        if(!empty($DatosAGuardar['IVA'])){
            if(!is_numeric($DatosAGuardar['IVA'])){
                $issues[] = 'El IVA '.$DatosAGuardar['IVA'].' es inválido';
            }
        }else{
            $DatosAGuardar['IVA'] = '0';
        }

        if(!empty($issues)){
            throw new Exception(implode('¿', $issues));
        }

        if(empty($DatosAGuardar['ListaProductosConPrecio'])){
            $issues[] = 'No se ha indicado ningun producto a vender';
        }else{
            $Array_ProdXCantXPrec = explode('¿', $DatosAGuardar['ListaProductosConPrecio']);
            $Array_IdsProductosVendidos = array();
            $Array_IdsDeAlmacenesUsados = array();

            foreach($Array_ProdXCantXPrec as $ProdXCantXPrec){
                $Array_dato = explode('x', $ProdXCantXPrec);
                if(count($Array_dato)!=3){
                    $issues[] = 'El formato del producto vendido ('.$ProdXCantXPrec.') es inválido';
                }else{
                    if(in_array($Array_dato[0], $Array_IdsProductosVendidos)){
                        $issues[] = 'El producto vendido #'.$Array_dato[0].' está repetido en la lista de productos vendidos';
                    }else{
                        if(!is_numeric($Array_dato[1])||!is_numeric($Array_dato[2])){
                            $issues[] = 'El producto vendido #'.$Array_dato[0].' tiene valores inválidos ('.$Array_dato[1].'x'.$Array_dato[2].')';
                        }else{
                            $Array_IdsProductosVendidos[] = $Array_dato[0];
                        }
                    }
                }
            }

            $result['IdsProductosVendidos'] = $Array_IdsProductosVendidos;
        }
        
        if(!empty($issues)){
            throw new Exception(implode('¿', $issues));
        }

        foreach($Array_IdsProductosVendidos as $idProductoVendido){
            $search = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE (`id` = $idProductoVendido AND `idEstado`<4)");
            if(empty($search)){
                $issues[] = "El producto #$idProductoVendido no se encontró en la base de datos";
            }
        }

        if(!empty($issues)){
            throw new Exception(implode('¿', $issues));
        }

        if(empty($DatosAGuardar['ListaDeExtraccion'])){
            $issues[] = 'No se ha indicado el origen de los productos a vender';
        }else{
            $Array_ListaDeExtraccion = explode('¿', $DatosAGuardar['ListaDeExtraccion']);
            
            foreach($Array_ListaDeExtraccion as $pedazoDeExtraccion){
                $trozos = explode(':', $pedazoDeExtraccion);
                if(count($trozos)!=2){
                    $issues[] = "La lista de extracción del almacen #".$trozos[0]." es inválida($pedazoDeExtraccion)";
                }else{
                    $search = $BaseDeDatos->consultar("SELECT * FROM `almacenes` WHERE (`id` = ".$trozos[0]." AND `idEstado` <53)");
                    if(empty($search)){
                        $issues[] = 'No se encontró el almacen #'.$trozos[0].' en la base de datos';
                    }else{
                        $Array_ExtracionDelAlmacen = explode(',', $trozos[1]);
                        foreach($Array_ExtracionDelAlmacen as $IDxCant){
                            $ped = explode('x', $IDxCant);
                            if(count($ped) != 2){
                                $issues[] = "El producto $IDxCant a extraer del almacen #".$trozos[0]." no es válido";
                            }else{
                                if(!in_array($ped[0], $Array_IdsProductosVendidos)){
                                    $issues[] = "El producto #".$ped[0]." que desea extraer del almacén #".$trozos[0]." no está en la lista de productos vendidos";
                                }else{
                                    if(!is_numeric($ped[1])){
                                        $issues[] = "La cantidad ".$ped[1]." del producto #".$ped[0]." a extraer del almacen #".$trozos[0]." es inválida";
                                    }
                                }
                            }
                        }
                        $Array_IdsDeAlmacenesUsados[] = $trozos[0];
                    }
                }
            }
        }

        if(!empty($issues)){throw new Exception(implode('¿', $issues));}
        
        $sql['INSERT_ajustedeinventario'] = "INSERT INTO `ajustedeinventario`(`descripcion`, `idTipoDeAjuste`) VALUES ('Venta #".$DatosAGuardar['IDCoti']." confirmada', 2)";
        $result['ID_ajustedeinventario'] = $BaseDeDatos->ejecutar($sql['INSERT_ajustedeinventario']);
        $sql['INSERT_detallesdeajuste'] = array();
        
        $inventario = new inventario();
        $AlmCosas_AlmCosas = explode('¿', $DatosAGuardar['ListaDeExtraccion']);
        foreach($AlmCosas_AlmCosas as $AlmCosas){
            $trozos = explode(':', $AlmCosas);
            $idAlmacenModificado = $trozos[0];
            $ProdXCant_ProdXCant = explode(',', $trozos[1]);
            foreach($ProdXCant_ProdXCant as $ProdXCant){
                $xd = explode('x', $ProdXCant);
                $ID_Producto = $xd[0];
                $Cantidad = $xd[1];
                $inventario->InsertarRowDetalleDeAjuste($ID_Producto, $idAlmacenModificado, $Cantidad, $result['ID_ajustedeinventario']);
            }
        }
        
        $BaseDeDatos->ejecutar("DELETE FROM `cuerpocotizacion` WHERE `idCotizacion` = ".$DatosAGuardar['IDCoti']);
        $ProdXCantXPrec_ProdXCantXPrec = explode('¿', $DatosAGuardar['ListaProductosConPrecio']);
        foreach($ProdXCantXPrec_ProdXCantXPrec as $ProdXCantXPrec){
            $Prod_Cant_Prec = explode('x', $ProdXCantXPrec);

            $search = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE `id` = ".$Prod_Cant_Prec[0]);
            if(!empty($search)){
                if($search[0]['idCategoria']<3){
                    if($search[0]['idCategoria']==1){
                        $PrecioMultiplicado = ($Prod_Cant_Prec[1] * $Prod_Cant_Prec[2]);
                    }else{
                        $depreciacion = 1;
                        $search2 = $BaseDeDatos->consultar("SELECT * FROM `depreciacion` WHERE `idProducto` = ".$Prod_Cant_Prec[0]);
                        if(!empty($search2)){
                            $depreciacion = $search2[0]['valor'];
                        }

                        $PrecioMultiplicado = ($Prod_Cant_Prec[1] * $Prod_Cant_Prec[2] * $depreciacion);
                    }
                }else{
                    $nmms = explode('.', $Prod_Cant_Prec[1]);
                    $PrecioMultiplicado = ($nmms[0] * $nmms[1] * $Prod_Cant_Prec[2]);
                }
                
                $BaseDeDatos->ejecutar("INSERT INTO `cuerpocotizacion`(`idCotizacion`, `idProducto`, `cantidad`, `precioUnitario`, `precioMultiplicado`) VALUES (
                    ".$DatosAGuardar['IDCoti'].",
                    ".$Prod_Cant_Prec[0].",
                    ".$Prod_Cant_Prec[1].",
                    ".$Prod_Cant_Prec[2].",
                    $PrecioMultiplicado
                )");
            }
        }

        $BaseDeDatos->ejecutar("UPDATE `cotizaciones` SET `nombre`='".$DatosAGuardar['tituloDeVenta']."',
        `cedulaCliente`=".$DatosAGuardar['IDCliente'].",
        `idEstado`='31',
        `pUtilidades`=".$DatosAGuardar['Utilidades'].",
        `pIVA`=".$DatosAGuardar['IVA'].",
        `pCASalario`=".$DatosAGuardar['CAS'].",
        `idAjusteDeSalida`=".$result['ID_ajustedeinventario']." WHERE `id` = ".$DatosAGuardar['IDCoti']);

        foreach(explode('¿', $DatosAGuardar['ListaDeExtraccion']) as $AlmConProds){
            $pieces = explode(':', $AlmConProds);
            $idAlm = $pieces[0];
            foreach(explode(',', $pieces[1]) as $prodXCant){
                $parts = explode('x', $prodXCant);
                $idProd = $parts[0];
                $cantidadAVender = $parts[1];
                $cantidadActual = 0;

                $search = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE (`idAlmacen` = $idAlm AND `idProducto` = $idProd)");
                if(!empty($search)){
                    $cantidadActual = $search[0]['existencia'];
                }
                $nuevaCantidad = $cantidadActual - $cantidadAVender;
                

                if(empty($search)){
                    $BaseDeDatos->ejecutar("INSERT INTO `inventario`(`idAlmacen`, `idProducto`, `existencia`) VALUES ($idAlm, $idProd, $nuevaCantidad);");
                }else{
                    $BaseDeDatos->ejecutar("UPDATE `inventario` SET `existencia`= '$nuevaCantidad' WHERE `idAlmacen` = $idAlm AND `idProducto` = $idProd");
                }
            }
        }

        $Historial->CrearNuevoRegistro(2, 4, $DatosAGuardar['IDCoti'], 'Se ha confirmado la venta #'.$DatosAGuardar['IDCoti']);
        $Historial->CrearNuevoRegistro(1, 7, $result['ID_ajustedeinventario'], 'Se ha hecho un ajuste de inventario #'.$result['ID_ajustedeinventario']);
        return $DatosAGuardar['IDCoti'];

        return array(
            'issues' => $issues,
            'sql' => $sql,
            'result' => $result
        );
    }

    public function ListarCotizaciones($FiltrosRecibidos){
        $BaseDeDatos = new conexion();
        $SQLDeMes = "";
        $SQLDeAnio = "";
        $SQLDeEstado = "";
        $SQLDeDescripcion = "";
        
        if(!empty($FiltrosRecibidos['mes'])){
            $SQLDeMes = "(
                SELECT MONTH(`fechaCreacion`) FROM `historial` 
                    WHERE (
                        `idTipoDeHuella` = 1 AND `idTipoDeEntidad` = 4 AND `idDeEntidad` =  `cotizaciones`.`id`
                    )
                ) = ".$FiltrosRecibidos['mes'];
        }

        if(!empty($FiltrosRecibidos['anio'])){
            $SQLDeAnio = "(
                SELECT YEAR(`fechaCreacion`) FROM `historial` 
                    WHERE (
                        `idTipoDeHuella` = 1 AND `idTipoDeEntidad` = 4 AND `idDeEntidad` =  `cotizaciones`.`id`
                    )
                ) = ".$FiltrosRecibidos['anio'];
        }

        if(!empty($FiltrosRecibidos['estado'])){
            $SQLDeEstado = "`idEstado` = ".$FiltrosRecibidos['estado'];
        }

        if(!empty($FiltrosRecibidos['descripcion'])){
            $SQLDeDescripcion = "(`nombre` LIKE '%".$FiltrosRecibidos['descripcion']."%' OR `cedulaCliente` LIKE '%".$FiltrosRecibidos['descripcion']."%')";
        }

        $SQLDelWhere = "";

        if(!empty($SQLDeMes) && !empty($SQLDeAnio)){
            $SQLDelWhere = $SQLDeMes.' AND '.$SQLDeAnio;
        }else{
            $SQLDelWhere = $SQLDeMes.$SQLDeAnio;
        }
        
        if(!empty($SQLDelWhere) && !empty($SQLDeEstado)){
            $SQLDelWhere = $SQLDelWhere.' AND '.$SQLDeEstado;
        }else{
            $SQLDelWhere = $SQLDelWhere.$SQLDeEstado;
        }

        if(!empty($SQLDelWhere) && !empty($SQLDeDescripcion)){
            $SQLDelWhere = $SQLDelWhere.' AND '.$SQLDeDescripcion;
        }else{
            $SQLDelWhere = $SQLDelWhere.$SQLDeDescripcion;
        }

        if(!empty($SQLDelWhere)){
            $SQLDelWhere = ' WHERE ('.$SQLDelWhere.')';
        }

        //SI recibo count, devuelvo la cantidad de cotizaciones por cliente
        if(!empty($FiltrosRecibidos['count'])){
            return $BaseDeDatos->consultar("SELECT 
            `clientes`.`nombre`, 
            `cedulaCliente`, 
            COUNT(*) AS 'contador' 
            FROM `cotizaciones`
            INNER JOIN `clientes` ON `cotizaciones`.`cedulaCliente` = `clientes`.`rif`
            WHERE ( (`cedulaCliente` IS NOT NULL) ) GROUP BY `cedulaCliente` HAVING COUNT(*) > 0 ORDER BY COUNT(*) DESC LIMIT 0,4;");
        }

        $result = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones`".$SQLDelWhere);

        $result2 = array();
        foreach($result as $row){
            $RespuestaDeHistorial = $BaseDeDatos->consultar("SELECT * FROM `historial` WHERE (`idTipoDeHuella` = 1 AND `idTipoDeEntidad` = 4 AND `idDeEntidad` = '".$row['id']."')");

            $result2[] = array_merge($row, array('fechaCreacion' => (empty($RespuestaDeHistorial)?NULL:$RespuestaDeHistorial[0]['fechaCreacion'])));
        }
        
        return $result2;
    }

    public function RechazarCot(){
        $BaseDeDatos = new conexion();
        $Historial = new historial();

        $BaseDeDatos->ejecutar("UPDATE `cotizaciones` SET `idEstado`= '32' WHERE `id` = $this->id");
        $Historial->CrearNuevoRegistro(2, 4, $this->id, 'Se ha rechazado la cotización #'.$this->id);

        
    }

    public function ListarProductosDeCot(){
        $BaseDeDatos = new conexion();
        
        
        return $BaseDeDatos->consultar("SELECT `cuerpocotizacion`.`cantidad`,`cuerpocotizacion`.`precioUnitario`,`cuerpocotizacion`.`precioMultiplicado`,`productos`.* FROM `cuerpocotizacion` INNER JOIN `productos` ON `cuerpocotizacion`.`idProducto` = `productos`.`id` WHERE `idCotizacion` = $this->id");
    }
    
    public function __construct($idACargar){
        $BaseDeDatos = new conexion();

        if($idACargar!=0){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE `id` = ".$idACargar);
            $DatosACargar = $DatosDeLaConsulta[0];

            $this->id = $DatosACargar['id'];
            $this->nombre = $DatosACargar['nombre'];
            $this->cedulaCliente = $DatosACargar['cedulaCliente'];
            $this->fechaExpiracion = $DatosACargar['fechaExpiracion'];
            $this->idEstado = $DatosACargar['idEstado'];
            $this->pUtilidades = $DatosACargar['pUtilidades'];
            $this->pIVA = $DatosACargar['pIVA'];
            $this->pCASalario = $DatosACargar['pCASalario'];
        }
    }

    public function ObtenerDatos(){
        $BaseDeDatos = new conexion();
        $MaterialesCotizados = "";
        $MaquinasCotizados = "";
        $ManoCotizados= "";

        //Consulto Materiales
        $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` 
        WHERE (`idCotizacion` = ".$this->id." AND (SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 1)");

        if(!empty($DatosDeLaConsulta)){
            foreach($DatosDeLaConsulta as $ProductoCotizado){
                $MaterialesCotizados = $ProductoCotizado['idProducto']."x".$ProductoCotizado['cantidad'].((empty($MaterialesCotizados))?"":"¿".$MaterialesCotizados);
            }
        }

        //Consulto maquinas
        $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` 
        WHERE (`idCotizacion` = ".$this->id." AND (SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 2)");

        if(!empty($DatosDeLaConsulta)){
            foreach($DatosDeLaConsulta as $ProductoCotizado){
                $MaquinasCotizados = $ProductoCotizado['idProducto']."x".$ProductoCotizado['cantidad'].((empty($MaquinasCotizados))?"":"¿".$MaquinasCotizados);
            }
        }

        //Consulto Mano de obra
        $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `cuerpocotizacion` WHERE 
        ((`idCotizacion` = ".$this->id.") AND 
        ( ((SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 3) OR ((SELECT `productos`.`idCategoria` FROM `productos` WHERE `productos`.`id` = `cuerpocotizacion`.`idProducto`) = 4) ))");

        if(!empty($DatosDeLaConsulta)){
            foreach($DatosDeLaConsulta as $ProductoCotizado){
                $ManoCotizados = $ProductoCotizado['idProducto']."x".$ProductoCotizado['cantidad'].((empty($ManoCotizados))?"":"¿".$ManoCotizados);
            }
        }

        //Consulto estado
        $RespuestaDeEstado = $BaseDeDatos->consultar("SELECT * FROM `estados` WHERE `id` = ".$this->idEstado);

        //Consulto la fecha de creacion
        $RespuestaDeHistorial = $BaseDeDatos->consultar("SELECT * FROM `historial` WHERE (`idTipoDeHuella` = 1 AND `idTipoDeEntidad` = 4 AND `idDeEntidad` = '".$this->id."')");

        //Consulto la fecha de modificado
        $RespuestaDeHistorial2 = $BaseDeDatos->consultar("SELECT * FROM `historial` WHERE (`idTipoDeHuella` = 2 AND `idTipoDeEntidad` = 4 AND `idDeEntidad` = '".$this->id."')");
        
        return array(
            'id' => $this->id,
            'nombre' => $this->nombre,
            'cedulaCliente' => $this->cedulaCliente,
            'fechaExpiracion' => $this->fechaExpiracion,
            'DiasDeVigencia' => "0",
            'pUtilidades' => $this->pUtilidades,
            'pIVA' => $this->pIVA,
            'pCASalario' => $this->pCASalario,
            'MaterialesCotizados' => $MaterialesCotizados,
            'MaquinasCotizados' => $MaquinasCotizados,
            'ManoCotizados' => $ManoCotizados,
            'estado' => $RespuestaDeEstado[0]['estado'],
            'creado' => ((empty($RespuestaDeHistorial))?'':$RespuestaDeHistorial[0]['fechaCreacion']),
            'modificado' => ((empty($RespuestaDeHistorial2))?'':$RespuestaDeHistorial[0]['fechaCreacion']),
        );
    }

    public function Actualizar($DatosNuevos, $Estado){
        ////////////OBSOLETO////////////
        $BaseDeDatos = new conexion();

        //Valido los datos
        $ListaDeErrores = $this->ValidarDatos($DatosNuevos, $Estado);

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Preparo los datos para el insert
            $DatosParaElInsert = array(
                'nombre' => "'".$DatosNuevos['Nombre']."'",
                'cedulaCliente' => ((empty($DatosNuevos['RifCliente']))?'NULL':$DatosNuevos['RifCliente']),
                'fechaExpiracion' => ((empty($DatosNuevos['FechaExpiracion']))?"NULL":"'".$DatosNuevos['FechaExpiracion']."'"),
                'idEstado' => $Estado,
                'pUtilidades' =>$DatosNuevos['Utilidades'],
                'pIVA' =>$DatosNuevos['IVA']
            );

            //Actualizo la cotizacion
            $BaseDeDatos->ejecutar("UPDATE `cotizaciones` SET 
            `nombre`= ".$DatosParaElInsert['nombre'].", 
            `cedulaCliente`= ".$DatosParaElInsert['cedulaCliente'].", 
            `fechaExpiracion`= ".$DatosParaElInsert['fechaExpiracion'].", 
            `pUtilidades`= ".$DatosParaElInsert['pUtilidades'].", 
            `pIVA`= ".$DatosParaElInsert['pIVA'].", 
            `idEstado`= ".$Estado." WHERE `id` = ".$this->id);

            //Creo registro en historial
            if($this->idEstado == 36 && $Estado != 36){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 4, $this->id, 'Se ha creado la cotización #'.$this->id);
            }

            //Borro el nuevo cuerpo de la cotizacion
            $BaseDeDatos->ejecutar("DELETE FROM `cuerpocotizacion` WHERE `idCotizacion` = ".$this->id);
            
            //Uno el contenido de los inputs que guardan las ID's y las cantidades
            if(!empty($DatosNuevos['IDsDeMateriales']) && !empty($DatosNuevos['IDsDeMaquinaria'])){
                $ProductosCotizados = $DatosNuevos['IDsDeMateriales']."¿".$DatosNuevos['IDsDeMaquinaria'];
            }else{
                $ProductosCotizados = $DatosNuevos['IDsDeMateriales'].$DatosNuevos['IDsDeMaquinaria'];
            }

            if(!empty($ProductosCotizados) && !empty($DatosNuevos['IDsDeManoDeObra'])){
                $ProductosCotizados = $ProductosCotizados."¿".$DatosNuevos['IDsDeManoDeObra'];
            }else{
                $ProductosCotizados = $ProductosCotizados.$DatosNuevos['IDsDeManoDeObra'];
            }

            //Creo el nuevo cuerpo de cotizacion
            $ArrayProductosCotizados = explode("¿", $ProductosCotizados);
            
            foreach($ArrayProductosCotizados as $ProductoARegistrar){
                $pedazos = explode('x', $ProductoARegistrar);
                $ConsultaDelProducto = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE `id` = ".$pedazos[0]);
                $DatosDelProducto = $ConsultaDelProducto[0];

                $BaseDeDatos->ejecutar("INSERT INTO `cuerpocotizacion`(`idCotizacion`, `idProducto`, `cantidad`, `precioUnitario`, `precioMultiplicado`) VALUES (
                    ".$this->id.", 
                    ".$pedazos[0].", 
                    ".$pedazos[1].", 
                    ".$DatosDelProducto['precio'].", 
                    ".($DatosDelProducto['precio'] * $pedazos[1])."
                )");
                
            }
            

        }

    }

    public function CrearNuevo($DatosAGuardar, $Estado){
        $BaseDeDatos = new conexion();

        //Valido los datos
        $ListaDeErrores = $this->ValidarDatos($DatosAGuardar, $Estado);

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Preparo los datos para el insert
            $DatosParaElInsert = array(
                'nombre' => "'".$DatosAGuardar['Nombre']."'",
                'cedulaCliente' => ((empty($DatosAGuardar['RifCliente']))?'NULL':$DatosAGuardar['RifCliente']),
                'fechaExpiracion' => ((empty($DatosAGuardar['FechaExpiracion']))?"NULL":"'".$DatosAGuardar['FechaExpiracion']."'"),
                'idEstado' => $Estado,
                'pUtilidades' =>$DatosAGuardar['Utilidades'],
                'pIVA' =>$DatosAGuardar['IVA'],
                'pCASalario' =>$DatosAGuardar['CASalario']
            );

            
            //Uno el contenido de los inputs que guardan las ID's y las cantidades
            if(!empty($DatosAGuardar['IDsDeMateriales']) && !empty($DatosAGuardar['IDsDeMaquinaria'])){
                $ProductosCotizados = $DatosAGuardar['IDsDeMateriales']."¿".$DatosAGuardar['IDsDeMaquinaria'];
            }else{
                $ProductosCotizados = $DatosAGuardar['IDsDeMateriales'].$DatosAGuardar['IDsDeMaquinaria'];
            }

            if(!empty($ProductosCotizados) && !empty($DatosAGuardar['IDsDeManoDeObra'])){
                $ProductosCotizados = $ProductosCotizados."¿".$DatosAGuardar['IDsDeManoDeObra'];
            }else{
                $ProductosCotizados = $ProductosCotizados.$DatosAGuardar['IDsDeManoDeObra'];
            }

            $ArrayProductosCotizados = explode("¿", $ProductosCotizados);
            
            //session_start();
            $UsuarioLogeado = unserialize($_SESSION["UsuarioLogeado"]);
            $DatosDelUsuarioLogeado = $UsuarioLogeado->ObtenerDatos();
            
            
            //Registro la cotizacion
            $IDCotizacionInsertada = $BaseDeDatos->ejecutar("INSERT INTO `cotizaciones`(`nombre`, `cedulaCliente`, `fechaExpiracion`, `idEstado`, `pUtilidades`, `pIVA`, `pCASalario`) VALUES (
                ".$DatosParaElInsert['nombre'].", 
                ".$DatosParaElInsert['cedulaCliente'].", 
                ".$DatosParaElInsert['fechaExpiracion'].", 
                ".$DatosParaElInsert['idEstado'].", 
                ".$DatosParaElInsert['pUtilidades'].", 
                ".$DatosParaElInsert['pIVA'].",
                ".$DatosParaElInsert['pCASalario'].")
                ");
            
            //Registro el cuerpo de la cotizacion
            foreach($ArrayProductosCotizados as $ProductoARegistrar){
                $pedazos = explode("x", $ProductoARegistrar, 2);
                $ProductoConsultado = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE `id` = ".$pedazos[0]);
                $ProductoConsultado = $ProductoConsultado[0];
                if($ProductoConsultado['idCategoria'] > 2){
                    $CantidadYDia = explode('.', $pedazos[1]);
                    $PrecioMultiplicado = ($ProductoConsultado['precio'] * $CantidadYDia[0] * $CantidadYDia[1]);
                    
                }else{
                    if($ProductoConsultado['idCategoria']==1){
                        $PrecioMultiplicado = ($ProductoConsultado['precio'] * $pedazos[1]);
                    }else{
                        $depreciacion = 1;
                        $search2 = $BaseDeDatos->consultar("SELECT * FROM `depreciacion` WHERE `idProducto` = ".$pedazos[0]);
                        if(!empty($search2)){
                            $depreciacion = $search2[0]['valor'];
                        }

                        $PrecioMultiplicado = ($ProductoConsultado['precio'] * $pedazos[1] * $depreciacion);
                    }
                }
                

                $BaseDeDatos->ejecutar("INSERT INTO `cuerpocotizacion`(`idCotizacion`, `idProducto`, `cantidad`, `precioUnitario`, `precioMultiplicado`) VALUES (
                    ".$IDCotizacionInsertada.", 
                    ".$ProductoConsultado['id'].", 
                    ".$pedazos[1].", 
                    ".$ProductoConsultado['precio'].", 
                    ".$PrecioMultiplicado."
                )");
            }

            //Creo registro en historial
            if($DatosParaElInsert['idEstado'] != 36){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 4, $IDCotizacionInsertada, 'Se ha creado la cotización #'.$IDCotizacionInsertada);
            }
            
            
        }

    }

    public function ValidarDatos($Datos, $Estado){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = array();


        if(!empty($Datos['RifCliente'])){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `clientes` WHERE `rif` = ".$Datos['RifCliente']);
            if(empty($DatosDeLaConsulta[0])){
                $ListaDeErrores [] = "El cliente no existe¿";
            }
        }

        if(empty($Datos['Nombre'])){
            $ListaDeErrores [] = "Nombre de la cotización está vacío¿";
        }

        if(isset($Datos['FechaExpiracion'])){
            if(isset($Datos['DiasDeVigencia'])){
                if($Datos['DiasDeVigencia'] < 1){
                    $ListaDeErrores [] = "Si la cotización cuenta con límite de tiempo, este debe ser mayor a 0 días¿";
                }
            }else{
                $ListaDeErrores [] = "No se encontró el numero de dias de vigencia¿";
            }
        }

        if(empty($Datos['IDsDeMateriales']) && empty($Datos['IDsDeMaquinaria']) && empty($Datos['IDsDeManoDeObra'])){
            $ListaDeErrores [] = "Esta cotización no cuenta con ningún producto¿";
        }else{
            //Verificar los materiales por separado
            foreach(array_merge(explode('¿', $Datos['IDsDeMateriales']), explode('¿', $Datos['IDsDeMaquinaria']), explode('¿', $Datos['IDsDeManoDeObra'])) as $Producto){
                if(!empty($Producto)){
                    $pedazos = explode('x', $Producto);

                    if(intval($pedazos[0]) != 0){
                        if(empty($BaseDeDatos->consultar("SELECT `nombre` FROM `productos` WHERE (`id` = ".$pedazos[0]." AND `idEstado` != 4 AND `idEstado` != 5)"))){
                            $ListaDeErrores [] = "La ID ".$pedazos[0]." no existe¿";
                        }
                    }else{
                        $ListaDeErrores [] = "La ID ".$pedazos[0]." no es una ID de producto válida¿";
                    }
                }
            }
        }

        if(empty($Datos['Utilidades'])){
            $ListaDeErrores [] = 'Porcentaje de utilidades no puede estar vacío¿';
        }
        if(empty($Datos['IVA'])){
            $ListaDeErrores [] = 'Porcentaje de I.V.A. no puede estar vacío¿';
        }

        return $ListaDeErrores;

    }
}

//////////USUARIO//////////////
class usuario {
    public $nombreDeUsuario;
    public $contrasenia;
    public $idNivelDeUsuario;
    public $preguntasDeSeguridad;
    public $respuestasDeSeguridad;
    public $tipoDeDocumento;
    public $cedula;
    public $nombres;
    public $idEstado;


    function crearNuevoUsuario($givenData){
        $BaseDeDatos = new conexion();
        $result[] = array();
        $problemas = array();

        

        if(empty($givenData['tipoDeDocumento'])){
            $problemas[] = 'No se especificó el tipo de documento.';
        }

        if(empty($givenData['cedula'])){
            $problemas[] = 'Cedula está vacío.';
        }else{
            if(!is_numeric($givenData['cedula'])){
                $problemas[]='Cédula debe contener un valor numérico.';
            }else{
                if(strlen($givenData['cedula'])>9){
                    $problemas[]='Cédula debe comprender una longitud máximad de 9 caractéres.';
                }
            }
        }

        if(empty($givenData['sexo'])){
            $problemas[] = 'No se especificó el sexo.';
        }

        if(empty($givenData['nombres'])){
            $problemas[] = 'Nombre y apellido está vacío.';
        }else{
            if(strlen($givenData['nombres']) > 50){
                $problemas[] = 'Nombre y apellido debe comprender una longitud máximad de 50 caractéres.';
            }
        }

        if(empty($givenData['nombreDeUsuario'])){
            $problemas[] = 'Nombre de usuario está vacío.';
        }else{
            if(strlen($givenData['nombreDeUsuario']) > 30){
                $problemas[] = 'Nombre de usuario debe comprender una longitud máximad de 30 caractéres.';
            }else{
                $search = $BaseDeDatos->consultar("SELECT * FROM `usuarios` WHERE `nombreDeUsuario` = '".$givenData['nombreDeUsuario']."'");
                if(!empty($search)){
                    $problemas[] = 'El nombre de usuario ya existe';
                }
            }
        }

        if(empty($givenData['contrasenia'])){
            $problemas[] = 'Contraseña está vació';
        }else{
            if(strlen($givenData['contrasenia']<8 || strlen($givenData['contrasenia'])>20)){
                $problemas = 'La contraseña debe comprender una longitud entre 8 y 20 caractéres';
            }
        }

        $pre_1 = 'custom1';
        if(empty($givenData['pregunta1'])){
            if(empty($givenData['custom1'])){
                $problemas[]='No se especificó la pregunta personalizada #1';
            }else{
                if(strlen($givenData['custom1'])>30){
                    $problemas[] = 'La pregunta personalizada #1 debe compreder una longitud de máximo 30 caractéres';
                }
            }
        }else{
            $search = $BaseDeDatos->consultar("SELECT * FROM `preguntas` WHERE `id` = ".$givenData['pregunta1']);
            if(empty($search)){
                $problemas[] = 'No se encontró la pregunta seleccionada #1';
            }else{
                $pre_1 = $givenData['pregunta1'];
            }
        }

        $pre_2 = 'custom2';
        if(empty($givenData['pregunta2'])){
            if(empty($givenData['custom2'])){
                $problemas[]='No se especificó la pregunta personalizada #2';
            }else{
                if(strlen($givenData['custom2'])>30){
                    $problemas[] = 'La pregunta personalizada #2 debe compreder una longitud de máximo 30 caractéres';
                }
            }
        }else{
            $search = $BaseDeDatos->consultar("SELECT * FROM `preguntas` WHERE `id` = ".$givenData['pregunta2']);
            if(empty($search)){
                $problemas[] = 'No se encontró la pregunta seleccionada #2';
            }else{
                $pre_2 = $givenData['pregunta2'];
            }
        }

        $pre_3 = 'custom3';
        if(empty($givenData['pregunta3'])){
            if(empty($givenData['custom3'])){
                $problemas[]='No se especificó la pregunta personalizada #3';
            }else{
                if(strlen($givenData['custom3'])>30){
                    $problemas[] = 'La pregunta personalizada #3 debe compreder una longitud de máximo 30 caractéres';
                }
            }
        }else{
            $search = $BaseDeDatos->consultar("SELECT * FROM `preguntas` WHERE `id` = ".$givenData['pregunta3']);
            if(empty($search)){
                $problemas[] = 'No se encontró la pregunta seleccionada #3';
            }else{
                $pre_3 = $givenData['pregunta3'];
            }
        }

        if($pre_1 == $pre_2 || $pre_2 == $pre_3 || $pre_1 == $pre_3){
            $problemas[] = 'Las preguntas de recuperación deben ser diferentes';
        }else{
            if(empty($givenData['respuesta1'])){
                $problemas[] = 'Respuesta de recuperación #1 está vacío.';
            }else{
                if(strlen($givenData['respuesta1'])>20){
                    $problemas[] = 'Respuesta de recuperación #1 debe comprender una longitud máxima de 20 caractéres.';
                }
            }

            if(empty($givenData['respuesta2'])){
                $problemas[] = 'Respuesta de recuperación #2 está vacío.';
            }else{
                if(strlen($givenData['respuesta2'])>20){
                    $problemas[] = 'Respuesta de recuperación #2 debe comprender una longitud máxima de 20 caractéres.';
                }
            }

            if(empty($givenData['respuesta3'])){
                $problemas[] = 'Respuesta de recuperación #3 está vacío.';
            }else{
                if(strlen($givenData['respuesta3'])>20){
                    $problemas[] = 'Respuesta de recuperación #3 debe comprender una longitud máxima de 20 caractéres.';
                }
            }
        }

        if(!empty($problemas)){throw new Exception(implode('¿', $problemas));}

        if(empty($givenData['idNivelDeUsuario'])){
            $problemas[]='Perfil de usuario no fue especificado';
        }else{
            if($givenData['idNivelDeUsuario']!=1 && $givenData['idNivelDeUsuario']!=2 && $givenData['idNivelDeUsuario']!=3){
                $problemas[]='Nivel de usuario es inválido';
            }
        }

        if(!empty($problemas)){throw new Exception(implode('¿', $problemas));}

        if(empty($givenData['permisos'])){
            $problemas[]='No se han encontrado los permisos del usuario.';
        }else{
            $permisosEncontrados = array();
            $idModulos = array();
            foreach($givenData['permisos'] as $permiso){
                if(!empty($permiso)){
                    $permisosEncontrados[] = $permiso;
                }
            }
            if(count($permisosEncontrados)<1){
                $problemas[]='No se ha indicado ningún permiso al usuario.';
            }else{
                foreach($permisosEncontrados as $permisoencontrado){
                    $search = $BaseDeDatos->consultar("SELECT * FROM `modulos` WHERE `nombre` = '$permisoencontrado'");
                    if(empty($search)){
                        $problemas[] = 'El módulo '.$permisoencontrado.' no existe.';
                    }else{
                        $idModulos[] = $search[0]['id'];
                    }
                }
            }
        }

        if(!empty($problemas)){throw new Exception(implode('¿', $problemas));}


        if($givenData['pregunta1'] == '0'){
            $result['sqlcustom1'] = "INSERT INTO `preguntas`(`pregunta`) VALUES ('".$givenData['custom1']."')";
            $givenData['pregunta1'] = $BaseDeDatos->ejecutar($result['sqlcustom1']);
        }
        if($givenData['pregunta2'] == '0'){
            $result['sqlcustom2'] = "INSERT INTO `preguntas`(`pregunta`) VALUES ('".$givenData['custom2']."')";
            $givenData['pregunta2'] = $BaseDeDatos->ejecutar($result['sqlcustom2']);
        }
        if($givenData['pregunta3'] == '0'){
            $result['sqlcustom3'] = "INSERT INTO `preguntas`(`pregunta`) VALUES ('".$givenData['custom3']."')";
            $givenData['pregunta3'] = $BaseDeDatos->ejecutar($result['sqlcustom3']);
        }

        $constraseniaCrypted = password_hash(strtolower($givenData['contrasenia']), PASSWORD_DEFAULT, ['cost' => 10]);
        $respuesta1Crypted = password_hash(strtolower($givenData['respuesta1']), PASSWORD_DEFAULT, ['cost' => 10]);
        $respuesta2Crypted = password_hash(strtolower($givenData['respuesta2']), PASSWORD_DEFAULT, ['cost' => 10]);
        $respuesta3Crypted = password_hash(strtolower($givenData['respuesta3']), PASSWORD_DEFAULT, ['cost' => 10]);

        $result['sqlInsert'] = "INSERT INTO `usuarios`(`tipoDeDocumento`, `cedula`, `nombres`, `sexo`, `nombreDeUsuario`, `contrasenia`, `idNivelDeUsuario`, `idPregunta1`, `idPregunta2`, `idPregunta3`, `respuesta1`, `respuesta2`, `respuesta3`, `idEstado`) VALUES (
            '".$givenData['tipoDeDocumento']."', 
            '".$givenData['cedula']."', 
            '".$givenData['nombres']."', 
            '".$givenData['sexo']."', 
            '".$givenData['nombreDeUsuario']."', 
            '".$constraseniaCrypted."', 
            '".$givenData['idNivelDeUsuario']."', 
            '".$givenData['pregunta1']."',
            '".$givenData['pregunta2']."',
            '".$givenData['pregunta3']."',
            '".$respuesta1Crypted."', 
            '".$respuesta2Crypted."', 
            '".$respuesta3Crypted."', 
            41
        )";

        
        $BaseDeDatos->ejecutar($result['sqlInsert']);
        foreach($idModulos as $idMod){
            $BaseDeDatos->ejecutar("INSERT INTO `modulospermitidos`(`usuario`, `idModulo`) VALUES ('".$givenData['nombreDeUsuario']."', $idMod)");
        }

        $Auditoria = new historial();
        $Auditoria->CrearNuevoRegistro(1, 5, $givenData['nombreDeUsuario'], 'Se ha creado el usuario #'.$givenData['nombreDeUsuario']);


        
        
        //return $result;
    }

    function ConsultarUsuarios(){
        $BaseDeDatos = new conexion();
        $search = $BaseDeDatos->consultar("SELECT `usuarios`.*, `nivelesdeusuario`.`nombre` AS 'perfil'  FROM `usuarios` INNER JOIN `nivelesdeusuario` ON `usuarios`.`idNivelDeUsuario` = `nivelesdeusuario`.`id`");
        return $search;
    }

    function ActualizarRespuestas($DatosAGuardar){
        $BaseDeDatos = new conexion();

        //Valido los datos
        $ListaDeErrores = array();

        if(!isset($DatosAGuardar['Pregunta1'])){
            $ListaDeErrores [] = "La pregunta #1 no fue definida¿";
        }else{
            if(!is_numeric($DatosAGuardar['Pregunta1'])){
                $ListaDeErrores [] = "La pregunta #1 tiene un valor inválido¿";
            }
        }
        if(!isset($DatosAGuardar['Pregunta2'])){
            $ListaDeErrores [] = "La pregunta #2 no fue definida¿";
            if(!is_numeric($DatosAGuardar['Pregunta2'])){
                $ListaDeErrores [] = "La pregunta #2 tiene un valor inválido¿";
            }
        }
        if(!isset($DatosAGuardar['Pregunta3'])){
            $ListaDeErrores [] = "La pregunta #3 no fue definida¿";
            if(!is_numeric($DatosAGuardar['Pregunta3'])){
                $ListaDeErrores [] = "La pregunta #3 tiene un valor inválido¿";
            }
        }

        if(empty($ListaDeErrores)){
            if($DatosAGuardar['Pregunta3'] == $DatosAGuardar['Pregunta2'] || $DatosAGuardar['Pregunta3'] == $DatosAGuardar['Pregunta1']){
                $ListaDeErrores [] = "La pregunta #3 ya fue seleccionada¿";
            }
            if($DatosAGuardar['Pregunta2'] == $DatosAGuardar['Pregunta1']){
                $ListaDeErrores [] = "La pregunta #2 ya fue seleccionada¿";
            }
        }

        if(empty($ListaDeErrores)){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `preguntas` WHERE `id` = ".$DatosAGuardar['Pregunta1']);
            if(empty($DatosDeLaConsulta)){
                $ListaDeErrores [] = "La pregunta #1 no existe¿";
            }
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `preguntas` WHERE `id` = ".$DatosAGuardar['Pregunta2']);
            if(empty($DatosDeLaConsulta)){
                $ListaDeErrores [] = "La pregunta #2 no existe¿";
            }
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `preguntas` WHERE `id` = ".$DatosAGuardar['Pregunta3']);
            if(empty($DatosDeLaConsulta)){
                $ListaDeErrores [] = "La pregunta #3 no existe¿";
            }
        }

        if(empty($ListaDeErrores)){
            if(strlen($DatosAGuardar['Respuesta1']) > 20){
                $ListaDeErrores [] = "La respuesta #1 debe tener un máximo de 20 caractéres¿";
            }
            if(strlen($DatosAGuardar['Respuesta2']) > 20){
                $ListaDeErrores [] = "La respuesta #2 debe tener un máximo de 20 caractéres¿";
            }
            if(strlen($DatosAGuardar['Respuesta3']) > 20){
                $ListaDeErrores [] = "La respuesta #3 debe tener un máximo de 20 caractéres¿";
            }
        }

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            $Auditoria = new historial();
            
            if($this->preguntasDeSeguridad['idPregunta1'] != $DatosAGuardar['Pregunta1']){
                $BaseDeDatos->ejecutar("UPDATE `usuarios` SET `idPregunta1`=".$DatosAGuardar['Pregunta1']." WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'"); 
                $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se ha actualizado la pregunta de seguridad #1 del usuario #'.$this->nombreDeUsuario);
            }
            if($this->preguntasDeSeguridad['idPregunta2'] != $DatosAGuardar['Pregunta2']){
                $BaseDeDatos->ejecutar("UPDATE `usuarios` SET `idPregunta2`=".$DatosAGuardar['Pregunta2']." WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'");
                $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se ha actualizado la pregunta de seguridad #2 del usuario #'.$this->nombreDeUsuario);
            }
            if($this->preguntasDeSeguridad['idPregunta3'] != $DatosAGuardar['Pregunta3']){
                $BaseDeDatos->ejecutar("UPDATE `usuarios` SET `idPregunta3`=".$DatosAGuardar['Pregunta3']." WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'");
                $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se ha actualizado la pregunta de seguridad #3 del usuario #'.$this->nombreDeUsuario);
            }
            if(!empty($DatosAGuardar['Respuesta1'])){
                $RespuestaEncriptada = password_hash(strtolower($DatosAGuardar['Respuesta1']), PASSWORD_DEFAULT, ['cost' => 10]);
                $BaseDeDatos->ejecutar("UPDATE `usuarios` SET `respuesta1`='".$RespuestaEncriptada."' WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'");
                $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se ha actualizado la respuesta de seguridad #1 del usuario #'.$this->nombreDeUsuario);
            }
            if(!empty($DatosAGuardar['Respuesta2'])){
                $RespuestaEncriptada = password_hash(strtolower($DatosAGuardar['Respuesta2']), PASSWORD_DEFAULT, ['cost' => 10]);
                $BaseDeDatos->ejecutar("UPDATE `usuarios` SET `respuesta2`='".$RespuestaEncriptada."' WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'");
                $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se ha actualizado la respuesta de seguridad #2 del usuario #'.$this->nombreDeUsuario);
            }
            if(!empty($DatosAGuardar['Respuesta3'])){
                $RespuestaEncriptada = password_hash(strtolower($DatosAGuardar['Respuesta3']), PASSWORD_DEFAULT, ['cost' => 10]);
                $BaseDeDatos->ejecutar("UPDATE `usuarios` SET `respuesta3`='".$RespuestaEncriptada."' WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'");
                $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se ha actualizado la respuesta de seguridad #3 del usuario #'.$this->nombreDeUsuario);
            }
        }

        
        
    }

    function ActualizarContrasenia($DatosAGuardar){
        $BaseDeDatos = new conexion();

        //Valido los datos
        $ListaDeErrores = array();
        
        
        if(!isset($DatosAGuardar['Contrasenia'])){
            $ListaDeErrores [] = "Contraseña no puede estar vacío¿";
        }else{
            if(strlen($DatosAGuardar['Contrasenia']) < 8){
                $ListaDeErrores [] = "La contraseña debe tener una longitud mayor a 8 caractéres¿";
            }
            if(strlen($DatosAGuardar['Contrasenia']) > 20){
                $ListaDeErrores [] = "La contraseña debe tener una longitud menor a 20 caractéres¿";
            }
        }
        


        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            $ContraEncriptada = password_hash(strtolower($DatosAGuardar['Contrasenia']), PASSWORD_DEFAULT, ['cost' => 10]);

            $BaseDeDatos->ejecutar("UPDATE `usuarios` SET `contrasenia`='".$ContraEncriptada."' WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'");

            //Dejo la huella en historial
            $Auditoria = new historial();
            $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se ha actualizado la contraseña del usuario #'.$this->nombreDeUsuario);
        }

    }

    function ActualizarDatosPersonales($DatosAGuardar){
        $BaseDeDatos = new conexion();

        //Valido los datos
        $ListaDeErrores = array();
        if(empty($DatosAGuardar['tipoDeDocumento'])){
            $ListaDeErrores [] = "Tipo de documento no puede estar vacío¿";
        }
        if(empty($DatosAGuardar['cedula'])){
            $ListaDeErrores [] = "Cédula no puede estar vacío¿";
        }
        if(empty($DatosAGuardar['nombres'])){
            $ListaDeErrores [] = "Nombre y apellido no puede estar vacío¿";
        }
        if(empty($DatosAGuardar['sexo'])){
            $ListaDeErrores [] = "Sexo no puede estar vacío¿";
        }


        if(!empty($ListaDeErrores)){
            return implode($ListaDeErrores);
        }else{
            //Preparo los datos
            $DatosListos = array(
                'tipoDeDocumento' => "'".$DatosAGuardar['tipoDeDocumento']."'",
                'cedula' => $DatosAGuardar['cedula'],
                'nombres' => "'".$DatosAGuardar['nombres']."'",
                'sexo' => "'".$DatosAGuardar['sexo']."'",
            );

            $BaseDeDatos->ejecutar("UPDATE `usuarios` SET 
            `tipoDeDocumento`=".$DatosListos['tipoDeDocumento'].",
            `cedula`=".$DatosListos['cedula'].",
            `nombres`=".$DatosListos['nombres'].",
            `sexo`=".$DatosListos['sexo']." WHERE `nombreDeUsuario`  = '".$this->nombreDeUsuario."'");

            //Dejo la huella en historial
            $Auditoria = new historial();
            $Auditoria->CrearNuevoRegistro(2, 5, $this->nombreDeUsuario, 'Se han actualizado los datos personales del usuario #'.$this->nombreDeUsuario);
        }


        
    }
    
    function __construct($UsuarioACargar){
        $BaseDeDatos = new conexion();

        if(!empty($UsuarioACargar)){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT 
            `usuarios`.`nombreDeUsuario`, 
            `usuarios`.`contrasenia`, 
            `usuarios`.`idNivelDeUsuario`, 
            `usuarios`.`tipoDeDocumento`, 
            `usuarios`.`cedula`, 
            `usuarios`.`sexo`, 
            `usuarios`.`nombres`, 
            `usuarios`.`idEstado`, 
            `usuarios`.`idPregunta1`, 
            (SELECT `pregunta` FROM `preguntas` WHERE `preguntas`.`id` = `usuarios`.`idPregunta1`) AS 'pregunta1', `usuarios`.`respuesta1`,
            `usuarios`.`idPregunta2`, 
            (SELECT `pregunta` FROM `preguntas` WHERE `preguntas`.`id` = `usuarios`.`idPregunta2`) AS 'pregunta2', `usuarios`.`respuesta2`,
            `usuarios`.`idPregunta3`, 
            (SELECT `pregunta` FROM `preguntas` WHERE `preguntas`.`id` = `usuarios`.`idPregunta3`) AS 'pregunta3', `usuarios`.`respuesta3`
            FROM `usuarios` 
            INNER JOIN `preguntas` ON (`usuarios`.`idPregunta1` = `preguntas`.`id` OR `usuarios`.`idPregunta2` = `preguntas`.`id` OR `usuarios`.`idPregunta3` = `preguntas`.`id`) 
            WHERE `nombreDeUsuario` = '".$UsuarioACargar."'");

            if(empty($DatosDeLaConsulta)){
                throw new Exception("El usuario no existe");
            }else{
                $DatosACargar = $DatosDeLaConsulta[0];
                $this->sexo = $DatosACargar['sexo'];
                $this->nombreDeUsuario = $UsuarioACargar;
                $this->contrasenia = $DatosACargar['contrasenia'];
                $this->idNivelDeUsuario = $DatosACargar['idNivelDeUsuario'];
                $this->tipoDeDocumento = $DatosACargar['tipoDeDocumento'];
                $this->cedula = $DatosACargar['cedula'];
                $this->nombre = $DatosACargar['nombres'];
                
                
                
                $this->idEstado = $DatosACargar['idEstado'];
                $this->preguntasDeSeguridad = array(
                    "idPregunta1" => $DatosACargar['idPregunta1'],
                    "pregunta1" => $DatosACargar['pregunta1'],
                    "respuesta1" => $DatosACargar['respuesta1'],
                    "idPregunta2" => $DatosACargar['idPregunta2'],
                    "pregunta2" => $DatosACargar['pregunta2'],
                    "respuesta2" => $DatosACargar['respuesta2'],
                    "idPregunta3" => $DatosACargar['idPregunta3'],
                    "pregunta3" => $DatosACargar['pregunta3'],
                    "respuesta3" => $DatosACargar['respuesta3']
                );
            }
        }
    }

    public function ObtenerDatos(){
        $BaseDeDatos = new conexion();

        $ConsultaDeNivelDeU = $BaseDeDatos->consultar("SELECT * FROM `nivelesdeusuario` WHERE `id` = ".$this->idNivelDeUsuario);
        $ConsultaDelUsuario = $BaseDeDatos->consultar("SELECT `sexo` FROM `usuarios` WHERE `nombreDeUsuario` = '".$this->nombreDeUsuario."'");
        $ConsultaDeHistorial = $BaseDeDatos->consultar("SELECT * FROM `historial` WHERE ( (`idTipoDeHuella` = 1) AND (`idDeEntidad` = '".$this->nombreDeUsuario."') )");

        if(empty($ConsultaDeHistorial)){
            $FechaDeCreacion = "Desconocido";
        }else{
            $FechaDeCreacion = $ConsultaDeHistorial[0]['fechaCreacion'];
        }

        return array(
            "nombreDeusuario" => $this->nombreDeUsuario,
            "contrasenia" => $this->contrasenia,
            "idNivelDeUsuario" => $this->idNivelDeUsuario,
            "tipoDeDocumento" => $this->tipoDeDocumento,
            "cedula" => $this->cedula,
            "nombres" => $this->nombre,
            "idEstado" => $this->idEstado,
            "preguntasDeSeguridad" => $this->preguntasDeSeguridad,
            "nivelDeUsuario" => $ConsultaDeNivelDeU[0]['nombre'],
            "sexo" => $ConsultaDelUsuario[0]['sexo'],
            "fechaCreacion" => $FechaDeCreacion
        );
    }

    public function Saludar(){
        return "Hola, soy ".$this->nombreDeUsuario;
    }

    public function CerrarSesion(){
        return session_destroy();
    }

    public function IniciarSesion($contraseniaRecibida){
        if(empty($contraseniaRecibida)){
            throw new Exception("Contraseña vacía");
        }else{
            if(password_verify(strtolower($contraseniaRecibida), $this->contrasenia)){
                if($this->idEstado == 42){
                    throw new Exception("Este usuario se encuentra inhabilitado");
                }
                session_start();
                $_SESSION["nombreDeUsuario"] = $this->nombreDeUsuario;
                $_SESSION["idNivelDeUsuario"]= $this->idNivelDeUsuario;
                $_SESSION["UsuarioLogeado"] = serialize($this);
                return true;
            }else{
                throw new Exception("La contraseña es incorrecta");
            }
        }
        
    }

    public function VerificarPermisoSegunModulo($ModuloAPreguntar){
        $ListaDeModulosPermitidos = $this->MostrarListaDePermisos();

        foreach($ListaDeModulosPermitidos as $ModuloPermitido){
            if($ModuloPermitido['nombre'] == $ModuloAPreguntar){
                return true;
            }
        }
        return false;
    }

    public function MostrarListaDePermisos(){
        $BaseDeDatos = new conexion();
        
        return $BaseDeDatos->consultar("SELECT 
        `modulos`.`nombre`,
        `modulos`.`nombreDeImagen` 
        FROM `modulospermitidos` 
        INNER JOIN `modulos` ON `modulospermitidos`.`idModulo` = `modulos`.`id`
        WHERE `usuario` = '".$this->nombreDeUsuario."';");
    }

}


//////////CONEXION//////////////
class conexion{
    protected $servidor="localhost";
    protected $usuario="root";
    protected $contrasenia="";
    protected $conexion;

    protected $claveDeCrypt = 'Polaroid';

    public function __construct(){
        try{
            $this->conexion = new PDO("mysql:host=$this->servidor;dbname=CLEODataBase", $this->usuario, $this->contrasenia);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $error){
            return "Error de conexion: ".$error;
        }
    }

    public function ejecutar($sql){
        $this->conexion->exec($sql);
        return $this->conexion->lastInsertId();
    }

    public function consultar($sql){
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll();
    }

    
}

//////// CLIENTE /////////
class cliente{
    private $tipoDeDocumento;
    private $rif;
    private $nombre;
    private $idContacto;
    private $idEstado;
    private $ULRImagen;

    public function ObtenerCotizaciones($Filtros){
        $BaseDeDatos = new conexion();

        //return $Filtros;

        if(empty($Filtros['descripcion'])){
            $sqldescripcion = '(1=1)';
        }else{
            $sqldescripcion = "(`nombre` LIKE '%".$Filtros['descripcion']."%')";
        }

        if(empty($Filtros['estado'])){
            $sqlEstado = "(`idEstado`<35)";
        }else{
            $sqlEstado = "(`idEstado`=".$Filtros['estado'].")";
        }
        
        $search = $BaseDeDatos->consultar("SELECT * FROM `cotizaciones` WHERE (`cedulaCliente` = '$this->rif' AND $sqldescripcion AND $sqlEstado)");
        
        
        return $search;
    }

    function __construct($idACargar){
        $BaseDeDatos = new conexion();

        if($idACargar!=0){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `clientes` 
            WHERE `rif` = ".$idACargar);

            if(!empty($DatosDeLaConsulta)){
                $DatosACargar = $DatosDeLaConsulta[0];

                $this->rif = $idACargar;
                $this->tipoDeDocumento = $DatosACargar['tipoDeDocumento'];
                $this->nombre = $DatosACargar['nombre'];
                $this->idContacto = $DatosACargar['idContacto'];
                $this->idEstado = $DatosACargar['idEstado'];
                $this->ULRImagen = $DatosACargar['ULRImagen'];
            }

        }
    }

    public function ListarClientes($Filtros){
        $FiltroLIKE = ((empty($Filtros['descripcion']))?'':' AND (`rif` LIKE "%'.$Filtros['descripcion'].'%" OR `nombre` LIKE "%'.$Filtros['descripcion'].'%")');
        $BaseDeDatos = new conexion();

        if(isset($Filtros['id'])){
            if(is_numeric($Filtros['id'])){
                $FiltroLIKE = ' AND `rif` = '.$Filtros['id'];
            }
        }
        
        return $BaseDeDatos->consultar("SELECT 
        `clientes`.`rif`,
        `clientes`.`tipoDeDocumento`,
        `clientes`.`nombre`,
        `clientes`.`ULRImagen`,
        `contactos`.`direccion`,
        `contactos`.`telefono1`,
        `contactos`.`telefono2`,
        `contactos`.`correo`
        FROM `clientes` INNER JOIN `contactos` ON `clientes`.`idContacto` = `contactos`.`id` WHERE ( `idEstado` = 11 ".$FiltroLIKE.")");
    }

    function ObtenerDatos(){
        $BaseDeDatos = new conexion();
        if(!empty($this->rif)){
            $ContactoDelProveedor = new contacto($this->idContacto);
            $DatosDelContacto = $ContactoDelProveedor->ObtenerDatos();

            return array(
                "rif" => $this->rif,
                "tipoDeDocumento" => $this->tipoDeDocumento,
                "nombre" => $this->nombre,
                "idContacto" => $this->idContacto,
                "idEstado" => $this->idEstado,
                "ULRImagen" => $this->ULRImagen,
                "numeroCompleto1" => $DatosDelContacto['numeroCompleto1'],
                "numeroCompleto2" => $DatosDelContacto['numeroCompleto2'],
                "correo" => $DatosDelContacto['correo'],
                "direccion" => $DatosDelContacto['direccion']
                
            );
        }
    }

    function CrearNuevo($DatosAGuardar, $Archivos, $Estado){
        $BaseDeDatos = new conexion();
        $ContactoFantasma = new contacto(0);

        //Valido los datos
        $ErroresDelCliente = $this->ValidarDatos($DatosAGuardar, $Estado);
        $ErroresDelContacto = $ContactoFantasma->ValidarDatos($DatosAGuardar, $Estado);
        $ListaDeErrores = array_merge($ErroresDelCliente, $ErroresDelContacto);
        
        //Si hay errores, los arrojo; si no los hay, procedo
        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Preparo los datos para el insert
            $DatosAGuardar['tipoDeDocumento'] = "'".$DatosAGuardar['tipoDeDocumento']."'";
            $DatosAGuardar['nombre'] = "'".strtoupper($DatosAGuardar['nombre'])."'";

            //Si recibo una imagen la guardo; si no, establezco NULL//
            if(empty($Archivos['ULRImagen']['name'])){
                $ImagenAGuardar="NULL";
            }else{
                $tiempo = new DateTime();
                $NuevoNombreIMG=$tiempo->getTimestamp()."_".$Archivos['ULRImagen']['name'];
                $ImagenAGuardar="'".$NuevoNombreIMG."'";
                $ImagenRecibida=$Archivos['ULRImagen']['tmp_name'];
                move_uploaded_file($ImagenRecibida,"../../Imagenes/Clientes/".$NuevoNombreIMG);
            }

            //creo el contacto
            $IdDelContacto = $ContactoFantasma->CrearNuevo($DatosAGuardar);

            if($Estado != 12){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 3, $DatosAGuardar['rif'], 'Se ha registrado el cliente #'.$DatosAGuardar['rif']);
            }
            

            return $BaseDeDatos->ejecutar("INSERT INTO `clientes`(`rif`, `tipoDeDocumento`, `nombre`, `idContacto`, `idEstado`, `ULRImagen`) VALUES (
                ".$DatosAGuardar['rif'].", 
                ".$DatosAGuardar['tipoDeDocumento'].", 
                ".$DatosAGuardar['nombre'].", 
                ".$IdDelContacto.", 
                ".$Estado.", 
                ".$ImagenAGuardar.")");
        }
        
    }

    function ValidarDatos($Datos, $Estado){
        $ListaDeErrores = array();

        if(empty($Datos['rif'])){
            $ListaDeErrores[] = "RIF  está vacío¿";
        }
        if($Estado!=12 && empty($Datos['nombre']) && $Estado != 13){
            $ListaDeErrores[] = "Nombre está vacío¿";
        }
        if(empty($Datos['tipoDeDocumento'])){
            $ListaDeErrores[] = "Tipo de documento está vacío¿";
        }

        return $ListaDeErrores;
    }

    function ActualizarDatos($DatosNuevos, $Archivos, $NuevoEstado){
        $BaseDeDatos = new conexion();
        $ContactoActual = new contacto($this->idContacto);

        //Valido los datos
        $ErroresDelCliente = $this->ValidarDatos($DatosNuevos, $NuevoEstado);
        $ErroresDelContacto = $ContactoActual->ValidarDatos($DatosNuevos, $NuevoEstado);
        $ListaDeErrores = array_merge($ErroresDelCliente, $ErroresDelContacto);

        //Si hay errores, los arrojo; si no los hay, procedo
        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Si recibo una imagen, borro la vieja y cargo la nueva//
            $ImagenNueva = "";
            if(empty($Archivos['ULRImagen']['name'])){
                if(empty($this->ULRImagen)){
                    $ImagenNueva = "NULL";
                }else{
                    $ImagenNueva = "'".$this->ULRImagen."'";
                }
            }else{
                if(!empty($this->ULRImagen)){
                    unlink('../../Imagenes/clientes/'.$this->ULRImagen);
                }
                $tiempo = new DateTime();
                $NuevoNombreIMG = $tiempo->getTimestamp()."_".$Archivos['ULRImagen']['name'];
                $ImagenNueva = "'".$NuevoNombreIMG."'";
                $ImagenRecibida = $Archivos['ULRImagen']['tmp_name'];
                move_uploaded_file($ImagenRecibida,"../../Imagenes/clientes/".$NuevoNombreIMG);
            }

            //Actualizo el contacto
            $ContactoActual->ActualizarDatos($DatosNuevos);

            //Perparo los datos para el update
            $DatosNuevos['nombre'] = "'".strtoupper($DatosNuevos['nombre'])."'";
            $DatosNuevos['tipoDeDocumento'] = "'".$DatosNuevos['tipoDeDocumento']."'";

            if($this->$idEstado == 12 && $NuevoEstado != 12){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(2, 3, $this->rif, 'Se ha registrado el proveedor #'.$this->rif);
            }

            return $BaseDeDatos->ejecutar("UPDATE `clientes` SET 
            `tipoDeDocumento` = ".$DatosNuevos['tipoDeDocumento'].", 
            `nombre` = ".$DatosNuevos['nombre'].", 
            `idEstado`= ".$NuevoEstado.", 
            `ULRImagen`= ".$ImagenNueva."
            WHERE `rif` = ".$this->rif);
        }
    }

    public function Eliminar(){
        $BaseDeDatos = new conexion();
        if(!empty($this->ULRImagen)){
            unlink('../../Imagenes/clientes/'.$this->ULRImagen);
        } 
        $BaseDeDatos->ejecutar("DELETE FROM `clientes` WHERE `rif` = ".$this->rif);    

    }

}

////////PROVEEDOR///////
class proveedor{
    private $tipoDeDocumento;
    private $rif;
    private $nombre;
    private $idContacto;
    private $idEstado;
    private $ULRImagen;
    

    function __construct($idACargar){
        $BaseDeDatos = new conexion();

        if($idACargar!=0){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT 
            `proveedores`.`tipoDeDocumento`,
            `proveedores`.`nombre`,
            `proveedores`.`idEstado`,
            `proveedores`.`ULRImagen`,
            `proveedores`.`idContacto`
            FROM `proveedores` 
            WHERE `rif` = ".$idACargar);

            if(!empty($DatosDeLaConsulta)){
                $DatosACargar = $DatosDeLaConsulta[0];

                $this->rif = $idACargar;
                $this->tipoDeDocumento = $DatosACargar['tipoDeDocumento'];
                $this->nombre = $DatosACargar['nombre'];
                $this->idContacto = $DatosACargar['idContacto'];
                $this->idEstado = $DatosACargar['idEstado'];
                $this->ULRImagen = $DatosACargar['ULRImagen'];
            }

            
        }
    }

    public function ObtenerDatos(){
        $BaseDeDatos = new conexion();
        if(!empty($this->rif)){
            $NumeroDeProductos = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `productosdeproveedor` WHERE `idProveedor` = ".$this->rif);
            $ProductosProveeidos = "";
            if($NumeroDeProductos[0][0] > 0){
                $ProductosDelProveedor = $BaseDeDatos->consultar("SELECT * FROM `productosdeproveedor` WHERE `idProveedor` = ".$this->rif);
                foreach($ProductosDelProveedor as $ProductoProveeido){
                    $ProductosProveeidos = $ProductosProveeidos.((empty($ProductosProveeidos))?$ProductoProveeido['idProducto']:"¿".$ProductoProveeido['idProducto']);
                }
            }
            $ContactoDelProveedor = new contacto($this->idContacto);
            $DatosDelContacto = $ContactoDelProveedor->ObtenerDatos();
            
            return array(
                "rif" => $this->rif,
                "tipoDeDocumento" => $this->tipoDeDocumento,
                "nombre" => $this->nombre,
                "idContacto" => $this->idContacto,
                "idEstado" => $this->idEstado,
                "ULRImagen" => $this->ULRImagen,
                "numeroDeProductos" => $NumeroDeProductos[0][0],
                "numeroCompleto1" => $DatosDelContacto['numeroCompleto1'],
                "numeroCompleto2" => $DatosDelContacto['numeroCompleto2'],
                "correo" => $DatosDelContacto['correo'],
                "direccion" => $DatosDelContacto['direccion'],
                "IndicarProductos" => (($NumeroDeProductos[0][0] > 0 )?"checked":""),
                "ProductosSeleccionados" => $ProductosProveeidos
            );
        }
    }

    public function ListarProveedores($Filtros){
        $FiltroLIKE = ((empty($Filtros['nombre']))?'':' WHERE (`rif` LIKE "%'.$Filtros['nombre'].'%" OR `nombre` LIKE "%'.$Filtros['nombre'].'%")');
        $BaseDeDatos = new conexion();

        $CounsultaDeProveedores = $BaseDeDatos->consultar("SELECT * FROM `proveedores`".$FiltroLIKE);

        $ArrayRespuesta = array();
        foreach($CounsultaDeProveedores as $RowProveedor){
            $Temporal = $RowProveedor;
            $ProveedorTemporal = new proveedor($RowProveedor['rif']);
            $LIstaDeProductos = $ProveedorTemporal->ObtenerListaDeProductos();

            array_push($Temporal, $LIstaDeProductos);
            $ArrayRespuesta [] = $Temporal;
        }
        
        return $ArrayRespuesta;
    }

    public function ObtenerListaDeProductos(){
        $BaseDeDatos = new conexion();
        return $BaseDeDatos->consultar("SELECT
        `productos`.`id`, 
        `productos`.`nombre`, 
        `productos`.`ULRImagen`, 
        `categorias`.`nombre` AS 'categoria' 
        FROM `productosdeproveedor` 
        INNER JOIN `productos` ON (`productosdeproveedor`.`idProducto` = `productos`.`id` AND `productos`.`idEstado` != 5 AND `productos`.`idEstado` != 4) 
        INNER JOIN `categorias` ON `productos`.`idCategoria` = `categorias`.`id` 
        WHERE `idProveedor` = ".$this->rif);
    }

    public function ActualizarDatos($DatosNuevos, $Archivos, $NuevoEstado){
        
        $BaseDeDatos = new conexion();
        $ContactoActual = new contacto($this->idContacto);

        //Validos los datos
        $ErroresDelProveedor = $this->ValidarDatos($DatosNuevos, $NuevoEstado);
        $ErroresDelContacto = $ContactoActual->ValidarDatos($DatosNuevos, $NuevoEstado);
        $ListaDeErrores = array_merge($ErroresDelProveedor, $ErroresDelContacto);

        //Si hay errores, los arrojo; si no los hay, procedo
        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Si recibo una imagen, borro la vieja y cargo la nueva//
            $ImagenNueva = "";
            if(empty($Archivos['ULRImagen']['name'])){
                if(empty($this->ULRImagen)){
                    $ImagenNueva = "NULL";
                }else{
                    $ImagenNueva = "'".$this->ULRImagen."'";
                }
            }else{
                if(!empty($this->ULRImagen)){
                    unlink('../../Imagenes/proveedores/'.$this->ULRImagen);
                }
                $tiempo = new DateTime();
                $NuevoNombreIMG = $tiempo->getTimestamp()."_".$Archivos['ULRImagen']['name'];
                $ImagenNueva = "'".$NuevoNombreIMG."'";
                $ImagenRecibida = $Archivos['ULRImagen']['tmp_name'];
                move_uploaded_file($ImagenRecibida,"../../Imagenes/proveedores/".$NuevoNombreIMG);
            }

            //Actualizo el contacto
            $ContactoActual->ActualizarDatos($DatosNuevos);

            //Perparo los datos para el update
            $DatosNuevos['nombre'] = strtoupper($DatosNuevos['nombre']);//nuevo
            
            //Actualizo productos del proveedor(Borro los viejos e inserto los nuevos)
            $DatosActuales = $this->ObtenerDatos();
            if($DatosActuales['ProductosSeleccionados'] != $DatosNuevos['ProductosSeleccionados']){
                $BaseDeDatos->ejecutar("DELETE FROM `productosdeproveedor` WHERE `idProveedor` = ".$DatosActuales['rif']);
                $NuevoEstado = '8';
                if(!empty($DatosNuevos['ProductosSeleccionados'])){
                    $IdesDeProductos = explode('¿', $DatosNuevos['ProductosSeleccionados']);
                    $valores = "";              

                    foreach($IdesDeProductos as $IDDeUnProductoSolito){
                        $valores = $valores.((empty($valores))?"(".$IDDeUnProductoSolito.", ".$DatosActuales['rif'].")":", (".$IDDeUnProductoSolito.", ".$DatosActuales['rif'].")");
                    }
                    $BaseDeDatos->ejecutar("INSERT INTO `productosdeproveedor`(`idProducto`, `idProveedor`) VALUES ".$valores);
                    $NuevoEstado = '7';
                }
            }
 
            if($this->idEstado == 9 && $NuevoEstado != 9){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(2, 2, $this->rif, 'Se ha registrado el proveedor #'.$this->rif);
            }

            return $BaseDeDatos->ejecutar("UPDATE `proveedores` SET "
            ."`tipoDeDocumento` = '".$DatosNuevos['tipoDeDocumento']."', "
            ."`nombre` = '".strtoupper($DatosNuevos['nombre'])."', "
            ."`idEstado` = '".$NuevoEstado."', "
            ."`ULRImagen` = ".$ImagenNueva
            ." WHERE `rif` = ".$this->rif);
        }

        
        
    }

    public function ValidarDatos($DatosAGuardar, $Estado){
        $ListaDeErrores = array();

        if($Estado!=9&&empty($DatosAGuardar['tipoDeDocumento'])) {
            $ListaDeErrores[] = "Tipo de documento está vacío¿";
        }
        if(empty($DatosAGuardar['rif'])) {
            $ListaDeErrores[] = "RIF  está vacío¿";
        }
        if($Estado!=9&&empty($DatosAGuardar['nombre'])){
            if($Estado!=9){
                $ListaDeErrores[] = "Nombre está vacío¿";
            }
        }

        if(!empty($DatosAGuardar['IndicarProductos'])){
            $BaseDeDatos = new conexion();
            $IdesDeProductos = explode('¿' ,$DatosAGuardar['ProductosSeleccionados']);
            foreach($IdesDeProductos as $ID){
                if(!is_numeric($ID)){
                    if(!empty($ID)){
                        $ListaDeErrores[] = "'".$ID."' no es un ID válido de un producto¿";
                        break;
                    }
                }else{
                    if(empty($BaseDeDatos->consultar("SELECT * FROM `productos` WHERE `id` = ".$ID))){
                        $ListaDeErrores[] = "El producto '".$ID."' no existe¿";
                    }
                }
            }            
        }

        return $ListaDeErrores;
    }

    public function CrearNuevo($DatosAGuardar, $Archivos,$Estado){
        $BaseDeDatos = new conexion();
        $ContactoFantasma = new contacto(0);
        
        //Valido los datos
        $ErroresDelProveedor = $this->ValidarDatos($DatosAGuardar, $Estado);
        $ErroresDelContacto = $ContactoFantasma->ValidarDatos($DatosAGuardar, $Estado);

        $ListaDeErrores = array_merge($ErroresDelProveedor, $ErroresDelContacto);
        

        //Si hay errores, los arrojo; si no los hay, procedo
        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Preparo los datos para el insert
            $DatosAGuardar['tipoDeDocumento'] = "'".$DatosAGuardar['tipoDeDocumento']."'";
            $DatosAGuardar['nombre'] = "'".strtoupper($DatosAGuardar['nombre'])."'";
            if($Estado != 9){
                //$Estado = ((empty($DatosAGuardar['ProductosSeleccionados']))?"8":"7");
                $Estado = '7';
            }
            

            //Si recibo una imagen la guardo; si no, establezco NULL//
            if(empty($Archivos['ULRImagen']['name'])){
                $ImagenAGuardar="NULL";
            }else{
                $tiempo = new DateTime();
                $NuevoNombreIMG=$tiempo->getTimestamp()."_".$Archivos['ULRImagen']['name'];
                $ImagenAGuardar="'".$NuevoNombreIMG."'";
                $ImagenRecibida=$Archivos['ULRImagen']['tmp_name'];
                move_uploaded_file($ImagenRecibida,"../../Imagenes/Proveedores/".$NuevoNombreIMG);
            }

            //creo el contacto
            $IdDelContacto = $ContactoFantasma->CrearNuevo($DatosAGuardar);

            //creo el proveedor
            $BaseDeDatos->ejecutar("INSERT INTO `proveedores`(`rif`, `tipoDeDocumento`, `nombre`, `idContacto`, `idEstado`, `ULRImagen`) VALUES ("
            .$DatosAGuardar['rif']." ,"
            .$DatosAGuardar['tipoDeDocumento']." ,"
            .$DatosAGuardar['nombre']." ,"
            .$IdDelContacto." ,"
            .$Estado." ,"
            .$ImagenAGuardar.")");

            //Dejo la huella en el historial
            if($Estado != 9){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 2, $DatosAGuardar['rif'], "Se ha registrado al proveedor #".$DatosAGuardar['rif']);
            }
            

            //Relaciono los productos
            if(!empty($DatosAGuardar['IndicarProductos']) && !empty($DatosAGuardar['ProductosSeleccionados'])){
                $IdesDeProductos = explode('¿', $DatosAGuardar['ProductosSeleccionados']);
                $valores = "";

                foreach($IdesDeProductos as $IDDeUnProductoSolito){
                    $valores = $valores.((empty($valores))?"(".$IDDeUnProductoSolito.", ".$DatosAGuardar['rif'].")":", (".$IDDeUnProductoSolito.", ".$DatosAGuardar['rif'].")");
                }
                $SQLDeProductosRelacionados = "INSERT INTO `productosdeproveedor`(`idProducto`, `idProveedor`) VALUES ".$valores;
                
                return $BaseDeDatos->ejecutar($SQLDeProductosRelacionados);
            }
        }
    }

    public function Eliminar(){
        $BaseDeDatos = new conexion();
        if(!empty($this->ULRImagen)){
            unlink('../../Imagenes/proveedores/'.$this->ULRImagen);
        } 
        $BaseDeDatos->ejecutar("DELETE FROM `proveedores` WHERE `rif` = ".$this->rif);    

    }
}

//////////CONTACTO////
class contacto{
    private $id;
    private $direccion;
    private $correo;
    private $telefono1;
    private $telefono2;

    function __construct($idACargar){
        $BaseDeDatos = new conexion();

        //Si recibo una id, cargo los datos en el objeto//
        if($idACargar!=0){
            $BaseDeDatos = new conexion();
            $datosDeContacto = $BaseDeDatos->consultar("SELECT * FROM `contactos` WHERE `id` = ".$idACargar);
            $datosDeContacto = $datosDeContacto[0];

            $this->id = $idACargar;
            $this->direccion = $datosDeContacto['direccion'];
            $this->correo = $datosDeContacto['correo'];
            $this->telefono1 = $datosDeContacto['telefono1'];
            $this->telefono2 = $datosDeContacto['telefono2'];
        }        
    }

    public function ValidarDatos($DatosAGuardar, $Estado){
        $ListaDeErrores = array();

        if(($Estado!=9 && $Estado!=12)&&empty($DatosAGuardar['direccion'])) {
            //$ListaDeErrores[] = "Dirección está vacío¿";
        }

        return $ListaDeErrores;
    }

    public function CrearNuevo($DatosAGuardar){
        $BaseDeDatos = new conexion();
        
        //Preparo los datos para el insert
        $DatosAGuardar['direccion'] = "'".$DatosAGuardar['direccion']."'";
        $DatosAGuardar['correo'] = "'".$DatosAGuardar['correo']."'";

        if(!empty($DatosAGuardar['ModoInter1'])){
            $Telefono1AGuardar = "'+".((empty($DatosAGuardar['telefono1']))?"":$DatosAGuardar['telefono1'])."'";
        }else{
            $Telefono1AGuardar = "'".((empty($DatosAGuardar['CodigoArea1']) && empty($DatosAGuardar['telefono1']))?"":$DatosAGuardar['CodigoArea1']."-".$DatosAGuardar['telefono1'])."'";
        }
        
        if(!empty($DatosAGuardar['ModoInter2'])){
            $Telefono2AGuardar = "'+".((empty($DatosAGuardar['telefono2']))?"":$DatosAGuardar['telefono2'])."'";
        }else{
            $Telefono2AGuardar = "'".((empty($DatosAGuardar['CodigoArea2']) && empty($DatosAGuardar['telefono2']))?"":$DatosAGuardar['CodigoArea2']."-".$DatosAGuardar['telefono2'])."'";
        }



        //Si hay errores, arrojo la lista de errores; si no hay, creo el objeto
        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            return $BaseDeDatos->ejecutar("INSERT INTO `contactos` (`direccion`, `telefono1`, `telefono2`, `correo`) VALUES ("
            .$DatosAGuardar['direccion'].", "
            .$Telefono1AGuardar.", "
            .$Telefono2AGuardar." ,"
            .$DatosAGuardar['correo'].")");

        }
    }

    public function TransformarFormato($DatosDeTelefonos){
        $Simbolo1 = ((empty($DatosDeTelefonos['ModoInter1']))?"-":"+");
        $Simbolo2 = ((empty($DatosDeTelefonos['ModoInter2']))?"-":"+");
        $CodigoDeArea1 = (($Simbolo1=="+")?"0000":$DatosDeTelefonos['CodigoArea1']);
        $CodigoDeArea2 = (($Simbolo2=="+")?"0000":$DatosDeTelefonos['CodigoArea2']);
        $NumeroDerecho1 = $DatosDeTelefonos['telefono1'];
        $NumeroDerecho2 = $DatosDeTelefonos['telefono2'];
        $NumeroCompleto1 = (($Simbolo1=="+")?"+".$DatosDeTelefonos['telefono1']:$DatosDeTelefonos['CodigoArea1']."-".$DatosDeTelefonos['telefono1']);
        $NumeroCompleto2 = (($Simbolo2=="+")?"+".$DatosDeTelefonos['telefono2']:$DatosDeTelefonos['CodigoArea2']."-".$DatosDeTelefonos['telefono2']);


        if($Simbolo1=="+"){
            $NumeroCompleto1 = "'+".((empty($DatosDeTelefonos['telefono1']))?"":$DatosDeTelefonos['telefono1'])."'";
        }else{
            $NumeroCompleto1 = "'".((empty($DatosDeTelefonos['CodigoArea1']) && empty($DatosDeTelefonos['telefono1']))?"":$DatosDeTelefonos['CodigoArea1']."-".$DatosDeTelefonos['telefono1'])."'";
        }
        
        if($Simbolo2=="+"){
            $NumeroCompleto2 = "'+".((empty($DatosDeTelefonos['telefono2']))?"":$DatosDeTelefonos['telefono2'])."'";
        }else{
            $NumeroCompleto2 = "'".((empty($DatosDeTelefonos['CodigoArea2']) && empty($DatosDeTelefonos['telefono2']))?"":$DatosDeTelefonos['CodigoArea2']."-".$DatosDeTelefonos['telefono2'])."'";
        }

        return array(
            "simbolo1" => $Simbolo1,
            "simbolo2" => $Simbolo2,
            "codigoDeArea1" => $CodigoDeArea1,
            "codigoDeArea2" => $CodigoDeArea2,
            "numeroDerecho1" => $NumeroDerecho1,
            "numeroDerecho2" => $NumeroDerecho2,
            "numeroCompleto1" => $NumeroCompleto1,
            "numeroCompleto2" => $NumeroCompleto2
        );
    }

    public function ActualizarDatos($DatosNuevos){
        $BaseDeDatos = new conexion();

        //Guardo cambios en el hisotorial
        if($DatosNuevos['direccion']!=$this->direccion){
            
        }

        //Preparo los datos para el insert
        $TelefonosListos = $this->TransformarFormato($DatosNuevos);

        return $BaseDeDatos->ejecutar("UPDATE `contactos` SET "
        ."`direccion` = '".$DatosNuevos['direccion']."', "
        ."`correo` = '".$DatosNuevos['correo']."', "
        ."`telefono1` = ".$TelefonosListos['numeroCompleto1'].", "
        ."`telefono2` = ".$TelefonosListos['numeroCompleto2']." "
        ."WHERE `id` = ".$this->id);
        
    }
    

    public function ObtenerDatos(){
        $Simbolo1 = ((str_contains($this->telefono1, '+'))?"+":"-");
        $Simbolo2 = ((str_contains($this->telefono2, '+'))?"+":"-");
        $CodigoDeArea1 = (($Simbolo1=="+")?"0000":substr($this->telefono1,0,4));
        $CodigoDeArea2 = (($Simbolo2=="+")?"0000":substr($this->telefono2,0,4));

        if(str_contains($this->telefono1, '-')||str_contains($this->telefono1, '+')){
            if(str_contains($this->telefono1, '-')){
                $pedazos = explode("-", $this->telefono1);
                $NumeroDerecho1 = $pedazos[1];
            }else{
                $pedazos = explode("+", $this->telefono1);
                $CodigoDeArea1 = "0000";
                $NumeroDerecho1 = $pedazos[1];
            }
        }else{
            $NumeroDerecho1 = $this->telefono1;
        }
        
        if(str_contains($this->telefono2, '-')||str_contains($this->telefono2, '+')){
            if(str_contains($this->telefono2, '-')){
                $pedazos = explode("-", $this->telefono2);
                $NumeroDerecho2 = $pedazos[1];
            }else{
                $pedazos = explode("+", $this->telefono2);
                $CodigoDeArea2 = "0000";
                $NumeroDerecho2 = $pedazos[1];
            }
        }else{
            $NumeroDerecho2 = $this->telefono2;
        }
        

        return array(
            "direccion" => $this->direccion,
            "correo" => $this->correo,
            "simbolo1" => $Simbolo1,
            "simbolo2" => $Simbolo2,
            "codigoDeArea1" => $CodigoDeArea1,
            "codigoDeArea2" => $CodigoDeArea2,
            "numeroDerecho1" => $NumeroDerecho1,
            "numeroDerecho2" => $NumeroDerecho2,
            "numeroCompleto1" => $this->telefono1,
            "numeroCompleto2" => $this->telefono2
        );
    }

}

//////////PRODUCTO//////////////
class producto{
    private $id;    
    private $nombre;
    private $idCategoria;
    private $precio;
    private $idUnidadDeMedida;
    private $descripcion;
    private $nivelDeAlerta;
    private $ULRImagen;
    private $idEstado;

    public function obtenerDisposicion(){
        $BaseDeDatos = new conexion();
        $sql = array();
        $result = array();

        return $BaseDeDatos->consultar("SELECT `almacenes`.*, `inventario`.`existencia` FROM `inventario` INNER JOIN `almacenes` ON `inventario`.`idAlmacen` = `almacenes`.`id` WHERE `idProducto` = $this->id");


        return array(
            'sql' => $sql,
            'result' => $result,
        );
    }

    public function ListarProductos($Filtros){
        $FiltroDescripion = ((empty($Filtros['descripcion']))?'':'(`productos`.`id` LIKE "%'.$Filtros['descripcion'].'%" OR `productos`.`nombre` LIKE "%'.$Filtros['descripcion'].'%")');
        //$FiltroCategoria = ((empty($Filtros['categoria']))?'':(($Filtros['categoria'] == 2)?"(`idCategoria` = 2 OR `idCategoria` = 3)":"(`idCategoria` = ".$Filtros['categoria'].")"));
        $FiltroCategoria = ((empty($Filtros['categoria']))?'':"(`idCategoria` = ".$Filtros['categoria'].")");

        $FiltrosDeConsulta = $FiltroDescripion;
        $FiltrosDeConsulta = ((!empty($FiltroDescripion) && !empty($FiltroCategoria))?($FiltroCategoria.' AND '.$FiltroDescripion):($FiltroCategoria.$FiltroDescripion));
        $ExcepcionDeEstado = '(`idEstado` != 4 AND `idEstado` != 5)';

        $FiltrosDeConsulta = ((empty($FiltrosDeConsulta))?$ExcepcionDeEstado:$FiltrosDeConsulta.' AND '.$ExcepcionDeEstado);


        if(!empty($FiltrosDeConsulta)){
            $FiltrosDeConsulta = " WHERE (".$FiltrosDeConsulta.")";
        }
        $SQLOMPLETO = "SELECT 
        `productos`.`id`,
        `productos`.`nombre`,
        `productos`.`idCategoria`,
        `productos`.`precio`,
        `productos`.`descripcion`,
        `productos`.`ULRImagen`,
        `productos`.`idEstado`,
        `unidadesdemedida`.`simbolo`,
        `unidadesdemedida`.`nombre` AS 'nombredeunidad'
        FROM `productos` INNER JOIN `unidadesdemedida` ON `productos`.`idUnidadDeMedida` = `unidadesdemedida`.`id` ".$FiltrosDeConsulta." ORDER BY `nombre`";

        

        $ArrayARetornar = array();

        $BaseDeDatos = new conexion();
        
        

        foreach($BaseDeDatos->consultar($SQLOMPLETO) as $RowProducto){
            $ProductoExistente = new producto($RowProducto['id']);
            $DatosDelProducto = $ProductoExistente->ObtenerDatos();

            $ConsultaDeExistencia = $BaseDeDatos->consultar("SELECT * FROM `inventario` WHERE `idProducto` = ".$DatosDelProducto['id']);
            $ExistenciaAcumulada = 0;
            foreach($ConsultaDeExistencia as $ProductoEnUnAlmacen){
                $ExistenciaAcumulada = $ExistenciaAcumulada + $ProductoEnUnAlmacen['existencia'];
            }

            $ArrayDeProveedores = '';

            $ListaDeProveedores = $BaseDeDatos->consultar("SELECT * FROM `productosdeproveedor` 
            INNER JOIN `proveedores` ON `productosdeproveedor`.`idProveedor` = `proveedores`.`rif` 
            WHERE ( `idProducto` = ".$DatosDelProducto['id']." AND `proveedores`.`idEstado` = 7)");

            if(!empty($ListaDeProveedores)){
                foreach($ListaDeProveedores as $RowProveedor){
                    $ArrayDeProveedores = $ArrayDeProveedores.((empty($ArrayDeProveedores))?$RowProveedor['idProveedor']:'x'.$RowProveedor['idProveedor']);
                }
            }
            
            

            $ArrayARetornar [] = array(
                'id' => $DatosDelProducto['id'],
                'nombre' => $DatosDelProducto['nombre'],
                'idCategoria' => $DatosDelProducto['idCategoria'],
                'precio' => $DatosDelProducto['precio'],
                'descripcion' => $DatosDelProducto['descripcion'],
                'ULRImagen' => $DatosDelProducto['ULRImagen'],
                'idEstado' => $DatosDelProducto['idEstado'],
                'simbolo' => $DatosDelProducto['simboloConEstiloUM'],
                'nombredeunidad' => $DatosDelProducto['nombreUM'],
                'existencia' => $ExistenciaAcumulada,
                'nivelDeAlerta' => $DatosDelProducto['nivelDeAlerta'],
                'listaDeProveedores' => $ArrayDeProveedores
            );
        }


        return $ArrayARetornar;
    }

    public function ObtenerDatos(){
        $BaseDeDatos = new conexion();
        if(!empty($this->id)){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `categorias` WHERE `id` = ".$this->idCategoria);
            $DatosDeLaCategoria = $DatosDeLaConsulta[0];

            $UnidadDeMedida = new unidadDeMedida($this->idUnidadDeMedida);
            $DatosDeUnidadDeMedida = $UnidadDeMedida->ObtenerDatos();

            $NumeroDeProveedores = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `productosdeproveedor` WHERE `idProducto` = ".$this->id);
            $Proveedores = "";
            if($NumeroDeProveedores[0][0] > 0){
                $ProveedoresDisponibles = $BaseDeDatos->consultar("SELECT * FROM `productosdeproveedor` WHERE `idProducto` = ".$this->id);
                foreach($ProveedoresDisponibles as $RowProveedorDisponible){
                    $Proveedores = $Proveedores.((empty($Proveedores))?$RowProveedorDisponible['idProveedor']:"¿".$RowProveedorDisponible['idProveedor']);
                }
            }
        }
        
        return array(
            "id" => $this->id,
            "nombre" => $this->nombre,
            "idCategoria" => $this->idCategoria,
            "categoria" => $DatosDeLaCategoria['nombre'],
            "precio" => $this->precio,
            "idUnidadDeMedida" => $this->idUnidadDeMedida,
            "nombreUM" => $DatosDeUnidadDeMedida['nombre'],
            "simboloUM" => $DatosDeUnidadDeMedida['simbolo'],
            "simboloConEstiloUM" => $DatosDeUnidadDeMedida['simboloConEstilo'],
            "descripcion" => $this->descripcion,
            "nivelDeAlerta" => $this->nivelDeAlerta,
            "ULRImagen" => $this->ULRImagen,
            "idEstado" => $this->idEstado,
            "CheckboxMostrarLista" => (($NumeroDeProveedores[0][0] > 0 )?"checked":""),
            "ItemsSeleccionadosDeLista" => $Proveedores
        );
    }

    public function ObtenerProveedoresDisponibles(){
        $BaseDeDatos = new conexion();
        return $BaseDeDatos->consultar(
        'SELECT * FROM `productosdeproveedor` 
        INNER JOIN `proveedores` ON `productosdeproveedor`.`idProveedor` = `proveedores`.`rif` 
        INNER JOIN `contactos` ON `proveedores`.`idContacto` = `contactos`.`id`
        WHERE ((SELECT `proveedores`.`idEstado` FROM `proveedores` WHERE `proveedores`.`rif` = `idProveedor`) = 7 AND (`idProducto` = '.$this->id.'));');
    }

    function __construct($idACargar){
        $BaseDeDatos = new conexion();

        //Si recibo una id, cargo los datos en el objeto//
        if($idACargar!=0){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT 
            `productos`.`nombre`, 
            `productos`.`idCategoria`, 
            `productos`.`precio`, 
            `productos`.`idUnidadDeMedida`, 
            `productos`.`descripcion`, 
            `productos`.`nivelDeAlerta`, 
            `productos`.`ULRImagen`, 
            `productos`.`idEstado`
            FROM `productos` 
            WHERE `id` = ".$idACargar);
            
            if(!empty($DatosDeLaConsulta)){
                $datosDelObjeto = $DatosDeLaConsulta[0];
            
                $this->id = $idACargar;
                $this->nombre = $datosDelObjeto['nombre'];
                $this->idCategoria = $datosDelObjeto['idCategoria'];
                $this->precio = $datosDelObjeto['precio'];
                $this->idUnidadDeMedida = $datosDelObjeto['idUnidadDeMedida'];
                $this->descripcion = $datosDelObjeto['descripcion'];
                $this->nivelDeAlerta = $datosDelObjeto['nivelDeAlerta'];
                $this->ULRImagen = $datosDelObjeto['ULRImagen'];
                $this->idEstado = $datosDelObjeto['idEstado'];
            }
        }

	}

    public function ActualizarDatos($DatosNuevos, $Archivos, $NuevoEstado){
        $BaseDeDatos = new conexion();
        $ListaDeErrores = $this->ValidarDatos($DatosNuevos, $NuevoEstado);

        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //Preparo los datos para el update
            $DatosNuevos['nombre'] = "'".$DatosNuevos['nombre']."'";
            $DatosNuevos['descripcion'] = "'".$DatosNuevos['descripcion']."'";
            $DatosNuevos['nivelDeAlerta'] = (($DatosNuevos['categoria'] > 2)?'NULL':$DatosNuevos['nivelDeAlerta']);
            $DatosNuevos['idUnidadDeMedida'] = (($DatosNuevos['categoria'] == 3)?'1':(($DatosNuevos['categoria'] == 1)?$DatosNuevos['idUnidadDeMedida']:'2'));
            $NuevoEstado = (($NuevoEstado == 3 && ($DatosNuevos['categoria'] == 3 || $DatosNuevos['categoria'] == 4))?1: $NuevoEstado);
            
            //Si recibo una imagen, borro la vieja y cargo la nueva//
            $ImagenNueva = "";
            if(empty($Archivos['ULRImagen']['name'])){
                if(empty($this->ULRImagen)){
                    $ImagenNueva = "NULL";
                }else{
                    $ImagenNueva = "'".$this->ULRImagen."'";
                }
            }else{
                if(!empty($this->ULRImagen)){
                    unlink('../../Imagenes/Productos/'.$this->ULRImagen);
                }
                $tiempo = new DateTime();
                $NuevoNombreIMG = $tiempo->getTimestamp()."_".$Archivos['ULRImagen']['name'];
                $ImagenNueva = "'".$NuevoNombreIMG."'";
                $ImagenRecibida = $Archivos['ULRImagen']['tmp_name'];
                move_uploaded_file($ImagenRecibida,"../../Imagenes/Productos/".$NuevoNombreIMG);
            }


            //Ejecuto el update segun la categoria
            if($DatosNuevos['categoria']==1){
                $BaseDeDatos->ejecutar("UPDATE `productos` SET 
                `nombre`=".$DatosNuevos['nombre'].",
                `idCategoria`=".$DatosNuevos['categoria'].",
                `precio`=".$DatosNuevos['precio'].",
                `idUnidadDeMedida`=".$DatosNuevos['idUnidadDeMedida'].",
                `descripcion`=".$DatosNuevos['descripcion'].",
                `nivelDeAlerta`=".$DatosNuevos['nivelDeAlerta'].",
                `ULRImagen`=".$ImagenNueva.",
                `idEstado`=".$NuevoEstado." 
                WHERE `id` = ".$this->id);
            }else{
                $BaseDeDatos->ejecutar("UPDATE `productos` SET 
                `nombre`=".$DatosNuevos['nombre'].",
                `idCategoria`=".$DatosNuevos['categoria'].",
                `precio`=".$DatosNuevos['precio'].",
                `idUnidadDeMedida`=NULL,
                `descripcion`=".$DatosNuevos['descripcion'].",
                `nivelDeAlerta`= NULL,
                `ULRImagen`=".$ImagenNueva.",
                `idEstado`=".$NuevoEstado." 
                WHERE `id` = ".$this->id);
            }

            
            //Creo el registro en historial
            if($this->idEstado == 5 &&  $NuevoEstado != 5){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 1, $this->id, 'Se ha registrado el producto #'.$this->id);
            }
        }
    }

    public function CrearNuevo($DatosAGuardar, $Archivos, $Estado){
        $BaseDeDatos = new conexion();

        //Valido los datos
        $ListaDeErrores = $this->ValidarDatos($DatosAGuardar, $Estado);

        
        if(!empty($ListaDeErrores)){
            throw new Exception(implode($ListaDeErrores));
        }else{
            //preparo los datos para el insert number_format($number, $decimals, '.', "");
            $DatosAGuardar['nivelDeAlerta'] = (($DatosAGuardar['categoria'] > 2)?'NULL':$DatosAGuardar['nivelDeAlerta']);
            $DatosAGuardar['idUnidadDeMedida'] = (($DatosAGuardar['categoria'] == 3)?'1':(($DatosAGuardar['categoria'] == 1)?$DatosAGuardar['idUnidadDeMedida']:'2'));
            $Estado = (($Estado == 3 && ($DatosAGuardar['categoria'] == 3 || $DatosAGuardar['categoria'] == 4))?1: $Estado);

            $datosLimpios = array(
                'nombre' => "'".htmlentities($DatosAGuardar['nombre'])."'",
                'descripcion' => "'".htmlentities($DatosAGuardar['descripcion'])."'",
                'nivelDeAlerta' => (($DatosAGuardar['categoria'] > 2)?'NULL':$DatosAGuardar['nivelDeAlerta']),
                'idUnidadDeMedida' => (($DatosAGuardar['categoria'] == 3)?'1':(($DatosAGuardar['categoria'] == 1)?$DatosAGuardar['idUnidadDeMedida']:'2'))
            );
            
            //Si recibo una imagen la guardo; si no, establezco NULL//
            if(empty($Archivos['ULRImagen']['name'])){
                $ImagenAGuardar="NULL";
            }else{
                $tiempo = new DateTime();
                $NuevoNombreIMG=$tiempo->getTimestamp()."_".$Archivos['ULRImagen']['name'];
                $ImagenAGuardar="'".$NuevoNombreIMG."'";
                $ImagenRecibida=$Archivos['ULRImagen']['tmp_name'];
                move_uploaded_file($ImagenRecibida,"../../Imagenes/Productos/".$NuevoNombreIMG);
            }

            //Calculo la próxima id
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT `id` FROM `productos` ORDER BY `id`");
            $ConsultaDeIDMaximo = $BaseDeDatos->consultar("SELECT `id` FROM `productos` ORDER BY `id` DESC LIMIT 0,1");
            $ConsultaCantidadDeProductos = $BaseDeDatos->consultar("SELECT COUNT(*) FROM `productos`");            
        
            if($ConsultaCantidadDeProductos[0]['COUNT(*)'] == 0){
                //Si no hay productos, la siguiente id es 1
                $ProximaID="1";
            }else{
                if($ConsultaDeIDMaximo[0]['id'] > $ConsultaCantidadDeProductos[0]['COUNT(*)']){
                    //si  la id maxima es mayor a la cantidad de productos, buscar que id es la siguiente
                    $IdEsperada = 1;
                    $log = "";

                    foreach($DatosDeLaConsulta as $rowProductos){
                        if($rowProductos['id'] != $IdEsperada){
                            $ProximaID = $IdEsperada;
                            break;
                        }
                        $IdEsperada++;
                    }
                }else{
                    //si la id maxima es igual que la cantidad de productos, la siguiente id es la maxima + 1
                    $ProximaID = $ConsultaDeIDMaximo[0]['id'] + 1;
                }
                
            }

            

            //Creo el producto
            $BaseDeDatos->ejecutar("INSERT INTO `productos`(`id`, `nombre`, `idCategoria`, `precio`, `idUnidadDeMedida`, `descripcion`, `nivelDeAlerta`, `ULRImagen`, `idEstado`) VALUES (
                ".$ProximaID.",
                ".$datosLimpios['nombre'].",
                ".$DatosAGuardar['categoria'].",
                ".$DatosAGuardar['precio'].",
                ".$datosLimpios['idUnidadDeMedida'].",
                ".$datosLimpios['descripcion'].",
                ".$datosLimpios['nivelDeAlerta'].",
                ".$ImagenAGuardar.",
                ".$Estado.")"
            );


            //Creo el registro en historial
            if($Estado != 5){
                $Auditoria = new historial();
                $Auditoria->CrearNuevoRegistro(1, 1, $ProximaID, 'Se ha registrado el producto #'.$ProximaID);
            }

            
            //Creo la relacion producto-proveedor
            if(!empty($DatosAGuardar['CheckboxMostrarLista']) && !empty($DatosAGuardar['ItemsSeleccionadosDeLista'])){
                $RifDeLosProveedores = explode('¿', $DatosAGuardar['ItemsSeleccionadosDeLista']);
                $valores = "";

                foreach($RifDeLosProveedores as $RifDeUnProveedorSolito){
                    $valores = $valores.((empty($valores))?"(".$RifDeUnProveedorSolito.", ".$ProximaID.")":", (".$RifDeUnProveedorSolito.", ".$ProximaID.")");
                }
                $SQLDeProveedoresRelacionados = "INSERT INTO `productosdeproveedor`(`idProveedor`, `idProducto`) VALUES ".$valores;

                return $BaseDeDatos->ejecutar($SQLDeProveedoresRelacionados);
            }           
        }     
    }

    public function Eliminar(){
        $BaseDeDatos = new conexion();
        if(!empty($this->ULRImagen)){
            unlink('../Imagenes/productos/'.$this->ULRImagen);
        } 
        $BaseDeDatos->ejecutar("DELETE FROM `productos` WHERE `id` = ".$this->id);

    }

    public function ValidarDatos($DatosAGuardar, $Estado){
        $ListaDeErrores = array();
        
        if(empty($DatosAGuardar['nombre']) && $Estado!=5){
            $ListaDeErrores[] = "Nombre está vacío¿";
        }

        if(empty($DatosAGuardar['precio'])){
            $ListaDeErrores[] = "Precio está vacío¿";
        }

        if(empty($DatosAGuardar['categoria'])){
            $ListaDeErrores[] = "Categoría está vacío¿";
        }

        if($DatosAGuardar['categoria']==1 && empty($DatosAGuardar['idUnidadDeMedida'])){
            $ListaDeErrores[] = "Unidad de medida está vacío¿";
        }

        if($DatosAGuardar['categoria'] < 3 && empty($DatosAGuardar['nivelDeAlerta']) && $Estado!=5){
            $ListaDeErrores[] = "Nivel de alerta está vacío¿";
        }

        if(!empty($DatosAGuardar['CheckboxMostrarLista'])){
            $BaseDeDatos = new conexion();
            $RifDeProveedores = explode('¿' ,$DatosAGuardar['ItemsSeleccionadosDeLista']);

            foreach($RifDeProveedores as $RIF){
                if(!is_numeric($RIF)){
                    if(!empty($RIF)){
                        $ListaDeErrores[] = "'".$RIF."' no es un ID válido de un proveedor";
                        break;
                    }
                }else{
                    if(empty($BaseDeDatos->consultar("SELECT * FROM `proveedores` WHERE `rif` = ".$RIF))){
                        $ListaDeErrores[] = "El proveedor '".$RIF."' no existe¿";
                    }
                }
            }

            
        }

        

        return $ListaDeErrores;
    }
}

//////////HISTORIAL////////////////
class historial{
    public $Huella_Creado = 1;
    public $Huella_Modificado = 2;
    public $Huella_Eliminado = 3;


    function BuscarRegistro($TipoDeHuella, $TipoDeEntidad, $IDDeEntidad){
        $BaseDeDatos = new conexion();
        

        return $BaseDeDatos->consultar("SELECT * FROM `historial` WHERE (`idTipoDeHuella` = ".$TipoDeHuella." AND `idTipoDeEntidad` = ".$TipoDeEntidad." AND `idDeEntidad` = ".$IDDeEntidad.")");
    }

    function CrearNuevoRegistro($TipoDeHuella, $TipoDeEntidad, $IDDeEntidad, $CambioRealizado){
        $BaseDeDatos = new conexion();

        
        $UsuarioLogeado = unserialize($_SESSION["UsuarioLogeado"]);
        $DatosDelUsuarioLogeado = $UsuarioLogeado->ObtenerDatos();
        date_default_timezone_set('America/Caracas');

        //Preparo la ID de entidad
        if($TipoDeEntidad == 1){
            $IDDeEntidad = sprintf("%07d", $IDDeEntidad);
        }
        
        //Preparo el Cambio
        if(empty($CambioRealizado)){
            $CambioRealizado = "NULL";
        }else{
            $CambioRealizado = "'".$CambioRealizado."'";
        }

        $BaseDeDatos->ejecutar
        ("INSERT INTO `historial`(`idTipoDeHuella`, `idTipoDeEntidad`, `idDeEntidad`, `cambioRealizado`, `fechaCreacion`, `nombreDeUsuario`) 
        VALUES (".$TipoDeHuella.", ".$TipoDeEntidad.", '".$IDDeEntidad."', ".$CambioRealizado.", '".date('Y-m-d h:i:s')."', '".$DatosDelUsuarioLogeado['nombreDeusuario']."')");    

    }

    function ObtenerResultadosDeBusqueda($searchParams){
        $BaseDeDatos = new conexion();
        $sql = array();
        $result = array();
        $preparedParams = array();

        $preparedParams['descripcion'] = $searchParams['descripcion'];
        $preparedParams['tipo'] = ($searchParams['tipo']==1||$searchParams['tipo']==2||$searchParams['tipo']==3? $searchParams['tipo']:'0');
        $preparedParams['entidad'] = ($searchParams['entidad']!='0' && is_numeric($searchParams['entidad'])? floor($searchParams['entidad']):'0');
        $preparedParams['mes'] = ($searchParams['mes']!=0 && $searchParams['mes']>0 && $searchParams['mes']<13? floor($searchParams['mes']):'0');
        $preparedParams['anio'] = ($searchParams['anio']!=0&&is_numeric($searchParams['anio'])? floor($searchParams['anio']):'0');
        $preparedParams['pagina'] = (is_numeric($searchParams['pagina'])&&$searchParams['pagina']>1? floor($searchParams['pagina']):'1');

        $sql['descripcion'] = '('.(empty($preparedParams['descripcion'])? '1=1':"`cambioRealizado` LIKE '%".$preparedParams['descripcion']."%'").')';
        $sql['tipo'] = '('.($preparedParams['tipo']>0? '`idTipoDeHuella`='.$preparedParams['tipo']:'2=2').')';
        $sql['entidad'] = '('.($preparedParams['entidad']>'0'? '`idTipoDeEntidad`='.$preparedParams['entidad']:'3=3').')';
        $sql['mes'] = '('.($preparedParams['mes']>0? 'MONTH(`fechaCreacion`)='.$preparedParams['mes']:'4=4').')';
        $sql['anio'] = '('.($preparedParams['anio']>0? 'YEAR(`fechaCreacion`)='.$preparedParams['anio']:'5=5').')';
        $sql['pagina'] = 'ORDER BY `id` DESC LIMIT '.(($preparedParams['pagina'] - 1) * 15).',15' ;

        $where_sql = $sql['descripcion'].' AND '.$sql['tipo'].' AND '.$sql['entidad'].' AND '.$sql['mes'].' AND '.$sql['anio'];

        $sql['SELECT_historial'] = "SELECT * FROM `historial` WHERE ($where_sql) ".$sql['pagina'];
        $sql['COUNT_historial'] = "SELECT COUNT(*) AS 'count' FROM `historial` WHERE ($where_sql) ";

        //return $sql['SELECT_historial'];

        $rows = $BaseDeDatos->consultar($sql['SELECT_historial']);
        $count = $BaseDeDatos->consultar($sql['COUNT_historial']);
        $count = $count[0]['count'];
        return array(
            'rows'=> $rows,
            'count'=> $count
        );

        return array(
            'preparedParams' => $preparedParams,
            'sql' => $sql,
            'result' => $result
        );
    }
}

//////////UNIDAD DE MEDIDA//////////////
class unidadDeMedida{
    private $id;
    private $nombre;
    private $simbolo;


    public function __construct($id){
        $BaseDeDatos = new conexion();

        if($id!=0){
            $DatosDeLaConsulta = $BaseDeDatos->consultar("SELECT * FROM `unidadesdemedida` WHERE`id` = ".$id);
            $DatosDeLaUnidad = $DatosDeLaConsulta[0];
            $this->id = $DatosDeLaUnidad['id'];
            $this->nombre = $DatosDeLaUnidad['nombre'];
            $this->simbolo = $DatosDeLaUnidad['simbolo'];
        }
    }

    public function ObtenerDatos(){




        return array(
            "id" => $this->id,
            "nombre" => $this->nombre,
            "simbolo" => $this->simbolo,
            "simboloConEstilo" => ((strpos($this->nombre, "cuadrado"))?$this->simbolo."<sup>2</sup>":$this->simbolo)
        );
    }

    
}

//////////CONTROLADOR DE TIEMPO//////////////
class AsistenteDeTiempo{
    public $dia_num = "";
    public $mes_num = "";
    public $anio_num = "";
    public $diaDeLaSemana_num = "";
    public $seg_num = "";
    public $min_num = "";
    public $hora_num = "";

    public $diaDeLaSemana_text = "";
    public $mes_text = "";

    public function EstablecerFecha($Fecha, $FormatoDeEntrada){
        if($FormatoDeEntrada == 'BaseDeDatos'){
            $pedazos = explode('-', $Fecha);
            $dia_num = $pedazos[2];
            $mes_num = $pedazos[1];
            $anio_num = $pedazos[0];
        }

        if($FormatoDeEntrada == 'BaseDeDatosConTiempo'){
            $pedazosgrandes = explode(' ', $Fecha);
            $pedazos = explode('-', $pedazosgrandes[0]);
            $dia_num = $pedazos[2];
            $mes_num = $pedazos[1];
            $anio_num = $pedazos[0];
            $pedazos2 = explode(':', $pedazosgrandes[1]);
            $seg_num = $pedazos2[2];
            $min_num = $pedazos2[1];
            $hora_num = $pedazos2[0];
        }
    }
    
    public function ConvertirFormato($FechaAConvertir, $FormatoDeEntrada, $FormatoDeSalida){
        $Respuesta = "";
        $DiaRecibido = "x";
        $MesRecibido = "x";
        $AnioRecibido = "x";
        $HoraRecibido = "x";
        $MinRecibido = "x";
        $SegRecibido = "x";

        

        if($FormatoDeEntrada == 'BaseDeDatos'){
            $pedazos = explode('-', $FechaAConvertir);
            $DiaRecibido = $pedazos[2];
            $MesRecibido = $pedazos[1];
            $AnioRecibido = $pedazos[0];
        }

        if($FormatoDeEntrada == 'BaseDeDatosConTiempo'){
            $pedazosgrandes = explode(' ', $FechaAConvertir);
            $pedazos = explode('-', $pedazosgrandes[0]);
            $DiaRecibido = $pedazos[2];
            $MesRecibido = $pedazos[1];
            $AnioRecibido = $pedazos[0];
            $pedazos2 = explode(':', $pedazosgrandes[1]);
            $SegRecibido = $pedazos2[2];
            $MinRecibido = $pedazos2[1];
            $HoraRecibido = $pedazos2[0];
        }


        //Respuestas
        if($FormatoDeSalida == 'Usuario'){
            $Respuesta = $DiaRecibido.' de '.$this->ConvertirMes_NumAText($MesRecibido).' del '.$AnioRecibido;
        }
        if($FormatoDeSalida == 'UsuarioConTiempo'){
            $Respuesta = $DiaRecibido.' de '.$this->ConvertirMes_NumAText($MesRecibido).' del '.$AnioRecibido.' a las '.$HoraRecibido.':'.$MinRecibido;
        }
        if($FormatoDeSalida == 'MaracayXD'){
            $Respuesta = $DiaRecibido.'/'.$MesRecibido.'/'.$AnioRecibido;
        }

        return $Respuesta;
    }

    public function __construct(){
        $this->dia_num = date('j');
        $this->mes_num = date('n');
        $this->anio_num = date('Y');
        $this->diaDeLaSemana_num = date('N');
        $this->diaDeLaSemana_text = $this->ConvertirDia_NumAText($this->diaDeLaSemana_num);
        $this->mes_text = $this->ConvertirMes_NumAText($this->mes_num);

        
        date_default_timezone_set('America/Caracas');
    }

    public function FechaActual_USER(){
        return $this->diaDeLaSemana_text.", ".$this->dia_num." de ".$this->mes_text." del ".$this->anio_num;
    }

    public function ConvertirDia_NumAText($NumeroDelDia){
        switch($NumeroDelDia){
            case 1:
                return "Lunes";
                break;
            case 2:
                return "Martes";
                break;
            case 3:
                return "Miercoles";
                break;
            case 4:
                return "Jueves";
                break;
            case 5:
                return "Viernes";
                break;
            case 6:
                return "Sabado";
                break;
            case 7:
                return "Domingo";
                break;
            default:
                return "Desconocido";
                break;
        }
    }

    public function ConvertirMes_NumAText($NumeroDelMes){
        switch($NumeroDelMes){
            case 1:
                return "Enero";
                break;
            case 2:
                return "Febrero";
                break;
            case 3:
                return "Marzo";
                break;
            case 4:
                return "Abril";
                break;
            case 5:
                return "Mayo";
                break;
            case 6:
                return "Junio";
                break;
            case 7:
                return "Julio";
                break;
            case 8:
                return "Agosto";
                break;
            case 9:
                return "Septiembre";
                break;
            case 10:
                return "Octubre";
                break;
            case 11:
                return "Noviembre";
                break;
            case 12:
                return "Diciembre";
                break;
            default:
                return "Desconocido";
        }
    }
}



class budget {
    
    function __construct($id){
        if(!is_numeric($id) || $id<0){
            throw new Exception("El ID $id no es válido");
        }else{
            $this->db = new conexion();
            $this->audit = new historial();

            $search = $this->db->consultar("SELECT * FROM `cotizaciones` WHERE `id` = $id");
            if(empty($search)){
                throw new Exception("No se encontró #$id en la base de datos");
            }else{
                $data = $search[0];
            }

            $this->id = $data['id'];
            $this->name = $data['nombre'];
            $this->clientCedula = $data['cedulaCliente'];
            $this->expireDate = $data['fechaExpiracion'];
            $this->idStatus = $data['idEstado'];
            $this->pUtilidades = $data['pUtilidades'];
            $this->pIVA = $data['pIVA'];
            $this->pCASalario = $data['pCASalario'];
            $this->idOutputSetting = $data['idAjusteDeSalida'];
        }
    }


    public function setExpired(){
        $this->db->ejecutar("UPDATE `cotizaciones` SET `idEstado` = 34 WHERE `id` = $this->id");
    }

    public function updateData($givenData){
        $this->validateData($givenData);
        $clean_name = "'".str_replace("'", '', trim($givenData['name']))."'";
        $clean_idClient = (empty($givenData['idClient'])? 'NULL':"'".$givenData['idClient']."'");
        $clean_expireDate = (empty($givenData['expireDate'])? 'NULL':"'".str_replace("'", '', trim($givenData['expireDate']))."'");
        $clean_percentCAS = $givenData['percentCAS'];
        $clean_percentUti = $givenData['percentUti'];
        $clean_percentIVA = $givenData['percentIVA'];


        $this->db->ejecutar("UPDATE `cotizaciones` SET 
        `nombre`= $clean_name,
        `cedulaCliente`= $clean_idClient,
        `fechaExpiracion`= $clean_expireDate,
        `pUtilidades`= $clean_percentUti,
        `pIVA`= $clean_percentIVA,
        `pCASalario`= $clean_percentCAS WHERE `id` = $this->id");

        $this->db->ejecutar("DELETE FROM `cuerpocotizacion` WHERE `idCotizacion` =  $this->id");



        if(!empty($givenData['materialProducts'])){
            $products = explode('¿', $givenData['materialProducts']);
            $values = '';

            foreach($products as $prodXquan){
                $pieces = explode('x', $prodXquan);
                $prod = new product($pieces[0]);
                $totalPrice = $prod->getPrice() * $pieces[1];
                $values.= (empty($values)? '':', ')."($this->id, ".$pieces[0].", ".$pieces[1].", ".$prod->getPrice().", $totalPrice)";
            }

            $this->db->ejecutar("INSERT INTO `cuerpocotizacion`(`idCotizacion`, `idProducto`, `cantidad`, `precioUnitario`, `precioMultiplicado`) VALUES $values");
        }

        if(!empty($givenData['equipProducts'])){
            $products = explode('¿', $givenData['equipProducts']);
            $values = '';

            foreach($products as $prodXquan){
                $pieces = explode('x', $prodXquan);
                $prod = new product($pieces[0]);
                $totalPrice = $prod->getPrice() * $pieces[1] * $prod->getDefaultSpoilage();
                $values.= (empty($values)? '':', ')."($this->id, ".$pieces[0].", ".$pieces[1].", ".$prod->getPrice().", $totalPrice)";
            }

            $this->db->ejecutar("INSERT INTO `cuerpocotizacion`(`idCotizacion`, `idProducto`, `cantidad`, `precioUnitario`, `precioMultiplicado`) VALUES $values");
        }

        if(!empty($givenData['personalProducts'])){
            $products = explode('¿', $givenData['personalProducts']);
            $values = '';

            foreach($products as $prodXquan){
                $pieces = explode('x', $prodXquan);
                $prod = new product($pieces[0]);
                $persXdays = explode('.', $pieces[1]);
                $totalPrice = $persXdays[0] * $persXdays[1] * $prod->getPrice();
                $values.= (empty($values)? '':', ')."($this->id, ".$pieces[0].", ".$pieces[1].", ".$prod->getPrice().", $totalPrice)";
            }

            $this->db->ejecutar("INSERT INTO `cuerpocotizacion`(`idCotizacion`, `idProducto`, `cantidad`, `precioUnitario`, `precioMultiplicado`) VALUES $values");
        }


        $this->audit->CrearNuevoRegistro(2, 4, $this->id, 'Se ha modificado la cotización #'.$this->id);
    }

    public function validateData($givenData){
        if($this->idStatus != 33){
            throw new Exception("Esta cotización no puede ser modificada");
        }

        if(!empty($givenData['idClient'])){
            $customer = new customer($givenData['idClient']);

            if($customer->getIdStatus() != 11){
                throw new Exception('Este cliente no está disponible');
            }
        }

        if(strlen($givenData['name']) < 5 || strlen($givenData['name']) > 50){
            throw new Exception('El nombre debe comprender una longitud de entre 5 y 50 caractéres');
        }

        if(!empty($givenData['expireDate'])){
            $pieces = explode('-', $givenData['expireDate']);
            if(count($pieces) != '3'){
                throw new Exception('La fecha de expiración cuenta con un formato incorrecto');
            }

            if($pieces[0] < 2024){
                throw new Exception('El año de la fecha de expiración no es válido');
            }
            if($pieces[1] > 12){
                throw new Exception('El mes de la fecha de expiración no es válido');
            }
            if($pieces[2] > 31){
                throw new Exception('El mes de la fecha de expiración no es válido');
            }
        }


        if(!empty($givenData['percentCAS'])){
            if(!is_numeric($givenData['percentCAS'])){
                throw new Exception('El costo asociado al salario no es válido');
            }
        }
        if(!empty($givenData['percentUti'])){
            if(!is_numeric($givenData['percentUti'])){
                throw new Exception('El porcentaje de utilidades no es válido');
            }
        }
        if(!empty($givenData['percentIVA'])){
            if(!is_numeric($givenData['percentIVA'])){
                throw new Exception('El porcentaje del I.V.A no es válido');
            }
        }


        if(empty($givenData['materialProducts']) && empty($givenData['equipProducts']) && empty($givenData['personalProducts'])){
            throw new Exception('No se agregó ningún producto a la cotización');
        }

        if(!empty($givenData['materialProducts'])){
            $pieces = explode('¿', $givenData['materialProducts']);
            foreach($pieces as $prodXquan){
                $parts = explode('x', $prodXquan);
                $product = new product($parts[0]);
                if($product->getIdStatus() > 4){
                    throw new Exception("El producto #".$product->getId().' no se encuentra disponible');
                }

                if(!is_numeric($parts[1]) || count(explode('.', $parts[1])) > 1){
                    throw new Exception("La cantidad especificada para el producto #".$product->getId().' no es válida');
                }
            }
        }

        if(!empty($givenData['equipProducts'])){
            $pieces = explode('¿', $givenData['equipProducts']);
            foreach($pieces as $prodXquan){
                $parts = explode('x', $prodXquan);
                $product = new product($parts[0]);
                if($product->getIdStatus() > 4){
                    throw new Exception("El producto #".$product->getId().' no se encuentra disponible');
                }

                if(!is_numeric($parts[1])){
                    throw new Exception("La cantidad especificada para el producto #".$product->getId().' no es válida');
                }
            }
        }

        if(!empty($givenData['personalProducts'])){
            $pieces = explode('¿', $givenData['personalProducts']);
            foreach($pieces as $prodXquan){
                $parts = explode('x', $prodXquan);
                $product = new product($parts[0]);
                if($product->getIdStatus() > 4){
                    throw new Exception("El producto #".$product->getId().' no se encuentra disponible');
                }

                if(!is_numeric($parts[1]) || count(explode('.', $parts[1])) != 2){
                    throw new Exception("La cantidad especificada para el producto #".$product->getId().' no es válida');
                }
            }
        }
    }

    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getClientCedula(){
        return $this->clientCedula;
    }
    public function getExpireDate(){
        return $this->expireDate;
    }
    public function getExpireDateReverse(){
        $pieces = explode('-', $this->expireDate);
        return implode('/', array_reverse($pieces));
    }
    public function getIdStatus(){
        return $this->idStatus;
    }
    public function getPUtilidades(){
        return $this->pUtilidades;
    }
    public function getPIVA(){
        return $this->pIVA;
    }
    public function getPCASalario(){
        return $this->pCASalario;
    }
    public function getIdOutputSetting(){
        return $this->idOutputSetting;
    }
    public function getCreationDate(){
        $result = '';
        $search = $this->db->consultar("SELECT * FROM `historial` WHERE (`idTipoDeHuella` = 1 AND `idTipoDeEntidad` = 4 AND `idDeEntidad` = $this->id)");
        if(!empty($search)){
            if(!empty($search[0]['fechaCreacion'])){
                $date_time = explode(' ', $search[0]['fechaCreacion']);
                $year_month_day = explode('-', $date_time[0]);
                $year_month_day = array_reverse($year_month_day);
                $result = implode('/', $year_month_day);
            }
        }
        
        return $result;
    }
    public function getUpdatedDate(){
        $result = '';
        $search = $this->db->consultar("SELECT * FROM `historial` WHERE (`idTipoDeHuella` = 2 AND `idTipoDeEntidad` = 4 AND`idDeEntidad` = $this->id) ORDER BY `id` DESC LIMIT 1;");
        if(!empty($search)){
            if(!empty($search[0]['fechaCreacion'])){
                $date_time = explode(' ', $search[0]['fechaCreacion']);
                $year_month_day = explode('-', $date_time[0]);
                $year_month_day = array_reverse($year_month_day);
                $result = implode('/', $year_month_day);
            }
        }
        
        return $result;
    }
    

    
    


    public function getProductsOnCategory($IDs){
        $result = array();
        if(count($IDs) < 1){
            throw new Exception('Error al leer las categorías a buscar');
        }

        $search = $this->db->consultar("SELECT * FROM `cuerpocotizacion` WHERE `idCotizacion` = $this->id");
        
        if(!empty($search)){
            foreach($search as $row){
                $product = new product($row['idProducto']);
                if(in_array($product->getIdCategory(), $IDs)){
                    $data = array(
                        'id' => $product->getId(),
                        'name' => $product->getName(),
                        'idCategory' => $product->getIdCategory(),
                        'img' => (empty($product->getImage())? 'ImagenPredefinida_Productos.png':$product->getImage()),
                        'unitSymbol' => $product->getUnitSimbol(),
                        'unitName' => $product->getUnitName(),
                        'defaultSpoilage' => $product->getDefaultSpoilage(),
                        'quantity' => $row['cantidad'],
                        'price' => $row['precioUnitario'],
                        'total' => number_format($row['precioMultiplicado'], 2, '.', '')
                    );

                    if($product->getIdCategory() > 2){
                        $pieces = explode('.', $row['cantidad']);

                        $data['quantityPerson'] = $pieces[0];
                        $data['quantityDays'] = $pieces[1];
                    }

                    $result[] = $data;
                }
                
            }
        }
        return $result;
    }

    public function getProductIDsOnCategory($IDs){
        if(count($IDs) < 1){
            throw new Exception('Error al leer las categoroias a buscar');
        }

        $categorySQL = '';
        foreach($IDs as $idCategory){
            if(empty($categorySQL)){
                $categorySQL ="`productos`.`idCategoria` = $idCategory";
            }else{
                $categorySQL.=" OR `productos`.`idCategoria` = $idCategory";
            }
        }
        
        $result = array();
        $search = $this->db->consultar("SELECT `cuerpocotizacion`.`idProducto`, `cuerpocotizacion`.`cantidad` FROM `cuerpocotizacion` INNER JOIN `productos` ON `cuerpocotizacion`.`idProducto` = `productos`.`id` WHERE `idCotizacion` = $this->id AND ($categorySQL)");

        if(!empty($search)){
            foreach($search as $id){
                $result[] = $id[0].'x'.$id[1];
            }
        }
        return $result;
    }
    

    public function delete(){
        if($this->idStatus == 35){
            throw new Exception('No es posible eliminar esta cotización');
        }

        $this->db->ejecutar("UPDATE `cotizaciones` SET `idEstado` = 35 WHERE `id` = $this->id");
        $this->audit->CrearNuevoRegistro(3, 4, $this->id, "Cotización #$this->id eliminada");
    }
}


class purchase {
    function __construct($id){
        if(!is_numeric($id) || $id<0){
            throw new Exception("El ID $id no es válido");
        }else{
            $this->db = new conexion();
            $this->audit = new historial();

            $search = $this->db->consultar("SELECT * FROM `ordenesdecompra` WHERE `id` = $id");
            if(empty($search)){
                throw new Exception("No se encontró #$id en la base de datos");
            }else{
                $data = $search[0];
            }


            $this->id = $data['id'];
            $this->name = $data['nombre'];
            $this->expireDate = $data['fechaExpiracion'];
            $this->idStatus = $data['idEstado'];
            $this->idInputSetting = $data['idAjusteDeEntrada'];
        }
    }

    public function updateData($givenData){
        $this->validateData($givenData);

        $clean_name = "'".str_replace("'", '', trim($givenData['name']))."'";
        $clean_expireDate = (isset($givenData['expireDate'])? "'".$givenData['expireDate']."'":'NULL');


        $this->db->ejecutar("UPDATE `ordenesdecompra` SET 
        `nombre` = $clean_name,
        `fechaExpiracion` = $clean_expireDate 
        WHERE `id` = $this->id");

        $this->db->ejecutar("DELETE FROM `cuerpoorden` WHERE `idOrden` = $this->id");

        foreach(explode('¿', $givenData['products']) as $idXquan){
            $pieces = explode('x', $idXquan);
            $this->db->ejecutar("INSERT INTO `cuerpoorden`(`idProducto`, `idOrden`, `cantidad`) VALUES (".$pieces[0].", $this->id, ".$pieces[1].")");
        }
    }

    public function validateData($givenData){
        if($this->idStatus != 63){
            throw new Exception('Esta orden de compra no puede ser modificada');
        }

        if(strlen($givenData['name']) < 5 || strlen($givenData['name']) > 50){
            throw new Exception('El nombre debe comprender una longitud de entre 5 y 50 caractéres');
        }

        if(!empty($givenData['expireDate'])){
            $pieces = explode('-', $givenData['expireDate']);
            if(count($pieces) != '3'){
                throw new Exception('La fecha de expiración cuenta con un formato incorrecto');
            }

            if($pieces[0] < 2024){
                throw new Exception('El año de la fecha de expiración no es válido');
            }
            if($pieces[1] > 12){
                throw new Exception('El mes de la fecha de expiración no es válido');
            }
            if($pieces[2] > 31){
                throw new Exception('El mes de la fecha de expiración no es válido');
            }
        }
        


        if(empty($givenData['products'])){
            throw new Exception("No se indicó ningún producto");
        }else{
            $pieces = explode('¿', $givenData['products']);
            foreach($pieces as $prodXquan){
                $parts = explode('x', $prodXquan);
                $product = new product($parts[0]);

                if(!is_numeric($parts[1])){
                    throw new Exception("La cantidad especificada para el producto #".$product->getId().' no es válida');
                }
            }
        }
    }

    public function delete(){
        if($this->idStatus == 66){
            throw new Exception("No es posible eliminar esta orden de compra");
        }
        $this->db->ejecutar("UPDATE `ordenesdecompra` SET `idEstado` = 66 WHERE `id` = $this->id");
        $this->audit->CrearNuevoRegistro(3, 8, $this->id, "Orden de compra  #$this->id eliminada");
    }

    public function setExpired(){
        $this->db->ejecutar("UPDATE `ordenesdecompra` SET `idEstado` = 64 WHERE `id` = $this->id");
    }

    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getExpireDate(){
        return $this->expireDate;
    }
    public function getIdStatus(){
        return $this->idStatus;
    }
    public function getIdInputSetting(){
        return $this->idInputSetting;
    }


    public function getStatusName(){
        $result = '';
        $search = $this->db->consultar("SELECT * FROM `estados` WHERE `id` = $this->idStatus");
        if(!empty($search)){
            $result = $search[0]['estado'];
        }

        return $result;
    }

    public function getCreationDate(){
        $result = '';
        $search = $this->db->consultar("SELECT * FROM `historial` WHERE `idTipoDeHuella` = 1 AND `idTipoDeEntidad` = 8 AND `idDeEntidad` = $this->id");
        if(!empty($search)){
            $pieces = explode(' ', $search[0]['fechaCreacion']);
            $pieces = explode('-', $pieces[0]);
            $pieces = array_reverse($pieces);
            $result = implode('-', $pieces);
        }

        return $result;
    }

    public function getModifyDate(){
        $result = '';
        $search = $this->db->consultar("SELECT * FROM `historial` WHERE `idTipoDeHuella` = 2 AND `idTipoDeEntidad` = 8 AND `idDeEntidad` = $this->id ORDER BY `id` DESC");
        if(!empty($search)){
            $pieces = explode(' ', $search[0]['fechaCreacion']);
            $pieces = explode('-', $pieces[0]);
            $pieces = array_reverse($pieces);
            $result = implode('-', $pieces);
        }

        return $result;
    }

    public function getExpirationDate(){
        $result = '';
        if(!empty($this->expireDate)){
            $pieces = explode('-', $this->expireDate);
            $pieces = array_reverse($pieces);
            $result = implode('-', $pieces);
        }
        return $result;
    }
    
    public function getPublicQuantity(){
        return count($this->getProducts());
    }


    public function getMaterialProducts(){
        $proucts = $this->getProducts();
        $result = array();

        foreach($proucts as $row){
            $product = new product($row['idProducto']);

            if($product->getIdCategory() == '1'){
                $row['name'] = $product->getName();
                $result[] = $row;
            }
        }

        return $result;
    }

    public function getEquipmentProducts(){
        $proucts = $this->getProducts();
        $result = array();

        foreach($proucts as $row){
            $product = new product($row['idProducto']);

            if($product->getIdCategory() == '2'){
                $row['name'] = $product->getName();
                $result[] = $row;
            }
        }

        return $result;
    }

    public function getProductList(){
        $result = '';
        foreach($this->getProducts() as $row){
            $result.= (empty($result)? '':'¿').$row['idProducto'].'x'.$row['cantidad'];
        }

        return $result;
    }

    public function getProducts(){
        if(!isset($this->products)){
            $this->products = $this->db->consultar("SELECT * FROM `cuerpoorden` WHERE `idOrden` = $this->id");
        }

        return $this->products;
    }

    public function getProviders(){
        $result = array();
        $providerSearch = $this->db->consultar("SELECT `idProveedor` FROM `productosdeproveedor` GROUP BY `idProveedor`");
        if(!empty($providerSearch)){

            $products = array();
            foreach($this->getProducts() as $row){
                $products[] = $row['idProducto'];
            }
            
            foreach($providerSearch as $row){
                $search = $this->db->consultar("SELECT * FROM `productosdeproveedor` WHERE `idProveedor` = ".$row['idProveedor']);
                $providerProducts = array();
                
                foreach($search as $row){
                    if(in_array($row['idProducto'], $products)){
                        $providerProducts[] = $row['idProducto'];
                    }   
                }



                if(!empty($providerProducts)){
                    $result[$row['idProveedor']] = array(
                        'idProveedor' => $row['idProveedor'],
                        'products' => $providerProducts
                    );
                }
            }

        }
        return $result;
    }
}


class user {
    private $username;
    private $password;
    private $idUserLevel;
    private $idQuestion1;
    private $idQuestion2;
    private $idQuestion3;
    private $answer1;
    private $answer2;
    private $answer3;
    private $docType;
    private $cedula;
    private $name;
    private $sex;
    private $idStatus;
    public $db;
    public $audit;


    public function createProvider($givenData, $files){
        $publicFunctions = new publicFunctions();
        $publicFunctions->validateNewProvider($givenData);

        $rif = "'".$givenData['cedula']."'";
        $docType = "'".$givenData['docType']."'";
        $name = "'".htmlentities(trim($givenData['name']))."'";
        $img = 'NULL';
        $status = '7';

        $address = (empty($givenData['address'])? 'NULL':"'".htmlentities(trim($givenData['address']))."'");
        $phone = (empty($givenData['phone'])? 'NULL':"'".$givenData['phone']."'");
        $phone2 = (empty($givenData['phone2'])? 'NULL':"'".$givenData['phone2']."'");
        $email = (empty($givenData['email'])? 'NULL':"'".$givenData['email']."'");


        if(!empty($files['image']['type'])){
            $validFormat = array('png', 'jpg', 'jpeg');
            $format = explode('/', $files['image']['type']);
            
            if(!is_numeric(array_search($format[1], $validFormat))){
                throw new Exception('El formato no es válido');
            }


            $time = new DateTime();
            $imageName = $time->getTimestamp()."_".$files['image']['name'];
            $ruta = "../../Imagenes/Proveedores/$imageName";
            
            
            move_uploaded_file($files['image']['tmp_name'], $ruta);

            $img = "'$imageName'";
        }

        $sql = "INSERT INTO `contactos`(`direccion`, `telefono1`, `telefono2`, `correo`) VALUES 
        ($address, $phone, $phone2, $email)";

        $createdID = $this->db->ejecutar($sql);
        

        $sql = "INSERT INTO `proveedores`(`rif`, `tipoDeDocumento`, `nombre`, `idContacto`, `idEstado`, `ULRImagen`) VALUES 
        ($rif, $docType, $name, $createdID, $status, $img)";
        
        $createdID =  $this->db->ejecutar($sql);

        if(!empty($givenData['productsList'])){
            foreach(explode('¿', $givenData['productsList']) as $id){
                $this->db->ejecutar("INSERT INTO `productosdeproveedor`(`idProducto`, `idProveedor`) VALUES ($id, $rif)");
            }
        }

        return $givenData['cedula'];
    }

    public function createProduct($givenData, $files){
        $publicFunctions = new publicFunctions();
        $publicFunctions->validateNewProduct($givenData);
        if(empty($givenData['idUnit'])){
            $givenData['idUnit'] = '2';
        }

        $name = "'".htmlentities(trim($givenData['name']))."'";
        $idCat = $givenData['idCategory'];
        $price = $givenData['price'];
        $idUnit = ($idCat==3? '1':($idCat==1? $givenData['idUnit']:'2'));
        $desc = (empty($givenData['description'])? 'NULL':"'".htmlentities(trim($givenData['description']))."'");
        $alertLevel = ($idCat<3? $givenData['alertLevel']:'NULL');
        $spoilage = ($idCat==2? $givenData['deafultSpoilage']:'1');
        $img = 'NULL';
        $status = ($idCat<3? '3':'1');


        if(!empty($files['image']['type'])){
            $validFormat = array('png', 'jpg', 'jpeg');
            $format = explode('/', $files['image']['type']);
            
            if(!is_numeric(array_search($format[1], $validFormat))){
                throw new Exception('El formato no es válido');
            }


            $time = new DateTime();
            $imageName = $time->getTimestamp()."_".$files['image']['name'];
            $ruta = "../../Imagenes/Productos/$imageName";
            
            
            move_uploaded_file($files['image']['tmp_name'], $ruta);

            $img = "'$imageName'";
        }
        

        
        
        $createdID = $this->db->ejecutar("INSERT INTO `productos`(`nombre`, `idCategoria`, `precio`, `idUnidadDeMedida`, `descripcion`, `nivelDeAlerta`, `ULRImagen`, `idEstado`) 
        VALUES ($name, $idCat, $price, $idUnit, $desc, $alertLevel, $img, $status)");

        
        $this->db->ejecutar("INSERT INTO `depreciacion`(`valor`, `idProducto`) VALUES ($spoilage, $createdID)");


        if(!empty($givenData['providers'])){
            foreach(explode('¿', $givenData['providers']) as $rif){
                $this->db->ejecutar("INSERT INTO `productosdeproveedor`(`idProducto`, `idProveedor`) VALUES ($createdID, '$rif')");
            }
        }



        return $createdID;
    }

    public function getModulesID(){
        $result = array();
        $search = $this->db->consultar("SELECT * FROM `modulospermitidos` WHERE `usuario` = '$this->username'");
        if(!empty($search)){
            foreach($search as $row){
                $result[] = $row['idModulo'];
            }
        }
        return $result;
    }

    function __construct($id){
        if(empty($id)){
            throw new Exception("No se especificó el usuario");
        }else{
            $this->db = new conexion();
            $this->audit = new historial();

            $id = trim(str_replace("'", '', $id));
            $search = $this->db->consultar("SELECT * FROM `usuarios` WHERE `nombreDeUsuario` = '$id';");
            if(empty($search)){
                throw new Exception("No se encontró el usuario $id en la base de datos");
            }else{
                $data = $search[0];

                $this->username = $data['nombreDeUsuario'];
                $this->password = $data['contrasenia'];
                $this->idUserLevel = $data['idNivelDeUsuario'];
                $this->idQuestion1 = $data['idPregunta1'];
                $this->idQuestion2 = $data['idPregunta2'];
                $this->idQuestion3 = $data['idPregunta3'];
                $this->answer1 = $data['respuesta1'];
                $this->answer2 = $data['respuesta2'];
                $this->answer3 = $data['respuesta3'];
                $this->docType = $data['tipoDeDocumento'];
                $this->cedula = $data['cedula'];
                $this->name = $data['nombres'];
                $this->sex = $data['sexo'];
                $this->idStatus = $data['idEstado'];
            }

        }
    }

    public function getUsername(){
        return $this->username;
    }
    public function getPassword(){
        return $this->password;
    }
    public function getIdUserLevel(){
        return $this->idUserLevel;
    }
    public function getIdQuestion1(){
        return $this->idQuestion1;
    }
    public function getIdQuestion2(){
        return $this->idQuestion2;
    }
    public function getIdQuestion3(){
        return $this->idQuestion3;
    }
    public function getAnswer1(){
        return $this->answer1;
    }
    public function getAnswer2(){
        return $this->answer2;
    }
    public function getAnswer3(){
        return $this->answer3;
    }
    public function getDocType(){
        return $this->docType;
    }
    public function getCedula(){
        return $this->cedula;
    }
    public function getName(){
        return $this->name;
    }
    public function getSex(){
        return $this->sex;
    }
    public function getIdStatus(){
        return $this->idStatus;
    }
    public function getPermissionsList(){
        $result = array();
        $search = $this->db->consultar("SELECT `modulos`.* FROM `modulospermitidos` INNER JOIN `modulos` ON `modulospermitidos`.`idModulo` = `modulos`.`id` WHERE `modulospermitidos`.`usuario` = '$this->username'");
        if(!empty($search)){
            foreach($search as $row){
                $result[] = array(
                    'nombre' => $row['nombre'],
                    'nombreDeImagen' => $row['nombreDeImagen']
                );
            }
        }

        return $result;
    }

    public function validateData($givenData){
        if(empty($givenData['docType'])){
            throw new Exception("No se especificó el tipo de documento");
        }

        if(empty($givenData['cedula'])){
            throw new Exception("No se especificó la cédula");
        }else{
            if(!is_numeric($givenData['cedula'])){
                throw new Exception("La cédula no es válida");
            }
        }

        if(empty($givenData['name'])){
            throw new Exception("No se especificó el nombre");
        }else{
            if(strlen($givenData['name'])<4 || strlen($givenData['name'])>40){
                throw new Exception("El nombre debe comprender un longitud entre 4 y 40 caractéres");
            }
        }

        if(empty($givenData['sex'])){
            throw new Exception("No se especificó el sexo");
        }else{
            if($givenData['sex'] != 'M' && $givenData['sex'] != 'F'){
                throw new Exception("El sexo es inválido");
            }
        }


        if(!empty($givenData['password'])){
            if(strlen($givenData['password']) < 8 || strlen($givenData['password']) > 20){
                throw new Exception("La contraseña debe comprender una longitud entre 8 y 20 caracteréres");
            }else{
                if(preg_match('~[0-9]+~', $givenData['password']) && !is_numeric($givenData['password'])) {
                    
                }else{
                    throw new Exception("La nueva contraseña debe contener numeros y letras");
                }
            }
        }


        if($givenData['profileLevel'] != '1' && $givenData['profileLevel'] != '2' && $givenData['profileLevel'] != '3'){
            throw new Exception("El nivel de usuario no es válido");
        }
    }

    public function updateData($givenData){
        $this->validateData($givenData);
        

        $modulosPermitidos = array();
        if(isset($givenData['permiso-Almacenes'])){
            $modulosPermitidos[] = 1;
        }
        if(isset($givenData['permiso-Ventas'])){
            $modulosPermitidos[] = 2;
        }
        if(isset($givenData['permiso-Clientes'])){
            $modulosPermitidos[] = 3;
        }
        if(isset($givenData['permiso-Inventario'])){
            $modulosPermitidos[] = 4;
        }
        if(isset($givenData['permiso-Productos'])){
            $modulosPermitidos[] = 5;
        }
        if(isset($givenData['permiso-Compras'])){
            $modulosPermitidos[] = 6;
        }
        if(isset($givenData['permiso-Proveedores'])){
            $modulosPermitidos[] = 7;
        }
        if(isset($givenData['permiso-Auditoría'])){
            $modulosPermitidos[] = 8;
        }
        if(isset($givenData['permiso-Usuarios'])){
            $modulosPermitidos[] = 9;
        }
        if(isset($givenData['permiso-Sistema'])){
            $modulosPermitidos[] = 10;
        }


        if(empty($modulosPermitidos)){
            throw new Exception("No se otorgó ningun permiso");
        }


        $this->db->ejecutar("DELETE FROM `modulospermitidos` WHERE `usuario` = '$this->username'");

        foreach($modulosPermitidos as $id){
            $this->db->ejecutar("INSERT INTO `modulospermitidos`(`usuario`, `idModulo`) VALUES ('$this->username', $id)");
        }


        if(empty($givenData['password'])){
            $newPassword = $this->password;
        }else{
            $newPassword = password_hash(strtolower($givenData['password']), PASSWORD_DEFAULT, ['cost' => 10]);
        }

        
        

        $cleanData = array(
            'docType' => "'".$givenData['docType']."'",
            'cedula' => $givenData['cedula'],
            'name' => "'".htmlentities(trim(str_replace("'", '', $givenData['name'])))."'",
            'sex' => "'".$givenData['sex']."'",
            'password' => "'$newPassword'",
            'idProfileLevel' => $givenData['profileLevel'],
        );


        $this->db->ejecutar("UPDATE `usuarios` SET 
        `contrasenia` = ".$cleanData['password'].",
        `idNivelDeUsuario` = ".$cleanData['idProfileLevel'].",
        `tipoDeDocumento` = ".$cleanData['docType'].",
        `cedula` = ".$cleanData['cedula'].",
        `nombres` = ".$cleanData['name'].",
        `sexo` = ".$cleanData['sex']." 
        WHERE `nombreDeUsuario` = '$this->username'");

        $this->audit->CrearNuevoRegistro(2, 5, $this->id, "Usuario $this->username modificado");
    }

    public function isModuloAble($idModulo){
        $search = $this->db->consultar("SELECT * FROM `modulospermitidos` WHERE (`usuario` = '$this->username' AND `idModulo` = '$idModulo')");
        return !empty($search);
    }


    public function disableUser(){
        if($this->idUserLevel == 1){
            $search = $this->db->consultar("SELECT * FROM `usuarios` WHERE (`idNivelDeUsuario` = 1 AND `nombreDeUsuario` != '$this->username')");
            if(empty($search)){
                throw new Exception("No es posible inhabilitar este usuario porque es el unico administrador");
            }
        }


        $this->db->ejecutar("UPDATE `usuarios` SET `idEstado` = 42 WHERE `nombreDeUsuario` = '$this->username'");

        $this->audit->CrearNuevoRegistro(2, 5, $this->username, "Usuario $this->username inhabilitado");
    }


    public function enableUser(){
        $this->db->ejecutar("UPDATE `usuarios` SET `idEstado` = 41 WHERE `nombreDeUsuario` = '$this->username'");

        $this->audit->CrearNuevoRegistro(2, 5, $this->username, "Usuario $this->username habilitado");
    }
}


class store {


    function __construct($id){
        if(!is_numeric($id) || $id<0){
            throw new Exception("El ID $id no es válido");
        }else{
            $this->db = new conexion();
            $this->audit = new historial();

            $search = $this->db->consultar("SELECT * FROM `almacenes` WHERE `id` = $id");
            if(empty($search)){
                throw new Exception("No se encontró #$id en la base de datos");
            }else{
                $data = $search[0];
            }

            $this->id = $data['id'];
            $this->name = $data['nombre'];
            $this->address = $data['direccion'];
            $this->default = ($data['predeterminado']=='1');
            $this->idStatus = $data['idEstado'];
        }
    }

    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getAddress(){
        return $this->address;
    }
    public function getDefault(){
        return $this->default;
    }
    public function getIdStatus(){
        return $this->idStatus;
    }

    public function validateData($data){
        if(empty($data['name'])){
            throw new Exception("No se especificó el nombre");
        }else{
            if(strlen($data['name'])<4 || strlen($data['name'])>40){
                throw new Exception("El nombre debe comprender un longitud entre 4 y 40 caractéres");
            }
        }

        if(empty($data['address'])){
            throw new Exception("No se especificó la dirección");
        }else{
            if(strlen($data['address'])<5 || strlen($data['address'])>60){
                throw new Exception("La dirección debe comprender una longitud entre 5 y 60 caractéres");
            }
        }
    }

    public function updateData($data){
        $this->validateData($data);

        $cleanData = array(
            'name' => "'".trim($data['name'])."'",
            'address' => "'".trim($data['address'])."'"
        );

        $sql = "UPDATE `almacenes` SET 
        `nombre` = ".$cleanData['name'].", 
        `direccion` = ".$cleanData['address']." 
        WHERE `id` = $this->id";
        
        $this->db->ejecutar($sql);
        $this->audit->CrearNuevoRegistro(2, 1, $this->id, "Almacén #$this->id modificado");
    }

    public function delete(){
        $search = $this->db->consultar("SELECT * FROM `inventario` WHERE `idAlmacen` = $this->id AND `existencia` > 0");
        if(!empty($search)){
            throw new Exception("Debe vaciar el inventario antes de ser eliminado");
        }

        $search = $this->db->consultar("SELECT * FROM `almacenes` WHERE `idEstado` = 51 AND `id` != $this->id");
        if(empty($search)){
            throw new Exception("No es posible eliminar este almacén porque es el único activo actualmente");
        }

        $this->db->ejecutar("UPDATE `almacenes` SET `idEstado` = 54 WHERE `id` = $this->id");
        $this->audit->CrearNuevoRegistro(3, 1, $this->id, "Almacén #$this->id eliminado");
    }
}


class provider{
    private $id;
    private $docType;
    private $name;
    private $idContact;
    private $idStatus;
    private $img;

    private $address;
    private $phone;
    private $phone2;
    private $email;

    function __construct($id){
        if(!is_numeric($id) || $id<0){
            throw new Exception("El ID $id no es válido");
        }else{
            $this->db = new conexion();
            $search = $this->db->consultar("SELECT * FROM `proveedores` INNER JOIN `contactos` ON `proveedores`.`idContacto` = `contactos`.`id` WHERE `rif` = '$id'");
            if(empty($search)){
                throw new Exception("No se encontró el proveedor #$id en la base de datos");
            }else{
                $data = $search[0];
            }

            $this->id = $data['rif'];
            $this->docType = $data['tipoDeDocumento'];
            $this->name = $data['nombre'];
            $this->idContact = $data['idContacto'];
            $this->idStatus = $data['idEstado'];
            $this->img = $data['ULRImagen'];

            $this->address = $data['direccion'];
            $this->phone = $data['telefono1'];
            $this->phone2 = $data['telefono2'];
            $this->email = $data['correo'];
        }
    }

    public function getId(){
        return $this->id;
    }
    public function getDocType(){
        return $this->docType;
    }
    public function getName(){
        return $this->name;
    }
    public function getIdContact(){
        return $this->idContact;
    }
    public function getIdStatus(){
        return $this->idStatus;
    }
    public function getImg(){
        return $this->img;
    }
    public function getAddress(){
        return $this->address;
    }
    public function getPhone(){
        return $this->phone;
    }
    public function getPhone2(){
        return $this->phone2;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getPhoneToUser(){
        if(!empty($this->phone) && !empty($this->phone2)){
            return "$this->phone / $this->phone2";
        }else{
            return $this->phone.$this->phone2;
        }
    }

    public function getProductsID(){
        $result = '';
        $search = $this->db->consultar("SELECT * FROM `productosdeproveedor` WHERE `idProveedor` = '$this->id'");
        foreach($search as $row){
            $result.= (empty($result)? '':'¿').$row['idProducto'];
        }
        return $result;
    }


    public function validateData($data){
        
        if(empty($data['docType'])){
            throw new Exception("No se especificó el tipo de documento");
        }

        if(empty($data['name'])){
            throw new Exception("No se especificó el nombre");
        }else{
            if(strlen($data['name'])<4 || strlen($data['name'])>40){
                throw new Exception("El nombre debe comprender un longitud entre 4 y 40 caractéres");
            }
        }

        if(!empty($data['phone'])){
            if(strlen($data['phone'])<12 || strlen($data['phone'])>14){
                throw new Exception("El teléfono debe comprender una longitud entre 12 y 14 caractéres");
            }
        }

        if(!empty($data['phone2'])){
            if(strlen($data['phone2'])<12 || strlen($data['phone2'])>14){
                throw new Exception("El teléfono 2 debe comprender una longitud entre 12 y 14 caractéres");
            }
        }

        if(!empty($data['email'])){
            if(strlen($data['email'])>50){
                throw new Exception("El correo debe comprender una longitud menor a 50 caractéres");
            }else{
                if(strpos($data['email'], '@')==0 ||strpos($data['email'], '.')==0){
                    throw new Exception("El correo no cuenta con un formato correcto");
                }
            }
        }

        if(!empty($data['address'])){
            if(strlen($data['address'])<5 || strlen($data['address'])>60){
                throw new Exception("La dirección debe comprender una longitud entre 5 y 60 caractéres");
            }
        }


        if(!empty($data['products'])){
            $array_products = explode('¿', $data['products']);

            foreach($array_products as $ID){
                $xd = new product($ID);
                $xd = null;
            }
        }
    }

    public function updateData($data, $files){
        
        $this->validateData($data, $files);
        if(!empty($files['image']['type'])){
            $validFormat = array('png', 'jpg', 'jpeg');
            $format = explode('/', $files['image']['type']);
            
            if(!is_numeric(array_search($format[1], $validFormat))){
                throw new Exception('El formato no es válido');
            }
        }

        $cleanData = array(
            'docType' => "'".$data['docType']."'",
            'name' => "'".trim($data['name'])."'",
            'phone' => empty($data['phone'])? 'NULL':"'".trim($data['phone'])."'",
            'phone2' => empty($data['phone2'])? 'NULL':"'".trim($data['phone2'])."'",
            'email' => empty($data['email'])? 'NULL':"'".clean($data['email'])."'",
            'address' => empty($data['address'])? 'NULL':"'".clean($data['address'])."'"
        );



        if(empty($files['image']['type'])){
            $cleanData['image'] = empty($this->img)? "NULL":"'".$this->img."'";
        }else{
            $time = new DateTime();
            $imageName = $time->getTimestamp()."_".$files['image']['name'];
            $ruta = "../../Imagenes/Proveedores/$imageName";
            
            
            move_uploaded_file($files['image']['tmp_name'], $ruta);

            
            if(!empty($this->img)){
                unlink("../../Imagenes/Proveedores/$this->img");
            }

            $cleanData['image'] = "'$imageName'";
        }

        
                


        $sql = "UPDATE `contactos` SET
         `direccion`= ".$cleanData['address']."
        ,`telefono1`= ".$cleanData['phone']."
        ,`telefono2`= ".$cleanData['phone2']."
        ,`correo`= ".$cleanData['email']."
         WHERE `id` = $this->idContact";

        
        $this->db->ejecutar($sql);

        $sql = "UPDATE `proveedores` SET
         `tipoDeDocumento`= ".$cleanData['docType']."
        ,`nombre`= ".$cleanData['name']."
        ,`ULRImagen`= ".$cleanData['image']."
         WHERE `rif` = '$this->id'";

        $this->db->ejecutar($sql);
        
        

        $sql = "DELETE FROM `productosdeproveedor` WHERE `idProveedor` = '$this->id'";
        $this->db->ejecutar($sql);

        
        if(!empty($data['products'])){
            $array_products = explode('¿', $data['products']);
            
            foreach($array_products as $idProduct){
                $sql = "INSERT INTO `productosdeproveedor`(`idProducto`, `idProveedor`) VALUES ($idProduct, '$this->id');";
                $this->db->ejecutar($sql);
            }
        }


        
        $Auditoria = new historial();
        $Auditoria->CrearNuevoRegistro(2, 2, $this->id, "Proveedor #$this->id modificado");
    }

    public function delete(){
        $this->db->ejecutar("UPDATE `proveedores` SET `idEstado` = 10 WHERE `rif` = '$this->id'");


        $this->db->ejecutar("DELETE FROM `productosdeproveedor` WHERE `idProveedor` = '$this->id'");
        
        $Auditoria = new historial();
        $Auditoria->CrearNuevoRegistro(3, 2, $this->id, "Proveedor #$this->id eliminado");
    }
}

class product{
    private $id;
    private $name;
    private $idCategory;
    private $price;
    private $idUnit;
    private $description;
    private $alertLevel;
    private $img;
    private $idStatus;

    function getDefaultSpoilage(){
        if(!isset($this->defaultSpoilage)){
            $search = $this->db->consultar("SELECT * FROM `depreciacion` WHERE `idProducto` = $this->id");
            if(!empty($search)){
                $this->defaultSpoilage = $search[0]['valor'];
            }else{
                $this->defaultSpoilage = 1;
            }
        }
        
        return number_format($this->defaultSpoilage, 4, '.', '');
    }

    function getStockExistence(){
        if($this->idCategory > 2){
            throw new Exception("El producto $this->id no es almacenable");
        }

        $result = 0;
        $search = $this->db->consultar("SELECT * FROM `inventario` WHERE `idProducto` = $this->id");
        if(!empty($search)){
            foreach($search as $row){
                $result+= $row['existencia'];
            }
        }
        return $result;
    }


    function __construct($id){
        if(!is_numeric($id) || $id<0){
            throw new Exception("El ID $id no es válido");
        }else{
            $this->db = new conexion();
            $search = $this->db->consultar("SELECT * FROM `productos` WHERE `id` =  $id");
            if(empty($search)){
                throw new Exception("No se encontró el producto #$id en la base de datos");
            }else{
                $data = $search[0];
            }

            $this->id = $data['id'];
            $this->name = $data['nombre'];
            $this->idCategory = $data['idCategoria'];
            $this->price = $data['precio'];
            $this->idUnit = $data['idUnidadDeMedida'];
            $this->description = $data['descripcion'];
            $this->alertLevel = $data['nivelDeAlerta'];
            $this->img = $data['ULRImagen'];
            $this->idStatus = $data['idEstado'];
        }
    }

    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getIdCategory(){
        return $this->idCategory;
    }
    public function getPrice(){
        return $this->price;
    }
    public function getIdUnit(){
        return $this->idUnit;
    }
    public function getDescription(){
        return $this->description;
    }
    public function getAlertLevel(){
        return $this->alertLevel;
    }
    public function getImage(){
        return $this->img;
    }
    public function getIdStatus(){
        return $this->idStatus;
    }


    public function getUnitSimbol(){
        $search = $this->db->consultar("SELECT * FROM `unidadesdemedida` WHERE `id` = $this->idUnit");
        if(!empty($search)){
            return $search[0]['simbolo'];
        }
    }
    public function getUnitName(){
        $search = $this->db->consultar("SELECT * FROM `unidadesdemedida` WHERE `id` = $this->idUnit");
        if(!empty($search)){
            return $search[0]['nombre'];
        }
    }


    public function getProvidersID(){
        $result = '';
        $search = $this->db->consultar("SELECT * FROM `productosdeproveedor` WHERE `idProducto` = $this->id");
        foreach($search as $row){
            $result.= (empty($result)? '':'¿').$row['idProveedor'];
        }
        return $result;
    }

    public function validateData($givenData){
        
        if(empty($givenData['name'])){
            throw new Exception("No se especificó el nombre");
        }else{
            if(strlen($givenData['name']) < 3 || strlen($givenData['name'])> 30){
                throw new Exception("El nombre debe comprender una longitud entre 3 y 30 caractéres.");
            }
        }
        

        if(empty($givenData['price'])){
            //throw new Exception("No se especificó el precio");
        }else{
            if(!is_numeric($givenData['price'])){
                throw new Exception("El precio no es válido");
            }
        }

        if(empty($givenData['idCategory'])){
            throw new Exception("No se especificó la categoría");
        }else{
            if($givenData['idCategory'] != '1' && $givenData['idCategory'] != '2' && $givenData['idCategory'] != '3' && $givenData['idCategory'] != '4'){
                throw new Exception("La categoría no es válida");
            }
        }

        if(empty($givenData['description'])){
            //throw new Exception("No se especificó la descipción");
        }else{
            if(strlen($givenData['description']) > 150){
                throw new Exception("La descripción debe comprender una longitud máxima de 150 caractéres.");
            }
        }


        if($givenData['idCategory'] == '1'){
            if(empty($givenData['idUnit'])){
                throw new Exception("No se especificó la unidad de medida");
            }else{
                if(!is_numeric($givenData['idUnit'])){
                    throw new Exception("La unidad de medida no es válida");
                }
            }
        }

        if($givenData['idCategory']<=2){
            if(empty($givenData['alertLevel'])){
                throw new Exception("No se especificó el nivel de alerta");
            }else{
                if(!is_numeric($givenData['alertLevel'])){
                    throw new Exception("El nivel de alerta no es válido");
                }
            }
        }

        if($givenData['idCategory']==2){
            if(empty($givenData['deafultSpoilage']) || $givenData['deafultSpoilage'] <= 0){
                throw new Exception("La depreciación estándar debe ser mayor a 0 ");
            }
        }


        if(!empty($givenData['providers'])){
            $array_providers = explode('¿', $givenData['providers']);
            
            foreach($array_providers as $idProvider){
                if(!empty($idProvider)){
                    $p = new provider($idProvider);
                    $p = null;
                }
            }
        }
    }

    public function updateData($givenData, $files){
        $this->validateData($givenData);

        if(empty($this->img)){
            $imgName = 'NULL';
        }else{
            $imgName = "'$this->img'";
        }

        if(!empty($files['image']['type'])){
            $validFormat = array('image/png', 'image/jpg', 'image/jpeg');
            if(!is_numeric(array_search($files['image']['type'], $validFormat))){
                throw new Exception('El formato no es válido');
            }else{
                $time = new DateTime();
                $imageName = $time->getTimestamp()."_".$files['image']['name'];
                $ruta = "../../Imagenes/Productos/$imageName";
                
                move_uploaded_file($files['image']['tmp_name'], $ruta);
                unlink("../../Imagenes/Productos/$this->img");
                $imgName = "'$imageName'";
            }
        }
        

        

        $cleanData = array(
            'name' => "'".htmlentities(trim($givenData['name']))."'",
            'price' => (empty($givenData['price'])? '0':$givenData['price']),
            'idCategory' => $givenData['idCategory'],
            'idUnit' => $givenData['idUnit'],
            'alertLevel' => $givenData['alertLevel'],
            'description' => (empty($givenData['description'])? 'NULL':"'".htmlentities(trim($givenData['description']))."'")
        );
        $defaultSpoilage = ($givenData['idCategory']==2? $givenData['deafultSpoilage']:1);

        if($givenData['idCategory'] == '2'){
            $cleanData['idUnit'] = '2';
        }
        if($givenData['idCategory'] == '3'){
            $cleanData['idUnit'] = '1';
            $cleanData['alertLevel'] = 'NULL';
        }
        if($givenData['idCategory'] == '4'){
            $cleanData['idUnit'] = '2';
            $cleanData['alertLevel'] = 'NULL';
        }

        $sql = "UPDATE `productos` SET
         `nombre`= ".$cleanData['name']."
         ,`idCategoria`= ".$cleanData['idCategory']."
         ,`precio`= ".$cleanData['price']."
         ,`idUnidadDeMedida`= ".$cleanData['idUnit']."
         ,`descripcion`= ".$cleanData['description']."
         ,`nivelDeAlerta`= ".$cleanData['alertLevel']."
         ,`ULRImagen`= $imgName
         WHERE `id` = ".$this->id;
        
         $this->db->ejecutar($sql);

        
        $sql = "DELETE FROM `productosdeproveedor` WHERE `idProducto` = ".$this->id;
        $this->db->ejecutar($sql);

        if(!empty($givenData['providers'])){
            $array_providers = explode('¿', $givenData['providers']);
            foreach($array_providers as $idProvider){
                $sql = "INSERT INTO `productosdeproveedor`(`idProducto`, `idProveedor`) VALUES ($this->id, $idProvider);";
                $this->db->ejecutar($sql);
            }
        }
        
        if($givenData['idCategory'] == '2'){
            $search = $this->db->consultar("SELECT * FROM `depreciacion` WHERE `idProducto` = $this->id");
            if(empty($search)){
                $this->db->ejecutar("INSERT INTO `depreciacion`(`valor`, `idProducto`) VALUES ($defaultSpoilage, $this->id)");
            }else{
                $this->db->ejecutar("UPDATE `depreciacion` SET `valor` = $defaultSpoilage WHERE `idProducto` = $this->id");
            }
        }
        
        $Auditoria = new historial();
        $Auditoria->CrearNuevoRegistro(2, 1, $this->id, "Producto #$this->id modificado");
    }

    public function delete(){
        if($this->idStatus > 3){
            throw new Exception('No es posible eliminar este producto');
        }

        $search = $this->db->consultar("SELECT * FROM `inventario` WHERE `idProducto` = $this->id");
        if(!empty($search)){
            $existencia = 0;
            foreach($search as $row){
                $existencia+= $row['existencia'];
            }

            if($existencia>0){
                throw new Exception("No es posible eliminar porque tiene una existencia de $existencia ".$this->getUnitName()." en el inventario.");
            }
        }
        

        $search = $this->db->consultar("SELECT * FROM `cuerpocotizacion` INNER JOIN `cotizaciones` ON `cuerpocotizacion`.`idCotizacion` = `cotizaciones`.`id` 
        WHERE (`cuerpocotizacion`.`idProducto` = $this->id AND `cotizaciones`.`idEstado` = 33)");
        if(!empty($search)){
            throw new Exception("No es posible eliminar este producto porque forma parte de la venta ".'"'.$search[0]['nombre'].'"');
        }

        $search = $this->db->consultar("SELECT * FROM `cuerpoorden` INNER JOIN `ordenesdecompra` ON `cuerpoorden`.`idOrden` = `ordenesdecompra`.`id` 
        WHERE (`idProducto` = $this->id AND `ordenesdecompra`.`idEstado` = 63)");
        if(!empty($search)){
            throw new Exception("No es posible eliminar este producto porque forma parte de la compra ".'"'.$search[0]['nombre'].'"');
        }

        $this->db->ejecutar("DELETE FROM `productosdeproveedor` WHERE `idProducto` = $this->id");
        
        
        $this->db->ejecutar("UPDATE `productos` SET `idEstado` = 4 WHERE `id` = $this->id");
        $Auditoria = new historial();
        $Auditoria->CrearNuevoRegistro(3, 1, $this->id, "Producto #$this->id eliminado");
    }
}



class customer {
    private $id;
    private $docType;
    private $name;
    private $idContact;
    private $idStatus;
    private $img;

    private $address;
    private $phone;
    private $phone2;
    private $email;
    
    function __construct($id){
        if(!is_numeric($id) || $id<0){
            throw new Exception("El ID $id no es válido");
        }else{
            $this->db = new conexion();
            $search = $this->db->consultar("SELECT * FROM `clientes` INNER JOIN `contactos` ON `clientes`.`idContacto` = `contactos`.`id` 
            WHERE `rif` = '$id'");
            if(empty($search)){
                throw new Exception("No se encontró #$id en la base de datos");
            }else{
                $data = $search[0];
            }
        }

        $this->id = $data['rif'];
        $this->docType = $data['tipoDeDocumento'];
        $this->name = $data['nombre'];
        $this->idContact = $data['idContacto'];
        $this->idStatus = $data['idEstado'];
        $this->img = $data['ULRImagen'];

        $this->address = $data['direccion'];
        $this->phone = $data['telefono1'];
        $this->phone2 = $data['telefono2'];
        $this->email = $data['correo'];
    }

    public function delete(){
        if($this->idStatus != 11){
            throw new Exception('No es posible eliminar este cliente');
        }

        if(!empty($this->db->consultar("SELECT * FROM `cotizaciones` WHERE (`cedulaCliente` = '$this->id' AND `idEstado` = 33)"))){
            throw new Exception('No es posible eliminar este cliente porque cuenta con contizaciones en espera');
        }

        $this->db->ejecutar("UPDATE `clientes` SET `idEstado` = 13 WHERE `rif` = '$this->id'");
        $Auditoria = new historial();
        $Auditoria->CrearNuevoRegistro(3, 3, $this->id, "Cliente #$this->id eliminado");
    }

    public function getId(){
        return $this->id;
    }
    public function getDocType(){
        return $this->docType;
    }
    public function getName(){
        return $this->name;
    }
    public function getIdContact(){
        return $this->idContact;
    }
    public function getIdStatus(){
        return $this->idStatus;
    }
    public function getImg(){
        return $this->img;
    }
    public function getAddress(){
        return $this->address;
    }
    public function getPhone(){
        return $this->phone;
    }
    public function getPhone2(){
        return $this->phone2;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getPhoneData(){
        if(empty($this->phone) || empty($this->phone2)){
            return $this->phone.$this->phone2;
        }else{
            return $this->phone.' / '.$this->phone2;
        }
    }
    

    public function validateData($data){
        if(empty($data['docType'])){
            throw new Exception("No se especificó el tipo de documento");
        }

        if(empty($data['name'])){
            throw new Exception("No se especificó el nombre");
        }else{
            if(strlen($data['name'])<4 || strlen($data['name'])>40){
                throw new Exception("El nombre debe comprender un longitud entre 4 y 40 caractéres");
            }
        }

        if(!empty($data['phone'])){
            if(strlen($data['phone'])<12 || strlen($data['phone'])>14){
                throw new Exception("El teléfono debe comprender una longitud entre 12 y 14 caractéres");
            }
        }

        if(!empty($data['phone2'])){
            if(strlen($data['phone2'])<12 || strlen($data['phone2'])>14){
                
            }
            
        }
        

        if(!empty($data['email'])){
            if(strlen($data['email'])>50){
                throw new Exception("El correo debe comprender una longitud menor a 50 caractéres");
            }else{
                if(strpos($data['email'], '@')==0 ||strpos($data['email'], '.')==0){
                    throw new Exception("El correo no cuenta con un formato correcto");
                }
            }
        }

        if(!empty($data['address'])){
            if(strlen($data['address'])<5 || strlen($data['address'])>60){
                throw new Exception("La dirección debe comprender una longitud entre 5 y 60 caractéres");
            }
        }
    }

    public function updateData($data, $files){
        
        $this->validateData($data, $files);
        if(!empty($files['image']['type'])){
            $validFormat = array('png', 'jpg', 'jpeg');
            $format = explode('/', $files['image']['type']);
            
            if(!is_numeric(array_search($format[1], $validFormat))){
                throw new Exception('El formato no es válido');
            }
        }


        $cleanData = array(
            'docType' => "'".$data['docType']."'",
            'name' => "'".trim($data['name'])."'",
            'phone' => empty($data['phone'])? 'NULL':"'".trim($data['phone'])."'",
            'phone2' => empty($data['phone2'])? 'NULL':"'".trim($data['phone2'])."'",
            'email' => empty($data['email'])? 'NULL':"'".clean($data['email'])."'",
            'address' => empty($data['address'])? 'NULL':"'".clean($data['address'])."'"
        );



        if(empty($files['image']['type'])){
            $cleanData['image'] = empty($this->img)? "NULL":"'".$this->img."'";
        }else{
            $time = new DateTime();
            $imageName = $time->getTimestamp()."_".$files['image']['name'];
            $ruta = "../../Imagenes/Clientes/$imageName";
            
            
            move_uploaded_file($files['image']['tmp_name'], $ruta);

            
            if(!empty($this->img)){
                unlink("../../Imagenes/Clientes/$this->img");
            }

            $cleanData['image'] = "'$imageName'";
        }

        
                


        $sql = "UPDATE `contactos` SET
         `direccion`= ".$cleanData['address']."
        ,`telefono1`= ".$cleanData['phone']."
        ,`telefono2`= ".$cleanData['phone2']."
        ,`correo`= ".$cleanData['email']."
         WHERE `id` = $this->idContact";

        $this->db->ejecutar($sql);

        $sql = "UPDATE `clientes` SET
         `tipoDeDocumento`= ".$cleanData['docType']."
        ,`nombre`= ".$cleanData['name']."
        ,`ULRImagen`= ".$cleanData['image']."
         WHERE `rif` = '$this->id'";

        
        $this->db->ejecutar($sql);
        $Auditoria = new historial();
        $Auditoria->CrearNuevoRegistro(2, 3, $this->id, "Cliente #$this->id modificado");
    }
    
}



class publicFunctions extends conexion {
    public function getInventary($givenData){
        $resultMax = 20;
        $result = array();
        $step = 1;
        $isNextStepPossible = false;
        $filters = ['(`productos`.`idCategoria` = 1 OR `productos`.`idCategoria` = 2)'];

        if(!empty($givenData['step'])){
            $step = $givenData['step'];
        }

        $limit = "LIMIT ".(($step - 1) * $resultMax).",".($resultMax + 1);
        
        
        if(!empty($givenData['search'])){
            $filters[] = "(`productos`.`nombre` LIKE '%".$givenData['search']."%' OR `productos`.`id` LIKE '%".$givenData['search']."%' OR `productos`.`descripcion` LIKE '%".$givenData['search']."%')";
        }

        if(!empty($givenData['status'])){
            $filters[] = "(`productos`.`idEstado` = ".$givenData['status'].")";
        }

        $sql = "SELECT `productos`.*, `unidadesdemedida`.`nombre` AS 'unit' FROM `productos` INNER JOIN `unidadesdemedida` ON `productos`.`idUnidadDeMedida` = `unidadesdemedida`.`id` WHERE ".implode(' AND ', $filters)."ORDER BY id $limit";

        $search = $this->consultar($sql);
        if(!empty($search)){
            if(count($search) > $resultMax){
                array_pop($search);
                $isNextStepPossible = true;
            }


            foreach($search as $row){
                $existence = 0;

                $search2 = $this->consultar("SELECT * FROM `inventario` WHERE `idProducto` = ".$row['id']);
                if(!empty($search2)){
                    foreach($search2 as $row2){
                        $existence = $existence + $row2['existencia'];
                    }
                }

                $result[] = array(
                    'id' => $row['id'],
                    'img' => $row['ULRImagen'],
                    'name' => $row['nombre'],
                    'alertLevel' => $row['nivelDeAlerta'],
                    'idState' => $row['idEstado'],
                    'existence' => $existence,
                    'unit' => $row['unit'],
                );
            }
        }


        return array(
            'step' => $step,
            'result' => $result,
            'isNextStepPossible' => $isNextStepPossible
        );
    }

    public function checkProvidersImagesExistence(){
        $search = $this->consultar("SELECT * FROM `proveedores`");
        if(!empty($search)){
            foreach($search as $row){
                if($row['idEstado']==10 && !empty($row['ULRImagen'])){
                    unlink("../Imagenes/proveedores/".$row['ULRImagen']);;
                    $this->ejecutar("UPDATE `proveedores` SET `ULRImagen` = NULL WHERE `rif` = ".$row['rif']);
                }

                if(!empty($row['ULRImagen'])){
                    if(!file_exists("../Imagenes/proveedores/".$row['ULRImagen'])){
                        $this->ejecutar("UPDATE `proveedores` SET `ULRImagen` = NULL WHERE `rif` = ".$row['rif']);
                    }
                }
            }
        }
    }

    public function checkCustomersImagesExistence(){
        $search = $this->consultar("SELECT * FROM `clientes`");
        if(!empty($search)){
            foreach($search as $row){
                if($row['idEstado']==13 && !empty($row['ULRImagen'])){
                    unlink("../Imagenes/clientes/".$row['ULRImagen']);;
                    $this->ejecutar("UPDATE `clientes` SET `ULRImagen` = NULL WHERE `rif` = ".$row['rif']);
                }

                if(!empty($row['ULRImagen'])){
                    if(!file_exists("../Imagenes/clientes/".$row['ULRImagen'])){
                        $this->ejecutar("UPDATE `clientes` SET `ULRImagen` = NULL WHERE `rif` = ".$row['rif']);
                    }
                }
            }
        }
    }

    public function checkProductImagesExistence(){
        $search = $this->consultar("SELECT * FROM `productos`");
        if(!empty($search)){
            foreach($search as $row){
                if($row['idEstado']==4 && !empty($row['ULRImagen'])){
                    unlink("../Imagenes/Productos/".$row['ULRImagen']);;
                    $this->ejecutar("UPDATE `productos` SET `ULRImagen` = NULL WHERE `id` = ".$row['id']);
                }

                if(!empty($row['ULRImagen'])){
                    if(!file_exists("../Imagenes/Productos/".$row['ULRImagen'])){
                        $this->ejecutar("UPDATE `productos` SET `ULRImagen` = NULL WHERE `id` = ".$row['id']);
                    }
                }
            }
        }
    }

    public function getProductsToPurchase($givenData){
        $result = array();
        $sql_desc = '1=1';
        $sql_cat = '`idEstado` < 4';

        if(!empty($givenData['description'])){
            $sql_desc = "'%".$givenData['description']."%'";
            $sql_desc ="(`productos`.`id` LIKE $sql_desc OR `productos`.`nombre` LIKE $sql_desc)";
        }
        if(!empty($givenData['category']) && $givenData['category']>0){
            $sql_cat = "(`idEstado` = ".$givenData['category'].")";
        }

        $sql = "SELECT `productos`.*, `unidadesdemedida`.`nombre` AS 'unidad', `unidadesdemedida`.`simbolo` AS 'simbolo' FROM `productos` INNER JOIN `unidadesdemedida` ON `productos`.`idUnidadDeMedida` = `unidadesdemedida`.`id` 
        WHERE ((`idCategoria` = 1 OR `idCategoria` = 2) AND $sql_desc AND $sql_cat)";
        
        $search = $this->consultar($sql);
        if(!empty($search)){
            foreach($search as $row){
                $existence = 0;

                $search2 = $this->consultar("SELECT * FROM `inventario` WHERE `idProducto` = ".$row['id']);
                if(!empty($search2)){
                    foreach($search2 as $row2){
                        $existence = $existence + intval($row2['existencia']);
                    }
                }

                $ArrayDeProveedores = '';

                $ListaDeProveedores = $this->consultar("SELECT * FROM `productosdeproveedor` 
                INNER JOIN `proveedores` ON `productosdeproveedor`.`idProveedor` = `proveedores`.`rif` 
                WHERE ( `idProducto` = ".$row['id']." AND `proveedores`.`idEstado` = 7)");

                if(!empty($ListaDeProveedores)){
                    foreach($ListaDeProveedores as $RowProveedor){
                        $ArrayDeProveedores = $ArrayDeProveedores.((empty($ArrayDeProveedores))?$RowProveedor['idProveedor']:'x'.$RowProveedor['idProveedor']);
                    }
                }

                $result[] = array(
                    'id' => $row['id'],
                    'name' => $row['nombre'],
                    'img' => $row['ULRImagen'],
                    'statusID' => $row['idEstado'],
                    'unit' => $row['unidad'],
                    'symbol' => $row['simbolo'],
                    'alertLevel' => $row['nivelDeAlerta'],
                    'categoryID' => $row['idCategoria'],
                    'price' => number_format($row['precio'], 2, '.',''),
                    'existence' => $existence,
                    'listaDeProveedores' => $ArrayDeProveedores
                );
            }
        }

        return $result;
    }

    public function validateNewProvider($givenData){
        if(empty($givenData['docType'])){
            throw new Exception("No se especificó el tipo de documento");
        }else{
            if($givenData['docType']!='V' && $givenData['docType']!='J' && $givenData['docType']!='E' && $givenData['docType']!='G' && $givenData['docType']!='P'){
                throw new Exception("El tipo de documento no es válido");
            }
        }

        if(empty($givenData['cedula'])){
            throw new Exception("No se especificó la cédula");
        }else{
            if(strlen($givenData['cedula'])>9){
                throw new Exception("La cédula debe comprender una longitud máxima de 9 números");
            }

            if(!is_numeric($givenData['cedula'])){
                throw new Exception("El número cédula no es válido");
            }
        }

        if(strlen($givenData['name'])<5 || strlen($givenData['name'])>40){
            throw new Exception("El nombre debe comprender una longitud entre 5 y 40 caractéres");
        }


        if(empty($givenData['phone'])){
            //throw new Exception("No se especificó el télefono 1");;
        }else{
            if(strpos($givenData['phone'], '-') == 4){
                $pieces = explode('-', $givenData['phone']);
                if(count($pieces)!=2){
                    throw new Exception("El número de teléfono 1 no es válido");
                }else{
                    if(!is_numeric($pieces[0])){
                        throw new Exception("El código de operadora en el teléfono 1 no es válido");
                    }
                    if(!is_numeric($pieces[1])){
                        throw new Exception("El el teléfono 1 no es válido");
                    }
                }
            }else{
                if(strlen($givenData['phone'])<10 || strlen($givenData['phone'])>13){
                    throw new Exception("El formato del número de teléfono 1 como télefono internacional no es válido");
                }else{
                    if(!is_numeric($givenData['phone'])){
                        throw new Exception("El formato del número de teléfono 1 no es válido");
                    }
                }
            }
        }

        if(!empty($givenData['phone2'])){
            if(strpos($givenData['phone2'], '-') == 4){
                $pieces = explode('-', $givenData['phone2']);
                if(count($pieces)!=2){
                    throw new Exception("El número de teléfono 2 no es válido");
                }else{
                    if(!is_numeric($pieces[0])){
                        throw new Exception("El código de operadora en el teléfono 2 no es válido");
                    }
                    if(!is_numeric($pieces[1])){
                        throw new Exception("El el teléfono 2 no es válido");
                    }
                }
            }else{
                if(strlen($givenData['phone2'])<10 || strlen($givenData['phone2'])>13){
                    throw new Exception("El formato del número de teléfono 2 como télefono internacional no es válido");
                }else{
                    if(!is_numeric($givenData['phone2'])){
                        throw new Exception("El formato del número de teléfono 2 no es válido");
                    }
                }
            }
        }

        
        if(!empty($givenData['email'])){
            if(strlen($givenData['email'])<5 || strlen($givenData['email'])>40){
                throw new Exception("El correo debe comprender una longitud de entre 5 y 40 caractéres");
            }else{
                $arroba = strpos($givenData['email'], '@');
                $dot = strpos($givenData['email'], '.');

                if(empty($arroba) || empty($dot)){
                    throw new Exception("El formato del correo no es válido");
                }
            }
        }

        if(!empty($givenData['address'])){
            if(strlen($givenData['address'])>150){
                throw new Exception("La dirección debe comprender una longitud máxima de 150 caractéres");
            }
        }

        if(!empty($givenData['productsList'])){
            $products_array = explode('¿', $givenData['productsList']);
            foreach($products_array as $id){
                if(!is_numeric($id)){
                    throw new Exception("El ID de producto $id no es válido");
                }else{
                    if(empty($this->consultar("SELECT * FROM `productos` WHERE `id` = $id AND `idEstado` < 4"))){
                        throw new Exception("El producto $id no existe o no está disponible");
                    }
                }
            }
        }
    }

    public function getConsumableProducts($givenData){
        $result = array();
        $toSearch = htmlspecialchars(htmlentities(trim($givenData['value'])));
        if(empty($toSearch)){
            $toSearch = '1=1';
        }else{
            $toSearch = "(`nombre` LIKE '%$toSearch%' OR `id` LIKE '%$toSearch%' OR `descripcion` LIKE '%$toSearch%')";
        }

        $sql = "SELECT * FROM `productos` WHERE ((`idCategoria` = 1 OR `idCategoria` = 2) AND $toSearch)";
        $search = $this->consultar($sql);
        if(!empty($search)){
            foreach($search as $row){
                $result[] = array(
                    'id' => $row['id'],
                    'name' => $row['nombre'],
                    'img' => $row['ULRImagen'],
                );
            }
        }

        return $result;
    }

    public function validateNewProduct($givenData){
        if(strlen($givenData['name'])<5 || strlen($givenData['name'])>30){
            throw new Exception("El nombre debe comprender una longitud entre 5 y 30 caractéres");
        }

        if(empty($givenData['price'])){
            throw new Exception("El precio debe ser mayor a 0");
        }else{
            if(!is_numeric($givenData['price'])){
                throw new Exception("El precio no es válido");
            }
        }

        if($givenData['idCategory']!=1 && $givenData['idCategory']!=2 && $givenData['idCategory']!=3 && $givenData['idCategory']!=4){
            throw new Exception("La categoría del producto no es válida");
        }

        if(empty($givenData['idUnit'])){
            //throw new Exception("No se especificó la unidad de medida");
        }else{
            if(!is_numeric($givenData['idUnit'])){
                throw new Exception("La unidad de medida no es válida");
            }else{
                if(empty($this->consultar("SELECT * FROM `unidadesdemedida` WHERE `id` = ".$givenData['idUnit']))){
                    throw new Exception("La unidad de medida no existe");   
                }
            }
        }

        if($givenData['idCategory']<3){
            if(empty($givenData['alertLevel'])){
                throw new Exception("El nivel de alerta debe ser mayor a 0");
            }else{
                if(!is_numeric($givenData['alertLevel'])){
                    throw new Exception("El nivel de alerta no es válido");
                }else{
                    if($givenData['alertLevel']<=0){
                        throw new Exception("El nivel de alerta debe ser mayor a 0");
                    }
                }
            }
        }

        if($givenData['idCategory']==2){
            if(empty($givenData['deafultSpoilage']) || $givenData['deafultSpoilage'] <= 0){
                throw new Exception("La depreciación estándar debe ser mayor a 0 ");
            }
        }


        if(!empty($givenData['description'])){
            if(strlen(trim($givenData['description'])) > 150){
                throw new Exception("La descripción debe contar con un máximo de 150 caractéres");
            }
        }


        if(!empty($givenData['providers'])){
            $providers_array = explode('¿', $givenData['providers']);
            foreach($providers_array as $id){
                if(!is_numeric($id)){throw new Exception("El proveedor $id no es válido");}

                $search = $this->consultar("SELECT * FROM `proveedores` WHERE `rif` = '$id'");
                if(empty($search)){
                    throw new Exception("El proveedor $id no existe");
                }
                if($search[0]['idEstado'] != 7){
                    throw new Exception("El proveedor $id no está disponible");
                }
            }
        }
    }

    public function getManualHelp($givenData){
        $user = new user($givenData['user']);
        $modulesID = $user->getModulesID();
        $value = trim($givenData['search']);
        $result = array();
        $idMainTopics = array();

        $search = $this->consultar("SELECT * FROM `manual` WHERE (`titulo` LIKE '$value%' AND `indice` = 0)");
        if(!empty($search)){
            foreach($search as $row){
                $result[] = array(
                    'id' => $row['id'],
                    'name' => $row['titulo'],
                    'unlock' => in_array($row['idModulo'], $modulesID)
                );

                $idMainTopics[] = $row['id'];
            }
        }

        
        $keywordsID = array();
        foreach(explode(' ', $value) as $word){
            if($word != ''){
                $search = $this->consultar("SELECT * FROM `etiqueta` WHERE `nombre` = '$word'");
                if(!empty($search)){
                    $keywordsID[] = $search[0]['id'];
                }
            }
        }
        
        $sql = '';
        if(count($keywordsID) > 1){
            foreach($keywordsID as $id){
                $sql.= (empty($sql)? '':' OR ')."`idEtiqueta` = $id";
            }
            
            $search = $this->consultar("SELECT `idManual`, COUNT(`idManual`) as 'nroPalabras' FROM `etiquetasdepagina` WHERE ($sql) GROUP by `idManual`;");
            if(!empty($search)){
                $idExtraTopics = array();
                foreach($search as $row){
                    if($row['nroPalabras']>1){
                        $idExtraTopics[] = $row['idManual'];
                    }
                }

                

                if(!empty($idExtraTopics)){
                    
                    if(empty($idMainTopics)){
                        $idNewExtraTopics = $idExtraTopics;
                    }else{
                        $idNewExtraTopics = array();
                        foreach($idExtraTopics as $idExtra){
                            if(!in_array($idExtra, $idMainTopics)){
                                $idNewExtraTopics[] = $idExtra;
                            }
                        }
                    }

                    
                    if(!empty($idNewExtraTopics)){
                        $sql = '';
                        foreach($idNewExtraTopics as $idNew){
                            $sql.= (empty($sql)? '': ' OR ')."`id` = $idNew";
                        }
                        
                        $search = $this->consultar("SELECT * FROM `manual` WHERE ($sql);");
                        if(!empty($search)){
                            foreach($search as $row){
                                $result[] = array(
                                    'id' => $row['id'],
                                    'name' => $row['titulo'],
                                    'unlock' => in_array($row['idModulo'], $modulesID)
                                );                
                            }
                        }
                    }

                }
            }
        }

        return $result;
    }

    public function getSubtitlesFor($idModulo){
        $result = array();
        $search = $this->consultar("SELECT * FROM `manual` WHERE (`idModulo` = $idModulo AND `indice` = 1)");
        if(!empty($search)){
            foreach($search as $row){
                $result[] = array(
                    'id' => $row['id'],
                    'name' => $row['titulo']
                );
            }
        }
        return $result;
    }

    public function getManualIndexTitles(){
        return array(
            array('id' => 5, 'name' => 'Productos'),
            array('id' => 1, 'name' => 'Almacenes'),
            array('id' => 4, 'name' => 'Inventario'),
            array('id' => 6, 'name' => 'Compras'),
            array('id' => 7, 'name' => 'Proveedores'),
            array('id' => 2, 'name' => 'Ventas'),
            array('id' => 3, 'name' => 'Clientes'),
            array('id' => 8, 'name' => 'Auditoría'),
            array('id' => 9, 'name' => 'Usuarios'),
            array('id' => 10,'name' => 'Sistema')
        );
    }

    public function lookForProviders($givenData){
        $result = array();

        if(empty($givenData['value'])){
            throw new Exception("No indicó ningún producto");
        }

        foreach(explode('¿', $givenData['value']) as $id){
            $product = new product($id);
            $providersID = $product->getProvidersID();
            if(!empty($providersID)){
                
                foreach(explode('¿', $providersID) as $idProvider){
                    
                    
                    if(isset($result[$idProvider])){
                        array_push($result[$idProvider], $id);
                    }else{
                        $result[$idProvider] = array($id);
                    }
                    
                }
            }
        }

        
        return $result;
    }

    public function checkBudgetsAndPurchase(){
        $search = $this->consultar("SELECT * FROM `cotizaciones` WHERE `idEstado` = 33 AND `fechaExpiracion` IS NOT NULL;");
        if(!empty($search)){
            foreach($search as $row){
                if(strtotime($row['fechaExpiracion']) - strtotime("now") < 0){
                    $budget = new budget($row['id']);
                    $budget->setExpired();
                }
            }
        }

        $search = $this->consultar("SELECT * FROM `ordenesdecompra` WHERE `idEstado` = 63 AND `fechaExpiracion` IS NOT NULL");
        if(!empty($search)){
            foreach($search as $row){
                if(strtotime($row['fechaExpiracion']) - strtotime("now") < 0){
                    $purchase = new purchase($row['id']);
                    $purchase->setExpired();
                }
            }
        }
    }

    public function setNationalCurrency_simbol($givenData){
        if(empty($givenData['value'])){
            throw new Exception("No se especificó el nuevo nombre");
        }else{
            if(strlen($givenData['value']) > 5){
                throw new Exception("El valor debe comprender una longitud máxima de 5 caractéres");
            }
        }

        $value = htmlentities(trim($givenData['value']));
        $this->ejecutar("UPDATE `sistema` SET `valor` = '$value' WHERE `descripcion` = 'SimboloMonedaNacional'");
    }

    public function setNationalCurrency_name($givenData){
        if(empty($givenData['value'])){
            throw new Exception("No se especificó el nuevo nombre");
        }else{
            if(strlen($givenData['value']) > 20){
                throw new Exception("El valor debe comprender una longitud máxima de 20 caractéres");
            }
        }

        $value = htmlentities(trim($givenData['value']));
        $this->ejecutar("UPDATE `sistema` SET `valor` = '$value' WHERE `descripcion` = 'NombreMonedaNacional'");
    }

    public function updateQuestion($givenData){
        if(empty($givenData['id'])){
            throw new Exception("No se especificó el ID de la pregunta");
        }else{
            if(!is_numeric($givenData['id'])){
                throw new Exception("El ID de la pregunta no es válido");
            }else{
                $search = $this->consultar("SELECT * FROM `preguntas` WHERE `id` = ".$givenData['id']);
                if(empty($search)){
                    throw new Exception("La pregunta de seguridad no existe");
                }
            }
        }


        if(empty($givenData['value'])){
            throw new Exception('No se especificó la pregunta');
        }else{
            if(strlen($givenData['value']) >25){
                throw new Exception('La pregunta debe comprender una longitud máxima de 25 caractéres');
            }
        }


        $idQ = $givenData['id'];
        $search = $this->consultar("SELECT * FROM `usuarios` WHERE (`idPregunta1` = $idQ OR `idPregunta2` = $idQ OR `idPregunta3` = $idQ)");
        if(!empty($search)){
            throw new Exception("Esta pregunta está siendo usadada por uno o más usuarios");
        }


        $value = htmlentities(trim($givenData['value']));
        $this->ejecutar("UPDATE `preguntas` SET `pregunta` = '$value' WHERE `id` = $idQ");
    }

    public function updateUnit($givenData){
        if(empty($givenData['idUnit'])){
            throw new Exception("No se especificó el ID de la unidad");
        }else{
            if(!is_numeric($givenData['idUnit'])){
                throw new Exception("El ID de la unidad no es válido");
            }else{
                $search = $this->consultar("SELECT * FROM `unidadesdemedida` WHERE `id` = ".$givenData['idUnit']);
                if(empty($search)){
                    throw new Exception("La unidad de medida no existe");
                }
            }
        }


        if(empty($givenData['name'])){
            throw new Exception('No se especificó el nombre de la unidad');
        }else{
            if(strlen($givenData['name']) >15){
                throw new Exception('El nombre de la unidad debe comprender una longitud máxima de 15 caractéres');
            }
        }


        if(empty($givenData['simbol'])){
            throw new Exception('No se especificó el símbolo de la unidad');
        }else{
            if(strlen($givenData['simbol']) >5){
                throw new Exception('El símbolo de la unidad debe comprender una longitud máxima de 15 caractéres');
            }
        }

        $idU = $givenData['idUnit'];
        $name = htmlentities(trim($givenData['name']));
        $simbolo = htmlentities(trim($givenData['simbol']));
        $this->ejecutar("UPDATE `unidadesdemedida` SET `nombre` = '$name', `simbolo` = '$simbolo' WHERE `id` = $idU");
    }

    public function deleteUnit($givenData){
        if(empty($givenData['id'])){
            throw new Exception("No es posible eliminar en este momento, intentélo más tarde");
        }else{
            if(!is_numeric($givenData['id'])){
                throw new Exception("El ID de la unidad no es válido");
            }
        }

        $idU = $givenData['id'];

        $search = $this->consultar("SELECT * FROM `unidadesdemedida`");
        if(count($search) <= 5){
            throw new Exception("No es posible eliminar más unidades de medición");
        }

        $search = $this->consultar("SELECT * FROM `productos` WHERE `idUnidadDeMedida` = $idU");
        if(!empty($search)){
            throw new Exception("Esta unidad de medida está siendo usada por uno o más productos");
        }


        $this->ejecutar("DELETE FROM `unidadesdemedida` WHERE `id` = $idU");
    }

    public function deleteQuestion($givenData){
        if(empty($givenData['id'])){
            throw new Exception("No es posible eliminar en este momento, intentélo más tarde");
        }else{
            if(!is_numeric($givenData['id'])){
                throw new Exception("El ID de la pregunta no es válido");
            }
        }


        $idQ = $givenData['id'];
        $search = $this->consultar("SELECT * FROM `usuarios` WHERE (`idPregunta1` = $idQ OR `idPregunta2` = $idQ OR `idPregunta3` = $idQ)");
        if(!empty($search)){
            throw new Exception("Esta pregunta está siendo usadada por uno o más usuarios");
        }

        $search = $this->consultar("SELECT * FROM `preguntas`");
        if(count($search) <= 3){
            throw new Exception("No es posible eliminar más preguntas de seguridad");
        }

        $this->ejecutar("DELETE FROM `preguntas` WHERE `id` = $idQ");
    }

    public function addNewUnit($givenData){
        if(empty($givenData['name'])){
            throw new Exception('No se especificó el nombre de la unidad');
        }else{
            if(strlen($givenData['name']) >15){
                throw new Exception('El nombre de la unidad debe comprender una longitud máxima de 15 caractéres');
            }
        }

        
        if(empty($givenData['simbol'])){
            throw new Exception('No se especificó el símbolo de la unidad');
        }else{
            if(strlen($givenData['simbol']) >5){
                throw new Exception('El símbolo de la unidad debe comprender una longitud máxima de 15 caractéres');
            }
        }


        foreach($this->getUnits() as $row){
            if(strtolower(trim($row['nombre'])) == strtolower(trim($givenData['name']))){
                throw new Exception('Ya existe una unidad con este nombre');
            }

            if(strtolower(trim($row['simbolo'])) == strtolower(trim($givenData['simbol']))){
                throw new Exception('Ya existe una unidad con este símbolo');
            }
        }

        
        $name = htmlentities(trim($givenData['name']));
        $simbolo = htmlentities(trim($givenData['simbol']));
        $this->ejecutar("INSERT INTO `unidadesdemedida`(`nombre`, `simbolo`) VALUES ('$name', '$simbolo')");
    }


    public function addNewQuestion($givenData){
        
        if(empty($givenData['value'])){
            throw new Exception('No se especificó la pregunta');
        }else{
            if(strlen($givenData['value']) >25){
                throw new Exception('La pregunta debe comprender una longitud máxima de 25 caractéres');
            }
        }

        foreach($this->getSecurityQuestions() as $row){
            if(strtolower(trim($row['pregunta'])) == strtolower(trim($givenData['value']))){
                throw new Exception('La pregunta ya existe');
            }
        }

        $value = htmlentities(trim($givenData['value']));
        $this->ejecutar("INSERT INTO `preguntas`(`pregunta`) VALUES ('$value')");
    }

    
    public function getUnits(){
        return $this->consultar("SELECT * FROM `unidadesdemedida` WHERE `id` > 2");
    }
    public function getSecurityQuestions(){
        return $this->consultar("SELECT * FROM `preguntas`");
    }

    public function getProvider($givenData){
        if(empty($givenData['id'])){
            throw new Exception("No se especificó el ID");
        }

        $provider = new provider($givenData['id']);


        return array(
            'id' => $provider->getId(),
            'name' => $provider->getName(),
            'docType' => $provider->getDocType(),
            'img' => $provider->getImg()
        );
    }

    public function getClient($givenData){
        $customer = new customer($givenData['id']);

        return array(
            'docType' => $customer->getDocType(),
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'phone' => $customer->getPhoneData(),
            'email' => $customer->getEmail(),
            'address' => $customer->getAddress(),
            'img' => $customer->getImg()
        );
    }

    public function getProduct($givenData){
        if(empty($givenData['id'])){
            throw new Exception("No se especificó el ID");
        }

        $product = new product($givenData['id']);

        return array(
            'id' => $product->getId(),
            'name' => $product->getName(),
            'img' => $product->getImage(),
            'price' => $product->getPrice(),
            'unit' => $product->getUnitSimbol(),
            'unitName' => $product->getUnitName(),
            'defaultSpoilage' => $product->getDefaultSpoilage(),
            'idCategory' => $product->getIdCategory(),
            'idStatus' => $product->getIdStatus(),
            'defaultSpoilage' => $product->getDefaultSpoilage(),
        );
    }

    public function getResultsProductsSearch($givenData){
        $result = array();
        $categorySQL = '1 = 1';
        if(!empty($givenData['category'])){
            if(!is_numeric($givenData['category'])){
                throw new Exception('La categoría no es válida');
            }else{
                if($givenData['category'] > 0 && $givenData['category'] < 5){
                    $categorySQL = "`idCategoria` = ".$givenData['category'];
                }else{
                    throw new Exception('La categoría no existe');
                }
            }
        }

        $valueToSearch = str_replace("'", '', trim($givenData['value']));

        $search = $this->consultar("SELECT * FROM `productos` WHERE (`idEstado` < 4 AND (`nombre` LIKE '%$valueToSearch%' OR `id` = '%$valueToSearch%') AND $categorySQL)");
        if(!empty($search)){
            foreach($search as $row){
                $depreciacion = 1;
                if($row['idCategoria'] == 2){
                    $search2 = $this->consultar("SELECT * FROM `depreciacion` WHERE `idProducto` = ".$row['id']);
                    if(!empty($search2)){
                        $depreciacion = $search2[0]['valor'];
                    }
                }

                $result[] = array(
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'descripcion' => $row['descripcion'],
                    'precio' => $row['precio'],
                    'idCategoria' => $row['idCategoria'],
                    'nivelDeAlerta' => $row['nivelDeAlerta'],
                    'idUnidadDeMedida' => $row['idUnidadDeMedida'],
                    'idEstado' => $row['idEstado'],
                    'ULRImagen' => $row['ULRImagen'],
                    'depreciacion' => $depreciacion
                );
            }
        }
        return $result;
    }

    public function getResultsProductsOnStockSearch($givenData){
        $result = array();
        $valueToSearch = str_replace("'", '', trim($givenData['value']));
        $search = $this->consultar("SELECT * FROM `productos` WHERE `idEstado` < 4 AND `idCategoria` < 3 AND (`id` LIKE '%$valueToSearch%' OR `nombre` LIKE '%$valueToSearch%')");

        if(!empty($search)){
            foreach($search as $row){
                $product = new product($row['id']);

                $result[] = array(
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'img' => $product->getImage(),
                    'idStatus' => $product->getIdStatus(),
                    'stockExistence' => $product->getStockExistence(),
                    'alertLvl' => $product->getAlertLevel(),
                    'unitName' => $product->getUnitName(),
                );
            }
        }

        return $result;
    }

    public function getResultsClientsSearch($givenData){
        if(empty($givenData['value'])){
            $search = $this->consultar("SELECT * FROM `clientes` WHERE `idEstado` = 11");
        }else{
            $value = str_replace("'", '', trim($givenData['value']));
            $search = $this->consultar("SELECT * FROM `clientes` WHERE `idEstado` = 11 AND (`rif` LIKE '%$value%' OR `nombre` LIKE '%$value%')");
        }
        
        return $search;
    }

    public function getProducts($givenData){
        $sqlName = '1=1';
        if(!empty($givenData['value'])){
            $cleanValue = str_replace("'", '', $givenData['value']);
            $sqlName = "`id` LIKE '$cleanValue%' OR `nombre` LIKE '%$cleanValue%'";
        }
        
        return $this->consultar("SELECT * FROM `productos` WHERE (($sqlName) AND `idEstado` < 4)");
    }

    public function getProviders($givenData){
        $sqlName = '1=1';
        if(!empty($givenData['value'])){
            $cleanValue = str_replace("'", '', $givenData['value']);
            $sqlName = "`rif` LIKE '$cleanValue%' OR `nombre` LIKE '%$cleanValue%'";
        }
        
        return $this->consultar("SELECT * FROM `proveedores` WHERE (($sqlName) AND `idEstado` = 7)");
    }
    

    public function getCompanyLogo(){
        $search = $this->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'LogoDeLaEmpresa'");
        return $search[0]['valor'];
    }
    public function getCompanySeal(){
        $search = $this->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'Sello'");
        return $search[0]['valor'];
    }
    public function getCompanyBossSing(){
        $search = $this->consultar("SELECT * FROM `sistema` WHERE `descripcion` = 'FirmaDelBoss'");
        return $search[0]['valor'];
    }

    
    public function setCompanyLogo($newName){
        unlink("../Imagenes/Sistema/".$this->getCompanyLogo());
        $this->ejecutar("UPDATE `sistema` SET `valor` = '$newName' WHERE `descripcion` = 'LogoDeLaEmpresa'");
    }
    public function setCompanySeal($newName){
        unlink("../Imagenes/Sistema/".$this->getCompanySeal());
        $this->ejecutar("UPDATE `sistema` SET `valor` = '$newName' WHERE `descripcion` = 'Sello'");
    }
    public function setCompanyBossSing($newName){
        unlink("../Imagenes/Sistema/".$this->getCompanyBossSing());
        $this->ejecutar("UPDATE `sistema` SET `valor` = '$newName' WHERE `descripcion` = 'FirmaDelBoss'");
    }
    

    function __construct(){
        try{
            $this->conexion = new PDO("mysql:host=$this->servidor;dbname=CLEODataBase", $this->usuario, $this->contrasenia);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $error){
            return "Error de conexion: ".$error;
        }

        $search = $this->consultar("SELECT * FROM `sistema`");
        foreach($search as $row){
            if($row['descripcion'] == 'RifDeEmpresa'){
                $this->company_rif = $row['valor'];
            }

            if($row['descripcion'] == 'NombreDeEmpresa'){
                $this->company_name = $row['valor'];
            }

            if($row['descripcion'] == 'DireccionDeEmpresa'){
                $this->company_address = $row['valor'];
            }

            if($row['descripcion'] == 'CiudadEstadoYZonaPostalDeEmpre'){
                $this->company_cityData = $row['valor'];
            }

            if($row['descripcion'] == 'TelefonoDeEmpresa'){
                $this->company_phone = $row['valor'];
            }

            if($row['descripcion'] == 'CorreoDeEmpresa'){
                $this->company_email = $row['valor'];
            }

            if($row['descripcion'] == 'NombreMonedaNacional'){
                $this->nationalCurrency_name = $row['valor'];
            }

            if($row['descripcion'] == 'SimboloMonedaNacional'){
                $this->nationalCurrency_simbol = $row['valor'];
            }
            
        }
    }

    public function obtenerPreguntasDeSeguridad($entradas){
        if(empty($entradas['usuario'])){
            throw new Exception("No se especificó el usuario");
        }
        
        $search = $this->consultar("SELECT * FROM `usuarios` WHERE `nombreDeUsuario` = '".$entradas['usuario']."';");
        if(empty($search)){
            throw new Exception("El nombre de usuario no existe");
        }else{
            $search = $search[0];
            $result = array();

            $search2 = $this->consultar("SELECT * FROM `preguntas` WHERE (`id` = ".$search['idPregunta1'].")");
            foreach($search2 as $row){
                $result[] = $row['pregunta'];
            }

            $search2 = $this->consultar("SELECT * FROM `preguntas` WHERE (`id` = ".$search['idPregunta2'].")");
            foreach($search2 as $row){
                $result[] = $row['pregunta'];
            }

            $search2 = $this->consultar("SELECT * FROM `preguntas` WHERE (`id` = ".$search['idPregunta3'].")");
            foreach($search2 as $row){
                $result[] = $row['pregunta'];
            }

            return $result;
        }
    }

    public function comprobarRespuestas($entradas){
        if(empty($entradas['usuario'])){
            throw new Exception("No se especificó el usuario");
        }
        $search = $this->consultar("SELECT * FROM `usuarios` WHERE `nombreDeUsuario` = '".$entradas['usuario']."';");
        if(empty($search)){
            throw new Exception("El nombre de usuario no existe");
        }else{
            $search = $search[0];
        }

        if(empty($entradas['respuesta1'])){
            throw new Exception("No se especificó la respuesta #1");
        }
        if(empty($entradas['respuesta2'])){
            throw new Exception("No se especificó la respuesta #2");
        }
        if(empty($entradas['respuesta3'])){
            throw new Exception("No se especificó la respuesta #3");
        }
        
        $errores = 0;

        
        
        if(!password_verify(strtolower($entradas['respuesta1']), $search['respuesta1'])){
            $errores++;
        }
        if(!password_verify(strtolower($entradas['respuesta2']), $search['respuesta2'])){
            $errores++;
        }
        if(!password_verify(strtolower($entradas['respuesta3']), $search['respuesta3'])){
            $errores++;
        }

        
        
        if($errores > 0){
            
            if($errores==1){
                throw new Exception("Una respuesta es incorrecta");
            }else{
                throw new Exception("$errores respuestas son incorrectas");
            }   
        }
    }

    public function restablecerContrasenia($entradas){
        if(empty($entradas['usuario'])){
            throw new Exception("No se especificó el usuario");
        }
        $search = $this->consultar("SELECT * FROM `usuarios` WHERE `nombreDeUsuario` = '".$entradas['usuario']."';");
        if(empty($search)){
            throw new Exception("El nombre de usuario no existe");
        }else{
            $search = $search[0];
        }

        if(empty($entradas['respuesta1'])){
            throw new Exception("No se especificó la respuesta #1");
        }
        if(empty($entradas['respuesta2'])){
            throw new Exception("No se especificó la respuesta #2");
        }
        if(empty($entradas['respuesta3'])){
            throw new Exception("No se especificó la respuesta #3");
        }

        

        
        $errores=0;
        if(!password_verify(strtolower($entradas['respuesta1']), $search['respuesta1'])){
            $errores++;
        }
        if(!password_verify(strtolower($entradas['respuesta2']), $search['respuesta2'])){
            $errores++;
        }
        if(!password_verify(strtolower($entradas['respuesta3']), $search['respuesta3'])){
            $errores++;
        }

        if($errores > 0){
            if($errores==1){
                throw new Exception("Una respuesta es incorrecta");
            }else{
                throw new Exception("$errores respuestas son incorrectas");
            }   
        }


        if(empty($entradas['contrasenia'])){
            throw new Exception("No se especificó la nueva contraseña");
        }else{
            if(strlen($entradas['contrasenia']) < 8 || strlen($entradas['contrasenia']) > 20){
                throw new Exception("La contraseña debe comprender una longitud de entre 8 y 20 caractéres");
            }
        }

        
        
        $newContra = password_hash(strtolower($entradas['contrasenia']), PASSWORD_DEFAULT, ['cost' => 10]);
        
        
        $this->ejecutar("UPDATE `usuarios` SET `contrasenia` = '$newContra' WHERE `nombreDeUsuario` = '".$entradas['usuario']."';");

        $user = new usuario($entradas['usuario']);
        $user->IniciarSesion($entradas['contrasenia']);
    }


    public function getCompany_rif(){
        return $this->company_rif;
    }
    public function getCompany_name(){
        return $this->company_name;
    }
    public function getCompany_address(){
        return $this->company_address;
    }
    public function getCompany_cityData(){
        return $this->company_cityData;
    }
    public function getCompany_phone(){
        return $this->company_phone;
    }
    public function getCompany_email(){
        return $this->company_email;
    }
    public function getNationalCurrency_name(){
        return $this->nationalCurrency_name;
    }
    public function getNationalCurrency_simbol(){
        return $this->nationalCurrency_simbol;
    }

    public function setCompany_rif($givenData){
        if(strlen($givenData['value'])<9 || strlen($givenData['value'])>12){
            throw new Exception("El valor debe comprender una longitud de entre 9 y 12 caractéres");
        }
        
        $this->ejecutar("UPDATE `sistema` SET `valor` = '".$givenData['value']."' WHERE `descripcion` = 'RifDeEmpresa';");
        return true;
    }

    public function setCompany_name($givenData){
        if(strlen($givenData['value'])<3 || strlen($givenData['value'])>80){
            throw new Exception("El valor debe comprender una longitud de entre 3 y 80 caractéres");
        }
        
        $this->ejecutar("UPDATE `sistema` SET `valor` = '".$givenData['value']."' WHERE `descripcion` = 'NombreDeEmpresa';");
        return true;
    }

    public function setCompany_address($givenData){
        if(strlen($givenData['value'])<3 || strlen($givenData['value'])>80){
            throw new Exception("El valor debe comprender una longitud de entre 3 y 80 caractéres");
        }
        
        $this->ejecutar("UPDATE `sistema` SET `valor` = '".$givenData['value']."' WHERE `descripcion` = 'DireccionDeEmpresa';");
        return true;
    }

    public function setCompany_cityData($givenData){
        if(strlen($givenData['value'])<3 || strlen($givenData['value'])>80){
            throw new Exception("El valor debe comprender una longitud de entre 3 y 80 caractéres");
        }
        
        $this->ejecutar("UPDATE `sistema` SET `valor` = '".$givenData['value']."' WHERE `descripcion` = 'CiudadEstadoYZonaPostalDeEmpre';");
        return true;
    }

    public function setCompany_phone($givenData){
        if(strlen($givenData['value'])<3 || strlen($givenData['value'])>80){
            throw new Exception("El valor debe comprender una longitud de entre 3 y 80 caractéres");
        }
        
        $this->ejecutar("UPDATE `sistema` SET `valor` = '".$givenData['value']."' WHERE `descripcion` = 'TelefonoDeEmpresa';");
        return true;
    }

    public function setCompany_email($givenData){
        if(strlen($givenData['value'])<3 || strlen($givenData['value'])>80){
            throw new Exception("El valor debe comprender una longitud de entre 3 y 80 caractéres");
        }
        
        $this->ejecutar("UPDATE `sistema` SET `valor` = '".$givenData['value']."' WHERE `descripcion` = 'CorreoDeEmpresa';");
        return true;
    }
}

?>