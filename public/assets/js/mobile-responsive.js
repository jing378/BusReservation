/**
 * Mobile helpers shared by the admin / customer pages.
 *
 * Copies each table's column headings onto its cells (data-label) so mobile.css can show
 * rows as stacked cards on phones. Tables replaced later via AJAX (e.g. the users list)
 * are labelled too.
 */
(function () {
    'use strict';

    function labelTable(table) {
        var headings = Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
            return th.textContent.replace(/\s+/g, ' ').trim();
        });

        if (headings.length === 0) {
            return;
        }

        Array.prototype.forEach.call(table.querySelectorAll('tbody tr'), function (row) {
            var column = 0;

            Array.prototype.forEach.call(row.children, function (cell) {
                if (!cell.hasAttribute('data-label') && headings[column] !== undefined) {
                    cell.setAttribute('data-label', headings[column]);
                }
                column += cell.colSpan || 1;
            });
        });
    }

    function labelAll(root) {
        Array.prototype.forEach.call((root || document).querySelectorAll('table.table'), labelTable);
    }

    function init() {
        labelAll(document);

        if ('MutationObserver' in window) {
            var pending = false;

            new MutationObserver(function () {
                if (pending) {
                    return;
                }
                pending = true;
                requestAnimationFrame(function () {
                    pending = false;
                    labelAll(document);
                });
            }).observe(document.body, { childList: true, subtree: true });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
