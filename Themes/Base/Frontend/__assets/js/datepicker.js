if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'datepicker',
        selector: '#datepicker',
        otherNotRequiredContent: 'some other content, that we can define and that is not required',
        init: function () {
            var me = this;
            var lang = document.documentElement.lang;
            var localisation = {
                de: {
                    previousMonth : 'Voriger Monat',
                    nextMonth     : 'Nächster Monat',
                    months        : ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
                    weekdays      : ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
                    weekdaysShort : ['So','Mo','Di','Mi','Do','Fr','Sa']
                },
            };
            var picker = new Pikaday({
                field: document.getElementById('datepicker'),
                maxDate: moment().toDate(),
                minDate: moment().subtract(40, 'years').toDate(),
                yearRange: 40,

            });

            if(lang in localisation) {
                picker._o.i18n = localisation[lang];
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
