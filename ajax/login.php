<?php  
	/*Reanudamos la sesion*/
  @session_start();
  
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
	
	require_once($ruta_raiz . 'clases/Session.php');

	$session = new Session();
	$session->set('usuario', $_POST['array']);
	
  echo "Ok";
?>