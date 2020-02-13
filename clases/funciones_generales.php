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

  //Se define la ruta de almacenamiento
  if (obtenerIp() == '201.236.254.67') {
    define("RUTA_ALMACENAMIENTO","http://192.168.1.141" . RUTA_SERVER . "/almacenamiento/"); 
  }else if(obtenerIp() == "::1"){
    define("RUTA_ALMACENAMIENTO","http://192.168.1.141" . RUTA_SERVER . "/almacenamiento/"); 
  }else{
    define("RUTA_ALMACENAMIENTO","http://201.236.254.67:141" . RUTA_SERVER . "/almacenamiento/"); 
  }

  function direccionIP(){
    //Se trae la ip de donde esta ingresando
    $ip = obtenerIp();
    if ($ip == '201.236.254.67') {
      return "http://192.168.1.198/";
    }else if($ip == "::1"){
      return "http://192.168.1.198/";
    }else{
      return "http://201.236.254.67:6060/";
    }
  }

  function direccionIPRuta(){
    //Se trae la ip de donde esta ingresando
    $ip = obtenerIp();
    if ($ip == '201.236.254.67') {
      return "http://192.168.1.141" . RUTA_SERVER . "/pantallas/intranet/";
    }else if($ip == "::1"){
      return "http://192.168.1.141" . RUTA_SERVER . "/pantallas/intranet/";
    }else{
      return "http://192.168.1.141" . RUTA_SERVER . "/pantallas/intranet/";
    }
  }

  function direccionIPRutaBase(){
    //Se trae la ip de donde esta ingresando
    $ip = obtenerIp();
    if ($ip == '201.236.254.67') {
      return "http://192.168.1.141" . RUTA_SERVER . "/";
    }else if($ip == "::1"){
      return "http://192.168.1.141" . RUTA_SERVER . "/";
    }else{
      return "http://192.168.1.141" . RUTA_SERVER . "/";
    }
  }

?>