/**
 * Class SearchView
* 
*/
var SearchWidget = FormWidget.extend({
    initialize: function (options) {
        var _this = this;
        if (_this.events === undefined) {
            _this.events = {};
        }
        var events = _.extend(_this.events, {
            'beforeValidate': 'beforeValidate',
            'click .form-trash': 'formTrash'
        });
        _this.delegateEvents(events);
        FormWidget.prototype.initialize.call(_this, options);
    },
    formTrash: function (e) {
        var _this = this,
            form = _this.$el.find('form');
        e.preventDefault();
        form.find(':input').not(':hidden, :radio, :checkbox, :button, :submit, :reset').val('');
        this.params['config']['model'] = {};
        this.params['config']['formData'] = '';
        _this.update();
    },
    beforeValidate: function () {
        var _this = this;
        var submitButton = _this.$el.find('button[type=submit]');
        submitButton.loading('loadingIcon');
        submitButton.on('click', function () { // todo plugin jquery
            return false;
        });
        var formData = _this.$el.find('form').serialize();
        _.extend(this.params['config'], {'formData': formData});
        this.update();

        return false;
    }
});