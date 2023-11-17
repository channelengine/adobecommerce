if (!window.ChannelEngine) {
    window.ChannelEngine = {};
}

(function () {
    function ProductService() {
        this.disablePriceSettingsFields = function (button, attributeButton, customerGroupBtn, attributeQuantity) {
            button.setAttribute('disabled', 'true');
            attributeButton.setAttribute('disabled', 'true');
            customerGroupBtn.setAttribute('disabled', 'true');
            attributeQuantity.setAttribute('disabled', 'true');
            attributeQuantity.classList.add('ce-disabled-product');
        }

        this.disableStockSynchronizationFields = function (stockBtn, inventory, stockQuantity) {
            stockBtn.setAttribute('disabled', 'true');
            inventory.setAttribute('disabled', 'true');
            stockQuantity.setAttribute('disabled', 'true');
            stockQuantity.classList.add('ce-disabled-product');
            inventory.classList.add('ce-disabled-product');
        }

        this.disableAttributeMappingsFields = function (attributeName,
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
                                                        ean) {
            attributeName.setAttribute('disabled', 'true');
            attributeDescription.setAttribute('disabled', 'true');
            attributeCategory.setAttribute('disabled', 'true');
            attributeShippingCost.setAttribute('disabled', 'true');
            attributeMSRP.setAttribute('disabled', 'true');
            attributePurchasePrice.setAttribute('disabled', 'true');
            attributeShippingTime.setAttribute('disabled', 'true');
            attributeBrand.setAttribute('disabled', 'true');
            attributeColor.setAttribute('disabled', 'true');
            attributeSize.setAttribute('disabled', 'true');
            attributeEan.setAttribute('disabled', 'true');
            attributeProductName.setAttribute('disabled', 'true');
            ean.classList.remove('ce-required-attribute');
        }

        this.disableExtraDataMappingFields = function (newAttributeBtn) {
            newAttributeBtn.setAttribute('disabled', 'true');
            let extraDataMappings = document.querySelectorAll('.ce-input-extra-data-mapping');

            extraDataMappings.forEach(extraData => {
                let elements = extraData.firstElementChild.children;
                elements.item(0).setAttribute('disabled', 'true');
            });

            extraDataMappings.forEach(mapping => {
                let elements = mapping.getElementsByClassName('ce-small-number-input');
                elements[0].setAttribute('disabled', 'true');
                elements[0].classList.add('ce-disabled-product');
            });
        }

        this.enablePriceSettingsFields = function (button, attributeButton, customerGroupBtn, attributeQuantity) {
            button.removeAttribute('disabled');
            attributeButton.removeAttribute('disabled');
            customerGroupBtn.removeAttribute('disabled');
            attributeQuantity.removeAttribute('disabled');
            attributeQuantity.classList.remove('ce-disabled-product');
        }

        this.enableStockSynchronizationFields = function (stockBtn, inventory, stockQuantity) {
            stockBtn.removeAttribute('disabled');
            inventory.removeAttribute('disabled');
            stockQuantity.removeAttribute('disabled');
            stockQuantity.classList.remove('ce-disabled-product');
            inventory.classList.remove('ce-disabled-product');
        }

        this.enableAttributeMappingsFields = function (attributeName,
                                                       attributeDescription, attributeCategory, attributeShippingCost,
                                                       attributeMSRP, attributePurchasePrice, attributeShippingTime,
                                                       attributeBrand, attributeColor, attributeSize, attributeEan,
                                                       attributeProductName, ean) {
            attributeName.removeAttribute('disabled');
            attributeDescription.removeAttribute('disabled');
            attributeCategory.removeAttribute('disabled');
            attributeShippingCost.removeAttribute('disabled');
            attributeMSRP.removeAttribute('disabled');
            attributePurchasePrice.removeAttribute('disabled');
            attributeShippingTime.removeAttribute('disabled');
            attributeBrand.removeAttribute('disabled');
            attributeColor.removeAttribute('disabled');
            attributeSize.removeAttribute('disabled');
            attributeEan.removeAttribute('disabled');
            attributeProductName.removeAttribute('disabled');

            if (!ean.getAttribute('data-ean-attribute') || ean.getAttribute('data-ean-attribute') === 'not_mapped') {
                ean.classList.add('ce-required-attribute');
            }
        }

        this.enableExtraDataMappingFields = function (newAttributeBtn) {
            newAttributeBtn.removeAttribute('disabled');
            let extraDataMappings = document.querySelectorAll('.ce-input-extra-data-mapping');

            extraDataMappings.forEach(extraData => {
                let elements = extraData.firstElementChild.children;
                elements.item(0).removeAttribute('disabled');
            });

            extraDataMappings.forEach(mapping => {
                let elements = mapping.getElementsByClassName('ce-small-number-input');
                elements[0].removeAttribute('disabled');
                elements[0].classList.remove('ce-disabled-product');
            });
        }

        this.exportProductsSelected = function (
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
            newAttributeBtn
        ) {
            exportProductsButton.setAttribute('data-group-export-products', '1');
            exportProductsList.style.display = "none";
            exportProductsButtonText.innerText = yesExportProducts.innerText.replace(/\s/g, '');

            this.enablePriceSettingsFields(button, attributeButton, customerGroupBtn, attributeQuantity);
            this.enableStockSynchronizationFields(stockBtn, inventory, stockQuantity);
            this.enableAttributeMappingsFields(
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
            this.enableExtraDataMappingFields(newAttributeBtn);
        }

        this.exportProductsNotSelected = function (
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
            newAttributeBtn
        ) {
            exportProductsButton.setAttribute('data-group-export-products', '0');
            exportProductsList.style.display = "none";
            exportProductsButtonText.innerText = noExportProducts.innerText.replace(/\s/g, '');

            this.disablePriceSettingsFields(button, attributeButton, customerGroupBtn, attributeQuantity);
            this.disableStockSynchronizationFields(stockBtn, inventory, stockQuantity);
            this.disableAttributeMappingsFields(
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
            this.disableExtraDataMappingFields(newAttributeBtn);
        }
    }

    ChannelEngine.productsService = new ProductService();
})();
