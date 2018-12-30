/**
 * Class RelatedWidget
 * @this {RelatedWidget}
 * 
 */
var RelatedWidget = View.extend({
    initialize: function (options) {
        var _this = this;
        if (_this.events === undefined) {
            _this.events = {};
        }
        var events = _.extend(_this.events, {
            'click tbody:not(\'.empty\') tr': 'selectRelated',
            'view-updating .grid-view': 'viewUpdating'
        });
        _this.delegateEvents(events);
        View.prototype.initialize.call(this, options);
    },
    viewUpdating: function () {
        this.$el.find('button[type="submit"]').prop('disabled', true);
    },
    selectRelated: function () {
        if (!this.$el.find('.grid-view').data('dynamicModel')) {
            return false;
        }
        this.$el.find('button[type="submit"]').prop('disabled', false);
        var _this = this;
        var $this = _this.$el;
        $this.find('#relation-model-pk').val(this.$el.find('.grid-view').data('dynamicModel'));
    }
});