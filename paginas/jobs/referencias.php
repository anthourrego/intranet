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
    <h2 class="text-center"><?php echo($_GET['nombre']) ?></h2>
    <hr>
    <div class="form-row">
      <div class="form-group col-12 col-md-4">
        <label for="">Línea:</label>
        <select id="selectLineas" class="custom-select">
          <option value="0" disabled selected>Seleccione una opción</option>
        </select>
      </div>
      <div class="form-group col-12 col-md-4">
        <label for="">Categoria:</label>
        <select id="selectCategorias" class="custom-select" disabled>
          <option value="0" disabled selected>Seleccione una opción</option>
        </select>
      </div>
      <div class="form-group col-12 col-md-4">
        <label for="">Tecnologia:</label>
        <select id="selectTecnologias" class="custom-select" disabled>
          <option value="0" disabled selected>Seleccione una opción</option>
        </select>
      </div>
    </div>
    <hr>
    <div id="btn-referencia" class="d-flex justify-content-end m-3"></div>
  
    <table class="table table-hover mt-4" id="referencia-tabla">
      <thead id="referencia-tabla-thead">
        <tr>
          <th>Referencia</th>
        </tr>
      </thead>
      <tbody id="referencia-tabla-tbody"></tbody>
    </table>
  </div>


  <div class="modal fade" id="modalCrearReferencia" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Crear Marca</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearReferencia">
          <input type="hidden" name="fk_marca" value="<?php echo($_GET['marca']); ?>" required>
          <input type="hidden" name="accion" value="crearReferencia" required>
          <div class="modal-body">            
            <label for="tecnologia">Línea:</label>
            <select class="custom-select mb-2" id="formSelectLinea" name="linea" required>
              <option selected disabled>Debe de seleccionar una opción</option>
            </select>
            <label for="tecnologia">Categoría:</label>
            <select class="custom-select mb-2" id="formSelectCategoria" name="categoria" required disabled>
              <option selected disabled>Debe de seleccionar una opción</option>
            </select>
            <label for="tecnologia">Tecnología:</label>
            <div class="form-row" id="check-tecnologia">
              <div class="form-group col-12">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="tecnologia" class="custom-control-input" id="categoriaCrear0" required disabled>
                  <label class="custom-control-label" for="categoriaCrear0" value="0">Debe seleccionar una categoria</label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="marca">Referencia</label>
              <input type="text" class="form-control" name="referencia" required>
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
    $permiso = top.validarPermiso('jobs_referencias');
    cerrarCargando();
    if ($permiso == 1) {
      $("#btn-referencia").append(`
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearReferencia"><i class="fas fa-plus"></i> Crear Referencia</button>
      `);
    }

    definirdataTable("#referencia-tabla");

    //Iniciamos los select de línea de productos
    selectTecnologia("selectLineas");

    $("#selectLineas").on("change", function(){
      selectTecnologia("selectCategorias", $(this).val());
      $("#selectTecnologias").val("0");
      $("#selectTecnologias").attr("disabled", true);
    });

    $("#selectCategorias").on("change", function(){
      selectTecnologia("selectTecnologias", $(this).val());
    });

    $("#selectTecnologias").on("change", function(){
      listaReferencias($(this).val());
    });

    //Select del formulario
    selectTecnologia('formSelectLinea');
    $("#formSelectLinea").on("change", function(){
      selectTecnologia('formSelectCategoria', $(this).val());
      $("#check-tecnologia").html(`
        <div class="form-group col-12">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="categoriaCrear0" required disabled>
            <label class="custom-control-label" for="categoriaCrear0" value="0">Debe seleccionar una categoria</label>
          </div>
        </div>
      `); 
    });

    $("#formSelectCategoria").on("change", function(){
      checkBoxTecnologia($(this).val());
    });

    $("#modalCrearReferencia").on('shown.bs.modal', function(e){
      if ($permiso == 1) {
        //Enfocamos el campo
        $("#selectLinea").focus(); 
      }else{
        $("#btn-referencia").empty();
        $("#modalCrearReferencia").modal('hide');
      }
    });

    $("#formCrearReferencia").submit(function(event){
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
                $("#modalCrearReferencia").modal("hide");
                alertify.success(data.msj);
                $("#formCrearReferencia :input[name='referencia']").val(""); 
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


  function selectTecnologia(id, tec = 0){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: "selectTecnologia", tecnologia: tec},
      success: function(data){
        $("#" + id).removeAttr("disabled");
        $("#" + id).empty();
        $("#" + id).append(`
          <option selected disabled value="0">Debe selecciona una opcion</option>
        `);
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#" + id).append(`
              <option value="${data.msj[i].id}">${data.msj[i].nombre}</option>
            `);
          }
        }else{
          alertify.error(data.msj);
        }

      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      }
    });
  }

  function checkBoxTecnologia(tec = 0){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: "selectTecnologia", tecnologia: tec},
      success: function(data){
        $("#check-tecnologia").empty();
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#check-tecnologia").append(`
              <div class="form-group col-6">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="tecnologia[]" class="custom-control-input" id="${data.msj[i].nombre + data.msj[i].id}" value="${data.msj[i].id}" required>
                  <label class="custom-control-label" for="${data.msj[i].nombre + data.msj[i].id}" >${data.msj[i].nombre}</label>
                </div>
              </div>
            `); 
          }
        }else{
          alertify.error(data.msj);
        }
      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      }
    });
  }

  function listaReferencias(tec){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: "listaReferencias", tecnologia: tec, marca: <?php echo($_GET['marca']); ?>},
      success: function(data){
        $('[data-toggle="tooltip"]').tooltip('hide');
        $("#referencia-tabla").dataTable().fnDestroy();
        $("#referencia-tabla-tbody").empty();
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#referencia-tabla-tbody").append(`
              <tr>
                <td>${data.msj[i].referencia}</td>
              </tr>
            `);
          }
        }else{
          alertify.error(data.msj);
        }
        definirdataTable("#referencia-tabla");
        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      }
    });
  }
</script>
</html>