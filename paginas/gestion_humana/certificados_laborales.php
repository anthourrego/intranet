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
  <?php  
    echo $lib->metaTagsRequired();
  ?>
	<title>Consumer Electronics Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->bootstrapTempusDominus();
    echo $lib->alertify();
    echo $lib->datatables();
    echo $lib->fontAwesome();
    echo $lib->jqueryValidate();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container mt-5 rounded pt-3 pb-5" style="background: rgba(255,255,255,0.6)">
    <h5 class="text-center">Certificados Laborales</h5>
    <table id="tabla" class="table table-bordered table-hover table-striped table-sm mt-5 ">
      <thead>
        <tr class="text-center">
          <th>Nombre</th>
          <th>Acciones</th>
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

    $.ajax({
      type: "POST",
      url: "<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/certificados_laborales.php",
      dataType: "json",
      data: {accion: "listaUsuarios"},
      success: function(data){
        $("#contenido").empty();
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#contenido").append(`
            <tr>
              <td class='text-center'>${data[i]['fun_nombre_completo']}</td>
              <td class='text-center'>
                <button class='btn btn-success mr-3' onClick='certificadoLaboral(${data[i]['fun_id']})'><i class='fas fa-eye'></i></button>
              </td>
            </tr>
          `);
        }
        
        definirdataTable("#tabla");
      },
      error: function(){
        alertify.error("No se ha podido traer la lista");
      },
      complete: function(){
        cerrarCargando();
      }
    });

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "certificados_laborales"},
      success: function(data){
        if (data.length == 0) {
          window.location.href = "index.php";
        }
      },
      error: function(){
        window.location.href = "index.php";
      }
    });
  });

  function certificadoLaboral(id){
    top.$('#cargando').modal("show");
    $('#modalArchivosTitulo').html("Certificado ");
    $("#contenidoArchivos").attr("src", '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/certificado_laboral.php?id=' + id);
    modalArchivos();
  }

</script>
</html>