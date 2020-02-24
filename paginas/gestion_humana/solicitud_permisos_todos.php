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
	<title>Consumer Electronics Group S.A.S</title>
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
	<div class="container mt-5 rounded pt-3 pb-5" style="background: rgba(255,255,255,0.6)">
    <h5 class="text-center">Autorización de Permisos TH</h5>
    <table id="tabla" class="table table-bordered table-hover table-striped table-sm mt-5 ">
      <thead>
        <tr class="text-center">
          <th>Nombre</th>
          <th>Permisos pendientes</th>
        </tr>
      </thead>
      <tbody id="contenido"></tbody>
    </table>
  </div>

  <div class="modal fade" tabindex="-2" style="z-index: 1600;" id="modal-observaciones" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Observaciones</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="modal-observaciones-body"></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalPermisosUsuario" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloModalPermisoUsuario">Usuarios</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="botones-usuario" class="d-flex justify-content-between mb-3"></div>
          <table id="tablaXUsuario" class="table table-bordered table-striped table-hover table-sm">
            <thead>
              <tr class="text-center">
                <th>Fecha Creación</th>
                <th>Motivo</th>
                <th>Reposicion</th>
                <th>Fecha Inicio</th>
                <th>Hora Inicio</th>
                <th>Fecha Fin</th>
                <th>Hora Fin</th>
                <th>Observaciones</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="contenidoTablaPermisoUsuario"></tbody>
          </table>

          <table id="tablaXUsuario1" class="table table-bordered table-hover table-striped table-sm table-light">
            <thead class="text-center">
              <tr>
                <th>Fecha Creación</th>
                <th>Motivo</th>
                <th>Reposicion</th>
                <th>Fecha Inicio</th>
                <th>Hora Inicio</th>
                <th>Fecha Fin</th>
                <th>Hora Fin</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody id="contenidoTablaPermisoUsuario1"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
    cargarTablaUsuarios();
    cerrarCargando();

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "solicitud_permisos_todos"},
      success: function(data){
        if (data.length == 0) {
          window.location.href = "index.php";
        }
      },
      error: function(){
        window.location.href = "index.php";
      }
    });
  });


  function anularPermiso(id){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'cambioEstadoPermiso', idPermiso: id, idEstado: 2},
      success: function(data){
        if (data == "Ok") {
          alertify.success("Se ha anulado el permiso.");
          tablaUsuario();
        }else{
          alertify.error(data);
        }
      },
      error: function(){
        alertify.error("Error al anular el permiso."); 
      }
    });
  }

  function cargarTablaUsuarios(){
    $.ajax({
      type: "POST",
      url: "<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php",
      data: {accion: "listaUsuarioTodos"},
      success: function(data){
        if (data != "No") {
          $("#personalACargo").removeClass("invisible");
          $("#contenido").empty();
          $("#contenido").html(data);
          // =======================  Data tables ==================================
          definirdataTable("#tabla");
        }
      },
      error: function(){
        alertify.error("No se ha podido traer la lista");
      }
    });
  }

  function tablaPermisoUsuario(id, estado, lider){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaPermisosUsuario1', idUsu: id, idEstado: estado, lider: lider},
      success: function(data){
        $("#tablaXUsuario1").hide();
        $("#tablaXUsuario1").dataTable().fnDestroy();
        
        $("#tablaXUsuario").show();        
        $("#tablaXUsuario").dataTable().fnDestroy();
        $("#contenidoTablaPermisoUsuario").empty();
        $("#contenidoTablaPermisoUsuario").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaXUsuario");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("Error al cargar.");
      }
    });
  }

  function tablaPermisoUsuario1(id, estado){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaPermisosUsuario1', idUsu: id, idEstado: estado},
      success: function(data){
        $("#tablaXUsuario").hide();
        $("#tablaXUsuario").dataTable().fnDestroy();
        
        $("#tablaXUsuario1").show();        
        $("#tablaXUsuario1").dataTable().fnDestroy();
        $("#contenidoTablaPermisoUsuario1").empty();
        $("#contenidoTablaPermisoUsuario1").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaXUsuario1");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function(){
        alertify.error("Error al cargar.");
      }
    });
  }

  function aprobarPermiso(idUsu, idPermiso){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'cambioEstadoPermiso', idPermiso: idPermiso, idEstado: 3, idAutoriza: <?php echo($usuario['id']); ?>},
      success: function(data){
        if (data == "Ok") {
          alertify.success("Se ha aprobado el permiso.");
          tablaPermisoUsuario(idUsu, 1, 1);
        }else{
          alertify.error(data);
        }
      },
      error: function(){
        alertify.error("Error al aprobar el permiso."); 
      }
    });
  }

  function rechazarPermiso(idUsu, idPermiso){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'cambioEstadoPermiso', idPermiso: idPermiso, idEstado: 4, idAutoriza: <?php echo($usuario['id']); ?>},
      success: function(data){
        if (data == "Ok") {
          alertify.success("Se ha rechazado el permiso.");
          tablaPermisoUsuario(idUsu, 1, 1);
        }else{
          alertify.error(data);
        }
      },
      error: function(){
        alertify.error("Error al rechazar el permiso."); 
      }
    });
  }

  function permisoUsuario(id, nombre){
    $.ajax({
      url: '<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/solicitud_permisos.php',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaPermisosUsuario1', idEstado: 1, idUsu: id, lider: 1},
      success: function(data){
        $("#tablaXUsuario1").hide();
        $("#tablaXUsuario1").dataTable().fnDestroy();
        
        $("#tablaXUsuario").show();        
        $("#tablaXUsuario").dataTable().fnDestroy();
        $("#contenidoTablaPermisoUsuario").empty();
        $("#contenidoTablaPermisoUsuario").html(data);
        // =======================  Data tables ==================================
        definirdataTable("#tablaXUsuario");

        $(".btn-observaciones").on("click", function(){
          $("#modal-observaciones-body").html($(this).val());
          $("#modal-observaciones").modal("show");
        });

        $('[data-toggle="tooltip"]').tooltip();

        $("#botones-usuario").html(`<div class="btn-group" role="group" aria-label="Basic example">
                                      <button type="button" class="btn btn-primary" onclick="tablaPermisoUsuario(${id}, 1, 1)">En espera</button>
                                      <button type="button" class="btn btn-success" onclick="tablaPermisoUsuario1(${id}, 3)">Aprobados</button>
                                      <button type="button" class="btn btn-secondary" onclick="tablaPermisoUsuario1(${id}, 4)">Rechazados</button>
                                      <button type="button" class="btn btn-info" onclick="tablaPermisoUsuario1(${id}, 5)">Finalizados</button>
                                    </div>`);

        $("#tituloModalPermisoUsuario").html(nombre);
        $("#modalPermisosUsuario").modal("show");
      },
      error: function(){
        alertify.error("Error al cargar.");
      }
    });
  }
</script>
</html>