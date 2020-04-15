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
    echo $lib->jqueryForm();
    echo $lib->lightbox();
    echo $lib->intranet();
  ?>
</head>
<body>
  <div class="container mt-5 rounded pt-3 pb-5 border" style="background: rgba(255,255,255,0.6)">
    <h2 class="text-center"><?php echo($_GET['referencia']) ?></h2>
    <hr>
    <div id="btn-pi" class="d-flex justify-content-end mb-3"></div>
    
    <div class="row">
      <div class="col-3">
        <select id="selectPi" class="custom-select d-none"></select>
      </div>
      <div class="col-9 text-right">
        <button id="btnAddArchivos" class="btn btn-primary d-none" type="button" data-toggle="modal" data-target="#modalAgregarDocumento"><i class="fas fa-file-upload"></i> Agregar Documento</button>
      </div>
    </div>
    
    <div id="categorias"></div>
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

  <!-- ==================== Modal Agregar Documento ============================ -->
  <div class="modal fade bd-example-modal-md" id="modalAgregarDocumento" tabindex="-1" role="dialog" aria-labelledby="modalAgregarDocumentoTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold">Agregar Documento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAgregarArchivo" autocomplete="off" enctype="multipart/form-data">
          <input type="hidden" name="accion" value="subirArchivos">
          <input type="hidden" name="idPI" id="idPI" value="0">
          <input type="hidden" name="refPI" id="refPI" value="0">
          <input type="hidden" name="aplicaPI" id="aplicaPI" value="1">
          <input type="hidden" name="idProducto" id="idProducto" value="<?= $_REQUEST['id'] ?>">
          <input type="hidden" name="referencia" id="referencia" value="<?= $_REQUEST['referencia'] ?>">
          <div class="modal-body">
            <div class="form-group col-12">
              <label for="Categoria">Categoria <span class="text-danger">*</span></label>
              <select required class="custom-select" name="categoria" id="categoria" autofocus></select>
            </div>
            <div class="col-12">
              <label for="">Adjuntar Archivo <span class="text-danger">*</span></label>
              <div class="custom-file">
                <input required type="file" disabled class="custom-file-input" id="archivos" name="archivos[]" accept="N/A" multiple>
                <label class="custom-file-label" id="labelArchivo" for="archivo">Seleccionar Archivo</label>
                <small id="archivosExtensionesSmall" class="form-text text-muted">
                  Archivos permitidos: N/A.
                </small>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label for="archivoObservaciones">Observaciones</label>
                <textarea class="form-control" name="archivoObservaciones" id="archivoObservaciones" rows="3"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <input type="submit" class="btn btn-success" value="Agregar">
          </div>
        </form>
        <div class="progress mt-2" style="height: 25px;">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><div class="percent">0%</div></div>
        </div>
      </div>
    </div>
  </div>
  <!-- ======================= Fin Modal Agregar Documento =============== -->

  <!-- Modal Admin PI -->
  <div id="modalAdminPI" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-chalkboard-teacher"></i> Admin PI</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tablaPI" class="mt-3 table table-bordered table-hover table-sm w-100">
            <thead>
              <tr>
                <th class="text-center">PI</th>
                <th class="text-center">Unidades</th>
                <th class="text-center">Fecha Creación</th>
                <th class="text-center">Acciones</th> 
              </tr>
            </thead>
            <tbody id="contenidoPI"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
        </div>
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
    $permiso_categorias = top.validarPermiso('jobs_categorias');
    selectPi();

    $(".archivos").on("click", function(event){
      event.preventDefault();
      //top.$('#modalArchivos').modal("show");
      top.$('#cargando').modal("show");
      $('#modalArchivosTitulo').html("Documentos");
      $("#contenidoArchivos").attr("src", $(this).attr("href"));
      modalArchivos();
    });

    if ($permiso == 1) {
      $("#btn-pi").append(`
        <button class="btn btn-success ml-2" data-toggle="modal" data-target="#modalCrearPI"><i class="fas fa-plus"></i> Crear PI</button>
        <button class="btn btn-success ml-2" data-toggle="modal" data-target="#modalAdminPI"><i class="fas fa-tools"></i> Admin PI</button>
      `);
    }
    if ($permiso_categorias == 1) {
      $("#btn-pi").append(`
        <a class="btn btn-info ml-2 text-white" href="view/producto/categoria_archivos"><i class="fas fa-plus"></i> Categoria Archivos</a>
      `);
    }

    $("#modalAdminPI").on('shown.bs.modal', function(e){
      if ($permiso == 0) {
        $("#btn-pi").empty();
        $("#modalAdminPI").modal('hide');
      }
    });

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
                selectPi();
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

    /* ==================================================================== */
    // Variables de barra de progreso
    var bar = $('.progress-bar');
    var percent = $('.percent');

    $("#formAgregarArchivo").submit(function(event){
      event.preventDefault();
      if ($(this).valid()) {
        $.ajax({
          xhr: function() {
              var xhr = new window.XMLHttpRequest();
              xhr.upload.addEventListener("progress", function(evt) {
                  if (evt.lengthComputable) {
                      var percentComplete = ((evt.loaded / evt.total) * 100);
                      $(".progress-bar").width(percentComplete + '%');
                      $(".progress-bar").html(percentComplete+'%');
                  }
              }, false);
              return xhr;
          },
          url: "acciones",
          type: "POST",
          dataType: "json",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          beforeSend: function(){
            $(".progress-bar").width('0%');
            $(".progress-bar").html('0%');
          },
          success: function(data){
            if (data.success) {
              if ($("#aplicaPI").val() == 0) {
                mostrarDocumentos($("#categoria").val(), $("#documento" + $("#categoria").val()).data("permiso"));
              }else{
                mostrarDocumentos($("#categoria").val(), $("#documento" + $("#categoria").val()).data("permiso"), $("#idPI").val());
              }
              $('#modalAgregarDocumento').modal('hide');
              $('#labelArchivo').text('Seleccionar Archivo');
              $("#categoria").val(0);
              $("#archivosExtensionesSmall").html("Archivos permitidos: N/A.");
              $('#archivos').val('');
              $("#archivos").prop("disabled", true);
              alertify.success("Se han agregado correctamente");
            }else{
              alertify.error(data.msj);
            }
          },
          error: function(){
            alertify.error("No se han enviado datos...");
          },
          complete: function(){
            setTimeout(function(){
              bar.width('0%');
              percent.html('0%');
            }, 1000);
          }
        });
      }
    });
  });

  function selectPi(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {
        accion: "listaPI", 
        fk_referencia: <?php echo($_GET['id']); ?>
      },
      success: function(data){
        if (data.success) {
          let ultimoID = 0;
          $("#selectPi").removeClass("d-none");
          $("#selectPi").empty();
          $("#tablaPI").dataTable().fnDestroy();
          $("#contenidoPI").empty();
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            ultimoID = data.msj[i].id;
            $("#selectPi").append(`
              <option value="${data.msj[i].id}">${data.msj[i].pi}</option>
            `);

            $("#contenidoPI").append(`
              <tr>
                <td><input class="form-control" type="text" id="EditPIPI${data.msj[i].id}" value="${data.msj[i].pi}" disabled></td>
                <td><input class="form-control" type="number" onkeypress="return soloNumeros(event)" id="EditPIUnidades${data.msj[i].id}" value="${data.msj[i].unidades}" disabled></td>
                <td class="text-center">${data.msj[i].fecha_creacion}</td>
                <td class="text-center">
                  <button class="btn btn-success EditBtnEditar" value="0" data-id="${data.msj[i].id}"><i class="fas fa-edit"></i></button>
                  <button class="btn btn-danger" id="BtnEliminarPI${data.msj[i].id}" onClick="eliminarPI(${data.msj[i].id}, '${data.msj[i].pi}')"><i class="fas fa-trash-alt"></i></button>
                </td>
              </tr>
            `);

          }
          //Definimos la tabla en la modal
          definirdataTable("#tablaPI");

          //Enviamos los ultimos datos de la PI
          $('#idPI').val(data.msj[data.msj.cantidad_registros - 1].id);
          $('#refPI').val(data.msj[data.msj.cantidad_registros - 1].pi);
          $("#selectPi").val(ultimoID);
          cargarCategorias(1);

          $("#selectPi").on("change", function(){
            top.$("#cargando").modal("show");
            cargarCategorias(1);
          });

          //Botones de editar en la PI
          $(".EditBtnEditar").on("click", function(){
            let id = $(this).data("id");
            if ($(this).val() == 0) {
              $(this).html(`<i class="far fa-save"></i>`);
              $("#EditPIPI" + id ).attr("disabled", false);
              $("#EditPIUnidades" + id).attr("disabled", false);
              $("#BtnEliminarPI" + id).attr("disabled", true);
              $(this).val(1);
            }else{
              $(this).html(`<i class="fas fa-edit"></i>`);
              $("#EditPIPI" + id ).attr("disabled", true);
              $("#EditPIUnidades" + id).attr("disabled", true);
              $("#BtnEliminarPI" + id).attr("disabled", false);
              editarPI(id);
              $(this).val(0);
            }
          });
        }else{
          $("#selectPi").addClass("d-none");
          $("#selectPi").empty();
          cargarCategorias(0);
        }
      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      }
    });
  }

  function mostrarDocumentos(subCat, permiso = 0, idPI = 0){
    $.ajax({
      type: 'POST',
      url: 'acciones',
      dataType: 'json',
      data: {
        accion: "listaDocumentos", 
        idPI: idPI, 
        idSub: subCat, 
        idPro: <?= $_GET['id'] ?>
      },
      success: function(data){
        $('#documento' + subCat).empty();
        if(data.success == true){
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            let html = '';
            if (data.msj[i].tipo2 == "jpg" || data.msj[i].tipo2 == "jpeg" || data.msj[i].tipo2 == "png") {
              html = `<div class="col-3 col-sm-2 col-md-2 col-lg-1">
                  <a data-toggle="tooltip" title="${"V" + i + " " + data.msj[i].observaciones}" href="<?php echo($ruta_raiz); ?>${data.msj[i].ruta}" data-lightbox="galeria${subCat}" data-title="${"V" + i + " " + data.msj[i].observaciones}"><img class="img-thumbnail" src="../../${data.msj[i].ruta}"></a>
                  <div class="text-center iconos-sig">
                    <a href="<?php echo($ruta_raiz); ?>${data.msj[i].ruta}" download="${data.msj[i].nombre_sub + "-" + i + "." + data.msj[i].tipo2}"><i class="fas fa-download"></i></a>`;
              
              if (permiso == 1) {
                html = html + `<hr>
                              <button class="btn btn-link text-danger" onClick="eliminarArchivo(${data.msj[i].id}, ${subCat}, ${permiso}, ${idPI})">
                                <i class="fas fa-trash-alt"></i>
                              </button>`;
              }

              html = html +  `</div>
                            </div>`;

                            
            }else{
              html = `<div class="col-3 col-sm-2 col-md-2 col-lg-1 text-center mb-3 iconos-sig">
                            <div class="mb-1" data-toggle="tooltip" title="${"V" + i + " " + data.msj[i].observaciones}">`;
              if (data.msj[i].tipo2 == "rar" || data.msj[i].tipo2 == "zip") {
                html = html + `<a href="<?php echo($ruta_raiz); ?>${data.msj[i].ruta}" download="${data.msj[i].nombre_sub + "-" + i + "." + data.msj[i].tipo2}">
                                <i class="${data.msj[i].icono} fa-3x"></i>
                              </a>`;
                
              }else if(data.msj[i].tipo2 == "pdf"){
                html = html + `<a class="text-decoration-none" onClick="modalDocumentos('<?php echo($ruta_raiz); ?>${data.msj[i].ruta}', 'V${i}')">
                                <i class="${data.msj[i].icono} fa-3x"></i>
                              </a>`;
              }else{
                html = html + `
                            <a class="text-decoration-none" onClick="modalDocumentos('https://view.officeapps.live.com/op/embed.aspx?src=http://consumerelectronicsgroup.com/intranet/${data.msj[i].ruta}', 'V${i}')">
                              <i class="${data.msj[i].icono} fa-3x"></i>
                            </a>`;
              }

              html = html + `</div>
                            <a href="<?php echo($ruta_raiz); ?>${data.msj[i].ruta}" download="${data.msj[i].nombre_sub + "-" + i + "." + data.msj[i].tipo2}"><i class="fas fa-download"></i></a>`;

              if (permiso == 1) {
                html = html + `<hr>
                              <button class="btn btn-link text-danger" onClick="eliminarArchivo(${data.msj[i].id}, ${subCat}, ${permiso}, ${idPI})">
                                <i class="fas fa-trash-alt"></i>
                              </button>`;
              }

              html = html + `</div>`;
            }
            $('#documento' + subCat).append(html);
          }
        }
        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(data){
        alertify.error("Error al cargar documentos");
      }
    });
  }

  function cargarCategorias(aplicaPI){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {
        accion: 'listaCategorias', 
        pi: aplicaPI
      },
      success: function(data){
        let cont_permisos = 0;  
        $("#categorias").empty();
        $("#categoria").empty();
        $("#categoria").append(`
          <option value="0" data-extensiones="" disabled selected>Seleccione una opción</option>
        `);
        
        if (data.success) {  
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            let permiso = top.validarPermiso(data.msj[i].fk_permiso);
            //Solo mostramos las que se permite mirar a todas las personas
            if (data.msj[i].publico == 1) {  
              $("#categorias").append(`
                <hr>
                <h5 class="mb-4">${capitalize(data.msj[i].nombre)}</h5>
                <div class="row">
                  <div class="col-12">
                    <div class="row" id='documento${data.msj[i].id}' data-permiso="${permiso}"></div>
                  </div>
                </div>
              `);
            }

            //Validamos el permiso y llenamos el select y colocamos un catador para contar si tiene algun permiso de esa forma habilitar el boton de agregar archivos, pero adicional validamos si la categoria no esta publica para poder que la visualice
            if (permiso == 1) {
              //Si estaba oculto y tiene el permiso para agregar archivos lo puede ver
              if (data.msj[i].publico == 0) {
                $("#categorias").append(`
                  <hr>
                  <h5>${capitalize(data.msj[i].nombre)}</h5>
                  <div class="row">
                    <div class="col-12">
                      <div class="row" id='documento${data.msj[i].id}' data-permiso="${permiso}"></div>
                    </div>
                  </div>
                `);
              }

              //Debe de tener alguna extesion agregada para que aparezca en la lista
              if (data.msj[i].extensiones != "") {  
                $("#categoria").append(`
                  <option data-aplicapi="${data.msj[i].aplica_pi}" data-extensiones="${data.msj[i].extensiones}" value="${data.msj[i].id}">${data.msj[i].nombre}</option>
                `);
              }
              cont_permisos++;
            }

            if (data.msj[i].aplica_pi == 0) {
              mostrarDocumentos(data.msj[i].id, permiso);
            }else{
              mostrarDocumentos(data.msj[i].id, permiso, $("#selectPi").val());
            }
          }

          if (cont_permisos > 0) {
            $("#btnAddArchivos").removeClass("d-none");
          }

          $("#categoria").on("change", function(){
            let extensiones = $("#categoria option[value='" + $(this).val() + "']").data("extensiones");
            let aplicaPI = $("#categoria option[value='" + $(this).val() + "']").data("aplicapi");
            if (extensiones != "") {
              $("#aplicaPI").val(aplicaPI);
              $("#archivos").attr("accept", extensiones);
              $("#archivosExtensionesSmall").html("Archivos permitidos: " + extensiones);
              $("#archivos").prop("disabled", false);
            }else{
              $("#archivos").prop("disabled", true);
              $("#archivosExtensionesSmall").html("Archivos permitidos: N/A.");
              $("#modalAgregarDocumento").modal("hide");
              console.log("Deben de definirle una extension a esta cargoria.");
            }
          });
        }else{
          alertify.error(data.msj);
        }
      },
      error: function(){
        alertify.error("Error al enviar datos");
      },
      complete: function(){
        cerrarCargando();
      }
    });
  }

  function eliminarArchivo(id, subCat, permiso, idPI){
    alertify.confirm(
      '¿Estas seguro?', 
      'Deseas eliminar este archivo.', 
    function(){ 
      $.ajax({
        url: 'acciones',
        type: 'POST',
        dataType: 'json',
        data: {
          accion: "eliminarArchivo", 
          archivo: id
        },
        success: function(data){
          if (data == 1) {
            mostrarDocumentos(subCat, permiso, idPI);
            alertify.warning("Se ha eliminado el archivo");
          }else{
            alertify.error("Error al eliminar el archivo");
          }
        },
        error: function(){
          alertify.error('No se han enviado los datos.');
        }
      });
    }, 
    function(){})
    .set('labels', {
      ok:'<i class="far fa-trash-alt"></i> Si', 
      cancel:'<i class="fa fa-times"></i> No'
    });
  }

  function eliminarPI(id, pi){
    alertify.confirm(
      '¿Estas seguro?', 
      'Deseas eliminar este archivo.', 
    function(){ 
      $.ajax({
        url: 'acciones',
        type: 'POST',
        dataType: 'json',
        data: {
          accion: "eliminarPI", 
          id: id,
          pi: pi
        },
        success: function(data){
          if (data == 1) {
            selectPi();
            alertify.warning("Se ha eliminado la PI " + pi);
          }else{
            alertify.error("Error al eliminar la PI" + pi);
          }
        },
        error: function(){
          alertify.error('No se han enviado los datos.');
        }
      });
    }, 
    function(){})
    .set('labels', {
      ok:'<i class="far fa-trash-alt"></i> Si', 
      cancel:'<i class="fa fa-times"></i> No'
    });
  }

  function modalDocumentos(url, nombreArchivo){
    top.$('#cargando').modal("show");
    $('#modalArchivosTitulo').html("Documento " + nombreArchivo);
    $("#contenidoArchivos").attr("src", url);
    modalArchivos();
  }

  function editarPI(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {
        accion: "editarPI", 
        id: id,
        pi: $("#EditPIPI" + id ).val(),
        unidades: $("#EditPIUnidades" + id).val(),
        referencia: <?php echo($_GET['id']); ?>
      },
      success: function(data){
        if (data.success == true) {
          if (id == $("#selectPi").val()) {
            selectPi();
          }
          alertify.success(data.msj);
        }else{
          alertify.error(data.msj);
        }
      },
      error: function(){
        alertify.error('No se han enviado los datos.');
      }
    });
  }

  function capitalize(word) {
    return word[0].toUpperCase() + word.slice(1);
  }
</script>
</html>