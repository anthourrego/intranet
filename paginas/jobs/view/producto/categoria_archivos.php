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
            <button class="btn btn-sm btn-success"  data-toggle="modal" data-target="#modal_tipo_archivo_categoria" id="tipo_archivo_categoria"><i class="fas fa-cogs"></i> Tipo Archivo por Categoria</button>
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
    //OBTENER LAS EXTENCIONES ACTIVAS POR TIPO DOCUMETO(IMAGENES,DOCUMENTOS,ARCHIVOS)
    $.ajax({
        url: '../../model/datajobs.php',
        type: 'POST',
        dataType: 'json',
        data: {
          accion: "getExtPorTipoDocumento"
        },
        success: function(data){
            if(data.exito){
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
                    html = html + `<div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                  ${"." + data.extensiones[categorias[i]][j].toUpperCase()}
                                </label>
                              </div>`;
                  }  
                    
                  html = html + `</div>`;


                  $(".contenedor_extensiones").append(html);
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

  });

  
</script>
</html>