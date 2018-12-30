var Config_WorkflowTransitionCreateForm = FormWidget.extend({
    events: {
        'change #workflowtransition-handler_type': 'changeType',
    },
    changeType: function () {
        var _this = this;
        var $this = _this.$el;
        var module = $this.find('#workflowtransition-workflow_module');
        var handlerType = $this.find('#workflowtransition-handler_type');
        var handlerMethod = $this.find('#workflowtransition-handler_method');
        if (_.isEmpty(handlerType.val())) {
            handlerMethod.val('');
            handlerMethod.prop('disabled', true);
            handlerMethod.find('option').remove();
            console.log('empty handler type val');
            return;
        }
        application.getComponent('request').ajax(
            application.getComponent('request').createUrl('/rest/handler-registry/handler-registry'), {
                'method': 'get',
                'dataType': 'json',
                'data': {
                    module: module.val(),
                    handlerType: handlerType.val()
                }
            }).done(function (data) {
            handlerMethod.find('option').remove();
            _this.iterator(data, handlerMethod);
        });
    },
    iterator: function (data, field) {
        var _this = this;
        field.select2("val", "");
        field.val('');
        field.find('option').remove();
        if (data instanceof Array && data.length) {
            var i = 0;
            data.forEach(function (item) {
                if (item instanceof Array && data.length) {
                    _this.iterator(item, field); // recursive for submodules backend API
                } else {
                    var newOption = item;
                    var newState = new Option(newOption, newOption, true, true);
                    if (i !== 0) {
                        $(newState).prop('selected', false);
                    }
                    field.append(newState);
                }
                i++;
            });
            if (i > 0) {
                field.prop('disabled', false).trigger('change');
            }
            return true;
        } else if (data instanceof Object && !_.isEmpty(data)) {
            var j = 0;
            for (var key in data) {
                if (key instanceof Object && !_.isEmpty(data)) {
                    _this.iterator(data, field); // recursive for submodules backend API
                }
                // var newOption = key;
                var newState = new Option(data[key], key, true, true);
                if (j !== 0) {
                    $(newState).prop('selected', false);
                }
                field.append(newState);
                j++;
            }
            if (j > 0) {
                field.prop('disabled', false).trigger('change');
            }
            return true;
        } else {
            field.val('');
            field.prop('disabled', true);
            return false;
        }
    },
});
var Config_WorkflowTransitionUpdateForm = Config_WorkflowTransitionCreateForm.extend({

});
var Config_WorkflowCreateForm = FormWidget.extend({
    events: {
        'change #workflow-orm_module': 'changeModule',
    },
    changeModule: function (e) {
        var _this = this;
        var $this = _this.$el;
        var target = $(e.currentTarget);
        var workflowOrm = $this.find('#workflow-orm_class');
        if (_.isEmpty(target.val())) {
            workflowOrm.val('');
            workflowOrm.prop('disabled', true);
            workflowOrm.find('option').remove();
            console.log('empty module val');
            return;
        }
        application.getComponent('request').ajax(
            application.getComponent('request').createUrl('/rest/active-record-registry/registry'), {
                'method': 'get',
                'dataType': 'json',
                'data': {module: target.val()}
            }).done(function (data) {
            workflowOrm.find('option').remove();
            _this.iterator(data, workflowOrm);
        });
    },
    iterator: function (data, field) {
        var _this = this;
        field.select2("val", "");
        field.val('');
        field.find('option').remove();
        if (data instanceof Array && data.length) {
            var i = 0;
            data.forEach(function (item) {
                if (item instanceof Array && data.length) {
                    _this.iterator(item, field); // recursive for submodules backend API
                } else {
                    var newOption = item;
                    var newState = new Option(newOption, newOption, true, true);
                    if (i !== 0) {
                        $(newState).prop('selected', false);
                    }
                    field.append(newState);
                }
                i++;
            });
            if (i > 0) {
                field.prop('disabled', false).trigger('change');
            }
            return true;
        } else if (data instanceof Object && !_.isEmpty(data)) {
            var j = 0;
            for (var key in data) {
                if (key instanceof Object && !_.isEmpty(data)) {
                    _this.iterator(data, field); // recursive for submodules backend API
                }
                // var newOption = key;
                var newState = new Option(data[key], key, true, true);
                if (j !== 0) {
                    $(newState).prop('selected', false);
                }
                field.append(newState);
                j++;
            }
            if (j > 0) {
                field.prop('disabled', false).trigger('change');
            }
            return true;
        } else {
            field.val('');
            field.prop('disabled', true);
            return false;
        }
    },
});

var Config_WorkflowUpdateForm = Config_WorkflowCreateForm.extend({});

var Config_WorkflowStatusCreateForm = FormWidget.extend({
    events: {
        'change #workflowstatus-orm_module': 'changeModule',
    },
    changeModule: function (e) {
        var _this = this;
        var $this = _this.$el;
        var target = $(e.currentTarget);
        var workflowOrm = $this.find('#workflowstatus-orm_class');
        if (_.isEmpty(target.val())) {
            workflowOrm.val('');
            workflowOrm.prop('disabled', true);
            workflowOrm.find('option').remove();
            console.log('empty module val');
            return;
        }
        application.getComponent('request').ajax(
            application.getComponent('request').createUrl('/rest/active-record-registry/registry'), {
                'method': 'get',
                'dataType': 'json',
                'data': {module: target.val()}
            }).done(function (data) {
            workflowOrm.find('option').remove();
            _this.iterator(data, workflowOrm);
        });
    },
    iterator: function (data, field) {
        var _this = this;
        field.select2("val", "");
        field.val('');
        field.find('option').remove();
        if (data instanceof Array && data.length) {
            var i = 0;
            data.forEach(function (item) {
                if (item instanceof Array && data.length) {
                    _this.iterator(item, field); // recursive for submodules backend API
                } else {
                    var newOption = item;
                    var newState = new Option(newOption, newOption, true, true);
                    if (i !== 0) {
                        $(newState).prop('selected', false);
                    }
                    field.append(newState);
                }
                i++;
            });
            if (i > 0) {
                field.prop('disabled', false).trigger('change');
            }
            return true;
        } else if (data instanceof Object && !_.isEmpty(data)) {
            var j = 0;
            for (var key in data) {
                if (key instanceof Object && !_.isEmpty(data)) {
                    _this.iterator(data, field); // recursive for submodules backend API
                }
                // var newOption = key;
                var newState = new Option(data[key], key, true, true);
                if (j !== 0) {
                    $(newState).prop('selected', false);
                }
                field.append(newState);
                j++;
            }
            if (j > 0) {
                field.prop('disabled', false).trigger('change');
            }
            return true;
        } else {
            field.val('');
            field.prop('disabled', true);
            return false;
        }
    },
});

var Config_WorkflowStatusUpdateForm = Config_WorkflowStatusCreateForm.extend({});