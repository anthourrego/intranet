<?php
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

	include_once($ruta_raiz . "clases/sessionActiva.php");
	include_once($ruta_raiz . "clases/Conectar.php");

	function nombreEvaluador($nombre){
		$session = new Session();
		$usuario = $session->get('usuario');
		$cadena_retorno= $usuario['nombre'];
		$cadena_retorno.='<input type="hidden" name="'.$nombre.'" id="'.$nombre.'" value="'.$usuario['id'].'">';
		return $cadena_retorno;
	}	

	function cargoEvaluador($nombre){
		$session = new Session();
		$usuario = $session->get('usuario');
		$db = new Bd();
		$db->conectar();
		$cargo = $db->consulta("SELECT * FROM vrol WHERE fun_id = :fun_id AND rol_estado = 1", array(":fun_id" => $usuario['id']));
		$db->desconectar();
		$cadena_retorno = $cargo[0]['car_tag'];
		$cadena_retorno .= '<input type="hidden" name="'.$nombre.'" id="'.$nombre.'" value="'. $cargo[0]['car_id'] .'">';
		return $cadena_retorno;
	}

	function nombreEvaluado($nombre){
		$db = new Bd();
		$db->conectar();
		$usuario = $db->consulta("SELECT * FROM ceg_funcionario WHERE fun_estado = 1 AND fun_id = :fun_id", array(":fun_id" => @$_GET['id_usu']));
		$db->desconectar();
		$cadena_retorno = $usuario[0]['fun_nombre'] . " " . $usuario[0]['fun_nombre2'] . " " . $usuario[0]['fun_apellido'] . " " . $usuario[0]['fun_apellido2'];
		$cadena_retorno.='<input type="hidden" name="'.$nombre.'" id="'.$nombre.'" value="'. $usuario[0]['fun_id'] .'">';
		return $cadena_retorno;
	}

	function cargoEvaluado($nombre){
		$db = new Bd();
		$db->conectar();
		$cargo = $db->consulta("SELECT * FROM vrol WHERE rol_estado = 1 AND fun_estado = 1 AND fun_id = :fun_id", array(":fun_id" => @$_GET['id_usu']));
		$db->desconectar();
		$cadena_retorno = $cargo[0]['car_tag'];
		$cadena_retorno .= '<input type="hidden" name="'.$nombre.'" id="'.$nombre.'" value="'. $cargo[0]['car_id'] .'">';
		return $cadena_retorno;
	}
	
	function mostrarNombre($id){ 
		$db = new Bd();
		$db->conectar();
		$usuario = $db->consulta("SELECT * FROM ceg_funcionario WHERE fun_estado = 1 AND fun_id = :fun_id", array(":fun_id" => $id));
		$db->desconectar();	
		return $usuario[0]['fun_nombre'] . " " . $usuario[0]['fun_nombre2'] . " " . $usuario[0]['fun_apellido'] . " " . $usuario[0]['fun_apellido2'];
	}

	function mostrarCargo($id){ 
		$db = new Bd();
		$db->conectar();
		$cargo = $db->consulta("SELECT * FROM ceg_cargo WHERE car_id = :car_id", array(":car_id" => $id));
		$db->desconectar();
		return $cargo[0]['car_tag'];
	}
?>