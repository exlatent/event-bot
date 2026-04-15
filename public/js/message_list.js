let selection = [];

$('input[name="checkbox-selection-all"]').on('change', function () {
    $('input[name="selection"]').prop('checked', this.checked).trigger('change');
});

$('input[name="selection"]').on('change', function () {
    if (this.checked) {
        if (!selection.includes(this.value)) {
            selection.push(this.value);
        }
    } else {
        selection = selection.filter(item => item != this.value);
    }

    $('input[name="selected"]').val(JSON.stringify(selection));
});

