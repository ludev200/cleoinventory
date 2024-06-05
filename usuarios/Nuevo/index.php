<?php
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();
$Tiempo = new AsistenteDeTiempo();
$Auditoria = new historial();
session_start();
$usuarioConectado = new usuario($_SESSION['nombreDeUsuario']);
$preguntas = $BaseDeDatos->consultar("SELECT * FROM `preguntas`");

$permisos = array();
$permisos[] = (empty($_POST['permiso-Almacenes'])? '':'Almacenes');
$permisos[] = (empty($_POST['permiso-Ventas'])? '':'Ventas');
$permisos[] = (empty($_POST['permiso-Clientes'])? '':'Clientes');
$permisos[] = (empty($_POST['permiso-Inventario'])? '':'Inventario');
$permisos[] = (empty($_POST['permiso-Productos'])? '':'Productos');
$permisos[] = (empty($_POST['permiso-Compras'])? '':'Compras');
$permisos[] = (empty($_POST['permiso-Proveedores'])? '':'Proveedores');
$permisos[] = (empty($_POST['permiso-Auditoría'])? '':'Auditoría');
$permisos[] = (empty($_POST['permiso-Usuarios'])? '':'Usuarios');
$permisos[] = (empty($_POST['permiso-Sistema'])? '':'Sistema');

$datosAMostrar = array(
    'tipoDeDocumento' => (empty($_POST['tipoDeDocumento'])? 'V':$_POST['tipoDeDocumento']),
    'cedula' => (empty($_POST['cedula'])? '':$_POST['cedula']),
    'nombres' => (empty($_POST['nombres'])? '':$_POST['nombres']),
    'sexo' => (empty($_POST['sexo'])? 'M':$_POST['sexo']),
    'nombreDeUsuario' => (empty($_POST['nombreDeUsuario'])? '':$_POST['nombreDeUsuario']),
    'contrasenia' => (empty($_POST['contrasenia'])? '':$_POST['contrasenia']),
    'pregunta1' => (isset($_POST['pregunta1'])? ($_POST['pregunta1']=='0'? '0':$_POST['pregunta1']):'1'),
    'pregunta2' => (isset($_POST['pregunta2'])? ($_POST['pregunta2']=='0'? '0':$_POST['pregunta2']):'2'),
    'pregunta3' => (isset($_POST['pregunta3'])? ($_POST['pregunta3']=='0'? '0':$_POST['pregunta3']):'3'),
    'custom1' => (empty($_POST['custom1'])? '':$_POST['custom1']),
    'custom2' => (empty($_POST['custom2'])? '':$_POST['custom2']),
    'custom3' => (empty($_POST['custom3'])? '':$_POST['custom3']),
    'respuesta1' => (empty($_POST['respuesta1'])? '':$_POST['respuesta1']),
    'respuesta2' => (empty($_POST['respuesta2'])? '':$_POST['respuesta2']),
    'respuesta3' => (empty($_POST['respuesta3'])? '':$_POST['respuesta3']),
    'idNivelDeUsuario' => (empty($_POST['idNivelDeUsuario'])? '3':$_POST['idNivelDeUsuario']),
    'permisos' => $permisos
);

if($_POST){
    //print_r($_POST);
    
    //print_r($datosAMostrar);
    try{
        //$errores = $usuarioConectado->crearNuevoUsuario($datosAMostrar);
        print_r($usuarioConectado->crearNuevoUsuario($datosAMostrar));
        if(empty($errores)){
            header('Location: ../Usuario/?id='.$datosAMostrar['nombreDeUsuario']);
        }
    }catch(Exception $error){
        $errores = $error->getMessage();
    }
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="estilos.css">
    <title><?php echo $GLOBALS['nombreCorto'];?>: Nuevo usuario</title>
    <?php include('../../Otros/cabecera_N3.php');?>
    <div id="CajaDeBarras">
        <a href="../../index.php" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="../" class="Barra">
            <p>Usuarios</p>
            <div class="Cuadrito"></div>
        </a>
        <a href="" class="Barra">
            <p>Nuevo</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <modal id="ModalDeErrores" class="dplaynn">
        <div class="ContenidoDelModal">
            <img class="TextoCentro" src="../../Imagenes/TrianguloDeAdvertencia.png" alt="">
            <b class="TextoCentro">No se puede guardar</b>
            <p>Se han encontrado errores que impiden continuar. Rectifique e intentelo de nuevo.</p>
            <b class="TextoIzquierda">Errores:</b>
            <div class="CajaDeErrores"><?php
                if(isset($errores)){
                    $lineas = explode('¿', $errores);

                    foreach($lineas as $linea){
                        echo '<span>'.$linea.'</span>';
                    }
                }
                ?></div>
        </div>
    </modal>
    <form method="post" autocomplete="off">
        <span id="TopeDelListado" class="fi-sr-clipboard-list TituloDeSectionDelArticle"> DATOS PERSONALES:</span>
        <ul class="personales">
            <li>
                <b>Cédula</b>
                <div class="CajaPalInput">
                    <select name="tipoDeDocumento" id="tipoDeDocumento">
                        <option <?php echo ($datosAMostrar['tipoDeDocumento']=='V'? 'selected':'');?> value="V">V</option>
                        <option <?php echo ($datosAMostrar['tipoDeDocumento']=='J'? 'selected':'');?> value="J">J</option>
                        <option <?php echo ($datosAMostrar['tipoDeDocumento']=='E'? 'selected':'');?> value="E">E</option>
                        <option <?php echo ($datosAMostrar['tipoDeDocumento']=='G'? 'selected':'');?> value="G">G</option>
                        <option <?php echo ($datosAMostrar['tipoDeDocumento']=='P'? 'selected':'');?> value="P">P</option>
                    </select>
                    <input id="inputCedula" type="text" onkeypress="return soloInt(this, event)" class="noarrows" name="cedula" value="<?php echo $datosAMostrar['cedula'];?>">
                </div>
            </li>
            <li>
                <b>Nombre y apellido</b>
                <div class="CajaPalInput">
                    <input type="text" name="nombres" maxlength="50" value="<?php echo $datosAMostrar['nombres'];?>">
                </div>
            </li>
            <li>
                <b>Sexo</b>
                <div class="CajaPalInput">
                    <select name="sexo" id="">
                        <option <?php echo ($datosAMostrar['sexo']=='M'? 'selected':'');?> value="M">Masculino</option>
                        <option <?php echo ($datosAMostrar['sexo']=='F'? 'selected':'');?> value="F">Femenino</option>
                    </select>
                </div>
            </li>
        </ul>
        <span id="TopeDelListado" class="fi-sr-shield TituloDeSectionDelArticle"> DATOS DE LA CUENTA Y SEGURIDAD:</span>
        <ul class="cuenta">
            <li>
                <b>Nombre de usuario</b>
                <div class="CajaPalInput">
                    <input type="text" name="nombreDeUsuario" maxlength="30" value="<?php echo $datosAMostrar['nombreDeUsuario'];?>">
                </div>
            </li>
            <li>
                <b>Contraseña</b>
                <div class="CajaPalInput">
                    <input type="text" name="contrasenia"  maxlength="20" value="<?php echo $datosAMostrar['contrasenia'];?>">
                </div>
            </li>
            <li>
                <b>Pregunta de recuperación 1</b>
                <div class="CajaPalInput CajaDePregRecup">
                    <select name="pregunta1" id="selectordepregunta">
                        <option value="0">Pregunta personalizada</option>
                        <?php
                        if(!empty($preguntas)){
                            foreach($preguntas as $row){
                                echo '<option '.($datosAMostrar['pregunta1']==$row['id']? 'selected':'').' value="'.$row['id'].'">¿'.$row['pregunta'].'?</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="customquestion" id="preguntapersonalizada">
                        <span>¿</span>
                        <input type="text" placeholder="Mi pregunta" name="custom1" maxlength="30" value="<?php echo $datosAMostrar['custom1'];?>">
                        <span>?</span>
                    </div>
                    <input type="text" placeholder="Mi respuesta" name="respuesta1" maxlength="20" value="<?php echo $datosAMostrar['respuesta1'];?>">
                </div>
            </li>
            <li>
                <b>Pregunta de recuperación 2</b>
                <div class="CajaPalInput CajaDePregRecup" numero="2">
                    <select name="pregunta2" id="selectordepregunta">
                        <option value="0">Pregunta personalizada</option>
                        <?php
                        if(count($preguntas)>1){
                            foreach($preguntas as $row){
                                echo '<option '.($datosAMostrar['pregunta2']==$row['id']? 'selected':'').' value="'.$row['id'].'">¿'.$row['pregunta'].'?</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="customquestion" id="preguntapersonalizada">
                        <span>¿</span>
                        <input type="text" placeholder="Mi pregunta" name="custom2" maxlength="30" value="<?php echo $datosAMostrar['custom2'];?>">
                        <span>?</span>
                    </div>
                    <input type="text" placeholder="Mi respuesta" name="respuesta2" maxlength="20" value="<?php echo $datosAMostrar['respuesta2'];?>">
                </div>
            </li>
            <li>
                <b>Pregunta de recuperación 3</b>
                <div class="CajaPalInput CajaDePregRecup" numero="3">
                    <select name="pregunta3" id="selectordepregunta">
                        <option value="0">Pregunta personalizada</option>
                        <?php
                        if(count($preguntas)>2){
                            foreach($preguntas as $row){
                                echo '<option '.($datosAMostrar['pregunta3']==$row['id']? 'selected':'').' value="'.$row['id'].'">¿'.$row['pregunta'].'?</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="customquestion" id="preguntapersonalizada">
                        <span>¿</span>
                        <input type="text" placeholder="Mi pregunta" name="custom3" maxlength="30" value="<?php echo $datosAMostrar['custom3'];?>">
                        <span>?</span>
                    </div>
                    <input type="text" placeholder="Mi respuesta" name="respuesta3" maxlength="20" value="<?php echo $datosAMostrar['respuesta3'];?>">
                </div>
            </li>
        </ul>
        <span id="TopeDelListado" class="fi-sr-user TituloDeSectionDelArticle"> PERFIL DE USUARIO:</span>
        <ul class="seguridad">
            <li>
                <div class="CajaPalInput">
                    <b>Perfil de usuario:</b>
                    <select name="idNivelDeUsuario" id="profileselector">
                        <option <?php echo ($datosAMostrar['idNivelDeUsuario']=='3'? 'selected':'');?> value="3">Analista</option>
                        <option <?php echo ($datosAMostrar['idNivelDeUsuario']=='2'? 'selected':'');?> value="2">Contador</option>
                        <option <?php echo ($datosAMostrar['idNivelDeUsuario']=='1'? 'selected':'');?> value="1">Administrador</option>
                    </select>
                </div>
            </li>
        </ul>
        <b>Permisos:</b>
        <div class="permisos">
            
            <?php
            $search = $BaseDeDatos->consultar("SELECT * FROM `modulos`");

            foreach($search as $row){
                $nivel1 = !empty($BaseDeDatos->consultar("SELECT * FROM `modulospredeterminados` WHERE (`idNivelDeUsuario` = 1 AND `idModulo` = ".$row['id'].")"));
                $nivel2 = !empty($BaseDeDatos->consultar("SELECT * FROM `modulospredeterminados` WHERE (`idNivelDeUsuario` = 2 AND `idModulo` = ".$row['id'].")"));
                $nivel3 = !empty($BaseDeDatos->consultar("SELECT * FROM `modulospredeterminados` WHERE (`idNivelDeUsuario` = 3 AND `idModulo` = ".$row['id'].")"));

                
                echo '
                <label>
                    <input '.((in_array($row['nombre'],$datosAMostrar['permisos']))? 'checked':'').' HIDDEN type="checkbox" name="permiso-'.$row['nombre'].'" id="" class="checkboxmodulo '.($nivel1? 'perfil1':'').' '.($nivel2? 'perfil2':'').' '.($nivel3? 'perfil3':'').'">
                    <div class="modulocard">
                        <img src="../../imagenes/'.$row['nombreDeImagen'].'" alt="">
                        <span>'.$row['nombre'].'</span>
                    </div>
                </label>
                ';
            }
            ?>
            
        </div>
        <div class="coolFinalButtons">
            <a href="../" class="hovershadow">Salir</a>
            <button id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
    </form>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Usuario.png" alt="">
                <b>Usuarios</b>
            </div>
            <a href="../../Ayuda/#84" target="_blank"><i class="fi-rr-interrogation"></i> Ir a información<br>sobre usuarios</a>
        </div>
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="js.js"></script>
</body>
</html>