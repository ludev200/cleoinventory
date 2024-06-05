<?php
include_once('../Otros/clases.php');
$BaseDeDatos = new conexion();
$Tiempo = new AsistenteDeTiempo();
$Auditoria = new historial();

$usuarioObj = new usuario(0);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $GLOBALS['nombreCorto'];?>: Usuarios</title>
    <link rel="stylesheet" href="estilos.css">

    <?php include('../Otros/cabecera_N2.php');?>
    <div id="CajaDeBarras">
        <a href="../index.php" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="" class="Barra">
            <p>Usuarios</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
    <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> USUARIOS DEL SISTEMA:</span>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaUsuario">Usuario</celda>
                <celda class="ColumnaNombre">Nombre</celda>
                <celda class="ColumnaPerfil">Perfil</celda>
                <celda class="ColumnaCedula">Estado</celda>
                <celda class="ColumnaDetalles">Detalles</celda>
            </div>
            <div class="CuerpoDeLaTabla"> 
                <?php
                $usuarios = $usuarioObj->ConsultarUsuarios();
                if(empty($usuarios)){
                    echo '
                    <row class="RowVacio">
                        <span>No hay usuarios a mostrar</span>
                    </row>
                    ';
                }else{
                    foreach($usuarios as $row){
                        echo '
                        <row>
                            <celda class="ColumnaUsuario">'.$row['nombreDeUsuario'].'</celda>
                            <celda class="ColumnaNombre">'.$row['nombres'].'</celda>
                            <celda class="ColumnaPerfil">'.$row['perfil'].'</celda>
                            <celda class="ColumnaCedula"><div class="puntito '.($row['idEstado']=='41'? 'Activo':'Inhabilitado').'" title="'.($row['idEstado']=='41'? 'Habilitado':'Inhabilitado').'"></div></celda>
                            <celda class="ColumnaDetalles">
                                <a href="Usuario/?id='.$row['nombreDeUsuario'].'">Ver más</a>
                            </celda>
                        </row>
                        ';
                    }
                }
                ?>
            </div>
        </div>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Usuario.png" alt="">
                <b>Usuarios</b>
            </div>
            <a href="Nuevo"><i class="fi-rr-add"></i> Crear nuevo usuario</a>
        </div>
    </aside>
    <?php include '../ipserver.php';?>
    <script src="js.js"></script>
</body>
</html>