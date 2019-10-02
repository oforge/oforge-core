if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'datepicker',
        selector: '#datepicker',
        otherNotRequiredContent: 'some other content, that we can define and that is not required',
        init: function () {
            var picker = new Pikaday({
                field: document.getElementById('datepicker'),
                maxDate: moment().toDate(),
                minDate: moment().subtract(40, 'years').toDate(),
            });
            console.log(picker);
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
