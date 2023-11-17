var ChannelEngine = window.ChannelEngine || {};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const url = document.getElementById('ce-transactions-get'),
            table = document.getElementById('ce-table-body'),
            logsFrom = document.getElementById('ce-logs-from'),
            logsTo = document.getElementById('ce-logs-to'),
            logsTotal = document.getElementById('ce-logs-total'),
            viewDetailsTranslation = document.getElementById('ce-view-details-translation'),
            startTranslation = document.getElementById('ce-start-translation'),
            completedTranslation = document.getElementById('ce-completed-translation'),
            pagination = document.getElementsByClassName('ce-pagination-pages')[0],
            nextPage = document.getElementsByClassName('ce-button__next')[0],
            prevPage = document.getElementsByClassName('ce-button__prev')[0],
            pageSize = document.getElementById('ce-page-size'),
            pageSizeList = document.getElementById('ce-page-size-list'),
            detailsUrl = document.getElementById('ce-details-get'),
            storeId = document.getElementById('ce-store-scope'),
            pageOf = document.getElementById('ce-details-from');

        ChannelEngine.loader.show();
        pageSize.onclick = () => {
            toggleDropDown(pageSize, pageSizeList);
        }

        pageSize.onfocusout = () => {
            pageSize.classList.remove('ce-active');
            pageSizeList.style.display = "none";
        }

        getPage(1);

        function getPage(page) {
            let taskType = '',
                status = '',
                current = document.getElementsByClassName('ce-current')[0];

            switch (current.id) {
                case 'ce-product-link':
                    taskType = 'ProductSync';
                    break;
                case 'ce-order-link':
                    taskType = 'OrderSync';
                    break;
                case 'ce-errors-link':
                    status = true;
            }

            ChannelEngine.ajaxService.get(
                url.value + '?page=' + page
                + '&page_size=' + pageSize.getAttribute('data-page-size') + '&task_type=' + taskType
                + '&status=' + status + '&storeId=' + storeId.value,
                function (response) {
                    renderPage(response);
                    ChannelEngine.loader.hide();
                }
            );
        }

        function renderPage(response) {
            removeData();
            logsTotal.innerHTML = response.numberOfLogs;
            logsFrom.innerHTML = response.from;
            logsTo.innerHTML = response.to;

            renderFilters(response);
            renderPagination(response);

            if (response.logs.length === 0) {
                let row = document.createElement('TR'),
                    noResults = document.getElementById('ce-no-results');

                row.innerHTML = noResults.value;
                row.style.display = "block";
                row.style.padding = "0.75rem";
                table.append(row);

                return;
            }

            response.logs.forEach(item => addRow(item));
        }

        function renderPagination(response) {
            nextPage.onclick = function () {
                getPage(response.currentPage + 1);
            }

            prevPage.onclick = function () {
                getPage(response.currentPage - 1);
            }

            prevPage.disabled = response.currentPage === 1;
            nextPage.disabled = response.currentPage === response.numberOfPages || response.numberOfPages === 0;

            renderPaginationButton(response.currentPage, response);

            addCurrentPageInputListener(response);
            addPageSizeListeners();
        }

        function renderPaginationButton(i, response) {
            let pageNumber = document.createElement('input'),
                paginationSpan = document.createElement('SPAN');
            paginationSpan.innerText = pageOf.value + ' ' + response.numberOfPages;
            paginationSpan.classList.add('ce-number-of-pages');
            pageNumber.classList.add('ce-page-number');
            pageNumber.setAttribute('type', 'text');
            pageNumber.setAttribute('value', i);

            pagination.insertBefore(pageNumber, nextPage);
            pagination.insertBefore(paginationSpan, nextPage);
        }

        function addRow(log) {
            let row = document.createElement('TR'),
                taskType = document.createElement('TD'),
                status = document.createElement('TD'),
                statusSpan = document.createElement('SPAN'),
                compact = document.createElement('TD'),
                viewButton = document.createElement('BUTTON'),
                startTime = document.createElement('TD'),
                timeCompleted = document.createElement('TD'),
                viewDetails = document.createElement('TD'),
                button = document.createElement('BUTTON');


            taskType.innerHTML = log.taskType;
            status.classList.add('text-center');
            statusSpan.classList.add('ce-status');

            if (log.status === 'Completed') {
                statusSpan.classList.add('ce-status__success');
            }

            if (['Pending'].includes(log.status)) {
                statusSpan.classList.add('ce-status__info');
            }

            if (['Has errors'].includes(log.status)) {
                statusSpan.classList.add('ce-status__error');
            }

            if (['Partially completed'].includes(log.status)) {
                statusSpan.classList.add('ce-status__warning');
            }

            statusSpan.innerHTML = log.status;
            status.append(statusSpan);

            compact.classList.add('ce-table-compact-view')
            compact.append(getCompactView(log));
            if (log.hasDetails) {
                viewButton.classList.add('ce-open-modal', 'ce-button', 'ce-button__primary');
                viewButton.innerHTML = viewDetailsTranslation.value;
                viewButton.onclick = function () {
                    showDetails(log.id);
                }
                compact.append(viewButton);
            }
            startTime.innerHTML = log.startTime;
            startTime.classList.add('text-center', 'ce-table-full-view');
            timeCompleted.innerHTML = log.completedTime;
            timeCompleted.classList.add('text-center', 'ce-table-full-view');
            row.append(taskType);
            row.append(status);
            row.append(compact);
            row.append(startTime);
            row.append(timeCompleted);
            viewDetails.classList.add('text-center', 'ce-table-full-view');

            if (log.hasDetails) {
                button.innerHTML = viewDetailsTranslation.value;
                button.classList.add('ce-open-modal', 'ce-button', 'ce-button__link');
                button.onclick = function () {
                    showDetails(log.id);
                }
                viewDetails.append(button);
            }
            row.append(viewDetails);

            table.append(row);
        }

        function getCompactView(log) {
            let table = document.createElement('DL'),
                startTime = document.createElement('DT'),
                start = document.createElement('DD'),
                timeCompleted = document.createElement('DT'),
                completed = document.createElement('DD');

            startTime.innerHTML = startTranslation.value;
            start.innerHTML = log.startTime;
            timeCompleted.innerHTML = completedTranslation.value;
            completed.innerHTML = log.completedTime;

            table.append(startTime);
            table.append(start);
            table.append(timeCompleted);
            table.append(completed);

            return table;
        }

        function showDetails(logId) {
            let modalHeader = document.getElementById('ce-modal-header'),
                buttonText = document.getElementById('ce-modal-button-text');

            ChannelEngine.ajaxService.get(
                detailsUrl.value + '?log_id=' + logId + '&storeId=' + storeId.value,
                function (response) {
                    ChannelEngine.modalService.showModal(
                        modalHeader.value,
                        ChannelEngine.details.getContent(response),
                        buttonText.value,
                        closeModal
                    );

                    ChannelEngine.details.addListeners();
                }
            )
        }

        function closeModal() {
            let modal = document.getElementById('ce-modal');

            modal.style.display = "none";
        }

        function removeData() {
            let numberOfChildren = pagination.children.length;

            for (let i = numberOfChildren - 2; i > 0; i--) {
                pagination.removeChild(pagination.children[i]);
            }

            table.innerHTML = '';
        }

        function renderFilters(response) {
            let productLink = document.getElementById('ce-product-link'),
                orderLink = document.getElementById('ce-order-link'),
                errorsLink = document.getElementById('ce-errors-link');

            productLink.onclick = function () {
                addClassToCurrentFilter('ProductSync');
                getPage(1);
            }

            orderLink.onclick = function () {
                addClassToCurrentFilter('OrderSync');
                getPage(1);
            }

            errorsLink.onclick = function () {
                addClassToCurrentFilter('Errors');
                getPage(1);
            }

            addClassToCurrentFilter(response.taskType);
        }

        function addClassToCurrentFilter(current) {
            let productLink = document.getElementById('ce-product-link'),
                orderLink = document.getElementById('ce-order-link'),
                errorsLink = document.getElementById('ce-errors-link');

            switch (current) {
                case 'ProductSync':
                    productLink.classList.add('ce-current');
                    orderLink.classList.remove('ce-current');
                    errorsLink.classList.remove('ce-current');
                    break;
                case 'OrderSync':
                    orderLink.classList.add('ce-current');
                    productLink.classList.remove('ce-current');
                    errorsLink.classList.remove('ce-current');
                    break;
                case 'Errors':
                    errorsLink.classList.add('ce-current');
                    productLink.classList.remove('ce-current');
                    orderLink.classList.remove('ce-current');
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

        function addCurrentPageInputListener(response) {
            let pageInputs = document.getElementsByClassName('ce-page-number');
            pageInputs[0].onchange = function () {
                let value = parseInt(this.value);
                if (value < 1) {
                    value = 1;
                }

                if (value > response.numberOfPages) {
                    value = response.numberOfPages
                }

                getPage(value);
            }
        }

        function addPageSizeListeners() {
            let pageSizeValues = document.getElementById('ce-page-size-list').getElementsByTagName('LI'),
                pageSizeText = document.getElementById('ce-page-size-text'),
                pageNumber = document.getElementsByClassName('ce-page-number')[0];

            for (let i = 0; i < pageSizeValues.length; i++) {
                pageSizeValues[i].onmousedown = () => {
                    pageSize.setAttribute('data-page-size', pageSizeValues[i].value);
                    pageSizeText.innerText = pageSizeValues[i].value;
                    toggleDropDown(pageSize, pageSizeList);
                    getPage(pageNumber.value);
                }
            }
        }
    }
);