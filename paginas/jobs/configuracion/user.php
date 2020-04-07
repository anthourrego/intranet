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
    echo $lib->bootstrapTreeView();
  ?>
</head>
<body>
  <!-- button ir atras -->
  <!-- <i class="fas fa-plus" onclick="history.back()"> </i>    -->
  
  <div class="container mt-5 rounded pt-3 pb-5 border" style="background: rgba(255,255,255,0.6)">
    <div class="mx-auto" style="width: 200px;">
      <h5>Gestion de Usuarios</h5>
    </div>
    <table class="table table-hover mt-4 w-100" id="table_usuarios">
      <thead id="marcas-tabla-thead">
      </thead>
      <tbody id="marcas-tabla-tbody"></tbody>
    </table>
  </div>


<!-- Modal -->
<div class="modal fade" id="modalTree" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Permisos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 50vh !important;">
        <div class="form-group">
          <input type="input" class="form-control" id="input-search" placeholder="Buscar una permiso" value="" autocomplete="off">
        </div>
        <div id="tree"></div>
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
<script type="text/javascript">
  $(function(){
    //INICIAR VARIABLE GLOBAL DE DATATABLE
    window.datatable='';	

	  dt_gestionUsuarios();	
    iniciar_consulta();

    // accion boton para abrir arbol y asiganar permisos
    $(document).on("click",".btn_permisos",function(){
      var fun_id= $(this).attr("fun_id");
      var initSelectableTree = function() {
        return $('#tree').treeview({
          data: getTree(fun_id),
          color: "#428bca",
          showIcon:true,
          showCheckbox:true,
          expanded: true,
          
          //eventos del checked asigna permiso
          onNodeChecked: function(event, node) {
            gestionPermisosJobs(node.idPermiso,fun_id,1);
            //$('#checkable-output').prepend('<p>' + node.text + ' was checked</p>');
          },

          //eventos cuando se quita el checked quita permiso
          onNodeUnchecked: function (event, node) {
            gestionPermisosJobs(node.idPermiso,fun_id,0);
            //$('#checkable-output').prepend('<p>' + node.text + ' was unchecked</p>');
          }
        });
      }

      var $selectableTree = initSelectableTree();

      var findSelectableNodes = function() {
        return $selectableTree.treeview('search', [ $('#input-search').val(), { ignoreCase: true, exactMatch: false } ])
      };
      var selectableNodes = findSelectableNodes();

      $('#input-search').on('keyup', function (e) {
        //$('#tree').treeview('collapseAll', { silent:true });
        selectableNodes = findSelectableNodes();
      });
    });
  });

  // consulta a dyn para hacer la accion del boton
  function gestionPermisosJobs(idPermiso,fun_id,accionPermiso){

    //accionPermiso 1 si es seleccionado 0 si es deseleccionado
    $.ajax({
      type: "POST",
      url: "<?php echo(RUTA_CONSULTAS); ?>paginas/jobs/funJobs.php",
      cache: false,
      dataType: 'json',
      async:false,
      data: {
        accion:"gestionPermisosJobs",
        fun_id:fun_id,
        idPermiso:idPermiso,
        accionPermiso:accionPermiso
      },
      success: function(data){
        if(data.exito){
          if(accionPermiso == 0){
            alertify.warning("Permiso Inhabilitado.");
          }else if(accionPermiso == 1){
            alertify.success("Permiso Habilitado.");
          }
        }
      },
      error: function(){
        //Habilitamos el botón
        alertify.error("Error al intentar modificar permiso.");
      } 
    });
  }

  function getTree(fun_id) {
    var datatree= "";
    $.ajax({
        type: "POST",
        url: "<?php echo(RUTA_CONSULTAS); ?>paginas/jobs/funJobs.php",
        cache: false,
        dataType: 'json',
        async:false,
        data: {
          accion:"permisos_usuarios",
          fun_id:fun_id
        },
        success: function(data){
         datatree= data
        },
        error: function(){
          //Habilitamos el botón
          $("#btn-login").attr("Disabled", false);
          alertify.error("Error al inicar sesion.");
        } 
      });
    return datatree;
  }

  
  //OPCIONES DE DATATABLE
  function dt_gestionUsuarios(){
      datatable =$("#table_usuarios").DataTable({       
        paging: true,	
        stateSave: false,	
        "searching": true,
        "autoWidth": false,		
        ordering:false,
        columns: [
          { title: ""},       
          { title: "NOMBRE" },			   
          { title: "USUARIO" },
          { title: "ACCIONES" }
        ],		
        dom: 'Bfrtip',
        buttons: [{
          extend: 'excel',
          text: 'Excel',
          className: 'exportExcel btn btn-success excel_btn borde_card',
          filename: 'lista_usuarios',
          footer: true,
          exportOptions: {
            modifier: {
            page: 'all'
            },
          }	     
        }],		
        "language": {			
          "sProcessing":     "Procesando...",
          "sLengthMenu":     "Mostrar _MENU_ registros",
          "sZeroRecords":    "No se encontraron resultados",
          "sEmptyTable":     "Ningún dato disponible en esta tabla",
          "sInfo":           "Registros  _START_ al _END_ - Total  _TOTAL_ registros",
          "sInfoEmpty":      "Registros  0 al 0 - Total  0 registros",
          "sInfoFiltered":   "(filtrado 	 total de _MAX_ registros)",
          "sInfoPostFix":    "",
          "sSearch":         "Buscar:",
          "sUrl":            "",
          "sInfoThousands":  ",",
          "sLoadingRecords": "Cargando...",
          "oPaginate": {		
            "sFirst":    "Primero",		
            "sLast":     "Último",		
            "sNext":     "Siguiente",		
            "sPrevious": "Anterior"		
          },
          "oAria": {		
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",		
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"		
          }
        }		
	    });
      // BOTON DE EXCEL APLICANDO ESTILOS
	    $(".excel_btn").html('Excel <i class="far fa-file-excel"></i>');
    }

    // INICIAR CONSULTA A BASE DE DATOS
    function iniciar_consulta(){

      $.ajax({
        type: "POST",
        url: "<?php echo(RUTA_CONSULTAS); ?>paginas/jobs/funJobs.php",
        cache: false,
        dataType: 'json',
        async:false,
        data: {
          accion:"listaUsuarios"
        },
        success: function(data){
          var obj = JSON.parse(data.data);
          var datatable = $('#table_usuarios').DataTable();
          datatable.clear();
          $.each(obj, function(index, value) {
            datatable.row.add(value);
          });
          datatable.draw();
          datatable.columns.adjust().draw();
          $('[data-toggle="tooltip"]').tooltip();
        },
        error: function(){
          //Habilitamos el botón
          $("#btn-login").attr("Disabled", false);
          alertify.error("Error al inicar sesion.");
        } 
      });
      cerrarCargando();
    }
  
</script>
</html>