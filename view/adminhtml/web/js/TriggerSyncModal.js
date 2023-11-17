var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let productCheckbox = document.getElementById('ce-product-sync-checkbox'),
            orderCheckbox = document.getElementById('ce-order-sync-checkbox'),
            startSyncBtn = document.getElementById('ce-start-sync-btn');

        productCheckbox.onchange = function (event) {
            if (event.currentTarget.checked) {
                startSyncBtn.disabled = false;
            } else {
                if (!orderCheckbox.checked) {
                    startSyncBtn.disabled = true;
                }
            }
        }

        orderCheckbox.onchange = function (event) {
            if (event.currentTarget.checked) {
                startSyncBtn.disabled = false;
            } else {
                if (!productCheckbox.checked) {
                    startSyncBtn.disabled = true;
                }
            }
        }
    }
);