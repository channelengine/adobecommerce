if (!window.ChannelEngine) {
    window.ChannelEngine = {};
}

(function () {
    function Notifications() {
        /**
         * Removes notifications from page.
         */
        this.removeNotifications = function () {
            const main = document.getElementsByTagName('main')[0],
                messages = main.getElementsByClassName('ce-notice');

            for (let index = messages.length - 1; index >= 0; index--) {
                main.removeChild(messages[index]);
            }
        };

        /**
         * Adds notification to page.
         *
         * @param messageText
         * @param success Defines whether success or error message should be displayed.
         */
        this.addNotification = function (messageText, success) {
            const main = document.getElementsByTagName('main')[0],
                messageDiv = document.createElement('div'),
                message = document.createElement('p');

            messageDiv.classList.add('ce-notice');
            messageDiv.classList.add(success ? 'ce-notice-success' : 'ce-notice-error');
            messageDiv.style.marginTop = '0px';
            messageDiv.style.marginLeft = '0px';
            messageDiv.style.marginRight = '0px';
            messageDiv.style.marginBottom = '15px';
            message.innerText = messageText;

            messageDiv.appendChild(message);
            main.insertBefore(messageDiv, main.firstChild);
        }
    }

    ChannelEngine.notificationService = new Notifications();
})();