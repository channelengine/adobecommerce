var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const disconnectHeader = document.getElementById('disconnect-header'),
            disconnectUrl = document.getElementById('disconnect-url'),
            stateUrl = document.getElementById('state-url'),
            storeScope = document.getElementById('store-scope');

        disconnectHeader.onclick = function () {
            let header = document.getElementById('disconnect-header-text'),
                btnText = document.getElementById('disconnect-button-text'),
                text = document.getElementById('disconnect-text'),
                content = document.getElementById('disconnect-modal-content'),
                label = document.getElementById('disconnect-modal-label');
            label.innerText = text.value;

            ChannelEngine.modalService.showModal(
                header.value,
                content.innerHTML,
                btnText.value,
                disconnect
            );
        }

        let disconnect = function () {
            ChannelEngine.ajaxService.get(
                disconnectUrl.value + '?storeId=' + storeScope.value,
                function (response) {
                    window.location.assign(stateUrl.value + '?storeId=' + storeScope.value);
                }
            );
        }
    }
);
