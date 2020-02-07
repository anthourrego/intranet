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
  	.lista_autocompletar_ciudad_fk{
  		cursor:pointer;
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
			
			var continuar_submit=1;
			var misma_direccion_despacho_factura=parseInt($('.misma_direccion_despacho_factura:checked').val());
			
			$('.funco_det_cuotas').each(function(){
				if(!$(this).val() || $(this).val()=='' || $(this).val()=='0' || $(this).val()==0){
					alertify.alert('Atencion', 'Debe ingresar una cuota valida para continuar!');
					continuar_submit=0;
					return false;
				}
			});					
			
			if(!misma_direccion_despacho_factura || misma_direccion_despacho_factura==0){

				$('.funco_det_dir_direccion').each(function(){
					if(!$(this).val() && $(this).val()==''){
						alertify.alert('Atencion', 'Debe completar todas las direcciones de despacho para continuar!');
						continuar_submit=0;
						return false;
					}
				});				

				$('.ciudad_fk').each(function(){
					if(!$(this).val() && $(this).val()==''){
						alertify.alert('Atencion', 'Debe completar todas las ciudades de despacho para continuar!');
						continuar_submit=0;
						return false;
					}
				});					
			}

			
			if($("#form_completar_pedido").valid() && continuar_submit){
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
		
		

		$(document).on('click','.misma_direccion_despacho_factura',function(){

			var misma_direccion_despacho_factura=parseInt($(this).val());

			if(misma_direccion_despacho_factura){
				$('.contenedor_direccion_despacho').fadeOut();
			}else{
				$('.contenedor_direccion_despacho').fadeIn("slow");
			}
		});
		
		
		
		//AUTOCOMPLETAR CIUDAD

		$(document).on('keyup','.autocompletar_ciudad_fk',function(){
			var ciu_descripcion=$(this).val();
			var key=$(this).attr('key');
			
			
			if(ciu_descripcion && ciu_descripcion!='' && ciu_descripcion.length>=3){
				
				$.ajax({
					type:'POST',
					dataType: 'json',
					url: "<?php echo(direccionIPRuta()); ?>funcionario_compra/ejecutar_acciones.php",
					async:false,
					data:{
						ejecutar_accion:'funcionario_compra_autocompletar_ciudad_fk',
						ciu_descripcion:ciu_descripcion,
						key:key
					},
					success:function(retorno){
						$('#lista_autocompletar_ciudad_fk_'+key).remove();
						$('#autocompletar_ciudad_fk_'+key).after(retorno.ul);
					}	
				});				
					
			}
			
		});	
		
		$(document).on('click','.autocompletar_ciudad_fk_item',function(){
			var ciudad_fk=$(this).attr('ciudad_fk');
			var ciu_descripcion=$(this).text();
			var key=$(this).attr('key');
			
			$('#lista_autocompletar_ciudad_fk_'+key).remove();
			$('#autocompletar_ciudad_fk_'+key).hide();
			$('#autocompletar_ciudad_fk_'+key).val('');
			
			var seleccionado=`
				<span>${ciu_descripcion} <i class="fas fa-times autocompletar_ciudad_fk_seleccionado_quitar" key="${key}"></i></span>
			`;
			$('#autocompletar_ciudad_fk_'+key).after(seleccionado);
			$('#ciudad_fk_'+key).val(ciudad_fk);
		});
		
		$(document).on('click','.autocompletar_ciudad_fk_seleccionado_quitar',function(){
			var key=$(this).attr('key');
			$(this).parent('span').remove();
			$('#ciudad_fk_'+key).val('');
			$('#autocompletar_ciudad_fk_'+key).show();
		});	
		
		
		//FIN AUTOCOMPLETAR CIUDAD		
		
			
	});
</script>