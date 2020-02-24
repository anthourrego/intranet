<?php  
  header("Access-Control-Allow-Origin:*");
 

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
  
  $car_nombre=$usuario['car_nombre'];
  $dep_nombre=$usuario['dep_nombre']; 
  $historico=0;
  if(@$_REQUEST['historico']){
  	$historico=1;
  } 
  
  
  $ruta_documentos = array();

  $lib = new Libreria;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Solicitud Compra Funcionario</title>
  
  <?php  
  	echo $lib->metaTagsRequired();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->intranet();
	echo $lib->cambioPantalla();
	echo $lib->datatables();
	echo $lib->alertify();		
  ?>
</head>
<body>
	
	<div class="container contenedor_general">
		<div class="row titulo_general_row">
			<div class="col-12 titulo_general">
				<h1>Solicitudes de Compra Funcionarios</h1>	
			</div>
		</div>
		<br>
	</div>
	<div class="container">
	
		<div class="table-responsive">
			<table class="table table-hover" id="tabla_solicitudes_funcionario">
				
			</table>
		</div>
		
	</div>
		


	<div class="modal" tabindex="-1" role="dialog" id="modal_visualizar_compra_funcionario" >
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title"></h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      	<iframe class="w-100" id="contenido_visualizar_compra_funcionario" style="width:100%;height: 80vh; border: 0px;"></iframe>
	      </div>
	    </div>
	  </div>
	</div>	
	
</body>
</html>		
<script>
	$(function(){
		cerrarCargando();
	});

	function iniciar_consulta(){
		//top.$("#cargando").modal("show");
		setTimeout(function(){	
					
			$.ajax({
				type:'POST',
				dataType: 'json',
				url: "<?php echo(direccionIPRuta()); ?>funcionario_compra/ejecutar_acciones.php",			
				data: {
					ejecutar_accion:'data_solicitud_reporte_revision',
					car_nombre:'<?php echo($car_nombre); ?>',
					dep_nombre:'<?php echo($dep_nombre); ?>',
					historico:<?php echo($historico); ?>
				},
				success:function(data){
					top.$("#cargando").modal("hide");
					var obj = JSON.parse(data.data);
					var datatable = $( '#tabla_solicitudes_funcionario' ).DataTable();
									
					datatable.clear();
			   		datatable.rows.add(obj);
			   		datatable.draw();
			   		
			   		$('[data-toggle="tooltip"]').tooltip();
				}	
			});	

		}, 500);	
	}


	$(document).ready(function(){

	    $('#tabla_solicitudes_funcionario').DataTable( {
	        columns: [
				{ title: "Acciones" },
				{ title: "Estado" },
				{ title: "Fecha" },
            	{ title: "Funcionario" },
            	{ title: "Ver" }       	
	        ],
	        ordering: false,
	        dom: 'Bfrtip',
	        buttons: [
		    {
		      extend: 'excel',
		      text: 'Excel',
		      className: 'exportExcel btn btn-success excel_btn borde_card',
		      filename: 'Reporte Solicitudes de Compra Funcionarios',
		      exportOptions: {
		        modifier: {
		          page: 'all'
		        }
		      }	     
		    }]	        
	    } );


	 	$(".excel_btn").html('Excel <i class="far fa-file-excel"></i>');

		iniciar_consulta();
		
		
		
		
		$(document).on('click','.ver_detalle_compra',function(){
			var funco_id=$(this).attr('funco_id');
			
			top.$("#cargando").modal("show");
			var enlace='compra_detalle.php?funco_id='+funco_id;
			$('#modal_visualizar_compra_funcionario').modal('show');
			$("#contenido_visualizar_compra_funcionario").attr("src", enlace);				
			
		});
		
		

		$('[data-toggle="tooltip"]').tooltip();
		
		
		$(document).on('click','.btn_revisar_precio',function(){
			var funco_id=$(this).attr('funco_id');
			top.$("#cargando").modal("show");
			var enlace='compra_revisar_precio.php?funco_id='+funco_id;
			$('#modal_visualizar_compra_funcionario').modal('show');
			$("#contenido_visualizar_compra_funcionario").attr("src", enlace);					
		});
		
		$(document).on('click','.btn_revisar_capacidad',function(){
			var funco_id=$(this).attr('funco_id');
			top.$("#cargando").modal("show");
			var enlace='compra_revisar_capacidad.php?funco_id='+funco_id;
			$('#modal_visualizar_compra_funcionario').modal('show');
			$("#contenido_visualizar_compra_funcionario").attr("src", enlace);					
		});
		
		$(document).on('click','.btn_asignar_serial',function(){
			var funco_id=$(this).attr('funco_id');
			top.$("#cargando").modal("show");
			var enlace='compra_asignar_serial.php?funco_id='+funco_id;
			$('#modal_visualizar_compra_funcionario').modal('show');
			$("#contenido_visualizar_compra_funcionario").attr("src", enlace);					
		});		
		
		$(document).on('click','.ver_compra_timeline',function(){
			var funco_id=$(this).attr('funco_id');
			
			top.$("#cargando").modal("show");
			var enlace='compra_timeline.php?funco_id='+funco_id;
			$('#modal_visualizar_compra_funcionario').modal('show');
			$("#contenido_visualizar_compra_funcionario").attr("src", enlace);				
			
		});		
				
		
	});
</script>