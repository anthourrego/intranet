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
            <button class="btn btn-sm btn-success" id="tipo_archivo_categoria"><i class="fas fa-cogs"></i> Tipo Archivo por Categoria</button>
        </div>
    </div>
    <br>
    <table class="table table-hover mt-4 w-100" id="table_usuarios">
      <thead id="marcas-tabla-thead">
      </thead>
      <tbody id="marcas-tabla-tbody"></tbody>
    </table>
  </div>


<!-- Modal -->
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
      console.log(seleccionados);
    });



  });

  //ACTUALIZAR ESTADO DE LAS EXTENSIONES
  function changeStateExt(){
    $.ajax({
      url: "../../model/datajobs.php",
      type: "POST",
      dataType: "json",
      data:{
        accion: "changeStateExt"
      },
      success:function(data){
        if(data.exito){
          alertify.succes("Cambios almacenados con exito")
        }
        
      },
      error:function(){
        alertify.error('error, Intentalo de nuevo');
      },
      complete:function(){
        
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
  

  
</script>
</html>