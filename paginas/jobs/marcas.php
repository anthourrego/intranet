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
    <div class="row">
      <div class="col md-2">
        <div id="btn-permisos" class="m-3"></div>
      </div>
      <div class="col md-3 offset-md-7">
        <div id="btn-marcas" class="d-flex justify-content-around m-3"></div>
      </div>
    </div>
  
    
    <table class="table table-hover mt-4 w-100" id="marcas-tabla">
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

  <div class="modal fade" id="modalEditarMarca" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Marca</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditarMarca">
          <input type="hidden" name="idMarca" value="">
          <input type="hidden" name="accion" value="editarMarca" required>
          <div class="modal-body">              
            <div class="form-group">
              <label for="marca">Marca</label>
              <input type="text" class="form-control" name="nombre" autofocus required>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
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
    
    $permiso = top.validarPermiso('jobs_marcas');
    $btn_permiso = top.validarPermiso('jobs_gestion_permisos');
    //Cargamos la tabla de marcas
    cargarMarcas();

    if ($permiso == 1) {
      $("#btn-marcas").append(`
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearMarca"><i class="fas fa-plus"></i> Crear Marca</button>
      `);
    }

    if ($btn_permiso == 1) {
      $("#btn-permisos").append(`
        <a class="btn btn-info" href="configuracion/user"><i class="fas fa-plus"></i> Permisos</a>
      `);
    }

    $("#modalCrearMarca").on('shown.bs.modal', function(e){
      if ($permiso == 1) {
        //Enfocamos el campo
        $("#formCrearMarca :input[name='nombre']").focus(); 
      }else{
        $("#btn-marcas").empty();
        $("#modalCrearMarca").modal('hide');
      }
    });

    $("#formCrearMarca").submit(function(event){
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
                $("#modalCrearMarca").modal("hide");
                alertify.success(data.msj);
                $("#formCrearMarca :input[name='nombre']").val(""); 
                cargarMarcas();
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

    $("#modalEditarMarca").on('shown.bs.modal', function(e){
      if ($permiso == 1) {
        //Enfocamos el campo
        $("#formEditarMarca :input[name='nombre']").focus(); 
      }else{
        $("#btn-marcas").empty();
        $("#modalEditarMarca").modal('hide');
      }
    });

    $("#formEditarMarca").submit(function(event){
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
                $("#modalEditarMarca").modal("hide");
                alertify.success(data.msj);
                cargarMarcas();
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

  function eliminarMarca(id, nombre){
    $.ajax({
      url: 'acciones.php',
      type: 'POST',
      dataType: 'json',
      data: {accion: "eliminarMarca", idMarca: id, nombreMarca: nombre},
      success: function(data){
        if (data == 1) {
          alertify.success("Se ha aliminado la marca <b>" + nombre + "</b>");
          cargarMarcas();
        }else{
          alertify.error("No se ha podido eliminar la marca <b>" + nombre + "</b>")
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


  function cargarMarcas(){
    $.ajax({
      url: 'acciones.php',
      type: 'POST',
      dataType: 'json',
      data: {accion: "ListaMarcas"},
      success: function(data){
        $('[data-toggle="tooltip"]').tooltip('hide');
        $("#marcas-tabla").dataTable().fnDestroy();
        $("#marcas-tabla-tbody").empty();
        if (data.success == true) {
          $('#marcas-tabla-thead').empty();
          if($permiso == 1){
            $('#marcas-tabla-thead').append(`<tr>
                                              <th>Marca</th>
                                              <th>Acciones</th>
                                            </tr>`);
            for (let i = 0; i < data.msj.cantidad_registros; i++) {
              $("#marcas-tabla-tbody").append(`
                <tr>
                  <td>${data.msj[i].nombre}</td>
                  <td class="text-center">
                    <button class='btn btn-primary' data-toggle="tooltip" data-placement="top" title="Editar" onclick="modalEditarMarca(${data.msj[i].id} , '${data.msj[i].nombre}')"><i class="fas fa-edit"></i></button>
                    <button class='btn btn-danger' data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="eliminarMarca(${data.msj[i].id} , '${data.msj[i].nombre}')"><i class="far fa-trash-alt"></i></button>
                    <a class="btn btn-success" href="referencias?marca=${data.msj[i].id}&nombre=${data.msj[i].nombre}" data-toggle="tooltip" data-placement="top" title="Ingresar"><i class="fas fa-sign-in-alt"></i></a>
                  </td>
                </tr>
              `);
            }
          }else{
            $('#marcas-tabla-thead').append(`<tr>
                                              <th>Marca</th>
                                            </tr>`);
            for (let i = 0; i < data.msj.cantidad_registros; i++) {
              $("#marcas-tabla-tbody").append(`
                <tr onClick="window.location.href='referencias?marca=${data.msj[i].id}&nombre=${data.msj[i].nombre}';">
                  <td>${data.msj[i].nombre}</td>
                </tr>
              `);
            }
          }
          
        }
        definirdataTable("#marcas-tabla");
        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error('No se han encontrado datos.');
      },
      complete: function(){
        cerrarCargando();
      }
    });
  }

  function modalEditarMarca(id, nombre){
    $("#formEditarMarca :input[name='idMarca']").val(id);
    $("#formEditarMarca :input[name='nombre']").val(nombre);
    $("#modalEditarMarca").modal("show");
  }
</script>
</html>