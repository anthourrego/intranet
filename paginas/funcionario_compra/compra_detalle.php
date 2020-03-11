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
	
	$titulo_pantalla='Detalle de Compra';

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
  ?>
</head>
<body>
	<h3><?php echo($titulo_pantalla); ?></h3>
	<div id="contenido_compra_detalle">
		
	</div>	
	
</body>
</html>	  


<script>
	$(document).ready(function(){
		
		$.ajax({
			type:'POST',
			dataType: 'json',
			url: "<?php echo(RUTA_CONSULTAS); ?>funcionario_compra/ejecutar_acciones.php",
			async:false,
			data:{
				ejecutar_accion:'funcionario_compra_detalle',
				funco_id:<?php echo($funco_id); ?>
			},
			success:function(retorno){
				$('#contenido_compra_detalle').html(retorno.html);
				top.cerrarCargando();
			}	
		});
			
	});
</script>