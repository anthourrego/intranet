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

include_once($ruta_raiz.'clases/sessionActiva.php');
include_once($ruta_raiz.'clases/Conectar.php');
include_once($ruta_raiz.'clases/funciones_generales.php');


function guardar_encuesta(){

	$et_id=@$_REQUEST['et_fk'];	
	$ere_fecha=fecha_db_insertar(date("Y-m-d\TH:i:s"));
	$erep_fecha=$ere_fecha;
	$erea_fecha=$ere_fecha;
	$ere_uid=uniqid();
	
	
	$bd=new Bd();
	$bd->conectar();	
	
	//insert rta
	$sql_insert_respuesta="INSERT INTO encuesta_respuesta (et_fk,ere_fecha,ere_uid) VALUES (:et_fk, :ere_fecha, :ere_uid)";
	$bd->sentencia($sql_insert_respuesta, array(':et_fk' => $et_id, 
																							':ere_fecha' => $ere_fecha, 
																							':ere_uid' => $ere_uid
																						));
	
	
	$sql_consulta_id_respuesta="SELECT ere_id FROM encuesta_respuesta WHERE ere_uid='".$ere_uid."'";
	$consulta_id_respuesta=$bd->consulta($sql_consulta_id_respuesta);
	$ere_fk=$consulta_id_respuesta[0]['ere_id'];
	
	
	//atributos
	$sql_atr="SELECT ea_id,ea_type FROM encuesta_atr WHERE ea_estado=1 AND et_fk=".$et_id;
	$consulta_atr=$bd->consulta($sql_atr);	
	for ($i=0; $i < $consulta_atr['cantidad_registros']; $i++) {
		if(@$_REQUEST['atr_'.$consulta_atr[$i]['ea_id']]){
			$campo_atr='';
			$value_atr="";
			switch ($consulta_atr[$i]['ea_type']) {
				case 'varchar':
					$campo_atr='erea_valor_varchar';
					$value_atr="'".cadena_db_insertar($_REQUEST['atr_'.$consulta_atr[$i]['ea_id']])."'";
					break;
				case 'datetime':
					$campo_atr='erea_valor_datetime';
					$value_atr="'".fecha_db_insertar($_REQUEST['atr_'.$consulta_atr[$i]['ea_id']])."'";					
					break;		
				case 'int':
					$campo_atr='erea_valor_int';
					$value_atr=intval($_REQUEST['atr_'.$consulta_atr[$i]['ea_id']]);					
					break;	
				case 'text':
					$campo_atr='erea_valor_text';
					$value_atr="'".cadena_db_insertar($_REQUEST['atr_'.$consulta_atr[$i]['ea_id']])."'";
					break;										
				
			}
			
			$sql_insert_atr="
				INSERT INTO encuesta_respuesta_atr 
				(
					ea_fk,
					ere_fk,
					erea_fecha,
					".$campo_atr."
				)
				VALUES
				(
					".$consulta_atr[$i]['ea_id'].",
					".$ere_fk.",
					'".$erea_fecha."',
					".$value_atr."
				)
			";				
			$bd->sentencia($sql_insert_atr);
		}		
	}	
	
	
	
	
	//preguntas
	$sql_encuesta_grupo="SELECT * FROM encuesta_grupo a, encuesta_reg b WHERE eg_estado=1 AND a.eg_id=b.eg_fk AND b.et_fk=".$et_id;
	$consulta_encuesta_grupo=$bd->consulta($sql_encuesta_grupo);
	
	
	for ($eg=0; $eg < $consulta_encuesta_grupo['cantidad_registros']; $eg++) {
		 
		$sql_encuesta_pregunta="SELECT * FROM encuesta_pregunta WHERE ep_estado=1 AND eg_fk=".$consulta_encuesta_grupo[$eg]['eg_id'];
		$consulta_encuesta_pregunta=$bd->consulta($sql_encuesta_pregunta);
	
		//preguntas
		for ($ep=0; $ep < $consulta_encuesta_pregunta['cantidad_registros']; $ep++) {
			
			$name_item='grupo_'.$consulta_encuesta_grupo[$eg]['eg_id'].'_pregunta_'.$consulta_encuesta_pregunta[$ep]['ep_id'].'';	
			
			if(@$_REQUEST[$name_item]){
				$sql_insert_respuesta_pregunta="
					INSERT INTO encuesta_respuesta_pregunta 
					(
						eg_fk,
						ep_fk,
						epa_fk,
						ere_fk,
						erep_fecha
					)
					VALUES
					(
						".$consulta_encuesta_grupo[$eg]['eg_id'].",
						".$consulta_encuesta_pregunta[$ep]['ep_id'].",
						".$_REQUEST[$name_item].",
						".$ere_fk.",
						'".$erep_fecha."'
					)
				
				";	
				$bd->sentencia($sql_insert_respuesta_pregunta);
			} //SI LLEGA EL REQUEST DE PREGUNTA
	
		}
	
	
	
	}	
	
	$bd->desconectar();
	
	$retorno=array();
	$retorno['exito']=1;
	return(json_encode($retorno));
			
}


if(@$_REQUEST['ejecutar_accion']){
	if(function_exists($_REQUEST['ejecutar_accion'])){
		echo($_REQUEST['ejecutar_accion']());
	}
}


?>
