/**
 * Class View
 * @this {View}
 * 
 */
var View = Backbone.View.extend({
    initialize: function (options) {
        var _this = this;
        _this.options = options || {}; // todo delete
        _this.params = options.params || {};
        _this.$el.data('widget', this);
    },
    /**
     * @todo подписывать виджет на обновление конфигурации и обновлять автоматически
     * @param {Object} viewConfig
     * @param {Object} viewParams
     * @param {boolean|string} loading
     * @param {Object} queryParams
     * @returns {*}
     */
    update: function (viewConfig, viewParams, loading, queryParams) {
        // todo url
        var _this = this;
        _this.$el.trigger('view-updating');
        viewConfig = viewConfig || {};
        viewParams = viewParams || {};
        queryParams = queryParams || {};
        if (loading === undefined) {
            loading = true;
        }
        _.extend(_this.params, viewParams);
        _.extend(_this.params['config'], viewConfig);
        if (_this.params['module'] === undefined || _this.params['clientUpdated'] === false || _this.$el.data('widget') === undefined) {
            return;
        }
        if (loading === true) {
            _this.$el.loading('start');
        } else if (typeof (loading) === 'string') {
            _this.$el.find(loading).loading('start');
        }
        this.params['config']['wrapper'] = false;
        return application.getComponent('widgetLoader')
            .load(_this.params, queryParams)
            .done(function (response) {
                if (!response.html) {
                    throw new Error('В ответе нету контента для вставки клиенту.');
                }
                _this.$el.trigger('view-updated');
                var content = application.getComponent('htmlHelper').movementResources(response.html);
                application.getComponent('htmlHelper').replaceWith(_this, content);
            })
            .fail(function () {
                _this.$el.loading('stop');
            });
    }
});