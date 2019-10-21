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
    <table id="tabla" class="table table-bordered bg-light table-hover table-sm">
      <thead>
        <tr>
          <th class="text-center">Área</th>
          <th class="text-center">Usuarios</th>
          <th class="text-center">Inactivos</th>
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
      data: {accion: "areas"},
      success: function(data){
        $("#contenido").empty();
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#contenido").append(`
            <tr onClick="redireccionar(${data[i].dep_id})">
              <td>${data[i].dep_tag}</td>
              <td class="text-center">${data[i].usuarios_activos}/${data[i].usuarios_evaluados}</td>
              <td class="text-center">${data[i].usuarios_evaluados_inactivos}</td>
            </tr>
          `);
        }
        // =======================  Data tables ==================================
        definirdataTable("#tabla");
      },
      error: function(){
        alert("No se ha podido traer la lista");
      }
    });
  }

  function redireccionar(idArea){
    window.location = "areas?idArea="+idArea
  }
</script>
</html>