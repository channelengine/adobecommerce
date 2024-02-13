if (!window.ChannelEngine) {
    window.ChannelEngine = {};
}

(function () {
    function Loader() {
        this.show = function () {
            let loader = document.getElementById('ce-loader'),
                page = document.getElementsByClassName('channel-engine')[0];

            loader.style.display = 'block';
            page.style.display = 'none';
        }

        this.hide = function () {
            let loader = document.getElementById('ce-loader'),
                page = document.getElementsByClassName('channel-engine')[0];

            loader.style.display = 'none';
            page.style.display = 'block';
        }
    }

    ChannelEngine.loader = new Loader();
})();