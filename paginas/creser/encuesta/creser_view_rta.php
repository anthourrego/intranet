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

include_once($ruta_raiz . 'clases/Conectar.php');
include_once($ruta_raiz . 'clases/librerias.php');
include_once($ruta_raiz . 'clases/sessionActiva.php');
include_once($ruta_raiz . 'clases/funciones_generales.php');


if(!@$_REQUEST['et_id']){
	header('location: '.RUTA_RAIZ.'central.php');
	die();
}

$bd=new Bd();
$bd->conectar();


$et_id=@$_REQUEST['et_id'];
$ere_id=@$_REQUEST['ere_id'];

$sql_encuesta_tipo="SELECT * FROM encuesta_tipo WHERE et_estado=1 AND et_id=".$et_id;
$consulta_encuesta_tipo=$bd->consulta($sql_encuesta_tipo);

$titulo_encuesta=@$consulta_encuesta_tipo[0]['et_titulo'];
$descripcion_encuesta=@$consulta_encuesta_tipo[0]['et_descripcion'];

if($consulta_encuesta_tipo[0]['et_libreria']){
	$vlibrerias=explode(',', $consulta_encuesta_tipo[0]['et_libreria']);
	for ($i=0; $i < count($vlibrerias); $i++) {
		if(file_exists($ruta_raiz.$vlibrerias[$i])){
			include_once($ruta_raiz.$vlibrerias[$i]);
		} 	
	}
}

$html_rules_validate='';
$html_msj_validate='';
$html_encuesta='';
$scripts = '';
$sumaTotal1 = 0;
$sumaTotal2 = 0;
$cont = 1;
$html_promedio = '';



//atributos
$sql_atr="SELECT * FROM encuesta_atr a, encuesta_respuesta_atr b WHERE a.ea_id=b.ea_fk AND a.ea_estado=1 AND a.et_fk=".$et_id." AND ere_fk=".$ere_id;
$consulta_atr=$bd->consulta($sql_atr);


if( $consulta_atr['cantidad_registros'] ){
	$html_encuesta.='
		<table class="table table-bordered">
	';
	for ($i=0; $i < $consulta_atr['cantidad_registros']; $i++) {

		$input_atr="";
		$name_atr='atr_'.$consulta_atr[$i]['ea_id'];
		$readonly_atr=$consulta_atr[$i]['ea_readonly'];
		$predeterminado_atr=$consulta_atr[$i]['ea_predeterminado'];
		$funcion_atr=$consulta_atr[$i]['ea_funcion_view'];
		
		
		if($funcion_atr && function_exists($funcion_atr)){
			$input_atr=($funcion_atr($consulta_atr[$i]['erea_valor_varchar'].fecha_db_obtener($consulta_atr[$i]['erea_valor_datetime'],'Y-m-d H:i:s').$consulta_atr[$i]['erea_valor_int'].$consulta_atr[$i]['erea_valor_text']));
		}else{		
			switch ($consulta_atr[$i]['ea_type']) {
				case 'varchar':
					$input_atr=$consulta_atr[$i]['erea_valor_varchar'];
					break;
				case 'datetime':
					$input_atr=fecha_db_obtener($consulta_atr[$i]['erea_valor_datetime'],'Y-m-d H:i:s');
					
					break;		
				case 'int':
					$input_atr=$consulta_atr[$i]['erea_valor_int'];
					break;	
				case 'text':
					$input_atr=$consulta_atr[$i]['erea_valor_text'];
					break;											
				default:
					
					break;
			}
		}

		 $html_encuesta.='
		 	<tr>
		 		<th>
		 			'.$consulta_atr[$i]['ea_titulo'].'
		 		</th>
		 		<td>
		 			'.$input_atr.'
		 		</td>
			</tr>
		 ';
	}
	$html_encuesta.='
		</table>
	';
}




$sql_encuesta_grupo="SELECT * FROM encuesta_grupo a, encuesta_reg b WHERE eg_estado=1 AND a.eg_id=b.eg_fk AND b.et_fk=".$et_id;
$consulta_encuesta_grupo=$bd->consulta($sql_encuesta_grupo);
for ($eg=0; $eg < $consulta_encuesta_grupo['cantidad_registros']; $eg++) {
	$html_encuesta.='<table class="table table-bordered" id="competencia_' . $consulta_encuesta_grupo[$eg]['eg_id']. '">';


	$sql_encuesta_pregunta="SELECT * FROM encuesta_pregunta WHERE ep_estado=1 AND eg_fk=".$consulta_encuesta_grupo[$eg]['eg_id'];
	$consulta_encuesta_pregunta=$bd->consulta($sql_encuesta_pregunta);


	$sql_encuesta_param_reg="SELECT * FROM encuesta_param_reg a, encuesta_param b WHERE b.epa_estado=1 AND a.epa_fk=b.epa_id AND a.eg_fk=".$consulta_encuesta_grupo[$eg]['eg_id'];
	$consulta_encuesta_param_reg=$bd->consulta($sql_encuesta_param_reg);

	//encabezado parametros
	$html_params_titulo='';
	$html_params_valor='';
	for ($epar=0; $epar < $consulta_encuesta_param_reg['cantidad_registros']; $epar++) {
		$html_params_titulo.='<th class="text-center" tabindex="0" data-toggle="tooltip" title="' . $consulta_encuesta_param_reg[$epar]['epa_descripcion'] . '">'.$consulta_encuesta_param_reg[$epar]['epa_titulo'].'</th>';
		$html_params_valor.='<th class="text-center">'.$consulta_encuesta_param_reg[$epar]['epa_valor'].'</th>';
		if( ($epar+1)== $consulta_encuesta_param_reg['cantidad_registros']){
			$html_params_titulo.='<th rowspan="2" class="text-center">TOTAL</th>';
		}

	}

	
	$html_encuesta.='<tr><th colspan="'.($consulta_encuesta_param_reg['cantidad_registros']+3).'" class="text-center">'.$consulta_encuesta_grupo[$eg]['eg_titulo']. '</th></tr>';
	if($consulta_encuesta_grupo[$eg]['eg_descripcion']){
		$html_encuesta.='<tr><td colspan="'.($consulta_encuesta_param_reg['cantidad_registros']+3).'" class="text-center">'.$consulta_encuesta_grupo[$eg]['eg_descripcion'].'</td></tr>';
	}
			
	//preguntas
	$html_encuesta_pregunta='';
	$total_respuestas=0;
	for ($ep=0; $ep < $consulta_encuesta_pregunta['cantidad_registros']; $ep++) {

		$html_radio_params='';

		$valor_respuesta='';
		for ($epar=0; $epar < $consulta_encuesta_param_reg['cantidad_registros']; $epar++) {

			$sql_respuesta_pregunta="SELECT erep_id FROM encuesta_respuesta_pregunta WHERE eg_fk=".$consulta_encuesta_grupo[$eg]['eg_id']." AND ep_fk=".$consulta_encuesta_pregunta[$ep]['ep_id']." AND epa_fk=".$consulta_encuesta_param_reg[$epar]['epa_id']." AND ere_fk=".$ere_id;
			$consulta_respuesta_pregunta=$bd->consulta($sql_respuesta_pregunta);


			$marcar_respuesta='';
			if($consulta_respuesta_pregunta['cantidad_registros']){
				$marcar_respuesta='X';
				$valor_respuesta=$consulta_encuesta_param_reg[$epar]['epa_valor'];
				$total_respuestas=$total_respuestas+intval($consulta_encuesta_param_reg[$epar]['epa_valor']);
			}
			$html_radio_params.='
				<td class="text-center font-weight-bold align-middle">
					'.$marcar_respuesta.'
				</td>
			';

			if( ($epar+1)== $consulta_encuesta_param_reg['cantidad_registros'] ){
				$html_radio_params.='<td class="text-center font-weight-bold align-middle"><h5>'.$valor_respuesta.'</h5></td>';
			}
		}

		$html_encuesta_pregunta.='
			<tr>
				<td class="text-center">'.$consulta_encuesta_pregunta[$ep]['ep_titulo'].'</td>
				<td>'.$consulta_encuesta_pregunta[$ep]['ep_descripcion'].'</td>
				'.$html_radio_params.'
			</tr>
		';
	}

	$html_encuesta.='
		<tr>
			<th rowspan="2" class="text-center">Variable</th>
			<th rowspan="2" class="text-center">Explicaci&oacute;n</th>
				'.$html_params_titulo.'
		</tr>
		<tr>
				'.$html_params_valor.'
		</tr>
	';
	$html_encuesta.=$html_encuesta_pregunta;

	if ($cont <= 4) {
		$total = $total_respuestas / $consulta_encuesta_pregunta['cantidad_registros'];
		$sumaTotal1 = $sumaTotal1 + $total;
	}else{
		$total = $total_respuestas / $consulta_encuesta_pregunta['cantidad_registros'];
		$sumaTotal2 = $sumaTotal2 + $total;
	}

	if ($cont == 4) {
		$html_promedio = '<h1 class="text-center">Promedio Organizacional: '. number_format(($sumaTotal1 / 4), 2, '.', '').'</h1>';
	}elseif ($cont == 7) {
		$html_promedio .= '<h1 class="text-center">Promedio Lideres: '. number_format(($sumaTotal2 / 3), 2, '.', '').'</h1>';
	}

	$cont++;

	$html_encuesta.='<tr><th colspan="'.($consulta_encuesta_param_reg['cantidad_registros']+2).'" class="text-center">Promedio</th><th class="text-center font-weight-bold align-middle"><h4>'. $total .'</h4></th></tr>';

	$html_encuesta.='</table>';
	//Valido si cumple con el requisito
	
	if ($_GET['idUsu']) {
		$usuario = $bd->consulta("SELECT * FROM ceg_funcionario_atr WHERE fun_atr_nombre = 'creser_nivel' AND fun_fk = :fun_fk", array(":fun_fk" => $_GET['idUsu']));

		$sql_validar_nivel = "SELECT * FROM creser_u_com_nivel INNER JOIN creser_competencias ON creser_competencias.cc_id = creser_u_com_nivel.cc_fk WHERE cn_fk = :cn_fk AND fk_eg = :fk_eg ";


		$consulta_validar_nivel = $bd->consulta($sql_validar_nivel, array(":cn_fk" => $usuario[0]['fun_atr_valor'], 
																																			":fk_eg" => $consulta_encuesta_grupo[$eg]['eg_id']
																																		));

		if ($consulta_validar_nivel[0]['ccn_nivel_esperado'] > ($total_respuestas / $consulta_encuesta_pregunta['cantidad_registros'])) {
			$scripts .= '$("#competencia_'. $consulta_encuesta_grupo[$eg]['eg_id'] .'").addClass("table-danger");';
		}
		
	}

}

$bd->desconectar();

$lib = new Libreria();

?>
<!DOCTYPE html>
<html>
<head>
	<title>Consumer Electronocs Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->intranet();
  ?>
</head>
<body>
	<BR>
	<div class="container mt-5 mb-3">

		<h1 class="text-center mb-5"><?php echo($titulo_encuesta); ?></h1>
		<p><?php echo($descripcion_encuesta); ?></p>

		<?php 
			echo($html_encuesta); 
			echo($html_promedio);
		?>

	</div>
</body>
<?php  
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
	$(function(){
		$('[data-toggle="tooltip"]').tooltip();

		<?php echo $scripts; ?>
		//$('#competencia_1').addClass('table-danger');
	});
</script>
</html>
