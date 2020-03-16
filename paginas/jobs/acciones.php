<?php
header("Access-Control-Allow-Origin:*");
$max_salida=10; // Previene algun posible ciclo infinito limitando a 10 los ../
$ruta_raiz=$ruta="";
while($max_salida>0){
  if(@is_file($ruta.".htaccess")){
    $ruta_raiz=$ruta; //Preserva la ruta superior encontrada
    break;
  }
  $ruta.="../";
  $max_salida--;
}
include_once($ruta_raiz.'clases/define.php');
include_once($ruta_raiz.'clases/funciones_generales.php');
include_once($ruta_raiz.'clases/Conectar.php');

function crearMarca(){
  $resp = array();
  $db = new Bd();
  $db->conectar();

  $marca = trim($_REQUEST['nombre']);

  if (validarMarcaNombre($marca) == 0) {
    $db->sentencia("INSERT INTO marcas (nombre, fecha_creacion, activo) VALUES (:nombre, :fecha_creacion, :activo)", array(":nombre" => $marca,":fecha_creacion" => date('Y-m-d H:i:s'), ":activo" => 1));
    $resp = array("success" => true,
                  "msj" => "La marca <b>"  . $marca . "</b> se ha creado.");
  }else{
    $resp = array("success" => false,
                "msj" => "La marca <b>" . $marca . "</b> ya se encuentra creada.");
  }
  
  $db->desconectar();

  return json_encode($resp);
};

function validarMarcaNombre($nombre, $id = 0){
  $db = new Bd();
  $db->conectar();

  if ($id = 0) {
    $nombreMarca = $db->consulta("SELECT * FROM marcas WHERE nombre = :nombre", array(":nombre" => $nombre));
  }else{
    $nombreMarca = $db->consulta("SELECT * FROM marcas WHERE id != :id AND nombre = :nombre", array(":nombre" => $nombre, ":id" => $id));
  }


  $db->desconectar();

  return json_encode($nombreMarca['cantidad_registros']);
}

function ListaMarcas(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $marcas = $db->consulta("SELECT * FROM marcas WHERE activo = 1");

  if ($marcas['cantidad_registros'] > 0) {
    $resp['success'] = true;
    $resp['msj'] = $marcas;
  } else {
    $resp['success'] = false;
    $resp['msj'] = 'No existen datos.';
  }

  $db->desconectar();

  return json_encode($resp);
}

function eliminarMarca(){
  $db = new Bd();
  $db->conectar();

  $db->sentencia("UPDATE marcas SET activo = 0 WHERE id = :id", array(":id" => $_REQUEST['idMarca']));

  $db->desconectar();

  return json_encode(1);
}

function editarMarca(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $nombre = trim($_REQUEST['nombre']);

  if (validarMarcaNombre($nombre, $_REQUEST['idMarca']) == 0) {
    $db->sentencia("UPDATE marcas SET nombre = :nombre WHERE id = :id", array(":nombre" => $nombre, ":id" =>$_REQUEST['idMarca']));

    $resp = array("success" => true,
                  "msj" => "Se  ha actualizado el nombre");
  }else{
    $resp = array('success' => false,
                  'msj' => "El nombre no se puede utilizar.</b>");
  }

  $db->desconectar();

  return json_encode($resp);
}

function selectTecnologia(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $sql = $db->consulta("SELECT * FROM tecnologias WHERE activo = 1 AND fk_tecnologia = :fk_tecnologia", array(":fk_tecnologia" => $_REQUEST['tecnologia']));

  if ($sql['cantidad_registros'] > 0) {
    $resp = array("success" => true,
                  "msj" => $sql);
  }else{
    $resp = array("success" => false,
                  "msj" => "No se han encontrado datos.");
  }

  $db->desconectar();

  return json_encode($resp);
}

function crearReferencia(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  if (isset($_REQUEST['referencia']) && isset($_REQUEST['tecnologia'])) {
    $referencia = trim($_REQUEST['referencia']);

    if (validarNombreReferencia($referencia) == 0) {
      $db->sentencia("INSERT INTO referencias (referencia, fecha_creacion, fk_marca) VALUES (:referencia, :fecha_creacion, :fk_marca)", array(":referencia" => $referencia, ":fecha_creacion" => date("Y-m-d H:i:s"), ":fk_marca" => $_REQUEST['fk_marca']));

      $idReferencia = $db->consulta('SELECT * FROM referencias WHERE fk_marca = :fk_marca AND referencia = :referencia', array(":fk_marca" => $_REQUEST['fk_marca'], ":referencia" => $referencia));

      if ($idReferencia['cantidad_registros'] == 1) {

        foreach($_POST['tecnologia'] as $tec) {
          $db->sentencia("INSERT INTO referencias_tecnologias (fk_referencia, fk_tecnologia, fecha_creacion, activo) VALUES (:fk_referencia, :fk_tecnologia, :fecha_creacion, :activo)", array(":fk_referencia" => $idReferencia[0]['id'], ":fk_tecnologia" => $tec, ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1));
        }

        $resp = array("success" => true,
                    "msj" => 'Se ha creado la referencia <b>' . $referencia . '</b>');
      }else{
        $resp = array("success" => false,
                    "msj" => 'Error al crear la referencia <b>' . $referencia . '</b>.');
      }

    }else{
      $resp = array("success" => false,
                    "msj" => 'Esta referencia <b>' . $referencia . '</b> ya se encuentra creado.');
    }
  } else {
    $resp = array("success" => false,
                  "msj" => "Algunos campos no se encuentra definidos.");
  }

  $db->desconectar();

  return json_encode($resp);
}

function validarNombreReferencia($referencia){
  $db = new Bd();
  $db->conectar();

  $validar = $db->consulta("SELECT * FROM referencias WHERE referencia = :referencia", array(":referencia" => $referencia));

  $db->desconectar();
  return json_encode($validar['cantidad_registros']);
}

function listaReferencias(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $referencias = $db->consulta("SELECT r.id, r.referencia FROM referencias_tecnologias AS rt INNER JOIN referencias AS r ON r.id = rt.fk_referencia WHERE r.fk_marca = :fk_marca AND rt.fk_tecnologia = :fk_tecnologia", array(":fk_marca" => $_REQUEST['marca'], ":fk_tecnologia" => $_REQUEST['tecnologia']));
  
  if ($referencias['cantidad_registros'] > 0) {
    $resp = array("success" => true,
                  "msj" => $referencias);
  } else {
    $resp = array("success" => false,
                  "msj" => "No se han encontrado referencias relacionadas.");
  }
  
  $db->desconectar();
  return json_encode($resp);
}

function crearPI(){
  $db = new Bd();
  $db->conectar();
  $resp = array();
  $pi = trim($_REQUEST['nombre']);

  if (validarPI($pi) == 0) {
    $db->sentencia("INSERT INTO pi (pi, unidades, fecha_creacion, activo, fk_referencia) VALUES (:pi, :unidades, :fecha_creacion, :activo, :fk_referencia)", array(":pi" => $pi, ":unidades" => $_REQUEST['unidades_pi'], ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1, ":fk_referencia" => $_REQUEST['fk_referencia']));
    $resp = array("success" => true,
                  "msj" => "La PI <b>" . $pi . "</b> se ha creado.");
  } else {
    $resp = array("success" => false,
                  "msj" => "La PI ya se encuentra creada");
  }

  $db->desconectar();

  return json_encode($resp);
}

function validarPI($pi){
  $db = new Bd();
  $db->conectar();

  $sql = $db->consulta("SELECT * FROM pi WHERE pi = :pi", array(":pi" => $pi));

  $db->desconectar();

  return json_encode($sql['cantidad_registros']);
}

function listaPI(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $sql = $db->consulta("SELECT * FROM pi WHERE fk_referencia = :fk_referencia", array(":fk_referencia" => $_REQUEST['fk_referencia']));

  if ($sql["cantidad_registros"] > 0) {
    $resp = array("success" => true,
                  "msj" => $sql);
  } else {
    $resp = array("success" => false,
                  "msj" => "No hay PI creadas");
  }
  $db->desconectar();
  return json_encode($resp);
}

function subirImagenes(){
  $db = new Bd();
  $db->conectar();
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		//Validmos que todos los campos de formulario esten definidos
		if(isset($_FILES["imagenes"]) && isset($_POST['idPIImagen']) && isset($_POST['referenciaImagen']) && isset($_POST['PIImagen']) && isset($_POST['idRefImagen'])){
			$cont=-1;
			$cont1 = 0;
			$cantidad = cantidadArchivos($_POST['idRefImagen'], 2);
			//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
			foreach($_FILES["imagenes"]['tmp_name'] as $key => $tmp_name){
				$tipo_imagen = $_FILES['imagenes']['type'][$key];
				$tamano_imagen = $_FILES['imagenes']['size'][$key];
				//Validamos que el archivo exista
				if ($tipo_imagen == "image/jpeg" || $tipo_imagen == "image/jpg" || $tipo_imagen == "image/png" || $tipo_imagen == "image/gif") {

					//El tamaño maximo es de 10 mb
					if ($tamano_imagen <=10000000) {
						if($_FILES["imagenes"]["name"][$key]) {
							$cantidad++;//Si se valida todo incrementa
							$filename = $_FILES["imagenes"]["name"][$key]; //Obtenemos el nombre original del archivo
							$source = $_FILES["imagenes"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivo

							$directorio = '../../almacenamiento/jobs/' . $_POST['referenciaImagen'] . '/imagenes'; //Declaramos un  variable con la ruta donde guardaremos los archivos

							//Validamos si la ruta de destino existe, en caso de no existir la creamos
							if(!file_exists($directorio)){
								// Para crear una estructura anidada se debe especificar
								// el parámetro $recursive en mkdir().
								if(!mkdir($directorio, 0777, true)) {
									die('Fallo al crear las carpetas...');
								}
							}

							//Abrimos el directorio de destino
							$dir=opendir($directorio);
							//Obtenemos la extension del archivo para agregarla al a final
							$info = new SplFileInfo($_FILES['imagenes']['name'][$key]);
							$extension = $info->getExtension();
							//Indicamos la ruta de destino, así como el nombre del archivo
							$target_path = $directorio.'/'. $_POST['PIImagen'] . "-" . $cantidad . "." . $extension;

							//Movemos y validamos que el archivo se haya cargado correctamente
							//El primer campo es el origen y el segundo el destino
							if(move_uploaded_file($source, $target_path)) {
                $db->sentencia("INSERT INTO archivos_pi_referencias (tipo, ruta, observaciones, fecha_creacion, activo, fk_referencia, fk_categoria) VALUES (:tipo, :ruta, :observaciones, :fecha_creacion, :activo, :fk_referencia, :fk_categoria)", array(":tipo" => $_FILES['imagenes']['type'][$key], ":ruta" => substr($target_path, 6), ":observaciones" => "", ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1, ":fk_referencia" => $_POST['idRefImagen'], ":fk_categoria" => 2));
								$cont++;
								$cont1 = $key;
							} else {
								echo "Ha ocurrido un error con ". $filename .", por favor inténtelo de nuevo";
							}
							closedir($dir); //Cerramos el directorio de destino
						}
					}else{
						echo "Ha exedido el tamaño permitido";
					}
				}else{
					echo "El tipo de imagen no es permitido";
				}
			}
			if ($cont == $cont1) {
				echo "OK";
			}
		}else{
			echo "Error validar los campos";
		}
	}else{
	  throw new Exception("Error Processing Request", 1);
  }
  $db->desconectar();
}

function cantidadArchivos($id_producto, $id_categoria, $id_pi = 0){
  $db = new Bd();
  $db->conectar();

  if ($id_pi = 0) {
    $sql = $db->consulta("SELECT * FROM archivos_pi_referencias WHERE fk_referencia = :fk_referencia AND fk_categoria = :fk_categoria", array(":fk_referencia" => $id_producto, ":fk_categoria" => $id_categoria));
  }else{
    $sql = $db->consulta("SELECT * FROM archivos_pi_referencias WHERE fk_referencia = :fk_referencia AND fk_categoria = :fk_categoria AND fk_pi = :fk_pi", array(":fk_referencia" => $id_producto, ":fk_categoria" => $id_categoria, ":fk_pi" => $id_pi));
  }

  $db->desconectar();

  return json_encode($sql['cantidad_registros']);
}

function mostrarImagenes(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $sql = $db->consulta("SELECT * FROM archivos_pi_referencias WHERE fk_referencia = :fk_referencia AND fk_categoria = :fk_categoria", array(":fk_referencia" => $_REQUEST['idProducto'], ":fk_categoria" => 2));

  if ($sql['cantidad_registros'] > 0) {
    $resp = array("success" => true,
                  "msj" => $sql);
  } else {
    $resp = array("success" => false,
                  "msj" => "No hay imagenes para mostrar.");
  }
  $db->desconectar();
  return json_encode($resp);
}

function subirArchivos(){
  $db = new Bd();
  $db->conectar();

  if (isset($_FILES['archivo']) && isset($_POST['idPI']) && isset($_POST['refPI']) && isset($_POST['referencia']) && isset($_POST['categoria']) && isset($_POST['idProducto'])) {
    if (textoblanco($_POST['idPI']) > 0 && textoblanco($_POST['refPI']) > 0 && textoblanco($_POST['referencia']) && textoblanco($_POST['categoria']) > 0) {
      $cont=-1;
			$cont1 = 0;
      //Se valida cuantos archivos se han agregados a esa categoria con el lote
      if ($_POST['categoria'] == 1 || $_POST['categoria'] == 1) {
        $cantidad = cantidadArchivos($_POST['idProducto'], $_POST['categoria']);
        $pia = null;
      }else{
        $cantidad = cantidadArchivos($_POST['idProducto'], $_POST['categoria'], $_POST['idPI']);
        $pia = $_POST['idPI'];
      }

      foreach ($_FILES['archivo']['tmp_name'] as $key => $tmp_name) {
        //Obtenemos la extension del archivo para agregarla al a final
        $info = new SplFileInfo($_FILES['archivo']['name'][$key]);
        $extension = $info->getExtension();
        //Validamos el tipo de archivos
        $tipo = $_FILES['archivo']['type'][$key];
        if ($tipo == "application/msword" || $tipo == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $tipo == "application/vnd.ms-excel" || $tipo == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || $tipo == "application/pdf" || $tipo == "application/x-zip-compressed" || $tipo == "application/zip" || $tipo == "application/vnd.ms-powerpoint" || $tipo == "application/vnd.openxmlformats-officedocument.presentationml.presentation" || $extension === "rar") {
          //Declaramos un  variable con la ruta donde guardaremos los archivos
          if ($_POST['categoria'] == 1 || $_POST['categoria'] == 18) {
            $directorio = '../../almacenamiento/jobs/' . $_POST['referencia'];
          }else{
            $directorio = '../../almacenamiento/jobs/' . $_POST['referencia'] . '/' . $_POST['refPI'];
          }

          //Validamos si la ruta de destino existe, en caso de no existir la creamos
          if(!file_exists($directorio)){
            // Para crear una estructura anidada se debe especificar
            // el parámetro $recursive en mkdir().
            if(!mkdir($directorio, 0777, true)) {
              die('Fallo al crear las carpetas...');
            }
          }

          //Abrimos el directorio de destino
          $dir=opendir($directorio);
          //Incrementamos la cantidad actual
          $cantidad++;
          //Indicamos la ruta de destino, así como el nombre del archivo
          $target_path = $directorio.'/'. $_POST['categoria'] . "-" . $cantidad . "." . $extension;

          if ($extension != "rar") {
            $extension = $_FILES['archivo']['type'][$key];
          }
          //Movemos y validamos que el archivo se haya cargado correctamente
          //El primer campo es el origen y el segundo el destino
          if(move_uploaded_file($_FILES['archivo']['tmp_name'][$key], $target_path)) {
            $db->sentencia("INSERT INTO archivos_pi_referencias (tipo, ruta, observaciones, fecha_creacion, activo, fk_referencia, fk_categoria, fk_pi) VALUES (:tipo, :ruta, :observaciones, :fecha_creacion, :activo, :fk_referencia, :fk_categoria, :fk_pi)", array(":tipo" => $extension, ":ruta" => substr($target_path, 6), ":observaciones" => $_POST['archivoObservaciones'], ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1, ":fk_referencia" => $_POST['idProducto'], ":fk_categoria" => $_POST['categoria'], ":fk_pi" => $pia));
            $cont++;
            $cont1 = $key;
          } else {
            echo "Ha ocurrido un error con ". $_FILES['archivo']['name'][$key] .", por favor inténtelo de nuevo";
          }
          closedir($dir); //Cerramos el directorio de destino
        }else{
          echo "El tipo de archivo no es permitido";
        }
      }
      if ($cont == $cont1) {
				echo "OK";
			}
    }else{
      echo "Error";
    }
  }else{
    echo "Error";
  }

  $db->desconectar();
}

function listaDocumentos(){
  $db = new Bd();
  $db->conectar();
  if ($_POST['idSub'] == 1) {
    $sql = $db->consulta("SELECT apr.observaciones AS observaciones, apr.ruta AS ruta, apr.tipo AS tipo, c.nombre AS nombre_sub FROM archivos_pi_referencias AS apr INNER JOIN categorias AS c ON c.id = apr.fk_categoria WHERE apr.fk_categoria = :fk_categoria AND fk_referencia = :fk_referencia", array(":fk_referencia" => $_POST['idPro'], ":fk_categoria" => $_POST['idSub']));
  }else{
    $sql = $db->consulta("SELECT apr.observaciones AS observaciones, apr.ruta AS ruta, apr.tipo AS tipo, c.nombre AS nombre_sub FROM archivos_pi_referencias archivos_pi_referencias AS apr INNER JOIN categorias AS c ON c.id = apr.fk_categoria WHERE apr.fk_categoria = :fk_categoria AND fk_referencia = :fk_referencia AND fk_pi = :fk_pi", array(":fk_referencia" => $_POST['idPro'], ":fk_categoria" => $_POST['idSub'], ":fk_pi" => $_POST['idPI']));
  }

  if ($sql['cantidad_registros'] > 0) {
    $categorias = "";
    $icono = "";
    $cont=1;
    $ext = "";
    $toggle = "";
    for ($i=0; $i <$sql['cantidad_registros']; $i++) { 
      if ($sql[$i]['tipo'] == "rar") {
        $ext = ".rar";
      }else{
        $ext="";
      }

      if($sql[$i]['observaciones'] != "" && $sql[$i]['observaciones'] != NULL){
        $toggle = $sql[$i]['observaciones'];
      }

      $categorias .= '<div data-toggle="tooltip" title="' . $toggle . '" class="col-3 col-sm-2 col-md-2 col-lg-1 text-center productosfd-iconos mb-3" ><a href="../../' . $sql[$i]['ruta'] . '" download="'. $sql[$i]['nombre_sub'] . '-' . $cont . $ext.'"><i class="' . iconos($sql[$i]['tipo']) . ' fa-3x"></i></a></div>';
    }
  } else {
    $categorias = "";
  }
  return $categorias;

  $db->conectar();
}

if(@$_REQUEST['accion']){
	if(function_exists($_REQUEST['accion'])){
		echo($_REQUEST['accion']($_REQUEST));
	}
}
?>