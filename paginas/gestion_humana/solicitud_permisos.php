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
    <div class="d-flex justify-content-between" id="botones">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-primary" onclick="tablaUsuario()">Activos</button>
        <button type="button" class="btn btn-secondary" onclick="tablaUsuario1(4)">Rechazados</button>
        <button type="button" class="btn btn-info" onclick="tablaUsuario1(5)">Finalizados</button>
        <button type="button" class="btn btn-danger" onclick="tablaUsuario1(2)">Anulados</button>
      </div>
      
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_solicitarPermiso"><i class="fas fa-plus"></i> Solicitar Permiso</button>
    </div>
    <hr>
    <table id="tablaUsuario" class="table table-bordered table-hover table-striped table-sm w-100">
      <thead class="text-center">
        <tr>
          <th>Fecha Creación</th>
          <th>Motivo</th>
          <th>Reposicion</th>
          <th>Fecha Inicio</th>
          <th>Hora Inicio</th>
          <th>Fecha Fin</th>
          <th>Observaciones</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="contenidoUsuario">
        
      </tbody>
    </table>

    <table id="tablaUsuario1" class="table table-bordered table-hover table-striped table-sm w-100">
      <thead class="text-center">
        <tr>
          <th>Fecha Creación</th>
          <th>Autorización</th>
          <th>Motivo</th>
          <th>Reposicion</th>
          <th>Fecha Inicio</th>
          <th>Hora Inicio</th>
          <th>Fecha Fin</th>
          <th>Hora Fin</th>
          <th>Observaciones</th>
        </tr>
      </thead>
      <tbody id="contenidoUsuario1">
        
      </tbody>
    </table>


    <div id="personalACargo" class="mt-5 pt-3 border-top invisible">
      <h5 class="text-center">Persona a Cargo</h5>
      <table id="tabla" class="table table-bordered table-hover table-striped table-sm mt-5 w-100">
        <thead>
          <tr class="text-center">
            <th>Nombre</th>
            <th>Permisos pendientes</th>
          </tr>
        </thead>
        <tbody id="contenido">

        </tbody>
      </table>
    </div>

  </div>

  <div class="modal fade" id="modal_solicitarPermiso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Solicitar Permiso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formCrearPermiso" class="container">
            <input type="hidden" name="idUsu" value="<?php echo($usuario['id']); ?>">
            <input type="hidden" name="accion" value="crearPermiso">
            <div class="form-row">
              <div class="form-group col-12 col-md-6">
                <div class="row">
                  <label class="col-3 col-form-label">Nombre:</label>
                  <div class="col-9">
                    <input type="text" readonly class="form-control" required name="nombre" value="<?php echo($usuario['nombre']); ?>">
                  </div>
                </div>
              </div>
              <div class="form-group col-12 col-md-6">
                <div class="row">
                  <label class="col-3 col-form-label">Cedula:</label>
                  <div class="col-9">
                    <input type="text" readonly class="form-control" required name="cedula" value="<?php echo($usuario['cedula']); ?>">
                  </div>
                </div>
              </div>
              <div class="form-group col-12 col-md-6">
                <div class="row">
                  <label class="col-3 col-form-label">Fecha:</label>
                  <div class="col-9">
                    <input type="text" class="form-control" name="fecha" required readonly value="<?php echo(date("d/m/Y H:m:s")); ?>">
                  </div>
                </div>
              </div>
              <div class="form-group col-12 col-md-6">
                <div class="row">
                  <label class="col-3 col-form-label">Área:</label>
                  <div class="col-9">
                    <input type="text" name="area" value="<?php echo($usuario['area']); ?>" readonly required class="form-control">
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <div class="form-row">
              <div class="col-4 align-self-center">
                <b>MOTIVO DEL PERMISO <span class="text-danger">*</span></b>
              </div>
              <div class="col-8 mt-3">
                <div class="row container" id="motivosPermiso">
                  <div class="custom-control custom-radio col-6 col-md-3">
                    <input type="radio" id="motivoMedica" required name="motivo_permiso" class="custom-control-input" value="1">
                    <label class="custom-control-label" for="motivoMedica">Medica</label>
                  </div>
                  <div class="custom-control custom-radio col-6 col-md-3">
                    <input type="radio" id="motivoUrgenciaMedica" required name="motivo_permiso" class="custom-control-input" value="2">
                    <label class="custom-control-label" for="motivoUrgenciaMedica">Urgencia Medica</label>
                  </div>
                  <div class="custom-control custom-radio col-6 col-md-3">
                    <input type="radio" id="motivoLaboral" required name="motivo_permiso" class="custom-control-input" value="3">
                    <label class="custom-control-label" for="motivoLaboral">Laboral</label>
                  </div>
                  <div class="custom-control custom-radio col-6 col-md-3">
                    <input type="radio" id="motivoPersonal" required name="motivo_permiso" class="custom-control-input" value="4">
                    <label class="custom-control-label" for="motivoPersonal">Personal</label>
                  </div>
                </div>
              </div>
              <div id="selectPersonal" class="col-12 mt-3 border-top pt-3 d-none">
                <div class="d-flex justify-content-center">
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="radioReposicionTiempo" value="1" disabled required name="reposicion" class="custom-control-input">
                    <label class="custom-control-label" for="radioReposicionTiempo">Reposición en tiempo</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="radioNoRemunerado" disabled value="2" required name="reposicion" class="custom-control-input">
                    <label class="custom-control-label" for="radioNoRemunerado">No remunerado</label>
                  </div>
                </div>
              </div>
            </div>
            
            <hr>

            <div class="form-row">
              <div class="col-12 col-md-4">
                <label>Fecha de inicio del permiso <span class="text-danger">*</span></label>
                <div class="input-group date" id="inicioPermiso" data-target-input="nearest">
                  <input type="text" id="formInicioPermiso" name="formInicioPermiso" class="form-control datetimepicker-input" required data-toggle="datetimepicker" data-target="#inicioPermiso"/>
                  <div class="input-group-append" data-target="#inicioPermiso" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <label>Hora de sálida <span class="text-danger">*</span></label>
                <div class="input-group date" id="inicioHora" data-target-input="nearest">
                  <input type="text" id="formInicioHora" name="formInicioHora" class="form-control datetimepicker-input" data-toggle="datetimepicker" required data-target="#inicioHora"/>
                  <div class="input-group-append" data-target="#inicioHora" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <label>Fecha fin del permiso <span class="text-danger">*</span></label>
                <div class="input-group date" id="finalPermiso" data-target-input="nearest">
                  <input type="text" id="formFinalPermiso" name="formFinalPermiso" class="form-control datetimepicker-input" required data-toggle="datetimepicker" data-target="#finalPermiso"/>
                  <div class="input-group-append" data-target="#finalPermiso" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <div class="form-group">
              <label>OBSERVACIONES <span class="text-danger">*</span></label>
              <textarea class="form-control" name="observaciones" id="observaciones" required rows="3"></textarea>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" tabindex="-2" style="z-index: 1600;" id="modal-observaciones" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Observaciones</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="modal-observaciones-body"></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalPermisosUsuario" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloModalPermisoUsuario">Usuarios</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="botones-usuario" class="d-flex justify-content-between mb-3"></div>
          <table id="tablaXUsuario" class="table table-bordered table-striped table-hover table-sm">
            <thead>
              <tr class="text-center">
                <th>Fecha Creación</th>
                <th>Autorización</th>
                <th>Motivo</th>
                <th>Reposicion</th>
                <th>Fecha Inicio</th>
                <th>Hora Inicio</th>
                <th>Fecha Fin</th>
                <th>Hora Fin</th>
                <th>Observaciones</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="contenidoTablaPermisoUsuario"></tbody>
          </table>

          <table id="tablaXUsuario1" class="table table-bordered table-hover table-striped table-sm table-light">
            <thead class="text-center">
              <tr>
                <th>Fecha Creación</th>
                <th>Autorización</th>
                <th>Motivo</th>
                <th>Reposicion</th>
                <th>Fecha Inicio</th>
                <th>Hora Inicio</th>
                <th>Fecha Fin</th>
                <th>Hora Fin</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody id="contenidoTablaPermisoUsuario1"></tbody>
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
    tablaUsuario();
    cargarTablaUsuarios();
    cerrarCargando();

    $("input[name='motivo_permiso']").on("click", function(){
      if ($(this).val() == 4) {
        $("#selectPersonal").removeClass('d-none');
        $("input[name='reposicion']").removeAttr('disabled');
      }else{
        $("#selectPersonal").addClass('d-none');
        $("input[name='reposicion']").attr('disabled', 'true');
      }
    });

     $.ajax({
      url: '<?php echo(RUTA_BASE); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "solicitud_permisos_registros"},
      success: function(data){
        if (data.length != 0) {
          $("#botones").append('<a class="btn btn-info" href="solicitud_permisos_registro"><i class="fas fa-book-open"></i> Registro</a>');
        }
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });


    $.ajax({
      url: '<?php echo(RUTA_BASE); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "solicitud_permisos_todos"},
      success: function(data){
        if (data.length != 0) {
          $("#botones").append('<button class="btn btn-success mr-2" id="btn-todos"><i class="fas fa-users"></i> Todos</button>');
          $('#btn-todos').on("click", function(){
            window.location.href = "solicitud_permisos_todos.php";
          });
        }
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });

    // Rango de fechas 
    $('#inicioPermiso').datetimepicker({
      format: 'L',
      defaultDate: new Date()
    });

    $('#finalPermiso').datetimepicker({
      format: 'L',
      defaultDate: new Date()
    });

    $('#inicioHora').datetimepicker({
      format: 'h:mm a',
      defaultDate: new Date()
    });

    $('#llegadaHora').datetimepicker({
      format: 'h:mm a'
    });

    $("#inicioPermiso").on("change.datetimepicker", function (e) {
      $('#finalPermiso').datetimepicker('minDate', e.date);
    });
    $("#finalPermiso").on("change.datetimepicker", function (e) {
      $('#inicioPermiso').datetimepicker('maxDate', e.date);
    });

    //Formulario crear curso
    $("#formCrearPermiso").validate({
      debug: true,
      rules: {
        motivo_permiso: "required",
        formInicioPermiso: "required",
        formInicioHora: "required",
        formFinalPermiso: "required",
        observaciones: {
          required: true,
          minlength: 10
        }
      },
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
        $(element).removeClass('is-valid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
        $(element).addClass('is-valid');
      }
    });

    $("#formCrearPermiso").submit(function(event){
      event.preventDefault();
      if($("#formCrearPermiso").valid()){
        top.$("#cargando").modal("show");
        $.ajax({
          url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              setTimeout(function() {
                tablaUsuario();
                $("#modal_solicitarPermiso").modal("hide");
                top.$("#cargando").modal("hide");
              }, 1000);
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            setTimeout(function() {
              top.$("#cargando").modal("hide");
            }, 1000);
            alertify.error("No se ha podido enviar el formulario.");
          }
        });
      }
    });
  });

  function tablaUsuario(){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'HTML',
      data: {accion: 'listaPermisosUsuario', idUsu: <?php echo($usuario['id']); ?>},
      success: function(data){
        $("#tablaUsuario1").hide();
        $("#tablaUsuario1").dataTable().fnDestroy();

        $("#tablaUsuario").show();
        $("#tablaUsuario").dataTable().fnDestroy();
        $("#contenidoUsuario").empty();
        $("#contenidoUsuario").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaUsuario");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("No se ha cargado la tabla");
      }
    }); 
  }

  function tablaUsuario1(id){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaPermisosUsuario1', idUsu: <?php echo($usuario['id']); ?>, idEstado: id},
      success: function(data){
        $("#tablaUsuario").hide();
        $("#tablaUsuario").dataTable().fnDestroy();
        
        $("#tablaUsuario1").show();        
        $("#tablaUsuario1").dataTable().fnDestroy();
        $("#contenidoUsuario1").empty();
        $("#contenidoUsuario1").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaUsuario1");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("");
      }
    });
  }

  function anularPermiso(id){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'cambioEstadoPermiso', idPermiso: id, idEstado: 2},
      success: function(data){
        if (data == "Ok") {
          alertify.success("Se ha anulado el permiso.");
          tablaUsuario();
        }else{
          alertify.error(data);
        }
      },
      error: function(){
        alertify.error("Error al anular el permiso."); 
      }
    });
  }

  function cargarTablaUsuarios(){
    $.ajax({
      type: "POST",
      url: "<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php",
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

  function tablaPermisoUsuario(id, estado, lider){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaPermisosUsuario1', idUsu: id, idEstado: estado, lider: lider},
      success: function(data){
        $("#tablaXUsuario1").hide();
        $("#tablaXUsuario1").dataTable().fnDestroy();
        
        $("#tablaXUsuario").show();        
        $("#tablaXUsuario").dataTable().fnDestroy();
        $("#contenidoTablaPermisoUsuario").empty();
        $("#contenidoTablaPermisoUsuario").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaXUsuario");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("Error al cargar.");
      }
    });
  }

  function tablaPermisoUsuario1(id, estado){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaPermisosUsuario1', idUsu: id, idEstado: estado},
      success: function(data){
        $("#tablaXUsuario").hide();
        $("#tablaXUsuario").dataTable().fnDestroy();
        
        $("#tablaXUsuario1").show();        
        $("#tablaXUsuario1").dataTable().fnDestroy();
        $("#contenidoTablaPermisoUsuario1").empty();
        $("#contenidoTablaPermisoUsuario1").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaXUsuario1");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("Error al cargar.");
      }
    });
  }

  function aprobarPermiso(idUsu, idPermiso){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'cambioEstadoPermiso', idPermiso: idPermiso, idEstado: 3, idAutoriza: <?php echo($usuario['id']); ?>},
      success: function(data){
        if (data == "Ok") {
          alertify.success("Se ha aprobado el permiso.");
          tablaPermisoUsuario(idUsu, 1, 1);
        }else{
          alertify.error(data);
        }
      },
      error: function(){
        alertify.error("Error al aprobar el permiso."); 
      }
    });
  }

  function rechazarPermiso(idUsu, idPermiso){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'cambioEstadoPermiso', idPermiso: idPermiso, idEstado: 4, idAutoriza: <?php echo($usuario['id']); ?>},
      success: function(data){
        if (data == "Ok") {
          alertify.success("Se ha rechazado el permiso.");
          tablaPermisoUsuario(idUsu, 1, 1);
        }else{
          alertify.error(data);
        }
      },
      error: function(){
        alertify.error("Error al rechazar el permiso."); 
      }
    });
  }

  function permisoUsuario(id, nombre){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaPermisosUsuario1', idEstado: 1, idUsu: id, lider: 1},
      success: function(data){
        $("#tablaXUsuario1").hide();
        $("#tablaXUsuario1").dataTable().fnDestroy();
        
        $("#tablaXUsuario").show();        
        $("#tablaXUsuario").dataTable().fnDestroy();
        $("#contenidoTablaPermisoUsuario").empty();
        $("#contenidoTablaPermisoUsuario").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaXUsuario");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();

        $("#botones-usuario").html(`<div class="btn-group" role="group" aria-label="Basic example">
                                      <button type="button" class="btn btn-primary" onclick="tablaPermisoUsuario(${id}, 1, 1)">En espera</button>
                                      <button type="button" class="btn btn-success" onclick="tablaPermisoUsuario1(${id}, 3)">Aprobados</button>
                                      <button type="button" class="btn btn-secondary" onclick="tablaPermisoUsuario1(${id}, 4)">Rechazados</button>
                                      <button type="button" class="btn btn-info" onclick="tablaPermisoUsuario1(${id}, 5)">Finalizados</button>
                                    </div>`);

        $("#tituloModalPermisoUsuario").html(nombre);
        $("#modalPermisosUsuario").modal("show");
      },
      error: function(){
        alertify.error("Error al cargar.");
      }
    });
  }
</script>
</html>