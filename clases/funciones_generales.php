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
    define("RUTA_DROPBOX","http://192.168.1.198/"); 
  /*}else if($ip == "::1"){
    define("RUTA_BASE","http://192.168.1.141/" . RUTA_SERVER ); 
    define("RUTA_DROPBOX","http://192.168.1.141/"); */
  }else{
    define("RUTA_BASE","http://201.236.254.67:141/" . RUTA_SERVER ); 
    define("RUTA_DROPBOX","http://201.236.254.67:6060/"); 
  }

  //Se define el resto de las rutas
  define("RUTA_ALMACENAMIENTO", RUTA_BASE . "almacenamiento/"); 
  define("RUTA_CONSULTAS", RUTA_BASE . "pantallas/intranet/"); 

  function textoblanco($texto){
    $conv= array(" " => "");
    //Guardamos el resultado en una variable
    $textblanco = strtr($texto, $conv);
    /* Cuenta cuantos caracteres tiene el texto */
    $cont = strlen($textblanco);
    /* Retornamos la cantidad */
    return $cont;
  }

  function iconos($tipo){
    $icono = "";
    if ($tipo == "application/pdf") {
      $icono = "fas fa-file-pdf";
    }elseif ($tipo == "application/msword") {
      $icono = "fas fa-file-word";
    }elseif ($tipo == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
      $icono = "fas fa-file-word";
    }elseif ($tipo == "application/vnd.ms-excel") {
      $icono = "fas fa-file-excel";
    }elseif ($tipo == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
      $icono = "fas fa-file-excel";
    }elseif ($tipo == "application/x-zip-compressed") {
      $icono = "fas fa-file-archive";
    }elseif ($tipo == "application/zip" || $tipo == "rar") {
      $icono = "fas fa-file-archive";
    }elseif ($tipo == "application/vnd.ms-powerpoint") {
      $icono = "fas fa-file-powerpoint";
    }elseif ($tipo == "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
      $icono = "fas fa-file-powerpoint";
    }else{
      $icono = "fas fa-file";
    }
    return $icono;
  }

?>