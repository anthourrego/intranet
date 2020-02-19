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
	<title>Consumer Electronocs Group S.A.S</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->datatables();
    echo $lib->alertify();
    echo $lib->echarts();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container mt-5">
    <table id="tabla" class="table table-bordered bg-light table-hover table-sm">
      <thead>
        <tr>
          <th class="text-center">Nombre</th>
          <th class="text-center">Estado</th>
          <th class="text-center">Cantidad</th>
        </tr>
      </thead>
      <tbody id="contenido">

      </tbody>
    </table>

    <div id="graficos" class="row w-100 d-none mt-5">
			<div class="col-6">
				<div id="grafico_barras_general" style="width: 100%;height:400px;"></div>
			</div>
      <div class="col-6">
				<div id="grafico_barras_lideres" style="width: 100%;height:400px;"></div>
			</div>
		</div>
  </div>
</body>
<?php  
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    cargarTabla();
  });

  function cargarTabla(){
    $.ajax({
      type: "POST",
      url: "<?php echo(direccionIPRuta()); ?>ajax/usuarios.php",
      dataType: 'json',
      data: {
        accion: "PersonasAreas", 
        idDep: <?php echo($_GET['idArea']); ?>, 
        periodo: <?php echo($_GET['idPeriodo']); ?> 
      },
      success: function(data){
        $("#contenido").empty();

        for (let i = 0; i < data.cantidad_registros; i++) {
          if(data[i].competencia_creser == 0){
            $("#contenido").append(`
              <tr class="alert-danger">
                <td>${data[i].fun_nombre_completo}</td>
                <td>Sin definir competencia</td>
                <td>${data[i].cantidad_intentos}</td>
              </tr>
            `);
          }else if(data[i].cantidad_intentos > 0){
            $("#contenido").append(`
              <tr onclick="encuesta(${data[i].fun_id}, ${data[i].competencia_creser})" class="alert-success">
                <td>${data[i].fun_nombre_completo}</td>
                <td>Realizado</td>
                <td>${data[i].cantidad_intentos}</td>
              </tr>
            `);
          }else{
            $("#contenido").append(`
              <tr onclick="encuesta(${data[i].fun_id}, ${data[i].competencia_creser})">
                <td>${data[i].fun_nombre_completo}</td>
                <td>No realizado</td>
                <td>${data[i].cantidad_intentos}</td>
              </tr>
            `);
          }
        }
        // =======================  Data tables ==================================
        definirdataTable("#tabla");
        datos = ["ORIENTACION AL SERVICIO", "TRABAJO EN EQUIPO", "EFECTIVIDAD", "INNOVACION Y GESTION DEL CAMBIO"]
        valores = [((data.orientacion_al_servicio*100)/data.orientacion_al_servicio_total).toFixed(2), ((data.trabajo_en_equipo*100)/data.trabajo_en_equipo_total).toFixed(2), ((data.efectividad*100)/data.efectividad_total).toFixed(2), ((data.innovacion_y_gestion_del_cambio*100)/data.innovacion_y_gestion_del_cambio_total).toFixed(2)];

        if (data.cont_general > 0) {
          $("#graficos").removeClass("d-none");
          graficos("Desempeño", datos, valores, data.cont_general, "grafico_barras_general");

          if (data.cont_lideres > 0) {
            datos_lider = ["DESARROLLO DE SI MISMO Y DE OTROS", "TOMA DE DECISIONES ESTRATEGICAS ", "ORIENTACION AL LOGRO"];
            valores_lider = [((data.desarrollo_de_si_mismo_y_de_otros*100)/data.desarrollo_de_si_mismo_y_de_otros_total).toFixed(2), ((data.toma_de_decisiones_estrategicas*100)/data.toma_de_decisiones_estrategicas_total).toFixed(2), ((data.orientacion_al_logro*100)/data.orientacion_al_logro_total).toFixed(2)];

            graficos("Lideres", datos_lider, valores_lider, data.cont_lideres, "grafico_barras_lideres");
          }
        }


      },
      error: function(){
        alertify.error("No se ha podido traer la lista");
      }
    });
  }

  function encuesta(id, idCompetencia){
    if(idCompetencia != null){
      if (idCompetencia == 2) {
        var atributo = 10;      
      } else {
        var atributo = 31;
      }
      //window.location.href = 'encuesta?et_id=' + idCompetencia + '&id_usu='+id;
      top.$("#cargando").modal("show");
      window.location.href = 'encuesta.php?et_id=' + idCompetencia + '&filtro_atr=' + atributo + '|' + id;
    }else{
      alertify.error("Debes de definir los atributos de creser.");
    }
  }

  function graficos(titulo, datos, valores, cantidad, idGrafico){
    require.config({
      paths: {
        echarts: '<?php echo($ruta_raiz); ?>lib/echarts'
      }
    });
    require(['echarts','echarts/chart/bar'],// require the specific chart type        
      
    function (ec) {
      var myChart = ec.init(document.getElementById(idGrafico));

      var option = {
        title : {
          text: titulo,
          subtext: "Personas " + cantidad,
          x:'center'
        },
        tooltip : {
          trigger: 'axis',
          axisPointer : {            
              type : 'shadow' 
          },   
          formatter: "{b} : {c}%"                 
        },
        calculable : true,
        xAxis : [
          {
            nameTextStyle:{
              color: '#000000',
              fontWeight:'bold'
            },
            nameLocation:'end',      
            name:'',                        
            type : 'category',
            axisLabel:{
              rotate:18,
              textStyle:{
                fontSize:9
              }
            },                        
            data : datos
          }
        ],
        yAxis : [
          { 
            nameTextStyle:{
              color: '#000000',
              fontWeight:'bold'
            },
            nameLocation:'end',
            name:'%',                        
            type : 'value'
          }
        ],
        series : [
          {
            type:"bar",
            //barWidth: 30,
            itemStyle: {
              normal: {
                color: function(params) {
                  if(params.dataIndex % 2 == 0){ //es par
                    return('#007bff');
                  }else{
                    return('#17a2b8');
                  }        
                },
                label: {
                  show: true,
                  position: 'top',
                  formatter: '{c}%'
                }
              }
            },                                
            data: valores
          }                             
        ]
      };
      myChart.setOption(option);
    } //fin function ec
    );
  }
</script> 
</html>