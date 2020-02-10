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
	
	$titulo_pantalla='Seguimiento de compra';

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

	<style>
	
		.tracking-detail {
		 padding:3rem 0
		}
		#tracking {
		 margin-bottom:1rem
		}
		[class*=tracking-status-] p {
		 margin:0;
		 font-size:1.1rem;
		 color:#fff;
		 text-transform:uppercase;
		 text-align:center
		}
		[class*=tracking-status-] {
		 padding:1.6rem 0
		}
		.tracking-status-intransit {
		 background-color:#65aee0
		}
		.tracking-status-outfordelivery {
		 background-color:#f5a551
		}
		.tracking-status-deliveryoffice {
		 background-color:#f7dc6f
		}
		.tracking-status-delivered {
		 background-color:#4cbb87
		}
		.tracking-status-attemptfail {
		 background-color:#b789c7
		}
		.tracking-status-error,.tracking-status-exception {
		 background-color:#d26759
		}
		.tracking-status-expired {
		 background-color:#616e7d
		}
		.tracking-status-pending {
		 background-color:#ccc
		}
		.tracking-status-inforeceived {
		 background-color:#214977
		}
		.tracking-list {
		 border:1px solid #e5e5e5
		}
		.tracking-item {
		 border-left:1px solid #e5e5e5;
		 position:relative;
		 padding:2rem 1.5rem .5rem 2.5rem;
		 font-size:.9rem;
		 margin-left:3rem;
		 min-height:5rem
		}
		.tracking-item:last-child {
		 padding-bottom:4rem
		}
		.tracking-item .tracking-date {
		 margin-bottom:.5rem
		}
		.tracking-item .tracking-date span {
		 color:#888;
		 font-size:85%;
		 padding-left:.4rem
		}
		.tracking-item .tracking-content {
		 padding:.5rem .8rem;
		 background-color:#f4f4f4;
		 border-radius:.5rem
		}
		.tracking-item .tracking-content span {
		 display:block;
		 color:#888;
		 font-size:85%
		}
		.tracking-item .tracking-icon {
		 line-height:2.6rem;
		 position:absolute;
		 left:-1.3rem;
		 width:2.6rem;
		 height:2.6rem;
		 text-align:center;
		 border-radius:50%;
		 font-size:1.1rem;
		 background-color:#fff;
		 color:#fff
		}
		.tracking-item .tracking-icon.status-sponsored {
		 background-color:#f68
		}
		.tracking-item .tracking-icon.status-delivered {
		 background-color:#4cbb87
		}
		.tracking-item .tracking-icon.status-outfordelivery {
		 background-color:#f5a551
		}
		.tracking-item .tracking-icon.status-deliveryoffice {
		 background-color:#f7dc6f
		}
		.tracking-item .tracking-icon.status-attemptfail {
		 background-color:#b789c7
		}
		.tracking-item .tracking-icon.status-exception {
		 background-color:#d26759
		}
		.tracking-item .tracking-icon.status-inforeceived {
		 background-color:#214977
		}
		.tracking-item .tracking-icon.status-intransit {
		 color:#e5e5e5;
		 border:1px solid #e5e5e5;
		 font-size:.6rem
		}
		@media(min-width:992px) {
		 .tracking-item {
		  margin-left:10rem
		 }
		 .tracking-item .tracking-date {
		  position:absolute;
		  left:-10rem;
		  width:7.5rem;
		  text-align:right
		 }
		 .tracking-item .tracking-date span {
		  display:block
		 }
		 .tracking-item .tracking-content {
		  padding:0;
		  background-color:transparent
		 }
		}	
		
	</style>

</head>
<body>
	<h3><?php echo($titulo_pantalla); ?></h3>
	<div class="modal-body contenedor_funcionario_compra_timeline">
	</div>
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
				ejecutar_accion:'html_funcionario_compra_timeline',
				funco_id:<?php echo($funco_id); ?>
			},
			success:function(datos){
				$('.contenedor_funcionario_compra_timeline').html(datos.html);
				top.cerrarCargando();												
			}	
		});							
	});
</script>