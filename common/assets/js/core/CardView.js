/**
 * CardView
 * @copyright 2012-2019 Medkey
 */
var CardView = View.extend({
    initialize: function (options) {
        var _this = this;
        if (_this.events === undefined) {
            _this.events = {};
        }
        var events = _.extend(_this.events, {
            'click [data-card-switch]': 'clickSwitch',
            'click .state-transition': 'applyTransition',
            'afterSubmit': 'afterSubmit'
        });
        _this.delegateEvents(events);
        View.prototype.initialize.call(_this, options);
    },
    applyTransition: function (e) {
        var _this = this;
        var target = $(e.currentTarget);
        var workflowModule = target.data('workflow_module');
        var ormModule = target.data('orm_module');
        var workflowClass = target.data('workflow_class');
        var transitionName = target.data('transition_name');
        var ormClass = target.data('orm_class');
        var ormId = target.data('orm_id');
        var url = application.getComponent('request').createUrl('/rest/state-transition/apply');
        target.loading('loadingIcon');
        application.getComponent('request').ajax(url, {
            method: 'post',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                workflowModule: workflowModule,
                ormModule: ormModule,
                workflowClass: workflowClass,
                transitionName: transitionName,
                ormClass: ormClass,
                ormId: ormId
            })
        }).done(function () {
            target.loading('stopIcon');
            _this.update();
        }).always(function () {
            target.loading('stopIcon');
        });
    },
    /**
     * @param {Object} viewConfig
     * @param {Object} viewParams
     * @param {boolean|string} loading
     * @param {Object} queryParams
     * @returns {*}
     */
    update: function (viewConfig, viewParams, loading, queryParams) {
        var _this = this;
        if (_this.params['redirectSubmit'] === true) {
            return;
        }
        var def = View.prototype.update.call(_this, viewConfig, viewParams, loading, queryParams);
        if (_this.params['pushState']) {
            def.done(function () {
                var scenario = '';
                if (queryParams && queryParams['scenario']) {
                    scenario = queryParams['scenario'];
                } else {
                    scenario = _this.params['scenario'];
                }
                var url = Request.setQueryParam(window.location.href, 'scenario', scenario);
                if (_this.params['config']
                    && _this.params['config']['model']
                    && _this.params['config']['model']['id']
                ) {
                    url = Request.setQueryParam(url, 'id', _this.params['config']['model']['id']);
                }
                history.pushState(null, '', url);
            });
        }
    },
    /**
     * @param e
     */
    clickSwitch: function (e) {
        e.preventDefault();
        var _this = this;
        var btn = $(e.currentTarget);
        var scenario = btn.data('card-switch');
        var afterCloseModal = _this.params['formOptions']['afterCloseModal'];
        if (afterCloseModal === true && _this.$el.parents('.modal').length > 0) {
            var modal = _this.$el.parents('.modal');
            modal.modal('hide');
            return;
        }
        if (_this.params['config']['model'] && !_.isEmpty(_this.params['config']['model']['id'])) {
            _this.update({}, {}, true, {'scenario': scenario});
        } else {
            var referrer = document.referrer;
            window.location.replace(referrer);
        }
    },
    /**
     *
     * @param {jQuery.Event} e
     * @param {Object} model
     */
    afterSubmit: function (e, model) {
        var _this = this;
        var afterUpdateBlockId = _this.params['afterUpdateBlockId'];
        _this.$el.find('form').loading('start'); // temp hook
        _this.update({model: model}, {}, false, {'scenario': 'default'});
        if (afterUpdateBlockId) {
            application.getWidgetById(afterUpdateBlockId).update();
        }
    }
});