function cerrarCargando() {
  setTimeout(function() {
    top.$("#cargando").modal("hide");
  }, 1000);
}

function modalArchivos() {
  $("#contenidoArchivos").on("load", function() {
    $("#modalArchivos").modal("show");
    setTimeout(function() {
      top.$("#cargando").modal("hide");
    }, 1000);
  });
}

function definirdataTable(nombreDataTable) {
  // =======================  Data tables ==================================
  var tabla = $(nombreDataTable).DataTable({
    responsive: true,
    "language": {
      "decimal": "",
      "emptyTable": "No hay datos disponibles en la tabla",
      "info": "Mostrando _START_ desde _END_ hasta _TOTAL_ registros",
      "infoEmpty": "Mostrando 0 desde 0 hasta 0 registros",
      "infoFiltered": "(Filtrado por _MAX_ total)",
      "infoPostFix": "",
      "thousands": ",",
      "lengthMenu": "Mostrar _MENU_",
      "loadingRecords": "Cargando...",
      "processing": "Procesando...",
      "search": "Buscar:",
      "zeroRecords": "No se encontraron registros",
      "paginate": {
        "first": "Primero",
        "last": "Ãšltimo",
        "next": "Siguiente",
        "previous": "Anterior"
      }
    },
    stateSave: true,
    "processing": true,
    lengthChange: true,
    buttons: ['excel', 'pdf',
      {
        extend: 'colvis',
        text: 'Mostrar columnas'
      }
    ]
  });

  tabla.buttons().container().appendTo(nombreDataTable + '_wrapper .col-md-6:eq(0)');
}

function minutosAHorasyminutos(mins) {
  //do not include the first validation check if you want, for example,
  //getTimeFromMins(1530) to equal getTimeFromMins(90) (i.e. mins rollover)
  /*if (mins >= 24 * 60 || mins < 0) {
    throw new RangeError("Valid input should be greater than or equal to 0 and less than 1440.");
  }*/
  var h = mins / 60 | 0,
      m = mins % 60 | 0;
  return moment.utc().hours(h).minutes(m).format("HH:mm");
}

function soloNumeros(e){
  var keynum = window.event ? window.event.keyCode : e.which;
  if ((keynum == 8) || (keynum == 46))
    return true;
    return /\d/.test(String.fromCharCode(keynum));
}