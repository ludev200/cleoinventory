<?php
include('../Otros/clases.php');

$public = new publicFunctions();


if(isset($_POST['color'])){
    switch($_POST['color']){
        case '1':
            $VinotintoOscuro = "32,56,100";
            $Vinotinto = "31,78,121";
            $VinotintoClarito = "46,117,182";
            $RositaOscuro = "150,190,250";
            $Rosita = "189,215,238";
        break;

        case '2':
            $VinotintoOscuro = "162,19,37";
            $Vinotinto = "188,28,72";
            $VinotintoClarito = "215,54,99";
            $RositaOscuro = "240,210,210";
            $Rosita = "247,229,233";
        break;

        
        case '3':
            $VinotintoOscuro = "198,68,16";
            $Vinotinto = "237,125,49";
            $VinotintoClarito = "254,168,47";
            $RositaOscuro = "250,203,98";
            $Rosita = "251,216,137";
        break;
        

        case '4':
            $VinotintoOscuro = "54,110,66";
            $Vinotinto = "84,130,53";
            $VinotintoClarito = "133,189,95";
            $RositaOscuro = "168, 199, 148";
            $Rosita = "197,224,180";
        break;
    }
    $file = fopen('../Otros/colores.css', 'w');

    
    $content = ":root {
    --VinotintoOscuro: rgb($VinotintoOscuro);
    --Vinotinto: rgb($Vinotinto);
    --VinotintoClarito: rgb($VinotintoClarito);
    --RositaOscuro: rgb($RositaOscuro);
    --Rosita: rgb($Rosita);
}";
    
    
    fwrite($file, $content);
    fclose($file);
    header('Location: ../Sistema/#TopeColores');
}

if(isset($_POST['photo'])){
    print_r($_POST);
    print_r($_FILES[$_POST['photo']]);
    $gottenPhoto = $_FILES[$_POST['photo']];
    $pieces = explode('/', $gottenPhoto['type']);
    
    $newName = $_POST['photo']."_".time().".".$pieces[1];
    move_uploaded_file($gottenPhoto['tmp_name'], "../Imagenes/Sistema/$newName");

    switch($_POST['photo']){
        case 'row_companyLogo': $public->setCompanyLogo($newName);
        break;

        case 'row_companySeal': $public->setCompanySeal($newName);
        break;

        case 'row_companyBossSing': $public->setCompanyBossSing($newName);
        break;

        default: echo 'ERROR: NO SE RECONOCE EL ID DE PHOTO';
    }

    header('Location: ../Sistema/');



    
}


$securityQuestions = $public->getSecurityQuestions();
$units = $public->getUnits();


function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r"));  
    }

    else {
        exec($cmd . " > /dev/null &");   
    }
}


if(isset($_GET['openCA'])){
    
    $rutaActual = explode('\\', __FILE__);
    $rutaActual[count($rutaActual) - 1] = 'CleoAssistent.jar';
    $counter=0;
    foreach($rutaActual as $dir){
        
        if($rutaActual[$counter] == 'Sistema'){
            $rutaActual[$counter] = 'Assistent';
        }
        $counter++;
    }

    $nuevaRuta = implode('\\', $rutaActual);
    execInBackground($nuevaRuta);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Sistema</title>
    <link rel="stylesheet" href="sistema.css">
    <?php include('../Otros/cabecera_N2.php');?>
    <nav class="CajaDeBarras">
        <a class="BarraDeNavegacion" href="../">
            <p>Menú</p>
            <div class="CuadritoInclinado"></div>
        </a>
        <a class="BarraDeNavegacion" href="">
            <p>Sistema</p>
            <div class="CuadritoInclinado"></div>
        </a>
    </nav>
    <div class="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <article>
    <span id="TopeDeBackUp" class="fi-rr-database test1"> RESTAURACIÓN Y COPIAS DE SEGURIDAD:</span>
    <section>
        <form method="get">
            <button type="<?php echo (isset($nuevaRuta)? 'button':'submit');?>" id="openCAButton" class="<?php echo (isset($nuevaRuta)? '':'hovershadow');?>" name="openCA" value="true">
                <img src="../Imagenes/Sistema/Cleo_4.png">
                <div id="<?php echo (isset($nuevaRuta)? 'CLEOATButton':'');?>">Asistente Técnico</div>
            </button>
        </form>
    </section>
    <br>

    <span id="TopeDocumentos" class="fi-rr-file test1"> IDENTIFICACIÓN DE DOCUMENTOS:</span>
        <section>
            <div id="listaEditable">
                <div class="row" id="row_companyRif">
                    <div class="hideInActive">
                        <b>RIF</b>
                        <span><?php echo $public->getCompany_rif();?></span>
                        <button class="editData_button hovershadow" idRow="row_companyRif" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>RIF</small>
                        <input type="text" idRow="row_companyRif">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_companyRif"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_companyRif">Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_companyName">
                    <div class="hideInActive">
                        <b>Nombre de la empresa</b>
                        <span><?php echo $public->getCompany_name();?></span>
                        <button class="editData_button hovershadow" idRow="row_companyName" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Nombre de la empresa</small>
                        <input type="text" idRow="row_companyName">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_companyName"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_companyName">Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_companyAddress">
                    <div class="hideInActive">
                        <b>Dirección</b>
                        <span><?php echo $public->getCompany_address();?></span>
                        <button class="editData_button hovershadow" idRow="row_companyAddress" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Dirección</small>
                        <input type="text" idRow="row_companyAddress">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_companyAddress"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_companyAddress">Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_companyCityData">
                    <div class="hideInActive">
                        <b>Ciudad, estado y zona postal</b>
                        <span><?php echo $public->getCompany_cityData();?></span>
                        <button class="editData_button hovershadow" idRow="row_companyCityData" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Ciudad, estado y zona postal</small>
                        <input type="text" idRow="row_companyCityData">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_companyCityData"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_companyCityData">Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_companyPhone">
                    <div class="hideInActive">
                        <b>Teléfono</b>
                        <span><?php echo $public->getCompany_phone();?></span>
                        <button class="editData_button hovershadow" idRow="row_companyPhone" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Teléfono</small>
                        <input type="text" idRow="row_companyPhone">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_companyPhone"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_companyPhone">Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_companyEmail">
                    <div class="hideInActive">
                        <b>Correo</b>
                        <span><?php echo $public->getCompany_email();?></span>
                        <button class="editData_button hovershadow" idRow="row_companyEmail" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Correo</small>
                        <input type="text" idRow="row_companyEmail">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_companyEmail"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_companyEmail">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            <form class="listaImagenEditable" enctype='multipart/form-data' method="post">
                <div class="row" id="row_companyLogo">
                    <div class="hideInActive">
                        <b>Logo de la empresa</b>
                        <img src="../Imagenes/Sistema/<?php echo $public->getCompanyLogo();?>" alt="">
                        <button class="editPhoto_button hovershadow" idrow="row_companyLogo" title="Cambiar" type="button"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Logo de la empresa</small>
                        <div class="imgAndData">
                            <img src="../Imagenes/Sistema/<?php echo $public->getCompanyLogo();?>" alt="">
                            <div class="data">
                                <label class="hovershadow">
                                    <i class="fi-rr-folder-upload"></i>
                                    <span>Subir imagen</span>
                                    <input hidden name="row_companyLogo" type="file" idRow="row_companyLogo" accept="image/png, image/jpg, image/jpeg"><br>
                                </label>
                                <div class="buttonsContainer">
                                    <button class="back hovershadow" idRow="row_companyLogo" type="button"><i class="fi-rr-angle-small-left"></i></button>
                                    <button name="photo" value="row_companyLogo" class="save hovershadow" idRow="row_companyLogo">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_companySeal">
                    <div class="hideInActive">
                        <b>Sello de la empresa</b>
                        <img src="../Imagenes/Sistema/<?php echo $public->getCompanySeal();?>" alt="">
                        <button class="editPhoto_button hovershadow" idrow="row_companySeal" title="Cambiar" type="button"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Sello de la empresa</small>
                        <div class="imgAndData">
                            <img src="../Imagenes/Sistema/<?php echo $public->getCompanySeal();?>" alt="">
                            <div class="data">
                                <label class="hovershadow">
                                    <i class="fi-rr-folder-upload"></i>
                                    <span>Subir imagen</span>
                                    <input hidden name="row_companySeal" type="file" idRow="row_companySeal" accept="image/png, image/jpg, image/jpeg"><br>
                                </label>
                                <div class="buttonsContainer">
                                    <button class="back hovershadow" idRow="row_companySeal" type="button"><i class="fi-rr-angle-small-left"></i></button>
                                    <button name="photo" value="row_companySeal" class="save hovershadow" idRow="row_companySeal">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_companyBossSing">
                    <div class="hideInActive">
                        <b>Firma del representante de la empresa</b>
                        <img src="../Imagenes/Sistema/<?php echo $public->getCompanyBossSing();?>" alt="">
                        <button class="editPhoto_button hovershadow" idrow="row_companyBossSing" title="Cambiar" type="button"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Firma del representante de la empresa</small>
                        <div class="imgAndData">
                            <img src="../Imagenes/Sistema/<?php echo $public->getCompanyBossSing();?>" alt="">
                            <div class="data">
                                <label class="hovershadow">
                                    <i class="fi-rr-folder-upload"></i>
                                    <span>Subir imagen</span>
                                    <input hidden name="row_companyBossSing" type="file" idRow="row_companyBossSing" accept="image/png, image/jpg, image/jpeg"><br>
                                </label>
                                <div class="buttonsContainer">
                                    <button class="back hovershadow" idRow="row_companyBossSing" type="button"><i class="fi-rr-angle-small-left"></i></button>
                                    <button name="photo" value="row_companyBossSing" class="save hovershadow" idRow="row_companyBossSing">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <br>

        <span id="OpcionesExtras" class="fi-rr-apps-add test1"> OPCIONES EXTRAS:</span>
        <section>
            <div id="listaEditable">
                <div class="row" id="row_nationalCurrencyName">
                    <div class="hideInActive">
                        <b>Nombre de moneda nacional</b>
                        <span><?php echo $public->getNationalCurrency_name();?></span>
                        <button class="editData_button hovershadow" idRow="row_nationalCurrencyName" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Nombre de moneda nacional</small>
                        <input type="text" idRow="row_nationalCurrencyName">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_nationalCurrencyName"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_nationalCurrencyName">Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="row" id="row_nationalCurrencySimbol">
                    <div class="hideInActive">
                        <b>Símbolo de moneda nacional</b>
                        <span><?php echo $public->getNationalCurrency_simbol();?></span>
                        <button class="editData_button hovershadow" idRow="row_nationalCurrencySimbol" title="Modificar"><i class="fi-rr-pencil"></i></button>
                    </div>
                    <div class="showInActive">
                        <small>Nombre de moneda nacional</small>
                        <input type="text" idRow="row_nationalCurrencySimbol">
                        <div class="buttonsContainer">
                            <button class="back hovershadow" idRow="row_nationalCurrencySimbol"><i class="fi-rr-angle-small-left"></i></button>
                            <button class="save hovershadow" idRow="row_nationalCurrencySimbol">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="selectsEditables">
                <div class="row" id="questions">
                    <b>Preguntas de seguridad</b>
                    <div class="showOnEdit">
                        <span>Modificar pregunta de seguridad</span>
                        <div class="questionMarks">
                            <span>¿</span>
                            <input id="editQuestionInput" type="text" style="width: 200px; border-right: none; border-left: none; border-top: none; border-radius: 0;" maxlength="25">
                            <span>?</span>
                        </div>
                        <div class="buttonsContainer">
                            <button id="hideEditModeButton" class="back hovershadow"><i class="fi-rr-angle-small-left"></i></button>
                            <button id="saveUpdateQuestionButton" class="save hovershadow">Guardar</button>
                        </div>
                    </div>
                    <div class="selectButtoned hideOnEdit">
                        <select>
                            <?php
                            foreach($securityQuestions as $row){
                                echo '<option value="'.$row['id'].'">'.$row['pregunta'].'</option>';
                            }
                            ?>
                        </select>
                        
                        <button id="showQuestionEditModeButton" class="hovershadow"><i class="fi fi-rr-pencil"></i></button>
                        <button id="deleteQuestionButton" class="hovershadow"><i class="fi fi-rr-trash"></i></button>
                        <div class="addNewDiv">
                            <div class="hideInActive">
                                <span id="showAddNewQuestionButton" class="showAddNewOptionButton">¿Quieres añadir una nueva pregunta?</span>
                            </div>
                            <div class="showInActive">
                                <div class="addNewForm">
                                    <b>AÑADIR PREGUNTA DE SEGURIDAD</b>
                                    <p>Esta pregunta podra ser seleccionada por los usuarios como una de sus preguntas de seguridad.</p>
                                    <div class="questionMarks">
                                        <span>¿</span>
                                        <input id="newQuestionInput" type="text" maxlength="25">
                                        <span>?</span>
                                    </div>
                                    <div class="buttonsContainer">
                                        <button class="back hideAddNewQuestionButton hovershadow"><i class="fi-rr-angle-small-left"></i></button>
                                        <button id="saveNewQuestionButton" class="save hovershadow">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="units">
                    <b>Unidades de medida</b>
                    <div class="showOnEdit">
                        <span>Modificar unidad de medida</span>
                        <div class="span_input">
                            <span>Nombre:</span>
                            <input id="editNameUnitInput" type="text" maxlength="15">
                        </div>
                        <div class="span_input">
                            <span>Símbolo:</span>
                            <input id="editSimbolUnitInput" type="text" maxlength="5">
                        </div>
                        <div class="buttonsContainer">
                            <button id="hideEditModeButton" class="back hovershadow"><i class="fi-rr-angle-small-left"></i></button>
                            <button id="saveUpdateUnitButton" class="save hovershadow">Guardar</button>
                        </div>
                    </div>
                    <div class="selectButtoned hideOnEdit">
                        <select id="">
                            <?php
                            foreach($units as $row){
                                echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
                            }
                            ?>
                        </select>
                        <button id="showUnitEditModeButton" class="hovershadow"><i class="fi fi-rr-pencil"></i></button>
                        <button id="deleteUnitButton" class="hovershadow"><i class="fi fi-rr-trash"></i></button>
                        <div class="addNewDiv">
                            <div class="hideInActive">
                                <span id="showAddNewQuestionButton" class="showAddNewOptionButton">¿Quieres añadir una nueva unidad de medida?</span>
                            </div>
                            <div class="showInActive">
                                <div class="addNewForm">
                                    <b>AÑADIR UNIDAD DE MEDIDA</b>
                                    <p>Las unidades de medida se utilizan para cuantificar los productos consumibles.</p>
                                    <div class="span_input">
                                        <span>Nombre:</span>
                                        <input id="unitNameInput" type="text" maxlength="15">
                                    </div>
                                    <div class="span_input">
                                        <span>Símbolo:</span>
                                        <input id="unitSymbolInput" type="text" maxlength="5">
                                    </div>
                                    <div class="buttonsContainer">
                                        <button class="back hideAddNewQuestionButton hovershadow"><i class="fi-rr-angle-small-left"></i></button>
                                        <button id="saveNewUnitButton" class="save hovershadow">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <br>

        <span id="TopeColores" class="fi-rr-palette test1"> COLORES DEL SISTEMA:</span>
        <section>
            <form method="post" id="colorsSelector">
                <button name="color" value="1">
                    <div class="muestra"></div>
                    <b>Azul Índigo</b>
                    <span>
                        <i class="fi-rr-paint-roller"></i>
                    </span>
                </button>
                <button name="color" value="2">
                    <div class="muestra"></div>
                    <b>Vinotinto</b>
                    <span>
                        <i class="fi-rr-paint-roller"></i>
                    </span>
                </button>
                <button name="color" value="3">
                    <div class="muestra"></div>
                    <b>Naranja</b>
                    <span>
                        <i class="fi-rr-paint-roller"></i>
                    </span>
                </button>
                <button name="color" value="4">
                    <div class="muestra"></div>
                    <b>Verde Manzana</b>
                    <span>
                        <i class="fi-rr-paint-roller"></i>
                    </span>
                </button>
            </form>
        </section>
    </article>
    <aside>
        <div class="contenidoDeLaBarraLateral">
            <div class="EspacioDeLaImagen">
                <img src="../Imagenes/iconoDelMenu_Sistema.png" alt="">
                <b>Sistema</b>
            </div>
            <a href="#TopeDeBackUp"> <i class="fi-rr-database"></i> Restauración</a>
            <a href="#TopeDocumentos"> <i class="fi-rr-file"></i> Documentos</a>
            <a href="#OpcionesExtras"> <i class="fi-rr-apps-add"></i> Ociones extras</a>
            <a href="#TopeColores"> <i class="fi-rr-palette"></i> Paleta de colores</a>
        </div>
    </aside>
    <?php include '../ipserver.php';?>
    <script src="../Otros/sweetalert.js"></script>
    <script src="modoEdicion.js"></script>
    <script src="sistema.js"></script>
</body>
</html>