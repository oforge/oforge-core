/**
 * This module will highlight the active chat-navigation element
 */

if (typeof Oforge !== 'undefined') {

    Oforge.register({
        name: 'scroll-down',
        selector: '.messenger',
        otherNotRequiredContent: '',
        init: function () {
            var messageKey = document.URL.split("/").pop();
            var activeChat = document.getElementById(messageKey);
            if(activeChat) {
                activeChat.classList.add("is__active");
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
