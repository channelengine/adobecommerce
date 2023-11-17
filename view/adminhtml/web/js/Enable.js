var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const ajaxService = ChannelEngine.ajaxService,
            enableUrl = document.getElementById('ce-enable-plugin'),
            enableSwitch = document.getElementById('ce-enable-switch'),
            triggerUrl = document.getElementById('ce-trigger-sync-url'),
            storeScope = document.getElementById('ce-store-scope');

        enableSwitch.checked = false;

        enableSwitch.onchange = (event) => {
            if (event.currentTarget.checked) {
                let triggerModal = document.getElementById('ce-trigger-modal'),
                    modal = triggerModal.children[0];

                triggerModal.style.display = "block";
                modal.querySelectorAll('.ce-button__secondary').forEach(button => {
                    button.addEventListener('click', () => {
                        enablePlugin();
                    });
                });

                modal.querySelectorAll('.ce-button__primary').forEach(syncButton => {
                    syncButton.addEventListener('click', () => {
                        triggerModal.style.display = "none";
                        triggerSync(triggerUrl.value);
                        enablePlugin();
                    });
                });

                modal.querySelectorAll('.ce-close-button').forEach(closeBtn => {
                    closeBtn.addEventListener('click', () => {
                        triggerModal.style.display = "none";
                    });
                })
            }
        }

        function enablePlugin() {
            ajaxService.get(enableUrl.value + '?storeId=' + storeScope.value, function () {
                window.location.reload();
            });
        }

        function triggerSync(url) {
            let productsChecked = document.getElementById('ce-product-sync-checkbox'),
                ordersChecked = document.getElementById('ce-order-sync-checkbox');

            ChannelEngine.ajaxService.post(
                url + '?form_key=' + window.FORM_KEY + '&storeId=' + storeScope.value,
                {
                    product_sync: productsChecked.checked,
                    order_sync: ordersChecked.checked
                },
                function (response) {
                    if (!response.success) {
                        ChannelEngine.notificationService.removeNotifications();
                        ChannelEngine.notificationService.addNotification(response.message, false);
                    } else {
                        ChannelEngine.triggerSyncService.checkStatus();
                    }
                }
            );
        }
    }
);