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

function changeStateExt(){
    $db = new Bd();
    $db -> conectar();
    $retorno = array();

    $seleccionados = @$_REQUEST['seleccionados'];
    $noseleccionados = @$_REQUEST['noseleccionados'];

    if($seleccionados != "" || $noseleccionados != ""){
        $seleccionados=trim($seleccionados,',');
        $vseleccionados = explode(',',$seleccionados);
        $noseleccionados=trim($noseleccionados,',');
        $vnoseleccionados = explode(',',$noseleccionados);
	   
        $sql_changestateext= $db -> consulta("
        SELECT 
            id,
            estado
        FROM
            tipo_archivo
        ");
        $activos=array();
        $noativos=array();
        if($sql_changestateext['cantidad_registros']){

            for ($i=0; $i < $sql_changestateext['cantidad_registros']; $i++) { 
                if($sql_changestateext[$i]['estado']==1){
                    $activos[]=$sql_changestateext[$i]['id'];
                }else{
                    $noactivos[]=$sql_changestateext[$i]['id'];
                }  
            }

            $idsEstado1="";
            $idsEstado0="";

            for ($a=0; $a <count($vseleccionados) ; $a++) { 
                if(!in_array($vseleccionados[$a],$activos)){
                    $idsEstado1.=$vseleccionados[$a].",";
                }
            }

            for ($a=0; $a <count($vnoseleccionados) ; $a++) { 
                if(!in_array($vnoseleccionados[$a],$noactivos)){
                    $idsEstado0.=$vnoseleccionados[$a].",";
                }
            }
            
        }



    }

    


}

function getExtPorTipoDocumento(){

    $db = new Bd();
    $db->conectar();
    
    

    $retorno=array();
    $retorno['exito']=0;

    $sql_extensiones = $db->consulta("
    SELECT 
        nombre,
        extensiones,
        estado,
        id
    FROM 
        tipo_archivo 
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
                $extensiones['ext'] =$sql_extensiones[$a]['extensiones'];
                $extensiones['estado']=$sql_extensiones[$a]['estado'];
                $extensiones['id']=$sql_extensiones[$a]['id'];
                $retorno['extensiones'][$sql_extensiones[$a]['nombre']][] = $extensiones;
                //$retorno['extensiones'] = array($sql_extensiones[$a]['nombre'] => $sql_extensiones[$a]['extensiones']);
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