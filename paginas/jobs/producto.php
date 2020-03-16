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

  $usuario = $session->get("usuario");
  $ruta_documentos = array();

  $lib = new Libreria;
?>

<!DOCTYPE html>
<html>
<head>
  <?php 
    echo $lib->metaTagsRequired();
  ?>
  <title>Jobs</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->jqueryValidate();
    echo $lib->datatables();
    echo $lib->intranet();
  ?>
</head>
<body>
  <div class="container mt-5 rounded pt-3 pb-5 border" style="background: rgba(255,255,255,0.6)">
    <h2 class="text-center"><?php echo($_GET['referencia']) ?></h2>
    <hr>
    <div id="btn-pi" class="d-flex justify-content-end m-3"></div>
    <div id="btns-pi" class="d-flex justify-content-center m-3"></div>
  </div>

  <div class="modal fade" id="modalCrearPI" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Crear PI</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearPI">
          <input type="hidden" name="fk_referencia" value="<?php echo($_GET['id']); ?>" required>
          <input type="hidden" name="accion" value="crearPI" required>
          <div class="modal-body">            
            <div class="form-group">
              <label for="marca">PI</label>
              <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="form-group">
              <label for="unidades_pi">Unidades</label>
              <input type="number" class="form-control" name="unidades_pi" onkeypress="return soloNumeros(event)" value="" required>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Crear</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
<?php 
  echo $lib->cambioPantalla();
?>
<script>
  $(function(){
    $permiso = top.validarPermiso('jobs_pi');
    cerrarCargando();
    btnPI();

    if ($permiso == 1) {
      $("#btn-pi").append(`
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearPI"><i class="fas fa-plus"></i> Crear PI</button>
      `);
    }

    $("#modalCrearPI").on('shown.bs.modal', function(e){
      if ($permiso == 1) {
        //Enfocamos el campo
        $("#formCrearPI :input[name='nombre']").focus(); 
      }else{
        $("#btn-pi").empty();
        $("#modalCrearPI").modal('hide');
      }
    });

    $("#formCrearPI").submit(function(event){
      event.preventDefault();
      if ($permiso == 1) {
        if ($(this).valid()) {
          $.ajax({
            url: "acciones",
            type: "POST",
            dataType: "json",
            cache: false,
            contentType: false,
            processData: false,
            data: new FormData(this),
            success: function(data){
              if (data.success == true) {
                $("#modalCrearPI").modal("hide");
                alertify.success(data.msj);
                $("#formCrearPI :input[name='nombre']").val(""); 
                $("#formCrearPI :input[name='unidades_pi']").val(""); 
              }else{
                alertify.error(data.msj);
              }
            },
            error: function(){
              alertify.error("No se han enviado datos...");
            }
          });
        }
      }else{
        alertify.error("No tienes el permiso para hacer esto.");
      }
    });
  });

  function btnPI(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: "listaPI" , fk_referencia: <?php echo($_GET['id']); ?>},
      success: function(data){
        console.log(data);
        $('[data-toggle="tooltip"]').tooltip('hide');
        $("#btns-pi").empty();
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#btns-pi").append(`
              <button class="btn btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="${data.msj[i].unidades} unidades">${data.msj[i].pi}</button>
            `);
          }
        } else {
          alertify.error(data.msj);
        }
        $('[data-toggle="tooltip"]').tooltip();

        /*
        $("#referencia-tabla").dataTable().fnDestroy();
        $("#referencia-tabla-tbody").empty();
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#referencia-tabla-tbody").append(`
              <tr onClick="window.location.href='producto?id=${data.msj[i].id}&referencia=${data.msj[i].referencia}';">
                <td>${data.msj[i].referencia}</td>
              </tr>
            `);
          }
        }else{
          alertify.error(data.msj);
        }*/
      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      }
    });
  }
</script>
</html>