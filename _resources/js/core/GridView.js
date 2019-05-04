/**
 * Class GridView
 * 
 *
 */
var GridView = View.extend({
    initialize: function (options) {
        var _this = this;
        if (_this.events === undefined) {
            _this.events = {};
        }
        var events = _.extend(_this.events, {
            'click .pagination a': 'clickPage',
            'click .grid-update': 'clickUpdate',
            'click th a': 'clickSort',
            'click tbody:not(\'.empty\') tr': 'clickRow',
            'change .filters input, select': 'changeInput',
            'keyup .filters input': 'enterForm'
        });
        _this.delegateEvents(events);
        View.prototype.initialize.call(this, options);
    },
    sendForm: function () {
        var _this = this;
        var $this = _this.$el;
        var data = {};
        $this.find('table .filters input, select').each(function () {
            var name = $(this).attr('name');
            data[name] = $(this).val();
        });
        data = $.param(data);
        _this.update({formData: data}, {}, true);
    },
    enterForm: function (e) {
        var _this = this;
        if (e.keyCode === 13) {
            _this.sendForm();
        }
        return false;
    },
    changeInput: function () {
        var _this = this;
        _this.sendForm();
    },
    clickRow: function (e) {
        var _this = this,
            target = $(e.currentTarget),
            dynamicModel = null;
        if (!_this.params['clickable']) {
            return;
        }
        _this.$el.find('table tbody tr').removeClass('active');
        target.addClass('active'); // todo toggle
        if (target.data('key') === undefined || target.data('key') === '') { // todo check int
            return;
        }
        dynamicModel = target.data('key');
        _this.$el.data('dynamicModel', dynamicModel);
        _this.$el.find('table caption button, table caption a').each(function () { // todo delete refresh from dynamic_model
            var el = $(this);
            var primaryAttribute = 'id';
            if(el.data('is_dynamic_model')) {
                el.removeAttr('disabled').removeClass('disabled');
                el.data('dynamicModel', dynamicModel);
                if (el.is('a')) {
                    if (!el.data('url')) {
                        el.data('url', el.attr('href'));
                    }
                    if (el.data('primary_attribute')) {
                        primaryAttribute = el.data('primary_attribute');
                    }
                    el.attr('href', el.data('url') + (el.data('url').match(/\?./) ? '&' + primaryAttribute + '=' : '?' + primaryAttribute + '=') + dynamicModel); // todo Add correct URL building
                }
            }
        });
    },
    clickPage: function (e) { // todo при быстром перещёлкивании отображается один грид, возможно делать минимальную задержку
        e.preventDefault();
        e.stopImmediatePropagation();
        var _this = this;
        var target = $(e.currentTarget);
        var page = target.data('page');
        _this.update({}, {}, 'table', {
            'page': ++page
        });

        return false;
    },
    clickSort: function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var _this = this;
        var target = $(e.currentTarget);
        var sort = target.data('sort');
        _this.update({}, {}, 'table', {
            'sort': sort
        });

        return false;
    },
    clickUpdate: function (e) {
        var _this = this;
        var $this = _this.$el;
        $this.off('click');
        $(e.currentTarget).loading('rotateIcon');
        _this.update({}, {}, 'table', {});
    },
    update: function (viewConfig, viewParams, loading, queryParams) {
        var _this = this;
        queryParams = queryParams || {};
        if (_this.params['page'] !== undefined && queryParams['page'] === undefined) {
            queryParams['page'] = _this.params['page'];
            delete _this.params['page'];
        }
        if (this.params['sort'] !== undefined && queryParams['sort'] === undefined) {
            queryParams['sort'] = _this.params['sort'];
            delete _this.params['sort'];
        }
        return View.prototype.update.call(_this, viewConfig, viewParams, loading, queryParams);
    },
    /**
     * @param queryParams
     * @param loading
     */
    updateGrid: function (queryParams, loading) {
        var _this = this;
        _this.$el.find('table').loading('start');
        _this.update({}, {}, loading, queryParams);
    }
});