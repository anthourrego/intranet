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
  include_once($ruta_raiz . 'clases/sessionActiva.php');
  $usuario = $session->get("usuario");

  function arbolTecnologias($tec = 0){
    $arbol = array();
    $db = new Bd();
    $db->conectar();

    $tecnologias = $db->consulta("SELECT * FROM tecnologias WHERE fk_tecnologia = :fk_tecnologia AND activo = 1", array(":fk_tecnologia" => $tec));

    for ($i=0; $i < $tecnologias["cantidad_registros"]; $i++) { 
      
      $hijos = $db->consulta("SELECT * FROM tecnologias WHERE fk_tecnologia = :fk_tecnologia AND activo = 1", array(":fk_tecnologia" => $tecnologias[$i]["id"]));

      if ($hijos["cantidad_registros"] > 0) {
        $arbol[] = array(
                  "idTecnologia" => $tecnologias[$i]["id"],
                  "nivel" => $tecnologias[$i]["nivel"],
                  "fechaCreacion" => $tecnologias[$i]["fecha_creacion"], 
                  "fk_tecnologia" => $tecnologias[$i]["fk_tecnologia"],
                  "text" => $tecnologias[$i]["nombre"],
                  "tags" => [$hijos['cantidad_registros']],
                  "nodes" => arbolTecnologias($tecnologias[$i]["id"])
                );
      }else {
        $arbol[] = array(
                  "idTecnologia" => $tecnologias[$i]["id"],
                  "nivel" => $tecnologias[$i]["nivel"],
                  "text" => $tecnologias[$i]["nombre"],
                  "fechaCreacion" => $tecnologias[$i]["fecha_creacion"],
                  "fk_tecnologia" => $tecnologias[$i]["fk_tecnologia"]
                );
      }
    }

    $db->desconectar();

    if ($tec == 0) {
      return json_encode($arbol);
    } else {
      return $arbol;
    }
    
  }

  function listaTecnologia(){
    $resp = array();
    $db = new Bd();
    $db->conectar();

    $lista = $db->consulta("SELECT * FROM tecnologias WHERE nivel != 3 AND activo = 1");

    if ($lista["cantidad_registros"] > 0) {
      $resp = array(
        "success" => true,
        "msj" => $lista    
      );
    } else {
      $resp = array(
                "success" => false,
                "msj" => "No se encontraron registros"    
              );
    }
    

    $db->desconectar();

    return json_encode($resp);
  }

  function cantidadNiveles($tec = 1, $nivel=0){
    $cont = 0;
    $cont2 = 0;
    $arbol = array();
    $db = new Bd();
    $db->conectar();

    $tecnologias = $db->consulta("SELECT * FROM tecnologias WHERE fk_tecnologia = :fk_tecnologia AND activo = 1", array(":fk_tecnologia" => $tec));

    if ($tecnologias["cantidad_registros"] > 0) {
      for ($i=0; $i < $tecnologias["cantidad_registros"]; $i++) {         
        $cont = cantidadNiveles($tecnologias[$i]["id"], ($nivel+1));

        if($cont2 < $cont){
          $cont2 = $cont;
        }
      }

      if ($cont2 > $nivel) {
        $nivel = $cont2;
      }
    }else{ 
      $cont = 1;
    }

    $db->desconectar();

    return $nivel;

  }

  function datosTecnologia($idTec){
    $db = new Bd();
    $db->conectar();

    $datos = $db->consulta("SELECT * FROM tecnologias WHERE id = :id", array(":id" => $idTec));

    $db->desconectar();
    return $datos[0];
  }

  function crearTecnologia(){
    global $usuario;
    $resp = array();
    $db = new Bd();
    $db->conectar();

    $nombre = trim($_REQUEST["nombre"]);

    if (validarNombreTecnologia($nombre, $_REQUEST["fk_tecnologia"]) == 0) {
      $nivel = 1;
      if ($_REQUEST["fk_tecnologia"] > 0) {
        $datosTec = datosTecnologia($_REQUEST["fk_tecnologia"]);
        $nivel = $datosTec["nivel"] + 1; 
      }

      $ultimoId = $db->sentencia("INSERT INTO tecnologias (nombre, fecha_creacion, activo, fk_tecnologia, nivel, fk_creador) VALUES (:nombre, :fecha_creacion, :activo, :fk_tecnologia, :nivel, :fk_creador)", array(":nombre" => $nombre, ":fecha_creacion" => date("Y-m-d H:i:s"), ":activo" => 1, ":fk_tecnologia" => $_REQUEST["fk_tecnologia"], ":fk_creador" => $usuario["id"], ":nivel" => $nivel));

      $db->insertLogs("tecnologias", $ultimoId, "Creacion de tecnólogia " . $nombre, $usuario['id']);

      $resp = array(
                "success" => true,
                "msj" => "La tecnólogia <b>" . $nombre . "</b> se creado."
              );
    } else {
      $resp = array(
                "success" => false,
                "msj" => "La tecnólogia <b>" . $nombre . "</b> ya se encuentra creada."
              );
    }
    
    $db->desconectar();

    return json_encode($resp);
  }

  function validarNombreTecnologia($nombre, $fk_tecnologia, $id = 0){
    $db = new Bd();
    $db->conectar();
    $validarNombre = array(); 
    
    if ($id == 0) {
      $validarNombre = $db->consulta("SELECT * FROM tecnologias WHERE nombre = :nombre AND fk_tecnologia = :fk_tecnologia AND activo = 1", array(":nombre" => $nombre, ":fk_tecnologia" => $fk_tecnologia));
    } else {
      $validarNombre = $db->consulta("SELECT * FROM tecnologias WHERE id != :id AND nombre = :nombre AND fk_tecnologia = :fk_tecnologia AND activo = 1", array(":id" => $id, ":nombre" => $nombre, ":fk_tecnologia" => $fk_tecnologia));
    }

    $db->desconectar();

    return json_encode($validarNombre["cantidad_registros"]);
  }

  function eliminarTecnologia(){
    global $usuario;
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE tecnologias SET activo = 0 WHERE id = :id", array(":id" => $_REQUEST["idTec"]));

    $db->insertLogs("tecnologias", $_REQUEST["idTec"], "Elimina la tecnólogia " . $_REQUEST["nombre"], $usuario['id']);


    $db->desconectar();
    
    return json_encode(1);
  }

  function editarTecnologia(){
    $resp = array();
    global $usuario;
    $db = new Bd();
    $db->conectar();
    $nombreTec = trim($_REQUEST["nombre"]);

    if (validarNombreTecnologia($nombreTec, $_REQUEST["tecPadre"], $_REQUEST["idTecnologia"]) == 0) {
      $nivel = 1;
      if ($_REQUEST["tecPadre"] > 0) {
        $datosTec = datosTecnologia($_REQUEST["tecPadre"]);
        $nivel = $datosTec["nivel"] + 1; 
      }

      $niveles = cantidadNiveles($_REQUEST["idTecnologia"]);

      if(($niveles + $nivel) <= 3){
        $db->sentencia("UPDATE tecnologias SET nombre = :nombre, fk_tecnologia = :fk_tecnologia, nivel = :nivel WHERE id = :id", array(":id" => $_REQUEST["idTecnologia"], ":nombre" => $nombreTec, ":fk_tecnologia" => $_REQUEST["tecPadre"], ":nivel" => $nivel));
  
        $db->insertLogs("tecnologias", $_REQUEST["idTecnologia"], "Se ha actualizado la tecnólogia nombre " . $_REQUEST["nombre"] . " y tecnólogia padre " . $_REQUEST["tecPadre"], $usuario['id']);

        if (@$_REQUEST["compatibilidad"]) {
          $compatible = $_REQUEST["idTecnologia"];

          for ($i=0; $i < count($_REQUEST["compatibilidad"]); $i++) { 
            $compatible .= ', ' . $_REQUEST["compatibilidad"][$i];
            
            $check = $db->consulta("SELECT * FROM tecnologia_no_compatible WHERE fk_tecnologia = :fk_tecnologia AND fk_tecnologia_compatible = :fk_tecnologia_compatible", array(":fk_tecnologia" => $_REQUEST["idTecnologia"], ":fk_tecnologia_compatible" => $_REQUEST["compatibilidad"][$i]));
    
            $check1 = $db->consulta("SELECT * FROM tecnologia_no_compatible WHERE fk_tecnologia = :fk_tecnologia AND fk_tecnologia_compatible = :fk_tecnologia_compatible", array(":fk_tecnologia" => $_REQUEST["compatibilidad"][$i], ":fk_tecnologia_compatible" => $_REQUEST["idTecnologia"]));
            
            if ($check['cantidad_registros'] == 1) {
              $db->sentencia("UPDATE tecnologia_no_compatible SET estado = 0 WHERE id = :id", array(":id" => $check[0]['id']));
            }elseif($check1['cantidad_registros'] == 1){
              $db->sentencia("UPDATE tecnologia_no_compatible SET estado = 0 WHERE id = :id", array(":id" => $check1[0]['id']));
            }
          }


          $todasTecnologia = $db->consulta("SELECT * FROM tecnologias WHERE activo = 1 AND fk_tecnologia = :fk_tecnologia AND id NOT IN (" . $compatible . ")", array(":fk_tecnologia" => $_REQUEST["tecPadre"]));

          /* for ($i=0; $i < $todasTecnologia['cantidad_registros']; $i++) { 
            $db->sentencia("INSERT INTO tecnologia_no_compatible (fk_tecnologia, fk_tecnologia_compatible, fecha_creacion, estado, fk_creador) VALUES (:fk_tecnologia, :fk_tecnologia_compatible, :fecha_creacion, :estado, :fk_creador)", array(":fk_tecnologia" => $_REQUEST["idTecnologia"], ":fk_tecnologia_compatible" => $todasTecnologia[$i]['id'], ":fecha_creacion" => date("Y-m-d H:i:s"), ":estado" => 1, ":fk_creador" => $usuario['id']));
          } */

          /* 
            //Parte de no compatible
            foreach ($_REQUEST["compatibilidad"] as $comp) {
            $db->sentencia("INSERT INTO tecnologia_no_compatible (fk_tecnologia, fk_tecnologia_compatible, fecha_creacion, estado, fk_creador) VALUES (:fk_tecnologia, :fk_tecnologia_compatible, :fecha_creacion, :estado, :fk_creador)", array(":fk_tecnologia" => $_REQUEST["idTecnologia"], ":fk_tecnologia_compatible" => $comp, ":fecha_creacion" => date("Y-m-d H:i:s"), ":estado" => 1, ":fk_creador" => $usuario['id']));
          } */
        }else{
          $todasTecnologia = $db->consulta("SELECT * FROM tecnologias WHERE activo = 1 AND fk_tecnologia = :fk_tecnologia AND id != :id", array(":fk_tecnologia" => $_REQUEST["tecPadre"], ":id" => $_REQUEST["idTecnologia"]));
        }

        if ($todasTecnologia['cantidad_registros'] > 0) {
          for ($i=0; $i < $todasTecnologia['cantidad_registros']; $i++) { 
            $check = $db->consulta("SELECT * FROM tecnologia_no_compatible WHERE fk_tecnologia = :fk_tecnologia AND fk_tecnologia_compatible = :fk_tecnologia_compatible", array(":fk_tecnologia" => $_REQUEST["idTecnologia"], ":fk_tecnologia_compatible" => $todasTecnologia[$i]['id']));
  
            $check1 = $db->consulta("SELECT * FROM tecnologia_no_compatible WHERE fk_tecnologia = :fk_tecnologia AND fk_tecnologia_compatible = :fk_tecnologia_compatible", array(":fk_tecnologia" => $todasTecnologia[$i]['id'], ":fk_tecnologia_compatible" => $_REQUEST["idTecnologia"]));
            
            if ($check['cantidad_registros'] == 1) {
              $db->sentencia("UPDATE tecnologia_no_compatible SET estado = 1 WHERE id = :id", array(":id" => $check[0]['id']));
            }elseif($check1['cantidad_registros'] == 1){
              $db->sentencia("UPDATE tecnologia_no_compatible SET estado = 1 WHERE id = :id", array(":id" => $check1[0]['id']));
            }else{
              $db->sentencia("INSERT INTO tecnologia_no_compatible (fk_tecnologia, fk_tecnologia_compatible, fecha_creacion, estado, fk_creador) VALUES (:fk_tecnologia, :fk_tecnologia_compatible, :fecha_creacion, :estado, :fk_creador)", array(":fk_tecnologia" => $_REQUEST["idTecnologia"], ":fk_tecnologia_compatible" => $todasTecnologia[$i]['id'], ":fecha_creacion" => date("Y-m-d H:i:s"), ":estado" => 1, ":fk_creador" => $usuario['id']));
            }
          }
        }else{
          $db->sentencia("UPDATE tecnologia_no_compatible SET estado = 0 WHERE fk_tecnologia = :fk_tecnologia OR fk_tecnologia_compatible = :fk_tecnologia_compatible", array(":fk_tecnologia" => $_REQUEST["idTecnologia"], ":fk_tecnologia_compatible" => $_REQUEST["idTecnologia"]));
        }

        $resp = array(
                  "success" => true,
                  "msj" => "Se ha aculizado correctamente"
                );
      }else{
        $resp = array(
          "success" => false,
          "msj" => "Supera los sub niveles permitidos"
        );  
      }

    } else {
      $resp = array(
                "success" => false,
                "msj" => "El nombre <b>" . $nombreTec . "</b> ya se existe en esa técnologia padre."
              );
    }

    $db->desconectar();

    return json_encode($resp);

  }

  function tecnologiaCheckbox(){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT * FROM tecnologias WHERE activo = 1 AND nivel = 3 AND fk_tecnologia = :fk_tecnologia", array(":fk_tecnologia" => $_REQUEST['idTec']));
    
    for ($i=0; $i < $sql['cantidad_registros']; $i++) { 
      $check = $db->consulta("SELECT * FROM tecnologia_no_compatible WHERE fk_tecnologia = :fk_tecnologia AND fk_tecnologia_compatible = :fk_tecnologia_compatible AND estado = 1", array(":fk_tecnologia" => $_REQUEST['idTecActual'], ":fk_tecnologia_compatible" => $sql[$i]['id']));
      
      $check1 = $db->consulta("SELECT * FROM tecnologia_no_compatible WHERE fk_tecnologia = :fk_tecnologia AND fk_tecnologia_compatible = :fk_tecnologia_compatible AND estado = 1", array(":fk_tecnologia" => $sql[$i]['id'], ":fk_tecnologia_compatible" => $_REQUEST['idTecActual']));

      if (($check['cantidad_registros'] + $check1['cantidad_registros']) == 0) {
        $sql[$i]['check'] = 1;
      }else{
        $sql[$i]['check'] = 0;
      }
    }

    $db->desconectar();

    if ($sql['cantidad_registros'] > 0) {
      $resp = array(
        "success" => true,
        "msj" => $sql
      );
    }else{
      $resp = array(
                "success" => false,
                "msj" => "No se han encontrado datos."
              );
    }

    return json_encode($resp);
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>