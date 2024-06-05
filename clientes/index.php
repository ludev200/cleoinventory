<?php
include('../Otros/clases.php');
    if(!isset($_GET['descripcion'])||!isset($_GET['orden'])){
        header('Location: ?descripcion=&orden=nombre');
    }
   
    $SQLAConsultar = "SELECT * FROM `clientes`";

    $valorDeOrden="";
    $valordeDescripcion="todavia";

    if($_GET){
        $valordeDescripcion = $_GET['descripcion'];
        $valorDeOrden    = $_GET['orden'];
        
        $SQLDeDescripcion = (empty($valordeDescripcion)?"":"(`rif` LIKE '%".$valordeDescripcion."%' OR `nombre` LIKE '%".$valordeDescripcion."%')");
        $SQLDeEstado = "`idEstado` != 12";
        $Filtros = (empty($SQLDeDescripcion))?"WHERE `idEstado` = 11":" WHERE (`idEstado` = 11 AND ".$SQLDeDescripcion.")";
        
        $SQLDeOrden=" ORDER BY `".$valorDeOrden."`";

        $ConsultaSegunBuscador="SELECT * FROM `clientes`".$Filtros.$SQLDeOrden;
    }

    
    $public = new publicFunctions();
    $public->checkCustomersImagesExistence();
?>

<!DOCTYPE html>

<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Clientes</title>
    <link rel="stylesheet" href="estilos_clientes.css">

    <?php include('../Otros/cabecera_N2.php');?>
    <div id="CajaDeBarras">
        <a href="../index.php" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="" class="Barra">
            <p>Clientes</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article id="CajaContenido">
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> BUSCAR CLIENTE:</span>
        <form id="FormularioBuscador" action="" method="get" autocomplete="off" id="buscador">
            <input class="inputBuscador" type="search" name="descripcion" value="<?php echo $valordeDescripcion;?>" autofocus placeholder="Busca por RIF o razón social...">
            <button class="BotonBuscador" type="submit"><i class="fi-rr-search"></i></button>
            <select class="SelectBuscador" id="SelectorDeOrden" name="orden" id="SelectorDeOrden" title="Ordernar por">
                <option value="nombre" <?php echo ($valorDeOrden=="nombre")?'selected=true':'';?>>Nombre</option>
                <option value="rif" <?php echo ($valorDeOrden=="rif")?'selected=true':'';?>>RIF</option>
            </select>
        </form>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">Imagen</celda>
                <celda class="ColumnaRIF">RIF</celda>
                <celda class="ColumnaNombre">Razón Social</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla">
                <?php
                $ListaDeClientes = $BaseDeDatos->consultar($ConsultaSegunBuscador);
                if(empty($ListaDeClientes)){
                    echo '
                    <row class="RowVacio">
                        <span>No hay clientes a mostrar</span>
                    </row>
                    ';
                }else{
                    foreach($ListaDeClientes as $Cliente){
                        $ClienteAMostrar = new cliente($Cliente['rif']);
                        $DatosDelCliente = $ClienteAMostrar->ObtenerDatos();

                        echo '
                        <row>
                            <celda class="ColumnaID"><img src="../Imagenes/Clientes/'.((empty($DatosDelCliente['ULRImagen']))?"ImagenPredefinida_Clientes.png":$DatosDelCliente['ULRImagen']).'"></celda>
                            <celda class="ColumnaRIF">'.$DatosDelCliente['tipoDeDocumento'].' - '.zerofill($DatosDelCliente['rif'], 9).'</celda>
                            <celda class="ColumnaNombre">'.$DatosDelCliente['nombre'].'</celda>
                            <celda class="ColumnaDetalles"><a class="hovershadow" href="Cliente/?rif='.$DatosDelCliente['rif'].'">Ver más</a></celda>
                        </row>
                        ';
                    }
                }
                ?>
            </div>
        </div>
        
        </div>
    </article>
    <aside id="BarraLateral">
        <div id="contenidoDeLaBarraLateral">
            <div id="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Clientes.png" alt="">
                <b>Clientes</b>
            </div>
            <a id="AgregarNuevo" href="NuevoCliente"> <i class="fi-rr-add"></i> Nuevo Cliente</a>
        </div>
    </aside>
    <?php include '../ipserver.php';?>
<script src="Clientes.js"></script>

</body>
</html>