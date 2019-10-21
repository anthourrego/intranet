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

  require_once($ruta_raiz . 'clases/librerias.php');
  require_once($ruta_raiz . 'clases/sessionActiva.php');

  $usuario = $session->get("usuario");

  $lib = new Libreria;
?>

<!doctype html>
<html lang="es">
  <head>
    <?php  
      echo $lib->metaTagsRequired();
      echo $lib->iconoPag();
    ?>  
    <title>Consumer Electronics Group S.A.S</title>

    <?php  
      echo $lib->jquery();
      echo $lib->bootstrap();
      echo $lib->fontAwesome();
      echo $lib->alertify();
      echo $lib->slideNav2CSS();
      echo $lib->intranet();
      
    ?>
  </head>
  <body class="sidebar-toggled overflow-hidden">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
      <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

        <li class="nav-item no-arrow d-none d-md-block">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <img class="img-profile rounded-circle" src="<?php if($usuario['foto'] != "" OR NULL){echo RUTA_ALMACENAMIENTO . $usuario['foto']; }else{ echo RUTA_ALMACENAMIENTO . "foto-usuario/0.png"; }?>">
            <span><?php echo $usuario['nombre'] ?></span>
          </a>
          <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <!--<a class="collapse-item" href="#">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
              </a>
              <div class="dropdown-divider"></div>-->
              <a class="collapse-item" onclick="cerrarSesion()">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                Cerrar Sesión
              </a>
            </div>
          </div>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">
        
        <!-- Nav Item - Dashboard -->
        <!-- Nav Item - Pages Collapse Menu -->
        

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
          Paginas
        </div>
        <li class="nav-item">
          <a class="nav-link link" target="object-contenido" href="<?php $ruta_raiz ?>paginas/">
            <i class="fas fa-fw fa-home"></i>
            <span>Inicio</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link link" target="object-contenido" href="<?php $ruta_raiz ?>paginas/sig/">
            <i class="fas fa-fw fa-book "></i>
            <span>S.I.G</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link link" target="object-contenido" href="<?php $ruta_raiz ?>paginas/gestion_humana/">
            <i class="fas fa-fw fa-diagnoses"></i>
            <span>Gestión Humana</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link link" target="object-contenido" href="<?php $ruta_raiz ?>paginas/nosotros.php">
            <i class="fas fa-fw fa-list"></i>
            <span>Nosotros</span>
          </a>
        </li>
        

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
          <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

      </ul>
      <!-- End of Sidebar -->

      <!-- Content Wrapper -->
      <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

          <!-- Topbar -->
          <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow no-arrow d-md-none">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
              <i class="fa fa-bars"></i>
            </button>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">

              <div class="topbar-divider d-none d-sm-block"></div>

              <!-- Nav Item - User Information -->
              <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $usuario['nombre']; ?></span>
                  <img class="img-profile rounded-circle" src="http://consumerelectronicsgroup.com/intranet/img/usuarios/02.jpg">
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                  <a class="dropdown-item" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Cerrar Sesión
                  </a>
                </div>
              </li>

            </ul>

          </nav>
          <!-- End of Topbar -->

          <!-- Begin Page Content -->
          <!--<div id="contenido">
            
          </div>-->
          <object type="text/html" id="object-contenido" name="object-contenido" data="" class="w-100 vh-100 border-0"></object>
          <!--<iframe id="iframe" class="w-100" src="../admin2/"></iframe>-->
          <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->
        <!-- Footer -->
        <!--<footer class="sticky-footer bg-white">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright &copy; Consumer Electronics Group S.A.S 2019</span>
            </div>
          </div>
        </footer>-->
        <!-- End of Footer -->
      </div>
      <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    
    
    <!-- Modal de Cargando -->
    <div class="modal fade modal-cargando" id="cargando" tabindex="1" role="dialog" aria-labelledby="cargandoTitle" aria-hidden="true" data-keyboard="false" data-focus="true" data-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="box-loading">
          <div class="loader">
            <div class="loader-1">
              <div class="loader-2">
              </div>
            </div>
          </div>
          <div>
            <img class="w-50" src="<?php echo($ruta_raiz); ?>img/logo.png" alt="">
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Sesion Cerrada -->
    <div class="modal fade" id="cerrarSession" tabindex="-1" role="dialog" aria-labelledby="cerrarSessionTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <i class="fas fa-exclamation fa-7x text-warning mt-3 mb-3"></i>
            <h2>Lo sentimos, la sesión ha caducado</h2>
            Favor ingresar nuevamente, Gracias.
          </div>
          <div class="modal-footer d-flex justify-content-center">
            <a class="btn btn-primary" href="<?php echo $ruta_raiz; ?>">Cerrar <i class="fas fa-sign-out-alt"></i></a>
          </div>
        </div>
      </div>
    </div>
  </body>

  <?php  
    echo $lib->slideNav2JS();
  ?>
  <script type="text/javascript">
    $("#cargando").modal("show");
    var idleTime = 0; 
    $(function(){
      //Tiempo en que valida la session
      window.idleInterval = setInterval(validarSession, 600000); // 10 minute 
      // No permitimos utilizar el botón otras
      /*window.location.hash="no-back-button";
      window.location.hash="Again-No-back-button" //chrome
      window.onhashchange=function(){window.location.hash="no-back-button";}*/

      $(".nav-item").on("click", function(){
        $(".nav-item").removeClass("active");
        $(this).addClass("active");
      });

      if (localStorage.url == null) {
        $("#object-contenido").attr("data", "paginas/");
      }else{
        $("#object-contenido").attr("data", localStorage.url);
      }

      setTimeout(function() {
        $("#cargando").modal("hide");
      }, 1000);

      /*$(".link").on("click", function(event){
        $("#cargando").modal("show");
        event.preventDefault();
        //localStorage.url = $(this).attr("href");
        $("#object-contenido").attr("data", $(this).attr("href"));
      });*/
    });

    function validarSession(){
      $.ajax({
        type: 'POST',
        url: "<?php echo $ruta_raiz ?>ajax/usuarios.php",
        data: {accion: "sessionActiva"},
        success: function(data){
          if (data == 0) {
            localStorage.removeItem("url");
            $("#cerrarSession").modal("show");
          }else{
            console.log("Session activa.");
          }
        },
        error: function(data){
          alertify.error("No se ha podido validar la session");
        }
      });
    }

    function cerrarSesion(){
      localStorage.removeItem("url");
      window.location.href='<?php echo $ruta_raiz ?>clases/sessionCerrar';
    }
  </script>
</html>