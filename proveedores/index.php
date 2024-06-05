<?php

include('../Otros/clases.php');

    
   
    $SQLAConsultar = "SELECT * FROM `proveedores`";

    $valorDeEstado="";
    $valorDeOrden="";
    $valordeDescripcion="";

    
        $valordeDescripcion = (empty($_GET['descripcion'])? '':$_GET['descripcion']);
        $valorDeEstado   = (empty($_GET['estado'])? '0':$_GET['estado']);
        $valorDeOrden    = (empty($_GET['orden'])? 'nombre':$_GET['orden']);
        
        $SQLDeEstado= " `idEstado` = 7 ";
        $SQLDeDescripcion = (empty($valordeDescripcion)?"":"(`rif` LIKE '%".$valordeDescripcion."%' OR `nombre` LIKE '%".$valordeDescripcion."%')");


        if(empty($SQLDeEstado)||empty($SQLDeDescripcion)){
            $FiltroDeDescripcionYEstado = $SQLDeEstado.$SQLDeDescripcion;
        }else{
            $FiltroDeDescripcionYEstado = $SQLDeEstado." AND ".$SQLDeDescripcion;
        }
        
        $Filtros = (empty($FiltroDeDescripcionYEstado))?"":" WHERE (".$FiltroDeDescripcionYEstado.")";
        
        $SQLDeOrden=" ORDER BY `".$valorDeOrden."`";

        $ConsultaSegunBuscador="SELECT * FROM `proveedores`".$Filtros.$SQLDeOrden;
    

    
    $public = new publicFunctions();
    print_r($public->checkProvidersImagesExistence());
?>

<!DOCTYPE html>

<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Proveedores</title>
    <link rel="stylesheet" href="estilos_proveedores.css">

    <?php include('../Otros/cabecera_N2.php');?>
    <div id="CajaDeBarras">
        <a href="../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="" class="Barra">
            <p>Proveedores</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <div id="CajaContenido">
        
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> BUSCAR PROVEEDOR:</span>
        <form id="FormularioBuscador" action="" method="get" autocomplete="off" id="buscador">
            <input class="inputBuscador" type="search" name="descripcion" value="<?php echo $valordeDescripcion;?>" autofocus placeholder="Busca por RIF o razón social...">
            <button type="submit" class="BotonBuscador"><i class="fi-rr-search"></i></button>
            <select id="SelectorDeOrden" name="orden" class="SelectBuscador">
                <option value="nombre" <?php echo ($valorDeOrden=="nombre")?'selected=true':'';?>>Nombre</option>
                <option value="rif" <?php echo ($valorDeOrden=="cedula")?'selected=true':'';?>>RIF</option>
                <option value="idEstado" <?php echo ($valorDeOrden=="idEstado")?'selected=true':'';?>>Estado</option>
            </select>
        </form>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">Imagen</celda>
                <celda class="ColumnaRIF">RIF</celda>
                <celda class="ColumnaNombre">Razón Social</celda>
                <celda class="ColumnaProductos">Productos</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                $ListaDeProveedores = $BaseDeDatos->consultar($ConsultaSegunBuscador);

                if(empty($ListaDeProveedores)){
                    echo '
                    <row class="RowVacio">
                        <span>No hay proveedores a mostrar</span>
                    </row>
                    ';
                }else{
                    foreach($ListaDeProveedores as $row){
                        $ProveedorAMostrar = new proveedor($row['rif']);
                        $DatosDelProveedor = $ProveedorAMostrar->ObtenerDatos();
                        
                        echo '
                        <row>
                            <celda class="ColumnaID"><img src="../Imagenes/Proveedores/'.(empty($row['ULRImagen'])? 'ImagenPredefinida_Proveedores.png':$row['ULRImagen']).'"></celda>
                            <celda class="ColumnaRIF">'.$row['tipoDeDocumento'].'-'.$row['rif'].'</celda>
                            <celda class="ColumnaNombre">'.$row['nombre'].'</celda>
                            <celda class="ColumnaProductos">'.$DatosDelProveedor['numeroDeProductos'].'</celda>
                            <celda class="ColumnaDetalles"><a class="hovershadow" href="Proveedor/?rif='.$row['rif'].'">Ver más</a></celda>
                        </row>
                        ';
                    }
                }
                ?>
            </div>
        </div>
        
    </div>
    <div id="BarraLateral">
        <div id="contenidoDeLaBarraLateral">
            <div id="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Proveedores.png" alt="">
                <b>Proveedores</b>
            </div>
            <a id="AgregarNuevo" href="Nuevo"> <i class="fi-rr-add"></i> Nuevo Proveedor</a>
        </div>
    </div>
    <?php include '../ipserver.php';?>
<script src="proveedores.js"></script>

</body>
</html>