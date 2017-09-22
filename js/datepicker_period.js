function datepicker_period(start_date, end_date) {
    // Datepicker

    var dateFormat = 'yy-mm-dd';
    var start_date = $(start_date)
        .datepicker({
            dateFormat: dateFormat,
            showWeek: true,
            firstDay: 1,
            numberOfMonths: 3
        });
    var end_date = $(end_date)
        .datepicker({
            dateFormat: dateFormat,
            showWeek: true,
            firstDay: 1,
            numberOfMonths: 3
        });

    start_date
        .on( 'change', function() {
            var period_start = getDate(this);
            // The period will be at most 26 weeks
            var period_end = new Date(period_start.getFullYear(), period_start.getMonth(), period_start.getDate()+26*7-1);
            end_date
                .datepicker( 'option', 'minDate', period_start)
                .datepicker( 'option', 'maxDate', period_end);
        })
        .trigger("change"); // Set initial minDate and maxDate in end_date
    
    function getDate( element ) {
        var date;
        try {
            date = $.datepicker.parseDate( dateFormat, element.value );
        }
        catch( error ) {
            date = null;
        }
        
        return date;
    }
}
