var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let unknownLinesBtn = document.getElementById('ce-unknown-lines'),
            fulfilledBtn = document.getElementById('ce-import-fulfilled'),
            unknownLinesList = document.getElementById('ce-unknown-lines-list'),
            fulfilledList = document.getElementById('ce-import-fulfilled-list'),
            returnsButton = document.getElementById('ce-returns'),
            conditionBtn = document.getElementById('ce-default-condition'),
            resolutionBtn = document.getElementById('ce-default-resolution'),
            unknownLinesItems = document.getElementsByClassName('ce-unknown-lines'),
            unknownLinesText = document.getElementById('ce-unknown-lines-text'),
            fulfilledItems = document.getElementsByClassName('ce-fulfilled'),
            fulfilledText = document.getElementById('ce-import-fulfilled-text'),
            fulfilledFromDate = document.getElementById('ce-import-fulfilled-date'),
            saveBtn = document.getElementById('ceStatusesSave'),
            saveUrl = document.getElementById('ceStatusesSaveUrl'),
            storeId = document.getElementById('ce-store-id'),
            merchantSyncButton = document.getElementById('ce-merchant-fulfilled'),
            shipmentSyncButton = document.getElementById('ce-shipments-sync'),
            cancellationSyncButton = document.getElementById('ce-cancellations-sync'),
            returnsSyncButton = document.getElementById('ce-returns-sync');

        if (saveBtn) {
            ChannelEngine.loader.hide();
            saveBtn.onclick = () => {
                let requiredFields = false, returnsEnabled = '0';
                ChannelEngine.loader.show();

                if (returnsButton) {
                    returnsEnabled = returnsButton.getAttribute('returns-enabled')
                }

                if (returnsButton && returnsEnabled === '1' && !conditionBtn.getAttribute('data-default-condition')
                    .replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '')) {
                    conditionBtn.classList.add('ce-required-attribute');
                    requiredFields = true;
                    ChannelEngine.loader.hide();
                }

                if (returnsButton && returnsEnabled === '1' && !resolutionBtn.getAttribute('data-default-resolution')
                    .replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '')) {
                    resolutionBtn.classList.add('ce-required-attribute');
                    requiredFields = true;
                    ChannelEngine.loader.hide();
                }

                if (requiredFields) {
                    return;
                }

                let merchantOrderSync = merchantSyncButton.getAttribute('data-merchant-fulfilled-orders-enabled');

                ChannelEngine.ajaxService.post(
                    saveUrl.value + '?form_key=' + window.FORM_KEY + '&storeId=' + storeId.value,
                    {
                        unknownLinesHandling: unknownLinesBtn.getAttribute('data-unknown-lines').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, ''),
                        importFulfilledOrders: fulfilledBtn.getAttribute('data-fulfilled-orders').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, ''),
                        returnsEnabled: returnsEnabled,
                        defaultCondition: returnsButton && returnsEnabled === '1' ? conditionBtn.getAttribute('data-default-condition').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') : '',
                        defaultResolution: returnsButton && returnsEnabled === '1' ? resolutionBtn.getAttribute('data-default-resolution').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') : '',
                        merchantOrderSync: merchantOrderSync,
                        shipmentSync: merchantOrderSync === '1' ? shipmentSyncButton.getAttribute('data-shipments-sync-enabled') : '0',
                        cancellationSync: merchantOrderSync === '1' ? cancellationSyncButton.getAttribute('data-cancellations-sync-enabled') : '0',
                        fulfilledFromDate: fulfilledFromDate.value,
                        returnsSync: returnsSyncButton ? returnsSyncButton.getAttribute('data-returns-sync-enabled') : '0',
                    },
                    function (response) {
                        if (response.success) {
                            let stateUrl = document.getElementById('ce-state-url');
                            window.location.assign(stateUrl.value + '?storeId=' + storeId.value);
                        } else {
                            ChannelEngine.loader.hide();
                            ChannelEngine.notificationService.removeNotifications();
                            ChannelEngine.notificationService.addNotification(response.message, false);
                        }
                    }
                )
            }
        }

        unknownLinesBtn.onclick = () => {
            toggleDropDown(unknownLinesBtn, unknownLinesList);
        }

        unknownLinesBtn.onfocusout = () => {
            unknownLinesBtn.classList.remove('ce-active');
            unknownLinesList.style.display = "none";
        }

        fulfilledBtn.onclick = () => {
            toggleDropDown(fulfilledBtn, fulfilledList);
        }

        fulfilledBtn.onfocusout = () => {
            fulfilledBtn.classList.remove('ce-active');
            fulfilledList.style.display = "none";
        }

        for (let i = 0; i < unknownLinesItems.length; i++) {
            unknownLinesItems[i].onmousedown = () => {
                unknownLinesBtn.setAttribute('data-unknown-lines', unknownLinesItems[i].getAttribute('value'));
                unknownLinesBtn.classList.remove('ce-active');
                unknownLinesList.style.display = 'none';
                unknownLinesText.innerText = unknownLinesItems[i].querySelector('span').innerText.replace(/(\r\n|\n|\r)/gm, "");
            }
        }

        for (let i = 0; i < fulfilledItems.length; i++) {
            fulfilledItems[i].onmousedown = () => {
                fulfilledBtn.setAttribute('data-fulfilled-orders', fulfilledItems[i].value);
                fulfilledBtn.classList.remove('ce-active');
                fulfilledList.style.display = 'none';
                fulfilledText.innerText = fulfilledItems[i].querySelector('span').innerText.replace(/(\r\n|\n|\r)/gm, "");
                if (fulfilledText.innerText === 'No') {
                    fulfilledFromDate.setAttribute('disabled', 'true');
                } else {
                    fulfilledFromDate.removeAttribute('disabled');
                }
            }
        }

        function toggleDropDown(btn, lst) {
            if (!btn.classList.contains('ce-active')) {
                btn.classList.add('ce-active');
                lst.style.display = "block";
            } else {
                btn.classList.remove('ce-active');
                lst.style.display = "none";
            }
        }
    }
);
require([
    'jquery',
    'mage/translate',
    'mage/calendar'
], function ($, $t) {
    $('#ce-import-fulfilled-date').datepicker({
        changeMonth: true,
        changeYear: true,
        showOptions: true,
        showButtonPanel: true,
        currentText: $t('Go Today'),
        closeText: $t('Close')
    });
})
