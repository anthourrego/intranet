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
  
  
  //habilita boton solicitus funcionario segun cargo o dep
  $ver_btn_solicitudes_funcionario=0;
  
  $car_nombre=$usuario['car_nombre'];
  $dep_nombre=$usuario['dep_nombre'];
  
  $cargos_involucrados=array(
  	'coordinador_de_nomina_y_seguridad_social',  //german
  	'analista_comercial' //juliana
  );
  $dependencias_involucradas=array(
  	'inventario'
  );
  
  if( in_array($car_nombre, $cargos_involucrados) || in_array($dep_nombre, $dependencias_involucradas)){
  	$ver_btn_solicitudes_funcionario=1;
  }
  
  
?>
<!DOCTYPE html>
<html>
<head>
  <title>Compra Interna Funcionario</title>
  
  <?php  
  	echo $lib->metaTagsRequired();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
	echo $lib->cambioPantalla();
	echo $lib->jqueryValidate();
  ?>
</head>
<body>
	<div class="container contenedor_general">
		<div class="row titulo_general_row">
			<div class="col-12 titulo_general ">
				<h1>Compra Interna Funcionario</h1>	
			</div>
		</div>
		<br>
		<br>
		<div class="row">
			<div class="col-6">
				<a href="solicitud.php" style="text-decoration:none;">
				<div class="card text-white bg-primary text-center" style="max-width: 18rem;">
				  <div class="card-body">
				    <h5 class="card-title">Solicitud de Compra</h5>
				    <p class="card-text">
				    	<i class="fas fa-shopping-cart" style="font-size:100px;"></i>
				    </p>
				  </div>
				</div>
				</a>
				
			</div>			
			<div class="col-6">
				<a href="solicitud_reporte.php"  style="text-decoration:none;">
				<div class="card text-white bg-primary text-center" style="max-width: 18rem;">
				  <div class="card-body">
				    <h5 class="card-title">Mis Solicitudes</h5>
				    <p class="card-text">
				    	<i class="fas fa-list-ul" style="font-size:100px;"></i>
				    </p>
				  </div>
				</div>
				</a>
			</div>			
		</div>
		<br>
		<br>		
		<div class="row">
			<div class="col-6">
				<?php if($ver_btn_solicitudes_funcionario){ ?>
				<a href="solicitud_reporte_revision.php" style="text-decoration:none;">
				<div class="card text-white bg-primary text-center" style="max-width: 18rem;">
				  <div class="card-body">
				    <h5 class="card-title">Solicitud de Funcionarios</h5>
				    <p class="card-text">
				    	<i class="fas fa-tasks" style="font-size:100px;"></i>
				    </p>
				  </div>
				</div>
				</a>
				<?php } ?>
			</div>			
			<div class="col-6">

			</div>			
		</div>		
	</div>
</body>
</html>	
		