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

  include_once($ruta_raiz . 'clases/librerias.php');
  include_once($ruta_raiz . 'clases/sessionActiva.php');
  //include_once($ruta_raiz . 'clases/Conectar.php');

  $usuario = $session->get("usuario");
  $ruta_documentos = array();

  $lib = new Libreria;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Solicitud Compra Funcionario</title>
  
  <?php  
  	echo $lib->metaTagsRequired();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
	echo $lib->cambioPantalla();
	echo $lib->jqueryValidate();
	echo $lib->alertify();
  ?>
</head>
<body>

	<div class="container contenedor_general">
		<div class="row titulo_general_row">
			<div class="col-12 titulo_general">
				<h1>Solicitud Compra Funcionario</h1>	
			</div>
		</div>
		<br>
		<form id="formulario_funcionario_compra" name="formulario_funcionario_compra" enctype="multipart/form-data" accept-charset="utf-8">
			
			<div class="row">
				<div class="col-sm-12">
					<span id="oc" class="badge badge-pill badge-primary" style="font-size: 100%"><?php echo($usuario['nombre']); ?></span>
					<input type="hidden" name="fun_fk" id="fun_fk" value="<?php echo($usuario['id']); ?>" />
					<input type="hidden" name="funco_estado" id="funco_estado" value="1" />
					<span id="funco_fecha" class="float float-right badge badge-pill badge-primary" style="font-size: 100%"><?php echo(date('Y-m-d')); ?></span>
				</div>
			</div>
			<br>
						
			<div class="card shadow p-3 mb-5 bg-white rounded">	
				<div class="alert alert-primary text-center" role="alert">
				  <b class="mx-auto">Informaci&oacute;n General</b> 
				</div>					
				<div class="row">
					<div class="form-group col-sm-12 col-md-12">

						  <label class="col-md-4 control-label" for="funco_observacion">Observacion</label>
						  <div class="col-md-12">                     
						    <textarea class="form-control" id="funco_observacion" name="funco_observacion"></textarea>
						  </div>
						
					</div>
				</div>	
				
				<div class="row">
					<div class="form-group col-sm-12 col-md-12">
						
						  <label class="col-md-6 control-label" for="radios">Factura a nombre de <strong><?php echo($usuario['nombre']); ?></strong></label>
						  <div class="col-md-6"> 
						    <label class="radio-inline" for="funcionario_compra_factura-0">
						      <input class="funcionario_compra_factura" type="radio" name="funcionario_compra_factura" id="funcionario_compra_factura-0" value="0" >
						      No
						    </label> 
						    <label class="radio-inline" for="funcionario_compra_factura-1">
						      <input class="funcionario_compra_factura"  type="radio" name="funcionario_compra_factura" id="funcionario_compra_factura-1" value="1" checked="checked">
						      Si
						    </label> 
						  </div>	
					</div>
				</div>										
			
			</div>			

			<!-- contenedor datos facturacion -->
			<div class="card shadow p-3 mb-5 bg-white rounded contenedor_funcionario_compra_factura_tercero">
				<div class="alert alert-primary text-center" role="alert">
				  <b class=" mx-auto">Datos de Facturaci&oacute;n</b> 
				</div>	
				<hr>	
				<div class="row">
					<div class="form-group col-sm-6 col-md-6">
						<label for="funco_factura_nit">Cedula / Nit*</label>
						<input type="text" class="form-control funcionario_compra_factura_input" id="funco_factura_nit" name="funco_factura_nit">						
					</div>
					<div class="form-group col-sm-6 col-md-6">
						<label for="funco_factura_ciudad_fk">Ciudad*</label>
						<br>
						<input type="text" class="form-control" id="autocompletar_ciudad" name="autocompletar_ciudad">	
						<input type="hidden" class="funcionario_compra_factura_input" name="funco_factura_ciudad_fk" id="funco_factura_ciudad_fk" />					
					</div>					
				</div>						
				<div class="row">
					<div class="form-group col-sm-12 col-md-12">
						<label for="funco_factura_nombre">Nombre*</label>
						<input type="text" class="form-control funcionario_compra_factura_input" id="funco_factura_nombre" name="funco_factura_nombre">						
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-12 col-md-12">
						<label for="funco_factura_direccion">Direcci&oacute;n*</label>
						<input type="text" class="form-control funcionario_compra_factura_input" id="funco_factura_direccion" name="funco_factura_direccion">						
					</div>
				</div>				
				<div class="row">
					<div class="form-group col-sm-6 col-md-6">
						<label for="funco_factura_email">Email*</label>
						<input type="text" class="form-control funcionario_compra_factura_input" id="funco_factura_email" name="funco_factura_email">						
					</div>
					<div class="form-group col-sm-6 col-md-6">
						<label for="funco_factura_telefono">Telefono</label>
						<input type="text" class="form-control" id="funco_factura_telefono" name="funco_factura_telefono">						
					</div>					
				</div>
			</div>
			<!-- fin contenedor datos facturacion -->
			
			
			<div class="card shadow p-3 mb-5 bg-white rounded">	
				<div class="alert alert-primary text-center" role="alert">
				  <b class=" mx-auto">Detalle de la Compra</b> 
				</div>			
				<hr>
				<div class="row">
					<div class="form-group col-sm-12 col-md-12	">
						<label class="control-label" for="select_producto_linea">L&iacute;nea</label>
						<select id="select_producto_linea" name="select_producto_linea" class="form-control">
							<option value="">Seleccione...</option>
							<option value="tvs">Televisores</option>
							<option value="audioyvideo">Audio y Video</option>
							<option value="LineaBlanca">L&iacute;nea Blanca</option>
						</select>		
						<input type="hidden" id="producto_receptor" />	
					</div>				
				</div>				

				<div class="row">
					<div class="col-sm-12 tbscroll">
						<table class="table table-striped tablesc" id="fixTable">
							<thead>
								<tr>
									<th>Acciones</th>
		                            <th>Producto</th>
		                            <th>Bodega</th>
		                            <th class="text-right">Cantidad</th>
		                            <th class="text-right">Precio Unitario</th>
		                            <th class="text-right">Total</th>
								</tr>
							</thead>
							<tbody class="contenido_productos_carrito">
								
							</tbody>
						</table>
					</div>
				</div>
				<h1></h1>

				<p></p>
				<div class="row">
					<div class="col-sm-7"></div>
					<div class="col-sm-2">
						<h4><strong>Subtotal:</strong></h4>						
					</div>
					<div class="col-sm-3">
						<div class="card text-right" style="border-radius: 5%;background-color:#e8eaf6; ">
							<span id="suma_subtotal_carrito" style="font-size: 18px;color:#5c6bc0;">0</span>
						</div>
					</div>
				</div>
				<p></p>
				<div class="row">
					<div class="col-sm-7"></div>
					<div class="col-sm-2">
						<h4><strong>Iva:</strong></h4>						
					</div>
					<div class="col-sm-3">
						<div class="card text-right" style="border-radius: 5%;background-color:#e8eaf6;">
							<span id="suma_iva_carrito" style="font-size: 18px;color:#5c6bc0;">0</span>
						</div>
					</div>
				</div>
				<p></p>
				<div class="row">
					<div class="col-sm-7"></div>
					<div class="col-sm-2">
						<h4><strong>Total:</strong></h4>						
					</div>
					<div class="col-sm-3">
						<div class="card text-right" style="border-radius: 5%;background-color:#e8eaf6;">
							<span id="suma_total_carrito" style="font-size: 18px;color:#5c6bc0;">0</span>
						</div>
					</div>
				</div>				
				
				<div class="row">
					<div class="col-sm-12">
						<br>
						<button type="button" id="submit_formulario_funcionario_compra" name="submit_formulario_funcionario_compra" class="btn btn-primary float float-right" name="action">Enviar <i class="fas fa-arrow-right" style="font-size: 18px;"></i></button>	
						<input type="hidden" name="cantidad_items_producto_carrito" id="cantidad_items_producto_carrito">
						<input type="hidden" name="ejecutar_accion" id="ejecutar_accion" value="funcionario_compra_insert">
					</div>
				</div>
			</div>
		</form>
		<br>

	</div>
	<div class="modal" tabindex="-1" role="dialog" id="modal_lista_productos" data-backdrop="static" 
  data-keyboard="false">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Seleccionar Producto</h5>
	        <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      	<iframe class="w-100" id="contenido_lista_productos" style="width:100%;height: 80vh; border: 0px;"></iframe>
	      </div>
	    </div>
	  </div>
	</div>  
</body>
</html>
<script>
	var funco_factura_nit=0;
	var funco_factura_ciudad_fk='';
	var funco_factura_ciudad_text='';
	var funco_factura_nombre='';
	var funco_factura_direccion='';
	var funco_factura_email='';
	var funco_factura_telefono='';

	function datos_facturacion_funcionario_compra(){
		
		
		if(!funco_factura_nit){

			$.ajax({
				type:'POST',
				dataType: 'json',
				url: "<?php echo(direccionIPRuta()); ?>funcionario_compra/ejecutar_acciones.php",
				async:false,
				data:{
					ejecutar_accion:'datos_facturacion_funcionario_compra',
					fun_id:<?php echo($usuario['id']); ?>
				},
				success:function(retorno){
					
					if(retorno.exito){
					
						funco_factura_nit=retorno.CEDULA;
						funco_factura_ciudad_fk=retorno.CIUDAD;
						funco_factura_ciudad_text=retorno.CIUDAD_DESCRIPCION;
						funco_factura_nombre=retorno.NOMBRE;
						funco_factura_direccion=retorno.DIRECCION;
						funco_factura_email=retorno.EMAIL;
						
						var cadena_telefono='';
						if(retorno.TELEFONO && retorno.TELEFONO!=''){
							cadena_telefono+=retorno.TELEFONO+' - ';
						}
						if(retorno.CELULAR && retorno.CELULAR!=''){
							cadena_telefono+=retorno.CELULAR;
						}
						funco_factura_telefono=cadena_telefono;
						
					}
				}	
			});	

		}
		
		
		//asignacion inputs
		$('#funco_factura_nit').val(funco_factura_nit);
		$('#funco_factura_nit').attr('readonly',true);
		$('#funco_factura_ciudad_fk').val(funco_factura_ciudad_fk);
		$('#funco_factura_nombre').val(funco_factura_nombre);
		$('#funco_factura_nombre').attr('readonly',true);
		$('#funco_factura_direccion').val(funco_factura_direccion);
		$('#funco_factura_direccion').attr('readonly',true);
		$('#funco_factura_email').val(funco_factura_email);
		$('#funco_factura_email').attr('readonly',true);
		$('#funco_factura_telefono').val(funco_factura_telefono);
		$('#funco_factura_telefono').attr('readonly',true);
		
		//autocompletar ciudad
		$('#autocompletar_ciudad').hide();
		$('#lista_autocompletar_ciudad').remove();
		$('#autocompletar_ciudad').after('<span id="seleccionado_autocompletar_ciudad">'+funco_factura_ciudad_text+' &nbsp; <i class="eliminar_seleccionado_autocompletar_ciudad fas fa-times-circle"></i></span>');
		$('.eliminar_seleccionado_autocompletar_ciudad ').hide();
		
		
	}

	function parse_moneda_entero(entero){
		var parse=new Intl.NumberFormat().format(entero);
		return(parse);		
	}

	function sumatoria_productos_carritos(){
		
		//precio total
		var total=0;
		$(".item_producto_carrito_total").each(function(){
			total=total+parseInt($(this).val());
		});
		$('#suma_total_carrito').html('$'+parse_moneda_entero(total));
		
		//iva
		var iva=total*0.19;
		$('#suma_iva_carrito').html('$'+parse_moneda_entero(iva));
				
		//subtotal
		var subtotal=total-iva;
		$('#suma_subtotal_carrito').html('$'+parse_moneda_entero(subtotal));

	}

	$(document).ready(function(){
		
		$(document).on('click','.funcionario_compra_factura',function(){
			var funcionario_compra_factura=parseInt($('.funcionario_compra_factura:checked').val());
			
			if(funcionario_compra_factura){
				
				datos_facturacion_funcionario_compra();
				
			}else{
				
				$('#funco_factura_nit').val('');
				$('#funco_factura_nit').attr('readonly',false);
				$('#funco_factura_ciudad_fk').val('');
				$('#funco_factura_nombre').val('');
				$('#funco_factura_nombre').attr('readonly',false);
				$('#funco_factura_direccion').val('');
				$('#funco_factura_direccion').attr('readonly',false);
				$('#funco_factura_email').val('');
				$('#funco_factura_email').attr('readonly',false);
				$('#funco_factura_telefono').val('');
				$('#funco_factura_telefono').attr('readonly',false);
				
				//autocompletar ciudad
				$('#autocompletar_ciudad').val('');
				$('#autocompletar_ciudad').show();
				$('#seleccionado_autocompletar_ciudad').remove();


				$('.funcionario_compra_factura_input').each(function(){
					$(this).attr('required',true);
				});					
							
			}
			
		});



		//AUTOCOMPLETAR CIUDAD
		$(document).on('keyup','#autocompletar_ciudad',function(){
			var ciudad=$(this).val();
				
			if(ciudad.length>=3){
				$.ajax({
					type:'POST',
					dataType: 'json',
					url: "<?php echo(direccionIPRutaBase()); ?>modulos/backorder/ejecutar_acciones.php",
					async:false,
					data:{
						ejecutar_accion:'autocompletar_ciudad',
						ciudad:ciudad
					},
					success:function(datos){
						$('#lista_autocompletar_ciudad').remove();
						$('#autocompletar_ciudad').after(datos.lista);
					}	
				});					
			}else{
				$('#lista_autocompletar_ciudad').remove();
			}			
		});		
		$(document).on('click','.opcion_autocompletar_ciudad',function(){
			var valor=$(this).attr('valor');
			var text=$(this).html().trim();
				
			$('#lista_autocompletar_ciudad').remove();
			$('#autocompletar_ciudad').hide();
			$('#autocompletar_ciudad').val('');
			$('#funco_factura_ciudad_fk').val(valor);
			$('#autocompletar_ciudad').after('<span id="seleccionado_autocompletar_ciudad">'+text+' &nbsp; <i class="eliminar_seleccionado_autocompletar_ciudad fas fa-times-circle"></i></span>');
		});	
		$(document).on('click','.eliminar_seleccionado_autocompletar_ciudad',function(){
			if(confirm('Esta seguro de quitar la ciudad seleccionada?')){			
				$('#seleccionado_autocompletar_ciudad').remove();
				$('#funco_factura_ciudad_fk').val(0);
				$('#autocompletar_ciudad').show();				
			}
		});		
		//FIN AUTOCOMPLETAR ciudad


		//MODal productos
		$('#select_producto_linea').change(function(){
			var linea=$(this).val();
			if(linea!=''){
				top.$("#cargando").modal("show");
				var enlace='producto_seleccionar.php?linea='+linea;
				$('#modal_lista_productos').modal('show');
				$("#contenido_lista_productos").attr("src", enlace);				
			}

		});


		//agregar al carrito
		$('#producto_receptor').change(function(){
			
			var cantidad_items_producto_carrito=$('#cantidad_items_producto_carrito').val();
			cantidad_items_producto_carrito++;
			$('#cantidad_items_producto_carrito').val(cantidad_items_producto_carrito);
			
			var producto_bodega_cantidad_precio=$(this).val();
			var vector=producto_bodega_cantidad_precio.split('|');
			
			
			var producto=vector[0];
			var bodega=vector[1];
			var cantidad=vector[2];
			var precio=vector[3];
			var referencia=vector[4];
			var total=cantidad*precio;
			
			
			var parse_cantidad=parse_moneda_entero(cantidad);
			var parse_precio=parse_moneda_entero(precio);
			var parse_total=parse_moneda_entero(total);
			
			var html_agregar_carrito=`
				<tr class="tr_item_producto_carrito" id="tr_item_producto_carrito_${cantidad_items_producto_carrito}">
					<td>
						<i class="fas fa-minus-circle item_producto_carrito_eliminar" id="item_producto_carrito_eliminar_${cantidad_items_producto_carrito}" key="${cantidad_items_producto_carrito}"></i>
					</td>
					<td>
						${referencia}
						<input type="hidden" class="item_producto_carrito_producto" name="item_producto_carrito_producto_${cantidad_items_producto_carrito}" id="item_producto_carrito_producto_${cantidad_items_producto_carrito}" value="${producto}">
					</td>
					<td>
						${bodega}
						<input type="hidden" class="item_producto_carrito_bodega" name="item_producto_carrito_bodega_${cantidad_items_producto_carrito}" id="item_producto_carrito_bodega_${cantidad_items_producto_carrito}" value="${bodega}">
					</td>
					<td class="text-right">
						${parse_cantidad}
						<input type="hidden" class="item_producto_carrito_cantidad" name="item_producto_carrito_cantidad_${cantidad_items_producto_carrito}" id="item_producto_carrito_cantidad_${cantidad_items_producto_carrito}" value="${cantidad}">
					</td>
					<td class="text-right">
						$${parse_precio}
						<input type="hidden" class="item_producto_carrito_precio" name="item_producto_carrito_precio_${cantidad_items_producto_carrito}" id="item_producto_carrito_precio_${cantidad_items_producto_carrito}" value="${precio}">
					</td>
					<td class="text-right">
						$${parse_total}
						<input type="hidden" class="item_producto_carrito_total" name="item_producto_carrito_total_${cantidad_items_producto_carrito}" id="item_producto_carrito_total_${cantidad_items_producto_carrito}" value="${total}">
					</td>
				</tr>
			`;
			
			
			$('.contenido_productos_carrito').prepend(html_agregar_carrito);
			
			
			$('#producto_receptor').val('');
			$('#select_producto_linea').val('');
			
			sumatoria_productos_carritos();
			
		});
		
		
		//cerrar modal productos
		$(document).on('click','.close_modal',function(){
			$('#producto_receptor').val('');
			$('#select_producto_linea').val('');			
		});
		
		//eliminar item carrito
		$(document).on('click','.item_producto_carrito_eliminar',function(){
			var key=$(this).attr('key');
			$('#tr_item_producto_carrito_'+key).remove();
			sumatoria_productos_carritos();
		});
		
		
		//DATOS FACTURACION EMPLEADO
		datos_facturacion_funcionario_compra();
		
		
		
		//SUBMIT FORM
		$("#formulario_funcionario_compra").validate();
		$('#submit_formulario_funcionario_compra').click(function(){
	
			if($("#formulario_funcionario_compra").valid()){
				var formData = new FormData(document.getElementById("formulario_funcionario_compra"));	
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
							alertify.success('Solicitud de compra registrada satisfactoriamente!'); 
							
							setTimeout(function(){
								window.location.reload();
							}, 1000);							
						}	
					}	
				});		
			}	
			
		});
	
		
	});
</script>