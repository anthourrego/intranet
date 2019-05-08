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
	<div class="container mt-5">
    <div class="d-flex justify-content-end">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_solicitarPermiso"><i class="fas fa-plus"></i> Solicitar Permiso</button>
    </div>
    <hr>
    <div class="table-responsive">
      <table id="tablaUsuario" class="table table-bordered table-hover table-striped table-sm table-light">
        <thead>
          <tr>
            <th>Motivo</th>
            <th>Reposicion</th>
            <th>Fecha Inicio</th>
            <th>Hora Inicio</th>
            <th>Fecha Fin</th>
            <th>Observaciones</th>
            <th>Fecha</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody id="contenidoUsuario">
          
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
</body>
<?php  
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){

    tablaUsuario();

    $("input[name='motivo_permiso']").on("click", function(){
      if ($(this).val() == 4) {
        $("#selectPersonal").removeClass('d-none');
        $("input[name='reposicion']").removeAttr('disabled');
      }else{
        $("#selectPersonal").addClass('d-none');
        $("input[name='reposicion']").attr('disabled', 'true');
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
          url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              setTimeout(function() {
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
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'HTML',
      data: {accion: 'listaPermisosUsuario', idUsu: <?php echo($usuario['id']); ?>},
      success: function(data){
        $("#contenidoUsuario").empty();
        $("#contenidoUsuario").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaUsuario");
      },
      error: function(){
        alertify.error("No se ha cargado la tabla");
      }
    });
    
  }
</script>
</html>