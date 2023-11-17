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
            stockQuantity = document.getElementById('ceStockQuantity');

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

        yesStock.onmousedown = () => {
            stockButton.setAttribute('stock-enabled', '1');
            stockButton.classList.remove('ce-active');
            stockList.style.display = "none";
            stockInventory.removeAttribute('disabled');
            stockQuantity.removeAttribute('disabled');
            stockButtonText.innerText = yesStock.innerText.replace(/\s/g, '');
        }

        noStock.onmousedown = () => {
            stockButton.setAttribute('stock-enabled', '0');
            stockButton.classList.remove('ce-active');
            stockList.style.display = "none";
            stockInventory.setAttribute('disabled', 'true');
            stockQuantity.setAttribute('disabled', 'true');
            stockButtonText.innerText = noStock.innerText.replace(/\s/g, '');
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