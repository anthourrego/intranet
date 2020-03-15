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

  $usuario = $session->get("usuario");
  $ruta_documentos = array();

  $lib = new Libreria;
?>

<!DOCTYPE html>
<html>
<head>
  <?php 
    echo $lib->metaTagsRequired();
  ?>
  <title>Jobs</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
  ?>
</head>
<body class="container">
  <div class="card mt-5">
    <a class="card-header card-modulos d-flex justify-content-between btn btn-light" href="">
      <h5 class="mb-0 my-auto">Marcas</h5>
    </a>
    <a class="card-header card-modulos d-flex justify-content-between btn btn-light" href="">
      <h5 class="mb-0 my-auto">Categorias</h5>
    </a>
    <a class="card-header card-modulos d-flex justify-content-between btn btn-light" href="">
      <h5 class="mb-0 my-auto">Tecnologia</h5>
    </a>
  </div>
</body>
<?php 
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    cerrarCargando();
  });
</script>
</html>