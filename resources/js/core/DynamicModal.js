/**
 * Class
 * 
 */
var DynamicModal = View.extend({
    events: {
        'hidden.bs.modal': 'hidden'
    },
    initialize: function (options) {
        View.prototype.initialize.call(this, options);
        var _this = this;
        _this.$el.removeClass('b-client-view'); // todo не помню зачем удалял...
        _this.$el.addClass('b-client-modal');
    },
    hidden: function () {
        var _this = this;
        application.getComponent('htmlHelper').remove(_this);
    }
});