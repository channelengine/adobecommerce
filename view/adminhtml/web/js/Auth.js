var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let button = document.getElementById('ceAuth'),
            url = document.getElementById('ceAuthUrl'),
            ajaxService = ChannelEngine.ajaxService,
            storeSwitcher = document.getElementById('ce-store-change-button'),
            storeList = document.getElementById('ce-store-change-list'),
            storeViews = document.getElementsByClassName('ce-store-view'),
            storeViewText = document.getElementById('ce-dropdown-text');

        ChannelEngine.loader.hide();

        for (let i = 0; i < storeViews.length; i++) {
            if (!storeViews[i].classList.contains("ce-disabled")) {
                storeViews[i].onmousedown = () => {
                    storeSwitcher.setAttribute('data-store-id', storeViews[i].value);
                    storeSwitcher.classList.remove('ce-active');
                    storeList.style.display = "none";
                    storeViewText.innerText = storeViews[i].getElementsByClassName('ce-account-store')[0].innerText;
                }
            }
        }

        storeSwitcher.onclick = (event) => {
            event.preventDefault();
            if (!storeSwitcher.classList.contains('ce-active')) {
                storeSwitcher.classList.add('ce-active');
                storeList.style.display = "block";
            } else {
                storeSwitcher.classList.remove('ce-active');
                storeList.style.display = "none";
            }
        }

        storeSwitcher.onfocusout = () => {
            storeSwitcher.classList.remove('ce-active');
            storeList.style.display = "none";
        }

        button.onclick = () => {
            const apiKey = document.getElementById('ceApiKey'),
                accountName = document.getElementById('ceAccountName'),
                storeView = document.getElementById('ce-store-change-button');

            ChannelEngine.loader.show();

            ajaxService.post(
                url.value + '?form_key=' + window.FORM_KEY + '&storeId=' + storeView.getAttribute('data-store-id'),
                {
                    apiKey: apiKey.value,
                    accountName: accountName.value,
                    storeId: storeView.getAttribute('data-store-id')
                },
                function (response) {
                    if (response.success) {
                        let stateUrl = document.getElementById('ce-state-url');
                        window.location.assign(stateUrl.value + '?storeId=' + storeView.getAttribute('data-store-id'));
                    } else {
                        ChannelEngine.loader.hide();
                        ChannelEngine.notificationService.removeNotifications();
                        ChannelEngine.notificationService.addNotification(response.message, false);
                    }
                });
        }
    }
);
