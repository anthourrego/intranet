function cerrarCargando(){
  setTimeout(function() {
    $("#cargando").modal("hide");
  }, 1000);
}

function definirdataTable(nombreDataTable){
  // =======================  Data tables ==================================
  $(nombreDataTable).DataTable({
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
    "processing": true
  });
}