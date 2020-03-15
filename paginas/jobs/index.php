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
    echo $lib->intranet();
  ?>
</head>
<body>
  <div class="container mt-5 rounded pt-3 pb-5 border" style="background: rgba(255,255,255,0.6)">
    <div id="btn-marcas" class="d-flex justify-content-end"></div>
  
    <table class="table table-hover mt-4" id="marcas-tabla">
      <thead id="marcas-tabla-thead">
        <tr>
          <th>Nombre</th>
        </tr>
      </thead>
      <tbody id="marcas-tabla-tbody"></tbody>
    </table>
  </div>
  <div class="modal fade" id="modalCrearMarca" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Crear Marca</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearMarca">
          <input type="hidden" name="accion" value="crearMarca" required>
          <div class="modal-body">            
            <div class="form-group">
              <label for="marca">Marca</label>
              <input type="text" class="form-control" name="nombre" autofocus required>
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
<script type="text/javascript">
  $(function(){
    //Cargamos la tabla de marcas
    cargarMarcas();
    

    $("#modalCrearMarca").on('shown.bs.modal', function(e){
      if (top.validarPermiso('jobs_marcas') == 1) {
        $("#formCrearMarca").submit(function(event){
          event.preventDefault();
          if ($("#formCrearMarca").valid()) {
            top.$("#cargando").modal("show");
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
                  $("#modalCrearMarca").modal("hide");
                  alertify.success(data.msj);
                }else{
                  alertify.error(data.msj);
                }
              },
              error: function(){
                alertify.error("No se han enviado datos...");
              } 
            });
          }
        }); 
      }else{
        $("#btn-marcas").empty();
        $("#modalCrearMarca").modal('hide');
      }
    });

  });


  function cargarMarcas(){
    $.ajax({
      url: 'acciones.php',
      type: 'POST',
      dataType: 'json',
      data: {accion: "ListaMarcas"},
      success: function(data){
        $permiso = top.validarPermiso('jobs_marcas');
        if ($permiso == 1) {
          $("#btn-marcas").empty();
          $("#btn-marcas").append(`
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearMarca"><i class="fas fa-plus"></i> Crear Marca</button>
          `);
        }
        if (data.success == true) {
          $("#marcas-tabla-tbody").empty();
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#marcas-tabla-tbody").append(`
              <tr>
                <td>${data.msj[i].nombre}</td>
              </tr>
            `);
          }
        } else {
          alertify.error(data.msj);
        }
      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      },
      complete: function(){
        cerrarCargando();
      }
    });
  }
</script>
</html>