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
            returnsSyncButton = document.getElementById('ce-returns-sync'),
            incomingOrderStatusBtn = document.getElementById('ce-incoming-order-status'),
            incomingOrderStatusList = document.getElementById('ce-incoming-order-status-list'),
            incomingOrderStatusItems = document.getElementsByClassName('ce-incoming-order-status-item'),
            incomingOrderStatusText = document.getElementById('ce-incoming-order-status-text'),
            shippedOrderStatusBtn = document.getElementById('ce-shipped-order-status'),
            shippedOrderStatusList = document.getElementById('ce-shipped-order-status-list'),
            shippedOrderStatusItems = document.getElementsByClassName('ce-shipped-order-status-item'),
            shippedOrderStatusText = document.getElementById('ce-shipped-order-status-text'),
            fulfilledOrderStatusBtn = document.getElementById('ce-fulfilled-order-status'),
            fulfilledOrderStatusList = document.getElementById('ce-fulfilled-order-status-list'),
            fulfilledOrderStatusItems = document.getElementsByClassName('ce-fulfilled-order-status-item'),
            fulfilledOrderStatusText = document.getElementById('ce-fulfilled-order-status-text');

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
                        orderStatusMappings: {
                            statusOfIncomingOrders: incomingOrderStatusBtn.getAttribute('data-incoming-order-status').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, ''),
                            statusOfShippedOrders: shippedOrderStatusBtn.getAttribute('data-shipped-order-status').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, ''),
                            statusOfFulfilledOrders: fulfilledOrderStatusBtn.getAttribute('data-fulfilled-order-status').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, ''),
                        },
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

        addListenerToDropDownButton(unknownLinesBtn, unknownLinesList);
        addListenerToDropDownButton(fulfilledBtn, fulfilledList);
        addListenerToDropDownButton(incomingOrderStatusBtn, incomingOrderStatusList);
        addListenerToDropDownButton(shippedOrderStatusBtn, shippedOrderStatusList);
        addListenerToDropDownButton(fulfilledOrderStatusBtn, fulfilledOrderStatusList);

        function addListenerToDropDownButton(button, list) {
            button.onfocusout = () => {
                button.classList.remove('ce-active');
                list.style.display = "none";
            }
            button.onclick = () => {
                toggleDropDown(button, list)
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
                    if (parseInt(merchantSyncButton.getAttribute('data-merchant-fulfilled-orders-enabled')) === 0) {
                        incomingOrderStatusBtn.setAttribute('disabled', 'true');
                        shippedOrderStatusBtn.setAttribute('disabled', 'true');
                        fulfilledOrderStatusBtn.setAttribute('disabled', 'true');
                    }
                } else {
                    incomingOrderStatusBtn.removeAttribute('disabled');
                    shippedOrderStatusBtn.removeAttribute('disabled');
                    fulfilledOrderStatusBtn.removeAttribute('disabled');
                    fulfilledFromDate.removeAttribute('disabled');
                }
            }
        }

        addEventOnOptionItems(unknownLinesItems, unknownLinesBtn, unknownLinesList, unknownLinesText, 'data-unknown-lines')
        addEventOnOptionItems(incomingOrderStatusItems, incomingOrderStatusBtn, incomingOrderStatusList, incomingOrderStatusText, 'data-incoming-order-status')
        addEventOnOptionItems(shippedOrderStatusItems, shippedOrderStatusBtn, shippedOrderStatusList, shippedOrderStatusText, 'data-shipped-order-status')
        addEventOnOptionItems(fulfilledOrderStatusItems, fulfilledOrderStatusBtn, fulfilledOrderStatusList, fulfilledOrderStatusText, 'data-fulfilled-order-status')

        function addEventOnOptionItems(items, button, list, text, attribute) {
            for (let i = 0; i < items.length; i++) {
                items[i].onmousedown = () => {
                    button.setAttribute(attribute, items[i].getAttribute('value'));
                    button.classList.remove('ce-active');
                    list.style.display = 'none';
                    text.innerText = items[i].querySelector('span').innerText.replace(/(\r\n|\n|\r)/gm, "");
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
