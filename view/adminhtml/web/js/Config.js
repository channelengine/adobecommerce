var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const configDataUrl = document.getElementById('ce-config-data-url'),
            disconnectUrl = document.getElementById('ce-disconnect-url'),
            disableUrl = document.getElementById('ce-disable-url'),
            triggerSyncUrl = document.getElementById('ce-trigger-sync-url'),
            saveUrl = document.getElementById('ce-save-url'),
            disableSwitch = document.getElementById('ce-disable-switch'),
            apiKey = document.getElementById('ceApiKey'),
            accountName = document.getElementById('ceAccountName'),
            saveBtn = document.getElementById('ce-save-config'),
            saveBtnWrapper = document.getElementById('ce-save-changes'),
            enableStockSync = document.getElementById('ce-stock'),
            quantity = document.getElementById('ceStockQuantity'),
            enableThreeLevelSync = document.getElementById('ce-three-level-sync'),
            threeLevelSyncAttributeBtn = document.getElementById('ce-attribute-three-level-sync'),
            threeLevelSyncAttributeText = document.querySelector('#ce-attribute-three-level-sync #ce-three-level-sync-attribute-text'),
            threeLevelSyncAttributes = document.getElementById('ce-shipping-time-list')?.getElementsByTagName('li') ?? [],
            disconnectBtn = document.getElementById('ce-disconnect-btn'),
            syncNowBtn = document.getElementById('ce-sync-now'),
            stateUrl = document.getElementById('ce-state-url'),
            storeScope = document.getElementById('ce-store-scope'),
            groupPricingBtn = document.getElementById('ce-pricing'),
            groupPricingLabel = document.getElementById('ce-pricing-text'),
            priceAttributeBtn = document.getElementById('ce-pricing-attribute-btn'),
            priceAttributeBtnText = document.getElementById('ce-pricing-attribute-text'),
            priceAttribute = document.getElementById('ce-attributes-list')?.getElementsByTagName('li') ?? [],
            customerGroupBtn = document.getElementById('ce-customer-group-btn'),
            customerGroupText = document.getElementById('ce-customer-group-text'),
            customerGroups = document.getElementById('ce-customer-group-list')?.getElementsByTagName('li') ?? [],
            priceQuantity = document.getElementById('ce-attribute-quantity'),
            priceQuantitySection = document.getElementById('ce-price-attribute-quantity'),
            inventories = document.getElementById('ce-inventory-select')?.getElementsByTagName('option') ?? [],
            nameBtn = document.getElementById('ce-attribute-name'),
            nameText = document.querySelector('#ce-attribute-name #ce-pricing-attribute-text'),
            descriptionBtn = document.getElementById('ce-attribute-description'),
            descriptionText = document.querySelector('#ce-attribute-description #ce-pricing-attribute-text'),
            categoryBtn = document.getElementById('ce-attribute-category'),
            categoryText = document.querySelector('#ce-attribute-category #ce-pricing-attribute-text'),
            shippingCostBtn = document.getElementById('ce-attribute-shipping-cost'),
            shippingCostText = document.querySelector('#ce-attribute-shipping-cost #ce-pricing-attribute-text'),
            priceItems = document.getElementById('ce-shipping-cost-list')?.getElementsByTagName('li') ?? [],
            msrpBtn = document.getElementById('ce-attribute-msrp'),
            msrpText = document.querySelector('#ce-attribute-msrp #ce-pricing-attribute-text'),
            purchasePriceBtn = document.getElementById('ce-attribute-purchase-price'),
            purchasePriceText = document.querySelector('#ce-attribute-purchase-price #ce-pricing-attribute-text'),
            shippingTimeBtn = document.getElementById('ce-attribute-shipping-time'),
            shippingTimeText = document.querySelector('#ce-attribute-shipping-time #ce-pricing-attribute-text'),
            attributes = document.getElementById('ce-shipping-time-list')?.getElementsByTagName('li') ?? [],
            brandBtn = document.getElementById('ce-attribute-brand'),
            brandText = document.querySelector('#ce-attribute-brand #ce-pricing-attribute-text'),
            colorBtn = document.getElementById('ce-attribute-color'),
            colorText = document.querySelector('#ce-attribute-color #ce-pricing-attribute-text'),
            sizeBtn = document.getElementById('ce-attribute-size'),
            sizeText = document.querySelector('#ce-attribute-size #ce-pricing-attribute-text'),
            eanBtn = document.getElementById('ce-attribute-ean'),
            eanText = document.querySelector('#ce-attribute-ean #ce-pricing-attribute-text'),
            unknownOrderLinesBtn = document.getElementById('ce-unknown-lines'),
            unknownOrderLinesText = document.getElementById('ce-unknown-lines-text'),
            unknownOrderLinesOptions = document.getElementById('ce-unknown-lines-list')?.getElementsByTagName('li') ?? [],
            fulfilledByMarketplaceBtn = document.getElementById('ce-import-fulfilled'),
            fulfilledByMarketplaceText = document.getElementById('ce-import-fulfilled-text'),
            fulfilledByMarketplaceOptions = document.getElementById('ce-import-fulfilled-list')?.getElementsByTagName('li') ?? [],
            merchantSyncButton = document.getElementById('ce-merchant-fulfilled'),
            shipmentSyncButton = document.getElementById('ce-shipments-sync'),
            cancellationSyncButton = document.getElementById('ce-cancellations-sync'),
            returnsButton = document.getElementById('ce-returns'),
            conditionBtn = document.getElementById('ce-default-condition'),
            resolutionBtn = document.getElementById('ce-default-resolution'),
            returnSyncBtn = document.getElementById('ce-returns-sync'),
            exportProducts = document.getElementById('ce-export-products'),
            enableMSI = document.getElementById('ce-enable-msi'),
            msiEnabledInShop = document.getElementById('ce-enable-msi').getAttribute('msi-enabled-in-shop'),
            enableMSIValue = document.getElementById('ce-enable-msi').getAttribute('msi-enabled'),
            selectInventoriesSection = document.getElementById('ce-select-inventories-section'),
            noMSI = document.getElementById('ce-msi-item-no'),
            msiText = document.getElementById('ce-msi-text');

        let originalThreeLevelSyncEnabledValue = false;
        let originalThreeLevelSyncAttributeValue = false;
        ChannelEngine.loader.show();
        saveBtnWrapper.style.display = "block";
        ChannelEngine.triggerSyncService.checkStatus();

        syncNowBtn && (syncNowBtn.onclick = function() {
            ChannelEngine.triggerSyncService.showModal(triggerSyncUrl.value);
        });

        disconnectBtn && (disconnectBtn.onclick = function () {
            let header = document.getElementById('ce-disconnect-header-text'),
                btnText = document.getElementById('ce-disconnect-button-text'),
                text = document.getElementById('ce-disconnect-text'),
                content = document.getElementById('ce-disconnect-modal-content'),
                label = content.getElementsByTagName('label')[0];
            label.innerText = text.value;

            ChannelEngine.modalService.showModal(
                header.value,
                content.innerHTML,
                btnText.value,
                disconnect
            );
        });

        disableSwitch && (disableSwitch.onchange = (event) => {
            let header = document.getElementById('ce-disable-header-text'),
                btnText = document.getElementById('ce-disable-button-text'),
                text = document.getElementById('ce-disable-text'),
                content = document.getElementById('ce-disconnect-modal-content'),
                label = content.getElementsByTagName('label')[0];
            label.innerText = text.value;

            if (!event.currentTarget.checked) {
                ChannelEngine.modalService.showModal(
                    header.value,
                    content.innerHTML,
                    btnText.value,
                    () => {
                        ChannelEngine.ajaxService.get(
                            disableUrl.value + '?storeId=' + storeScope.value,
                            function (response) {
                                if (response.success) {
                                    window.location.assign(stateUrl.value + '?storeId=' + storeScope.value);
                                }
                            }
                        );
                    }
                );
            }
        });

        let disconnect = function () {
            ChannelEngine.ajaxService.get(
                disconnectUrl.value + '?storeId=' + storeScope.value,
                function (response) {
                    window.location.assign(stateUrl.value + '?storeId=' + storeScope.value);
                }
            )
        }

        configDataUrl && (ChannelEngine.ajaxService.get(
            configDataUrl.value + '?storeId=' + storeScope.value,
            function (response) {
                let productSyncCheckbox = document.getElementById('ce-product-sync-checkbox');
                apiKey.value = response.accountData.api_key;
                accountName.value = response.accountData.account_name;
                exportProducts.setAttribute('data-group-export-products', response.exportProducts);
                if(response.exportProducts) {
                    setPriceSettings(response);
                    setStockSettings(response);
                    setThreeLevelSyncSettings(response);
                    setAttributeMappings(response);
                    setExtraDataMappings(response);
                    productSyncCheckbox.removeAttribute('disabled');
                } else {
                    productSyncCheckbox.setAttribute('disabled', 'true');
                }

                setOrderSyncSetting(response);
                response.exportProducts ? enableProductSynchronizationFields() : disableProductSynchronizationFields();
                if (msiEnabledInShop === '0') {
                    enableMSI.setAttribute('disabled', 'true');
                    enableMSI.setAttribute('msi-enabled', '0');
                    msiText.innerText = noMSI.innerText.replace(/\s/g, '');
                    selectInventoriesSection.style.display = 'none';
                } else {
                    enableMSI.removeAttribute('disabled');
                    enableMSIValue === '0' ? selectInventoriesSection.style.display = 'none' : selectInventoriesSection.style.display = '';
                }

                ChannelEngine.loader.hide();
            }
        ));

        function disableProductSynchronizationFields() {
            ChannelEngine.productsService.exportProductsNotSelected(
                document.getElementById('ce-export-products-item-no'),
                document.getElementById('ce-export-products'),
                document.getElementById('ce-export-products-list'),
                document.getElementById('ce-export-products-text'),
                document.getElementById('ce-pricing'),
                document.getElementById('ce-pricing-attribute-btn'),
                document.getElementById('ce-customer-group-btn'),
                document.getElementById('ce-attribute-quantity'),
                document.getElementById('ce-stock'),
                document.getElementById('ce-inventory-select'),
                document.getElementById('ceStockQuantity'),
                document.getElementById('ce-three-level-sync'),
                document.getElementById('ce-attribute-three-level-sync'),
                document.getElementById('ce-attribute-name'),
                document.getElementById('ce-attribute-description'),
                document.getElementById('ce-attribute-category'),
                document.getElementById('ce-attribute-shipping-cost'),
                document.getElementById('ce-attribute-msrp'),
                document.getElementById('ce-attribute-purchase-price'),
                document.getElementById('ce-attribute-shipping-time'),
                document.getElementById('ce-attribute-brand'),
                document.getElementById('ce-attribute-color'),
                document.getElementById('ce-attribute-size'),
                document.getElementById('ce-attribute-ean'),
                document.getElementById('ce-attribute-product-number'),
                document.getElementById('ce-attribute-ean'),
                document.getElementById('ceAddNewAttribute'),
                document.getElementById('ce-enable-msi')
            );
        }

        function enableProductSynchronizationFields() {
            ChannelEngine.productsService.exportProductsSelected(
                document.getElementById('ce-export-products-item-yes'),
                document.getElementById('ce-export-products'),
                document.getElementById('ce-export-products-list'),
                document.getElementById('ce-export-products-text'),
                document.getElementById('ce-pricing'),
                document.getElementById('ce-pricing-attribute-btn'),
                document.getElementById('ce-customer-group-btn'),
                document.getElementById('ce-attribute-quantity'),
                document.getElementById('ce-stock'),
                document.getElementById('ce-inventory-select'),
                document.getElementById('ceStockQuantity'),
                document.getElementById('ce-three-level-sync'),
                document.getElementById('ce-attribute-three-level-sync'),
                document.getElementById('ce-attribute-name'),
                document.getElementById('ce-attribute-description'),
                document.getElementById('ce-attribute-category'),
                document.getElementById('ce-attribute-shipping-cost'),
                document.getElementById('ce-attribute-msrp'),
                document.getElementById('ce-attribute-purchase-price'),
                document.getElementById('ce-attribute-shipping-time'),
                document.getElementById('ce-attribute-brand'),
                document.getElementById('ce-attribute-color'),
                document.getElementById('ce-attribute-size'),
                document.getElementById('ce-attribute-ean'),
                document.getElementById('ce-attribute-product-number'),
                document.getElementById('ce-attribute-ean'),
                document.getElementById('ceAddNewAttribute'),
                document.getElementById('ce-enable-msi')
            );
        }

        function setOrderSyncSetting(response) {
            unknownOrderLinesBtn.setAttribute('data-unknown-lines', response.ordersData.unknownLinesHandling);
            for (let i = 0; i < unknownOrderLinesOptions.length; i++) {
                if (unknownOrderLinesOptions[i].getAttribute('value') === response.ordersData.unknownLinesHandling) {
                    unknownOrderLinesText.innerText = unknownOrderLinesOptions[i].getElementsByTagName('span')[0].innerText.replace(/(\r\n|\n|\r)/gm, "");
                }
            }
            fulfilledByMarketplaceBtn.setAttribute('data-fulfilled-orders', response.ordersData.enableOrdersByMarketplaceSync ? '1' : '0');

            let importValue = response.ordersData.enableOrdersByMarketplaceSync ? '1' : '0';
            for (let i = 0; i < fulfilledByMarketplaceOptions.length; i++) {
                if (fulfilledByMarketplaceOptions[i].getAttribute('value') === importValue) {
                    fulfilledByMarketplaceText.innerText = fulfilledByMarketplaceOptions[i].getElementsByTagName('span')[0].innerText;
                }
            }
        }
        if (enableThreeLevelSync) {
             originalThreeLevelSyncEnabledValue = enableThreeLevelSync.getAttribute('three-level-sync-enabled') === '1';
             originalThreeLevelSyncAttributeValue = document.getElementById('ce-three-level-sync-attribute-text').innerText.toUpperCase();
        }

        function setThreeLevelSyncSettings(response) {
            enableThreeLevelSync.setAttribute('three-level-sync-enabled', response.threeLevelSyncData.enableThreeLevelSync ? '1' : '0');
            threeLevelSyncAttributeBtn.setAttribute('data-three-level-sync-attribute', response.threeLevelSyncData.syncAttribute);

            for (let i = 0; i < threeLevelSyncAttributes.length; i++) {
                if (threeLevelSyncAttributes[i].getAttribute('value') === response.threeLevelSyncData.syncAttribute) {
                    threeLevelSyncAttributeText.innerText = threeLevelSyncAttributes[i].getElementsByTagName('span')[0].innerText;
                }
            }

            originalThreeLevelSyncEnabledValue = response.threeLevelSyncData.enableThreeLevelSync;
            originalThreeLevelSyncAttributeValue = response.threeLevelSyncData.syncAttribute.toUpperCase();
        }

        function setAttributeMappings(response) {
            nameBtn.setAttribute('data-name-attribute', response.attributesData.name);
            descriptionBtn.setAttribute('data-description-attribute', response.attributesData.description);
            categoryBtn.setAttribute('data-category-attribute', response.attributesData.category);
            shippingCostBtn.setAttribute('data-shipping-cost-attribute', response.attributesData.shippingCost);
            msrpBtn.setAttribute('data-msrp-attribute', response.attributesData.msrp);
            purchasePriceBtn.setAttribute('data-purchase-price-attribute', response.attributesData.purchasePrice)
            shippingTimeBtn.setAttribute('data-shipping-time-attribute', response.attributesData.shippingTime);
            brandBtn.setAttribute('data-brand-attribute', response.attributesData.brand);
            colorBtn.setAttribute('data-color-attribute', response.attributesData.color);
            sizeBtn.setAttribute('data-size-attribute', response.attributesData.size);
            eanBtn.setAttribute('data-ean-attribute', response.attributesData.ean);

            for (let i = 0; i < priceItems.length; i++) {
                if (priceItems[i].getAttribute('value') === response.attributesData.shippingCost) {
                    shippingCostText.innerText = priceItems[i].getElementsByTagName('span')[0].innerText;
                }

                if (priceItems[i].getAttribute('value') === response.attributesData.msrp) {
                    msrpText.innerText = priceItems[i].getElementsByTagName('span')[0].innerText;
                }

                if (priceItems[i].getAttribute('value') === response.attributesData.purchasePrice) {
                    purchasePriceText.innerText = priceItems[i].getElementsByTagName('span')[0].innerText;
                }
            }

            for (let i = 0; i < attributes.length; i++) {
                if (attributes[i].getAttribute('value') === response.attributesData.name) {
                    nameText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }

                if (attributes[i].getAttribute('value') === response.attributesData.description) {
                    descriptionText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }

                if (attributes[i].getAttribute('value') === response.attributesData.category) {
                    categoryText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }

                if (attributes[i].getAttribute('value') === response.attributesData.shippingTime) {
                    shippingTimeText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }

                if (attributes[i].getAttribute('value') === response.attributesData.brand) {
                    brandText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }

                if (attributes[i].getAttribute('value') === response.attributesData.color) {
                    colorText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }

                if (attributes[i].getAttribute('value') === response.attributesData.size) {
                    sizeText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }

                if (attributes[i].getAttribute('value') === response.attributesData.ean) {
                    eanText.innerText = attributes[i].getElementsByTagName('span')[0].innerText;
                }
            }
        }

        function setExtraDataMappings(response) {
            if (response.extraData.length === 0) {
                return;
            }

            Object.entries(response.extraData).forEach(mapping => {
                const [key, value] = mapping;
                let element = ChannelEngine.ExtraDataService.makeExtraDataForm(value);
                element.children[1].children[0].value = key;
            });
        }

        function setPriceSettings(response) {
            if(response.exportProducts) {
                groupPricingBtn.setAttribute('data-group-pricing', response.priceData.groupPricing ? '1' : '0');
                groupPricingLabel.innerText = response.priceData.groupPricing ?
                    document.getElementById('ce-pricing-item-yes').getElementsByTagName('span')[0].innerText :
                    document.getElementById('ce-pricing-item-no').getElementsByTagName('span')[0].innerText;

                if (!response.priceData.groupPricing) {
                    priceAttributeBtn.setAttribute('data-price-attribute-id', response.priceData.priceAttribute);
                    let text = '';
                    for (let i = 0; i < priceAttribute.length; i++) {
                        if (priceAttribute[i].getAttribute('value') === response.priceData.priceAttribute) {
                            text = priceAttribute[i].innerText;
                        }
                    }
                    priceAttributeBtnText.innerText = text;
                } else {
                    let priceAttributeSection = document.getElementById('ce-price-attribute'),
                        customerGroupSection = document.getElementById('ce-customer-group');

                    priceAttributeSection.style.display = "none";
                    priceQuantitySection.style.display = "flex";
                    customerGroupSection.style.display = "flex";
                    customerGroupBtn.setAttribute('data-customer-group-id', response.priceData.customerGroup);
                    let text = '';
                    for (let i = 0; i < customerGroups.length; i++) {
                        if (customerGroups[i].value === response.priceData.customerGroup) {
                            text = customerGroups[i].innerText;
                        }
                    }
                    customerGroupText.innerText = text;
                    priceQuantity.value = response.priceData.quantity;
                }
            } else {
                groupPricingBtn.setAttribute('data-group-pricing', '0');
                groupPricingLabel.innerText = document.getElementById('ce-pricing-item-no').getElementsByTagName('span')[0].innerText;

                if (!response.priceData.groupPricing) {
                    priceAttributeBtn.setAttribute('data-price-attribute-id', response.priceData.priceAttribute);
                    let text = '';
                    for (let i = 0; i < priceAttribute.length; i++) {
                        if (priceAttribute[i].getAttribute('value') === response.priceData.priceAttribute) {
                            text = priceAttribute[i].innerText;
                        }
                    }
                    priceAttributeBtnText.innerText = text;
                } else {
                    let priceAttributeSection = document.getElementById('ce-price-attribute'),
                        customerGroupSection = document.getElementById('ce-customer-group');

                    priceAttributeSection.style.display = "none";
                    priceQuantitySection.style.display = "flex";
                    customerGroupSection.style.display = "flex";
                    customerGroupBtn.setAttribute('data-customer-group-id', response.priceData.customerGroup);
                    let text = '';
                    for (let i = 0; i < customerGroups.length; i++) {
                        if (customerGroups[i].value === response.priceData.customerGroup) {
                            text = customerGroups[i].innerText;
                        }
                    }
                    customerGroupText.innerText = text;
                    priceQuantity.value = response.priceData.quantity;
                }
            }
        }

        function setStockSettings(response) {
            for (let i = 0; i < inventories.length; i++) {
                for (let j = 0; j < response.stockData.inventories.length; j++) {
                    if (inventories[i].value === response.stockData.inventories[j]) {
                        inventories[i].selected = 'selected';
                    }
                }
            }

            quantity.value = response.stockData.quantity;
        }

        saveBtn.onclick = function () {
            let newThreeLevelSyncEnabledValue = enableThreeLevelSync.getAttribute('three-level-sync-enabled') === '1';
            let newThreeLevelSyncAttributeValue = document.getElementById('ce-three-level-sync-attribute-text').innerText.toUpperCase();

            if (originalThreeLevelSyncEnabledValue !== newThreeLevelSyncEnabledValue ||
                (newThreeLevelSyncEnabledValue && (newThreeLevelSyncAttributeValue !== originalThreeLevelSyncAttributeValue))) {
                ChannelEngine.triggerSyncService.showSaveConfigModal(() => saveConfig(false), triggerSyncUrl.value);
            } else {
                saveConfig(true);
            }
        }

        let saveConfig = (showStartSyncModal) => {
            let productSyncCheckbox = document.getElementById('ce-product-sync-checkbox');
            if(exportProducts.getAttribute('data-group-export-products').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') === '1') {
                productSyncCheckbox.removeAttribute('disabled');
            } else {
                productSyncCheckbox.setAttribute('disabled', 'true');
            }

            ChannelEngine.loader.show();
            let selectedInventories = [];

            for (let i = 0; i < inventories.length; i++) {
                if (inventories[i].selected) {
                    selectedInventories.push(inventories[i].value);
                }
            }

            let extraData = {},
                isExtraDataValid = true,
                extraDataMappings = document.querySelectorAll('.ce-input-extra-data-mapping'),
                duplicatesText = document.getElementById('ceExtraDataDuplicatesText').value,
                duplicatesHeaderText = document.getElementById('ceExtraDataDuplicatesHeader').value;

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

            let merchantOrderSync = merchantSyncButton.getAttribute('data-merchant-fulfilled-orders-enabled');

            ChannelEngine.ajaxService.post(
                saveUrl.value + '?storeId=' + storeScope.value + '&form_key=' + window.FORM_KEY,
                {
                    apiKey: apiKey.value,
                    accountName: accountName.value,
                    groupPricing: groupPricingBtn.getAttribute('data-group-pricing'),
                    priceAttribute: priceAttributeBtn.getAttribute('data-price-attribute-id'),
                    customerGroup: customerGroupBtn.getAttribute('data-customer-group-id'),
                    quantity: priceQuantity.value,
                    selectedInventories: selectedInventories,
                    enableStockSync: enableStockSync.getAttribute('stock-enabled'),
                    enableMSI: enableMSI.getAttribute('msi-enabled'),
                    stockQuantity: quantity.value,
                    threeLevelSync: {
                        enableThreeLevelSync: enableThreeLevelSync.getAttribute('three-level-sync-enabled'),
                        syncAttribute: threeLevelSyncAttributeBtn.getAttribute('data-three-level-sync-attribute'),
                        syncAttributeType: threeLevelSyncAttributeBtn.getAttribute('data-three-level-sync-type'),
                    },
                    attributeMappings: {
                        name: nameBtn.getAttribute('data-name-attribute'),
                        nameType: nameBtn.getAttribute('data-name-type'),
                        description: descriptionBtn.getAttribute('data-description-attribute'),
                        descriptionType: descriptionBtn.getAttribute('data-description-type'),
                        category: categoryBtn.getAttribute('data-category-attribute'),
                        categoryType: categoryBtn.getAttribute('data-category-type'),
                        shippingCost: shippingCostBtn.getAttribute('data-shipping-cost-attribute'),
                        msrp: msrpBtn.getAttribute('data-msrp-attribute'),
                        purchasePrice: purchasePriceBtn.getAttribute('data-purchase-price-attribute'),
                        shippingTime: shippingTimeBtn.getAttribute('data-shipping-time-attribute'),
                        shippingTimeType: shippingTimeBtn.getAttribute('data-shipping-time-type'),
                        brand: brandBtn.getAttribute('data-brand-attribute'),
                        brandType: brandBtn.getAttribute('data-brand-type'),
                        color: colorBtn.getAttribute('data-color-attribute'),
                        colorType: colorBtn.getAttribute('data-color-type'),
                        size: sizeBtn.getAttribute('data-size-attribute'),
                        sizeType: sizeBtn.getAttribute('data-size-type'),
                        ean: eanBtn.getAttribute('data-ean-attribute'),
                        eanType: eanBtn.getAttribute('data-ean-type')
                    },
                    unknownLinesHandling: unknownOrderLinesBtn.getAttribute('data-unknown-lines'),
                    importFulfilledOrders: fulfilledByMarketplaceBtn.getAttribute('data-fulfilled-orders'),
                    merchantOrderSync: merchantOrderSync,
                    shipmentSync: merchantOrderSync === '1' ? shipmentSyncButton.getAttribute('data-shipments-sync-enabled') : '0',
                    cancellationSync: merchantOrderSync === '1' ? cancellationSyncButton.getAttribute('data-cancellations-sync-enabled') : '0',
                    returnsEnabled: returnsButton ? returnsButton.getAttribute('returns-enabled') : '',
                    defaultCondition: returnsButton && returnsButton.getAttribute('returns-enabled') === '1' ? conditionBtn.getAttribute('data-default-condition').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') : '',
                    defaultResolution: returnsButton && returnsButton.getAttribute('returns-enabled') === '1' ? resolutionBtn.getAttribute('data-default-resolution').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') : '',
                    extraDataMappings: extraData,
                    returnsSync: returnSyncBtn ? returnSyncBtn.getAttribute('data-returns-sync-enabled') : '',
                    exportProducts: exportProducts.getAttribute('data-group-export-products').replace(/(\r\n|\n|\r)/gm, "").replace(/\s+/g, '') === '1'
                },
                function (response) {
                    ChannelEngine.loader.hide();
                    if (response.success) {
                        ChannelEngine.notificationService.removeNotifications();
                        ChannelEngine.notificationService.addNotification(response.message, true);
                        showStartSyncModal && ChannelEngine.triggerSyncService.showModal(triggerSyncUrl.value);
                        originalThreeLevelSyncEnabledValue = enableThreeLevelSync.getAttribute('three-level-sync-enabled') === '1';
                        originalThreeLevelSyncAttributeValue = document.getElementById('ce-three-level-sync-attribute-text').innerText.toUpperCase();
                    } else {
                        ChannelEngine.notificationService.removeNotifications();
                        ChannelEngine.notificationService.addNotification(response.message, false);
                    }
                }
            )
        }
    }
);
