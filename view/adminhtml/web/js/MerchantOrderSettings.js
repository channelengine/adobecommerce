var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let unknownLinesBtn = document.getElementById('ce-unknown-lines'),
            merchantFulfilledButton = document.getElementById('ce-merchant-fulfilled'),
            merchantFulfilledButtonText = document.getElementById('ce-merchant-fulfilled-text'),
            merchantFulfilledList = document.getElementById('ce-merchant-fulfilled-list'),
            yesMerchantFulfilled = document.getElementById('ce-merchant-fulfilled-item-yes'),
            noMerchantFulfilled = document.getElementById('ce-merchant-fulfilled-item-no'),
            shipmentsSyncButton = document.getElementById('ce-shipments-sync'),
            shipmentsSyncButtonText = document.getElementById('ce-shipments-sync-text'),
            shipmentsSyncList = document.getElementById('ce-shipments-sync-list'),
            shipmentsSyncYes = document.getElementById('ce-shipments-sync-item-yes'),
            shipmentsSyncNo = document.getElementById('ce-shipments-sync-item-no'),
            cancellationsSyncButton = document.getElementById('ce-cancellations-sync'),
            cancellationsSyncButtonText = document.getElementById('ce-cancellations-sync-text'),
            cancellationsSyncList = document.getElementById('ce-cancellations-sync-list'),
            cancellationsSyncYes = document.getElementById('ce-cancellations-sync-item-yes'),
            cancellationsSyncNo = document.getElementById('ce-cancellations-sync-item-no'),
            returnsSyncButton = document.getElementById('ce-returns-sync'),
            marketplaceFulfilledButton = document.getElementById('ce-import-fulfilled'),
            incomingOrderStatusButton = document.getElementById('ce-incoming-order-status'),
            shippedOrderStatusButton = document.getElementById('ce-shipped-order-status'),
            marketplaceOrderStatusButton = document.getElementById('ce-fulfilled-order-status');

        merchantFulfilledButton.onclick = () => {
            toggleDropDown(merchantFulfilledButton, merchantFulfilledList);
        }

        merchantFulfilledButton.onfocusout = () => {
            merchantFulfilledButton.classList.remove('ce-active');
            merchantFulfilledList.style.display = "none";
        }

        yesMerchantFulfilled.onmousedown = () => {
            merchantFulfilledButton.classList.remove('ce-active');
            merchantFulfilledButton.setAttribute('data-merchant-fulfilled-orders-enabled', '1');
            merchantFulfilledList.style.display = "none";
            unknownLinesBtn.removeAttribute('disabled');
            shipmentsSyncButton.removeAttribute('disabled');
            cancellationsSyncButton.removeAttribute('disabled');
            incomingOrderStatusButton.removeAttribute('disabled');
            shippedOrderStatusButton.removeAttribute('disabled');
            marketplaceOrderStatusButton.removeAttribute('disabled');

            if (returnsSyncButton) {
                returnsSyncButton.removeAttribute('disabled');
            }

            merchantFulfilledButtonText.innerText = yesMerchantFulfilled.innerText.replace(/\s/g, '');
        }

        noMerchantFulfilled.onmousedown = () => {
            merchantFulfilledButton.setAttribute('data-merchant-fulfilled-orders-enabled', '0');
            merchantFulfilledButton.classList.remove('ce-active');
            merchantFulfilledList.style.display = "none";
            unknownLinesBtn.setAttribute('disabled', 'true');
            shipmentsSyncButton.setAttribute('disabled', 'true');
            cancellationsSyncButton.setAttribute('disabled', 'true');

            if (returnsSyncButton) {
                returnsSyncButton.setAttribute('disabled', 'true');
            }

            merchantFulfilledButtonText.innerText = noMerchantFulfilled.innerText.replace(/\s/g, '');
            shipmentsSyncButtonText.innerText = shipmentsSyncNo.innerText.replace(/\s/g, '');
            cancellationsSyncButtonText.innerText = cancellationsSyncNo.innerText.replace(/\s/g, '');

            if (parseInt(marketplaceFulfilledButton.getAttribute('data-fulfilled-orders')) === 0) {
                incomingOrderStatusButton.setAttribute('disabled', 'true');
                shippedOrderStatusButton.setAttribute('disabled', 'true');
                marketplaceOrderStatusButton.setAttribute('disabled', 'true');
            }
        }

        shipmentsSyncButton.onclick = () => {
            toggleDropDown(shipmentsSyncButton, shipmentsSyncList);
        }

        shipmentsSyncButton.onfocusout = () => {
            shipmentsSyncButton.classList.remove('ce-active');
            shipmentsSyncList.style.display = "none";
        }

        shipmentsSyncYes.onmousedown = () => {
            shipmentsSyncButton.setAttribute('data-shipments-sync-enabled', '1');
            shipmentsSyncButton.classList.remove('ce-active');
            shipmentsSyncList.style.display = "none";
            shipmentsSyncButtonText.innerText = shipmentsSyncYes.innerText.replace(/\s/g, '');
        }

        shipmentsSyncNo.onmousedown = () => {
            shipmentsSyncButton.setAttribute('data-shipments-sync-enabled', '0');
            shipmentsSyncButton.classList.remove('ce-active');
            shipmentsSyncList.style.display = "none";
            shipmentsSyncButtonText.innerText = shipmentsSyncNo.innerText.replace(/\s/g, '');
        }


        cancellationsSyncButton.onclick = () => {
            toggleDropDown(cancellationsSyncButton, cancellationsSyncList);
        }

        cancellationsSyncButton.onfocusout = () => {
            cancellationsSyncButton.classList.remove('ce-active');
            cancellationsSyncList.style.display = "none";
        }

        cancellationsSyncYes.onmousedown = () => {
            cancellationsSyncButton.setAttribute('data-cancellations-sync-enabled', '1');
            cancellationsSyncButton.classList.remove('ce-active');
            cancellationsSyncList.style.display = "none";
            cancellationsSyncButtonText.innerText = cancellationsSyncYes.innerText.replace(/\s/g, '');
        }

        cancellationsSyncNo.onmousedown = () => {
            cancellationsSyncButton.setAttribute('data-cancellations-sync-enabled', '0');
            cancellationsSyncButton.classList.remove('ce-active');
            cancellationsSyncList.style.display = "none";
            cancellationsSyncButtonText.innerText = cancellationsSyncNo.innerText.replace(/\s/g, '');
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
