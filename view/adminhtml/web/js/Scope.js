var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let scopeBtn = document.getElementById('ce-scope-btn'),
            scopeList = document.getElementById('ce-scope-list'),
            scopeItems = document.getElementsByClassName('ce-scope-item'),
            stateUrl = document.getElementById('ce-state-url');

        scopeBtn.onclick = () => {
            toggleDropDown(scopeBtn, scopeList);
        }

        scopeBtn.onfocusout = () => {
            scopeBtn.classList.remove('ce-active');
            scopeList.style.display = "none";
        }

        for (let i = 0; i < scopeItems.length; i++) {
            scopeItems[i].onmousedown = () => {
                let url = stateUrl.value + '?storeId=' + scopeItems[i].value;
                scopeBtn.classList.remove('ce-active');
                scopeList.style.display = "none";
                scopeList.innerText = scopeItems[i].querySelector('a').innerText.replace(/(\r\n|\n|\r)/gm, "");
                window.location.assign(url)
            }
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