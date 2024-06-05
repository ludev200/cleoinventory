<?php
session_start();
include_once('../../Otros/clases.php');
$BaseDeDatos = new conexion();
$Problemas = "";

$Inventario = new inventario();

//print_r($_POST);

$DatosIniciales = array(
    'MostrarProductosAgotados' => '',
    'descripcion' => '',
    'CambiosListados' => ''
);


if($_POST)    {
    $DatosIniciales = array(
        'MostrarProductosAgotados' => ((isset($_POST['MostrarProductosAgotados']))?'true':''),
        'descripcion' => ((isset($_POST['descripcion']))?$_POST['descripcion']:''),
        'CambiosListados' => ((isset($_POST['CambiosListados']))?$_POST['CambiosListados']:'')
    );

    if(isset($_POST['Guardar'])){
        try{
            $id = $Inventario->CrearNuevoAjusteDeInventario($_POST, 3);
            
        }catch(Exception $Error){
            $Problemas = $Error->getMessage();
        }

        if(empty($Problemas)){
            header('Location: ../Cambios/?descripcion='.$id);
        }
    }
}

$AlmacenesConProductos = $Inventario->ObtenerAlmacenesConProductos($DatosIniciales['MostrarProductosAgotados']);
?>


<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $GLOBALS['nombreCorto'];?>: Ajuste de inventario</title>
    <link rel="stylesheet" href="estilos_Ajuste.css">
    <?php include('../../Otros/cabecera_N3.php');?>
    <div id="CajaDeBarras">
        <a href="../../" class="Barra">
            <p>Menú</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="../" class="Barra">
            <p>Inventario</p>
            <div class="Cuadrito" href="x"></div>
        </a>
        <a href="" class="Barra">
            <p>Ajuste</p>
            <div class="Cuadrito" href=""></div>
        </a>
    </div>
    <div id="PanelDeCambiosARealizar" class="EspacioDeLosCambiosARealizar">
        <div class="DatosDelAjuste">
            <span class="Titulo">AJUSTE DE INVENTARIO #8</span>
            <div class="xd"> <i class="fi-rr-text"></i> Descripción:</div>
            <textarea autocomplete="off" form="FormularioDeInventario" placeholder="Añade una descripción con el motivo de los cambios realizados. " name="descripcion" id="textarealol" cols="30" rows="3"><?php echo $DatosIniciales['descripcion'];?></textarea>
        </div>
        <div class="DivDeLosCambios">
            <span class="xd"> <i class="fi-rr-exchange"></i> Cambios a realizar: </span>
            <input HIDDEN form="FormularioDeInventario" name="CambiosListados" type="text" id="InputDeCambios" value="<?php echo $DatosIniciales['CambiosListados'];?>">
            <div class="TablaDeCambios">
                <div id="CajaDeCambiosListados" class="EspacioDeRowDeCambio mostly-customized-scrollbar">
                    <!--Aqui actua NuevoAjuste.js-->
                </div>
            </div>
        </div>
        <div class="BotonesDelAjuste">
            <a href="../"> <i class="fi-rr-arrow-left"></i> Salir sin guardar</a>
            <button id="botonFakeSubmit"> <i class="fi-sr-bookmark"></i> Guardar cambios</button>
            <button HIDDEN id="botonSubmit" form="FormularioDeInventario" name="Guardar"> <i class="fi-sr-bookmark"></i> Guardar cambios</button>
        </div>
    </div>
    <label id="ModalDeErroresDelPOST" class="ModalDeError">
        <input <?php echo ((empty($Problemas))?"":"checked");?> type="checkbox" name="" id="CheckMostrarModalDeError">
        <div class="TarjetaDeWarning">
            <img class="TextoCentro" src="../../Imagenes/TrianguloDeAdvertencia.png" alt="">
            <b class="TextoCentro">No se puede guardar</b>
            <p>Se han encontrado errores que impiden continuar. Rectifique e intentelo de nuevo.</p>
            <b class="TextoIzquierda">Errores:</b>
            <div class="TextoCentro CajaDeErrores">
                <?php 
                    if(!empty($Problemas)){
                        foreach(explode("¿", $Problemas) as $ErrorDeFormato){
                            echo "<span>".$ErrorDeFormato.((empty($ErrorDeFormato))?"":".")."</span>";
                        }   
                    }
                ?>
            </div>
        </div>
    </label>
    <article>
        <span class="fi-sr-garage TituloDeSectionDelArticle"> PRODUCTOS POR ALMACÉN:</span>
        <form id="FormularioDeInventario" action="" method="post">
            <label class="LabelDeMostrarMas">
                <?php
                    echo 
                        (($DatosIniciales['MostrarProductosAgotados'])?'Mostrar':'Ocultar').' productos no almacenados
                        <input hidden '.(($DatosIniciales['MostrarProductosAgotados'])?'checked':'').' name="MostrarProductosAgotados" id="InputMostrarProductosAgotados" type="checkbox">
                        <i id="IconoDeOjo" class="'.(($DatosIniciales['MostrarProductosAgotados'])?'fi-rr-eye':'fi-rr-eye-crossed').'"></i>
                    ';
                ?>
            </label>
        </form>
        <?php
        if(empty($AlmacenesConProductos)){
            echo '
                <div class="DivNoAlmacen">No hay ningun almacén registrado.</div>
            ';
        }else{
            foreach($AlmacenesConProductos as $Almacen){
                if($Almacen['idEstado'] == 52){
                    echo '
                        <div class="CartaDeAlmacenApagao">
                            <span class="NombreDeAlmacen">'.$Almacen['nombre'].' <span class="Grisesito">- '.$Almacen['direccion'].'</span></span>
                            <div class="LetreroAlmacenApagao">
                                No puede modificar el inventario de este almacén porque se encuentra inactivo.
                                <a href="../../">¿Desea cambiar el estado de este almacén?</a>
                            </div>
                        </div>
                    ';
                }else{
                    if(empty($Almacen['productosAlmacenados'])){
                        echo '
                            <div class="CartaDeAlmacen">
                                <span class="NombreDeAlmacen">'.$Almacen['nombre'].' <span class="Grisesito">- '.$Almacen['direccion'].'</span></span>
                                <div class="LetreroAlmacenApagao">
                                    Este almacén no contiene ningun producto.
                                    <label for="InputMostrarProductosAgotados">Ver los productos no almacenados</label>
                                </div>
                            </div>
                        ';
                    }else{
                        echo '
                            <div class="CartaDeAlmacen" id="CartaAlmacen-'.$Almacen['idAlmacen'].'">
                                <span class="NombreDeAlmacen">'.$Almacen['nombre'].' <span class="Grisesito">- '.$Almacen['direccion'].'</span></span>
                                <div class="EspacioDeProductosDeAlmacen" id="ContenedorDeAlmacen-'.$Almacen['idAlmacen'].'">';

                        foreach($Almacen['productosAlmacenados'] as $ProductoAlmacenado){
                            $IDDelInput = "'InputINT-".$Almacen['idAlmacen']."x".$ProductoAlmacenado['id']."'";
                            echo '
                                    <row>
                                        <celda class="Celda_Imagen">
                                            <img src="../../Imagenes/Productos/'.((empty($ProductoAlmacenado['ULRImagen']))?'ImagenPredefinida_Productos.png':$ProductoAlmacenado['ULRImagen']).'" alt="">
                                        </celda>
                                        <celda class="Celda_ID">'.$ProductoAlmacenado['id'].'</celda>
                                        <celda class="Celda_Nombre">'.$ProductoAlmacenado['nombre'].'</celda>
                                        <celda class="Celda_Cantidad">
                                            <i class="fi-rr-cross-small"></i>
                                            <input maxlength="9" categoria="'.$ProductoAlmacenado['idCategoria'].'" onkeypress="return '.(($ProductoAlmacenado['idCategoria'] == 1)?'SoloInt':'SoloInt').'(event, '.$IDDelInput.')" id="InputINT-'.$Almacen['idAlmacen'].'x'.$ProductoAlmacenado['id'].'" class="InputINT" onClick="this.select();" type="text" value="'.$ProductoAlmacenado['existencia'].'">
                                        </celda>
                                        <celda class="Celda_Botones">
                                            <button id="BotonRehacer-'.$Almacen['idAlmacen'].'x'.$ProductoAlmacenado['id'].'" class="BotonRehacer BotonInactivo"> <i class="fi-rr-undo"></i> </button>
                                            <button id="BotonAgregar-'.$Almacen['idAlmacen'].'x'.$ProductoAlmacenado['id'].'" class="BotonAgregarCambio BotonInactivo" title="Listar cambio"> <i class="fi-rr-redo"></i> </button>
                                        </celda>
                                    </row>
                            ';
                        }

                        echo '
                                </div>
                            </div>
                        ';
                    }
                }                
            }
        }
        ?>
    </article>
    <?php include '../../ipserver.php';?>
    <script src="../../Otros/sweetalert.js"></script>
    <script src="NuevoAjuste.js"></script>
</body>
</html>