if (!window.ChannelEngine) {
    window.ChannelEngine = {};
}

(function () {
    function ExtraDataService() {
        let storeId = document.getElementById('ce-store-id');

        this.getExtraDataMappingOptions = function (url, element, selected) {
            const ajaxService = ChannelEngine.ajaxService;
            url += '?storeId=' + storeId;
            let wrapper = document.createElement('DIV'),
                button = document.createElement('button'),
                buttonText = document.createElement('span'),
                dropdownWrapper = document.createElement('DIV'),
                dropdownList = document.createElement('UL');

            wrapper.classList.add('ce-dropdown-wrapper');
            button.classList.add('ce-dropdown', 'ce-extra-data-dropdown');
            buttonText.classList.add('ce-dropdown-text');
            button.appendChild(buttonText);
            button.setAttribute('data-mapping', selected);
            wrapper.appendChild(button);

            dropdownWrapper.classList.add('ce-dropdown-menu', 'ce-extra-data-dropdown-menu');
            dropdownList.classList.add('ce-dropdown-list');
            dropdownList.style.display = "none";

            ajaxService.get(url, function (response) {
                response.product_attributes.forEach(mapping => {
                    let option = document.createElement('LI'),
                        optionText = document.createElement('span');

                    option.classList.add('ce-dropdown-list-item');
                    optionText.classList.add('ce-dropdown-list-item-text');
                    optionText.innerText = mapping.label;
                    option.setAttribute('value', mapping.value);
                    option.appendChild(optionText);
                    dropdownList.appendChild(option);

                    option.onmousedown = function () {
                        button.setAttribute('data-mapping', option.getAttribute('value'));
                        button.classList.remove('ce-active');
                        dropdownList.style.display = "none";
                        buttonText.innerText = option.innerText;
                    }

                    if (selected === mapping.value) {
                        buttonText.innerText = mapping.label;
                    }
                });

                button.onclick = function () {
                    if (!button.classList.contains('ce-active')) {
                        button.classList.add('ce-active');
                        dropdownList.style.display = "block";
                    } else {
                        button.classList.remove('ce-active');
                        dropdownList.style.display = "none";
                    }
                };
            });

            dropdownWrapper.appendChild(dropdownList);
            wrapper.appendChild(dropdownWrapper);
            element.insertBefore(wrapper, element.firstChild);
        };

        this.makeExtraDataForm = function (selected) {
            const newAttribute = document.getElementById('ceExtraDataForm'),
                clone = newAttribute.cloneNode(true),
                previous = document.querySelectorAll('.ce-last').item(0),
                attribute = previous.getAttribute('class'),
                attributeUrl = document.getElementById('ceProductAttributes');

            clone.removeAttribute('style');
            clone.removeAttribute('id');
            clone.setAttribute('class', attribute);

            if (previous.id === 'ceExtraDataForm') {
                previous.before(clone);
            } else {
                previous.after(clone);
            }

            previous.setAttribute('class', 'ce-input-extra-data-mapping');
            const addNewAttribute = document.getElementById('ceAddNewAttribute');
            let removeAttributeList = document.querySelectorAll('.ce-button-remove-mapping');
            removeAttributeList.forEach(removeAttribute => {
                removeAttribute.addEventListener('click', function () {
                    if(addNewAttribute.getAttribute('disabled')) {
                        return;
                    }

                    if (removeAttribute.parentNode.parentElement.getAttribute('class').includes('ce-last')) {
                        let baseDiv = document.getElementById('ceExtraDataForm');
                        if (baseDiv.previousElementSibling.previousElementSibling.getAttribute('class') !== 'ce-extra-data-heading') {
                            baseDiv.previousElementSibling.previousElementSibling.setAttribute(
                                'class',
                                baseDiv.previousElementSibling.previousElementSibling.getAttribute('class') + ' ce-last');
                        } else {
                            baseDiv.setAttribute('class', baseDiv.getAttribute('class') + ' ce-last');
                        }
                    }
                    removeAttribute.parentNode.parentElement.remove();
                });
            });

            ChannelEngine.ExtraDataService.getExtraDataMappingOptions(
                attributeUrl.value,
                clone,
                selected
            );

            return clone;
        };

        this.getExtraDataMapping = function (url) {
            const ajaxService = ChannelEngine.ajaxService;
            url += '?storeId=' + storeId;

            ajaxService.get(url, function (response) {
                Object.entries(response.extra_data_mapping).forEach(entry => {
                    const [key, value] = entry;
                    let element = this.makeExtraDataForm(value);
                    element.children[0].children[1].value = key;
                });
            });
        }

    }

    ChannelEngine.ExtraDataService = new ExtraDataService();
})();