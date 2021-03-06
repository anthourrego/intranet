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
  
  $lib = new Libreria;
?>
<!DOCTYPE html>
<html>
<head>
   <?php  
      echo $lib->jquery();
      echo $lib->bootstrap();
      echo $lib->fontAwesome();
      echo $lib->alertify();
      echo $lib->intranet();
      echo $lib->slideNav2CSS();
    ?>
</head>
<body id="page-top" class="overflow-auto">
	<div class="container-fluid mt-3 mb-4">
    <div class="row">
      <div class="col-12">
        <input autofocus type="search" class="form-control" id="input-search" placeholder="Buscar..." autocomplete="off">
      </div>
    </div>
    <div class="searchable-container">
      <div id="cards" class="row justify-content-center justify-content-lg-start"></div>
    </div>
  </div>
  <?php 
    include($ruta_raiz . 'footer.php');

    echo($lib->cambioPantalla());
  ?>
  <script type="text/javascript">
    $(function() {
      $.ajax({
        url: '<?php echo(RUTA_CONSULTAS); ?>paginas/nosotros.php',
        type: 'POST',
        dataType: 'json',
        data: {accion: 'cardUsuarios', ruta: "<?php echo(RUTA_ALMACENAMIENTO); ?>"},
        success: function(data){
          $("#cards").empty();
          if(data.success == true){
            for (let i = 0; i < data.msj.cantidad_registros; i++) {
              var foto = "<?php echo(RUTA_ALMACENAMIENTO); ?>foto-usuario/0.png";
              var correo = "N/A";
              var ext = "N/A"

              if (data.msj[i].fun_foto != null) {
                foto = '<?php echo(RUTA_ALMACENAMIENTO); ?>' + data.msj[i].fun_foto;
              }

              if (data.msj[i].fun_correo != null) {
                correo = `<a class="text-decoration-none" href="mailto:'${data.msj[i].fun_correo}'">${data.msj[i].fun_correo.slice(0,-17)}...</a>`;
              }

              if (data.msj[i].fun_extension != null) {
                ext= data.msj[i].fun_extension;
              }

              $("#cards").append(`
                <div class="col-10 col-sm-12 col-md-10 col-lg-6 col-xl-4 items">
                  <div class="card mt-3 border-left-primary shadow">
                    <div class="row no-gutters align-items-center">
                      <div class="col-12 col-sm-4">
                        <img src="${foto}" class="card-img w-100" alt="...">
                      </div>
                      <div class="col-12 col-sm-8">
                        <div class="card-body">
                          <p class="card-text">
                            ${data.msj[i].fun_nombre_completo}<br>
                            ${data.msj[i].dep_tag}<br>
                            ${correo}<br>
                            Ext: ${ext}<br>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              `);
            }
          }else{
            alertify.error(data.msj);
          }
        },
        error: function(){
          alertify.error("No se han cargado los usuarios.");
        },
        complete: function(){
          cerrarCargando();
        }
      });

      $('#input-search').on('keyup', function() {
        cont = 0;
        var rex = new RegExp($(this).val(), 'i');
        $('.searchable-container .items').hide();
        $('.searchable-container .items').filter(function() {
          return rex.test($(this).text());
          cont++;
        }).show();
      });
    });
  </script>
  <?php  
    echo $lib->slideNav2JS();
    echo $lib->cambioPantalla();
  ?>
</body>
</html>