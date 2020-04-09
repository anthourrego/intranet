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
    echo $lib->bootstrapTreeView();
    echo $lib->intranet();
  ?>
</head>
<body>
  <div class="container mt-5 rounded pt-3 pb-5 border" style="background: rgba(255,255,255,0.6)">
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <input type="input" class="form-control" id="input-search" placeholder="Buscar una tecnología" value="" autocomplete="off">
        </div>
        <div id="treeview1"></div>
      </div>
      <div class="col-6">
        <div class="row d-flex justify-content-around">
          <button class="btn btn-primary mt-2 mt-lg-0" id="btnCrearTecnologia" data-toggle="modal" data-target="#crearTecnologiaModal"><i class="fas fa-microchip"></i> Agregar Tecnólogia</button>
          <button class="btn btn-success mt-2 mt-lg-0" id="btnEditar"  disabled><i class="fas fa-plus"></i> Editar</button>
          <button class="btn btn-danger mt-2 mt-lg-0" id="btnEliminar" value="0" data-nombre="0" disabled><i class="far fa-trash-alt"></i> Eliminar</button>
        </div>
        <hr>
        <form id="formEditar" class="mt-4">
          <input type="hidden" name="accion" value="editarTecnologia">
          <input type="hidden" name="idTecnologia" value="0">
          <div class="form-group row">
            <label class="col-12 col-md-4 align-self-center" for="tecPadre">Tecnólogia Padre:</label>
            <div class="col-12 col-md-8">
              <select class="custom-select" name="tecPadre" id="selectTecPadre" disabled required>
                <option value="0" selected disabled>Raíz</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-12 col-md-4 align-self-center" for="nombre">Nombre:</label>
            <div class="col-12 col-md-8">
              <input class="form-control" type="text" name="nombre" placeholder="N/A" required autocomplete="off" disabled>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-12 col-md-4 align-self-center" for="fechaCreacion">Fecha creación:</label>
            <div class="col-12 col-md-8">
              <input class="form-control" type="text" name="fechaCreacion" placeholder="N/A" disabled>
            </div>
          </div>
          <div id="compatible" class="form-group d-none">
            <label for="compatible">Compatible:</label>
            <div id="datos-check" class="row"></div>
          </div>
          <div class="text-right">
            <button type="submit" class="btn btn-success" name="btnGuardar" disabled><i class="far fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal de Crear Tecnólogia Padre -->
  <div class="modal fade" id="crearTecnologiaModal" tabindex="-1" role="dialog" aria-labelledby="crearTecnologiaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="crearTecnologiaModalLabel"><i class="fas fa-microchip"></i> Agregar Tecnólogia</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearTecnologia" autocomplete="off">
          <input type="hidden" name="accion" value="crearTecnologia">
          <div class="modal-body">
            <div class="form-group">
              <label for="fk_tecnologia">Tecnólogia</label>
              <select name="fk_tecnologia" class="custom-select">
              </select>
            </div>
            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input class="form-control" type="text" name="nombre" autocomplete="off" required>
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
            <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Crear</button>
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
    if (top.validarPermiso('jobs_tecnologias') != 1) {
      window.location.href = "../../marcas.php";
    }
    cargarArbol();
    cargarSelect();
    //Formulario de crear padre
    $("#formCrearTecnologia").submit(function(e){
      e.preventDefault();
      if($(this).valid()){
        $.ajax({
          type: "POST",
          dataType: "json",
          url: "acciones",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data.success == true) {
              alertify.success(data.msj);
              $("#crearTecnologiaModal").modal("hide");
              $("#formCrearTecnologia :input[name='fk_tecnologia']").val(0);
              $("#formCrearTecnologia :input[name='nombre']").val();
              cargarArbol();
            } else {
              alertify.error(data.msj);
            }
          },
          error: function(data){
            alertify.error("No se han enviado datos...");
          }
        });
      }
    });

    //Formulario editar tecnólogia
    $("#formEditar").submit(function(e){
      e.preventDefault();
      if($(this).valid()){
        $.ajax({
          type: "POST",
          dataType: "json",
          url: "acciones",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data.success == true) {
              //Tomados el id de la tecnólogia seleccionada actualmente
              let idSelect = $("#formEditar :input[name='tecPadre']").val();

              //Se cargan todos los datos datos del arbol y del select
              cargarArbol();
              cargarSelect(idSelect);
              
              //Volvemos a deshabilitar los campos
              $("#formEditar :input[name='idTecnologia']").attr("disabled", true);
              $("#formEditar :input[name='tecPadre']").attr("disabled", true);
              $("#formEditar :input[name='nombre']").attr("disabled", true);
              $("#formEditar :input[name='btnGuardar']").attr("disabled", true);
              $("#formEditar :input[name='compatibilidad[]']").attr("disabled", true);
              alertify.success(data.msj);
            } else {
              alertify.error(data.msj);
            }
          },
          error: function(data){
            alertify.error("No se han enviado datos...");
          }
        });
      }
    });


    //Botón de editar tecnólogia
    $("#btnEditar").on("click", function(){
      $("#formEditar :input[name='idTecnologia']").attr("disabled", false);
      $("#formEditar :input[name='tecPadre']").attr("disabled", false);
      $("#formEditar :input[name='nombre']").attr("disabled", false);
      $("#formEditar :input[name='compatibilidad[]']").attr("disabled", false);
      $("#formEditar :input[name='btnGuardar']").attr("disabled", false);
    });

    //Acción al click botón eliminar
    $("#btnEliminar").on("click", function(){
      if ($(this).val() != 0) {
        alertify.confirm(
                  '¿Estas seguro?', 
                  'Deseas eliminar la tecnólogia <b>' + $(this).data("nombre") + '</b>', 
                function(){ 
                  eliminarTecnologia($("#btnEliminar").val() , $("#btnEliminar").data("nombre"))
                }, 
                function(){})
                .set('labels', {
                  ok:'<i class="far fa-trash-alt"></i> Si', 
                  cancel:'<i class="fa fa-times"></i> No'
                });
      }else{
        alerify.error("No ha seleccionado una tecnólogia.");
      }
    });

  });

  function cargarSelect(idSelect = 0){
    $.ajax({
      url: "acciones",
      type: "POST",
      dataType: "json",
      data: {
        accion: "listaTecnologia"
      },
      success: function(data){
        if (data.success) {
          $("#formEditar :input[name='tecPadre'], #formCrearTecnologia :input[name='fk_tecnologia']").empty();

          $("#formEditar :input[name='tecPadre'], #formCrearTecnologia :input[name='fk_tecnologia']").append(`
            <option value="0" selected>Raíz</option>
          `);
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#formEditar :input[name='tecPadre'], #formCrearTecnologia :input[name='fk_tecnologia']").append(`
              <option value="${data.msj[i].id}">${data.msj[i].nombre}</option>
            `); 
          }

          if (idSelect != 0) {
            $("#formEditar :input[name='tecPadre']").val(idSelect);
          }

        } else {
          alertify.error(data.msj);
        }
      },
      error: function(data){
        alertify.error("No se han encontrado datos...");
      }
    });
  }

  function cargarArbol(){
    $.ajax({
      url: "acciones",
      type: "POST",
      dataType: "json",
      data: {
        accion: "arbolTecnologias"
      },
      success: function(data){

        var initSelectableTree = function() {
          return $('#treeview1').treeview({
            levels: 1,
            data: data,
            showTags: true,
            onNodeSelected: function(event, node) {
              console.log(node);
              $("#compatible").addClass("d-none");

              //Motramos todos los campos del selece en editar si hemos ocultado alguno
              $("#formEditar :input[name='tecPadre'] option").show();
              
              //Enviamos id al botón eliminar
              $("#btnEliminar").val(node.idTecnologia);
              $("#btnEliminar").data("nombre", node.text);

              //Volvemos a deshabilitar los campos
              $("#formEditar :input[name='idTecnologia']").attr("disabled", true);
              $("#formEditar :input[name='tecPadre']").attr("disabled", true);
              $("#formEditar :input[name='nombre']").attr("disabled", true);
              $("#formEditar :input[name='btnGuardar']").attr("disabled", true);

              //Datos para editar
              $("#formEditar :input[name='idTecnologia']").val(node.idTecnologia);
              $("#formEditar :input[name='tecPadre']").val(node.fk_tecnologia);
              $("#formEditar :input[name='nombre']").val(node.text);
              $("#formEditar :input[name='fechaCreacion']").val(node.fechaCreacion);

              if (node.nivel == 3) {
                $("#compatible").removeClass("d-none");
                checkCompatible(node.fk_tecnologia, node.idTecnologia);
              }

              //Ocultamos la tecnólogia seleccionada en el select
              $("#formEditar :input[name='tecPadre'] option[value='" + node.idTecnologia + "']").hide();

              //Datos para crear tecnólogia
              $("#formCrearTecnologia :input[name='fk_tecnologia']").val(node.fk_tecnologia);
              $("#formCrearHijo :input[name='nombre']").val(node.text);

              $("#btnCrearHijo").prop("disabled", false);
              $("#btnEditar").prop("disabled", false);
              $("#btnEliminar").prop("disabled", false);

            }
          });
        };

        var $selectableTree = initSelectableTree();

        var findSelectableNodes = function() {
          return $selectableTree.treeview('search', [ $('#input-search').val(), { ignoreCase: false, exactMatch: false } ])
        };
        var selectableNodes = findSelectableNodes();

        $('#input-search').on('keyup', function (e) {
          $('#treeview1').treeview('collapseAll', { silent:true });
          selectableNodes = findSelectableNodes();
        });

      },
      error: function(){
        alertify.error("No se han encontrado datos...");
      },
      complete: function(){
        cerrarCargando();
      }
    });
  }

  function eliminarTecnologia(idTecnologia, nombre){
    $.ajax({
      url: "acciones",
      type: "POST",
      dataType: "json",
      data: {
        accion: "eliminarTecnologia",
        idTec: idTecnologia,
        nombre: nombre
      },
      success: function(data){
        if (data == 1) {
          cargarArbol();
          cargarSelect();
          alertify.warning("Se ha eliminado correctamente");
        }else{
          alertify.error("No ha podido eliminar la tecnólogia <b>" + nombre + "</b>")
        }
      },
      error: function(data){
        alertify.error("No se han encontrado datos...");
      }
    });
  }

  function checkCompatible(idTecnologia, idTecActual){
    $.ajax({
      url: "acciones",
      type: "POST",
      dataType: "json",
      data: {
        accion: "tecnologiaCheckbox",
        idTec: idTecnologia,
        idTecActual: idTecActual
      },
      success: function(data){
        if (data.success) {
          console.log(data.msj);
          $('#datos-check').empty();
          for (let i = 0; i < data.msj['cantidad_registros']; i++) {
            let check = '';
            if(data.msj[i].id != idTecActual){
              if (data.msj[i].check == 1) {
                check = 'checked'
              }
              console.log(check);
              $('#datos-check').append(`
                <div class="col-12 col-lg-6">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="compatibilidad[]" class="custom-control-input" value="${data.msj[i].id}" id="compatibilidad${data.msj[i].id}" ${check} disabled>
                    <label class="custom-control-label" for="compatibilidad${data.msj[i].id}">${data.msj[i].nombre}</label>
                  </div>
                </div>
              `); 
            }
          }
        }else{
          alertify.error(data.msj);
        }
      },
      error: function(data){
        alertify.error("No se han encontrado datos...");
      }
    });
  }
</script>
</html>