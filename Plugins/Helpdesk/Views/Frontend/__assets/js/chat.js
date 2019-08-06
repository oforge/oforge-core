/**
 * This module will:
 * 1. Add class 'is__active' to currently selected conversation element.
 * 2. Scroll active chat to user's last-seen message.
 */

if (typeof Oforge !== 'undefined') {

    Oforge.register({
        name: 'scroll-down',
        selector: '.messenger',
        otherNotRequiredContent: '',
        init: function () {
            let conversationId = document.URL.split("/").pop();
            let activeChat = document.getElementById(conversationId);
            if (activeChat) {
                activeChat.classList.add("is__active");
                let chatInput = document.getElementById('message');
                let unreadMessages = activeChat.dataset.unread;
                let messages = document.getElementsByClassName('message');
                let lastSeenMessageIndex = messages.length - (1 + parseInt(unreadMessages));
                messages[lastSeenMessageIndex].scrollIntoView(false);
                chatInput.scrollIntoView(false);
                chatInput.focus();
            }
            else {
                let chatForm = document.getElementById('message--input-form');
                chatForm.classList.add('unavailable');
            }
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
