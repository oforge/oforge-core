if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'checkOnlineServiceSearch',
        selector: '.form__input__search__online--checkbox',
        init: function () {
            var self = this;


            let onlineChecker = document.querySelectorAll(self.selector)[0];
            let zipField = document.getElementsByName('zip')[0];

            onlineChecker.addEventListener('change', function () {
                changeHandler();
            });

            function changeHandler(){
                if(onlineChecker.checked){
                    zipField.readOnly = true;

                    zipField.style.backgroundColor = "lightgrey";
                    zipField.style.color = "lightgrey";

                    zipField.value = "00000";

                } else {
                    zipField.readOnly = false;

                    zipField.style.backgroundColor = "white";
                    zipField.style.color = "#3c3c3b";


                    if(zipField.value == "00000") {
                        zipField.value = "";
                    }
                }
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
