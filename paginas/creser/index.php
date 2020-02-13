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
	<div class="container mt-5">
    <div class="mb-4 d-flex justify-content-end" id="botones"></div>
    <table id="tabla" class="table table-bordered bg-light table-hover table-sm">
      <thead>
        <tr>
          <th class="text-center">Nombre</th>
          <th class="text-center">Cantidad</th>
        </tr>
      </thead>
      <tbody id="contenido"></tbody>
    </table>
  </div>
</body>
<?php  
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    cargarTabla();

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "registros_creser"},
      success: function(data){
        if (data.length != 0) {
          $("#botones").append('<a href="registros" class="btn btn-success mb-3"><i class="fas fa-list"></i> Reportes</a>');
        }
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });
  });

  function cargarTabla(){
    $.ajax({
      type: "POST",
      url: "<?php echo(direccionIPRuta()); ?>ajax/usuarios.php",
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
    if (idCompetencia == 2) {
      var atributo = 10;      
    } else {
      var atributo = 31;
    }
    //window.location.href = 'encuesta?et_id=' + idCompetencia + '&id_usu='+id;
    top.$("#cargando").modal("show");
    window.location.href = 'encuesta.php?et_id=' + idCompetencia + '&filtro_atr=' + atributo + '|' + id;
  }
</script>
</html>