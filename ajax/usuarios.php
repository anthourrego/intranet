<?php
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

  header("Access-Control-Allow-Origin: *");
  require_once($ruta_raiz . "clases/Session.php");

  function sessionActiva(){
    $session = new Session();
    if ($session->exist('usuario') == true) {
      return 1;
    }else{
      return 0;
    }
  }

  /*function listaUsuarioCreser(){
    $db = new Bd();
    $db->conectar();

    $lista_usuario = "";

    $cargo_usuario = $db->consulta("SELECT rol_id FROM ceg_rol WHERE rol_estado=1 AND fun_fk = :fun_fk", array(":fun_fk" => $_POST['id']));

    $sql_lista_usuarios = $db->consulta("SELECT * FROM vrol WHERE rol_estado=1 AND rol_fk_padre = :rol_fk_padre", array(":rol_fk_padre" => $cargo_usuario[0]['rol_id']));

    if ($sql_lista_usuarios['cantidad_registros'] != 0) {
      for ($i=0; $i < $sql_lista_usuarios['cantidad_registros']; $i++) { 

        $sql_competencia = $db->consulta("SELECT * FROM ceg_funcionario_atr WHERE fun_atr_nombre = 'creser_competencia' AND fun_fk = :fun_fk", array(":fun_fk" => $sql_lista_usuarios[$i]['fun_id']));
        if ($sql_competencia['cantidad_registros'] != 0) {
          $lista_usuario .= "<tr onclick='encuesta(" . $sql_lista_usuarios[$i]['fun_id'] . ", " . $sql_competencia[0]['fun_atr_valor'] . ")'>
                                <td class='text-center'>" . $sql_lista_usuarios[$i]['fun_nombre'] . " " . $sql_lista_usuarios[$i]['fun_nombre2'] . " " . $sql_lista_usuarios[$i]['fun_apellido'] . " " . $sql_lista_usuarios[$i]['fun_apellido2'] . "</td>
                              </tr>";
        }else{
          $lista_usuario .= "<tr class='alert-danger'>
                                <td class='text-center'>" . $sql_lista_usuarios[$i]['fun_nombre'] . " " . $sql_lista_usuarios[$i]['fun_nombre2'] . " " . $sql_lista_usuarios[$i]['fun_apellido'] . " " . $sql_lista_usuarios[$i]['fun_apellido2'] . "</td>
                              </tr>";
        }
      }
    }else{
      $lista_usuario = "<tr>
                          <td class='text-center'>No tienes personas a cargo</td>
                        </tr>";
    }

    $db->desconectar();

    return $lista_usuario;
    /*$sql = "SELECT usuarios.id AS id, 
                    usuarios.nombre AS nombre, 
                    usuarios.nombre2 AS nombre2, 
                    usuarios.apellido AS apellido, 
                    usuarios.apellido2 AS apellido2, 
                    usuarios.competencias AS competencias 
                    FROM usuarios INNER JOIN creser_nivel ON cn_id = usuarios.nivel WHERE usuarios.jefe_inmediato = :id";

    $result = $conexion->prepare($sql);
    $result->execute(array(":id" => $_POST['id']));
    $usuarios = "";
    if ($result->rowCount() > 0) {
      while($row = $result->fetch(PDO::FETCH_ASSOC)){
        $usuarios .= "<tr onclick='encuesta(" . $row['id'] . ", " . $row['competencias'] . ")'>
                        <td class='text-center'>" . $row['nombre'] . " " . $row['nombre2'] . " " . $row['apellido'] . " " . $row['apellido2'] . "</td>
                      </tr>";
      }
      echo $usuarios;
    }else{
      echo $usuarios = '<h3>No se han econtrado resultado</h3>';
    }
    $result->closeCursor();
  }*/

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>
