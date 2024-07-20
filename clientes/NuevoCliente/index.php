<?php
include('../../Otros/clases.php');
print_r($_POST);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['nombreDelSoftware'];?>: Nuevo cliente</title>
<link rel="stylesheet" href="estilos_nuevoCliente.css">
<?php include('../../Otros/cabecera_N3.php');?>
<?php 
//Inicializo los datos a mostrar en los inputs
$DatosAMostrar = array(
    "tipoDeDocumento" => "",
    "rif" => "",
    "nombre" => "",
    "simbolo1" => "",
    "simbolo2" => "",
    
    "correo" => "",
    "direccion" => "",
    "ULRImagen" => "",
    'phone' => '',
    'phone2' => ''
);

//Reviso si hay proveedor en borrador; si lo hay, cargo el objeto
$listaDeBorrador = $BaseDeDatos->consultar("SELECT * FROM `clientes` WHERE `idEstado` = 12");
$ExisteBorrador = !empty($listaDeBorrador);
if($ExisteBorrador){
    $ClienteEnBorrador = new cliente($listaDeBorrador[0]['rif']);
    
}


if($_POST){
    //Almaceno los datos recibidos por el formulario
    $DatosAMostrar = array(
        "tipoDeDocumento" => $_POST['tipoDeDocumento'],
        "rif" => $_POST['rif'],
        "nombre" => $_POST['nombre'],
        "simbolo1" => ((isset($_POST['ModoInter1']))?"+":"-"),
        "simbolo2" => ((isset($_POST['ModoInter2']))?"+":"-"),
        
        "correo" => $_POST['correo'],
        "direccion" => $_POST['direccion'],
        "ULRImagen" => "",
        'phone' => $_POST['phone'],
        'phone2' => $_POST['phone2']
    );
    

//Si existe en borrador, lo actualizo; sino, lo creo
    if(isset($_POST['guardar'])) {
        if($ExisteBorrador){
            try{
                $Problemas = $ClienteEnBorrador->ActualizarDatos($_POST, $_FILES, 11);
            }catch(Exception $e){
                $Problemas = $e->getMessage();
            }
            
            if(empty($Problemas)){
                header("Location: ../");
            }
        }else{
            $ClienteFantasma = new cliente(0);
            try{
                $Problemas = $ClienteFantasma->CrearNuevo($_POST, $_FILES,11);
            }catch(Exception $e){
                $Problemas = $e->getMessage();
            }

            if(empty($Problemas)){
                header("Location: ../");
            }
        }
    }
   
//Si existe en borrador, lo actualizo; sino, lo creo    
    if(isset($_POST['borrador'])) {
        if($ExisteBorrador){
            try{
                $Problemas = $ClienteEnBorrador->ActualizarDatos($_POST, $_FILES, 9);
            }catch(Exception $e){
                $Problemas = $e->getMessage();
            }
            
            if(empty($Problemas)){
                header("Location: ../");
            }
        }else{
            $ClienteFantasma = new cliente(0);
            try{
                $Problemas = $ClienteFantasma->CrearNuevo($_POST, $_FILES,12);
            }catch(Exception $e){
                $Problemas = $e->getMessage();
            }

            if(empty($Problemas)){
                header("Location: ../");
            }
        }
    }
}
?>
<label id="VentanaDeErrores" <?php echo (empty($Problemas)?"hidden":"")?> class="modal">
    <input hidden <?php echo (empty($Problemas)?"":"checked")?> type="checkbox" name="VisibilidadModal" id="VisibilidadModal">
    <div class="CentroHorizontal">
        <div class="CentroVertical">
            <div class="CuerpoDeModal">
                
                <img class="TextoCentro" src="../../Imagenes/TrianguloDeAdvertencia.png" alt="">
                <b class="TextoCentro">No se puede guardar</b>
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
        </div>
    </div>
</label>
    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../" class="Barra">
            <p>Clientes</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p>Nuevo</p>
            <div class="Cuadrito" href=""></div>
        </a>
    </div>
    <div id="CajaDeFecha">
        <p><?php $Tiempo=new AsistenteDeTiempo(); echo $Tiempo->FechaActual_USER(); ?></p>
    </div>
    <form autocomplete="off" id="CajaContenido" method="post" enctype="multipart/form-data">
        <div class ="seccionDeContenido" id="CajaDeInputs">
        <div class="SubtituloCentral"> <i class="fi-sr-clipboard-list Arreglito"></i> INFORMACIÓN PERSONAL</div>
            <div class="FlexHorizontal">
                <label id="ContenedorDeLaImagen">
                    <input hidden accept="image/*" type="file" name="ULRImagen" id="inputHidden">
                    <img id="Spoiler" src="../../Imagenes/Clientes/<?php echo ((empty($DatosDelClienteEnBorrador['ULRImagen']))?"ImagenPredefinida_Clientes.png":$DatosDelClienteEnBorrador['ULRImagen'])?>" alt="">
                    <div id="CajaFantasma">
                        <img id="" src="../../Imagenes/ImagenSinDefinir.png" alt="">
                    </div>
                </label>
                <div class="FlexVertical Width400px">
                    <b>RIF o Cédula:</b>
                    <div class="FlexHorizontal">
                        <select name="tipoDeDocumento" class="" name="" id="InputDeTipoDoc">
                            <option <?php echo (($DatosAMostrar['tipoDeDocumento']=="V")?"selected":"")?> value="V">V</option>
                            <option <?php echo (($DatosAMostrar['tipoDeDocumento']=="J")?"selected":"")?> value="J">J</option>
                            <option <?php echo (($DatosAMostrar['tipoDeDocumento']=="E")?"selected":"")?> value="E">E</option>
                            <option <?php echo (($DatosAMostrar['tipoDeDocumento']=="G")?"selected":"")?> value="G">G</option>
                        </select>
                        <input id="Inputrif" value="<?php echo $DatosAMostrar['rif'] ?>" maxlength="9" name="rif" onkeypress="return validateRifAndCedula(event, this)" class="CampoDeEntrada Input200px">
                    </div>
                    <b>Razón social:</b>
                    <input id="InputDeNombre" value="<?php echo $DatosAMostrar['nombre'] ?>" name="nombre" class="CampoDeEntrada" type="text">
                </div>
            </div>
            <div class="SubtituloCentral"> <i class=" fi-sr-circle-phone Arreglito"></i> INFORMACIÓN DE CONTACTO</div>
            <div class="inputsContainer">
                <div style="margin-bottom: 10px;">
                    <b>Telefono 1:</b> <small style="color: gray;">(Opcional)</small>
                </div>
                <input name="phone" id="phoneInput" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="<?php echo $DatosAMostrar['phone'] ?>">


                <div style="margin-bottom: 10px;">
                    <b>Telefono 2:</b> <small style="color: gray;">(Opcional)</small>
                </div>
                <input name="phone2" id="phone2Input" onkeypress="return valida_phoneFormat(this, event)" type="text" style="width: 200px;" value="<?php echo $DatosAMostrar['phone2'] ?>">
            </div>

            <div class="lolo">
                
                <div class="Width400px">
                    <div style="margin-bottom: 10px;">
                        <b>Correo:</b> <small style="color: gray;">(Opcional)</small>
                    </div>
                    <input id="InputCorreo" value="<?php echo $DatosAMostrar['correo'] ?>" name="correo" class="CampoDeEntrada" type="email">
                    <div style="margin-bottom: 10px;">
                        <b>Dirección:</b> <small style="color: gray;">(Opcional)</small>
                    </div>
                    <input id="InputDireccion" value="<?php echo $DatosAMostrar['direccion'] ?>" name="direccion" class="CampoDeEntrada" type="text">
                </div>
            </div>
        </div>
        
        <div class="coolFinalButtons">
            <a href="../" class="hovershadow">Salir</a>
            <button name="guardar" id="validateFormButton" class="hovershadow">Guardar</button>
        </div>
        
    </form>
    <aside id="BarraLateral">
        <div id="contenidoDeLaBarraLateral">
            <div id="EspacioDeLaImagen">
                <img src="../../Imagenes/iconoDelMenu_Clientes.png" alt="">
                <b>Clientes</b>
            </div>
            <a href="../../Ayuda/#74" target="_blank"><i class="fi-rr-interrogation"></i> Obtener ayuda</a>
            <a href="../" id=""> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
            <a HIDDEN href="../../Otros/funcion_EliminarClientesEnBorrador.php" id="VaciarFormulario" class="BotonesLaterales" > <i class="fi-sr-broom"></i> Vaciar formulario</a>
            <label HIDDEN for="BotonBorrador" class="BotonesLaterales" > <i class="fi-sr-folder-minus"></i> Guardar en borrador</label>
            
        </div>
        
    </aside>
    <?php include '../../ipserver.php';?>
    <script src="NuevoCliente.js"></script>
    
</body>
</html>