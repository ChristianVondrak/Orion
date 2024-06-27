// Inicializar la variable
var start;
var end;

// Función para obtener parámetros de la URL
function getParameterByName(name) {
    const url = new URL(window.location.href);
    const param = url.searchParams.get(name);
    return param ? param : null;
}

// Capturar los parámetros de la URL
var urlStart = getParameterByName('start');
var urlEnd = getParameterByName('end');

// Comprobar si existen los parámetros en la URL
if (urlStart && urlEnd) {
    // Si existen, asignar los valores de la URL 
    start = moment(urlStart, "YYYY/MM/DD");
    end = moment(urlEnd, "YYYY/MM/DD");
}  else {
    // Si no existen, asignar inicio de mes y final de mes
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
    locale: {
        // Asegura que el formato visual en el datepicker sea el deseado
        format: 'DD/MM/YYYY'
    }
}, function(start, end) {
    // Asignar valor al input para que reciba en controlador
    $("#start").val(start.format('YYYY/MM/DD'));
    $("#end").val(end.format('YYYY/MM/DD'));
});
