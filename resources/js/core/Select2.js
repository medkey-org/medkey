/**
 * Class Select2
 *
 * @constructor
 * @this {Select2}
 * @deprecated Пока что
 * 
 */
var Select2 = View.extend({
    events: {
        'click': 'click',
        'change': 'change'
        // 'click .select2-search__field': 'changeInput',
    },
    initialize: function (options) {
        View.prototype.initialize.call(this, options);
        // $('#member-type').attr('title', '');
        this.change();
    },
    // changeInput: function () {
    // },
    change: function (e) {
        if (!this.params['custom']) {
            return false;
        }
        var target = this.$el;
        var table = target.parents('.form-group').find('table.select2');
        var input = target.parents('.form-group').find('.select2-search__field');
        input.on('keydown', function (e) {
            if (e.keyCode === 8 && $(this).val() == '') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
        });
        table.find('tbody').empty();
        target.find('option:selected').each(function () {
            table.find('tbody').append(
                '<tr><td>' + $(this).text() + '</td><td data-key="' + $(this).attr('value') + '"class="select2-table__remove">' +
                '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' +
                '</td></tr>');
        });
        table.find('tbody tr td.select2-table__remove').on('click', function () {
            var key = $(this).data('key');
            var option = target.find('option[value="' + key + '"]');
            option.prop('selected', false);
            target.trigger('change');
        });
    }
});