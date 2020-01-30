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
    echo $lib->alertify();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container mt-5">
    <table id="tabla" class="table table-bordered bg-light table-hover table-sm">
      <thead>
        <tr>
          <th class="text-center">Nombre</th>
          <th class="text-center">Estado</th>
          <th class="text-center">Cantidad</th>
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
      url: "<?php echo(direccionIPRuta()); ?>ajax/usuarios.php",
      dataType: 'json',
      data: {accion: "PersonasAreas", idDep: <?php echo($_GET['idArea']); ?>},
      success: function(data){
        console.log(data);
        $("#contenido").empty();
        for (let i = 0; i < data.cantidad_registros; i++) {

          if(data[i].fun_estado == 0 && data[i].ea_fk != null){
            $("#contenido").append(`
              <tr onclick="encuesta(${data[i].fun_id}, ${data[i].fun_atr_valor})" class="alert-warning">
                <td>${data[i].fun_nombre_completo}</td>
                <td>Realizado retirado</td>
                <td>${data[i].cantidad}</td>
              </tr>
            `);
          }else if(data[i].fun_estado == 1 && data[i].ea_fk != null){
            $("#contenido").append(`
              <tr onclick="encuesta(${data[i].fun_id}, ${data[i].fun_atr_valor})" class="alert-success">
                <td>${data[i].fun_nombre_completo}</td>
                <td>Realizado</td>
                <td>${data[i].cantidad}</td>
              </tr>
            `);
          }else if(data[i].fun_estado == 1){
            $("#contenido").append(`
              <tr onclick="encuesta(${data[i].fun_id}, ${data[i].fun_atr_valor})">
                <td>${data[i].fun_nombre_completo}</td>
                <td>No realizado</td>
                <td>${data[i].cantidad}</td>
              </tr>
            `);
          }
        }
        // =======================  Data tables ==================================
        definirdataTable("#tabla");
      },
      error: function(){
        alert("No se ha podido traer la lista");
      }
    });
  }

  function encuesta(id, idCompetencia){
    if(idCompetencia != null){
      if (idCompetencia == 2) {
        var atributo = 10;      
      } else {
        var atributo = 31;
      }
      //window.location.href = 'encuesta?et_id=' + idCompetencia + '&id_usu='+id;
      top.$("#cargando").modal("show");
      window.location.href = 'encuesta.php?et_id=' + idCompetencia + '&filtro_atr=' + atributo + '|' + id;
    }else{
      alertify.error("Debes de definir los atributos de creser.");
    }
  }
</script>
</html>