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
  include_once($ruta_raiz . 'paginas/jobs/model/datajobs.php');

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
    echo $lib->materialSwitch();
  ?>
</head>
<body>
  <!-- button ir atras -->
  <!-- <i class="fas fa-plus" onclick="history.back()"> </i>    -->
  
  <div class="container mt-5 rounded pt-3 pb-5 border" style="background: rgba(255,255,255,0.6)">
    <div class="mx-auto" style="width: 200px;">
      <h5>Categoria archivos</h5>
    </div>
    <div class="row">
        <div class="col-md-6 btn_ptipo_archivo_categoria">
            <button class="btn btn-sm btn-success" id="tipo_archivo_categoria"><i class="fas fa-cogs"></i>Extensiones por Categoria</button>
        </div>
    </div>
    <br>
    <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <input type="input" class="form-control" id="input-search" placeholder="Buscar una categoria" value="" autocomplete="off">
        </div>
        <div id="treecat"></div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="mx-auto" style="width: 200px;">
          <h6>Administrar Categorias</h6>
        </div>     
        <div class="row pt-3">
          <div class="col-md-4 col-sm-12">
            <button class="btn btn btn-success" id="add_categoria" type="button" data-toggle="modal" data-target="#modalAddCatergorias"><i class="fas fa-plus"></i> Agregar</button>
          </div>
          <div class="col-md-4 col-sm-12">
            <button class="btn btn btn-primary" disabled id="btnEditar" type="button"><i class="fas fa-plus"></i> Editar</button>
          </div>
          <div class="col-md-4 col-sm-12">
            <button class="btn btn btn-danger" disabled id="btnEliminar" type="button"><i class="fas fa-plus"></i> Eliminar</button>
          </div>
        </div>
        
          <form id="formEditar" class="mt-4" novalidate="novalidate">
            <input type="hidden" name="accion" value="editarCategoria">
            <input type="hidden" name="idCategoria" value="" disabled="disabled">
            <div class="form-group row">
              <label class="col-12 col-md-4 align-self-center" for="catPadre">Categoria Padre:</label>
              <div class="col-12 col-md-8">
                <select class="custom-select" name="catPadre" id="
                " required="" aria-invalid="false" disabled="disabled">
               </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-12 col-md-4 align-self-center" for="nombre">Nombre:</label>
              <div class="col-12 col-md-8">
                <input class="form-control" type="text" name="nombre" placeholder="N/A" required="" autocomplete="off" disabled="disabled">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-12 col-md-4 align-self-center" for="fechaCreacion">Fecha creación:</label>
              <div class="col-12 col-md-8">
                <input class="form-control" type="text" name="fechaCreacion" placeholder="N/A" disabled="">
              </div>
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-success" name="btnGuardar" disabled="disabled"><i class="far fa-save"></i> Guardar</button>
            </div>
          </form>
        
      </div>
    </div>
    <br>
    <table class="table table-hover mt-4 w-100" id="table_usuarios">
      <thead id="marcas-tabla-thead">
      </thead>
      <tbody id="marcas-tabla-tbody"></tbody>
    </table>
  </div>

  <!-- Modal crear Categorias -->
  <div class="modal fade" id="modalAddCatergorias" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Crear Categoria</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearCategoria" autocomplete="off">
          <input type="hidden" name="accion" value="crearCategoria">
          <div class="modal-body">
            <div class="form-group">
              <label for="fk_categoria">Miembro de</label>
              <select id="fk_categoria" name="fk_categoria" class="custom-select">
              
              </select>
            </div>
            <div class="form-group">
              <label for="nombreCat">Categoria</label>
              <input class="form-control" type="text" name="nameCategoria" id="nameCategoria" placeholder="Nombre para la categoria" name="nombreCat" autocomplete="off" required>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <label>Aplica PI</label> 
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <!-- sm switch -->
                    <span class="switch switch-sm">
                      <input type="checkbox" name="checkboxaplicaPI" class="switch" id="checkboxaplicaPI">
                      <label for="checkboxaplicaPI"></label>
                    </span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <label>Publico</label> 
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <!-- sm switch -->
                    <span class="switch switch-sm">
                      <input type="checkbox" name="checkboxprivacidad" class="switch" id="checkboxprivacidad">
                      <label for="checkboxprivacidad"></label>
                    </span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit"  class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  

<!-- Modal extensiones activas -->
<div class="modal fade bd-example-modal-lg" id="modal_tipo_archivo_categoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Tipo archivo por categoria</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 50vh !important;">
        <div class="row contenedor_extensiones">
        </div>

        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
        <button type="button" id="guardar_tipo_ext" class="btn btn-success" data-dismiss="modal"><i class="fas fa-save"></i> Guardar</button>
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

    //CARGAR ARBOL
    cargarArbol(0);
    //CARGAR SELECET CATEGORIA
    //cargarSelectMiembros(2);
    

    $("#modalAddCatergorias").on("show.bs.modal", function(e){
      cargarSelectMiembros(2);
    });

    

    //Acción al click botón eliminar
    $("#btnEliminar").on("click", function(){
      if ($(this).val() != 0) {
        alertify.confirm(
                  '¿Estas seguro?', 
                  'Deseas eliminar la categoria <b>' + $("#formEditar :input[name='nombre']").val() + '</b>', 
                function(){ 
                  eliminarCategoria($("#btnEliminar").val() , $("#btnEliminar").data("nombre"))
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


    //editar categoria
    //Formulario editar tecnólogia
    $("#formEditar").submit(function(e){
      e.preventDefault();
      if($(this).valid()){
        $.ajax({
          type: "POST",
          dataType: "json",
          url: "../../model/datajobs.php",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data.success == true) {
              //Tomados el id de la tecnólogia seleccionada actualmente
              let idSelect = $("#formEditar :input[name='catPadre']").val();

              //Se cargan todos los datos datos del arbol y del select
              cargarArbol();
              cargarSelectMiembros(0,idSelect);
              
              //Volvemos a deshabilitar los campos
              $("#formEditar :input[name='idCategoria']").attr("disabled", true);
              $("#formEditar :input[name='catPadre']").attr("disabled", true);
              $("#formEditar :input[name='nombre']").attr("disabled", true);
              $("#formEditar :input[name='btnGuardar']").attr("disabled", true);
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
      $("#formEditar :input[name='idCategoria']").attr("disabled", false);
      $("#formEditar :input[name='catPadre']").attr("disabled", false);
      $("#formEditar :input[name='nombre']").attr("disabled", false);
      $("#formEditar :input[name='btnGuardar']").attr("disabled", false);
    });

    //ACCION BTN GUARDAR EN CREAR CATEGORIA
    $("#formCrearCategoria").submit(function(e){
      e.preventDefault();
      if($(this).valid()){
        $.ajax({
          type:"POST",
          dataType:"json",
          url:"../../model/datajobs.php",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success:function(data){
            if(data.exito){
              alertify.success(data.alert.msj);
              $("#modalAddCatergorias").modal("hide");
              $("#modalAddCatergorias :input[name='fk_categoria']").val(0);
              $("#modalAddCatergorias :input[name='nameCategoria']").val('');
              $("#modalAddCatergorias :input[name='nameCategoria']").removeClass('is-valid');
              $("#modalAddCatergorias :input[name='checkboxaplicaPI']").prop("checked", false);
              $("#modalAddCatergorias :input[name='checkboxprivacidad']").prop("checked", false);
              cargarSelectMiembros();
              cargarArbol();
            }else{
              alertify.error(data.alert.msj);
            }

          },
          error:function(){
            alertify.error("Error. intenta de Nuevo...");
          }
        });
      }
    });



    function eliminarCategoria(idCategoria, nombre){
    $.ajax({
      url: "../../model/datajobs.php",
      type: "POST",
      dataType: "json",
      data: {
        accion: "eliminarCategoria",
        idCat: idCategoria,
        nombre: nombre
      },
      success: function(data){
        if (data == 1) {
          cargarArbol();
          cargarSelectMiembros();
          $("#formEditar :input[name='idCategoria']").val(0);
          $("#formEditar :input[name='catPadre']").val(0);
          $("#formEditar :input[name='nombre']").val('N/A');
          $("#formEditar :input[name='fechaCreacion']").val('N/A');

          $("#datos-check").empty();
          
          
          $("#formEditar :input[name='idCategoria']").attr("disabled", true);
          $("#formEditar :input[name='catPadre']").attr("disabled", true);
          $("#formEditar :input[name='nombre']").attr("disabled", true);
          $("#formEditar :input[name='btnGuardar']").attr("disabled", true);

          alertify.warning("Se ha eliminado correctamente");
        }else{
          alertify.error("No ha podido eliminar la Categoria <b>" + nombre + "</b>")
        }
      },
      error: function(data){
        alertify.error("No se han encontrado datos...");
      }
    });
  }

      
      
    cerrarCargando();

    $(document).on("click","#tipo_archivo_categoria",function(){
      // obtener lista ext
      top.$("#cargando").modal("show");
      getTypeExt();
    });

    $(document).on("click","#guardar_tipo_ext",function(){

      var seleccionados= "";
      var noseleccionados="";
      $(".ext_tipo_archivos").each(function(){
        if($(this).is(":checked")){
          seleccionados+=$(this).val()+',';
        }else{
          noseleccionados+=$(this).val()+',';
        }   
      });
      changeStateExt(seleccionados,noseleccionados);
    });



  });

  //CARGAR SELECT DE MIEMBROS
  function cargarSelectMiembros(idSelec = 0, fkCat = 0, idCat = 0){
      $.ajax({
        url: "../../model/datajobs.php",
        type: "POST",
        dataType: "json",
        data: {
          accion: "dataSelectCategoria"
        },
        success:function(data){
          if(data.exito){

          $inputSelect = "#formEditar :input[name='catPadre'], #formCrearCategoria :input[name='fk_categoria']";
          if (idSelec == 1) {
            $inputSelect = "#formEditar :input[name='catPadre']";
          }else if(idSelec == 2){
            $inputSelect = "#formCrearCategoria :input[name='fk_categoria']";
          }
            $($inputSelect).empty();
            $($inputSelect).append(`
            <option value="0" selected>Raíz</option>
            `);
            for (let i = 0; i < data.lista.msj.cantidad_registros; i++) {
              $($inputSelect).append(`
                <option  value="${data.lista.msj[i].id}">${data.lista.msj[i].nombre.charAt(0).toUpperCase() + data.lista.msj[i].nombre.slice(1) }</option>
              `); 
            }

            //Seleccionamos el fk padre de la tecnologia seleccionada
            if (fkCat != 0) {
              $("#formEditar :input[name='catPadre']").val(fkCat);
            }

          }else{
            console.log(data.lista.msj)
          }
        },
        error:function(){

        }
      });
    }

  //ACTUALIZAR ESTADO DE LAS EXTENSIONES
  function changeStateExt(seleccionados,noseleccionados){
    top.$("#cargando").modal("show");
    $.ajax({
      url: "../../model/datajobs.php",
      type: "POST",
      dataType: "json",
      data:{
        accion: "changeStateExt",
        seleccionados:seleccionados,
        noseleccionados:noseleccionados
      },
      success:function(data){
        if(data.exito == 1){
          alertify.success("Cambios almacenados con exito")
        }
        
      },
      error:function(){
        alertify.error('error, Intentalo de nuevo');
      },
      complete:function(){
        cerrarCargando();
      }

    });
  }
  
  //OBTENER LAS EXTENCIONES ACTIVAS POR TIPO DOCUMETO(IMAGENES,DOCUMENTOS,ARCHIVOS)
  function getTypeExt(){
    $.ajax({
        url: '../../model/datajobs.php',
        type: 'POST',
        dataType: 'json',
        data: {
          accion: "getExtPorTipoDocumento"
        },
        success: function(data){
            if(data.exito){
              $(".contenedor_extensiones").html("");
              console.log(data.extensiones);
                var categorias = Object.keys(data.extensiones);
                
                for (let i = 0; i < categorias.length; i++) {
                  for (let j = 0; j < data.extensiones[categorias[i]].length; j++){
                    console.log(data.extensiones[categorias[i]][j]);
                  }
                }

                for (let i = 0; i < categorias.length; i++) {
                  

                  html = `<div class="col-md-4">
                      <h6 class="mx-auto">${categorias[i].toUpperCase()}</h6>`;

                  for (let j = 0; j < data.extensiones[categorias[i]].length; j++) {
                    if(data.extensiones[categorias[i]][j].estado == 1){
                      checked="checked";
                    }else{
                      checked="";
                    }
                    html = html + `<div class="form-check">
                                <input class="form-check-input ext_tipo_archivos" ${checked} type="checkbox" value="${data.extensiones[categorias[i]][j].id}" id="">
                                <label class="form-check-label" for="${ data.extensiones[categorias[i]][j].id}">
                                  ${"." + data.extensiones[categorias[i]][j].ext.toUpperCase()}
                                </label>
                              </div>`;
                  }  
                    
                  html = html + `</div>`;


                  $(".contenedor_extensiones").append(html);
                  $("#modal_tipo_archivo_categoria").modal("show");
                } 
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


  function cargarArbol(iniciar = 1){
    $.ajax({
      url: "../../model/datajobs.php",
      type: "POST",
      dataType: "json",
      data: {
        accion: "arbolCategorias"
      },
      success: function(data){
        if (iniciar == 1) {
          arbol = $('#treecat').treeview('getExpanded');
        }

        var initSelectableTree = function() {
          return $('#treecat').treeview({
            levels: 1,
            data: data,
            showTags: true,
            onNodeSelected: function(event, node) {
              //Cargamos el select
              cargarSelectMiembros(0, node.fk_categoria, node.idCategoria);

              
              console.log(node.idCategoria);
              //Motramos todos los campos del selece en editar si hemos ocultado alguno
              $("#formEditar :input[name='catPadre'] option").show();
          
              //Enviamos id al botón eliminar
              $("#btnEliminar").val(node.idCategoria);
              $("#btnEliminar").data("nombre", node.text);

              //Volvemos a deshabilitar los campos
              $("#formEditar :input[name='idCategoria']").attr("disabled", true);
              $("#formEditar :input[name='catPadre']").attr("disabled", true);
              $("#formEditar :input[name='nombre']").attr("disabled", true);
              $("#formEditar :input[name='btnGuardar']").attr("disabled", true);

              //Datos para editar
              $("#formEditar :input[name='idCategoria']").val(node.idCategoria);
              $("#formEditar :input[name='catPadre']").val(node.fk_categoria);
              $("#formEditar :input[name='nombre']").val(node.text);
              $("#formEditar :input[name='fechaCreacion']").val(node.fechaCreacion);


              //Ocultamos la tecnólogia seleccionada en el select
              $("#formEditar :input[name='catPadre'] option[value='" + node.idCategoria + "']").hide();

              $("#formEditar :input[name='catPadre']").removeClass('is-valid')
              $("#formEditar :input[name='nombre']").removeClass('is-valid');
              

              
              
              //Habilitamos los botónes para editar
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
          $('#treecat').treeview('collapseAll', { silent:true });
          selectableNodes = findSelectableNodes();
        });

        if (iniciar == 1) {
          if (arbol.length > 0) {
            for (let i = 0; i < arbol.length; i++) {
              $('#treecat').treeview('expandNode', [ arbol[i].nodeId, { silent: true } ]);
            }
          }
        }
      },
      error: function(){
        alertify.error("No se han encontrado datos...");
      },
      complete: function(){
        cerrarCargando();
      }
    });
  }
  

  
</script>
</html>