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

  include_once($ruta_raiz . 'clases/librerias.php');
  include_once($ruta_raiz . 'clases/sessionActiva.php');
  include_once($ruta_raiz . 'clases/Conectar.php');

  $session = new Session();

  $usuario = $session->get("usuario");

  $lib = new Libreria;

  
?>
<!DOCTYPE html>
<html>
<head>
	<title>Consumer Electronocs Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->datatables();
    echo $lib->intranet();
  ?>
</head>
<body>
  <div class="container-fluid mt-3">
    <a class="btn btn-secondary link" href="<?php echo RUTA_RAIZ ?>paginas/gestion_humana"><i class="fas fa-arrow-left"></i> Atrás</a>
    <hr>
  </div>
	<div class="container mt-5">
    <table id="tabla" class="table table-bordered table-hover table-sm">
      <thead>
        <tr>
          <th class="text-center">Nombre</th>
        </tr>
      </thead>
      <tbody id="contenido">

      </tbody>
    </table>
  </div>
</body>
<?php  
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    cargarTabla();
  });

  function cargarTabla(){
    $.ajax({
      type: "POST",
      url: "<?php echo $ruta_raiz; ?>ajax/usuarios",
      data: {accion: "listaUsuarioCreser", id: <?php echo $usuario['id']; ?>},
      success: function(data){
        $("#contenido").empty();
        $("#contenido").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tabla");
      },
      error: function(){
        alert("No se ha podido traer la lista");
      }
    });
  }

  function encuesta(id, idCompetencia){
    var atributo;
    if (idCompetencia == "1") {
      idCompetencia = 2;
      atributo = 10;
    }else if(idCompetencia == "2"){
      idCompetencia = 3;
      atributo = 10;
    }
    //window.location.href = 'encuesta?et_id=' + idCompetencia + '&id_usu='+id;
    top.$("#cargando").modal("show");
    window.location.href = 'encuesta/creser_view?et_id=' + idCompetencia + '&filtro_atr=' + atributo + '|' + id;
  }
</script>
</html>