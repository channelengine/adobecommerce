if (!window.ChannelEngine) {
    window.ChannelEngine = {};
}

(function () {
    function Details() {
        this.logId = 0;

        this.replaceModalContent = function (response) {
            let modal = document.getElementById('ce-modal');
            if(document.getElementById('ce-modal-transactions')) {
                modal = document.getElementById('ce-modal-transactions');
            }

            let main = modal.getElementsByTagName('MAIN')[0];

            main.innerHTML = ChannelEngine.details.getContent(response);
            ChannelEngine.details.addListeners();
        }

        this.getContent = function (response) {
            let content = document.createElement('TABLE'),
                head = document.createElement('THEAD'),
                tr = document.createElement('TR'),
                identifier = document.createElement('TH'),
                message = document.createElement('TH'),
                identifierText = document.getElementById('ce-details-identifier'),
                messageText = document.getElementById('ce-details-message'),
                body = document.createElement('TBODY');

            this.logId = response.logId;

            content.classList.add('ce-table');

            identifier.innerHTML = identifierText.value;
            identifier.scope = 'col';
            message.innerHTML = messageText.value;
            message.scope = 'col';

            tr.append(identifier);
            tr.append(message);
            head.append(tr);
            content.append(head);

            response.details.forEach(item => body.append(ChannelEngine.details.addRow(item)));

            content.append(body);
            content.append(ChannelEngine.details.getFooter(response));

            return content.outerHTML;
        }

        this.getFooter = function (response) {
            let foot = document.createElement('TFOOT'),
                tr = document.createElement('TR'),
                td = document.createElement('TD'),
                pagination = document.createElement('DIV'),
                horizontal = document.createElement('DIV'),
                paginationStatus = document.createElement('DIV'),
                pageSize = document.createElement('DIV'),
                paginationPages = document.createElement('DIV'),
                prevButton = document.createElement('BUTTON'),
                nextButton = document.createElement('BUTTON'),
                displayText = document.getElementById('ce-details-display'),
                toText = document.getElementById('ce-details-to'),
                fromText = document.getElementById('ce-details-from'),
                pageSizeText = document.getElementById('ce-details-page-size'),
                goToPreviousText = document.getElementById('ce-details-go-to-previous'),
                goToNextText = document.getElementById('ce-details-go-to-next');

            td.colSpan = '2';
            pagination.classList.add('ce-table-pagination');
            horizontal.classList.add('ce-horizontal');
            paginationStatus.classList.add('ce-pagination-status');
            paginationStatus.innerHTML = displayText.value +
                ' <strong>' + response.from + '</strong> ' +
                toText.value + ' <strong>' + response.to + '</strong> ' +
                fromText.value + ' <strong>' + response.numberOfDetails + '</strong> ';
            horizontal.append(paginationStatus);

            pageSize.classList.add('ce-page-size');
            let selectHtml = '<label>' + pageSizeText.value +
                ' <select id="ce-select">' + '<option value="10" ';

            if (response.pageSize === "10") {
                selectHtml += 'selected';
            }

            selectHtml += '>10</option>' +
                '<option value="25" ';

            if (response.pageSize === "25") {
                selectHtml += 'selected';
            }

            selectHtml += '>25</option>' +
                '<option value="50" ';

            if (response.pageSize === "50") {
                selectHtml += 'selected';
            }

            selectHtml += '>50</option>' +
                '<option value="100" ';

            if (response.pageSize === "100") {
                selectHtml += 'selected';
            }

            selectHtml += '>100</option>' +
                '</select>' +
                '</label>';

            pageSize.innerHTML = selectHtml;

            horizontal.append(pageSize);

            paginationPages.classList.add('ce-pagination-pages');
            prevButton.id = 'ce-details-prev-button';
            prevButton.classList.add('ce-button', 'ce-button__prev');
            prevButton.title = goToPreviousText.value;
            nextButton.id = 'ce-details-next-button';
            nextButton.classList.add('ce-button', 'ce-button__next');
            nextButton.title = goToNextText.value;

            prevButton.disabled = response.currentPage === 1;
            nextButton.disabled = response.currentPage === response.numberOfPages;

            paginationPages.append(prevButton);
            paginationPages.append(nextButton);

            this.renderPaginationButton(paginationPages, response, nextButton);

            pagination.append(horizontal);
            pagination.append(paginationPages);
            td.append(pagination);
            tr.append(td);
            foot.append(tr);

            return foot;
        }

        this.renderPaginationButton = function (paginationPages, response, nextPage) {
            let pageOf = document.getElementById('ce-details-from'),
                pageNumber = document.createElement('input'),
                paginationSpan = document.createElement('SPAN'),
                numberOfPages = document.createElement('input');
            numberOfPages.setAttribute('type', 'hidden');
            numberOfPages.setAttribute('value', response.numberOfPages)
            numberOfPages.classList.add('ce-modal-number-of-pages-value');
            paginationSpan.innerText = pageOf.value + ' ' + response.numberOfPages;
            paginationSpan.classList.add('ce-number-of-pages');
            pageNumber.setAttribute('type', 'text');
            pageNumber.setAttribute('value', response.currentPage);
            pageNumber.classList.add('ce-page-number');
            pageNumber.classList.add('ce-modal-page-number');

            paginationPages.insertBefore(pageNumber, nextPage);
            paginationPages.insertBefore(paginationSpan, nextPage);
            paginationPages.insertBefore(numberOfPages, paginationSpan);
        }

        this.addRow = function (detail) {
            let tr = document.createElement('TR'),
                identifier = document.createElement('TD'),
                message = document.createElement('TD');

            message.style.wordBreak = "break-all";
            identifier.innerHTML = detail.identifier;
            message.innerHTML = detail.message;

            tr.append(identifier);
            tr.append(message);

            return tr;
        }

        this.getPage = function (url, page, pageSize) {
            let me = this,
                storeScope = document.getElementById('ce-store-scope');
            ChannelEngine.ajaxService.get(
                url.value + '?page=' + page + '&page_size=' + pageSize + '&log_id=' + me.logId + '&storeId=' + storeScope.value,
                this.replaceModalContent
            );
        }

        this.addListeners = function () {
            let modal = document.getElementById('ce-modal');
            if(document.getElementById('ce-modal-transactions')) {
                modal = document.getElementById('ce-modal-transactions');
            }

            let select = modal.getElementsByTagName('SELECT')[0],
                nextButton = document.getElementById('ce-details-next-button'),
                prevButton = document.getElementById('ce-details-prev-button'),
                pageBtns = modal.getElementsByClassName('ce-button__page'),
                me = this,
                url = document.getElementById('ce-details-get');

            this.addCurrentPageInputListener(url, select);

            select.onchange = function () {
                me.getPage(url, 1, this.value);
            }

            nextButton.onclick = function () {
                let page = document.getElementsByClassName('ce-modal-page-number')[0],
                    pageNumber = page ? parseInt(page.value) : 1;
                me.getPage(url, pageNumber + 1, select.value);
            }

            prevButton.onclick = function () {
                let page = document.getElementsByClassName('ce-modal-page-number')[0],
                    pageNumber = page ? parseInt(page.value) : 1;
                me.getPage(url, pageNumber - 1, select.value);
            }

            for (let i = 0; i < pageBtns.length; i++) {
                pageBtns[i].onclick = function () {
                    me.getPage(url, pageBtns[i].innerHTML, select.value);
                }
            }
        }

        this.addCurrentPageInputListener = function (url, select) {
            let me = this,
                pageInputs = document.getElementsByClassName('ce-modal-page-number');
            pageInputs[0].onchange = function () {
                let value = parseInt(this.value),
                    numberOfPages = document.getElementsByClassName('ce-modal-number-of-pages-value')[0],
                    pageNumber = numberOfPages ? parseInt(numberOfPages.value) : 1;
                if (value < 1) {
                    value = 1;
                }

                if (value > pageNumber) {
                    value = pageNumber
                }

                me.getPage(url, value, select.value);
            }
        }
    }

    ChannelEngine.details = new Details();
})();