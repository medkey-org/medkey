/**
 * Class HtmlHelper
 *
 * @constructor
 * @this {HtmlHelper}
 * 
 */
var HtmlHelper = function () {
    this.html = '';
    this.scripts = [];
    this.css = [];
};
HtmlHelper.prototype = Object.create(Component.prototype);
HtmlHelper.prototype.constructor = HtmlHelper;

/**
 *
 * @returns {Array}
 */
HtmlHelper.prototype.searchAttachCss = function () { // todo сканирование на <style import>
    var h = $('head');
    var arr = [];
    var $links = h.find('link[rel="stylesheet"]');
    $links.each(function () {
        var l = $(this);
        if (l.attr('href') !== undefined) {
            arr.push(l.attr('href'));
        }
    });

    return arr;
};

HtmlHelper.prototype.clear = function () {
    this.scripts = [];
    this.css = [];
    this.html = '';
};
/**
 *
 * @param {string} html
 * @returns {jQuery|*}
 */
HtmlHelper.prototype.movementResources = function (html) {
    var _this = this;
    _this.clear();
    _this.html = html;
    var i = 0;
    var styleTag = $('<style>');
    var h =  _this.searchAttachCss();
    $(html).each(function () {
        var $this = $(this);
        if ($this.is('script')) {
            _this.scripts[i] = $this;
            i++;
        }
        if ($this.is('link')) {
            var href = $this.attr('href');
            if (h.indexOf(href) === -1) {
                styleTag.append('@import "' + href + '" all;');
            }
        }
    });
    _this.css = styleTag; // todo на пустоту, и не подключать вовсе
    _this.out = this.searchFirstBlock();
    // TODO я бы удалял все <link> в ответе, они всё равно должны быть расположены в <head>
    return _this.out;
}

/**
 * @method
 * @returns {jQuery}
 */
HtmlHelper.prototype.searchFirstBlock = function () {
    var _this = this;
    var firstBlock = null;
    var i = 0;
    $(_this.html).each(function () {
        var $this = $(this);
        if (($this.is('div') || $this.is('table') || $this.is('tr')) && i === 0) {
            firstBlock = $this;
            i++;
        } else if ($this.is('div') && i > 0) { // todo не только div, но и другие теги переносить...
            // console.warn('Единичный wrapper не найден.');
            firstBlock.append($this);
        }
    });
    if (firstBlock instanceof jQuery) {
        firstBlock.append(_this.scripts);
        firstBlock.append(_this.css);

        return firstBlock;
    } else {
        throw new Error('Error parse containers');
    }
};

// todo статическими функциями
/**
 * @method
 * @param {View} view
 * @param {string} content
 */
HtmlHelper.prototype.replaceWith = function (view, content) {
    var _this = this;
    if (!(view instanceof View)) {
        throw new Error('view is undefined');
    }
    var $this = view.$el;
    _this.removeChildren(view);
    _this.detachView(view);
    $this.replaceWith(content);
    view.remove();
};

/**
 *
 * @param view
 */
HtmlHelper.prototype.remove = function (view) {
    var _this = this;
    if (!(view instanceof View)) {
        throw new Error('view is undefined');
    }
    _this.removeChildren(view);
    _this.detachView(view);
    view.remove(); // todo она уже делает функцию выше detaсhView, но на всякий случай
};

/**
 * @method
 * @param {View} view
 */
HtmlHelper.prototype.removeChildren = function (view) {
    var _this = this;
    var $this = view.$el;
    $this.find('.b-client-view')
        .each(function () {
            var widget = $(this).data('widget');
            if (widget === undefined) {
                // todo fix для select2 kartik
                // throw new Error('Widget is not defined');
            } else {
                _this.removeChildren(widget);
                _this.detachView(widget);
                 widget.remove();
            }
        });
};

/**
 * @method
 * @param {View} view
 */
HtmlHelper.prototype.detachView = function (view) {
    view.undelegateEvents();
    view.stopListening();
};