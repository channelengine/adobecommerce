var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const state = document.getElementById('ce-status');

        ChannelEngine.loader.show();
        showPanel(state.value);
        ChannelEngine.loader.hide();

        function hidePanels() {
            document.getElementById('sync-in-progress').style.display = 'none';
            document.getElementById('sync-completed').style.display = 'none';
            document.getElementById('notifications').style.display = 'none';
            document.getElementById('disabled-integration').style.display = 'none';
        }

        function showPanel(id) {
            hidePanels();

            if (id === 'order-sync-in-progress') {
                document.getElementById('ce-product-sync-in-progress').style.display = "none";
                document.getElementById('sync-in-progress').style.display = '';

                return;
            }

            if (id === 'product-sync-in-progress') {
                document.getElementById('ce-order-sync-in-progress').style.display = "none";
                document.getElementById('sync-in-progress').style.display = '';

                return;
            }

            document.getElementById(id).style.display = '';
        }
    }
);