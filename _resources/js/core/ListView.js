/**
 * Class GridView
 * 
 */
var ListView = View.extend({
    initialize: function (options) {
        var _this = this;
        if (_this.events === undefined) {
            _this.events = {};
        }
        var events = _.extend(_this.events, {
            'click .pagination a': 'clickPage',
            'click': 'click'
        });
        _this.delegateEvents(events);
        View.prototype.initialize.call(this, options);
    },
    clickPage: function (e) {
        var _this = this;
        var target = $(e.currentTarget);
        var $this = _this.$el;
        $this.off('click');
        $this.find('a').click(function () {
            return false;
        });
        var page = target.data('page');
        _this.update({}, {}, true, {
            'page': ++page
        });

        return false;
    },
});