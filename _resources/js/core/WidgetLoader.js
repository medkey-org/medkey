/**
 * Class WidgetLoader
 * @constructor
 * 
 */
var WidgetLoader = function () {

};
WidgetLoader.prototype = Object.create(Component.prototype);
WidgetLoader.prototype.constructor = WidgetLoader;
/**
 *
 * @param viewParams
 * @param queryParams
 * @returns {*}
 */
WidgetLoader.prototype.load = function (viewParams, queryParams) {
    var urlParams = $.param(queryParams || {});
    // todo проверку во viewParams
    var url = application.getComponent('request').createUrl(window.serverVars.widgetLoader);
    if (urlParams !== undefined && urlParams !== '') {
        url = url + '?' + urlParams;
    }
    return application.getComponent('request').ajax(url, {
        'method': 'post',
        'contentType': 'application/json',
        'dataType': 'json',
        'data': JSON.stringify(viewParams)
    });
};