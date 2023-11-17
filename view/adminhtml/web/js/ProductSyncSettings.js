var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let saveButton = document.getElementById('ceSave'),
            url = document.getElementById('ceProductSave'),
            ajaxService = ChannelEngine.ajaxService,
            extraDataUrl = document.getElementById('ceProductExtraData'),
            storeId = document.getElementById('ce-store-id');


        ajaxService.get(
            extraDataUrl.value + '?storeId=' + storeId.value,
            function (response) {
                if (response.extra_data_mapping.length === 0) {
                    return;
                }

                Object.entries(response.extra_data_mapping).forEach(mapping => {
                    const [key, value] = mapping;
                    let element = ChannelEngine.ExtraDataService.makeExtraDataForm(value);
                    element.children[1].children[0].value = key;
                });
                ChannelEngine.loader.hide();
            }
        );

        ChannelEngine.loader.hide();

        saveButton.onclick = () => {
            ChannelEngine.loader.show();
            const groupPricing = document.getElementById('ce-pricing'),
                priceAttribute = document.getElementById('ce-pricing-attribute-btn'),
                customerGroup = document.getElementById('ce-customer-group-btn'),
                quantity = document.getElementById('ce-attribute-quantity'),
                inventoryItems = document.getElementsByClassName('ce-stock-option'),
                selectedInventories = [],
                enableStockSync = document.getElementById('ce-stock').getAttribute('stock-enabled'),
                stockQuantity = document.getElementById('ceStockQuantity'),
                productNumber = document.getElementById('ce-attribute-product-number'),
                name = document.getElementById('ce-attribute-name'),
                description = document.getElementById('ce-attribute-description'),
                category = document.getElementById('ce-attribute-category'),
                shippingCost = document.getElementById('ce-attribute-shipping-cost'),
                msrp = document.getElementById('ce-attribute-msrp'),
                purchasePrice = document.getElementById('ce-attribute-purchase-price'),
                shippingTime = document.getElementById('ce-attribute-shipping-time'),
                brand = document.getElementById('ce-attribute-brand'),
                color = document.getElementById('ce-attribute-color'),
                size = document.getElementById('ce-attribute-size'),
                ean = document.getElementById('ce-attribute-ean'),
                groupPricingValue = groupPricing.getAttribute('data-group-pricing').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, ''),
                storeId = document.getElementById('ce-store-id'),
                extraDataMappings = document.querySelectorAll('.ce-input-extra-data-mapping'),
                duplicatesText = document.getElementById('ceExtraDataDuplicatesText').value,
                duplicatesHeaderText = document.getElementById('ceExtraDataDuplicatesHeader').value,
                exportProducts = document.getElementById('ce-export-products'),
                exportProductsValue = exportProducts.getAttribute('data-group-export-products').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '');

            if (exportProductsValue === '1' && (!ean.getAttribute('data-ean-attribute') || ean.getAttribute('data-ean-attribute') === 'not_mapped')) {
                ean.classList.add('ce-required-attribute');
                ChannelEngine.loader.hide();

                return;
            }

            for (let i = 0; i < inventoryItems.length; i++) {
                if (inventoryItems[i].selected) {
                    selectedInventories.push(inventoryItems[i].value);
                }
            }

            let extraData = {},
                isExtraDataValid = true;

            extraDataMappings.forEach(mapping => {
                if (mapping.id === 'ceExtraDataForm') {
                    return;
                }

                let elements = mapping.getElementsByClassName('ce-small-number-input');
                let key = elements.item(0).value;
                if (!key || Object.keys(extraData).includes(key)) {
                    isExtraDataValid = false;
                    ChannelEngine.modalService.showModal(
                        duplicatesHeaderText,
                        '<div>' +
                        '<label>' + duplicatesText + '</label>' +
                        '</div>',
                        null,
                        null
                    );
                    return;
                }
                extraData[key] = mapping.getElementsByClassName('ce-extra-data-dropdown')[0].getAttribute('data-mapping');
            });

            if (!isExtraDataValid) {
                ChannelEngine.loader.hide();

                return;
            }

            ajaxService.post(
                url.value + '?form_key=' + window.FORM_KEY + '&storeId=' + storeId.value,
                {
                    groupPricing: groupPricingValue,
                    priceAttribute: groupPricingValue === '0' ? priceAttribute.getAttribute('data-price-attribute-id').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') : '',
                    customerGroup: groupPricingValue === '1' ? customerGroup.getAttribute('data-customer-group-id').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') : '',
                    quantity: groupPricingValue === '1' || enableStockSync === '0' ? quantity.value : '',
                    selectedInventories: selectedInventories,
                    enableStockSync: enableStockSync,
                    stockQuantity: stockQuantity.value,
                    attributeMappings: {
                        productNumber: productNumber.getAttribute('data-product-number-attribute'),
                        name: name.getAttribute('data-name-attribute'),
                        nameType: name.getAttribute('data-name-type'),
                        description: description.getAttribute('data-description-attribute'),
                        descriptionType: description.getAttribute('data-description-type'),
                        category: category.getAttribute('data-category-attribute'),
                        categoryType: category.getAttribute('data-category-type'),
                        shippingCost: shippingCost.getAttribute('data-shipping-cost-attribute'),
                        msrp: msrp.getAttribute('data-msrp-attribute'),
                        purchasePrice: purchasePrice.getAttribute('data-purchase-price-attribute'),
                        shippingTime: shippingTime.getAttribute('data-shipping-time-attribute'),
                        shippingTimeType: shippingTime.getAttribute('data-shipping-time-type'),
                        brand: brand.getAttribute('data-brand-attribute'),
                        brandType: brand.getAttribute('data-brand-type'),
                        color: color.getAttribute('data-color-attribute'),
                        colorType: color.getAttribute('data-color-type'),
                        size: size.getAttribute('data-size-attribute'),
                        sizeType: size.getAttribute('data-size-type'),
                        ean: ean.getAttribute('data-ean-attribute'),
                        eanType: ean.getAttribute('data-ean-type')
                    },
                    extraDataMappings: extraData,
                    exportProducts: exportProductsValue === '1'
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
            );
        }
    }
);
