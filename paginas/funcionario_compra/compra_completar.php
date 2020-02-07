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
	
	$titulo_pantalla='Completar Pedido';
	
	
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
  	.contenedor_direccion_despacho{
  		display:none;
  	}
  </style>
  
</head>
<body>
	<h3><?php echo($titulo_pantalla); ?></h3>
	<form id="form_completar_pedido">
	<div id="contenido_compra_detalle">
		
	</div>	
	<div>
		<br>
		<button type="button" id="submit_form_completar_pedido" name="submit_form_completar_pedido" class="btn btn-primary float float-right" name="action">Enviar <i class="fas fa-arrow-right" style="font-size: 18px;"></i></button>	
		<input type="hidden" name="ejecutar_accion" id="ejecutar_accion" value="funcionario_compra_completar_pedido_update" />
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
				ejecutar_accion:'funcionario_compra_completar_html',
				funco_id:<?php echo($funco_id); ?>
			},
			success:function(retorno){
				$('#contenido_compra_detalle').html(retorno.html);
				top.cerrarCargando();
			}	
		});
		
		
		
		$(document).on('click','.funco_det_forma_pago',function(){
			var funco_det_id=$(this).attr('funco_det_id');
			var total=$(this).attr('total');
			var forma_pago=$(this).val();

			if(forma_pago=='credito'){
				$('#funco_det_anticipo_'+funco_det_id).attr('readonly',false);
				$('#funco_det_cuotas_'+funco_det_id).attr('readonly',false);
				
			}else{

				$('#funco_det_anticipo_'+funco_det_id).attr('readonly',true);
				$('#funco_det_anticipo_'+funco_det_id).val(total);
				$('#funco_det_cuotas_'+funco_det_id).attr('readonly',true);
				$('#funco_det_cuotas_'+funco_det_id).val(1);
				
			}

			
		});
		
		
		//SUBMIT FORM
		$("#form_completar_pedido").validate();
		$('#submit_form_completar_pedido').click(function(){
	
			if($("#form_completar_pedido").valid()){
				var formData = new FormData(document.getElementById("form_completar_pedido"));	
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
							
							parent.alertify.success('Revision de precios Satisfactoria!'); 
							parent.$('#modal_visualizar_compra_funcionario').modal('hide');
							setTimeout(function(){
								parent.iniciar_consulta();
							}, 500);
						}	
					}	
				});		
			}	
			
		});		
		
			
	});
</script>