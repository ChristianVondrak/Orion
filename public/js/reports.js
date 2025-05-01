// public/js/reports.js

// Variables globales
let start;
let end;

// Función para leer parámetros de la URL
function getParameterByName(name) {
    const url = new URL(window.location.href);
    return url.searchParams.get(name) || null;
}

// Captura de parámetros
const urlStart = getParameterByName('start');
const urlEnd   = getParameterByName('end');

if (urlStart && urlEnd) {
    start = moment(urlStart, 'YYYY/MM/DD');
    end   = moment(urlEnd,   'YYYY/MM/DD');
} else {
    start = moment().startOf('month');
    end   = moment().endOf('month');
}

// Inicializa el daterangepicker
$('#report_date_range').daterangepicker({
    startDate: start,
    endDate:   end,
    ranges: {
        'Today':       [moment(), moment()],
        'Yesterday':   [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days':[moment().subtract(29, 'days'), moment()],
        'This Month':  [moment().startOf('month'),   moment().endOf('month')],
        'Last Month':  [moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month')]
    },
    locale: { format: 'DD/MM/YYYY' }
}, function(pickedStart, pickedEnd) {
    $('#start').val(pickedStart.format('YYYY/MM/DD'));
    $('#end').val( pickedEnd.format('YYYY/MM/DD'));
});

// Asegurar valores antes de enviar
$('form').on('submit', function() {
    if (!$('#start').val() || !$('#end').val()) {
        $('#start').val(moment().startOf('month').format('YYYY/MM/DD'));
        $('#end').val(  moment().endOf('month').format('YYYY/MM/DD'));
    }
});
