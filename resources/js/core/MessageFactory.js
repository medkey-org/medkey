/**
 * MessageFactory
 * @constructor
 * 
 */
var MessageFactory = function () {
};

MessageFactory.prototype = Object.create(Component.prototype);
MessageFactory.prototype.constructor = MessageFactory;

/**
 * @static
 * @type {number}
 */
MessageFactory.removeDelay = 7000;
/**
 * @static
 * @type {number}
 */
MessageFactory.positionBlock = 60;
/**
 * @static
 * @type {number}
 */
MessageFactory.statusSuccess = 1;
/**
 * @static
 * @type {number}
 */
MessageFactory.statusWarning = 2;
/**
 * @static
 * @type {number}
 */
MessageFactory.statusError = 3;
/**
 * @method
 * @param {string} message
 * @param {Number}|{string} type
 * @param {Number}|{string} removeDelay
 */
MessageFactory.prototype.setFlash = function (message, type, removeDelay) {
	removeDelay = removeDelay ? removeDelay : MessageFactory.removeDelay;
    var color = '';
    switch (type) {
        case MessageFactory.statusSuccess: // todo test parse INT
            color = '#CEF6D8';
        break;
        case MessageFactory.statusWarning:
            color = '#FACC2E';
            break;
        case MessageFactory.statusError:
            color = '#F78181';
        break;
        default:
            color = '#F78181';
            message += ' (Тип ошибки не был определен)';
    }
    var elem = $('<div class="b-dynamic-message" style="top:' // todo in css
        +  MessageFactory.positionBlock +
        'px; max-height: 100px; background-color:'
        + color +
        ';">'
        + message +
        '</div>');
    MessageFactory.positionBlock += 60;
    $('body').append(elem);
    elem.show(300, function () {
        setTimeout(function () {
            elem.hide('slow', function () {
                MessageFactory.positionBlock -= 60;
                elem.remove();
                $('.b-dynamic-message').animate({
                    top: "-=60",
                }, 80);
            });
        }, removeDelay);
    });
};
