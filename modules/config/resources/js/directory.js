var Config_Module = function () {
    Module.call(this);
};
Config_Module.prototype = Object.create(Module.prototype);
Config_Module.prototype.constructor = Config_Module;
application.setModule({
    'config': new Config_Module()
});

/**
 * Список справочников
 */
var Config_DirectoryGrid = GridView.extend({
    initialize: function (options) {
        GridView.prototype.initialize.call(this, options);
        var _this = this;
        _this.listenTo(application.getModule('config'), 'directory:record-create', function () {
            _this.updateGrid({}, false);
        });
    }
});

/**
 * Форма создания записи справочника
 */
var Config_DirectoryCreateForm = FormWidget.extend({
    events: {
        afterSubmit: 'afterSubmit'
    },
    afterSubmit: function () {
        application.getModule('config').trigger('directory:record-create');
    }
});

/**
 * Форма редактирования записи справочника
 */
var Config_DirectoryUpdateForm = FormWidget.extend({
    events: {
        afterSubmit: 'afterSubmit'
    },
    afterSubmit: function () {
        application.getModule('config').trigger('directory:record-update');
    }
});

/**
 * Список записей справочника
 */
var Config_DirectoryEntityGrid = GridView.extend({
    clickDelete: function (e) {
        var _this = this,
            target = $(e.currentTarget),
            $this = _this.$el;

        return false;
    },
    initialize: function (options) {
        GridView.prototype.initialize.call(this, options);
        var _this = this;

        _this.listenTo(application.getModule('config'), 'directory:record-create', function () {
            _this.updateGrid({}, false);
        });

        _this.listenTo(application.getModule('config'), 'directory:record-update', function () {
            _this.updateGrid({}, false);
        });
    }
});