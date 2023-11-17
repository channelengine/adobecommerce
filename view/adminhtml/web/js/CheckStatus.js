var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const ajaxService = ChannelEngine.ajaxService,
            url = document.getElementById('ce-check-status'),
            productProgress = document.getElementById('ce-product-progress'),
            productProgressBar = document.getElementById('ce-product-progress-bar'),
            productTotal = document.getElementById('ce-product-total'),
            productSynced = document.getElementById('ce-product-synced'),
            orderProgress = document.getElementById('ce-order-progress'),
            orderProgressBar = document.getElementById('ce-order-progress-bar'),
            orderTotal = document.getElementById('ce-order-total'),
            orderSynced = document.getElementById('ce-order-synced'),
            storeId = document.getElementById('ce-store-scope'),
            stateUrl = document.getElementById('ce-state-url'),
            taskStatuses = ['completed', 'failed', 'aborted'],
            productSyncEnabled = document.getElementById('ce-product-sync-enabled'),
            productSyncSection = document.getElementById('ce-product-sync-in-progress');

        checkStatus();

        function checkStatus() {
            let syncPage = document.getElementById('sync-in-progress');

            if (syncPage.style.display !== 'none') {
                ajaxService.get(
                    url.value + '?storeId=' + storeId.value,
                    function (response) {
                        if (taskStatuses.includes(response.product_sync.status) && taskStatuses.includes(response.order_sync.status)) {
                            window.location.assign(stateUrl.value + '?storeId=' + storeId.value);
                        } else {
                            if(productSyncEnabled) {
                                productProgress.innerHTML = response.product_sync.progress + '%';
                                productProgressBar.style.clipPath = 'inset(0 0 0 ' + response.product_sync.progress + '%)';
                                productProgressBar.innerHTML = response.product_sync.progress + '%';
                                productSynced.innerHTML = response.product_sync.synced;
                                productTotal.innerHTML = response.product_sync.total;
                                productSyncSection.setAttribute('hidden', 'true');
                            } else {
                                productSyncSection.removeAttribute('hidden');
                            }
                            orderProgress.innerHTML = response.order_sync.progress + '%';
                            orderProgressBar.style.clipPath = 'inset(0 0 0 ' + response.order_sync.progress + '%)';
                            orderProgressBar.innerHTML = response.order_sync.progress + '%';
                            orderSynced.innerHTML = response.order_sync.synced;
                            orderTotal.innerHTML = response.order_sync.total;

                            setTimeout(checkStatus, 1000);
                        }
                    });
            }
        }
    }
);