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

function crearPermisoJobsyCategoria(){
    global $usuario;
    $db = new Bd();
    $db -> conectar();
    $retorno = array();

    $retorno['exito']=0;

    $padre=@$_REQUEST['fk_categoria'];
    $nombreCat =strtolower(cadena_db_insertar(trim(@$_REQUEST['nameCategoria'])));
    $nombreCatPermiso =strtolower(cadena_db_insertar(trim(@$_REQUEST['nombreCatPermiso'])));
    $checkboxprivacidad =@$_REQUEST['checkboxprivacidad'];
    $checkboxaplicaPI = @$_REQUEST['checkboxaplicaPI'];
    

    if($padre != "" && $nombreCat != "" && $nombreCatPermiso != ""){
        $aplicapi=0;
        $publico=0;

        if($checkboxaplicaPI == "on"){
            $aplicapi=1;
        }

        if($checkboxprivacidad =="on"){
            $publico=1;
        }

        if(validarNameCategoria($nombreCat)==0){
            $getlastId =$db -> sentencia("
            INSERT INTO
                categorias 
                    (nombre,
                    fecha_creacion,
                    aplica_pi,
                    activo,
                    publico,
                    fk_categoria,
                    fk_creador,
                    fk_permiso)
                VALUES
                    (:nombre,
                    :fecha_creacion,
                    :aplica_pi,
                    1,
                    :publico,
                    :fk_categoria,
                    :fk_creador,
                    :fk_permiso)",
                    array(":nombre" => $nombreCat,":fecha_creacion" => date('Y-m-d H:i:s'), ":aplica_pi" => $aplicapi,":publico"=>$publico,":fk_categoria" => $padre, ":fk_creador" => $usuario['id'],":fk_permiso" => "jobs_".$nombreCatPermiso)
            );

            $db->insertLogs("categorias", $getlastId, "Creacion de categoria " . $nombreCat, $usuario['id']);

            $retorno['exito']=1;
            $msj=array("success" => true,
                       "msj" => "Categoria <b>".$nombreCat."</b> Creada");
        }else{
            $msj = array("success" => false,
                         "msj" => "La categoria <b>".$nombreCat."</b> ya existe");
        }
        
    }
    $retorno['alert']=$msj;
    $db->desconectar();
    return json_encode($retorno);
}

function eliminarCategoria($id = 0, $nombre = ''){
    if ($id == 0) {
      $id = $_REQUEST["idCat"];
      $nombre =  $_REQUEST["nombre"];
    }
    
    
    global $usuario;
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE categorias SET activo = 0 WHERE id = :id", array(":id" => $id));

    $db->insertLogs("categorias", $id, "Elimina la categoria " . $nombre, $usuario['id']);

    $sql = $db->consulta("SELECT * FROM categorias WHERE fk_categoria = :fk_categoria AND activo = 1", array(":fk_categoria" => $id));

    for ($i=0; $i < $sql["cantidad_registros"]; $i++) { 
      
      eliminarCategoria($sql[$i]['id'], $sql[$i]['nombre']);

    }

    $db->desconectar();
    
    return json_encode(1);
  }

function editarCategoria(){
    $resp = array();
    global $usuario;
    $db = new Bd();
    $db->conectar();

    $nombreCat = strtolower(cadena_db_insertar(trim(@$_REQUEST["nombre"])));

    if (validarNameCategoria($nombreCat)== 0) {
    
        $db->sentencia("UPDATE categorias SET nombre = :nombre, fk_categoria = :fk_categoria WHERE id = :id", array(":id" => $_REQUEST["idCategoria"], ":nombre" => $nombreCat, ":fk_categoria" => $_REQUEST["catPadre"]));
  
        $db->insertLogs("categoria", $_REQUEST["idCategoria"], "Se ha actualizado la categoria nombre " .$nombreCat . " y categoria padre " . $_REQUEST["catPadre"], $usuario['id']);

    

        $resp = array(
                  "success" => true,
                  "msj" => "Se ha actualizado correctamente"
                );
      

    } else { 
        //se realiza consulta para determinar si el nombre siendo igual a uno existente. solo quieren cambiar el padre
        $cambiarPadre =$db->consulta("SELECT fk_categoria FROM categorias WHERE id=:id",array(":id" => @$_REQUEST['idCategoria']));
        if($cambiarPadre['cantidad_registros']){
            if($cambiarPadre[0]['fk_categoria'] != $_REQUEST['catPadre']){

                $db->sentencia("UPDATE categorias SET fk_categoria = :fk_categoria WHERE id = :id", array(":id" => $_REQUEST["idCategoria"], ":fk_categoria" => $_REQUEST["catPadre"]));
  
                $db->insertLogs("categoria", $_REQUEST["idCategoria"], "Se ha actualizado el padre a la categoria " . $nombreCat. " y la nueva categoria padre es" . $_REQUEST["catPadre"], $usuario['id']);

                $resp = array(
                    "success" => true,
                    "msj" => "Se ha actualizado correctamente"
                  );

            }else{
                $resp = array(
                    "success" => false,
                    "msj" => "El nombre <b>" . $nombreCat . "</b> ya  existe."
                  );
            }
        }
      
    }

    $db->desconectar();

    return json_encode($resp);

  }



function arbolCategorias($cat = 0){
    $arbol = array();
    $db = new Bd();
    $db->conectar();

    $categorias = $db->consulta("SELECT * FROM categorias WHERE fk_categoria = :fk_categoria AND activo = 1", array(":fk_categoria" => $cat));

    for ($i=0; $i < $categorias["cantidad_registros"]; $i++) { 
      
      $hijos = $db->consulta("SELECT * FROM categorias WHERE fk_categoria = :fk_categoria AND activo = 1", array(":fk_categoria" => $categorias[$i]["id"]));

      if ($hijos["cantidad_registros"] > 0) {
        $arbol[] = array(
                  "idCategoria" => $categorias[$i]["id"],
                  "fechaCreacion" => $categorias[$i]["fecha_creacion"], 
                  "fk_categoria" => $categorias[$i]["fk_categoria"],
                  "text" => ucwords(cadena_db_obtener($categorias[$i]["nombre"])),
                  "tags" => [$hijos['cantidad_registros']],
                  "nodes" => arbolCategorias($categorias[$i]["id"])
                );
      }else {
        $arbol[] = array(
                  "idCategoria" => $categorias[$i]["id"],
                  "text" => ucwords(cadena_db_obtener($categorias[$i]["nombre"])),
                  "fechaCreacion" => $categorias[$i]["fecha_creacion"],
                  "fk_categoria" => $categorias[$i]["fk_categoria"]
                );
      }
    }

    $db->desconectar();

    if ($cat == 0) {
      return json_encode($arbol);
    } else {
      return $arbol;
    }
    
}

function dataSelectTipoArchivo(){
    $db = new Bd();
    $db -> conectar();

    $db -> desconectar();

    $retorno =array();


    $retorno['exito']=0;


    $sql_tipoarchivos= $db->consulta("");

    return json_encode($retorno);
}

function dataSelectCategoria(){
    $db = new Bd();
    $db ->conectar();

    $retorno = array();
    $retorno['exito']=0;

    $sql_categorias = $db->consulta("
    SELECT
        id,
        nombre
    FROM
        categorias
    WHERE
        activo= 1
    ");
    
    if($sql_categorias['cantidad_registros']){
        $retorno['lista'] = array(
            "success" => true,
            "msj" => $sql_categorias    
        );

        $retorno['exito']=1;
    }else{
        $retorno['lista'] = array(
            "success" => false,
            "msj" => "No se encontraron Registros"    
        );
    }


    $db -> desconectar();
    return json_encode($retorno);
}

function validarNameCategoria($namecategoria){
    $db= new Bd();
    $db -> conectar();
    $sql_buscarSiExiste = $db-> consulta("
        SELECT
            nombre
        FROM
            categorias
        WHERE
            nombre = '".cadena_db_insertar($namecategoria)."'
            AND activo=1
        ");

    $db->desconectar();
    return json_encode($sql_buscarSiExiste['cantidad_registros']);
}

function changeStateExt(){
    global $usuario;
    $db = new Bd();
    $db -> conectar();
    $retorno = array();

    $retorno['exito'] = 0;

    $seleccionados = @$_REQUEST['seleccionados'];
    $noseleccionados = @$_REQUEST['noseleccionados'];

    
        
        
        
        if($seleccionados != ""){
            $seleccionados=trim($seleccionados,',');
            $vseleccionados = explode(",",$seleccionados);
            $update_ext_1= $db-> sentencia("
            UPDATE
                tipo_archivo
            SET
                estado = 1
            WHERE
                id IN (".$seleccionados.")
            ");

            
            for ($i=0; $i < count($vseleccionados); $i++) { 
                $db->insertLogs("tipo_archivo", $vseleccionados[$i], "Se cambio el estado a 1 de la ext con id" . $vseleccionados[$i], $usuario['id']);
            }
            $retorno['exito']=1;
            
        }
        
        if($noseleccionados != ""){
            $noseleccionados=trim($noseleccionados,',');
            $vnoseleccionados = explode(",",$noseleccionados);
            $update_ext_0 = $db -> sentencia("
            UPDATE
                tipo_archivo
            SET
                estado = 0
            WHERE
                id IN(".$noseleccionados.")
            ");

            for ($i=0; $i < count($vnoseleccionados); $i++) { 
                $db->insertLogs("tipo_archivo", $vnoseleccionados[$i], "Se cambio el estado a 0 de la ext con id " . $vnoseleccionados[$i], $usuario['id']);
            }

            $retorno['exito']=1;
        }

    $db->desconectar();
    return json_encode($retorno);
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

    
   
    $db->desconectar();
    return json_encode($retorno);

    
}


if(@$_REQUEST['accion']){
	if(function_exists($_REQUEST['accion'])){
		echo($_REQUEST['accion']());
	}
}


?>