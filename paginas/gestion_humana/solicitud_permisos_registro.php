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
	<title>Consumer Electronocs Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->bootstrapTempusDominus();
    echo $lib->alertify();
    echo $lib->datatables();
    echo $lib->fontAwesome();
    echo $lib->fontAwesome4();
    echo $lib->jqueryValidate();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container mt-5 rounded pt-3 pb-5" style="background: rgba(255,255,255,0.6)">
    <h5 class="text-center">Registro de permisos por fecha</h5>

    <div class="form-row pt-3">
      <div class="col-6">
        <select name="ano" id="ano" class="form-control"></select>
      </div>
      <div class="col-6">
        <select disabled="true" name="mes" id="mes" class="form-control"></select>
      </div>
    </div>

    <div class="text-center mb-3 mt-4">
      <button class="btn btn-primary motivo" disabled value="1">Médica</button>
      <button class="btn btn-primary motivo" disabled value="2">Urgencia Médica</button>
      <button class="btn btn-primary motivo" disabled value="3">Laboral</button>
      <button class="btn btn-primary motivo" disabled value="4">Personal</button>
    </div>

    <table id="tabla" class="table table-bordered table-hover table-striped table-sm mt-5 invisible">
      <thead>
        <tr class="text-center">
          <th>Persona</th>
          <th>Reposición</th>
          <th>Fecha Inicio</th>
          <th>Hora Inicio</th>
          <th>Fecha Fin</th>
          <th>Hora Fin</th>
          <th>Tiempo</th>
          <th>Observaciones</th>
          <th>Autorizó</th>
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
    cerrarCargando();
    $(".motivo").on("click", function(){
      $.ajax({
        url: "<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php",
        type: "POST",
        dataType: "json",
        data: {accion: "listaPermisosRegistros", ano: $("#ano").val(), mes: $("#mes").val(), motivo: $(this).val()},
        success: function(data){
          $("#tabla").removeClass("invisible");
          $("#tabla").dataTable().fnDestroy();
          $('[data-toggle="tooltip"]').tooltip('hide');
          $("#contenido").empty();
          for (let i = 0; i < data.cantidad_registros; i++) {
            if (data[i].sp_reposicion == null) {
              repo = "N/A";
            }else if(data[i].sp_reposicion == 1){
              repo = "Reposición en tiempo";
            }else{
              repo = "No remunerado";
            }

            $("#contenido").append(`
              <tr>
                <td>${data[i].persona}</td>
                <td>${repo}</td>
                <td>${data[i].sp_fecha_inicio}</td>
                <td>${moment(data[i].sp_hora_inicio, "hh:mm").format("hh:mm a")}</td>
                <td>${data[i].sp_fecha_fin}</td>
                <td>${moment(data[i].sp_hora_llegada, "hh:mm").format("hh:mm a")}</td>
                <td>${minutosAHorasyminutos(data[i].tiempo)}</td>
                <td><a href="#" data-toggle="tooltip" title="${data[i].sp_observaciones}">${data[i].sp_observaciones.substring(0,20)}...</a></td>
                <td>${data[i].autorizo}</td>
              </tr>
            `);
          }  
          $('[data-toggle="tooltip"]').tooltip();
          definirdataTable("#tabla");
          
        },
        error: function(){
          alertify.error("No se han podido traer los registros.");
        }
      });
    });

    $("#mes").on("change", function(){
      $("#btn-consulta").attr("disabled", false);
      $(".motivo").attr("disabled", false);
    });

    $.ajax({
      url: '<?php echo(RUTA_BASE); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "solicitud_permisos_registros"},
      success: function(data){
        if (data.length == 0) {
          window.location.href = "index.php";
        }
      },
      error: function(){
        window.location.href = "index.php";
      }
    });

    $.ajax({
      type: "POST",
      dataType: "json",
      cache: false,
      url: "<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php",
      data: {accion: "listaYearPermisos"},
      success: function(data){
        $("#ano").empty();
        $("#ano").append('<option value="" selected disabled>Seleccione un año</option>');
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#ano").append(`
            <option value="${data[i].ano}">${data[i].ano}</option>
          `);
        }

        $("#ano").on("change", function(){
          $.ajax({
            type: "POST",
            dataType: "json",
            cache: false,
            url: "<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php",
            data: {accion: "listaMesesPermisos", ano: $(this).val()},
            success: function(data){
              $("#mes").attr("disabled", false);
              $("#mes").empty();
              $("#mes").append('<option value="" selected disabled>Seleccione un mes</option>');
              for (let i = 0; i < data.cantidad_registros; i++) {
                $("#mes").append(`
                  <option value="${data[i].mes}">${moment.months(data[i].mes - 1)}</option>
                `);
              }
            },
            error: function(){
              alertify.error("Error al cargar los meses");
            }
          });
        });
      },
      error: function(){
        alertify.error("No se han cargado las fechas");
      }
    });
  });
</script>
</html>