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
include_once($ruta_raiz . 'clases/sessionActiva.php');
$usuario = $session->get("usuario");


function crearMarca(){
  global $usuario;
  $resp = array();
  $db = new Bd();
  $db->conectar();

  $marca = trim($_REQUEST['nombre']);

  if (validarMarcaNombre($marca) == 0) {
    //Insertamos la marca en la tabla
    $ultimoId = $db->sentencia("INSERT INTO marcas (nombre, fecha_creacion, activo, fk_creador) VALUES (:nombre, :fecha_creacion, :activo, :fk_creador)", array(":nombre" => $marca,":fecha_creacion" => date('Y-m-d H:i:s'), ":activo" => 1, ":fk_creador" => $usuario['id']));

    $db->insertLogs("marcas", $ultimoId, "Creacion de marca " . $marca, $usuario['id']);

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
  global $usuario;
  $db = new Bd();
  $db->conectar();

  $db->sentencia("UPDATE marcas SET activo = 0 WHERE id = :id", array(":id" => $_REQUEST['idMarca']));

  $db->insertLogs("marcas", $_REQUEST['idMarca'], "Se eliminar la marca " . $_REQUEST['nombreMarca'], $usuario['id']);

  $db->desconectar();

  return json_encode(1);
}

function editarMarca(){
  global $usuario;
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $nombre = trim($_REQUEST['nombre']);

  if (validarMarcaNombre($nombre, $_REQUEST['idMarca']) == 0) {
    $db->sentencia("UPDATE marcas SET nombre = :nombre WHERE id = :id", array(":nombre" => $nombre, ":id" =>$_REQUEST['idMarca']));

    $db->insertLogs("marcas", $_REQUEST['idMarca'], "Cambio nombre marca a " . $nombre, $usuario['id']);

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

function listaTecnologiaNoCompatible(){
  $db = new Bd();
  $db->conectar();
  $resp = array();
  $datos = array();

  if (@$_REQUEST['tecnologia']) {
    $compatible = '';
    for ($i=0; $i < count($_REQUEST["tecnologia"]); $i++) {
      if ((count($_REQUEST["tecnologia"]) -1 ) != $i) {
        $compatible .= $_REQUEST["tecnologia"][$i] . ', ';
      }else{
        $compatible .= $_REQUEST["tecnologia"][$i];
      }
    }

    $sql = $db->consulta("SELECT * FROM tecnologia_no_compatible WHERE estado = 1 AND (fk_tecnologia IN (" . $compatible . ") OR fk_tecnologia_compatible IN (" . $compatible . "))");

    for ($i=0; $i < $sql['cantidad_registros']; $i++) { 
      $datos[] = $sql[$i]['fk_tecnologia'];
      $datos[] = $sql[$i]['fk_tecnologia_compatible'];
    }
    
    $datos = array_unique($datos);
    
    foreach ($_REQUEST["tecnologia"] as $da) {
      if (in_array($da, $datos)) {
        $key = array_search($da, $datos);
        unset($datos[$key]);
      }
    }


    if ($sql['cantidad_registros'] > 0) {
      $resp = array("success" => true,
                    "msj" => array_values($datos));
    }else{
      $resp = array("success" => false,
                    "msj" => "No se han encontrado datos.");
    }
  }else{
    $resp = array("success" => false,
                  "msj" => "No se han encontrado datos.");
  }


  $db->desconectar();

  return json_encode($resp);
}

function crearReferencia(){
  global $usuario;
  $db = new Bd();
  $db->conectar();
  $resp = array();

  if (isset($_REQUEST['referencia']) && isset($_REQUEST['tecnologia'])) {
    $referencia = trim($_REQUEST['referencia']);

    if (validarNombreReferencia($referencia) == 0) {
      $ultimoIdRef = $db->sentencia("INSERT INTO referencias (referencia, fecha_creacion, fk_marca, fk_creador) VALUES (:referencia, :fecha_creacion, :fk_marca, :fk_creador)", array(":referencia" => $referencia, ":fecha_creacion" => date("Y-m-d H:i:s"), ":fk_marca" => $_REQUEST['fk_marca'], ":fk_creador" =>$usuario['id']));

      $db->insertLogs("referencias", $ultimoIdRef, "Creacion de referencia " . $referencia, $usuario['id']);

      if ($ultimoIdRef != 0) {

        foreach($_POST['tecnologia'] as $tec) {
          $ultimoIdRefTec = $db->sentencia("INSERT INTO referencias_tecnologias (fk_referencia, fk_tecnologia, fecha_creacion, activo, fk_creador) VALUES (:fk_referencia, :fk_tecnologia, :fecha_creacion, :activo, :fk_creador)", array(":fk_referencia" => $ultimoIdRef, ":fk_tecnologia" => $tec, ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1, ":fk_creador" => $usuario['id']));

          $db->insertLogs("referencias_tecnologias", $ultimoIdRefTec, "Creacion de referencia tecnologia de " . $referencia . " con " . $tec, $usuario['id']);
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

function validarNombreReferencia($referencia, $id=0){
  $db = new Bd();
  $db->conectar();
  
  if ($id == 0) {
    $validar = $db->consulta("SELECT * FROM referencias WHERE referencia = :referencia", array(":referencia" => $referencia));
  }else{
    $validar = $db->consulta("SELECT * FROM referencias WHERE referencia = :referencia AND id != :id", array(":referencia" => $referencia, ":id" => $id));
  }

  $db->desconectar();
  return json_encode($validar['cantidad_registros']);
}

function listaReferencias(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $referencias = $db->consulta("SELECT r.id, r.referencia FROM referencias_tecnologias AS rt INNER JOIN referencias AS r ON r.id = rt.fk_referencia WHERE r.fk_marca = :fk_marca AND rt.fk_tecnologia = :fk_tecnologia AND r.estado = 1", array(":fk_marca" => $_REQUEST['marca'], ":fk_tecnologia" => $_REQUEST['tecnologia']));
  
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

function listaReferenciaTec(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $sql = $db->consulta("SELECT * FROM referencias_tecnologias WHERE fk_referencia = :fk_referencia AND activo = 1", array(":fk_referencia" => $_REQUEST["fk_referencia"]));

  if ($sql["cantidad_registros"] > 0) {
    $resp = array(
              "success" => true,
              "msj" => $sql
            );
  }else{
    $resp = array(
              "success" => false,
              "msj" => "No se han encontrado resultados."
            );
  }

  $db->desconectar();
  return json_encode($resp);
}

function editarReferencia(){
  global $usuario;
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $referencia = trim($_REQUEST["nombre"]);
  
  if(validarNombreReferencia($referencia, $_REQUEST['idReferencia']) == 0){
    $db->sentencia("UPDATE referencias SET referencia = :referencia WHERE id = :id", array(":referencia" => $referencia, ":id" => $_REQUEST['idReferencia']));

    $db->insertLogs("referencias", $_REQUEST['idReferencia'], "Cambio de nombre de Referencia a " . $referencia, $usuario['id']);
    
    $ids = '';
    for ($i=0; $i < count($_REQUEST["tecnologia"]); $i++) { 
      $ids .= $_REQUEST["tecnologia"][$i] . ", ";
      if (count($_REQUEST["tecnologia"]) == ($i + 1)) {
        $ids .= $_REQUEST["tecnologia"][$i];
      }

      $validarTecnologia = $db->consulta("SELECT * FROM referencias_tecnologias WHERE fk_referencia = :fk_referencia AND fk_tecnologia = :fk_tecnologia", array(":fk_referencia" => $_REQUEST['idReferencia'], ":fk_tecnologia" => $_REQUEST["tecnologia"][$i]));

      if ($validarTecnologia["cantidad_registros"] == 1) {
        if ($validarTecnologia[0]["activo"] == 0) {
          $db->sentencia("UPDATE referencias_tecnologias SET activo = 1 WHERE id = :id", array(":id" => $validarTecnologia[0]["id"]));
          $db->insertLogs("referencias_tecnologias", $validarTecnologia[0]["id"], "Se activa la tecnologia", $usuario['id']);
        }
      }else{
        $idTec = $db->sentencia("INSERT INTO referencias_tecnologias (fk_referencia, fk_tecnologia, fecha_creacion, activo, fk_creador) VALUES (:fk_referencia, :fk_tecnologia, :fecha_creacion, :activo, :fk_creador)", array(":fk_referencia" => $_REQUEST['idReferencia'], ":fk_tecnologia" => $_REQUEST["tecnologia"][$i], ":fecha_creacion" => date('Y-m-d H:i:s'), ":activo" => 1, ":fk_creador" => $usuario['id']));

        $db->insertLogs("referencias_tecnologias", $idTec, "Se crea una nueva tecnologia a " . $referencia, $usuario['id']);
      }

    }

    $sqlTecnologiasDesactivar = $db->consulta("SELECT * FROM referencias_tecnologias WHERE fk_referencia = :fk_referencia AND fk_tecnologia NOT IN (" . $ids . ")", array(":fk_referencia" => $_REQUEST['idReferencia']));

    for ($i=0; $i < $sqlTecnologiasDesactivar["cantidad_registros"]; $i++) { 
      if ($sqlTecnologiasDesactivar[$i]['activo'] == 1) {
        $db->sentencia("UPDATE referencias_tecnologias SET activo = 0 WHERE id = :id", array(":id" => $sqlTecnologiasDesactivar[$i]["id"]));
          $db->insertLogs("referencias_tecnologias", $sqlTecnologiasDesactivar[0]["id"], "Se desactiva  la tecnologia", $usuario['id']);
      }
    }

    $resp = array(
              "success" => true,
              "msj" => "Le referencia se ha acutalizado correctamente."
            );

  }else{
    $resp = array(
              "success" => false,
              "msj" => "El nombre <b>" . $nombre . "</b> ya esta creado."
            );
  }

  $db->desconectar();
  return json_encode($resp);
}

function crearPI(){
  global $usuario;
  $db = new Bd();
  $db->conectar();
  $resp = array();
  $pi = trim($_REQUEST['nombre']);

  if (validarPI($pi, $_REQUEST['fk_referencia']) == 0) {
    $ultimoIdPI = $db->sentencia("INSERT INTO pi (pi, unidades, fecha_creacion, activo, fk_referencia, fk_creador) VALUES (:pi, :unidades, :fecha_creacion, :activo, :fk_referencia, :fk_creador)", array(":pi" => $pi, ":unidades" => $_REQUEST['unidades_pi'], ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1, ":fk_referencia" => $_REQUEST['fk_referencia'], ":fk_creador" => $usuario['id']));

    $db->insertLogs("pi", $ultimoIdPI, "Creacion de PI " . $pi, $usuario['id']);

    $resp = array("success" => true,
                  "msj" => "La PI <b>" . $pi . "</b> se ha creado.");
  } else {
    $resp = array("success" => false,
                  "msj" => "La PI ya se encuentra creada");
  }

  $db->desconectar();

  return json_encode($resp);
}

function validarPI($pi, $referencia, $id = 0){
  $db = new Bd();
  $db->conectar();

  if ($id == 0) {
    $sql = $db->consulta("SELECT * FROM pi WHERE pi = :pi AND fk_referencia = :fk_referencia", array(":pi" => $pi, ":fk_referencia" => $referencia));
  }else{
    $sql = $db->consulta("SELECT * FROM pi WHERE pi = :pi AND fk_referencia = :fk_referencia AND id != :id", array(":pi" => $pi, ":fk_referencia" => $referencia, ":id" => $id));
  }

  $db->desconectar();

  return json_encode($sql['cantidad_registros']);
}

function listaPI(){
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $sql = $db->consulta("SELECT * FROM pi WHERE fk_referencia = :fk_referencia AND activo = 1", array(":fk_referencia" => $_REQUEST['fk_referencia']));

  if ($sql["cantidad_registros"] > 0) {
    $resp = array(
              "success" => true,
              "msj" => $sql
            );
  } else {
    $resp = array(
              "success" => false,
              "msj" => "No hay PI creadas"
            );
  }
  $db->desconectar();
  return json_encode($resp);
}

function cantidadArchivos($id_producto, $id_categoria, $id_pi = 0){
  $db = new Bd();
  $db->conectar();

  if ($id_pi == 0) {
    $sql = $db->consulta("SELECT * FROM archivos_pi_referencias WHERE fk_referencia = :fk_referencia AND fk_categoria = :fk_categoria", array(":fk_referencia" => $id_producto, ":fk_categoria" => $id_categoria));
  }else{
    $sql = $db->consulta("SELECT * FROM archivos_pi_referencias WHERE fk_referencia = :fk_referencia AND fk_categoria = :fk_categoria AND fk_pi = :fk_pi", array(":fk_referencia" => $id_producto, ":fk_categoria" => $id_categoria, ":fk_pi" => $id_pi));
  }

  $db->desconectar();

  return json_encode($sql['cantidad_registros']);
}

function subirArchivos(){
  global $usuario;
  global $ruta_raiz;
  $resp = array();
  $db = new Bd();
  $db->conectar();

  if ((isset($_FILES['archivos']) && isset($_POST['referencia']) && isset($_POST['categoria']) && isset($_POST['idProducto']))) {
    $cont=-1;
    $cont1 = 0;
    //Se valida cuantos archivos se han agregados a esa categoria con el lote
    if ($_REQUEST['aplicaPI'] == 0) {
      $cantidad = cantidadArchivos($_POST['idProducto'], $_POST['categoria']);
      $pia = null;
    }else{
      $cantidad = cantidadArchivos($_POST['idProducto'], $_POST['categoria'], $_POST['idPI']);
      $pia = $_POST['idPI'];
    }

    foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
      //Creamos un contador para valdiar si aplica para alguna exntesion
      $contExt = 0;
      //Obtenemos la extension del archivo para agregarla al a final
      $info = new SplFileInfo($_FILES['archivos']['name'][$key]);
      $tamano = $_FILES['archivos']['size'][$key];
      $extension = $info->getExtension();
      $extension1 = "";
      //El tamaño maximo es de 10 mb
			if ($tamano <= 10000000) {

        //Validamos el tipo de archivos
        $extensionesPermitidad = $db->consulta("SELECT ta.extensiones AS extension FROM tipo_archivo_categoria AS tac INNER JOIN tipo_archivo AS ta ON ta.id = tac.fk_tarchivo WHERE fk_categoria = :fk_categoria", array(":fk_categoria" => $_POST['categoria']));
        
        $extension = strtolower($extension);
        //Realizamos un ciclo para validad si es compativle esl tipo de rachivo
        for ($i=0; $i < $extensionesPermitidad["cantidad_registros"]; $i++) { 
          if ($extensionesPermitidad[$i]['extension'] == $extension) {
            $contExt++;
          }
        }
  
        if ($contExt > 0) {
  
          //Declaramos un  variable con la ruta donde guardaremos los archivos
          if ($_REQUEST['aplicaPI'] == 0) {
            $directorio = $ruta_raiz . 'almacenamiento/jobs/' . $_POST['idProducto'];
          }else{
            $directorio = $ruta_raiz . 'almacenamiento/jobs/' . $_POST['idProducto'] . '/' . $_POST['idPI'];
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
          
          $extension1 = $_FILES['archivos']['type'][$key];
          
          if ($extension == "rar") {
            $extension1 = "rar";
          }

          //Movemos y validamos que el archivo se haya cargado correctamente
          //El primer campo es el origen y el segundo el destino
          if(move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $target_path)) {
            $idArchivo = $db->sentencia("INSERT INTO archivos_pi_referencias (tipo, tipo2, ruta, observaciones, fecha_creacion, activo, fk_referencia, fk_categoria, fk_pi) VALUES (:tipo, :tipo2, :ruta, :observaciones, :fecha_creacion, :activo, :fk_referencia, :fk_categoria, :fk_pi)", array(":tipo" => $extension1, ":ruta" => substr($target_path, 6), ":observaciones" => $_POST['archivoObservaciones'], ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1, ":fk_referencia" => $_POST['idProducto'], ":fk_categoria" => $_POST['categoria'], ":fk_pi" => $pia, ":tipo2" => $extension));

            if ($pia == null) {
              $db->insertLogs("archivos_pi_referencias", $idArchivo, "Creacion de archivo en referencia id: " . $_POST['idProducto'] . " Nombre: " . $_POST['referencia'], $usuario['id']);
            }else{
              $db->insertLogs("archivos_pi_referencias", $idArchivo, "Creacion de archivo en referencia id: " . $_POST['idProducto'] . " Nombre: " . $_POST['referencia'] . " con PI id: " . $pia . " Nombre: " . $_POST['refPI'], $usuario['id']);
            }

            $cont++;
            $cont1 = $key;
          } else {
            echo "Ha ocurrido un error con ". $_FILES['archivo']['name'][$key] .", por favor inténtelo de nuevo";
          }
          closedir($dir); //Cerramos el directorio de destino
        
        }else{
          $resp = array(
            "success" => false,
            "msj" => "El tipo de archivo no es permitido"
          );
        }
      }else{
        $resp = array(
          "success" => false,
          "msj" => "Ha exedido el tamaño permitido de 10MB"
        );
      }
    }
    if ($cont == $cont1) {
      $resp = array(
                "success" => true,
                "msj" => "Se han subido los archivos"
              );
    }else{
      $resp = array(
        "success" => false,
        "msj" => "Error al subir los archivos"
      );
    }
  }else{
    $resp = array(
              "success" => false,
              "msj" => "Los campos obligatorios no se encuentra definidos"
            );
  }

  $db->desconectar();

  return json_encode($resp);
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


function listaDocumentos(){
  $resp = array();
  $db = new Bd();
  $db->conectar();
  if ($_POST['idPI'] == 0) {
    $sql = $db->consulta("SELECT apr.id AS id, apr.observaciones AS observaciones, apr.ruta AS ruta, apr.tipo AS tipo, apr.tipo2 AS tipo2, c.nombre AS nombre_sub FROM archivos_pi_referencias AS apr INNER JOIN categorias AS c ON c.id = apr.fk_categoria WHERE apr.fk_categoria = :fk_categoria AND fk_referencia = :fk_referencia AND apr.activo = 1", array(":fk_referencia" => $_POST['idPro'], ":fk_categoria" => $_POST['idSub']));
  }else{
    $sql = $db->consulta("SELECT apr.id AS id, apr.observaciones AS observaciones, apr.ruta AS ruta, apr.tipo AS tipo, apr.tipo2 AS tipo2, c.nombre AS nombre_sub FROM archivos_pi_referencias AS apr INNER JOIN categorias AS c ON c.id = apr.fk_categoria WHERE apr.fk_categoria = :fk_categoria AND fk_referencia = :fk_referencia AND fk_pi = :fk_pi AND apr.activo = 1", array(":fk_referencia" => $_POST['idPro'], ":fk_categoria" => $_POST['idSub'], ":fk_pi" => $_POST['idPI']));
  }

  if ($sql['cantidad_registros'] > 0) {
    for ($i=0; $i <$sql['cantidad_registros']; $i++) { 
      
      if ($sql[$i]['tipo'] == "rar") {
        $sql[$i]['icono'] = iconos("rar");
      }else{
        $sql[$i]['icono'] = iconos($sql[$i]['tipo']);
      }
    }

    $resp = array(
              "success" => true,
              "msj" => $sql
            );
  } else {
    $resp = array(
              "success" => false,
              "msj" => "No se han encontrado documentos"
            );
  }
  $db->conectar();

  return json_encode($resp);
}

function listaCategorias(){
  $resp = array();
  $db = new Bd();
  $db->conectar();

  if ($_REQUEST["pi"] == 1) {
    $categorias = $db->consulta("SELECT * FROM categorias WHERE activo = 1");
  }else{
    $categorias = $db->consulta("SELECT * FROM categorias WHERE activo = 1 AND aplica_pi = :aplica_pi", array(":aplica_pi" => 0));
  }

  
  if ($categorias["cantidad_registros"] > 0) {
    //Recorremos los archivos para buscar las extensiones relacionadas
    for ($i=0; $i < $categorias["cantidad_registros"]; $i++) { 
      //Enviamso el id y buscamos las extensiones
      $archivos = $db->consulta("SELECT ta.extensiones AS extension FROM tipo_archivo_categoria AS tac INNER JOIN tipo_archivo AS ta ON ta.id = tac.fk_tarchivo WHERE fk_categoria = :fk_categoria AND ta.estado = 1 AND tac.estado = 1", array(":fk_categoria" => $categorias[$i]["id"]));
      //Metemos en la consulta principal las extensiones de la categoria
      $extensiones = "";
      for ($j=0; $j < $archivos["cantidad_registros"]; $j++) { 
        if ($archivos["cantidad_registros"] != ($j+1)) {
          $extensiones .= "." . $archivos[$j]["extension"] . ", ";
        }else{
          $extensiones .= "." . $archivos[$j]["extension"];
        }
      }
      $categorias[$i]["extensiones"] = $extensiones;
    }

    $resp = array(
              "success" => true,
              "msj" => $categorias
            );
  }else{
    $resp = array(
              "success" => false,
              "msj" => "No se han encontrado datos"
            );
  }

  $db->desconectar();

  return json_encode($resp);
}

function eliminarArchivo(){
  global $usuario;
  $resp = array();
  $db = new Bd();
  $db->conectar();

  $db->sentencia("UPDATE archivos_pi_referencias SET activo = 0 WHERE id = :id", array(":id" => $_REQUEST["archivo"]));

  $db->insertLogs("archivos_pi_referencias", $_REQUEST["archivo"], "Se elimina el archivo", $usuario['id']);

  $db->desconectar();

  return json_encode(1);
}

function eliminarPI(){
  global $usuario;
  $resp = array();
  $db = new Bd();
  $db->conectar();

  $db->sentencia("UPDATE pi SET activo = 0 WHERE id = :id", array(":id" => $_REQUEST["id"]));

  $db->insertLogs("pi", $_REQUEST["id"], "Se elimina la PI " . $_REQUEST["pi"], $usuario['id']);

  $db->desconectar();

  return json_encode(1);
}

function editarPI(){
  global $usuario;
  $db = new Bd();
  $db->conectar();
  $resp = array();

  $pi = trim($_REQUEST['pi']);

  if (validarPI($pi, $_REQUEST['referencia'], $_REQUEST["id"]) == 0) {
    
    $db->sentencia("UPDATE pi SET pi = :pi, unidades = :unidades WHERE id = :id", array(":pi" => $pi, ":id" =>$_REQUEST["id"], ":unidades" => $_REQUEST["unidades"]));

    $db->insertLogs("pi", $_REQUEST['id'], "Cambio nombre pi a " . $pi . " y de unidades a " . $_REQUEST["unidades"], $usuario['id']);

    $resp = array("success" => true,
                  "msj" => "Se  ha actualizado la pi");
  }else{
    $resp = array('success' => false,
                  'msj' => "El nombre no se puede utilizar.");
  }

  $db->desconectar();

  return json_encode($resp);
}

function eliminarReferencia(){
  global $usuario;
  $resp = array();
  $db = new Bd();
  $db->conectar();

  $db->sentencia("UPDATE referencias SET estado = 0 WHERE id = :id", array(":id" => $_REQUEST["idReferencia"]));

  $db->insertLogs("referencias", $_REQUEST["idReferencia"], "Se elimina la referencia " . $_REQUEST["nombreReferencia"], $usuario['id']);

  $db->desconectar();

  return json_encode(1);
}

if(@$_REQUEST['accion']){
	if(function_exists($_REQUEST['accion'])){
		echo($_REQUEST['accion']());
	}
}
?>