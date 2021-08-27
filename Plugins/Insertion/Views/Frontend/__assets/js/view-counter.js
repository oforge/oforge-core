if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'viewCounter',
        selector: '.content--insertion-counter',
        init: function () {
            var self = this;
            var selectionElement = document.querySelector(self.selector);
            var xhr = new XMLHttpRequest();

            if(localStorage.getItem("cookie_consent") === "accepted") {
                var insertionId = selectionElement.dataset.insertionid;
                var viewed = sessionStorage.getItem("viewed_insertion");

                if(viewed == null) {
                    viewed = [];
                } else {
                    viewed = JSON.parse(viewed);
                }

                if(viewed.indexOf(insertionId) < 0) {
                    xhr.open("POST", "/insertions/add_view", true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.send("id=" + insertionId);
                    xhr.addEventListener('load', function () {
                        if(xhr.status >= 200 && xhr.status < 300) {
                            viewed.push(insertionId);
                            sessionStorage.setItem("viewed_insertion", JSON.stringify(viewed));
                        }
                    });
                }
            }
            var xhr2 = new XMLHttpRequest();
            xhr2.open("POST", "/backend/analytics/dashboard/add_view", true);
            xhr2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr2.send("id=" + insertionId);
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
