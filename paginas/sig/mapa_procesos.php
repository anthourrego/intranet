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
	<div class="container-fluid mt-3">
    <a class="btn btn-secondary link" href="<?php echo RUTA_RAIZ ?>paginas/sig/index"><i class="fas fa-arrow-left"></i> Atrás</a>
    <hr>
  </div>
  <div class="container mt-5">
    <div class="row mapademacroprocesos">
      <div class="col-lg-2 col-xl-3 ocultar-xs ocultar-sm ocultar-md">
        <img src="<?= $ruta_raiz; ?>/img/mapa/01.jpg" width="100%" style="display: block; margin: auto;">
      </div>
      <div class="col-12 col-lg-8 col-xl-6">
        <div class="row mb-4">
          <div class="col-4 col-md-4 columna-sig" id="E01"></div>
          <div class="col-md-4 ocultar-xs ocultar-sm columna-sig">
            <img src="<?= $ruta_raiz; ?>/img/mapa/procesos_estrategicos.png" width="100%">
          </div>
          <div class="col-12 col-md-4 columna-sig" id="E02"></div>
        </div>
        <div class="row justify-content-center">
          <div class="col-12 col-md-4 columna-sig" id="V02"></div>
        </div>
        <div class="row">
          <div id="V01" class="col-12 col-md-4 columna-sig"></div>
          <div class="col-md-4 ocultar-xs ocultar-sm columna-sig">
            <img src="<?= $ruta_raiz; ?>/img/mapa/procesos_de_valor.png" width="100%">
          </div>
          <div id="V04" class="col-12 col-md-4 columna-sig"></div>
        </div>

        <div id="V03" class="row mb-4 justify-content-center"></div>
        <div class="row">
          <div id="A01" class="col-md-4 col-12 columna-sig"></div>
          <div class="col-md-4 ocultar-xs ocultar-sm columna-sig">
            <img src="<?= $ruta_raiz; ?>/img/mapa/procesos_de_apoyo.png" width="100%">
          </div>
          <div id="A02" class="col-md-4 col-12 columna-sig"></div>
        </div>
        <div class="row justify-content-center">
          <div id="A03" class="col-md-4 col-12 columna-sig"></div>
        </div>
      </div>
      <div class="col-lg-2 col-xl-3 ocultar-xs ocultar-sm ocultar-md">
        <img src="<?= $ruta_raiz; ?>/img/mapa/02.jpg" width="100%" style="display: block; margin: auto;">
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
      url: '<?php echo(direccionIPRuta()) ?>paginas/sig/mapa_procesos.php',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'mapaProcesos', fun_id: <?php echo($usuario['id']); ?>, ruta: "<?php echo(direccionIP()); ?>"},
      success: function(data){
        $("#E01").html(data.E01);
        $("#E02").html(data.E02);
        $("#V02").html(data.V02);
        $("#V01").html(data.V01);
        $("#V04").html(data.V04);
        $("#V03").html(data.V03);
        $("#A01").html(data.A01);
        $("#A02").html(data.A02);
        $("#A03").html(data.A03);
        
        $(".dropdown").hover(function() {
          $('.dropdown-menu-sig', this).stop( true, true ).fadeIn("fast");
          $(this).toggleClass('open');             
        },function() {
          $('.dropdown-menu-sig', this).stop( true, true ).fadeOut("fast");
          $(this).toggleClass('open');              
        });

        $(".archivos").on("click", function(event){
          event.preventDefault();
          top.$('#cargando').modal("show");
          $('#modalArchivosTitulo').html($(this).text());
          $("#contenidoArchivos").attr("src", $(this).attr("href"));
        });
      },
      error: function(){
        alertify.error("No se ha cargado el mapa de procesos.");
      }
    });

  });
</script>
</html>