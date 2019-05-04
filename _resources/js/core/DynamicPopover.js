/**
 * Class DynamicPopover
 * @deprecated
 */
var DynamicPopover = View.extend({
    initialize: function (options) {
        var _this = this;
        View.prototype.initialize.call(_this, options);
        var selector = options.params['selector'];
        $(selector).data('loader', false);
        $(selector).on('hidden.bs.popover', function () {
            $(selector).popover('destroy');
            $(selector).off('hidden.bs.popover');
            _this.remove();
            $(selector).data('loader', true);
        });
    },
});