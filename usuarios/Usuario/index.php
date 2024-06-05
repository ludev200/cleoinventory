<?php
include('../../Otros/clases.php');
$BaseDeDatos = new conexion();

if(empty($_GET['id'])){
    header('Location: ../../Error.php?desc=2');
}else{
    if(empty($BaseDeDatos->consultar("SELECT * FROM `usuarios` WHERE `nombreDeUsuario` = '".$_GET['id']."'"))){
        header('Location: ../../Error.php?desc=14');
    }else{
        $usuario = new usuario($_GET['id']);
        $user = new user($_GET['id']);
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $GLOBALS['nombreCorto'];?>: <?php echo $usuario->nombreDeUsuario;?></title>
    <link rel="stylesheet" href="estilos.css">
    <?php include('../../Otros/cabecera_N3.php');?>
    <nav id="ZonaDeCliente" class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../../">
            <p >Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="../">
            <p>Usuarios</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p><?php echo $usuario->nombreDeUsuario;?></p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
        <div class="imgXdiv">
            <div>
                <img src="../../Imagenes/UsuarioNivel<?php echo $usuario->idNivelDeUsuario;?>Sexo<?php echo $usuario->sexo;?>.png" alt="">
            </div>
            <div class="datospersonales">
                <h2 id="username"><?php echo $usuario->nombreDeUsuario;?></h2>
                <p><?php echo ($usuario->idNivelDeUsuario=='1'? 'Administrador':($usuario->idNivelDeUsuario==2? 'Contador':'Analista'));?></p>
                <br>
                <ul>
                    <li>
                        <b>Cédula:</b>
                        <span><?php echo $usuario->tipoDeDocumento.'-'.$usuario->cedula;?></span>
                    </li>
                    <li>
                        <b>Nombres:</b>
                        <span><?php echo $usuario->nombre;?></span>
                    </li>
                    <li>
                        <b>Sexo:</b>
                        <span><?php echo ($usuario->sexo=='M'? 'Masculino':'Femenino');?></span>
                    </li>
                </ul>
            </div>
        </div>
        <span id="TopeDelListado" class="fi-rr-apps TituloDeSectionDelArticle"> MÓDULOS PERMITIDOS:</span>
        <div class="modulos">
            <?php
            $search = $BaseDeDatos->consultar("SELECT `modulos`.* FROM `modulospermitidos` INNER JOIN `modulos` ON `modulospermitidos`.`idModulo` = `modulos`.`id` WHERE `usuario` = '$usuario->nombreDeUsuario'");
            if(empty($search)){
                echo '
                
                ';
            }else{
                foreach($search as $row){
                    echo '
                    <div class="modulocard seleccionado">
                        <img src="../../imagenes/'.$row['nombreDeImagen'].'" alt="">
                        <span>'.$row['nombre'].'</span>
                    </div>
                    ';
                }
            }

            ?>
        </div>
        <span id="TopeDelListado" class="fi-rr-line-width TituloDeSectionDelArticle"> ACCIONES DEL USUARIO:</span>
        <div class="TablaDeResultados">
            <div class="CabeceraDeLaTabla">
                <celda class="ColumnaID">ID</celda>
                <celda class="ColumnaTipo">Tipo</celda>
                <celda class="ColumnaDescripcion">Descripción</celda>
                <celda class="ColumnaFecha">Fecha</celda>
            </div>
            <div class="CuerpoDeLaTabla"> 
                <?php
                $search = $BaseDeDatos->consultar("SELECT * FROM `historial` WHERE `nombreDeUsuario` = '$usuario->nombreDeUsuario'");
                if(empty($search)){
                    echo '
                    <row class="TablaVacia">
                        No hay registros para mostrar
                    </row>
                    ';
                }else{
                    foreach($search as $row){
                        echo '
                        <row huella="1" entidadvisible="true" tipodeentidad="5" identidad="argelia18" imagenurl="../imagenes/UsuarioNivel2SexoF.png" extrastylep="true">
                            <celda class="ColumnaID">'.$row['id'].'</celda>
                            <celda class="ColumnaTipo">Creado</celda>
                            <celda class="ColumnaDescripcion">'.$row['cambioRealizado'].'</celda>
                            <celda class="ColumnaFecha">'.$row['fechaCreacion'].'</celda>
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
                <img src="../../Imagenes/iconoDelMenu_Usuario.png" alt="">
                <b>Usuarios</b>
            </div>
            <a href="../../Modificar/Usuario/?id=<?php echo $_GET['id'];?>"><i class="fi-rr-pencil"></i> Modificar usuario</a>
            <?php
            if($user->getIdStatus() == '41'){
                echo '<button href="Nuevo" id="BotonEliminar"><i class="fi-rr-trash"></i> Inhabilitar usuario</button>';
            }else{
                echo '<button href="Nuevo" id="BotonHabilitar"><i class="fi-rr-power"></i> Habilitar usuario</button>';
            }
            ?>
        </div>
    </aside>

    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="js.js"></script>
</body>
</html>