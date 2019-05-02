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
	header('location: '.RUTA_RAIZ.'central');
	die();
}

$bd=new Bd();
$bd->conectar();


$et_id=@$_REQUEST['et_id'];
$sql_encuesta_tipo="SELECT * FROM encuesta_tipo WHERE et_estado=1 AND et_id= :et_id";
$consulta_encuesta_tipo=$bd->consulta($sql_encuesta_tipo, array(":et_id" => $et_id));

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



//atributos
$sql_atr="SELECT * FROM encuesta_atr WHERE ea_estado=1 AND et_fk = :et_fk";
$consulta_atr=$bd->consulta($sql_atr, array(":et_fk"=>$et_id));

if( $consulta_atr['cantidad_registros'] ){
	
	$html_encuesta.='<table class="table table-bordered">';

	for ($i=0; $i < $consulta_atr['cantidad_registros']; $i++) {

		$input_atr="";
		$name_atr='atr_'.$consulta_atr[$i]['ea_id'];
		$readonly_atr=$consulta_atr[$i]['ea_readonly'];
		$predeterminado_atr=$consulta_atr[$i]['ea_predeterminado'];
		$funcion_atr=$consulta_atr[$i]['ea_funcion_insert'];
		
		if($funcion_atr && function_exists($funcion_atr)){
			$input_atr=($funcion_atr($name_atr));
		}else{
			switch ($consulta_atr[$i]['ea_type']) {
				case 'varchar':
					$input_atr='<input class="form-control input-lg" type="text" name="'.$name_atr.'" id="'.$name_atr.'" value="'.$predeterminado_atr.'">';
					break;
				case 'datetime':
					$fecha_predeterminada=$predeterminado_atr;
					if($predeterminado_atr=='now()'){
						$fecha_predeterminada=date('Y-m-d H:i:s');
					}
				
					
					if($readonly_atr){
						$input_atr='<input class="form-control input-lg" type="text" name="'.$name_atr.'" id="'.$name_atr.'" value="'.$fecha_predeterminada.'" readonly/>';
					}else{
						$input_atr='
						    <input type="text" name="'.$name_atr.'" id="'.$name_atr.'" value="'.$fecha_predeterminada.'" />
						    <script>
								$("#'.$name_atr.'").datetimepicker({
									 formatDate:"Y-m-d H:i:s",
								});
						    </script>							
						';					
					}
						
									
					break;		
				case 'int':
					$input_atr='<input class="form-control input-lg" type="number" name="'.$name_atr.'" id="'.$name_atr.'" value="'.$predeterminado_atr.'">';
					break;	
				case 'text':
					$input_atr='<textarea class="form-control" name="'.$name_atr.'" id="'.$name_atr.'" >'.$predeterminado_atr.'</textarea';
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

		$html_rules_validate.='
				'.$name_atr.':{
					required: true
				},
		';
		$html_msj_validate.='
			'.$name_atr.':{
				required: "Debe ingresar un '.$consulta_atr[$i]['ea_titulo'].'"
			},
		';

	}
	$html_encuesta.='
		</table>
	';
}

$sql_encuesta_grupo="SELECT * FROM encuesta_grupo a, encuesta_reg b WHERE eg_estado=1 AND a.eg_id=b.eg_fk AND b.et_fk= :bet_fk";
$consulta_encuesta_grupo=$bd->consulta($sql_encuesta_grupo, array(":bet_fk" =>$et_id));

for ($eg=0; $eg < $consulta_encuesta_grupo['cantidad_registros']; $eg++) {

	$html_encuesta.='<table class="table table-bordered">';


	$sql_encuesta_pregunta="SELECT * FROM encuesta_pregunta WHERE ep_estado=1 AND eg_fk=:eg_fk";
	$consulta_encuesta_pregunta=$bd->consulta($sql_encuesta_pregunta, array(":eg_fk" => $consulta_encuesta_grupo[$eg]['eg_id']));

	$sql_encuesta_param_reg="SELECT * FROM encuesta_param_reg a, encuesta_param b WHERE b.epa_estado=1 AND a.epa_fk=b.epa_id AND a.eg_fk = :aeg_fk";
	$consulta_encuesta_param_reg=$bd->consulta($sql_encuesta_param_reg ,array(":aeg_fk" =>$consulta_encuesta_grupo[$eg]['eg_id']));


	//encabezado parametros
	$html_params_titulo='';
	$html_params_valor='';
	for ($epar=0; $epar < $consulta_encuesta_param_reg['cantidad_registros']; $epar++) {
		$html_params_titulo.='<th class="text-center" tabindex="0" data-toggle="tooltip" title="' . $consulta_encuesta_param_reg[$epar]['epa_descripcion'] . '">'.$consulta_encuesta_param_reg[$epar]['epa_titulo'].'</th>';
		$html_params_valor.='<th class="text-center">'.$consulta_encuesta_param_reg[$epar]['epa_valor'].'</th>';
	}

	
	$html_encuesta.='<tr><th colspan="'.($consulta_encuesta_param_reg['cantidad_registros']+3).'" class="text-center">'.$consulta_encuesta_grupo[$eg]['eg_titulo'].'</th></tr>';
	if($consulta_encuesta_grupo[$eg]['eg_descripcion']){
		$html_encuesta.='<tr><td colspan="'.($consulta_encuesta_param_reg['cantidad_registros']+3).'" class="text-center">'.$consulta_encuesta_grupo[$eg]['eg_descripcion'].'</td></tr>';
	}
		
	//preguntas
	$html_encuesta_pregunta='';
	for ($ep=0; $ep < $consulta_encuesta_pregunta['cantidad_registros']; $ep++) {

		$html_radio_params='';

		$name_item='grupo_'.$consulta_encuesta_grupo[$eg]['eg_id'].'_pregunta_'.$consulta_encuesta_pregunta[$ep]['ep_id'].'';
		$html_rules_validate.='
				'.$name_item.':{
					required: true
				},
		';
		$html_msj_validate.='
			'.$name_item.':{
				required: "Debe elegir una Respuesta"
			},
		';

		for ($epar=0; $epar < $consulta_encuesta_param_reg['cantidad_registros']; $epar++) {
			$html_radio_params.='
				<td class="text-center">
					<input type="radio" name="'.$name_item.'" value="'.$consulta_encuesta_param_reg[$epar]['epa_id'].'">
				</td>
			';
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

	$html_encuesta.='</table>';


}
$html_encuesta.='<input type="hidden" name="et_fk" id="et_fk" value="'.$et_id.'">';
$html_encuesta.='<input type="hidden" name="ejecutar_accion" id="ejecutar_accion" value="guardar_encuesta">';

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
    echo $lib->datatables();
    echo $lib->jqueryValidate();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container-fluid mt-3">
    <a class="btn btn-secondary link-atras" href="#"><i class="fas fa-arrow-left"></i> Atrás</a>
    <hr>
  </div>

	<div class="container mt-4 mb-5">

		<h1 class="text-center mb-5"><?php echo($titulo_encuesta); ?></h1>
		<p><?php echo($descripcion_encuesta); ?></p>
		<form id="formulario_encuesta">

			<?php echo($html_encuesta); ?>
			<br>
			<input class="btn btn-primary float float-right" type="button" name="enviar_formulario_encuesta" id="enviar_formulario_encuesta" value="Enviar"/>
		</form>
	</div>
</body>
<?php  
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
  	$('[data-toggle="tooltip"]').tooltip();


    $("#formulario_encuesta").validate({
      debug: false,
      rules:{
        <?php echo($html_rules_validate); ?>
      },
      messages: {
        <?php echo($html_msj_validate); ?>
      }

    });

    $(document).on('click', '#enviar_formulario_encuesta', function () {
      if( $("#formulario_encuesta").valid() ){

        var formData = new FormData(document.getElementById("formulario_encuesta"));
        $.ajax({
          type:'POST',
          dataType: 'json',
          url: "ejecutar_acciones.php",
          cache: false,
          contentType: false,
          processData: false,
          data: formData,
          success:function(datos){
            //alertify.success('Encuesta Registrada Satisfactoriamente');
            setTimeout(function(){
              window.history.back();
              //window.location.reload();
            }, 2000);
          }
        });
      }else{
        alert('Existen preguntas sin contestar, favor verificar');
      }
    });
  });
  </script>
</html>	
