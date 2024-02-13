var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const ajaxService = ChannelEngine.ajaxService,
            url = document.getElementById('ceOnboardingUrl'),
            link = document.getElementById('ce-configure');

        link.onclick = () => {
            ajaxService.get(url.value, function () {
                window.location.reload();
            });
        }
    }
);