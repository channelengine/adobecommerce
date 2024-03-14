if (!window.ChannelEngine) {
    window.ChannelEngine = {};
}

(function () {
    function ModalService() {
        this.showModal = function (header, content, buttonText, callback, transactionLogsModal = false) {
            let modal = this.getModal(transactionLogsModal);

            let node = document.createElement('div'),
                modalContent = modal.querySelector('#ce-modal-main'),
                modalHeader = modal.getElementsByTagName('H3')[0],
                modalButton = modal.getElementsByTagName('BUTTON')[0],
                closeButton = modal.getElementsByClassName('ce-close-modal')[0],
                disableSwitch = document.getElementById('ce-disable-switch');
            modalHeader.innerHTML = header;

            if (buttonText) {
                modalButton.onclick = callback;
                modalButton.innerHTML = buttonText;
            } else {
                modalButton.style.display = "none";
            }

            closeButton.onclick = function () {
                if (disableSwitch) {
                    disableSwitch.checked = true;
                }
                modal.style.display = "none";
            }
            node.innerHTML = content;
            modalContent.removeChild(modalContent.firstChild);
            modalContent.append(node);
            modal.style.display = "block";
        };

        this.getModal = function (transactionLogsModal) {
            if(transactionLogsModal) {
                return document.getElementById('ce-modal-transactions');
            }

            if(document.getElementById('ce-modal-dashboard')) {
                return document.getElementById('ce-modal-dashboard');
            }

            if(document.getElementById('ce-modal-order-sync')) {
                return document.getElementById('ce-modal-order-sync');
            }

            if(document.getElementById('ce-modal-initial-sync')) {
                return  document.getElementById('ce-modal-initial-sync');
            }

            return document.getElementById('ce-modal');
        }
    }

    ChannelEngine.modalService = new ModalService();
})();
