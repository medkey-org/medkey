/**
 * CardView
 * @copyright 2012-2019 Medkey
 */
var CardView = View.extend({
    initialize: function (options) {
        let _this = this;
        if (_this.events === undefined) {
            _this.events = {};
        }
        let events = _.extend(_this.events, {
            'click [data-card-switch]': 'clickSwitch',
            'click .state-transition': 'applyTransition',
            'afterSubmit': 'afterSubmit'
        });
        _this.delegateEvents(events);
        View.prototype.initialize.call(_this, options);
    },
    applyTransition: function (e) {
        let _this = this;
        let target = $(e.currentTarget);
        let workflowModule = target.data('workflow_module');
        let ormModule = target.data('orm_module');
        let workflowClass = target.data('workflow_class');
        let transitionName = target.data('transition_name');
        let ormClass = target.data('orm_class');
        let ormId = target.data('orm_id');
        let url = application.getComponent('request').createUrl('/rest/state-transition/apply');
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
        let _this = this;
        if (_this.params['redirectSubmit'] === true) {
            return;
        }
        let def = View.prototype.update.call(_this, viewConfig, viewParams, loading, queryParams);
        if (_this.params['pushState']) {
            def.done(function () {
                let scenario = '';
                if (queryParams && queryParams['scenario']) {
                    scenario = queryParams['scenario'];
                } else {
                    scenario = _this.params['scenario'];
                }
                let url = Request.setQueryParam(window.location.href, 'scenario', scenario);
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
        let _this = this;
        let btn = $(e.currentTarget);
        let scenario = btn.data('card-switch');
        let afterCloseModal = _this.params['formOptions']['afterCloseModal'];
        if (afterCloseModal === true && _this.$el.parents('.modal').length > 0) {
            let modal = _this.$el.parents('.modal');
            modal.modal('hide');
            return;
        }
        if (_this.params['config']['model'] && !_.isEmpty(_this.params['config']['model']['id'])) {
            _this.update({}, {}, true, {'scenario': scenario});
        } else {
            let referrer = document.referrer;
            window.location.replace(referrer);
        }
    },
    /**
     *
     * @param {jQuery.Event} e
     * @param {Object} model
     */
    afterSubmit: function (e, model) {
        let _this = this;
        let afterUpdateBlockId = _this.params['afterUpdateBlockId'];
        console.log(afterUpdateBlockId);
        console.log(42342);
        _this.$el.find('form').loading('start'); // temp hook
        _this.update({model: model}, {}, false, {'scenario': 'default'});
        if (afterUpdateBlockId) {
            application.getWidgetById(afterUpdateBlockId).update();
        }
    }
});