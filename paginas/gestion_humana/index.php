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
	<title>Consumer Electronocs Group S.A.S</title>
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
    <div class="row justify-content-center" id="contenido">
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" href="<?php echo(direccionIPRuta()); ?>paginas/gestion_humana/certificado_laboral.php?id=<?php echo($usuario['id']) ?>">
          <i class="fas fa-certificate fa-7x"></i>
          <h4 class="mt-2">Certificado laboral</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none archivos" href="<?php echo RUTA_RAIZ ?>documentos/REGLAMENTOINTERNO.pdf">
          <i class="fas fa-ruler fa-7x"></i>
          <h4 class="mt-2">Reglamento Interno de Trabajo</h4>
        </a>
      </div>
      <div class="col-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none link" href="<?php echo RUTA_RAIZ; ?>paginas/creser/">
          <img src="<?php echo RUTA_RAIZ; ?>img/creser.png" class="w-100">
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a target="_blank" class="text-decoration-none archivos" id="higiene_seguridad_industrial" href="">
          <i class="fas fa-bookmark fa-7x"></i>
          <h4 class="mt-2">Reglamento de higiene y seguridad industrial</h4>
        </a>
      </div>
      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none" href="<?php echo RUTA_RAIZ; ?>paginas/biometrico/">
          <i class="fas fa-fingerprint fa-7x"></i>
          <h4 class="mt-2">Biometrico</h4>
        </a>
      </div>

      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none" href="<?php echo RUTA_RAIZ; ?>paginas/gestion_humana/solicitud_permisos.php">
          <i class="fas fa-clipboard-list fa-7x"></i>
          <h4 class="mt-2">Solicitud de permisos</h4>
        </a>
      </div>

      <div class="col-10 col-md-3 text-center mt-4 iconos-sig">
        <a class="text-decoration-none" href="<?php echo RUTA_RAIZ; ?>paginas/gestion_humana/sala_juntas.php">
          <i class="fas fa-door-open fa-7x"></i>
          <h4 class="mt-2">Sala de Juntas</h4>
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
    $(".archivos").on("click", function(event){
      event.preventDefault();
      //top.$('#modalArchivos').modal("show");
      top.$('#cargando').modal("show");
      $('#modalArchivosTitulo').html($("h4", this).text());
      $("#contenidoArchivos").attr("src", $(this).attr("href"));
    });

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "solicitud_permisos_porteria"},
      success: function(data){
        if (data.length != 0) {
          $("#contenido").append(`<div class="col-10 col-md-3 text-center mt-4 iconos-sig">
                                    <a class="text-decoration-none" href="<?php echo(RUTA_RAIZ); ?>paginas/gestion_humana/porteria.php">
                                      <i class="fas fa-user-shield fa-7x"></i>
                                      <h4 class="mt-2">Porteria</h4>
                                    </a>
                                  </div>`);
        }
        
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'permiso_fun_app', mod_tipo: 'intranet', fun_id: <?php echo($usuario['id']); ?>, mod_nombre: "certificados_laborales"},
      success: function(data){
        if (data.length != 0) {
          $("#contenido").append(`<div class="col-10 col-md-3 text-center mt-4 iconos-sig">
                                    <a class="text-decoration-none archivos" href="certificados_laborales">
                                      <i class="far fa-address-book fa-7x"></i>
                                      <h4 class="mt-2">Certificados laborales</h4>
                                    </a>
                                  </div>`);
        }
        
      },
      error: function(){
        alertify.error('No ha validado el permiso');
      }
    });

    $.ajax({
      url: '<?php echo(direccionIPRutaBase()); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'modulo_lista_info', mod_nombre: 'intranet_gh', mod_tipo: 'intranet'},
      success: function(data){
        //console.log(data);
        $('#higiene_seguridad_industrial').attr("href", '<?php echo(direccionIP()); ?>' + data.intranet_gh_regalmento_higene_seguridad_indsutrial.mod_ruta + '?ruta=/' + data.intranet_gh_regalmento_higene_seguridad_indsutrial.mod_ruta);
      },
      error: function(){
        alertify.error("No se ha podido cargar.");
      }
    });

    cerrarCargando();
  });
</script>
</html>