var Security_Module = function () {
    Module.call(this);
};
Security_Module.prototype = Object.create(Module.prototype);
Security_Module.prototype.constructor = Security_Module;
application.setModule({
    'security': new Security_Module()
});
var Security_AclCreateForm = FormWidget.extend({
    events: {
        'change #acl-module': 'changeModule',
        'change #acl-entity_type': 'changeEntityType'
    },
    changeEntityType: function (e) {
        var _this = this;
        var $this = _this.$el;
        var target = $(e.currentTarget);
        var aclType = $this.find('#acl-type');
        var module = $this.find('#acl-module');
        // var entityType = target.val();
        var privilege = $this.find('#acl-action');
        if (_.isEmpty(target.val())) {
            privilege.val('');
            privilege.prop('disabled', true);
            privilege.find('option').remove();
            return;
        }
        application.getComponent('request').ajax(
            application.getComponent('request').createUrl('/rest/acl-resource-registry/privileges'), {
                'method': 'get',
                'dataType': 'json',
                'data': {module: module.val(), entityType: target.val(), aclType: aclType.val()}
            }).done(function (data) {
                _this.iterator(data, privilege);
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
    changeModule: function (e) {
        var _this = this;
        var $this = _this.$el;
        var target = $(e.currentTarget);
        // var attr = target;
        var aclType = $this.find('#acl-type');
        var entityType = $this.find('#acl-entity_type');
        var privilege = $this.find('#acl-action');
        if (_.isEmpty(target.val())) {
            entityType.val('');
            entityType.prop('disabled', true);
            entityType.find('option').remove();
            privilege.val('');
            privilege.prop('disabled', true);
            privilege.find('option').remove();
            return;
        }
        application.getComponent('request').ajax(
            application.getComponent('request').createUrl('/rest/acl-resource-registry/registry'), {
                'method': 'get',
                'dataType': 'json',
                'data': {module: target.val(), aclType: aclType.val()}
            }).done(function (data) {
                entityType.find('option').remove();
                _this.iterator(data, entityType);
            });
    }
});
var Security_AclUpdateForm = Security_AclCreateForm.extend({

});