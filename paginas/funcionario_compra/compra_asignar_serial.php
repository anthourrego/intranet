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
	
	$titulo_pantalla='Asignar Seriales de Compra';
	
	
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
  
  <style>
  	.autocompletar_serial_serial{
  		cursor:pointer;
  	}
  	.lista_autocompletar_serial,.autocompletar_serial_serial{
  		z-index: 1 !important;
  	}
  </style>
</head>
<body>
	<h3><?php echo($titulo_pantalla); ?></h3>
	<form id="form_asignar_serial">
	<div id="contenido_compra_detalle">
		
	</div>	
	<div>
		<strong>Observaci&oacute;n:</strong>
		<br>
		<textarea class="form-control" id="funco_paso_observacion" name="funco_paso_observacion"></textarea>
	</div>
	<div>
		<br>
		<button type="button" id="submit_form_asignar_serial" name="submit_form_asignar_serial" class="btn btn-primary float float-right" name="action">Enviar <i class="fas fa-arrow-right" style="font-size: 18px;"></i></button>	
		<input type="hidden" name="ejecutar_accion" id="ejecutar_accion" value="funcionario_compra_asignar_serial_update" />
		<input type="hidden" name="funco_id" id="funco_id" value="<?php echo($funco_id); ?>"/>
		<input type="hidden" name="fun_id" id="fun_id" value="<?php echo($fun_id); ?>" />
	</div>
	</form>
</body>
</html>	  


<script>
	$(document).ready(function(){
		
		$.ajax({
			type:'POST',
			dataType: 'json',
			url: "<?php echo(direccionIPRuta()); ?>funcionario_compra/ejecutar_acciones.php",
			async:false,
			data:{
				ejecutar_accion:'funcionario_compra_asignar_serial_html',
				funco_id:<?php echo($funco_id); ?>
			},
			success:function(retorno){
				$('#contenido_compra_detalle').html(retorno.html);
				top.cerrarCargando();
			}	
		});
		
		
		
		//SUBMIT FORM
		$("#form_asignar_serial").validate();
		$('#submit_form_asignar_serial').click(function(){
			
			var continuar_submit=1;
			$('.serial_fk').each(function(){
				if(!$(this).val() && $(this).val()==''){
					alertify.alert('Atencion', 'Debe completar todos los seriales para continuar!');
					continuar_submit=0;
					return false;
				}
			});			
	
			if($("#form_asignar_serial").valid() && continuar_submit){
				var formData = new FormData(document.getElementById("form_asignar_serial"));	
				$.ajax({
					type:'POST',
					dataType: 'json',
					url: "<?php echo(direccionIPRuta()); ?>funcionario_compra/ejecutar_acciones.php",
					cache: false,
					contentType: false,
					processData: false,					
					data: formData,
					success:function(datos){
						if(datos.exito==1){
							
							parent.alertify.success('Asignacion de seriales Satisfactoria!'); 
							parent.$('#modal_visualizar_compra_funcionario').modal('hide');
							setTimeout(function(){
								parent.iniciar_consulta();
							}, 500);
							
						}	
					}	
				});		
			}	
			
		});	
		
		
		
		//AUTOCOMPLETAR SERIAL

		$(document).on('keyup','.autocompletar_serial',function(){
			var serial_serial=$(this).val();
			var key=$(this).attr('key');
			var funco_det_bodega=$('#funco_det_bodega_'+key).val();
			var PR_REFERENCIA=$('#PR_REFERENCIA_'+key).val();
			
			var serial_fk_cadena_not='';
			$('.serial_fk').each(function(){
				if($(this).val()!=''){
					serial_fk_cadena_not=serial_fk_cadena_not+$(this).val()+',';
				}
			});
			
			if(serial_serial && serial_serial!='' && serial_serial.length>=5){
				
				$.ajax({
					type:'POST',
					dataType: 'json',
					url: "<?php echo(direccionIPRuta()); ?>funcionario_compra/ejecutar_acciones.php",
					async:false,
					data:{
						ejecutar_accion:'funcionario_compra_autocompletar_serial',
						serial_serial:serial_serial,
						bodega_geminus_fk:funco_det_bodega,
						key:key,
						serial_fk_cadena_not,serial_fk_cadena_not,
						PR_REFERENCIA:PR_REFERENCIA
					},
					success:function(retorno){
						$('#lista_autocompletar_serial_'+key).remove();
						$('#autocompletar_serial_'+key).after(retorno.ul);
					}	
				});				
					
			}
			
		});	
		
		$(document).on('click','.autocompletar_serial_serial',function(){
			var serial_id=$(this).attr('serial_id');
			var serial_serial=$(this).text();
			var key=$(this).attr('key');
			
			$('#lista_autocompletar_serial_'+key).remove();
			$('#autocompletar_serial_'+key).hide();
			$('#autocompletar_serial_'+key).val('');
			
			var seleccionado=`
				<span>${serial_serial} <i class="fas fa-times autocompletar_serial_serial_seleccionado_quitar" key="${key}"></i></span>
			`;
			$('#autocompletar_serial_'+key).after(seleccionado);
			$('#serial_fk_'+key).val(serial_id);
		});
		
		$(document).on('click','.autocompletar_serial_serial_seleccionado_quitar',function(){
			var key=$(this).attr('key');
			$(this).parent('span').remove();
			$('#serial_fk_'+key).val('');
			$('#autocompletar_serial_'+key).show();
		});	
		
		
		//FIN AUTOCOMPLETAR SERIAL
		
			
	});
</script>