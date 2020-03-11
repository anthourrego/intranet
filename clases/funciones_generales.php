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

  require_once($ruta_raiz . 'clases/define.php');

  //Miramos desde que ip publica estamos y haci mostramos los datos de la intranet
  function obtenerIp(){
    //Se valida cual ip por la cual esta ingresando
    if (isset($_SERVER["HTTP_CLIENT_IP"])){
      return $_SERVER["HTTP_CLIENT_IP"];
    }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
      return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
      return $_SERVER["HTTP_X_FORWARDED"];
    }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
      return $_SERVER["HTTP_FORWARDED_FOR"];
    }elseif (isset($_SERVER["HTTP_FORWARDED"])){
      return $_SERVER["HTTP_FORWARDED"];
    }else{
      return $_SERVER["REMOTE_ADDR"];
    }
  }

  //Parametrizamos la ruta de donde vamos a consultar
  $ip = obtenerIp();
  if ($ip == '201.236.254.67') {
    define("RUTA_BASE","http://192.168.1.141/" . RUTA_SERVER ); 
    define("RUTA_DROPBOX","http://192.168.1.198/" . RUTA_SERVER ); 
  }else if($ip == "::1"){
    define("RUTA_BASE","http://192.168.1.141/" . RUTA_SERVER ); 
    define("RUTA_DROPBOX","http://192.168.1.198/" . RUTA_SERVER ); 
  }else{
    define("RUTA_BASE","http://201.236.254.67:141/" . RUTA_SERVER ); 
    define("RUTA_DROPBOX","http://201.236.254.67:6060/" . RUTA_SERVER ); 
  }

  //Se define el resto de las rutas
  define("RUTA_ALMACENAMIENTO", RUTA_BASE . "almacenamiento/"); 
  define("RUTA_CONSULTAS", RUTA_BASE . "pantallas/intranet/"); 

?>