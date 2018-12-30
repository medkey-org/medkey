/**
 * Class Request
 *
 * @constructor
 * @this {Request}
 *
 */
var Request = function () {
    this.baseUrl = window.serverVars.baseUrl; // todo from global window var
};
Request.prototype = Object.create(Component.prototype);
Request.prototype.constructor = Request;
/**
 *
 * @param {string} url
 * @param {string} params
 * @returns {*}
 */
Request.prototype.createUrl = function (url, params) {
    if (url) {
        url = this.baseUrl + url; // todo check /
        // console.log(url);
        return url;
    }
    return this.baseUrl;
};
/**
 *
 * @param name
 * @param url
 * @returns {*}
 */
Request.getQueryParam = function (name, url) {
    if (!url) {
        url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) {
        return null;
    }
    if (!results[2]) {
        return '';
    }
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};
/**
 *
 * @param {string} url
 * @param {string} param
 * @param {string} value
 * @returns {*}
 */
Request.setQueryParam = function (url, param, value) {
    if (!Request.getQueryParam(param, url)) {
        if (url.indexOf('?') === -1) {
            url += '?' + param + '=' + value;
        } else {
            url += '&' + param + '=' + value;
        }
        return url;
    } else {
        var regex = new RegExp('('+param+'=)[^\&]+');
        return url.replace(regex , '$1' + value);
    };
};
Request.getAllQueryParams = function () {

};
/**
 * @todo refactor ajax_complete()
 * @param url
 * @param setting
 * @returns {*}
 */
Request.prototype.ajax = function (url, setting) {
    setting = setting || {};
    setting.converters = {
        'text json': function (result) {
            if (result === '') {
                return {};
            }
            return jQuery.parseJSON(result);
        }
    };
    return jQuery.ajax(url, setting).always(function (response, textStatus) {
        // todo смотреть код ответа http и в зависимости от кода выкидывать мессагу (MessageFactory)
        if (response.status === 302 || response.status === 301) {
            return;
        }
        if ((textStatus === 'error' || textStatus === 'parsererror')) {
            response = response.responseJSON;
            if ($.isPlainObject(response) && response.message) {
                return application.getComponent('messageFactory').setFlash(response.message, MessageFactory.statusError, MessageFactory.removeDelay);
            } else if (typeof response === 'string' && response.charAt(0) === '{') {
                var obj = $.parseJSON(response);
                if (typeof obj === 'object' && obj.message) {
                    return application.getComponent('messageFactory').setFlash(obj.message, MessageFactory.statusError, MessageFactory.removeDelay);
                }
            } else {
                return application.getComponent('messageFactory').setFlash('Произошла ошибка при обработке запроса. Обратитесь к администратору!', MessageFactory.statusError, window.serverVars.removeDelay);
            }
        }
        if (textStatus === 'timeout') {
            return application.getComponent('messageFactory').setFlash('Превышен timeout запроса. Обратитесь к администратору.', MessageFactory.statusError, window.serverVars.removeDelay);
        }
        if (textStatus === 'success') {
            if ($.isPlainObject(response) && response.message) {
                return application.getComponent('messageFactory').setFlash(response.message, response.type, response.removeDelay);
            } else if (typeof response === 'string' && response.charAt(0) === '{') {
                var obj = $.parseJSON(response);
                if (typeof obj === 'object' && obj.message) {
                    return application.getComponent('messageFactory').setFlash(obj.message, obj.type, obj.removeDelay);
                }
            }
        } else {
            // throw new Error('Error in response');
        }
    });
};