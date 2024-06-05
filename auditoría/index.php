<?php
include_once('../Otros/clases.php');
$BaseDeDatos = new conexion();
$Tiempo = new AsistenteDeTiempo();
$Auditoria = new historial();
$NroMaximoDeRowsMostradas = 7;



$searchParams = array(
    'descripcion' => '',
    'tipo' => '0',
    'entidad' => '0',
    'mes' => '0',
    'anio' => '0',
    'pagina' => '1'
);
if($_GET){
    $searchParams['descripcion'] = (isset($_GET['descripcion'])? $_GET['descripcion']:'');
    $searchParams['tipo'] = (isset($_GET['tipo'])? $_GET['tipo']:'');
    $searchParams['entidad'] = (isset($_GET['entidad'])? $_GET['entidad']:'');
    $searchParams['mes'] = (isset($_GET['mes'])? $_GET['mes']:'');
    $searchParams['anio'] = (isset($_GET['anio'])? $_GET['anio']:'');
    $searchParams['pagina'] = (isset($_GET['pagina'])? $_GET['pagina']:'1');
}

//print_r($searchParams);

$rows = $Auditoria->ObtenerResultadosDeBusqueda($searchParams);
$count = $rows['count']; 
$rows = $rows['rows'];

$totalPages = ceil($count / 15);
?>

<!DOCTYPE html>
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Auditoría</title>
    <link rel="stylesheet" href="estilos_auditoria.css">

    <?php include('../Otros/cabecera_N2.php');?>
    <div id="CajaDeBarras">
        <a href="../index.php" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="" class="Barra">
            <p>Auditoría</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> HISTORIAL DE CAMBIOS EN EL SISTEMA:</span>
        <form method="get" class="formularioDeBuqueda" id="formularioDeBuqueda" autocomplete="off">
            <div class="spaceSearchBar">
                <input value="<?php echo $searchParams['descripcion'];?>" name="descripcion" id="Input_busqueda" type="text" placeholder="Busca por ID o descripción...">
                <button id="Button_busqueda"><i class="fi-rr-search"></i></button>
            </div>
            <div class="spaceSelectInputs">
                <select name="tipo" id="">
                    <option value="0">Movimiento</option>
                    <option <?php echo ($searchParams['tipo']==1? 'selected':'')?> value="1">Creado</option>
                    <option <?php echo ($searchParams['tipo']==2? 'selected':'')?> value="2">Modificado</option>
                    <option <?php echo ($searchParams['tipo']==3? 'selected':'')?> value="3">Eliminado</option>
                </select>
                <select name="entidad" id="">
                    <option value="0">Entidad</option>
                    <?php
                    $search = $BaseDeDatos->consultar("SELECT * FROM `tipodeentidad`");
                    if(!empty($search)){
                        foreach($search as $row){
                            echo '<option '.($searchParams['entidad']==$row['id']? 'selected':'').' value="'.$row['id'].'">'.$row['descripcion'].'</option>';
                        }
                    }
                    ?>
                </select>
                <select name="mes" id="">
                    <option value="0">Mes</option>
                    <?php
                    for ($i=1; $i <= 12; $i++) { 
                        echo '<option '.($searchParams['mes']==$i? 'selected':'').' value="'.$i.'">'.$Tiempo->ConvertirMes_NumAText($i).'</option>';
                    }
                    ?>
                </select>
                <select name="anio" id="">
                    <option value="0">Año</option>
                    <?php
                    for ($i=2022; $i <= date('Y'); $i++) { 
                        echo '<option '.($searchParams['anio']==$i? 'selected':'').' value="'.$i.'">'.$i.'</option>';
                    }
                    ?>
                </select>
            </div>
        </form>
        <?php
        //print_r($rows);
        ?>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaTipo">Tipo</celda>
                <celda class="ColumnaDescripcion">Descripción</celda>
                <celda class="ColumnaFecha">Fecha</celda>
                <celda class="ColumnaUsuario">Usuario</celda>
            </div>
            <div class="CuerpoDeLaTabla"> 
                <?php
                
                if(empty($rows)){
                    echo '
                    <row class="TablaVacia">
                        No hay registros para mostrar
                    </row>
                    ';
                }else{
                    foreach($rows as $row){
                        switch($row['idTipoDeEntidad']){
                            case '11':
                                $entidadvisible = 'false';
                            break;
                            default:
                                $entidadvisible = 'true';
                        };

                        $carpeta = '';
                        $extraStyleP = 'false';
                        switch($row['idTipoDeEntidad']){
                            case '1':
                                $carpeta = 'productos/';
                                $search = $BaseDeDatos->consultar("SELECT * FROM `productos` WHERE `id` = ".$row['idDeEntidad']);
                                $nombreimagen = 'ImagenPredefinida_Productos.png';
                                if(!empty($search)){
                                    if(!empty($search[0]['ULRImagen'])){
                                        $nombreimagen = $search[0]['ULRImagen'];
                                    }
                                }
                            break;
                            case '2':
                                $carpeta = 'proveedores/';
                                $search = $BaseDeDatos->consultar("SELECT * FROM `proveedores` WHERE `rif` = ".$row['idDeEntidad']);
                                $nombreimagen = 'ImagenPredefinida_Proveedores.png';
                                if(!empty($search)){
                                    if(!empty($search[0]['ULRImagen'])){
                                        $nombreimagen = $search[0]['ULRImagen'];
                                    }
                                }
                            break;
                            case '3':
                                $carpeta = 'clientes/';
                                $search = $BaseDeDatos->consultar("SELECT * FROM `clientes` WHERE `rif` = ".$row['idDeEntidad']);
                                $nombreimagen = 'ImagenPredefinida_Clientes.png';
                                if(!empty($search)){
                                    if(!empty($search[0]['ULRImagen'])){
                                        $nombreimagen = $search[0]['ULRImagen'];
                                    }
                                }
                            break;
                            case '4':
                                $nombreimagen = 'iconoDelMenu_Ventas.png';
                                $extraStyleP = 'true';
                            break;
                            case '5':
                                $search = $BaseDeDatos->consultar("SELECT `nombreDeUsuario`, `idNivelDeUsuario`, `sexo` FROM `usuarios` WHERE `nombreDeUsuario` = '".$row['idDeEntidad']."'");
                                $nombreimagen = 'UsuarioNivel1SexoM.png';
                                $extraStyleP = 'true';
                                if(!empty($search)){
                                    if(!empty($search[0]['idNivelDeUsuario']) && !empty($search[0]['sexo'])){
                                        $nombreimagen = 'UsuarioNivel'.$search[0]['idNivelDeUsuario'].'Sexo'.$search[0]['sexo'].'.png';
                                    }
                                }
                            break;
                            case '6':
                                $nombreimagen = 'iconoDelMenu_Almacenes.png';
                                $extraStyleP = 'true';
                            break;
                            case '7':
                                $nombreimagen = 'iconoDelMenu_Inventario.png';
                                $extraStyleP = 'true';
                            break;
                            case '8':
                                $nombreimagen = 'iconoDelMenu_Compras.png';
                                $extraStyleP = 'true';
                            break;
                            default:
                                
                        }
                        $imagenurl = '../imagenes/'.$carpeta.$nombreimagen;
                        

                        echo '
                        <row huella="'.$row['idTipoDeHuella'].'" entidadvisible="'.$entidadvisible.'" tipodeentidad="'.$row['idTipoDeEntidad'].'" identidad="'.$row['idDeEntidad'].'" imagenurl="'.$imagenurl.'" extraStyleP="'.$extraStyleP.'">
                            <celda class="ColumnaID">'.$row['id'].'</celda>
                            <celda class="ColumnaTipo">'.($row['idTipoDeHuella']==1? 'Creado':($row['idTipoDeHuella']==2? 'Modificado':'Eliminado')).'</celda>
                            <celda class="ColumnaDescripcion">'.($row['cambioRealizado']? $row['cambioRealizado']:'<span style="color: gray;">Sin descripción</span>').'</celda>
                            <celda class="ColumnaFecha">'.$row['fechaCreacion'].'</celda>
                            <celda class="ColumnaUsuario">'.$row['nombreDeUsuario'].'</celda>
                        </row>
                        ';
                    }
                }
                
                ?>   
                
            </div>
        </div>
        <div id="FondoDeLaBusqueda" class="BotonesDeConsulta">
            <div class="SeparadorDeBotones">
                <button value="1" type="<?php echo ($searchParams['pagina']>1? 'submit':'button');?>" name="pagina" form="formularioDeBuqueda" title="Ir a la primera página"> <i class="fi-rr-angle-double-small-left"></i> </button>
                <button value="<?php echo ($searchParams['pagina']-1);?>" type="<?php echo ($searchParams['pagina']>1? 'submit':'button');?>" name="pagina" form="formularioDeBuqueda" title="Ir a la página anterior"> <i class="fi-rr-angle-small-left"></i> </button>
            </div>
            <div class="SeparadorDeBotones">
                <span class="NroPag"><?php echo $searchParams['pagina'];?></span>
            </div>
            <div class="SeparadorDeBotones">
                <button value="<?php echo ($searchParams['pagina']+1);?>" type="<?php echo ($searchParams['pagina']<$totalPages? 'submit':'button')?>" name="pagina" form="formularioDeBuqueda" title="Ir a la página siguente"> <i class="fi-rr-angle-small-right"></i> </button>
                <button value="<?php echo $totalPages?>" type="<?php echo ($searchParams['pagina']<$totalPages? 'submit':'button')?>" name="pagina" form="formularioDeBuqueda" title="Ir a la última página"> <i class="fi-rr-angle-double-small-right"></i> </button>
            </div>
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Auditoria.png" alt="">
                <b>Auditoría</b>
            </div>
            <a href="../"><i class="fi-rr-undo-alt"></i> Volver a inicio</a>
        </div>
        <div class="RegistroSeleccionado">
            <div class="Vacio">
                No hay información a mostrar
            </div>
        </div>
    </aside>
    <?php include '../ipserver.php';?>
    <script src="auditoria.js"></script>
</body>