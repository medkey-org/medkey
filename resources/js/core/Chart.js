var Chart = View.extend({
    initialize: function(options) {
        var _this = this;

        View.prototype.initialize.call(this, options);

        _this.options.params.chartConfig = JSON.parse(_this.options.params.chartConfig);

        _this.callbackRecursiveIteration(_this.options.params.chartConfig, _this.options.params.callbackSuffix);

        _this.render();
    },
     render: function() {
         var _this = this;

         var container = $(_this.$el).find('.chart');

         var width = container.parents(_this.options.params.parentSelector).width() - _this.options.params.widthOffset;
         var height = parseInt(container.parents(_this.options.params.parentSelector).width() * _this.options.params.aspectRatio);

         container.width(((width > 50) ? width : 50) + 'px')
             .height(((height > 50) ? height : 50) + 'px');

          $.plot(container, _this.options.params.chartData, _this.options.params.chartConfig);

          $(window).one('resize', function() {_this.render()});
     },
    callbackRecursiveIteration: function(object, suffix) {
        for (var property in object) {
            if (object.hasOwnProperty(property)) {
                if (typeof object[property] == "object"){
                    this.callbackRecursiveIteration(object[property], suffix);
                } else {
                    if(property.indexOf(suffix, property.length - suffix.length) !== -1) {
                        var name = property.substr(0, property.length - suffix.length);
                        object[name] = new Function("return " + object[property])();
                    }
                }
            }
        }
    }
});
