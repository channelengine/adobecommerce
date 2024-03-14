var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let inventoryItems = document.getElementsByClassName('ce-stock-option'),
            stockButton  = document.getElementById('ce-stock'),
            stockButtonText = document.getElementById('ce-stock-text'),
            stockList = document.getElementById('ce-stock-list'),
            yesStock = document.getElementById('ce-stock-item-yes'),
            noStock = document.getElementById('ce-stock-item-no'),
            stockInventory = document.getElementById('ce-inventory-select'),
            stockQuantity = document.getElementById('ceStockQuantity'),
            enableMSI = document.getElementById('ce-enable-msi'),
            yesMSI = document.getElementById('ce-msi-item-yes'),
            noMSI = document.getElementById('ce-msi-item-no'),
            selectInventoriesSection = document.getElementById('ce-select-inventories-section'),
            msiList = document.getElementById('ce-msi-list'),
            msiText = document.getElementById('ce-msi-text'),
            enableMSIValue = document.getElementById('ce-enable-msi').getAttribute('msi-enabled'),
            msiEnabledInShop = document.getElementById('ce-enable-msi').getAttribute('msi-enabled-in-shop');

        if (msiEnabledInShop === '0') {
            enableMSI.setAttribute('disabled', 'true');
            enableMSI.setAttribute('msi-enabled', '0');
            msiText.innerText = noMSI.innerText.replace(/\s/g, '');
            selectInventoriesSection.style.display = 'none';
        } else {
            enableMSI.removeAttribute('disabled');
            enableMSIValue === '0' ? selectInventoriesSection.style.display = 'none' : selectInventoriesSection.style.display = '';
        }

        for (let i = 0; i < inventoryItems.length; i++) {
            inventoryItems[i].onclick = () => {
                if (inventoryItems[i].selected) {
                    inventoryItems[i].selected = 'selected';
                } else {
                    inventoryItems[i].selected = false;
                }
            }
        }

        stockButton.onclick = () => {
            toggleDropDown(stockButton, stockList);
        }

        stockButton.onfocusout = () => {
            stockButton.classList.remove('ce-active');
            stockList.style.display = "none";
        }

        enableMSI.onclick = () => {
            toggleDropDown(enableMSI, msiList);
        }

        enableMSI.onfocusout = () => {
            enableMSI.classList.remove('ce-active');
            msiList.style.display = "none";
        }

        yesStock.onmousedown = () => {
            stockButton.setAttribute('stock-enabled', '1');
            stockButton.classList.remove('ce-active');
            stockList.style.display = "none";
            stockInventory.removeAttribute('disabled');
            stockQuantity.removeAttribute('disabled');
            stockButtonText.innerText = yesStock.innerText.replace(/\s/g, '');
            if (msiEnabledInShop === '1') {
                enableMSI.removeAttribute('disabled');
            }
        }

        noStock.onmousedown = () => {
            stockButton.setAttribute('stock-enabled', '0');
            stockButton.classList.remove('ce-active');
            stockList.style.display = "none";
            stockInventory.setAttribute('disabled', 'true');
            stockQuantity.setAttribute('disabled', 'true');
            stockButtonText.innerText = noStock.innerText.replace(/\s/g, '');
            enableMSI.setAttribute('disabled', 'true');
        }

        yesMSI.onmousedown = () => {
            enableMSI.setAttribute('msi-enabled', '1');
            enableMSI.classList.remove('ce-active');
            msiList.style.display = "none";
            msiText.innerText = yesMSI.innerText.replace(/\s/g, '');
            selectInventoriesSection.style.display = '';
        }

        noMSI.onmousedown = () => {
            enableMSI.setAttribute('msi-enabled', '0');
            enableMSI.classList.remove('ce-active');
            msiList.style.display = "none";
            msiText.innerText = noMSI.innerText.replace(/\s/g, '');
            selectInventoriesSection.style.display = 'none';
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
