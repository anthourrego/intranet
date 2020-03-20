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
    <div id="btn-pi" class="d-flex justify-content-end m-3"></div>
    <div id="btns-pi" class="d-flex justify-content-center m-3"></div>
    <div id="documentos" class="d-none">
      <div class="text-center text-md-right">
        <button class="btn btn-primary mt-2 mt-md-0" type="button" data-toggle="modal" data-target="#modalAgregarDocumento">Agregar Documento</button>
        <button type="button" class="btn btn-primary mt-2 mt-md-0" data-toggle="modal" data-target="#agregarImagen">Agregar Fotos</button>
      </div>
      <hr>
      <div class="row">
        <div class="col-12 col-md-3 border-right">
          <h5 class="text-center text-md-right">Ficha técnica comercial</h5>
        </div>
        <div class="col-12 col-md-9">
          <div class="row" id='documento1'></div>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-12 col-md-3 border-right">
          <h5 class="text-center text-md-right">Fotos de alta resolución</h5>
        </div>
        <div class="col-12 col-md-9">
          <div class="row" id="mostrarImagenes"></div>
        </div>
      </div>
    </div>
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

  <!-- Agregar Imagenes -->
  <div class="modal fade" id="agregarImagen" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold">Agregar Imagenes</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAgregarImagen" method="post" autocomplete="off" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="custom-file">
              <input type="hidden" name="accion" value="subirImagenes">
              <input type="hidden" name="idPIImagen" id="idPIImagen">
              <input type="hidden" name="PIImagen" id="PIImagen">
              <input type="hidden" name="idRefImagen" id="idRefImagen" value="<?= $_REQUEST['id'] ?>">
              <input type="hidden" name="referenciaImagen" id="referenciaImagen" value="<?= $_REQUEST['referencia'] ?>">
              <div class="form-group">
                <input required type="file" class="custom-file-input" id="imagenes" name="imagenes[]" multiple="true" accept=".jpg, .jpeg, .png">
                <label class="custom-file-label" id="labelImagen" for="archivo">Seleccionar Archivo</label>
                <small class="form-text text-muted">
                  Solo se permiten archivos jpg, jpeg, png.
                </small>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
            <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Subir</button>
          </div>
        </form>
        <div class="progress mt-2" style="height: 25px;">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><div class="percent">50%</div></div>
        </div>
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
        <form class="was-validated" novalidate id="formAgregarArchivo" method="post" autocomplete="off" enctype="multipart/form-data" action="acciones">
          <div class="modal-body">
            <input type="hidden" name="accion" value="subirArchivos">
            <input type="hidden" name="idPI" id="idPI">
            <input type="hidden" name="refPI" id="refPI">
            <input type="hidden" name="idProducto" id="idProducto" value="<?= $_REQUEST['id'] ?>">
            <input type="hidden" name="referencia" id="referencia" value="<?= $_REQUEST['referencia'] ?>">
            <div class="">
              <div class="form-group col-12">
                <label for="Categoria">Categoria</label>
                <select required class="custom-select" name="categoria" id="categoria" autofocus>
                  <option value="0" disabled selected>Debe selecciona una opcion</option>
                  <option value="1">Ficha técnica</option>
                </select>
              </div>
            </div>
            <div class="col-12">
              <label for="">Adjuntar Archivo</label>
              <div class="custom-file">
                <input required type="file" class="custom-file-input" id="archivo" name="archivo[]" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation, application/pdf, application/x-zip-compressed, .rar" multiple>
                <label class="custom-file-label" id="labelArchivo" for="archivo">Seleccionar Archivo</label>
                <small class="form-text text-muted">
                  Archivos permitidos: zip, rar, pfd, doc, docx, xls, xlsx, ppt, pptx.
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

    /* ==================================================================== */
      // Variables de barra de progreso
      var bar = $('.progress-bar');
      var percent = $('.percent');
      //var status = $('#status');
      /*====================== Formulario Agregar Imagenes ================== */
      $("#formAgregarImagen").submit(function(event){
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
            dataType: "html",
            cache: false,
            contentType: false,
            processData: false,
            data: new FormData(this),
            beforeSend: function(){
              $(".progress-bar").width('0%');
              $(".progress-bar").html('0%');
            },
            success: function(data){
              if(data == "OK"){
                alertify.success("Se han agregado correctamente");
                cargarImagenes();
                console.log();
                //$("#formAgregarImagen").find('input').removeClass('is-valid');
                //$("#formAgregarImagen").find('input').removeClass('is-invalid');
                $('#agregarImagen').modal('hide');
                $('#labelImagen').text('Seleccionar Archivo');
                $('#imagenes').val('');
              }else{
                alertify.error("No se ha subido el archivo.");
              }
              /*if (data.success == true) {
                $("#modalCrearPI").modal("hide");
                alertify.success(data.msj);
                $("#formCrearPI :input[name='nombre']").val(""); 
                $("#formCrearPI :input[name='unidades_pi']").val(""); 
              }else{
                alertify.error(data.msj);
              }*/
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
      /* ==================================================================== */
      /* ======================= Formulario de Archivos ===================== */
      $('#subirArchivo').click(function(){
        if ((textoBlanco($('#archivo')) > 0) && ($("#categoria").val() > 0)) {
          $('#formAgregarArchivo').submit()
        }else{
          if ($("#categoria").val() <= 0) {
            alertify.warning('Debes seleccionar una categoría');
            $("#categoria").focus();
          }else if ((textoBlanco($('#archivo')) > 0)) {
            alertify.warning('Debes adjuntar un documento o archivo');
            $('#archivo').focus();
          }else{
            alertify.warning('Error en el formulario');
          }
        }
      });

      $('#formAgregarArchivo').ajaxForm({
        beforeSend: function() {
          status.empty();
          var percentVal = '0%';
          bar.width(percentVal)
          percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
          var percentVal = percentComplete + '%';
          bar.width(percentVal)
          percent.html(percentVal);
          //console.log(percentVal, position, total);
        },
        success: function() {
          var percentVal = '100%';
          bar.width(percentVal)
          percent.html(percentVal);
        },
        complete: function(xhr) {
          if (xhr.responseText === 'OK') {
            alertify.success("Se han agregado correctamente");
            $('#modalAgregarDocumento').modal('hide');
            $('#labelArchivo').text('Seleccionar Archivo');
            mostrarDocumentos(idPIA, $("#categoria").val());
            $("#categoria").val();
            $('#archivo').val('');
            bar.width('0%');
            parcent.html('0%');
          }else{
            alertify.error(xhr.responseText);
            bar.width('0%');
            parcent.html('0%');
          }
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
        $('[data-toggle="tooltip"]').tooltip('hide');
        $("#btns-pi").empty();
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#btns-pi").append(`
              <button class="btn btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="${data.msj[i].unidades} unidades" onClick="mostrarContenido(${data.msj[i].id}, '${data.msj[i].pi}')">${data.msj[i].pi}</button>
            `);
          }
        } else {
          alertify.error(data.msj);
        }
        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      }
    });
  }

  function mostrarDocumentos(id, subCat){
    $.ajax({
      type: 'POST',
      url: 'acciones',
      data: {accion: "listaDocumentos", idPI: id, idSub: subCat, idPro: <?= $_GET['id'] ?>},
      success: function(result){
        $('#documento' + subCat).empty(),
        $('#documento' + subCat).append(result),
        $('[data-toggle="tooltip"]').tooltip()
      },
      error: function(result){
        alert("Error al cargar documentos")
      }
    });
  }

  function mostrarContenido(id, pi){
    //Cargamos la imagenes según el lote seleccionado
    cargarImagenes();

    //Mostramos los documentos que estan por cada categoria
    mostrarDocumentos(id,1);
    
    //Enviamos los id al formulario de imagenes
    $('#idPIImagen').val(id);
    $('#PIImagen').val(pi);
    
    //Enviamos el id y el lote al formulario de archivos
    $('#idPI').val(id);
    $('#refPI').val(pi);

    //Mostramos el contenido de los productos
    $('#documentos').removeClass("d-none");
  }

  function cargarImagenes(){
    $('#mostrarImagenes').empty();
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'mostrarImagenes', idProducto: <?= $_GET['id'] ?>},
      success: function(data){
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            var img = $('');
            $('#mostrarImagenes').append(`<div class="col-6 col-md-4 col-lg-3 mt-2 mb-2">
                                            <a class="example-image-link" href="../../${data.msj[i].ruta}" data-lightbox="example-set" data-title=""><img class="img-thumbnail" src="../../${data.msj[i].ruta}"></a>
                                          </div>`);
          }
        }
      },
      error: function(){
        alertify.error("Error al enviar datos");
      }
    });
  }
</script>
</html>