if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'checkOnlineServiceInsertion',
        selector: '.form__input__online--checkbox',
        init: function () {
            var self = this;


            let onlineChecker = document.querySelectorAll(self.selector)[0];
            let zipField = document.getElementsByName('contact_zip')[0];
            let cityField = document.getElementsByName('contact_city')[0];

            let firstValueZip = zipField.value;
            let firstValueCity = cityField.value;

            onlineChecker.addEventListener('change', function () {
                changeHandler();
            });

            function changeHandler(){
                if(onlineChecker.checked){
                    zipField.readOnly = true;
                    cityField.readOnly = true;

                    zipField.style.backgroundColor = "lightgrey";
                    zipField.style.color = "lightgrey";
                    cityField.style.backgroundColor = "lightgrey";
                    cityField.style.color = "lightgrey";

                    zipField.value = "00000";
                    cityField.value = "Online";

                } else {
                    zipField.readOnly = false;
                    cityField.readOnly = false;

                    zipField.style.backgroundColor = "white";
                    zipField.style.color = "#3c3c3b";
                    cityField.style.backgroundColor = "white";
                    cityField.style.color = "#3c3c3b";


                    if(zipField.value == "00000") {
                        zipField.value = "";
                    }
                    if(cityField.value == "Online") {
                        cityField.value = "";
                    }

                    zipField.value = firstValueZip;
                    cityField.value = firstValueCity;
                }
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
