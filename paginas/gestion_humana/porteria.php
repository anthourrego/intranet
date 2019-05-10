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
	<div class="container mt-5 rounded pt-3 pb-5" style="background: rgba(255,255,255,0.6);">
    <h2 class="text-center">Permisos aprobados</h2>
    <hr>
    <table id="tablaUsuario" class="table table-bordered table-hover table-striped table-sm">
      <thead class="text-center">
        <tr>
          <th>Funcionario</th>
          <th>Fecha Inicio</th>
          <th>Hora Inicio</th>
          <th>Fecha Fin</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="contenidoUsuario">
        
      </tbody>
    </table>
  </div>

  <div class="modal fade" tabindex="-2" style="z-index: 1600;" id="modal-observaciones" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Finalizar Permiso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formHoraLlegada">
            <div class="row justify-content-center">
              <input type="hidden" id="idSolicitudPermiso" name="idSolicitudPermiso">
              <input type="hidden" name="accion" value="finalizarPermiso">
              <div class="col-12 col-md-8">
                <label>Hora de llegada <span class="text-danger">*</span></label>
                <div class="input-group date" id="HoraLlegada" data-target-input="nearest">
                  <input type="text" id="formLlegadaHora" name="formLlegadaHora" class="form-control datetimepicker-input" data-toggle="datetimepicker" required data-target="#HoraLlegada"/>
                  <div class="input-group-append" data-target="#HoraLlegada" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button class="btn btn-success" type="submit"><i class='far fa-paper-plane'></i> Finalizar</button>
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
    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "solicitud_permisos_porteria"},
      success: function(data){
        if (data.length != 1) {
          window.location.href="index.php";
        }
        
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });


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
    $('#HoraLlegada').datetimepicker({
      format: 'h:mm a',
      defaultDate: new Date()
    });

    //Formulario crear curso
    $("#formHoraLlegada").validate({
      debug: true,
      rules: {
        formHoraLlegada: "required",
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

    $("#formHoraLlegada").submit(function(event){
      event.preventDefault();
      if($("#formHoraLlegada").valid()){
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
                tablaUsuario();
                $("#modal-observaciones").modal("hide");
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

  function finalizarPermiso(id){
    $("#modal-observaciones").modal("show");
    $("#idSolicitudPermiso").val(id);
  }

  function tablaUsuario(){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'HTML',
      data: {accion: 'listaUsuarioPorteria'},
      success: function(data){
        $("#tablaUsuario").dataTable().fnDestroy();
        $("#contenidoUsuario").empty();
        $("#contenidoUsuario").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaUsuario");

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("No se ha cargado la tabla");
      }
    }); 
  }
</script>
</html>