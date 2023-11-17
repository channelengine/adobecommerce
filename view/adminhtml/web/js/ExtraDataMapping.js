var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const addNewAttribute = document.getElementById('ceAddNewAttribute');
        let hasEmpty = false,
            extraDataUrl = document.getElementById('ceProductExtraData');

        addNewAttribute.onclick = () => {
            if(addNewAttribute.getAttribute('disabled')) {
                return;
            }

            const extraDataMapping = document.querySelectorAll('.ce-input-extra-data-mapping');
            let keys = [];
            extraDataMapping.forEach(mapping => {
                if (mapping.id === 'ceExtraDataForm') {
                    return;
                }
                let elements = mapping.firstElementChild.getElementsByClassName('ce-extra-data-dropdown');
                let key = elements.item(0).getAttribute('data-mapping');
                hasEmpty = key.match(/^ *$/) !== null
                keys.push(key);
            });
            let isValid = !hasEmpty && !keys.some(x => keys.indexOf(x) !== keys.lastIndexOf(x)) //without duplicates
            if (isValid) {
                ChannelEngine.ExtraDataService.makeExtraDataForm('');
            }
        }

        ChannelEngine.ExtraDataService.getExtraDataMapping(extraDataUrl.value);
    }
);