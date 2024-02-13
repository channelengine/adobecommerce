var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let button = document.getElementById('ce-pricing'),
            list = document.getElementById('ce-pricing-list'),
            noPricing = document.getElementById('ce-pricing-item-no'),
            yesPricing = document.getElementById('ce-pricing-item-yes'),
            priceAttribute = document.getElementById('ce-price-attribute'),
            buttonText = document.getElementById('ce-pricing-text'),
            attributeButton = document.getElementById('ce-pricing-attribute-btn'),
            attributesList = document.getElementById('ce-attributes-list'),
            customerGroupQuantity = document.getElementById('ce-price-attribute-quantity'),
            customerGroup = document.getElementById('ce-customer-group'),
            customerGroupBtn = document.getElementById('ce-customer-group-btn'),
            customerGroupList = document.getElementById('ce-customer-group-list'),
            exportProductsButton = document.getElementById('ce-export-products'),
            exportProductsList = document.getElementById('ce-export-products-list'),
            noExportProducts = document.getElementById('ce-export-products-item-no'),
            yesExportProducts = document.getElementById('ce-export-products-item-yes'),
            exportProductsButtonText = document.getElementById('ce-export-products-text'),
            stockBtn = document.getElementById('ce-stock'),
            threeLevelSyncBtn = document.getElementById('ce-three-level-sync'),
            attributeThreeLevelSync = document.getElementById('ce-attribute-three-level-sync'),
            inventory = document.getElementById('ce-inventory-select'),
            stockQuantity = document.getElementById('ceStockQuantity'),
            attributeName = document.getElementById('ce-attribute-name'),
            attributeDescription = document.getElementById('ce-attribute-description'),
            attributeCategory = document.getElementById('ce-attribute-category'),
            attributeShippingCost = document.getElementById('ce-attribute-shipping-cost'),
            attributeMSRP = document.getElementById('ce-attribute-msrp'),
            attributePurchasePrice = document.getElementById('ce-attribute-purchase-price'),
            attributeShippingTime = document.getElementById('ce-attribute-shipping-time'),
            attributeBrand = document.getElementById('ce-attribute-brand'),
            attributeColor = document.getElementById('ce-attribute-color'),
            attributeSize = document.getElementById('ce-attribute-size'),
            attributeEan = document.getElementById('ce-attribute-ean'),
            attributeProductName = document.getElementById('ce-attribute-product-number'),
            newAttributeBtn = document.getElementById('ceAddNewAttribute'),
            ean = document.getElementById('ce-attribute-ean'),
            attributeQuantity = document.getElementById('ce-attribute-quantity'),
            enableMSI = document.getElementById('ce-enable-msi');

        addAttributeItemsListeners();
        addCustomerGroupItemsListeners();

        if(exportProductsButton.getAttribute('data-group-export-products').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') === '1') {
            enableFields();
        } else {
            disableFields();
        }

        button.onclick = () => {
            toggleDropDown(button, list);
        }

        button.onfocusout = () => {
            button.classList.remove('ce-active');
            list.style.display = "none";
        }

        exportProductsButton.onclick = () => {
            toggleDropDown(exportProductsButton, exportProductsList);
        }

        exportProductsButton.onfocusout = () => {
            exportProductsButton.classList.remove('ce-active');
            exportProductsList.style.display = "none";
        }

        attributeButton.onclick = () => {
            toggleDropDown(attributeButton, attributesList);
        }

        attributeButton.onfocusout = () => {
            attributeButton.classList.remove('ce-active');
            attributesList.style.display = "none";
        }

        customerGroupBtn.onclick = () => {
            toggleDropDown(customerGroupBtn, customerGroupList);
        }

        customerGroupBtn.onfocusout = () => {
            customerGroupBtn.classList.remove('ce-active');
            customerGroupList.style.display = "none";
        }

        noPricing.onmousedown = () => {
            button.setAttribute('data-group-pricing', '0');
            button.classList.remove('ce-active');
            list.style.display = "none";
            priceAttribute.style.display = "flex";
            customerGroupQuantity.style.display = "none";
            customerGroup.style.display = "none";
            buttonText.innerText = noPricing.innerText.replace(/\s/g, '');
        }

        yesPricing.onmousedown = () => {
            button.setAttribute('data-group-pricing', '1');
            button.classList.remove('ce-active');
            list.style.display = "none";
            priceAttribute.style.display = "none";
            customerGroup.style.display = "flex";
            customerGroupQuantity.style.display = "flex";
            buttonText.innerText = yesPricing.innerText.replace(/\s/g, '');
        }

        yesExportProducts.onmousedown = () => {
            ChannelEngine.productsService.exportProductsSelected(
                yesExportProducts,
                exportProductsButton,
                exportProductsList,
                exportProductsButtonText,
                button,
                attributeButton,
                customerGroupBtn,
                attributeQuantity,
                stockBtn,
                inventory,
                stockQuantity,
                threeLevelSyncBtn,
                attributeThreeLevelSync,
                attributeName,
                attributeDescription,
                attributeCategory,
                attributeShippingCost,
                attributeMSRP,
                attributePurchasePrice,
                attributeShippingTime,
                attributeBrand,
                attributeColor,
                attributeSize,
                attributeEan,
                attributeProductName,
                ean,
                newAttributeBtn,
                enableMSI
            );
        }

        noExportProducts.onmousedown = () => {
            ChannelEngine.productsService.exportProductsNotSelected(
                noExportProducts,
                exportProductsButton,
                exportProductsList,
                exportProductsButtonText,
                button,
                attributeButton,
                customerGroupBtn,
                attributeQuantity,
                stockBtn,
                inventory,
                stockQuantity,
                threeLevelSyncBtn,
                attributeThreeLevelSync,
                attributeName,
                attributeDescription,
                attributeCategory,
                attributeShippingCost,
                attributeMSRP,
                attributePurchasePrice,
                attributeShippingTime,
                attributeBrand,
                attributeColor,
                attributeSize,
                attributeEan,
                attributeProductName,
                ean,
                newAttributeBtn,
                enableMSI
            );
        }

        function addCustomerGroupItemsListeners() {
            let groupItems = document.getElementsByClassName('ce-customer-group-item'),
                groupBtnText = document.getElementById('ce-customer-group-text');

            for (let i = 0; i < groupItems.length; i++) {
                groupItems[i].onmousedown = () => {
                    customerGroupBtn.setAttribute('data-customer-group-id', groupItems[i].value);
                    customerGroupBtn.classList.remove('ce-active');
                    customerGroupList.style.display = "none";
                    groupBtnText.innerText = groupItems[i].innerText;
                }
            }
        }

        function addAttributeItemsListeners() {
            let attributeItems = document.getElementsByClassName('ce-attribute-item'),
                attributeBtnText = document.getElementById('ce-pricing-attribute-text');

            for (let i = 0; i < attributeItems.length; i++) {
                attributeItems[i].onmousedown = () => {
                    attributeButton.setAttribute('data-price-attribute-id', attributeItems[i].getAttribute('value'));
                    attributeButton.classList.remove('ce-active');
                    attributesList.style.display = "none";
                    attributeBtnText.innerText = attributeItems[i].innerText;
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

        function disableFields() {
            ChannelEngine.productsService.disablePriceSettingsFields(button, attributeButton, customerGroupBtn, attributeQuantity);
            ChannelEngine.productsService.disableStockSynchronizationFields(stockBtn, inventory, stockQuantity, enableMSI);
            ChannelEngine.productsService.disableThreeLevelSynchronizationFields(threeLevelSyncBtn, attributeThreeLevelSync);
            ChannelEngine.productsService.disableAttributeMappingsFields(
                attributeName,
                attributeDescription,
                attributeCategory,
                attributeShippingCost,
                attributeMSRP,
                attributePurchasePrice,
                attributeShippingTime,
                attributeBrand,
                attributeColor,
                attributeSize,
                attributeEan,
                attributeProductName,
                ean);
            ChannelEngine.productsService.disableExtraDataMappingFields(newAttributeBtn);
        }

        function enableFields() {
            ChannelEngine.productsService.enablePriceSettingsFields(button, attributeButton, customerGroupBtn, attributeQuantity);
            ChannelEngine.productsService.enableStockSynchronizationFields(stockBtn, inventory, stockQuantity, enableMSI);
            ChannelEngine.productsService.enableThreeLevelSynchronizationFields(threeLevelSyncBtn, attributeThreeLevelSync);
            ChannelEngine.productsService.enableAttributeMappingsFields(
                attributeName,
                attributeDescription,
                attributeCategory,
                attributeShippingCost,
                attributeMSRP,
                attributePurchasePrice,
                attributeShippingTime,
                attributeBrand,
                attributeColor,
                attributeSize,
                attributeEan,
                attributeProductName,
                ean);
            ChannelEngine.productsService.enableExtraDataMappingFields(newAttributeBtn);
        }
    }
);
