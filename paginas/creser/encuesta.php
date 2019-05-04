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
  include_once($ruta_raiz . 'clases/funciones_generales.php');

  $session = new Session();

  $usuario = $session->get("usuario");

  $lib = new Libreria;

?>
<!DOCTYPE html>
<html>
<head>
	<title>Consumer Electronocs Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->datatables();
    echo $lib->intranet();
  ?>
</head>
<body>
  <object data="<?php echo(direccionIPRuta()); ?>/encuesta/creser_view.php?et_id=<?php echo($_GET['et_id']); ?>&filtro_atr=<?php echo($_GET['filtro_atr']); ?>&id_usuario=<?php echo($usuario['id']); ?>" class="w-100 vh-100"></object>
</body>
<?php  
  echo $lib->cambioPantalla();
?>
</html>