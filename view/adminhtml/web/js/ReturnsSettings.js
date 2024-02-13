var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let conditionBtn = document.getElementById('ce-default-condition'),
            resolutionBtn = document.getElementById('ce-default-resolution'),
            conditionList = document.getElementById('ce-default-condition-list'),
            resolutionList = document.getElementById('ce-default-resolution-list'),
            returnsButton = document.getElementById('ce-returns'),
            returnsButtonText = document.getElementById('ce-returns-text'),
            returnsList = document.getElementById('ce-returns-list'),
            returnsYes = document.getElementById('ce-returns-item-yes'),
            returnsNo = document.getElementById('ce-returns-item-no'),
            returnsSyncButton = document.getElementById('ce-returns-sync'),
            returnsSyncButtonText = document.getElementById('ce-returns-sync-text'),
            returnsSyncYes = document.getElementById('ce-returns-sync-item-yes'),
            returnsSyncNo = document.getElementById('ce-returns-sync-item-no'),
            returnsSyncList = document.getElementById('ce-returns-sync-list');

        if (!conditionBtn && !resolutionBtn) {
            return;
        }

        addConditionItemListeners();
        addResolutionItemListeners();

        conditionBtn.onclick = () => {
            toggleDropDown(conditionBtn, conditionList);
        }

        conditionBtn.onfocusout = () => {
            conditionBtn.classList.remove('ce-active');
            conditionList.style.display = "none";
        }

        resolutionBtn.onclick = () => {
            toggleDropDown(resolutionBtn, resolutionList);
        }

        resolutionBtn.onfocusout = () => {
            resolutionBtn.classList.remove('ce-active');
            resolutionList.style.display = "none";
        }

        returnsSyncButton.onclick = () => {
            toggleDropDown(returnsSyncButton, returnsSyncList);
        }

        returnsSyncButton.onfocusout = () => {
            returnsSyncButton.classList.remove('ce-active');
            returnsSyncList.style.display = "none";
        }

        returnsSyncYes.onmousedown = () => {
            returnsSyncButton.setAttribute('data-returns-sync-enabled', '1');
            returnsSyncButton.classList.remove('ce-active');
            returnsList.style.display = "none";
            returnsSyncButtonText.innerText = returnsSyncYes.innerText.replace(/\s/g, '');
        }

        returnsSyncNo.onmousedown = () => {
            returnsSyncButton.setAttribute('data-returns-sync-enabled', '0');
            returnsSyncButton.classList.remove('ce-active');
            returnsList.style.display = "none";
            returnsSyncButtonText.innerText = returnsSyncNo.innerText.replace(/\s/g, '');
        }

        function addConditionItemListeners() {
            let conditionItems = document.getElementsByClassName('ce-condition-item'),
                conditionText = document.getElementById('ce-default-condition-text');

            for (let i = 0; i < conditionItems.length; i++) {
                conditionItems[i].onmousedown = () => {
                    conditionBtn.setAttribute('data-default-condition', conditionItems[i].value);
                    conditionBtn.classList.remove('ce-active');
                    conditionList.style.display = "none";
                    conditionText.innerText = conditionItems[i].getElementsByClassName('ce-dropdown-list-item-text')[0].innerText;
                }
            }
        }

        function addResolutionItemListeners() {
            let resolutionItems = document.getElementsByClassName('ce-resolution-item'),
                resolutionText = document.getElementById('ce-default-resolution-text');

            for (let i = 0; i < resolutionItems.length; i++) {
                resolutionItems[i].onmousedown = () => {
                    resolutionBtn.setAttribute('data-default-resolution', resolutionItems[i].value);
                    resolutionBtn.classList.remove('ce-active');
                    resolutionList.style.display = "none";
                    resolutionText.innerText = resolutionItems[i].getElementsByClassName('ce-dropdown-list-item-text')[0].innerText;
                }
            }
        }

        returnsButton.onclick = () => {
            toggleDropDown(returnsButton, returnsList);
        }

        returnsButton.onfocusout = () => {
            returnsButton.classList.remove('ce-active');
            returnsList.style.display = "none";
        }

        returnsYes.onmousedown = () => {
            returnsButton.setAttribute('returns-enabled', '1');
            returnsButton.classList.remove('ce-active');
            returnsList.style.display = "none";
            returnsButtonText.innerText = returnsYes.innerText.replace(/\s/g, '');
            conditionBtn.removeAttribute('disabled');
            resolutionBtn.removeAttribute('disabled');
        }

        returnsNo.onmousedown = () => {
            returnsButton.setAttribute('returns-enabled', '0');
            returnsButton.classList.remove('ce-active');
            returnsList.style.display = "none";
            returnsButtonText.innerText = returnsNo.innerText.replace(/\s/g, '');
            conditionBtn.setAttribute('disabled', 'true');
            resolutionBtn.setAttribute('disabled', 'true');
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