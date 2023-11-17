var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let storeViews = document.getElementsByClassName('store-switcher-store-view'),
            button = document.getElementById('ce-store-change-button');

        for (let i = 0; i < storeViews.length; i++) {
            storeViews[i].onclick = function () {
                button.innerText = storeViews[i].innerText;
                button.setAttribute('data-store-id', storeViews[i].value);
                button.classList.remove('active');
                button.parentElement.classList.remove('active');
            };
        }
    }
);
