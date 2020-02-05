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
  
	$tipo_linea=@$_REQUEST['linea'];   
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Seleccionar Producto</title>
	

  <?php  
	echo $lib->metaTagsRequired();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
  ?>


	<style>


		/* ================== Descripcion de productos en el catalogo =========================== */
	    
	    .productos {
	        border: 0px;
	        padding-bottom: 5px;
	        border-bottom: 5px solid transparent;
	       /*background-color: rgb(231, 232, 234);*/
	        border-radius: 10px !important;
	        transition: 0.7s border-bottom;
	        min-height: 480px !important;
			background: rgb(231,232,234);
			background: linear-gradient(90deg, rgba(231,232,234,1) 50%, rgba(0, 51, 160, 1) 50%);	        
	    }
	    
	    .productos:hover {
	        padding-bottom: -5px;
	        border-bottom: 5px solid rgba(0, 51, 160, 1);
	        transition: 0.7s border-bottom;
	    }
	    
	    .productos .img-producto {
	        -ms-transform: scale(1);
	        /*Internet Explorer */
	        -webkit-transform: scale(1);
	        /* Safari */
	        -moz-transform: scale(1);
	        /* Mozilla */
	        -o-transform: scale(1);
	        /* Opera */
	        transform: scale(1);
	        /* Estandar */
	        -ms-transition: 0.5s transform;
	        -webkit-transition: 0.5s transform;
	        -moz-transition: 0.5s transform;
	        -o-transition: 0.5s transform;
	        transition: 0.5s transform;
	    }
	    
	    .productos:hover .img-producto {
	        -ms-transform: scale(1.1);
	        /*Internet Explorer */
	        -webkit-transform: scale(1.1);
	        /* Safari */
	        -moz-transform: scale(1.1);
	        /* Mozilla */
	        -o-transform: scale(1.1);
	        /* Opera */
	        transform: scale(1.1);
	        /* Estandar */
	        -ms-transition: 0.5s transform;
	        -webkit-transition: 0.5s transform;
	        -moz-transition: 0.5s transform;
	        -o-transition: 0.5s transform;
	        transition: 0.5s transform;
	    }
	    
	    .productos>img {
	        left: 0;
	        position: absolute;
	        width: 100% !important;
	        border-radius: 0px 10px 0px 0px !important;
	    }
	    
	    .productos a {
	        text-decoration: none;
	        color: #000;
	    }
	    
	    .productos a:hover {
	        text-decoration: none;
	    }
	    
	    .row_producto{
	    	width:99% !important;
	    }	
	    
		.lista_bodegas{
			background-color:white;
			border-style:solid;
			border-radius:7px;
			border-color:white;
		}
		.precio_venta_empleado{
			color:white;
			font-weight:bold;
		}

	</style>
</head>
<body>
    <div>
    	<input type="search" id="input-search" value="" class="form-control" placeholder="Buscar Referencia">
    </div>
    <br>
	<div class="html_productos">
		
	</div>
</body>
</html>
<script>

	function productos_disponibles_funcionario_compra(){
		$.ajax({
			type:'POST',
			dataType: 'json',
			url: "<?php echo(direccionIPRuta()); ?>funcionario_compra/ejecutar_acciones.php",
			async:false,
			data:{
				ejecutar_accion:'productos_disponibles_funcionario_compra',
				linea:'<?php echo($tipo_linea); ?>'
			},
			success:function(retorno){
				$('.html_productos').html(retorno.html_productos);
				
				
				top.cerrarCargando();
			}	
		});			
	}

	$(document).ready(function(){

	    $('#input-search').on('keyup', function() {
	        var rex = new RegExp($(this).val(), 'i');
	        var rexPul = new RegExp($("#pulgadas").val(), 'i');
	        var cont = 0;
	        $('.searchable-container .items').hide();
	        $('#resp').hide();
	
	        $('.searchable-container .items').filter(function() {
	            if (rex.test($(this).find(".referencia").text()) == true && rexPul.test($(this).find(".pulgadas").text())) {
	                cont++;
	            }
	
	            if (rexPul.test($(this).find(".pulgadas").text())) {
	                return rex.test($(this).find(".referencia").text());
	            } else {
	                return false
	            }
	
	        }).show();
	
	        if (cont == 0) {
	            $('#resp').show();
	        }
	    });
	    
	    
	    $(document).on('click','.agregar_producto_carrito',function(){
	    	var key=$(this).attr('key');
	    	var cantidad=$('#producto_cantidad_'+key).val();
	    	if(!cantidad){
	    		alert('Debe ingresar una cantidad valida!');
	    		$('#producto_cantidad_'+key).focus();
	    	}else{
	    		top.$("#cargando").modal("show");
	    		
		    	var bodega=$('input:radio[name=producto_bodega_'+key+']:checked').val();
		    	var producto=$('#producto_producto_'+key).val();
		    	var referencia=$('#producto_referencia_'+key).val();
		    	var precio=$('#producto_precio_'+key).val();
		    	
		    	
		    	
		    	var cadena=producto+'|'+bodega+'|'+cantidad+'|'+precio+'|'+referencia;
		    	
		    	parent.$('#producto_receptor').val(cadena);
		    	parent.$('#producto_receptor').change();
		    	top.cerrarCargando();
		    	
		    	parent.$('#modal_lista_productos').modal('hide');
	    	}

	    	
	    });
	    
	    
	    
	    
	    productos_disponibles_funcionario_compra();
		
	});
</script>	