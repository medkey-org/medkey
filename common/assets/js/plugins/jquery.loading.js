(function ($) {
    var methods = {
        start: function () {
            // TODO смотреть на всякий случай z-index блока текущего и повышать при вставке ++
            var _this = this;
            var $this = $(_this);
            $this.css('opacity', '0.5');
            if ($this.height() < 30) {
                $this.css('min-height', 30);
            }
            var loadingGif = window.serverVars.baseUrl + '/gif/' + 'spin.gif';
            var middleHeight = $this.outerHeight(false) / 2 - 30;
            var middleWidth = $this.outerWidth(false) / 2 - 30;
            var options = {
                'position': 'absolute',
                'display': 'block',
                'opacity': '1',
                'top': middleHeight,
                'left': middleWidth,
                'width': 60,
                'height': 60
            };
            if ($this.css('z-index') === 'auto' && $this.css('z-index') !== 'inherit') {
                options['z-index'] = 2;
            } else {
                options['z-index'] = (+$this.css('z-index'));
                +options['z-index']++;
            }
            var img = $('<img>')
                .css(options)
                .attr('src', loadingGif)
                .addClass('loading-img');
            $this.append(img);
            $this.css('pointer-events', 'none');
        },
        stop: function () {
            var _this = this;
            var $this = $(_this);
            $this.find('.loading-img').remove();
            $this.css('opacity', 1);
            $this.css('pointer-events', 'auto');
        },
        rotateIcon: function () {
            var _this = this;
            var $this = $(_this);
            $this.prop('disabled', true);
            var elem = $this.find('.glyphicon');
            if (elem.is('.glyphicon')) {
                elem.css('animation', 'shadow  0.7s infinite ease-in-out');
            }
        },
        loadingIcon: function () {
            var _this = this;
            var $this = $(_this);
            $this.prop('disabled', true);
            var elem = $this.find('.glyphicon');
            if (elem.is('.glyphicon')) {
                elem.data('pre-loading-icon', elem.attr('class'));
                elem.attr('class', 'glyphicon glyphicon-cog');
                elem.css('animation', 'shadow  0.7s infinite ease-in-out');
            }
        },
        stopIcon: function () {
            var $this = $(this),
                elem = $this.find('.glyphicon');

            $this.prop('disabled', false);
            elem.css('animation', 'none');

            if (!elem.length) {
                return;
            }
            elem.attr('class', elem.data('pre-loading-icon'));
        }
    };
    $.fn.loading = function (method) {
        if (methods[method] !== undefined) {
            return methods[method].apply(this);
        }
    };
})(jQuery);