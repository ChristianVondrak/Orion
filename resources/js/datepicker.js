// Inicializar la variable
var start;
var end;

// Comprobar si la variable ya existe en localStorage
if (localStorage.getItem('start')) {
    start = localStorage.getItem('start');
    end = localStorage.getItem('end');
} else {
    // Si la variable no existe, asignar inicio de mes y final de mes
    start = moment().startOf("month");
    end = moment().endOf("month");
}

$("#kt_daterangepicker_4").daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
}, function(start, end) {
    // Asignar valor al input start y end
    $("#start").val(start.format('YYYY/MM/DD'));
    $("#end").val(end.format('YYYY/MM/DD'));
    // Guardar en localStorage el valor de las variables
    localStorage.setItem('start', start.format('MM/DD/YYYY'));
    localStorage.setItem('end', end.format('MM/DD/YYYY'));
});
