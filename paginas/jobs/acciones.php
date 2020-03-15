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

if(@$_REQUEST['accion']){
	if(function_exists($_REQUEST['accion'])){
		echo($_REQUEST['accion']($_REQUEST));
	}
}
?>