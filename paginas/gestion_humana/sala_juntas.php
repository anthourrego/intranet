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
    echo $lib->jqueryValidate();
    echo $lib->fullCalendar();
    echo $lib->intranet();
  ?>
  <style>
    #calendar {
      max-height: 500px !important;
      margin: 0 auto;
    }
  </style>
</head>
<body>
	<div class="container mt-5 rounded pt-3 pb-5" style="background: rgba(255,255,255,0.6)">
    <div id='calendar' class="w-100"></div>
  </div>
</body>

<div class="modal fade" id="modalSepararSala" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Separar Sala</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formSepararSala">
        <div class="modal-body">
          <input type="hidden" name="accion" required value="formSepararSala">
          <input type="hidden" name="idusu" required value="<?php echo($usuario['id']); ?>">
          <input type="hidden" name="sala" required id="sala">
          <div class="row">
            <div class="form-group col-12 col-md-6">
              <label for="">Fecha Inicio:</label>
              <input type="text" name="fechaInicio" required id="fechaInicio" class="form-control" readonly>
            </div>
            <div class="form-group col-12 col-md-6">
              <label for="">Fecha Inicio:</label>
              <input type="text" name="fechaFinal" required id="fechaFinal" class="form-control" readonly>
            </div>
            <div class="form-group col-12">
            <label for="">Descripción:</label>
              <textarea id="descripcion" name="descripcion" cols="30" rows="3" class="form-control" require></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
          <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php  
  echo $lib->cambioPantalla();
?>
<script>
  $(function(){


    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
      plugins: [ 'interaction', 'resourceTimeGrid', 'resourceDayGrid'],
      locale: 'es-us',
      timeZone: 'local',
      defaultView: 'resourceTimeGridDay',
      contentHeight: 610,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'resourceTimeGridWeek, resourceTimeGridDay'
      },
      allDaySlot: false,
      datesAboveResources: true,
      minTime: '07:00',
      maxTime: '18:00',
      //defaultDate: '2019-04-12',
      defaultDate: new Date(),
      navLinks: true, // can click day/week names to navigate views
      selectable: true,
      selectOverlap: false,
      weekNumbers: true,
      selectMirror: true,
      select: function(arg) {
        //console.log(arg.start);
        //console.log(moment(arg.start).add(1, 'm').format('YYYY-MM-DD H:mm:ss') + "  " + moment(arg.end).format('YYYY-MM-DD H:mm:ss') + "  " + arg.resource.id)
        $.ajax({
          url: "<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/sala_juntas.php",
          type: "GET",
          dataType: "json",
          data: {accion: "validar", inicio: moment(arg.start).add(1, 'm').format('YYYY-MM-DD H:mm:ss'), fin: moment(arg.end).subtract(1, 'm').format('YYYY-MM-DD H:mm:ss'), sala: arg.resource.id},
          success: function(data){
            if (data.cantidad_registros == 0) {
              $("#fechaInicio").val(moment(arg.start).format('YYYY-MM-DD h:mm a'));
              $("#fechaFinal").val(moment(arg.end).format('YYYY-MM-DD h:mm a'));
              $("#sala").val(arg.resource.id);
              $("#modalSepararSala").modal("show");
            }else{
              alertify.error("En el rango de " + moment(arg.start).format('h:mm a') + " hasta las " + moment(arg.end).format('h:mm a'));
              calendar.refetchEvents();
            }
          },
          error: function(){
            alertify.error("Error al validar la reservación.");
          }
        });
        calendar.unselect()
      },
      eventClick: function (info) {
        var eventObj = info.event;
        $.ajax({
          url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
          type: 'POST',
          dataType: 'json',
          data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "sala_juntas_eliminar"},
          success: function(data){
            if (data.length == 1 || info.event.extendedProps.user == <?php echo($usuario['id']); ?>) {
              alertify.confirm('<b>Eliminar reservación</b> | ' + moment(eventObj.start).format('h:mm a') + " - " + moment(eventObj.end).format('h:mm a'), eventObj.extendedProps.description + "<br><b>Estás seguro de eliminarla?</b>", 
                      function(){
                        $.ajax({
                          url: "<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/sala_juntas.php",
                          type: "POST",
                          dataType: "json",
                          data: {accion: "eliminarReservacion", id: eventObj.id},
                          success: function(data){
                            if(data == 1){
                              calendar.refetchEvents();
                              alertify.success("Se ha eliminado correctamente");
                            }else{
                              alertify.error("Error al eliminar");
                            }
                          },
                          error: function(){
                            alertify("No se ha podido eliminar la reservación");
                          }
                        }); 
                      }, function(){});
            }else{
              alertify.alert('Reservación | ' + moment(eventObj.start).format('h:mm a') + " - " + moment(eventObj.end).format('h:mm a'), eventObj.extendedProps.usuario + "<br>" + eventObj.extendedProps.description);
            }
          },
          error: function(){
            //window.location.href = "index.php";
          }
        });
      },
      editable: false,
      eventLimit: true, // allow "more" link when too many events
      eventTextColor: "white",
      resourceLabelText: 'Sala de Juntas',
      resources: [
        { id: 1, title: 'Android' },
        { id: 2, title: 'Inverter' }
      ],
      events: {
        url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/sala_juntas.php?accion=reservaciones&idUsu=<?php echo($usuario['id']); ?>',
        cache: false
        /*failure: function () {
          document.getElementById('script-warning').style.display = 'block';
        }*/
      }
    });

    calendar.render();


    //Formulario crear curso
    $("#formSepararSala").validate({
      debug: true,
      rules: {
        fechaInicio: "required",
        fechaFinal: "required",
        sala: "required",
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

    $("#formSepararSala").submit(function(event){
      event.preventDefault();
      $.ajax({
        url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/sala_juntas.php',
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: new FormData(this),
        success: function(data){
          if (data == 1) {
            $("#fechaInicio").val("");
            $("#fechaFinal").val("");
            $("#sala").val("");
            $("#descripcion").val("");
            $("#modalSepararSala").modal("hide");
            calendar.refetchEvents();
          }else{
            aleritify.error(data);
          }
          
        },
        error: function(){
          alertify.error();
        }
      });
    });
  });

</script>
</html>