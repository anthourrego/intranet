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
include_once($ruta_raiz.'clases/define.php');
include_once($ruta_raiz.'clases/funciones_generales.php');
include_once($ruta_raiz.'clases/Conectar.php');
include_once($ruta_raiz .'clases/sessionActiva.php');
$usuario = $session->get("usuario");

function getExtPorTipoDocumento(){

    $db = new Bd();
    $db->conectar();
    
    $retorno['exito']=0;

    $retorno=array();

    $sql_extensiones = $db->consulta("
    SELECT 
        nombre,
        extensiones 
    FROM 
        tipo_archivo 
    WHERE 
        estado = 1 
    ORDER BY 
        nombre
        ");
    $catnombre= array();
    $extensiones=array();
    if($sql_extensiones['cantidad_registros']){
        for($a=0; $a < $sql_extensiones['cantidad_registros']; $a++){
            if(!in_array($sql_extensiones[$a]['nombre'],$catnombre)){
                $catnombre[$sql_extensiones[$a]['nombre']] = $sql_extensiones[$a]['nombre'];
            }

            if($sql_extensiones[$a]['nombre'] == $catnombre[$sql_extensiones[$a]['nombre']] ){
                $retorno['extensiones'][$sql_extensiones[$a]['nombre']][] = $sql_extensiones[$a]['extensiones'];
            }

    
        }
        $retorno['exito']=1;
    }

    
   

    return json_encode($retorno);

    
}


if(@$_REQUEST['accion']){
	if(function_exists($_REQUEST['accion'])){
		echo($_REQUEST['accion']());
	}
}


?>