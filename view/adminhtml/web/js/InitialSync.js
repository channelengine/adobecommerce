var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const ajaxService = ChannelEngine.ajaxService,
            url = document.getElementById('ceInitialSyncUrl'),
            link = document.getElementById('ceStartSync'),
            storeId = document.getElementById('ce-store-id');

        ChannelEngine.loader.hide();

        link.onclick = () => {
            ChannelEngine.loader.show();
            ajaxService.get(
                url.value + '?storeId=' + storeId.value,
                function (response) {
                    if (response.success) {
                        let stateUrl = document.getElementById('ce-state-url');
                        window.location.assign(stateUrl.value + '?storeId=' + storeId.value);
                    } else {
                        ChannelEngine.notificationService.removeNotifications();
                        ChannelEngine.notificationService.addNotification(response.message, false);
                    }
                }
            );
        }
    }
);