window.$ = window.jQuery = require('jquery');
require('select2');

$(() => {
    CKEDITOR.replace('details');
    const categoryIds = $("input:hidden[name='category-ids']").val();

    $('#category_id').select2({placeholder: 'Select an option'})
    .val(categoryIds).trigger('change');
})