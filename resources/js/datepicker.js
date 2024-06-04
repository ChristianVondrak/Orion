var start = moment().startOf("month");
var end = moment().endOf("month");

function cb(start, end) {
    $("#kt_daterangepicker_4").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
}

$("#kt_daterangepicker_4").daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
        "Today": [moment(), moment()],
        "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
    }
}, cb);

$('input[name="start"]').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('YYYY/MM/DD'));
    $('input[name="end"]').val(picker.endDate.format('YYYY/MM/DD'));
});

cb(start, end);
