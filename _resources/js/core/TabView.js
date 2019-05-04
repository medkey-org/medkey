var TabView = View.extend({
    initialize: function (options) {
        View.prototype.initialize.call(this, options);

        var _this = this;

        if (_this.events === undefined) {
            _this.events = {};
        }
        var events = _.extend(_this.events, {
                'click [data-tab-id]': 'changeTab',
                'click .tab-update': 'clickUpdate'
            });
        _this.delegateEvents(events);

    },
    changeTab: function (e) {
        e.preventDefault();
        var btn = $(e.currentTarget);
        if (btn.data('tab-view') !=  this.$el.attr('id')) {
            return;
        }

         var _this = this;
         var activeModelId = btn.data('tab-id');
         _this.update({'activeModelId': activeModelId}, {}, true, {});
    },
    clickUpdate: function (e) {
        e.preventDefault();
        var _this = this;
        _this.update({}, {}, true, {});
    }
});
