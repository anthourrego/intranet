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
  //include_once($ruta_raiz . 'clases/Conectar.php');

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
  <title></title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
  ?>
</head>
<body>
  <!-- Contenido -->
    <div class="container">
      <br>
      <div class="row">
        <div class="col-12">
        <a href="<?php echo RUTA_RAIZ; ?>paginas/creser/">
          <img src="../img/banner1.gif" class="w-100 mb-3 rounded">
        </a>
        </div>
      </div>

      <?php
      /*if (obtenerIp() == '201.236.254.67') {
      ?>
        <div class="row">
          <div class="col-12 text-center">
            <video class="video-principal" controls autoplay muted>
              <source src="<?php echo RUTA_RAIZ; ?>videos/hyundai.mp4" type="video/mp4">
              Su navegador no soporta este video...
            </video>
          </div>
        </div>
        <hr>
      <?php
      }*/
      ?>
      <div class="row mb-5">
        <div class="col-12 bg-white border border-secondary rounded">
          <ul class="nav nav-pills mb-3 mt-3 justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="pills-planeacion-tab" data-toggle="pill" href="#pills-planeacion" role="tab" aria-controls="pills-planeacion" aria-selected="true">Planeación estratégica</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pills-quienes-somos-tab" data-toggle="pill" href="#pills-quienes-somos" role="tab" aria-controls="pills-quienes-somos" aria-selected="false">Quiénes somos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pills-nuestra-cultura-tab" data-toggle="pill" href="#pills-nuestra-cultura" role="tab" aria-controls="pills-nuestra-cultura" aria-selected="false">Nuestra cultura</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pills-politica-sig-tab" data-toggle="pill" href="#pills-politica-sig" role="tab" aria-controls="pills-politica-sig" aria-selected="false">Política SIG</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pills-politicas-tab" data-toggle="pill" href="#pills-politicas" role="tab" aria-controls="pills-politicas" aria-selected="false">Políticas</a>
            </li>
          </ul>
          <hr>
          <div class="tab-content mb-4" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-planeacion" role="tabpanel" aria-labelledby="pills-planeacion-tab">
              <!--<a href="modelo">Modelo Organizacional</a>-->
              <div class="row">
                <div class="col-12 col-md-3">
                  <div class="list-group" id="list-tab" role="tablist">
                    <a class="list-group-item list-group-item-action active" id="list-1-list" data-toggle="list" href="#list-1" role="tab" aria-controls="1">1. Marco General</a>
                    <a class="list-group-item list-group-item-action" id="list-2-list" data-toggle="list" href="#list-2" role="tab" aria-controls="2">2. Reseña Historica</a>
                    <a class="list-group-item list-group-item-action" id="list-3-list" data-toggle="list" href="#list-3" role="tab" aria-controls="3">3. Equipo de Trabajo</a>
                    <a class="list-group-item list-group-item-action" id="list-4-list" data-toggle="list" href="#list-4" role="tab" aria-controls="4">4. Descargar</a>
                  </div>
                </div>
                <div class="col-12 col-md-9 mt-5 mt-md-0">
                  <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="list-1" role="tabpanel" aria-labelledby="list-1-list">
                      <p>
                        <strong>CONSUMER ELECTRONICS GROUP S.A.S</strong>, tiene como objeto social la importación, exportación, producción y distribución de productos electrónicos y electrodomésticos para el hogar, la industria, y otros sectores.
                      </p>

                      <p>
                        Con el fin de cumplir con este objeto social, maneja dos unidades de negocio: línea marrón, para televisores, equipos y aparatos de audio y sonido; y la línea blanca, para equipos de aire acondicionado, congeladores, neveras, lavadoras.
                      </p>

                      <p>
                        La importación, producción, y distribución de estos electrodomésticos y productos electrónicos, se desarrolla bajo la autorización como únicos distribuidores para Colombia y Latinoamérica de la marca HYUNDAI, y la licencia para ensamblaje de televisores de Hyundai corporation.
                      </p>

                      <p>
                        Para el desarrollo efectivo de tan importante tarea, se tiene una planta de producción y centro de distribución ubicada en el corregimiento de Cerritos, a dos(2) km de la ciudad de Pereira, ubicación estratégica como corredor logístico para el centro, sur y noroccidente de Colombia.
                      </p>

                      <p>
                        Actualmente se abastece el mercado en Colombia a sus canales retail, tradicional y mayorista, con una cobertura que abarca las regiones del Eje Cafetero, Antioquia, el Centro y Oriente del país, Pacifico, Llanos Orientales, Caribe, el Sur y la Amazonia.
                      </p>

                      <p>
                        Como factor diferenciador, se tiene un servicio especializado de postventa, con un excelente soporte técnico en todas las regiones a donde llegan los productos para la atención adecuada y oportuna de garantías, y una línea de atención establecida para responder a las necesidades de los clientes.
                      </p>
                    </div>
                    <div class="tab-pane fade" id="list-2" role="tabpanel" aria-labelledby="list-2-list">
                      <p>
                        <strong>CONSUMER ELECTRONICS GROUP S.A.S.</strong> fue constituida el 21 de diciembre de 2012, con el objetivo principal de ensamblar televisores, bajo la licencia de Hyundai Corporation. Los planes presentes y futuros incluyen ensamblar para el mercado nacional e internacional.
                      </p>

                      <p>
                        Somos distribuidores autorizados para Colombia y Latinoamérica de productos electrónicos y electrodomésticos para el hogar y el entretenimiento marca HYUNDAI.
                      </p>

                      <p>
                      <strong>CONSUMER ELECTRONICS GROUP S.A.S</strong> surgió como una propuesta visionaria de una familia de la región que ha ido creciendo y se comenzó a fortalecer en el 2016, consolidándose en el mercado nacional, proyectada a exportar el ensamble de televisores de alta calidad a mercados latinoamericanos aprovechando los tratados de comercio con algunos de estos países.
                      </p>

                      <p>
                        Contamos con una planta de fabricación y centro de distribución con una localización estratégica en el corregimiento de Cerritos a tan solo dos kilómetros de Pereira y conexión con las principales ciudades y de mayor crecimiento en el país.
                      </p>

                      <p>
                        Comercializamos línea marrón con tv, audio y video y línea blanca con aires acondicionados, neveras, congeladores y lavadoras.
                      </p>

                      <p>
                        Tenemos cobertura a nivel nacional en los canales Retail, Tradicional y Mayorista.
                      </p>

                      <ul>
                        <li>Eje-cafetero y Antioquia</li>
                        <li>Centro-Oriente</li>
                        <li>Pacífico</li>
                        <li>Llanos</li>
                        <li>Caribe</li>
                        <li>Centro-sur Amazonia</li>
                      </ul>

                      <p>
                        Nos especializamos en el servicio postventa con un excelente soporte técnico para la atención adecuada y oportuna de garantías y una línea de atención al cliente competente para responder a la necesidad el cliente.
                      </p>
                    </div>
                    <div class="tab-pane fade" id="list-3" role="tabpanel" aria-labelledby="list-3-list">
                      <p>
                        La declaratoria de los valores y principios definidos  van a ser trasmitidos a toda la Empresa y a la comunidad en general, a través del siguiente credo:
                      </p>
                      <h5 class="font-weight-bold font-italic text-center">
                        "Somos un equipo comprometido que cuenta con una excelente vocación de servicio y resultados. Actuamos con transparencia y oportunidad. Logramos altos estándares de cumplimiento y promulgamos el respeto entre todos y el entorno que nos rodea".
                      </h5>

                    </div>
                    <div class="tab-pane fade" id="list-4" role="tabpanel" aria-labelledby="list-4-list">
                      <div class="text-center iconos-sig">
                        <br>
                        <a href="<?php echo RUTA_RAIZ ?>documentos/pres.pptx" target="_blank" download="PRESENTACIÓN MODELO CULTURA ORGANIZACIONAL CONSUMER ELECTRONICS GROUP.pptx"><i class="fas fa-download fa-4x"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="pills-quienes-somos" role="tabpanel" aria-labelledby="pills-mision-tab">
              <h3>Misión</h3>
              <p>
                <strong>CONSUMER ELECTRONICS GROUP S.A.S</strong> es una empresa dedicada a la producción y comercialización de artículos electrónicos y electrodomésticos para el hogar, la industria y otros sectores, con los más altos estándares de calidad, tecnología, competitividad, y servicio; estamos ubicados en el Eje Cafetero, ubicación privilegiada como corredor logístico del centro, norte y suroccidente de Colombia.
              </p>
              <p>
                Promovemos relaciones  de   confianza, la mejora continua de los procesos  y el compromiso  de nuestros colaboradores para la satisfacción de nuestros clientes, generando rentabilidad para el bienestar de todos nuestros grupos de interés, y en  permanente armonía con el medio ambiente.
              </p>
              <h3>Visión</h3>
              <p>
                <strong>CONSUMER ELECTRONICS GROUP S.A.S</strong>, para el año 2020 será una empresa certificada y con reconocimiento de marca, que vive su cultura organizacional, enfocada permanentemente en la gestión, desarrollo e innovación de sus procesos, y apasionados por la satisfacción de nuestros clientes.
              </p>
              <p>
                Tendremos mayor cobertura y penetración con soluciones integrales (productos, servicios y asesorías) en Colombia y mercados Latinoamericanos, generando rentabilidad para los grupos de interés en armonía con el medio ambiente.
              </p>
              <h3>Objetivos estratégicos</h3>
              <ol>
                <li><p>Desarrollar integralmente el recurso humano con base en procesos de mejoramiento continuo que garanticen el crecimiento del negocio, la satisfacción de los clientes y el bienestar de sus colaboradores.</p></li>
                <li><p>Implementar procesos efectivos e innovadores que garanticen la competitividad del negocio.</p></li>
                <li><p>Estructurar ofertas de valor efectivas para el mercado que fortalezcan la preferencia de la empresa en los clientes.</p></li>
                <li><p>Fortalecer la orientación al servicio como factor diferenciador de nuestros equipos de trabajo frente a la competencia.</p></li>
                <li><p>Alcanzar niveles de rentabilidad que garanticen la sostenibilidad y el desarrollo continuo del negocio.</p></li>
                <li><p>Implementar un sistema de gestión integrado, que consolide un modelo de cultura por procesos, contribuya a la preservación del medio ambiente mediante la prevención de los impactos ambientales que se generen, fortalezca las condiciones de trabajo y el comportamiento para mejorar la salud y seguridad de los colaboradores, y establezca acciones para la prevención de actividades ilícitas en la cadena de suministro.</p></li>
              </ol>
            </div>
            <div class="tab-pane fade" id="pills-nuestra-cultura" role="tabpanel" aria-labelledby="pills-nuestra-cultura-tab">
              <h3>Competencias</h3>
              <div class="text-center">
                <img class="w-50" src="../img/creser/01.png">
              </div>
              <div class="row mt-4">
                <div class="col-3 text-center">
                  <img class="w-75" src="../img/creser/03.png">
                </div>
                <div class="col-3 text-center">
                  <img class="w-75" src="../img/creser/04.png">
                </div>
                <div class="col-3 text-center">
                  <img class="w-75" src="../img/creser/05.png">
                </div>
                <div class="col-3 text-center">
                  <img class="w-75" src="../img/creser/06.png">
                </div>
              </div>
              <div class="text-center mt-5">
                <img class="w-25" src="../img/creser/02.png">
              </div>
              <div class="row mt-4">
                <div class="col-4 text-center">
                  <img class="w-75" src="../img/creser/07.png">
                </div>
                <div class="col-4 text-center">
                  <img class="w-75" src="../img/creser/08.png">
                </div>
                <div class="col-4 text-center">
                  <img class="w-75" src="../img/creser/09.png">
                </div>
              </div>
              <hr>
              <h3>Valores</h3>
              <p><strong>Honestidad:</strong> Actuar coherentemente con el bien del interés general, actuar de manera clara y sincera en cada actividad de la empresa, de tal manera que siempre se manifieste su conformidad o inconformidad con lo encomendado.</p>

              <p><strong>Respeto:</strong> Entender los deberes y derechos de cada una de las personas que hacen parte de la organización y actuar, siempre, partiendo de la consideración y valoración de la dignidad de la persona humana. </p>

              <p><strong>Liderazgo:</strong> Es la intención de tomar el rol de líder en un equipo; implica un deseo de guiar a otros.</p>

              <p><strong>Responsabilidad:</strong> Capacidad que tiene todo individuo por tomar decisiones morales o racionales por sí mismo sin guía o autoridad superior y la habilidad de dirigir las actuaciones de un grupo y de rendir cuentas de sus actos o de los otros que dependan de él.</p>

              <p><strong>Lealtad:</strong> El trabajo en equipo, el amor por él, la lealtad y la transparencia frente a la organización, son características indispensables de nuestros colaboradores.</p>

              <p><strong>Solidaridad:</strong> Participar colectivamente en la solución de problemas y en el cumplimiento de objetivos.</p>
            </div>

            <div class="tab-pane fade" id="pills-politica-sig" role="tabpanel" aria-labelledby="pills-politica-sig-tab">
              <div class="mt-5 text-center">
                <img src="<?php echo RUTA_RAIZ ?>img/politica-sig.png" class="w-100 w-md-60">
              </div>
            </div>
            <div class="tab-pane fade" id="pills-politicas" role="tabpanel" aria-labelledby="pills-politicas-tab">
              <!--<a href="politicas">Ver Políticas</a>-->
              <div class="container">
                <div class="row justify-content-center">
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="sistemas_integrado_gestion" href="">
                      <i class="fas fa-newspaper fa-6x"></i>
                      <h4>Política sistema integrado de gestión</h4>
                    </a>
                  </div>
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="alcohol_dogras_tabaco" href="">
                      <i class="fas fa-book fa-6x"></i>
                      <h4>Política de Prevención del Consumo de alcohol drogas y tabaco</h4>
                    </a>
                  </div>
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="seguridad_vial" href="">
                      <i class="fas fa-car fa-6x"></i>
                      <h4>Política de seguridad vial</h4>
                    </a>
                  </div>
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="seguridad_informatica" href="">
                      <i class="fas fa-laptop fa-6x"></i>
                      <h4>Política de seguridad informática</h4>
                    </a>
                  </div>
                </div>

                <div class="row justify-content-md-center">
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="firmas_sellos" href="">
                      <i class="fas fa-certificate fa-6x"></i>
                      <h4>Política de firmas y sellos</h4>
                    </a>
                  </div>
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="legalizacion_anticipos" href="">
                      <i class="fas fa-bookmark fa-6x"></i>
                      <h4>Política de legalización de anticipos</h4>
                    </a>
                  </div>
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="politica_privacidad" href="">
                      <i class="fas fa-lock fa-6x"></i>
                      <h4>Política de privacidad</h4>
                    </a>
                  </div>
                  <div class="col-10 col-sm-6 col-md-3 text-center iconos-sig mt-3">
                    <a class="text-decoration-none archivos" id="compras" href="">
                      <i class="fa fa-shopping-cart fa-6x"></i>
                      <h4>Política de compras (bienes e insumos)</h4>
                    </a>
                  </div>
                </div>
              </div>
            </div>
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
    $.ajax({
      url: '<?php echo(RUTA_BASE); ?>app/funciones.php',
      type: 'POST',
      dataType: 'json',
      data: {ejecutar_accion: 'modulo_lista_info', mod_nombre: 'intranet_inicio', mod_tipo: 'intranet'},
      success: function(data){
        $('#sistemas_integrado_gestion').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.intranet_inicio_politica_sistema_integrado_gestion.mod_ruta);
        $('#alcohol_dogras_tabaco').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.intranet_inicio_politica_prevencion_consumer_alcohol_drogar_tabaco.mod_ruta);
        $('#seguridad_vial').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.intranet_inicio_politica_seguridad_vial.mod_ruta);
        $('#seguridad_informatica').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.  intranet_inicio_politica_seguridad_informatica.mod_ruta);
        $('#firmas_sellos').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.intranet_inicio_politica_firmas_sellos.mod_ruta);
        $('#legalizacion_anticipos').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.intranet_inicio_politica_legalizacion_anticipos.mod_ruta);
        $('#politica_privacidad').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.intranet_inicio_politica_privacidad.mod_ruta);
        $('#compras').attr('href', '<?php echo(RUTA_DROPBOX); ?>' + data.intranet_inicio_politica_compras.mod_ruta);
      },
      error: function(){
        alertify.error("Error al traer los daots de politicas.");
      }
    });

    $(".archivos").on("click", function(event){
      event.preventDefault();
      top.$('#cargando').modal("show");
      $('#modalArchivosTitulo').html($("h4", this).text());
      $("#contenidoArchivos").attr("src", $(this).attr("href"));
    });

    cerrarCargando();
  });
</script>
</html>