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
  $usuario['id'];
  $lib = new Libreria;

?>
<!DOCTYPE html>
<html>
<head>
  <?php 
    echo $lib->metaTagsRequired();
  ?>
  <title>Consumer Electrnics Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->bootstrapTempusDominus();
    echo $lib->fontAwesome();
    echo $lib->alertify();
    echo $lib->datatables();
    echo $lib->fontAwesome4();
    echo $lib->intranet();
  ?>
</head>
<body>
  <div class="container mt-5">
    <div class="mb-4 d-flex justify-content-end" id="botones">
    </div>
    
    <hr>

    <form id="formBiometricoUsuario">
      <div class="form-row mb-3">
        <div class="col-6">
          <div class="input-group date" id="inicioBiometricoUsu" data-target-input="nearest">
            <input type="text" id="formInicioUsuario" class="form-control datetimepicker-input" data-target="#inicioBiometricoUsu"/>
            <div class="input-group-append" data-target="#inicioBiometricoUsu" data-toggle="datetimepicker">
              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="input-group date" id="finalBiomatricoUsu" data-target-input="nearest">
            <input type="text" id="formFinalUsuario" class="form-control datetimepicker-input" data-target="#finalBiomatricoUsu"/>
            <div class="input-group-append" data-target="#finalBiomatricoUsu" data-toggle="datetimepicker">
              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
          </div>
        </div>
      </div>
    </form>
  
    <hr>

    <table id="tabla" class="table table-bordered table-hover table-striped table-sm table-light mt-5 ">
      <thead>
        <tr class="text-center">
          <th>Nombre</th>
          <th>Fecha</th>
          <th>Hora</th>
        </tr>
      </thead>
      <tbody id="contenido"></tbody>
    </table>
  </div>
  <div class="modal fade" id="modalBiometricoUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloModalBiometricoUsuario">Usuarios</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tablaXUsuario" class="table table-bordered table-striped table-hover table-sm">
            <thead>
              <tr class="text-center">
                <th>Fecha</th>
                <th>Hora</th>
              </tr>
            </thead>
            <tbody id="contenidoTablaBiometricoUsuario"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</body>
<?php  
  echo $lib->cambioPantalla();
?> 
<script type="text/javascript">
  $(function(){
    //Validamos que tenga el permiso
    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "biometrico_areas"},
      success: function(data){
        if (data.length == 0) {
          window.location.href = "index.php";
        }
      },
      error: function(){
        window.location.href = "index.php";
      }
    });


    cargarTabla();

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "biometrico_sincronizar"},
      success: function(data){
        if (data.length != 0) {
          $("#botones").append('<button class="btn btn-success" id="sincronizar"><i class="fas fa-sync-alt"></i> Sincronizar</button>');
        }
        $("#sincronizar").on("click", function(){
          top.$("#cargando").modal("show");
          sincronizar();
        });
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });
    

    // Rango de fecha de usuario 
    $('#inicioBiometricoUsu').datetimepicker({
      format: 'L',
      defaultDate: new Date(),
      maxDate: new Date()
    });

    $('#finalBiomatricoUsu').datetimepicker({
      format: 'L',
      defaultDate: new Date(),
      maxDate: new Date()
    });

    $("#inicioBiometricoUsu").on("change.datetimepicker", function (e) {
      $('#finalBiomatricoUsu').datetimepicker('minDate', e.date);
    });
    $("#finalBiomatricoUsu").on("change.datetimepicker", function (e) {
      $('#inicioBiometricoUsu').datetimepicker('maxDate', e.date);
    });
  });

  function cargarTabla(){
    $.ajax({
      type: "POST",
      url: "<?php echo(direccionIPRuta()); ?>paginas/biometrico/index.php",
      data: {accion: "listaTodosArea", idUsu: <?php echo($usuario['id']) ?>},
      success: function(data){
        $("#contenido").empty();
        $("#contenido").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tabla");
      },
      error: function(){
        alertify.error("No se ha podido traer la lista");
      }
    });
  }

  function sincronizar(){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/biometrico/index.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'sincronizar'},
      success: function(data){
        if (data == "Ok") {
          alertify.success("Sincronización exitosa");
        }else{
          alertify.error(data);
        }
        setTimeout(function() {
         top.$("#cargando").modal("hide");
        }, 1000);
      },
      error: function(){
        setTimeout(function() {
         top.$("#cargando").modal("hide");
        }, 1000);
        alertify.error("No se ha sincronizado");
      }
    });
  }

  function biometricoUsuario(id, nombre = 'Usuario', fecha_inicio = moment().format("DD/MM/YYYY"), fecha_final = moment().format("DD/MM/YYYY")){
    if ($("#formInicioUsuario").val() != "" && $("#formFinalUsuario").val() != "") {
      fecha_inicio = $("#formInicioUsuario").val();
      fecha_final = $("#formFinalUsuario").val();
    }

    $("#tituloModalBiometricoUsuario").html(nombre);

    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/biometrico/index.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'marcacionUsuario', idUsuario: id, inicio: fecha_inicio, final: fecha_final},
      success: function(data){
        $("#tablaXUsuario").dataTable().fnDestroy();
        $("#contenidoTablaBiometricoUsuario").html(data);
        definirdataTable("#tablaXUsuario");
        $("#formIdUsuario").val(id);

        $("#modalBiometricoUsuario").modal("show");
      },
      error: function(){
        alertify.error("No han cargado los datos.");
      }
    });
  }
</script>
</html>