console.log('load');

var $TABLE = $('[data-editable-table]');
var $BTN = $('[data-export-button]');
var $EXPORT = $('[data-export]');

$('[data-table-add]').click(function () {
    var $clone = $TABLE.find('tr.hide').clone(true).removeClass('hide');
    $TABLE.find('table').append($clone);
});

$('[data-table-remove]').click(function () {
    $(this).parents('tr').detach();
});

$('[data-table-up]').click(function () {
    var $row = $(this).parents('tr');
    if ($row.index() === 1) return; // Don't go above the header
    $row.prev().before($row.get(0));
});

$('[data-table-down]').click(function () {
    var $row = $(this).parents('tr');
    $row.next().after($row.get(0));
});

// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

$BTN.click(function () {
    var $rows = $TABLE.find('tr:not(:hidden)');
    var headers = [];
    var data = [];

    // Get the headers (add special header logic here)
    $($rows.shift()).find('th:not(:empty)').each(function () {
        headers.push($(this).text().toLowerCase());
    });

    // Turn all existing rows into a loopable array
    $rows.each(function () {
        var $td = $(this).find('td');
        var h = {};

        // Use the headers from earlier to name our hash keys
        headers.forEach(function (header, i) {
            h[header] = $td.eq(i).text();
        });

        data.push(h);
    });

    // Output the result
    $EXPORT.val(JSON.stringify(data));
});
