if (!window.ChannelEngine) {
    window.ChannelEngine = {};
}

(function () {
    function TriggerSyncService() {
        this.areEventsBinded = false;

        this.showSaveConfigModal = function (onSave, url) {
            let saveConfigModal = document.getElementById('ce-save-config-modal'),
                closeButton = document.getElementById('ce-cancel-config-modal-btn'),
                saveButton = document.getElementById('ce-save-config-modal-btn'),
                secondCloseButton = saveConfigModal.getElementsByClassName('ce-close-button')[0],
                storeScope = document.getElementById('ce-store-scope');

            saveConfigModal.classList.toggle('ce-hidden');

            closeButton.onclick = () => {saveConfigModal.classList.add('ce-hidden');};
            secondCloseButton.onclick = () => {saveConfigModal.classList.add('ce-hidden');};
            saveButton.onclick = () => {
                closeButton.click();
                onSave();
                ChannelEngine.ajaxService.post(
                    url + '?form_key=' + window.FORM_KEY + '&storeId=' + storeScope.value,
                    {
                        product_sync: true,
                        order_sync: false
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
            };
        };

        this.showModal = function (url) {
            let triggerModal = document.getElementById('ce-trigger-modal'),
                ordersChecked = document.getElementById('ce-order-sync-checkbox'),
                orderTooltip = document.getElementById('ce-order-sync-tooltip'),
                enableOrdersByMerchantSync = document.getElementById('ce-merchant-fulfilled').getAttribute('data-merchant-fulfilled-orders-enabled'),
                enableOrdersByMarketplaceSync = document.getElementById('ce-import-fulfilled').getAttribute('data-fulfilled-orders'),
                modal = triggerModal.children[0];

            triggerModal.style.display = "block";
            if( ! ( enableOrdersByMerchantSync === '1' || enableOrdersByMarketplaceSync === '1' ) ) {
                ordersChecked.setAttribute('disabled', 'true');
                ordersChecked.checked = false;
                orderTooltip.textContent = 'Order synchronization is disabled, because both order synchronization options are disabled in the configuration (fulfilled by merchant and by marketplace).';
            } else {
                ordersChecked.removeAttribute('disabled');
                orderTooltip.textContent = 'The integration will synchronize new and closed orders (fulfilled by the merchant and fulfilled by the marketplace) from ChannelEngine into the shop.';
            }

            if (!this.areEventsBinded) {
                modal.querySelectorAll('.ce-button__secondary').forEach(closeButton => {
                    closeButton.addEventListener('click', () => {
                        triggerModal.style.display = "none";
                    });
                });

                modal.querySelectorAll('.ce-button__primary').forEach(syncButton => {
                    syncButton.addEventListener('click', () => {
                        triggerModal.style.display = "none";
                        this.triggerSync(url);
                    });
                });

                modal.querySelectorAll('.ce-close-button').forEach(closeBtn => {
                    closeBtn.addEventListener('click', () => {
                        triggerModal.style.display = "none";
                    });
                })

                this.areEventsBinded = true;
            }
        };

        this.triggerSync = function (url) {
            let productsChecked = document.getElementById('ce-product-sync-checkbox'),
                ordersChecked = document.getElementById('ce-order-sync-checkbox'),
                storeScope = document.getElementById('ce-store-scope');

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

        this.checkStatus = function () {
            let syncNowButton = document.getElementById('ce-sync-now'),
                inProgressButton = document.getElementById('ce-sync-in-progress'),
                checkStatusUrl = document.getElementById('ce-check-status-url'),
                storeScope = document.getElementById('ce-store-scope');

            syncNowButton && (ChannelEngine.ajaxService.get(
                checkStatusUrl.value + '?storeId=' + storeScope.value,
                function (response) {
                    if (response.in_progress) {
                        syncNowButton.style.display = "none";
                        inProgressButton.style.display = "inline-block";
                        setTimeout(ChannelEngine.triggerSyncService.checkStatus, 1000);
                    } else {
                        syncNowButton.style.display = "inline-block";
                        inProgressButton.style.display = "none";
                    }
                }
            ));
        }
    }

    ChannelEngine.triggerSyncService = new TriggerSyncService();
})();