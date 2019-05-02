<?php  
  $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"); 
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
  include_once($ruta_raiz . 'clases/Conectar.php');
  
  $session=new Session();

  $usuario = $session->get("usuario");

  $inicio = "";

  $db = new Bd(BDNAME_GEMINUS, BDSERVER_GEMINUS, BDUSER_GEMINUS, BDPASS_GEMINUS, BDTYPE_GEMINUS);
  $db->Conectar();
  $sql_empleado_geminus = $db->consulta("SELECT E.CODIGO CEDULA, E.NOMBRE NOMBRE, E.FECHAINGRESO FECHAINGRESO, E.SALARIOBASICO SALARIO, C.DESCRIPCION CARGO, TC.DESCRIPCION TIPOCONTRATO, E.SEXO SEXO FROM EMPLEADOS E INNER JOIN CARGOS C ON E.CARGO = C.CARGO INNER JOIN TIPOSDECONTRATOS TC ON E.TIPODECONTRATO = TC.TIPODECONTRATO WHERE E.CODIGO = :cedula AND (E.ACTIVO = 'S' OR RETIRADO='N')", array(":cedula" => $usuario['cedula']));

  $db->desconectar(); 

  $lib = new Libreria;

  if ($sql_empleado_geminus[0]['SEXO'] == 'M') {
    $inicio = "El señor";
  }elseif ($sql_empleado_geminus[0]['SEXO'] == 'F') {
    $inicio = "La señora";
  }else{
    $inicio = "El señor";
  }

  $session->set('encabezado', '<img style="width: 745px;" src="img/certificado_laboral/encabezado.jpg">');
  $session->set('pie','<div></div><div></div><div></div><h6 style="text-align: center; font-weight: bold;">Centro Industrial y Logístico de Pereira - km2 Vía Cerritos - La Virginia - Ent 5 Cafelia - PBX: 326 25 00 - EXT 123 - Pereira/Colombia <br> <span style="color: #1874c1;">www.consumerelectronicsgroup.com</span></h6>');  
  $session->set('html', '<br><br><h4 style="text-align: center; font-weight: bold; line-height: 26px;">EL COORDINADOR DE NOMINA Y SEGURIDAD SOCIAL DE <br> CONSUMER ELECTRONICS GROUP S.A.S <br>CERTIFICA</h4>
    <br>
    <p style="line-height: 26px; text-align: justify;">' . $inicio . ' <b>' . $sql_empleado_geminus[0]['NOMBRE'] . '</b> identificado(a) con número de cedula <b>' . number_format($sql_empleado_geminus[0]['CEDULA']) . '</b>, presta sus servicios en esta compañía desde ' . $meses[date('m', strtotime($sql_empleado_geminus[0]['FECHAINGRESO']))-1] . " " . date('d', strtotime($sql_empleado_geminus[0]['FECHAINGRESO'])) . " de " . date('Y', strtotime($sql_empleado_geminus[0]['FECHAINGRESO'])) . ' en continuidad hasta la fecha, con una asignación mensual de $ <b> ' . number_format($sql_empleado_geminus[0]['SALARIO']) .' </b> con un ' . $sql_empleado_geminus[0]['TIPOCONTRATO'] . '.</p>
    <br>
    <p>Cargo desempeñado: <b>' . $sql_empleado_geminus[0]['CARGO'] . '</b>.</p>
    <br>
    <p style="line-height: 26px; text-align: justify;">El presente documento se ha generado vía WEB, consideramos importante validar lo aquí estipulado con los colaboradores de la oficina de Gestión Humana de esta ciudad.</p>
    <br>
    <p>Dada en Pereira, a los ' . date('d') . ' días del mes de ' . $meses[date('n')-1] . ' de ' . date('Y') .'. </p>
    <p>Cordialmente,</p>
    
    <table style="padding-top: 40px;">
      <tr nobr="true">
        <td style="text-align: center;">
          <img src="img/certificado_laboral/firma_Germ1.png">
          German Ospina Hurtado
          <br>
          <span style="font-size: 14px;">Coord de Nomina y Seg Social</span style="font-size: 14px;">
        </td>
        <td></td>
        <td></td>
      </tr>
    </table>');      
  $session->set('autor','Consumer Electronics Group S.A.S');                  
  $session->set('imprimir', 1);
  $session->set('tipo_salida', 'I');
  $session->set('marca_agua', 1);
  $session->set('ruta_marca_agua', 'img/certificado_laboral/marca_agua1.png');
  $session->set('nombre_archivo', 'Certificado Laboral');

  header('location: ' . $ruta_raiz . "pdf.php");
  //include_once($ruta_raiz.'pdf.php');
?>
<!--
<!DOCTYPE html>
<html>
<head>
	<title>Consumer Electrnics Group S.A.S</title>
</head>
<body>
  <div class="container bg-white pb-4 pt-4">
    <img class="w-100" src="img/certificado_laboral/encabezado.jpg">
    <h5 class="text-center font-weight-bold mt-5">EL COORDINADOR DE NOMINA Y SEGURIDAD SOCIAL DE <br> CONSUMER ELECTRONICS GROUP S.A.S</h5>
    <h5 class="text-center font-weight-bold">CERTIFICA</h5>
    <br>
    <p>El señor <b><?php echo $sql_empleado_geminus[0]['NOMBRE'] ?></b> identificado(a) con número de cedula <b><?php echo number_format($sql_empleado_geminus[0]['CEDULA']); ?></b> , presta sus servicios en esta compañía desde <?php echo $meses[date('m', strtotime($sql_empleado_geminus[0]['FECHAINGRESO']))-1] . " " . date('d', strtotime($sql_empleado_geminus[0]['FECHAINGRESO'])) . " de " . date('Y', strtotime($sql_empleado_geminus[0]['FECHAINGRESO'])); ?> en continuidad hasta la fecha, con una asignación mensual de $ <b><?php echo number_format($sql_empleado_geminus[0]['SALARIO']); ?> </b> con un <?php echo $sql_empleado_geminus[0]['TIPOCONTRATO']; ?></p>
    <br>
    <p>Cargo desempeñado: <b><?php echo $sql_empleado_geminus[0]['CARGO'] ?></b>.</p>
    <br>
    <p>El presente documento se ha generado vía WEB, consideramos importante validar lo aquí estipulado con los colaboradores de la oficina de Gestión Humana de esta ciudad.</p>
    <br>
    <p>Dada en Pereira, a los <?php echo date('d') ?> días del mes de <?php echo $meses[date('n')-1] ?> de <?php echo date('Y') ?>. <br>
      Cordialmente,
    </p>

    <div class="row mt-5">
      <div class="col-3 border-bottom border-dark">
        <img class="w-75" src="img/certificado_laboral/firma_Germ.jpg">
      </div>
    </div>

    <h5 class="text-center font-weight-bold mt-5">Vía Cerritos - La Virginia - Entrada 5 Cafelia (Los Cambulos) <br> Pereira- Colombia Teléfono: 3262500</h5>
  </div> 
</body>
</html>-->