var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        let threeLevelSyncButton  = document.getElementById('ce-three-level-sync'),
            threeLevelSyncButtonText = document.getElementById('ce-three-level-sync-text'),
            threeLevelSyncList = document.getElementById('ce-three-level-sync-list'),
            yesThreeLevelSync = document.getElementById('ce-three-level-sync-item-yes'),
            noThreeLevelSync = document.getElementById('ce-three-level-sync-item-no'),
            threeLevelSyncAttributePicker = document.getElementById('three-level-sync-attribute-picker');


        threeLevelSyncButton.onclick = () => {
            toggleDropDown(threeLevelSyncButton, threeLevelSyncList);
        }

        threeLevelSyncButton.onfocusout = () => {
            threeLevelSyncButton.classList.remove('ce-active');
            threeLevelSyncList.style.display = "none";
        }

        yesThreeLevelSync.onmousedown = () => {
            threeLevelSyncButton.setAttribute('three-level-sync-enabled', '1');
            threeLevelSyncButton.classList.remove('ce-active');
            threeLevelSyncList.style.display = "none";
            threeLevelSyncButtonText.innerText = yesThreeLevelSync.innerText.replace(/\s/g, '');
            threeLevelSyncAttributePicker.style.display = '';
        }

        noThreeLevelSync.onmousedown = () => {
            threeLevelSyncButton.setAttribute('three-level-sync-enabled', '0');
            threeLevelSyncButton.classList.remove('ce-active');
            threeLevelSyncList.style.display = "none";
            threeLevelSyncButtonText.innerText = noThreeLevelSync.innerText.replace(/\s/g, '');
            threeLevelSyncAttributePicker.style.display = 'none';
        }



        addListenerToButton('ce-attribute-three-level-sync', 'ce-sync-attribute-list');
        addAttributeItemsListeners(
            'data-three-level-sync-attribute',
            'data-three-level-sync-type',
            'ce-attribute-three-level-sync',
            'ce-sync-attribute-list'
        );

        function addAttributeItemsListeners(id, type, buttonId, listId) {
            let attributeItems = document.querySelectorAll('#' + listId + ' .ce-mapping-item'),
                attributeBtnText = document.querySelector('#' + buttonId + ' #ce-three-level-sync-attribute-text'),
                attributeButton = document.getElementById(buttonId),
                attributesList = document.getElementById(listId);

            for (let i = 0; i < attributeItems.length; i++) {
                attributeItems[i].onmousedown = () => {
                    attributeButton.setAttribute(id, attributeItems[i].getAttribute('value'));
                    if (attributeItems[i].getAttribute('data-type')) {
                        attributeButton.setAttribute(type, attributeItems[i].getAttribute('data-type'));
                    }
                    attributeButton.classList.remove('ce-active');
                    attributesList.style.display = "none";
                    attributeBtnText.innerText = attributeItems[i].getElementsByTagName('span')[0].innerText;
                }
            }
        }

        function addListenerToButton(buttonId, listId) {
            let button = document.getElementById(buttonId),
                list = document.getElementById(listId);

            button.onfocusout = () => {
                button.classList.remove('ce-active');
                list.style.display = "none";
            }
            button.onclick = () => {
                toggleDropDown(button, list)
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