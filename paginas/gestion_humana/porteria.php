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
	<div class="container mt-5 rounded pt-3 pb-5 border" style="background: rgba(255,255,255,0.6);">
    <h2 class="text-center">Permisos aprobados</h2>
    <hr>
    <div class="d-flex justify-content-end mb-4">
      <button id="sinc-permisos" class="btn btn-success" data-toggle='tooltip' data-placement='top' title='Sincronizar'>
        <i class="fas fa-sync-alt"></i>
      </button>
    </div>
    <table id="tablaPermisos" class="table table-bordered table-hover table-striped table-sm">
      <thead class="text-center">
        <tr>  
          <th>Funcionario</th>
          <th>Fecha Inicio</th>
          <th>Hora Inicio</th>
          <th>Fecha Fin</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="contenidoPermisos"></tbody>
    </table>
  </div>

  <div class="modal fade" tabindex="-2" id="modal-observaciones" role="dialog">
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
    //botón para sincronizar
    $("#sinc-permisos").on("click", function(){
      tablaPermiso(); 
    });

    //Se valida el permiso de acceso a la pagina
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

    // Se define el tipo de hora que se muestra en el input
    $('#HoraLlegada').datetimepicker({
      format: 'h:mm a',
      defaultDate: new Date()
    });

    //Cargamos la tabla de permisos
    tablaPermiso();

    //Validamos el formulario
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

    //Lo que se ejecuta al enviar los datos del formulario
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
              tablaPermiso();
              $("#modal-observaciones").modal("hide");
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("No se ha podido enviar el formulario.");
          },
          complete: function(){
            cerrarCargando();
          }
        });
      }
    });
  });

  function finalizarPermiso(id){
    $("#modal-observaciones").modal("show");
    $("#idSolicitudPermiso").val(id);
  }

  function tablaPermiso(){
    top.$("#cargando").modal("show");
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'listaUsuarioPorteria'},
      success: function(data){
        $("#tablaPermisos").dataTable().fnDestroy();
        $("#contenidoPermisos").empty();
        
        for (let i = 0; i < data.msj.cantidad_registros; i++) {
          $("#contenidoPermisos").append(`
            <tr>
              <td>${data.msj[i].fun_nombre_completo}</td>
              <td>${data.msj[i].sp_fecha_inicio}</td>
              <td>${moment(data.msj[i].sp_hora_inicio, "HH:mm:ss").format("hh:mm A")}</td>
              <td>${data.msj[i].sp_fecha_fin}</td>
              <td class='d-flex justify-content-center'>
                <button class='btn btn-secondary' onClick='finalizarPermiso(${data.msj[i].sp_id})' data-toggle='tooltip' data-placement='top' title='Finalizar'><i class='far fa-paper-plane'></i></button>
              </td>
            </tr>
          `);
        }

        // =======================  Data tables ==================================
        definirdataTable("#tablaPermisos");

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("No se ha cargado la tabla");
      },
      complete: function(){
        cerrarCargando();
      }
    }); 
  }
</script>
</html>