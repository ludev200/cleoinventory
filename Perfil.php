<?php
include('Otros/clases.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreCorto'];?>: Perfil</title>
    <link rel="stylesheet" href="estilos_Perfil.css">
    <?php include('Otros/cabecera.php');?>
    <div id="CajaDeBarras">
        <a href="index.php" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p>Perfil</p>
            <div class="Cuadrito"></div>
        </a>
    </div>
    <?php
        $Tiempo = new AsistenteDeTiempo();
        $Usuario = unserialize($_SESSION["UsuarioLogeado"]);
        $DatosDelUsuario = $Usuario->ObtenerDatos();
        $UsuarioActual = new usuario($DatosDelUsuario['nombreDeusuario']);
        $DatosDelUsuario = $UsuarioActual->ObtenerDatos();
        $ModulosPermitidos = $UsuarioActual->MostrarListaDePermisos();
        

        $Problemas = "";

        if(isset($_GET)){
            if(isset($_GET['pagina'])){
                if(!empty($_GET['pagina'])){
                    if(is_numeric($_GET['pagina'])){
                        if($_GET['pagina'] == 1 || $_GET['pagina'] == 2 || $_GET['pagina'] == 3){
                            $PaginaAMostrar = $_GET['pagina'];    
                        }else{
                            header("Location: Perfil.php?pagina=1#Card");
                        }
                    }else{
                        header("Location: Perfil.php?pagina=1#Card");
                    }
                }else{
                    header("Location: Perfil.php?pagina=1#Card");
                }
            }else{
                header("Location: Perfil.php?pagina=1#Card");
            }
        }else{
            header("Location: Perfil.php?pagina=1#Card");
        }


        if($_POST){
            if(isset($_POST['ActualizarDatosPersonales'])){
                try{
                    $Problemas = $UsuarioActual->ActualizarDatosPersonales($_POST);
                }catch(Exception $e){
                    $Problemas = $e->getMessage();
                }
    
                
            }

            if(isset($_POST['ActualizarContrasenia'])){
                try{
                    $Problemas = $UsuarioActual->ActualizarContrasenia($_POST);
                }catch(Exception $e){
                    $Problemas = $e->getMessage();
                }
            }

            if(isset($_POST['ActualizarRespuestas'])){
                try{
                    $Problemas = $UsuarioActual->ActualizarRespuestas($_POST);
                }catch(Exception $e){
                    $Problemas = $e->getMessage();
                }

            }

            if(empty($Problemas)){
                header("Refresh:0");
            }
        }



        
        


        $BaseDeDatos = new conexion();
        $PreguntasDisponibles = $BaseDeDatos->consultar("SELECT * FROM `preguntas`");
    ?>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
        <label id="ModalDeErrores" class="Modal">
            <input hidden <?php echo ((empty($Problemas))?"":"checked");?> type="checkbox" name="" id="InputMostrarVentanaDeErrores">
            <div class="Ventanita">
                <img class="TextoCentro" src="Imagenes/TrianguloDeAdvertencia.png" alt="">
                <b>No se puede guardar</b>
                <p>Se han encontrado errores que impiden continuar. Rectifique e intentelo de nuevo.</p>
                <b class="TextoIzquierda">Errores:</b>
                <div class="TextoCentro CajaDeErrores">
                    <?php 
                        foreach(explode("¿", $Problemas) as $ErrorDeFormato){
                            echo "<span>".$ErrorDeFormato.((empty($ErrorDeFormato))?"":".")."</span>";
                        }
                    ?>
                </div>
            </div>
        </label>
        <div class="HojaDeContenido" id="Card">
            <aside>
                <div class="EspacioDeLAIMagen">
                    <div class="MarcoDeLaImagen">
                        <img src="Imagenes/<?php echo 'UsuarioNivel'.$DatosDelUsuario['idNivelDeUsuario'].'Sexo'.$DatosDelUsuario['sexo'];?>.png" alt="">
                    </div>
                </div>
                <span class="NombreDelUsuairo"><?php echo $DatosDelUsuario['nombres'];?></span>
                <span class="NivelDelUsuario"><?php echo $DatosDelUsuario['nivelDeUsuario'];?></span>
                <div class="Acciones">
                    <Button id="BotonPagina1" class="BotonesDeAccion <?php echo (($PaginaAMostrar != '1')?'BotonPulsable':'');?>"> <i class="fi-sr-user"></i> Perfil</Button>
                    <Button id="BotonPagina2" class="BotonesDeAccion <?php echo (($PaginaAMostrar != '2')?'BotonPulsable':'');?>"> <i class="fi-sr-shield"></i> Seguridad</Button>
                    <Button id="BotonPagina3" class="BotonesDeAccion <?php echo (($PaginaAMostrar != '3')?'BotonPulsable':'');?>"> <i class="fi-sr-apps"></i> Permisos </Button>
                </div>
            </aside>
            <section style="display: none;" id="SectionPagina1">
                <div class="CajaParaTituloDeSection">
                    <span class="TituloDelSection"> <i class="fi-rr-clipboard-list"></i> Datos Personales:</span>
                    <span id="BotonPaEditarDatosPersonales" class="fi-rr-pencil BotonPaEditar" title="Modificar"></span>
                </div>
                <form autocomplete="off" action="Perfil.php?pagina=1" method="post" id="DatosPersonales"></form>
                <form autocomplete="off" action="Perfil.php?pagina=2" method="post" id="Contrasenia"></form>
                <form autocomplete="off" action="Perfil.php?pagina=2#Card" method="post" id="Respuestas"></form>
                <div class="DatosPersonales">
                    <div class="CajaIzquierda">
                        <b>Cédula</b>
                        <b>Nombre y apellido</b>
                        <b>Sexo</b>
                    </div>
                    <div class="CajaDelMedio">
                        <b>:</b>
                        <b>:</b>
                        <b>:</b>
                    </div>
                    <div id="CajaDerechaDeDatosPersonales" class="CajaDerecha">
                        <p><span id="DatosPersonalesTipoDeDocumento"><?php echo $DatosDelUsuario['tipoDeDocumento'];?></span><span>-</span><span id="DatosPersonalesCedula"><?php echo $DatosDelUsuario['cedula'];?></span></p>
                        <p id="DatosPersonalesNombres"><?php echo $DatosDelUsuario['nombres'];?></p>
                        <p id="DatosPersonalesSexo"><?php echo (($DatosDelUsuario['sexo'] == 'F')?'Femenino':'Masculino');?></p>
                    </div>
                </div>
                <div id="BotonesDeDatosPersonales" class="EspacioDeDPersonales">
                    <!--Aqui actua Perfil.js-->
                </div>
                <br>
                <span class="TituloDelSection"> <i class="fi-rr-user"></i> Datos del usuario:</span>
                <div class="DatosPersonales">
                    <div class="CajaIzquierda">
                        <b>Nombre de usuario</b>
                        <b>Nivel de usuario</b>
                        <b>Estado</b>
                        <b>Fecha de creación</b>
                    </div>
                    <div class="CajaDelMedio">
                        <b>:</b>
                        <b>:</b>
                        <b>:</b>
                        <b>:</b>
                    </div>
                    <div class="CajaDerecha">
                        <p><?php echo $DatosDelUsuario['nombreDeusuario'];?></p>
                        <p><?php echo $DatosDelUsuario['nivelDeUsuario'].(($DatosDelUsuario['idNivelDeUsuario'] == 1)?'<i style="color: orange;" class="fi-sr-crown" title="Este usuario tiene control total del sistema."></i>':'');?></p>
                        <p><?php echo (($DatosDelUsuario['idEstado'] == '41')?'Habilitado':'Deshabilitado');?></p>
                        <p><?php echo (($DatosDelUsuario['fechaCreacion'] == "Desconocido")?'<span style="color: gray;">Desconocido</span>':$Tiempo->ConvertirFormato($DatosDelUsuario['fechaCreacion'], 'BaseDeDatosConTiempo', 'UsuarioConTiempo'));?></p>
                    </div>
                </div>
            </section>
            <section style="display: none;" id="SectionPagina2">
                <div class="CajaParaTituloDeSection">
                    <span class="TituloDelSection"> <i class="fi-rr-key"></i> Contraseña:</span>
                    <span id="BotonPaEditarContrasenia" class="fi-rr-pencil BotonPaEditar" title="Modificar"></span>
                </div>                
                <p>La contraseña utilizada para acceder al sistema debe contar con un mínimo de 8 caracteres e incluir al menos un número.</p>
                <div id="ContenidoDeLaSeccionDeContra">
                    <span class="SimulacionDeInput">*********</span>
                </div>
                <br>
                <div class="CajaParaTituloDeSection">
                    <span class="TituloDelSection"> <i class="fi-rr-question-square"></i> Preguntas de recuperación:</span>
                    <span id="BotonPaEditarRespuestas" class="fi-rr-pencil BotonPaEditar" title="Modificar"></span>
                </div>
                <p>Las preguntas de recuperación se utilizan para recuperar la contraseña en caso de olvidarla.</p>
                <div id="CajaDeMuestraDeRespuestas" class="ContenidoDeLaSeccionDePreguntas">
                    <div class="CajaParaPregunta">
                        <span class="NumeroDePregunta">1</span>
                        <div class="NoSeQueNombrePonerleAEsteDiv">
                            <b>¿<?php echo $DatosDelUsuario['preguntasDeSeguridad']['pregunta1'];?>?</b>
                            <p>**************</p>
                        </div>
                    </div>
                    <div class="CajaParaPregunta">
                        <span class="NumeroDePregunta">2</span>
                        <div class="NoSeQueNombrePonerleAEsteDiv">
                            <b>¿<?php echo $DatosDelUsuario['preguntasDeSeguridad']['pregunta2'];?>?</b>
                            <p>**************</p>
                        </div>
                    </div>
                    <div class="CajaParaPregunta">
                        <span class="NumeroDePregunta">3</span>
                        <div class="NoSeQueNombrePonerleAEsteDiv">
                            <b>¿<?php echo $DatosDelUsuario['preguntasDeSeguridad']['pregunta3'];?>?</b>
                            <p>**************</p>
                        </div>
                    </div>
                </div>
                <div style="display: none;" id="CajaDeEdicionDeRespuestas" class="ContenidoDeLaSeccionDePreguntas">
                    <div class="CajaParaPregunta">
                        <span class="NumeroDePregunta">1</span>
                        <div class="NoSeQueNombrePonerleAEsteDiv">
                            <div class="UltimoDivLoPrometo">
                                <select form="Respuestas" name="Pregunta1" id="SelectPregunta1">
                                    <?php
                                        foreach($PreguntasDisponibles as $PreguntaDeLaBD){
                                            echo '<option '.(($PreguntaDeLaBD['id'] == $DatosDelUsuario['preguntasDeSeguridad']['idPregunta1'])?"selected":"").' value="'.$PreguntaDeLaBD['id'].'">'.$PreguntaDeLaBD['pregunta'].'</option>';
                                        }
                                    ?>
                                </select>
                                <input form="Respuestas" name="Respuesta1" maxlength="20" id="InputRespuesta1" placeholder="**********" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="CajaParaPregunta">
                        <span class="NumeroDePregunta">2</span>
                        <div class="NoSeQueNombrePonerleAEsteDiv">
                            <div class="UltimoDivLoPrometo">
                                <select form="Respuestas" name="Pregunta2" id="SelectPregunta2">
                                    <?php
                                        foreach($PreguntasDisponibles as $PreguntaDeLaBD){
                                            echo '<option '.(($PreguntaDeLaBD['id'] == $DatosDelUsuario['preguntasDeSeguridad']['idPregunta2'])?"selected":"").' value="'.$PreguntaDeLaBD['id'].'">'.$PreguntaDeLaBD['pregunta'].'</option>';
                                        }
                                    ?>
                                </select>
                                <input form="Respuestas" name="Respuesta2" maxlength="20" id="InputRespuesta2" placeholder="**********" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="CajaParaPregunta">
                        <span class="NumeroDePregunta">3</span>
                        <div class="NoSeQueNombrePonerleAEsteDiv">
                            <div class="UltimoDivLoPrometo">
                                <select form="Respuestas" name="Pregunta3" id="SelectPregunta3">
                                    <?php
                                        foreach($PreguntasDisponibles as $PreguntaDeLaBD){
                                            echo '<option '.(($PreguntaDeLaBD['id'] == $DatosDelUsuario['preguntasDeSeguridad']['idPregunta3'])?"selected":"").' value="'.$PreguntaDeLaBD['id'].'">'.$PreguntaDeLaBD['pregunta'].'</option>';
                                        }
                                    ?>
                                </select>
                                <input form="Respuestas" name="Respuesta3" maxlength="20" id="InputRespuesta3" placeholder="**********" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="BotonesPaGuardarYVolver">
                        <button disabled id="BotonGuardarRespuestas" name="ActualizarRespuestas" form="Respuestas" class="BotonPaGuardar">Guardar</button>
                        <div class="EspacioPaVolver">
                            <span id="BotonVolverDeRespuestas">Volver</span>
                        </div>
                    </div>
                </div>
                <?php
                        //print_r($DatosDelUsuario['preguntasDeSeguridad']);
                        
                    ?>
            </section>
            <section style="display: none;" id="SectionPagina3">
                <span class="TituloDelSection"> <i class="fi-rr-apps"></i> Módulos de usuario <?php echo $DatosDelUsuario['nivelDeUsuario'];?>:</span>
                <div class="CajaDeModulos">
                <?php
                    $ResultadoDeConsulta = $BaseDeDatos->consultar("SELECT `modulos`.`nombre`, `modulos`.`nombreDeImagen` FROM `modulospermitidos` INNER JOIN `modulos` ON `modulospermitidos`.`idModulo` = `modulos`.`id` WHERE ( (`modulospermitidos`.`usuario` = '".$DatosDelUsuario['nombreDeusuario']."') AND (`modulospermitidos`.`idModulo` IN (SELECT `modulospredeterminados`.`idModulo` FROM `modulospredeterminados` WHERE `modulospredeterminados`.`idNivelDeUsuario` = ".$DatosDelUsuario['idNivelDeUsuario'].")));");

                    foreach($ResultadoDeConsulta as $Modulo){
                        echo '
                            <a title="Ir a" href="'.$Modulo['nombre'].'" target="_blank" class="CartaDeModulo ModuloPermitido">
                                <div class="EspacioDeLaImagen">
                                    <img src="Imagenes/'.$Modulo['nombreDeImagen'].'" alt="">
                                </div>
                                <div class="EspacioNombreDelModulo">
                                    '.$Modulo['nombre'].'
                                </div>
                            </a>
                        ';   
                    }
                    $ResultadoDeConsulta = $BaseDeDatos->consultar("SELECT `modulos`.`nombre`, `modulos`.`nombreDeImagen` FROM `modulospredeterminados` INNER JOIN `modulos` ON `modulospredeterminados`.`idModulo` = `modulos`.`id` WHERE ( (`modulospredeterminados`.`idModulo` NOT IN (SELECT `modulospermitidos`.`idModulo` FROM `modulospermitidos` WHERE `modulospermitidos`.`usuario` = '".$DatosDelUsuario['nombreDeusuario']."')) AND (`modulospredeterminados`.`idNivelDeUsuario` = 2) );");
                    foreach($ResultadoDeConsulta as $Modulo){
                        echo '
                            <div title="Se te ha revocado el permiso a este modulo" href="'.$Modulo['nombre'].'.php" target="_blank" class="CartaDeModulo ModuloSinAcceso">
                                <div class="EspacioDeLaImagen">
                                    <img src="Imagenes/'.$Modulo['nombreDeImagen'].'" alt="">
                                </div>
                                <div class="EspacioNombreDelModulo">
                                    '.$Modulo['nombre'].'
                                </div>
                            </div>
                        ';   
                    }
                ?>
                </div>
                
                <?php
                    $ResultadoDeConsulta = $BaseDeDatos->consultar("SELECT `modulos`.`nombre`, `modulos`.`nombreDeImagen` FROM `modulospermitidos` INNER JOIN `modulos` ON `modulospermitidos`.`idModulo` = `modulos`.`id` WHERE ( (`modulospermitidos`.`usuario` = 'Fran') AND (`modulospermitidos`.`idModulo` NOT IN (SELECT `modulospredeterminados`.`idModulo` FROM `modulospredeterminados` WHERE `modulospredeterminados`.`idNivelDeUsuario` = ".$DatosDelUsuario['idNivelDeUsuario'].")));");

                    if(!empty($ResultadoDeConsulta)){
                        echo '
                            <br>
                            <span class="TituloDelSection"> <i class="fi-rr-apps-add"></i> Modulos agregados al usuario:</span>
                            <div class="CajaDeModulos">
                        ';

                        foreach($ResultadoDeConsulta as $Modulo){
                            echo '
                                <a href="'.$Modulo['nombre'].'.php" class="CartaDeModulo ModuloAgregado">
                                    <div class="EspacioDeLaImagen">
                                        <img src="Imagenes/'.$Modulo['nombreDeImagen'].'" alt="">
                                    </div>
                                    <div class="EspacioNombreDelModulo">
                                        '.$Modulo['nombre'].'
                                    </div>
                                </a>
                            ';   
                        }

                        echo '</div>';
                    }

                    
                ?>
                
            </section>
            
        </div>
    </article>
    <script src="Perfil.js"></script>
</body>
</html>
