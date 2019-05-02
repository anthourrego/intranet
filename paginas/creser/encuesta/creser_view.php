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

include_once($ruta_raiz . 'clases/Conectar.php');
include_once($ruta_raiz . 'clases/librerias.php');
include_once($ruta_raiz . 'clases/sessionActiva.php');
include_once($ruta_raiz . 'clases/funciones_generales.php');


if(!@$_REQUEST['et_id']){
	header('location: '.RUTA_RAIZ.'central');
	die();
}

$bd=new Bd();
$bd->conectar();


$et_id=@$_REQUEST['et_id'];

$sql_encuesta_tipo="SELECT * FROM encuesta_tipo WHERE et_estado=1 AND et_id=".$et_id;
$consulta_encuesta_tipo=$bd->consulta($sql_encuesta_tipo);
$titulo_encuesta=@$consulta_encuesta_tipo[0]['et_titulo'];


if($consulta_encuesta_tipo[0]['et_libreria']){
	$vlibrerias=explode(',', $consulta_encuesta_tipo[0]['et_libreria']);
	for ($i=0; $i < count($vlibrerias); $i++) {
		if(file_exists($ruta_raiz.$vlibrerias[$i])){
			include_once($ruta_raiz.$vlibrerias[$i]);
		}
	}
}

$sql_atr="SELECT * FROM encuesta_atr WHERE ea_estado=1 AND et_fk=".$et_id;
$consulta_atr=$bd->consulta($sql_atr);

$data_columns='[';
for ($i=0; $i < $consulta_atr['cantidad_registros']; $i++) {
	$data_columns.='{ title: "'.$consulta_atr[$i]['ea_titulo'].'" },';
}
$data_columns.='{ title: "Acciones" }';
$data_columns.=']';


if(@$_REQUEST['filtro_atr']){  //&filtro_atr=3|steven@alkosto.com
	$vfiltro_atr=explode('|', @$_REQUEST['filtro_atr']);
	$filtro_atr_ea_id=$vfiltro_atr[0];
	$filtro_atr_ea_valor=$vfiltro_atr[1];

	$sql_tipo_atr="SELECT ea_type FROM encuesta_atr WHERE ea_id=".$filtro_atr_ea_id;
	$consulta_tipo_atr=$bd->consulta($sql_tipo_atr);	
	$condicion_adicional_filtro_atr='';
	switch ($consulta_tipo_atr[0]['ea_type']) {
		case 'varchar':
			$condicion_adicional_filtro_atr="b.erea_valor_varchar='".$filtro_atr_ea_valor."'";
			break;
		case 'datetime':
					
			break;		
		case 'int':
			$condicion_adicional_filtro_atr="b.erea_valor_int=".$filtro_atr_ea_valor."";				
			break;	
		case 'text':
			$condicion_adicional_filtro_atr="b.erea_valor_text='".$filtro_atr_ea_valor."'";
			break;	
	}
	$sql_encuesta_respuesta="SELECT a.ere_id FROM encuesta_respuesta a, encuesta_respuesta_atr b WHERE a.ere_id=b.ere_fk AND a.et_fk=".$et_id." AND b.ea_fk=".$filtro_atr_ea_id." AND  ".$condicion_adicional_filtro_atr." GROUP BY a.ere_id";
	$consulta_encuesta_respuesta=$bd->consulta($sql_encuesta_respuesta);	
}else{

	$sql_encuesta_respuesta="SELECT * FROM encuesta_respuesta WHERE et_fk=".$et_id;
	$consulta_encuesta_respuesta=$bd->consulta($sql_encuesta_respuesta);	
}

$data_data="";
for ($i=0; $i < $consulta_encuesta_respuesta['cantidad_registros']; $i++) {
	$ere_id=$consulta_encuesta_respuesta[$i]['ere_id'];

	$sql_encuesta_respuesta_atr="SELECT * FROM encuesta_respuesta_atr WHERE ere_fk=".$ere_id;
	$consulta_encuesta_respuesta_atr=$bd->consulta($sql_encuesta_respuesta_atr);
	$data_data.='[';

	for ($ea=0; $ea < $consulta_atr['cantidad_registros']; $ea++) {

		for ($erea=0; $erea < $consulta_encuesta_respuesta_atr['cantidad_registros']; $erea++) {

			if($consulta_atr[$ea]['ea_id']==$consulta_encuesta_respuesta_atr[$erea]['ea_fk']){
				
				if($consulta_atr[$ea]['ea_funcion_view'] && function_exists($consulta_atr[$ea]['ea_funcion_view'])){
					$data_data.='"'.$consulta_atr[$ea]['ea_funcion_view'](fecha_db_obtener(@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_datetime'],'Y-m-d H:i:s').@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_varchar'].@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_int'].@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_text']).'",';
				}else{
					$data_data.='"'.fecha_db_obtener(@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_datetime'],'Y-m-d H:i:s').@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_varchar'].@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_int'].@$consulta_encuesta_respuesta_atr[$erea]['erea_valor_text'].'",';
				}
				
				
				
			}	
		}	
	}
	$data_data.='"<a class=\"btn btn-light\" href=\"creser_view_rta.php?et_id='.$et_id.'&ere_id='.$ere_id.'&idUsu='. @$filtro_atr_ea_valor .'\">Ver</a>"';
	$data_data.='],';

}

$data_data=trim($data_data,',');

$data_data='['.$data_data.']';
$bd->desconectar();

$session = new Session();

$usuario = $session->get("usuario");

$lib = new Libreria;
// Desde este aquí se agregan los script adicionales que puede ser utiles en esta pestaña
?>
<!DOCTYPE html>
<html>
<head>
	<title>Consumer Electronocs Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->datatables();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container-fluid mt-3">
    <a class="btn btn-secondary link" href="<?php echo RUTA_RAIZ ?>paginas/creser"><i class="fas fa-arrow-left"></i> Atrás</a>
    <hr>
  </div>
	<div class="mt-2">
  
		<h1 class="text-center"><?php echo($titulo_encuesta); ?></h1>

    <div class="container-fluid">
      <a class="btn btn-primary link" href="<?php echo RUTA_RAIZ ?>paginas/creser/encuesta/?et_id=<?php echo @$_GET['et_id'] ?>&id_usu=<?php echo @$filtro_atr_ea_valor ?>">Evaluar</a>
      <div class="table-responsive">
        <table class="table table-hover" id="tabla_visor_encuesta">

        </table>
      </div>  
    </div>
	
  </div>

</body>
<?php  
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    $('#tabla_visor_encuesta').DataTable( {
	    "language": {
	      "decimal":        "",
	      "emptyTable":     "No hay datos disponibles en la tabla",
	      "info":           "Mostrando _START_ desde _END_ hasta _TOTAL_ registros",
	      "infoEmpty":      "Mostrando 0 desde 0 hasta 0 registros",
	      "infoFiltered":   "(Filtrado por _MAX_ total)",
	      "infoPostFix":    "",
	      "thousands":      ",",
	      "lengthMenu":     "Mostrar _MENU_",
	      "loadingRecords": "Cargando...",
	      "processing":     "Procesando...",
	      "search":         "Buscar:",
	      "zeroRecords":    "No se encontraron registros",
	      "paginate": {
	        "first":      "Primero",
	        "last":       "Ãšltimo",
	        "next":       "Siguiente",
	        "previous":   "Anterior"
	      }
	    },
	    stateSave: true,
	    "processing": true,
	    columns: <?php echo($data_columns); ?>,
	    data:<?php echo($data_data); ?>,
	    ordering: false,
	    dom: 'Bfrtip',
	    buttons: [
	       'excel'
	    ]
	  });
  });
</script>
</html>
