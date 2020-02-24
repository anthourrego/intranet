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
    echo $lib->metaTagsRequired();
  ?>
	<title>Consumer Electrnics Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" id="documentos_generales" href="">
          <i class="fas fa-folder-open fa-7x"></i>
          <h4 class="mt-2">Documentos <br> Generales</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none link" href="<?php echo RUTA_RAIZ ?>paginas/sig/mapa_procesos">
          <i class="fas fa-sitemap fa-7x"></i>
          <h4 class="mt-2">Mapa de Macroprocesos</h4>
        </a>
      </div>
    </div>
    <div class="row d-flex justify-content-around mt-4 ">    
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none" id="matriz_responsabilidades_autoridad" download="Matriz de responsabilidades y autoridad" href="">
          <i class="fas fa-file-excel fa-5x"></i>
          <h4 class="mt-2">Matriz de responsabilidades y autoridad</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none" id="comunicaciones" download="Matriz de comunicaciones" href="">
          <i class="fas fa-file-excel fa-5x"></i>
          <h4 class="mt-2">Matriz de comunicaciones</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" id="aspectos_impactos_ambientales" href="">
          <i class="far fa-folder-open fa-5x"></i>
          <h4 class="mt-2">Matriz de aspectos e impactos ambientales</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" id="peligros_riesgos" download="Matriz de peligros y riesgos SST" href="">
          <i class="far fa-folder-open fa-5x"></i>
          <h4 class="mt-2">Matriz de peligros y riesgos SST</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none" id="contexto_organizacion" href="<?php echo direccionIP() . $ruta_documentos['Contexto de la organizacion']['mod_ruta'] ?>">
          <i class="fas fa-file-excel fa-5x"></i>
          <h4 class="mt-2">Contexto de la organización</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" id="seguimiento_medicion" href="">
          <i class="far fa-folder-open fa-5x"></i>
          <h4 class="mt-2">Matriz de seguimiento y medición</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" id="revision_direccion" href="">
          <i class="far fa-folder-open fa-5x"></i>
          <h4 class="mt-2">Revisión por la dirección</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" id="certificaciones" href="">
          <i class="far fa-folder-open fa-5x"></i>
          <h4 class="mt-2">Certificaciones</h4>
        </a>
      </div>
    </div>
  </div>
</body>
<?php  
  echo $lib->cambioPantalla();
?> 
<script type="text/javascript">
  $(function(){
    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'modulo_lista_info', mod_nombre: 'intranet_sig', mod_tipo: 'intranet'},
      success: function(data){
        $('#documentos_generales').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_documentos_generales.mod_ruta + '?ruta=/' + data.intranet_sig_documentos_generales.mod_ruta);
        $('#matriz_responsabilidades_autoridad').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_matriz_responsabilidades_autoridad.mod_ruta);
        $('#comunicaciones').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_matriz_comunicaciones.mod_ruta);
        $('#aspectos_impactos_ambientales').attr('href', '<?php echo(direccionIP()); ?>' + data.  intranet_sig_matriz_aspectos_impactos_ambientales.mod_ruta + '?ruta=/' + data.  intranet_sig_matriz_aspectos_impactos_ambientales.mod_ruta);
        $('#peligros_riesgos').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_matriz_peligros_riesgos_sst.mod_ruta + '?ruta=/' + data.intranet_sig_matriz_peligros_riesgos_sst.mod_ruta);
        $('#contexto_organizacion').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_contexto_organizacion.mod_ruta);
        $('#seguimiento_medicion').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_matriz_seguimiento_medicion.mod_ruta + '?ruta=/' + data.intranet_sig_matriz_seguimiento_medicion.mod_ruta);
        $('#revision_direccion').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_revision_direccion.mod_ruta + '?ruta=/' + data.intranet_sig_revision_direccion.mod_ruta);
        $('#certificaciones').attr('href', '<?php echo(direccionIP()); ?>' + data.intranet_sig_certificaciones.mod_ruta + '?ruta=/' + data.intranet_sig_certificaciones.mod_ruta);
      },
      error: function(){
        alertify.error("Error al traer los daots de politicas.");
      },
      complete: function(){
        cerrarCargando();
      }
    });


    $(".archivos").on("click", function(event){
      event.preventDefault();
      top.$('#cargando').modal("show");
      $('#modalArchivosTitulo').html($("h4", this).text());
      $("#contenidoArchivos").attr("src", $(this).attr("href"));
    });
  });
</script> 
</html>