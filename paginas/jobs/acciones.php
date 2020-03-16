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

if(@$_REQUEST['accion']){
	if(function_exists($_REQUEST['accion'])){
		echo($_REQUEST['accion']($_REQUEST));
	}
}
?>