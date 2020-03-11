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

	include_once($ruta_raiz . 'clases/librerias.php');
	include_once($ruta_raiz . 'clases/sessionActiva.php');
  
	$lib = new Libreria; 
  
	$funco_id=@$_REQUEST['funco_id'];   
	
	$titulo_pantalla='Revisar Capacidad de Compra';
	
	
	
	$usuario = $session->get("usuario");	
	$fun_id=$usuario['id'];	

?>
<!DOCTYPE html>
<html>
	<head>
	<title><?php echo($titulo_pantalla); ?></title>
	

  <?php  
	echo $lib->metaTagsRequired();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
	echo $lib->jqueryValidate();
  ?>
</head>
<body>
	<h3><?php echo($titulo_pantalla); ?></h3>
	<form id="form_revisar_capacidad">
	<div id="contenido_compra_detalle">
		
	</div>	
	<div>
		<strong>Observaci&oacute;n:</strong>
		<br>
		<textarea class="form-control" id="funco_paso_observacion" name="funco_paso_observacion"></textarea>
	</div>
	<div>
		<br>
		<button type="button" id="submit_form_revisar_capacidad" name="submit_form_revisar_capacidad" class="btn btn-primary float float-right" name="action">Enviar <i class="fas fa-arrow-right" style="font-size: 18px;"></i></button>	
		<input type="hidden" name="ejecutar_accion" id="ejecutar_accion" value="funcionario_compra_revisar_capacidad_update" />
		<input type="hidden" name="funco_id" id="funco_id" value="<?php echo($funco_id); ?>"/>
		<input type="hidden" name="fun_id" id="fun_id" value="<?php echo($fun_id); ?>" />		
	</div>
	</form>
</body>
</html>	  


<script>
	function parse_moneda_entero(entero){
		var parse=new Intl.NumberFormat().format(entero);
		return(parse);		
	}

	function sumatoria_productos_carritos(){
		
		//precio total
		var total=0;
		$(".total_pagar_aprobado").each(function(){
			total=total+parseInt($(this).val());
		});
		$('.contenedor_total').html('$'+parse_moneda_entero(total));
		
		//iva
		var iva=total*0.19;
		$('.contenedor_iva').html('$'+parse_moneda_entero(iva));
				
		//subtotal
		var subtotal=total-iva;
		$('.contenedor_subtotal').html('$'+parse_moneda_entero(subtotal));

	}
	
	$(document).ready(function(){
		
		$.ajax({
			type:'POST',
			dataType: 'json',
			url: "<?php echo(RUTA_CONSULTAS); ?>funcionario_compra/ejecutar_acciones.php",
			async:false,
			data:{
				ejecutar_accion:'funcionario_compra_revisar_capacidad_html',
				funco_id:<?php echo($funco_id); ?>
			},
			success:function(retorno){
				$('#contenido_compra_detalle').html(retorno.html);
				top.cerrarCargando();
			}	
		});
		
		
		
		$(document).on('click','.aprobar_misma_cantidad',function(){
			var funco_det_id=$(this).attr('funco_det_id');
			
			if($(this).is(':checked')){
				var precio_unitario=$(this).val();
				$('#funco_det_cantidad_aprobada_'+funco_det_id).val(precio_unitario);
				$('#funco_det_cantidad_aprobada_'+funco_det_id).attr('readonly',true);
			}else{
				$('#funco_det_cantidad_aprobada_'+funco_det_id).val(0);
				$('#funco_det_cantidad_aprobada_'+funco_det_id).attr('readonly',false);
			}
			
			$('#funco_det_cantidad_aprobada_'+funco_det_id).trigger('keyup');
		});
		
		
		//SUBMIT FORM
		$("#form_revisar_capacidad").validate();
		$('#submit_form_revisar_capacidad').click(function(){
	
			if($("#form_revisar_capacidad").valid()){
				var formData = new FormData(document.getElementById("form_revisar_capacidad"));	
				$.ajax({
					type:'POST',
					dataType: 'json',
					url: "<?php echo(RUTA_CONSULTAS); ?>funcionario_compra/ejecutar_acciones.php",
					cache: false,
					contentType: false,
					processData: false,					
					data: formData,
					success:function(datos){
						if(datos.exito==1){
							
							parent.alertify.success('Revision de capacidad de endeudamiento Satisfactoria!'); 
							parent.$('#modal_visualizar_compra_funcionario').modal('hide');
							setTimeout(function(){
								parent.iniciar_consulta();
							}, 500);
						}	
					}	
				});		
			}	
			
		});	
		
		
		$(document).on('keyup','.funco_det_cantidad_aprobada',function(){
			var funco_det_id=$(this).attr('funco_det_id');		
			var funco_det_cantidad_aprobada=$(this).val();
			var funco_det_precio_unitario_aprobado=$('#funco_det_precio_unitario_aprobado_'+funco_det_id).val();	
			var tota_pagar_aprobado=funco_det_cantidad_aprobada*funco_det_precio_unitario_aprobado;
			$('#total_pagar_aprobado_'+funco_det_id).val(tota_pagar_aprobado);
			
			$('#contenedor_tota_pagar_aprobado_'+funco_det_id).html('$'+parse_moneda_entero(tota_pagar_aprobado));
			
			sumatoria_productos_carritos();
		});	
		
			
	});
</script>