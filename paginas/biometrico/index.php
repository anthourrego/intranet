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
      <button class="btn btn-success mr-2" id="sincronizar"><i class="fas fa-sync-alt"></i> Sincronizar</button>
    </div>
    
    <hr>

    <form id="formBiometricoUsuario">
      <div class="form-row mb-3">
        <div class="col-5">
          <div class="input-group date" id="inicioBiometricoUsu" data-target-input="nearest">
            <input type="text" id="formInicioUsuario" class="form-control datetimepicker-input" data-target="#inicioBiometricoUsu"/>
            <div class="input-group-append" data-target="#inicioBiometricoUsu" data-toggle="datetimepicker">
              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
          </div>
        </div>
        <div class="col-5">
          <div class="input-group date" id="finalBiomatricoUsu" data-target-input="nearest">
            <input type="text" id="formFinalUsuario" class="form-control datetimepicker-input" data-target="#finalBiomatricoUsu"/>
            <div class="input-group-append" data-target="#finalBiomatricoUsu" data-toggle="datetimepicker">
              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
          </div>
        </div>
        <div class="col-2 text-center">
          <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Consultar</button>
        </div>
      </div>
    </form>
  
    <hr>

    <table id="tablaUsuario" class="table table-bordered table-hover table-striped table-sm table-light">
      <thead>
        <tr class="text-center">
          <th>Fecha</th>
          <th>Hora</th>
        </tr>
      </thead>
      <tbody id="marcacionUsuario"></tbody>
    </table>

    <div id="personalACargo" class="mt-5 pt-3 border-top invisible">
      <h5 class="text-center">Persona a Cargo</h5>
      <table id="tabla" class="table table-bordered table-hover table-striped table-sm table-light mt-5 ">
        <thead>
          <tr class="text-center">
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Hora</th>
          </tr>
        </thead>
        <tbody id="contenido">

        </tbody>
      </table>
    </div>
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
    //Esto se debe pasar al validar si tiene el permiso

    cargarTabla();
    marcacionUsuario();
    cerrarCargando();

    $("#sincronizar").on("click", function(){
      top.$("#cargando").modal("show");
      sincronizar();
    });

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "biometrico_todos"},
      success: function(data){
        if (data.length != 0) {
          $("#botones").append('<button class="btn btn-primary mr-2" id="btn-todos"><i class="fas fa-users"></i> Todos</button>');

          $('#btn-todos').on("click", function(){
            window.location.href = "todos.php";
          });
        }
        
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "biometrico_areas"},
      success: function(data){
        if (data.length != 0) {
          $("#botones").append('<a class="btn btn-primary mr-2" href="registro_areas"><i class="fas fa-users"></i> Área</a>');
        }
        
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

    $("#formBiometricoUsuario").submit(function(event){
      event.preventDefault();
      top.$("#cargando").modal("show");
      marcacionUsuario($("#formInicioUsuario").val(), $("#formFinalUsuario").val());
      
    });
  });

  function cargarTabla(){
    $.ajax({
      type: "POST",
      url: "<?php echo(direccionIPRuta()); ?>paginas/biometrico/index.php",
      data: {accion: "listaUsuario", id: <?php echo $usuario['id']; ?>},
      success: function(data){
        if (data != "No") {
          $("#personalACargo").removeClass("invisible");
          $("#contenido").empty();
          $("#contenido").html(data);
          // =======================  Data tables ==================================
          definirdataTable("#tabla");
        }
      },
      error: function(){
        alertify.error("No se ha podido traer la lista");
      }
    });
  }

  function marcacionUsuario(fecha_inicio = moment().format("DD/MM/YYYY"), fecha_final = moment().format("DD/MM/YYYY")){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/biometrico/index.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'marcacionUsuario', idUsuario: <?php echo($usuario['id_geminus']); ?>, inicio: fecha_inicio, final: fecha_final},
      success: function(data){
        $("#tablaUsuario").dataTable().fnDestroy();
        $("#marcacionUsuario").html(data);
        definirdataTable("#tablaUsuario");
        setTimeout(function() {
         top.$("#cargando").modal("hide");
        }, 1000);
      },
      error: function(){
        alertify.error("No se han cargado los datos.");
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