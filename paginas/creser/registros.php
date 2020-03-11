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
    echo $lib->bootstrapTempusDominus();
    echo $lib->alertify();
    echo $lib->fontAwesome();
    echo $lib->fontAwesome4();
    echo $lib->datatables();
    echo $lib->echarts();
    echo $lib->intranet();
  ?>
</head>
<body>
	<div class="container mt-5">
    <div class="mb-4 d-flex justify-content-between">
      <div>
        <select class="form-control" id="select_periodos">
          <option disabled selected>Seleccione un periodo...</option>  
        </select>
      </div>
      <button class="btn btn-primary" data-toggle="modal" data-target="#modalPeriodos"><i class="fas fa-chalkboard-teacher"></i> Periodos</button> 
    </div>

    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-registro-tab" data-toggle="tab" href="#nav-registro" role="tab" aria-controls="nav-registro" aria-selected="true">Registro</a>
        <a class="nav-item nav-link" id="nav-proyecto-tab" data-toggle="tab" href="#nav-proyecto" role="tab" aria-controls="nav-proyecto" aria-selected="false">Proyectos</a>
      </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active pt-4 pb-5" id="nav-registro" role="tabpanel" aria-labelledby="nav-registro-tab">
        
        <table id="tabla" class="table table-bordered bg-light table-hover table-sm ">
          <thead>
            <tr>
              <th class="text-center w-50">Área</th>
              <th class="text-center">Usuarios</th>
              <th class="text-center">Completados</th>
            </tr>
          </thead>
          <tbody id="contenido"></tbody>
        </table>

        <div id="graficos" class="row mb-5 d-none">
          <div id="pie_general" class="col-6 d-none mt-5">
            <div class="card">
              <div class="card-body">
                <div id="grafico_pie_general" style="width: 100%;height:400px;"></div>
              </div>
            </div>
          </div>
          <div id="pie_lideres" class="col-6 d-none mt-5">
            <div class="card">
              <div class="card-body">
                <div id="grafico_pie_lideres" style="width: 100%;height:400px;"></div>
              </div>
            </div>
          </div>
          <div id="barras_general" class="col-6 d-none mt-5">
            <div class="card">
              <div class="card-body">
                <div id="grafico_barras_general" style="width: 100%;height:400px;"></div>
              </div>
            </div>
          </div>
          <div id="barras_lideres" class="col-6 d-none mt-5">
            <div class="card">
              <div class="card-body">
                <div id="grafico_barras_lideres" style="width: 100%;height:400px;"></div>
              </div>
            </div>
          </div>
        </div>
        
      </div>

      <div class="tab-pane fade pt-4 pb-5" id="nav-proyecto" role="tabpanel" aria-labelledby="nav-proyecto-tab">
        <table id="tabla-proyecto" class="table table-bordered bg-light table-hover table-sm ">
          <thead>
            <tr>
              <th class="text-center">Área</th>
              <th class="text-center">Usuarios</th>
              <th class="text-center w-25">Formación</th>
              <th class="text-center w-25">Proyecto</th>
            </tr>
          </thead>
          <tbody id="contenido-proyecto"></tbody>
        </table>
      </div>
    </div>
  </div>


  <div id="modalPeriodos" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-chalkboard-teacher"></i> Periodos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>
            <button class="btn btn-primary" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseCrearPeriodo" aria-expanded="false" aria-controls="collapseCrearPeriodo"><i class="fas fa-plus"></i> Crear</button>
          </p>
          <div class="collapse mb-3" id="collapseCrearPeriodo">
            <div class="card card-body">
              <form id="formCrearPeriodo">
                <input type="hidden" name="accion" value="crearPeriodo">
                <input type="hidden" name="idUsuario" value="<?php echo($usuario['id']); ?>">
                <div class="form-row">
                  <div class="col-3">
                    <label for="">Fecha Inicio:</label>
                    <div class="input-group date" id="inicioPeriodo" data-target-input="nearest">
                      <input type="text" name="periodoInicio" id="periodoInicio" class="form-control datetimepicker-input" data-target="#inicioPeriodo"/>
                      <div class="input-group-append" data-target="#inicioPeriodo" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-3">
                    <label for="">Fecha Fin:</label>
                    <div class="input-group date" id="finalPeriodo" data-target-input="nearest">
                      <input type="text" name="peridodFinal" id="peridodFinal" class="form-control datetimepicker-input" data-target="#finalPeriodo"/>
                      <div class="input-group-append" data-target="#finalPeriodo" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-4">
                    <label for="">Descripción:</label>
                    <textarea class="form-control" name="peridoDescripcion" cols="30" rows="1"></textarea>
                  </div>
                  <div class="col-2 d-flex align-items-end justify-content-center">
                    <button class="btn btn-success"><i class="far fa-paper-plane"></i> Enviar</button>
                  </div> 
                </div>
              </form>
            </div>
          </div>

          <table id="tablaPeriodos" class="mt-3 table table-bordered bg-light table-hover table-sm">
            <thead>
              <tr>
                <th class="text-center">Periodo</th>
                <th class="text-center">Fecha Inicio</th>
                <th class="text-center">Fecha Fin</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Acciones</th> 
              </tr>
            </thead>
            <tbody id="contenidoPeriodos">

            </tbody>
          </table>
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
    validarEdicion = 0;

    $('#modalPeriodos').on('show.bs.modal', function (e) {
      validarEdicion = 0;
      // Rango de fecha de usuario 
      $('#inicioPeriodo').datetimepicker({
        format: 'L',
      });
  
      $('#finalPeriodo').datetimepicker({
        format: 'L',
      });
      
      $("#inicioPeriodo").on("change.datetimepicker", function (e) {
        $('#finalPeriodo').datetimepicker('minDate', e.date);
      });
      $("#finalPeriodo").on("change.datetimepicker", function (e) {
        $('#inicioPeriodo').datetimepicker('maxDate', e.date);
      });

      cargarTablaPeriodos();
    });


    //Formmulario para crear periodos
    $("#formCrearPeriodo").submit(function(e){
      e.preventDefault();
      $.ajax({
        url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/creser.php',
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: new FormData(this),
         success: function(data){
          if(data == 'true'){
            $("#collapseCrearPeriodo").collapse('hide');
            alertify.success("Se ha creado el periodo.");
            listaPeriodos();
            cargarTablaPeriodos();
          }else{
            alertify.error("No se ha podido crear el peridodo.");
          }
        },
        error: function(){
          alertify.error("No se ha podido crear.");
        },
        complete: function(){

        }
      });
    });

    listaPeriodos();

    //Al cambiar el select cambia los datos de las tabla de las áreas
    $("#select_periodos").on("change", function(){
      cargarTabla($(this).val());
      cargarProyectosyFormacion($(this).val());
    });
  });

  function cargarTabla(ultimoPeriodo){
    top.$("#cargando").modal("show");
    $.ajax({
      type: "POST",
      url: "<?php echo(RUTA_CONSULTAS); ?>ajax/usuarios.php",
      dataType: 'json',
      data: {
        accion: "areas", 
        periodo: ultimoPeriodo 
      },
      success: function(data){
        $("#tabla").dataTable().fnDestroy();
        if(data.success == true){
          console.log(data.msj);
          var total_usuarios = 0;
          $("#contenido").empty();
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            total_usuarios += data.msj[i].usuarios_total 
            if(data.msj[i].usuarios_total > 0){
              $("#contenido").append(`
                <tr onClick="redireccionar(${data.msj[i].dep_id}, ${ultimoPeriodo})">
                  <td>${data.msj[i].dep_tag}</td>
                  <td class="text-center">${data.msj[i].usuarios_total}</td>
                  <td class="text-center">${data.msj[i].usuarios_realizado}</td>
                </tr>
              `);
            }
          }
          
          $("#graficos").addClass("d-none");

          if (total_usuarios > 0) {
            $("#graficos").removeClass("d-none");
            $("#pie_general").removeClass("d-none");

            datos_pie = ["Pendientes", "Completados"];
            valores_pie_general = [{value: (total_usuarios - data.msj.cont_general), name: 'Pendientes'}, {value: data.msj.cont_general, name: 'Completados'}];
            
            torta("grafico_pie_general", "General", "Personas " + total_usuarios, datos_pie, valores_pie_general);

            if(data.msj.cont_lideres_total > 0){
              $("#pie_lideres").removeClass("d-none");
              valores_pie_lideres = [{value: (data.msj.cont_lideres_total - data.msj.cont_lideres), name: 'Pendientes'}, {value: data.msj.cont_lideres, name: 'Completados'}]
            
              torta("grafico_pie_lideres", "Lideres", "Personas " + data.msj.cont_lideres_total, datos_pie, valores_pie_lideres)
            }
            
            if (data.msj.cont_general > 0) {
              $("#barras_general").removeClass("d-none");
              datos = ["ORIENTACION AL SERVICIO", "TRABAJO EN EQUIPO", "EFECTIVIDAD", "INNOVACION Y GESTION DEL CAMBIO"]
              datos_pie = ["Faltan", "Completados"];
              valores_pie = [{value: (total_usuarios - data.msj.cont_general), name: 'Faltan'}, {value: data.msj.cont_general, name: 'Completados'}]
              valores = [((data.msj.orientacion_al_servicio*100)/data.msj.orientacion_al_servicio_total).toFixed(2), ((data.msj.trabajo_en_equipo*100)/data.msj.trabajo_en_equipo_total).toFixed(2), ((data.msj.efectividad*100)/data.msj.efectividad_total).toFixed(2), ((data.msj.innovacion_y_gestion_del_cambio*100)/data.msj.innovacion_y_gestion_del_cambio_total).toFixed(2)];
              
              graficos("Desempeño", datos, valores, data.msj.cont_general, "grafico_barras_general");
              
              if (data.msj.cont_lideres > 0) {
                $("#barras_lideres").removeClass("d-none");

                $("#graficos_general").addClass("d-flex");
                datos_lider = ["DESARROLLO DE SI MISMO Y DE OTROS", "TOMA DE DECISIONES ESTRATEGICAS ", "ORIENTACION AL LOGRO"];
                valores_lider = [((data.msj.desarrollo_de_si_mismo_y_de_otros*100)/data.msj.desarrollo_de_si_mismo_y_de_otros_total).toFixed(2), ((data.msj.toma_de_decisiones_estrategicas*100)/data.msj.toma_de_decisiones_estrategicas_total).toFixed(2), ((data.msj.orientacion_al_logro*100)/data.msj.orientacion_al_logro_total).toFixed(2)];
                
                graficos("Lideres", datos_lider, valores_lider, data.msj.cont_lideres, "grafico_barras_lideres");
              }
            }
          }
        }else{  
          alertify.error(data.msj);
        }
        // =======================  Data tables ==================================
        definirdataTable("#tabla");
      },
      error: function(){
        alertify.error("No se ha podido traer los datos de la tabla.");
      },
      complete: function(){
        cerrarCargando();
      }
    });
  }

  function cargarProyectosyFormacion(idPeridodo){
    $.ajax({
      type: "POST",
      url: '<?php echo(RUTA_CONSULTAS); ?>ajax/usuarios.php',
      dataType: 'json',
      data: {
        accion: "registroProyectoyFormacion",
        periodo: idPeridodo
      },
      success: function(data){
        $("#tabla-proyecto").dataTable().fnDestroy();
        $("#contenido-proyecto").empty();
        if (data.success == true) {
          for (let i = 0; i < data.msj.cantidad_registros; i++) {
            $("#contenido-proyecto").append(`
              <tr>
                <td>${data.msj[i].area}</td>
                <td>${data.msj[i].nombre_completo}</td>
                <td>${data.msj[i].formacion}</td>
                <td>${data.msj[i].proyecto}</td>
              </tr>
            `);
          }
        }else{
          alertify.error(data.msj);
        }

        // =======================  Data tables ==================================
        definirdataTable("#tabla-proyecto");
      },
      error: function(){
        alert("No se ha podido cargar la lista de formacion.");
      },
      complete: function(){
        cerrarCargando();
      }
    });
  }


  function cargarTablaPeriodos(){
    $.ajax({
      type: "POST",
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/creser.php',
      dataType: 'json',
      data: {accion: "listaPeriodos"},
      success: function(data){
        $("#tablaPeriodos").dataTable().fnDestroy();
        if(data.cantidad_registros > 0){
          $("#contenidoPeriodos").empty();
          for (let i = 0; i < data.cantidad_registros; i++) {
            peridoInicio = moment(data[i].cp_fecha_inicio).format('L');
            periodoFinal = moment(data[i].cp_fecha_fin).format("L");
            
            if (i == data.cantidad_registros-1) {
              final = `<td>
                        <button id="btnEditarPeriodo${data[i].cp_id}" onClick="editarPeriodo(${data[i].cp_id})" class="btn btn-success"><i class="far fa-edit"></i> Editar</button>
                        <button id="btnGuardarPeriodo${data[i].cp_id}" onClick="actualizarPeriodo(${data[i].cp_id})" disabled class="btn btn-primary btnGuardarPeriodo"><i class="far fa-save"></i> Guardar</button>
                      </td>`
            }else{
              final = `<td></td>`
            }

            $("#contenidoPeriodos").append(`
              <tr class="text-center">
                <td>${data[i].cp_id}</td>
                <td>
                  <div class="row">
                    <div class="col-12">
                      <input type="text" disabled class="form-control datetimepicker-input" value="${peridoInicio}" id="incioPeriodo${data[i].cp_id}" data-toggle="datetimepicker" data-target="#incioPeriodo${data[i].cp_id}"/>
                    </div>
                  </div>
                </td>
                <td> 
                  <div class="row">
                    <div class="col-12">
                      <input type="text" disabled class="form-control datetimepicker-input" value="${periodoFinal}" id="finalPeriodo${data[i].cp_id}" data-toggle="datetimepicker" data-target="#finalPeriodo${data[i].cp_id}"/>
                    </div>
                  </div>
                </td>
                <td>
                  <textarea disabled class="form-control" id="peridoDescripcion${data[i].cp_id}" name="peridoDescripcion${data[i].cp_id}" cols="30" rows="1">${data[i].cp_descripcion}</textarea>
                </td>
                ${final}
              </tr>
            `);
          }

          //
          $("#inicioPeriodo").datetimepicker('date', new Date(moment(periodoFinal, 'DD/MM/YYYY', true).format()));
          $("#finalPeriodo").datetimepicker('date', new Date(moment(periodoFinal, 'DD/MM/YYYY', true).format()));
          $('#inicioPeriodo').datetimepicker('minDate', new Date(moment(periodoFinal, 'DD/MM/YYYY', true).format()));
          $('#inicioPeriodo').datetimepicker('maxDate', new Date(moment(periodoFinal, 'DD/MM/YYYY', true).format()));
          $('#finalPeriodo').datetimepicker('minDate', new Date(moment(periodoFinal, 'DD/MM/YYYY', true).format()));
        }else{
          $("#inicioPeriodo").datetimepicker('date', new Date());
          $("#finalPeriodo").datetimepicker('date', new Date());
        }
        // =======================  Data tables ==================================
        definirdataTable("#tablaPeriodos");
      },
      error: function(){
        alertity.error("No se ha podido cargar la lista de periodos.");
      }
    });
  }

  function editarPeriodo(idPeriodo){
    if(validarEdicion == 0){
      //Se toman los fechas de los periodos
      periodoInicio = moment($('#incioPeriodo' + idPeriodo).val(), 'DD/MM/YYYY', true).format();
      periodoFinal = moment($('#finalPeriodo' + idPeriodo).val(), 'DD/MM/YYYY', true).format(); 

      //Se setean los campso
      $('#incioPeriodo' + idPeriodo + ', #finalPeriodo' + idPeriodo).val('');

      //Se habnilitan o dehabilitan del peridodo que se va a editar
      $("#btnGuardarPeriodo" + idPeriodo + ", #peridoDescripcion" + idPeriodo + ", #incioPeriodo" + idPeriodo + ", #finalPeriodo" +  idPeriodo).attr("disabled", false);
      $("#btnEditarPeriodo" + idPeriodo).attr("disabled", true);

      //Se definen los campos de los calendario a utilizar
      $('#incioPeriodo' + idPeriodo).datetimepicker({
        format: 'L',
        defaultDate: periodoInicio,
        maxDate: periodoFinal
      });

      $('#finalPeriodo' + idPeriodo).datetimepicker({
        format: 'L',
        defaultDate: periodoFinal,
        minDate: periodoInicio
      });


      $('#incioPeriodo' + idPeriodo).on("change.datetimepicker", function (e) {
        $('#finalPeriodo' + idPeriodo).datetimepicker('minDate', e.date);
      });
      $('#finalPeriodo' + idPeriodo).on("change.datetimepicker", function (e) {
        $('#incioPeriodo' + idPeriodo).datetimepicker('maxDate', e.date);
      });

      if ($('#finalPeriodo' + (idPeriodo - 1)).length > 0) {
        $('#incioPeriodo' + idPeriodo).datetimepicker('minDate', new Date(moment($('#finalPeriodo' + (idPeriodo - 1)).val(), 'DD/MM/YYYY', true).format()));
      }

      validarEdicion = 1;
    }else{
      alertify.error("Debes guardar antes de editar otro periodo.");
    }
  }

  function actualizarPeriodo(idPeriodo){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/creser.php',
      type: "POST",
      dataType: "json",
      data: {
        accion: "actualizarPerdidos",
        id: idPeriodo,
        incioPeriodo: $("#incioPeriodo" + idPeriodo).val(),
        finalPeriodo: $("#finalPeriodo" + idPeriodo).val(),
        peridoDescripcion: $("#peridoDescripcion" + idPeriodo).val()
      },
      success: function(data){
        if(data == true){
          $("#btnGuardarPeriodo" + idPeriodo + ", #peridoDescripcion" + idPeriodo + ", #incioPeriodo" + idPeriodo + ", #finalPeriodo" +  idPeriodo).attr("disabled", true);
          $("#btnEditarPeriodo" + idPeriodo).attr("disabled", false);
          validarEdicion = 0;
          cargarTablaPeriodos();
          alertify.success("Se ha actualizado el perido.");
        }else{
          alertify.error("No se ha podido actualizar el periodo");
        }
      },
      error: function(){
        alertify.error("No se ha actualizado el perido.");
      }
    });
  }

  function listaPeriodos(){
    $.ajax({
      url: '<?php echo(RUTA_CONSULTAS); ?>paginas/gestion_humana/creser.php',
      type: "POST",
      dataType: "json",
      data:{
        accion: "listaPeriodos",
      },
      success: function(data){
        var ultimoPeriodo;
        $("#select_periodos").empty();
        $("#select_periodos").html(`
          <option disabled selected>Seleccione un periodo...</option>  
        `);

        for (let i = 0; i < data.cantidad_registros; i++) {
          if (i == (data.cantidad_registros - 1)) {  
            $("#select_periodos").append(`
              <option selected value="${data[i].cp_id}">${data[i].cp_fecha_inicio + " - " +  data[i].cp_fecha_fin}</option>  
            `);
          }else{
            $("#select_periodos").append(`
              <option value="${data[i].cp_id}">${data[i].cp_fecha_inicio + " - " +  data[i].cp_fecha_fin}</option>  
            `);
          }
          ultimoPeriodo = data[i].cp_id;
        }

        cargarTabla(ultimoPeriodo);
        cargarProyectosyFormacion(ultimoPeriodo);
      },
      error: function(){
        alertify.error("No se ha podido cargar la lista");
      }
    });
  }

  function redireccionar(idArea, periodo){
    window.location = "areas?idArea="+idArea+"&idPeriodo="+periodo
  }

  function torta(id, titulo, subtitulo, datos, valores){
    var myChart = echarts.init(document.getElementById(id));
    option = {
      title: {
        text: titulo,
        subtext: subtitulo,
        left: 'center'
      },
      tooltip: {
        trigger: 'item',
        formatter: '{b} : {c} ({d}%)'
      },
      legend: {
        orient: 'vertical',
        left: 'left',
        data: datos
      },
      series: [
        {
          name: titulo,
          type: 'pie',
          radius: '55%',
          center: ['50%', '60%'],
          data: valores,
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowOffsetX: 0,
              shadowColor: 'rgba(0, 0, 0, 0.5)'
            }
          }
        }
      ]
    };
    ;
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
  }

  function graficos(titulo, datos, valores, cantidad, idGrafico){
   
      var myChart = echarts.init(document.getElementById(idGrafico));

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
  }
</script>
</html>