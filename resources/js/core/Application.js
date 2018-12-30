/**
 * Class Application
 * @constructor
 * 
 */
var Application = function () {
    this.components = {
        'request' : new Request(),
        'htmlHelper': new HtmlHelper(),
        'widgetLoader': new WidgetLoader(),
        'messageFactory': new MessageFactory()
    };
    this._modules = {

    };
};

Application.prototype = Object.create(Component.prototype);
Application.prototype.constructor = Application;

/**
 *
 * @param id
 */
Application.prototype.getWidgetById = function (id) {
    return $('#' + id).data('widget'); // todo проверку на существов.
};

/**
 *
 * @param componentName
 * @returns {*}
 */
Application.prototype.getComponent = function (componentName) {
    return this.components[componentName];
};

/**
 *
 * @param modules
 */
Application.prototype.setModule = function (modules) {
    for (var key in modules) {
        this._modules[key] = modules[key];
    }
};

/**
 *
 * @param name
 * @returns {*}
 */
Application.prototype.getModule = function (name) {
    if (this._modules[name]) { // todo проверку на Backbone.Events
        return this._modules[name];
    }
    return null;
};