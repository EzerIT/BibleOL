/**
 * Simple table sorting for BibleOL grading tables.
 * Handles both <table> and <div>-based tables.
 * Keeps detail rows associated with their parent rows.
 */

(function($) {
    $.fn.bibleolSorter = function(options) {
        const settings = $.extend({
            rowSelector: 'tr',
            headerSelector: 'th',
            cellSelector: 'td',
            detailRowClass: null, // If set, these rows move with the previous non-detail row
            excludeSelector: '.grading-header', // Header row(s) to exclude from sorting but use for triggers
            oddClass: 'zebra-odd',
            evenClass: 'zebra-even'
        }, options);

        const $container = this;

        this.each(function() {
            const $thisContainer = $(this);
            const $headers = $thisContainer.find(settings.headerSelector);

            $headers.each(function(index) {
                const $header = $(this);
                if ($header.is('[data-no-sort]')) return;

                $header.css('cursor', 'pointer').on('click', function() {
                    sortContainer($thisContainer, index, $header);
                });
            });

            // Initial zebra refresh
            refreshZebra($thisContainer);
        });

        // Expose refreshZebra to the container
        this.data('bibleolSorter', {
            refreshZebra: function() {
                return refreshZebra($container);
            }
        });

        return this;

        function refreshZebra($container) {
            let visibleIndex = 0;
            $container.find(settings.rowSelector).not(settings.excludeSelector).each(function() {
                const $row = $(this);
                // Check if the row is visible. 
                // We check display !== none and visibility !== collapse
                if ($row.css('display') !== 'none' && $row.css('visibility') !== 'collapse') {
                    $row.removeClass(settings.oddClass + ' ' + settings.evenClass);
                    $row.addClass(visibleIndex % 2 === 0 ? settings.oddClass : settings.evenClass);
                    visibleIndex++;
                }
            });
        }

        function sortContainer($container, colIndex, $header) {
            const rows = [];
            let currentParent = null;
            const sortOrder = $header.data('sort-order') === 'asc' ? 'desc' : 'asc';
            
            // Clear sort order from other headers
            $container.find(settings.headerSelector).data('sort-order', null).find('.sort-icon').remove();
            $header.data('sort-order', sortOrder);
            $header.append('<span class="sort-icon">' + (sortOrder === 'asc' ? ' ▲' : ' ▼') + '</span>');

            const allRows = $container.find(settings.rowSelector).not(settings.excludeSelector);
            
            allRows.each(function() {
                const $row = $(this);
                if (settings.detailRowClass && $row.hasClass(settings.detailRowClass)) {
                    if (currentParent) {
                        currentParent.children.push($row);
                    }
                } else {
                    currentParent = {
                        $el: $row,
                        children: [],
                        value: getCellValue($row, colIndex)
                    };
                    rows.push(currentParent);
                }
            });

            rows.sort(function(a, b) {
                let valA = a.value;
                let valB = b.value;

                if (!isNaN(parseFloat(valA)) && isFinite(valA) && !isNaN(parseFloat(valB)) && isFinite(valB)) {
                    valA = parseFloat(valA);
                    valB = parseFloat(valB);
                } else {
                    valA = valA.toString().toLowerCase();
                    valB = valB.toString().toLowerCase();
                }

                if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
                if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
                return 0;
            });

            // Re-append rows in sorted order
            const $tbody = $container.is('table') ? $container.find('tbody').length ? $container.find('tbody') : $container : $container;
            
            // If it's a div-based table, we just append to container. If it's a table, we should be careful about the header.
            // Since we excluded headers from 'allRows', appending will move them to the end of the current parent (e.g. tbody).
            
            rows.forEach(function(rowObj) {
                $tbody.append(rowObj.$el);
                rowObj.children.forEach(function($child) {
                    $tbody.append($child);
                });
            });

            // Refresh zebra after sorting
            refreshZebra($container);
        }

        function getCellValue($row, index) {
            const $cell = $row.find(settings.cellSelector).eq(index);
            // Use data-sort-value if available, otherwise use text
            let val = $cell.data('sort-value');
            if (val === undefined) {
                val = $cell.text().trim();
            }
            return val;
        }
    };
})(jQuery);
