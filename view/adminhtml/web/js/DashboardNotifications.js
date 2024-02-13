var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let url = document.getElementById('ce-notifications-url'),
            notificationsItems = document.getElementsByClassName('ce-notifications__items')[0],
            loadMore = document.getElementsByClassName('ce-notifications__load-more')[0],
            loadMoreButton = document.getElementById('ce-load-more-btn'),
            notificationsOffset = document.getElementById('ce-notifications-offset'),
            storeScope = document.getElementById('ce-store-scope');

        getNotifications(0, 15);

        function getNotifications(offset, limit) {
            ChannelEngine.loader.show();
            ChannelEngine.ajaxService.get(
                url.value + '?offset=' + offset + '&limit=' + limit + '&storeId=' + storeScope.value,
                function (response) {
                    if (response.notifications.length === 0 && document.getElementById('notifications').style.display === '') {
                        document.getElementById('notifications').style.display = 'none';
                        document.getElementById('sync-completed').style.display = '';
                    }

                    response.notifications.forEach(item => addNotification(item));
                    notificationsOffset.value = response.numberOfNotifications;

                    if (response.disableButton) {
                        loadMoreButton.disabled = true;
                    } else {
                        loadMoreButton.disabled = false;
                        loadMoreButton.onclick = function () {
                            getNotifications(notificationsOffset.value, 15);
                        }
                    }

                    ChannelEngine.loader.hide();
                }
            );
        }

        function addNotification(item) {
            let notificationItem = document.createElement('DIV'),
                notificationLabel = document.createElement('DIV'),
                showDetails = document.createElement('BUTTON'),
                showDetailsText = document.getElementById('ce-show-details-text');

            notificationItem.classList.add('ce-notifications__item');
            notificationLabel.innerHTML = item.date + ' - ' + item.context + ' - ' + item.message;
            notificationItem.append(notificationLabel);
            showDetails.innerHTML = showDetailsText.value;
            showDetails.classList.add('ce-open-modal', 'ce-button', 'ce-button__link');
            showDetails.onclick = function () {
                let detailsUrl = document.getElementById('ce-details-url'),
                    header = document.getElementById('ce-details-header'),
                    buttonText = document.getElementById('ce-modal-button-text');

                ChannelEngine.ajaxService.get(
                    detailsUrl.value + '?logId=' + item.logId + '&notificationId=' + item.notificationId  + '&storeId=' + storeScope.value,
                    function (response) {
                        ChannelEngine.modalService.showModal(
                            header.value,
                            ChannelEngine.details.getContent(response),
                            buttonText.value,
                            closeModal,
                            true
                        );

                        let closeButton = document.getElementById('ce-modal-transactions').getElementsByClassName('ce-close-modal')[0];
                        closeButton.onclick = function () {
                            closeModal();
                        };

                        ChannelEngine.details.addListeners();
                    }
                );
            };

            notificationItem.append(showDetails);

            notificationsItems.insertBefore(notificationItem, loadMore);
        }

        function closeModal() {
            let modal = document.getElementById('ce-modal-transactions'),
                notifications = document.getElementsByClassName('ce-notifications__item');

            modal.style.display = "none";

            while (notifications[0]) {
                notifications[0].parentElement.removeChild(notifications[0]);
            }

            getNotifications(0, notificationsOffset.value)
        }
    }
);
