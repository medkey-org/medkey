/**
 * Class Request
 *
 * @constructor
 * @this {RegisterModal}
 * 
 */
var RegisterModal = View.extend({
    events: {
        'click #confirm-ok': 'confirm',
        'click #confirm-cancel': 'cancel'
    },
    confirm: function () {
        var _this = this;
        var $this = _this.$el;
        // var elem = this.options;
        this.options['elementTarget'].trigger('click');
        $this.modal('hide');
    },
    cancel: function () {
        var _this = this;
        var $this = _this.$el;
        $this.modal('hide');
        return false;
    },
    initialize: function (options) {
        View.prototype.initialize.call(this, options);
        var _this = this;
        var $this = _this.$el;
        $this.removeClass('b-client-view');
        $this.addClass('b-client-modal');
    },
    setElementTarget: function (element) {
        this.options['elementTarget'] = element;
    }
});