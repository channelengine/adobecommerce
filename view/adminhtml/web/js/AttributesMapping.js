var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let productSku = document.getElementById('ce-product-sku'),
            productId = document.getElementById('ce-product-id'),
            productNumberBtn = document.getElementById('ce-attribute-product-number'),
            productNumberList = document.getElementById('ce-product-number-list'),
            productNumberBtnText = document.querySelector('#ce-attribute-product-number #ce-pricing-attribute-text'),
            skuWarning = document.getElementById('ce-sku-warning');

        productSku.addEventListener('mousedown', function () {
            productNumberBtn.setAttribute('data-product-number-attribute', productSku.getAttribute('value'));
            productNumberBtn.classList.remove('ce-active');
            productNumberList.style.display = "none";
            productNumberBtnText.innerText = productSku.getElementsByTagName('span')[0].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '');
            skuWarning.classList.remove('ce-hidden');
        });

        productId.addEventListener('mousedown', function () {
            productNumberBtn.setAttribute('data-product-number-attribute', productId.getAttribute('value'));
            productNumberBtn.classList.remove('ce-active');
            productNumberList.style.display = "none";
            productNumberBtnText.innerText = productId.getElementsByTagName('span')[0].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '');
            skuWarning.classList.add('ce-hidden');
        });

        addListenerToButton('ce-attribute-name', 'ce-name-list');
        addListenerToButton('ce-attribute-description', 'ce-description-list');
        addListenerToButton('ce-attribute-category', 'ce-category-list');
        addListenerToButton('ce-attribute-shipping-cost', 'ce-shipping-cost-list');
        addListenerToButton('ce-attribute-msrp', 'ce-msrp-list');
        addListenerToButton('ce-attribute-purchase-price', 'ce-purchase-price-list');
        addListenerToButton('ce-attribute-shipping-time', 'ce-shipping-time-list');
        addListenerToButton('ce-attribute-brand', 'ce-brand-list');
        addListenerToButton('ce-attribute-color', 'ce-color-list');
        addListenerToButton('ce-attribute-size', 'ce-size-list');
        addListenerToButton('ce-attribute-ean', 'ce-ean-list');
        addListenerToButton('ce-attribute-product-number', 'ce-product-number-list');
        addAttributeItemsListeners('data-name-attribute', 'data-name-type', 'ce-attribute-name', 'ce-name-list');
        addAttributeItemsListeners('data-description-attribute', 'data-description-type', 'ce-attribute-description', 'ce-description-list');
        addAttributeItemsListeners('data-category-attribute', 'data-category-type', 'ce-attribute-category', 'ce-category-list');
        addAttributeItemsListeners('data-shipping-cost-attribute', 'data-shipping-cost-type', 'ce-attribute-shipping-cost', 'ce-shipping-cost-list');
        addAttributeItemsListeners('data-msrp-attribute', 'data-msrp-type', 'ce-attribute-msrp', 'ce-msrp-list');
        addAttributeItemsListeners('data-purchase-price-attribute', 'data-purchase-price-type', 'ce-attribute-purchase-price', 'ce-purchase-price-list');
        addAttributeItemsListeners('data-shipping-time-attribute', 'data-shipping-time-type', 'ce-attribute-shipping-time', 'ce-shipping-time-list');
        addAttributeItemsListeners('data-brand-attribute', 'data-brand-type', 'ce-attribute-brand', 'ce-brand-list');
        addAttributeItemsListeners('data-color-attribute', 'data-color-type', 'ce-attribute-color', 'ce-color-list');
        addAttributeItemsListeners('data-size-attribute', 'data-size-type', 'ce-attribute-size', 'ce-size-list');
        addAttributeItemsListeners('data-ean-attribute', 'data-ean-type', 'ce-attribute-ean', 'ce-ean-list');

        function addAttributeItemsListeners(id, type, buttonId, listId) {
            let attributeItems = document.querySelectorAll('#' + listId + ' .ce-mapping-item'),
                attributeBtnText = document.querySelector('#' + buttonId + ' #ce-pricing-attribute-text'),
                attributeButton = document.getElementById(buttonId),
                attributesList = document.getElementById(listId);

            for (let i = 0; i < attributeItems.length; i++) {
                attributeItems[i].onmousedown = () => {
                    attributeButton.setAttribute(id, attributeItems[i].getAttribute('value'));
                    if (attributeItems[i].getAttribute('data-type')) {
                        attributeButton.setAttribute(type, attributeItems[i].getAttribute('data-type'));
                    }
                    attributeButton.classList.remove('ce-active');
                    attributesList.style.display = "none";
                    attributeBtnText.innerText = attributeItems[i].getElementsByTagName('span')[0].innerText;

                    if (id === 'data-ean-attribute') {
                        attributeButton.classList.remove('ce-required-attribute');
                    }
                }
            }
        }

        function addListenerToButton(buttonId, listId) {
            let button = document.getElementById(buttonId),
                list = document.getElementById(listId);

            button.onfocusout = () => {
                button.classList.remove('ce-active');
                list.style.display = "none";
            }
            button.onclick = () => {
                toggleDropDown(button, list)
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