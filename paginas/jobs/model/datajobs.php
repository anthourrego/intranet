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
    $fk_tparchivos =@$_REQUEST['fk_tparchivos'];
    $nombreCat =strtolower(cadena_db_insertar(trim(@$_REQUEST['nameCategoria'])));
    $nombreCatPermiso =strtolower(cadena_db_insertar(trim(@$_REQUEST['nombreCatPermiso'])));
    $checkboxprivacidad =@$_REQUEST['checkboxprivacidad'];
    $checkboxaplicaPI = @$_REQUEST['checkboxaplicaPI'];
   
    if($padre != "" && $nombreCat != "" && $nombreCatPermiso != "" && $fk_tparchivos != "0"){
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
                    array(":nombre" => $nombreCat,":fecha_creacion" => date('Y-m-d H:i:s'), ":aplica_pi" => $aplicapi,":publico"=>$publico,":fk_categoria" => $padre, ":fk_creador" => $usuario['id'],":fk_permiso" => "jobs_cat_".$nombreCatPermiso)
            );

            $db->insertLogs("categorias", $getlastId, "Creacion de categoria " . $nombreCat, $usuario['id']);
            if(asignarTipoArchivos($fk_tparchivos,$getlastId)){
                $retorno['exito']=1;
                $msj=array("success" => true,
                        "msj" => "Categoria <b>".$nombreCat."</b> Creada");
            }else{
                $msj=array("success" => false,
                            "msj" => "Error asignando extensionesa a la categoria <b>".$nombreCat."</b>");
            }

           

            
        }else{
            $msj = array("success" => false,
                         "msj" => "La categoria <b>".$nombreCat."</b> ya existe");
        }
        
    }
    $retorno['alert']=$msj;
    $db->desconectar();
    return json_encode($retorno);
}

function actualizarTipoArchivos($cat,$nuevotipo){
    global $usuario;
    $db = new Bd();
    $db -> conectar();

    $retorno=0;
    // buscamos el tipo de documento asignado para saber si es diferente al que envian en la actualizacion
    $buscartipoArchivo =$db->consulta("SELECT b.nombre,a.id FROM tipo_archivo_categoria a LEFT JOIN tipo_archivo b ON a.fk_tarchivo = b.id WHERE a.estado = 1  AND a.fk_categoria =".$cat."");

    if($buscartipoArchivo['cantidad_registros']){
        //validamos si son diferentes
        if($buscartipoArchivo[0]['nombre'] != $nuevotipo){
            //cambiamos a estado 0 el tipo actualmente asignado
            $estadoex=$db->sentencia("
            UPDATE
                tipo_archivo_categoria
            SET
                estado=0
            WHERE
                fk_categoria = ".$cat."
            ");

            for ($i=0; $i < $buscartipoArchivo['cantidad_registros'] ; $i++) { 
                $db->insertLogs("tipo_archivo_categoria", $buscartipoArchivo[$i]['id'], "secambio de estado a 0 el tipo de archivo para la categoria ".$cat, $usuario['id']);
            }    
            if(asignarTipoArchivos($nuevotipo,$cat)){
                $retorno=1;
            }
            
        }
    }
    
    $db ->desconectar();
    return json_encode($retorno);
}

function asignarTipoArchivos($nombretipo,$idcategoria){
    global $usuario;
    $db = new Bd();
    $db -> conectar();

    $retorno=0;

    //OBTENEMOS LAS EXTENSIONES HABILITADAS POR NOMBRE
    $AsignarTipoArchivos = $db -> consulta("
    SELECT
        id,
        nombre,
        extensiones
    FROM
        tipo_archivo
    WHERE
        estado =1 
        AND nombre = '".$nombretipo."'
    ");

    if($AsignarTipoArchivos['cantidad_registros']){

        for ($i=0; $i < $AsignarTipoArchivos['cantidad_registros'] ; $i++) { 
            
            
            $extensiones =$db->sentencia("
            INSERT INTO
                tipo_archivo_categoria
                    (fk_categoria,
                    fk_tarchivo,
                    estado,
                    fecha_creacion,
                    fk_creador)
                VALUES
                    (:fk_categoria,
                    :fk_tarchivo,
                    1,
                    :fecha_creacion,
                    :fk_creador)",
                array(
                    ":fk_categoria" => $idcategoria,
                    ":fk_tarchivo" => $AsignarTipoArchivos[$i]['id'],
                    ":fecha_creacion" => date('Y-m-d H:i:s'),
                    ":fk_creador" => $usuario['id']
                )
            );
            
            $db->insertLogs("tipo_archivo_categoria", $extensiones, "se asigna extencion ".$AsignarTipoArchivos[$i]['extensiones']." a la categoria " . $idcategoria, $usuario['id']);

        }
        $retorno=1;
    }

    $db ->desconectar();
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

    $retorno=array();
    $retorno['exito']=0;
    

    $db->sentencia("UPDATE categorias SET activo = 0 WHERE id = :id", array(":id" => $id));

    $db->insertLogs("categorias", $id, "Elimina la categoria " . $nombre, $usuario['id']);

    $sql = $db->consulta("SELECT * FROM categorias WHERE fk_categoria = :fk_categoria AND activo = 1", array(":fk_categoria" => $id));

    $obtenerfk_permiso = $db->consulta("SELECT fk_permiso FROM categorias WHERE fk_categoria = :fk_categoria", array(":fk_categoria" =>$id));
    if($obtenerfk_permiso['cantidad_registros']){
        $retorno['fk_permiso'][]=$obtenerfk_permiso[0]['fk_permiso'];
    }

    for ($i=0; $i < $sql["cantidad_registros"]; $i++) { 
      
      eliminarCategoria($sql[$i]['id'], $sql[$i]['nombre']);

    }

    $retorno['exito']=1;

    $db->desconectar();
    
    return json_encode($retorno);
  }

function editarCategoria(){
    $resp = array();
    global $usuario;
    $db = new Bd();
    $db->conectar();

    $nombreCat = strtolower(cadena_db_insertar(trim(@$_REQUEST["nombre"])));

    $checkboxprivacidadedit =@$_REQUEST['checkboxprivacidadedit'];
    $checkboxaplicaPIedit = @$_REQUEST['checkboxaplicaPIedit'];

    $aplicapi=0;
    $publico=0;

    
    if($checkboxaplicaPIedit == "on"){
        $aplicapi=1;
    }

    if($checkboxprivacidadedit =="on"){
        $publico=1;
    }

    if (validarNameCategoria($nombreCat)== 0) {

       
    
        $db->sentencia("UPDATE categorias SET nombre = :nombre, fk_categoria = :fk_categoria, aplica_pi=:aplicaPI, publico=:publico WHERE id = :id", array(":id" => $_REQUEST["idCategoria"], ":nombre" => $nombreCat, ":fk_categoria" => $_REQUEST["catPadre"], ":aplicaPI" => $aplicapi, ":publico" => $publico));
  
        $db->insertLogs("categoria", $_REQUEST["idCategoria"], "Se ha actualizado la categoria al nombre " .$nombreCat . " y categoria padre " . $_REQUEST["catPadre"]."publico es ".$publico."aplica pi es".$aplicapi, $usuario['id']);

        actualizarTipoArchivos($_REQUEST["idCategoria"],$_REQUEST['fk_tparchivos']);

        $resp = array(
                  "success" => true,
                  "msj" => "Se ha actualizado correctamente"
                );
      

    } else { 
        //se realiza consulta para determinar si el nombre siendo igual a uno existente. solo quieren cambiar el padre
        $cambiarPadre =$db->consulta("SELECT * FROM categorias WHERE id=:id",array(":id" => @$_REQUEST['idCategoria']));
        if($cambiarPadre['cantidad_registros']){
            if($cambiarPadre[0]['fk_categoria'] != $_REQUEST['catPadre'] || $cambiarPadre[0]['aplica_pi'] != $aplicapi || $cambiarPadre[0]['publico'] != $publico){

                $db->sentencia("UPDATE categorias SET fk_categoria = :fk_categoria, aplica_pi=:aplicaPI, publico=:publico WHERE id = :id", array(":id" => $_REQUEST["idCategoria"], ":fk_categoria" => $_REQUEST["catPadre"],":aplicaPI" => $aplicapi, ":publico" => $publico));
  
                $db->insertLogs("categoria", $_REQUEST["idCategoria"], "Se ha actualizado el padre a la categoria " . $nombreCat. " y la nueva categoria padre es" . $_REQUEST["catPadre"]."aplica pi es".$aplicapi."publico es".$publico, $usuario['id']);

                $resp = array(
                    "success" => true,
                    "msj" => "Se ha actualizado correctamente"
                  );

            
            }else if($_REQUEST['fk_tparchivos']){
                if(actualizarTipoArchivos($_REQUEST["idCategoria"],$_REQUEST['fk_tparchivos'])){
                    
                $resp = array(
                    "success" => true,
                    "msj" => "Se ha actualizado correctamente"
                  );
                }else{
                    $resp = array(
                        "success" => false,
                        "msj" => "Realize algun cambio antes de guardar"
                      );
                }
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

      $tipo_archivocat= $db->consulta("SELECT b.nombre FROM tipo_archivo_categoria a LEFT JOIN tipo_archivo b ON a.fk_tarchivo = b.id WHERE a.estado = 1  AND fk_categoria =".$categorias[$i]["id"]."");

      if ($hijos["cantidad_registros"] > 0) {
        $arbol[] = array(
                  "publico" => $categorias[$i]['publico'],
                  "aplicaPI" => $categorias[$i]['aplica_pi'],
                  "tipoDoc" => $tipo_archivocat[0]['nombre'],
                  "idCategoria" => $categorias[$i]["id"],
                  "fechaCreacion" => $categorias[$i]["fecha_creacion"], 
                  "fk_categoria" => $categorias[$i]["fk_categoria"],
                  "text" => ucwords(cadena_db_obtener($categorias[$i]["nombre"])),
                  "tags" => [$hijos['cantidad_registros']],
                  "nodes" => arbolCategorias($categorias[$i]["id"])
                );
      }else {
        $arbol[] = array(
                  "publico" => $categorias[$i]['publico'],
                  "aplicaPI" => $categorias[$i]['aplica_pi'],
                  "tipoDoc" => $tipo_archivocat[0]['nombre'],
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



    $retorno =array();


    $retorno['exito']=0;


    $sql_tipoarchivos= $db->consulta("
    SELECT
        nombre
    FROM
        tipo_archivo
    WHERE
        estado = 1
    GROUP BY
        nombre
    
    ");

    if($sql_tipoarchivos['cantidad_registros']){
        $retorno['exito']=1;
        for ($i=0; $i < $sql_tipoarchivos['cantidad_registros'] ; $i++) { 
            $retorno['tipo_archivos'][]=$sql_tipoarchivos[$i]['nombre'];
        }
    }

    $db -> desconectar();

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