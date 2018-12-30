/**
 * Class FormWidget
 *
 */
var FormWidget = View.extend({
    initialize: function (options) {
        var _this = this;
        if (_this.events === undefined) {
            _this.events = {};
        }
        var events = _.extend(_this.events, {
            'beforeSubmit form': 'beforeSubmit',
            'afterValidate form': 'afterValidate',
            'beforeValidate form': 'beforeValidate'
        });
        _this.delegateEvents(events);
        View.prototype.initialize.call(_this, options);
    },
    beforeValidate: function () {
        var _this = this;
        var $this = _this.$el;
        if (_this.params['animateLoading'] === true) {
            $this.loading('start');
        }
        var submitButton = _this.$el.find('button[type=submit]');
        submitButton.loading('loadingIcon');
    },
    afterValidate: function (e, errors) {
        var _this = this;
        for (var key in errors) {
            if (errors.hasOwnProperty(key) && errors[key].length > 0) {
                var $this = _this.$el;
                $this.loading('stop');
                var submitButton = _this.$el.find('button[type=submit]');
                submitButton.loading('stopIcon');
                return false;
            }
        }
    },
    formData: function () {
        var serialize = this.$el.find('form').serializeArray();
        if (!this.isMultipart()) {
            return serialize;
        }
        var data = new FormData();
        _.forEach(serialize, function (field) {
            data.append(field.name, field.value);
        });
        this.$el.find('input[type="file"][name]').each(function(key, field) {
            data.append($(field).attr('name'), $(field).prop('files')[0]);
        });
        return data;
    },
    requestOptions: function () {
        var data = this.formData();
        if (this.isMultipart()) {
            return {
                converters: {
                    'text json': function (result) {
                        if (result === '') {
                            return {};
                        }
                        return jQuery.parseJSON(result);
                    }
                },
                data: data,
                dataType: 'json',
                type: 'post',
                cache: false,
                contentType: false,
                processData: false
            };
        }
        return {
            converters: {
                'text json': function (result) {
                    if (result === '') {
                        return {};
                    }
                    return jQuery.parseJSON(result);
                }
            },
            data: data,
            dataType: 'json',
            type: 'post',
            cache: false
        };
    },
    beforeSubmit: function () {
        var _this = this;
        var $this = _this.$el;
        var submitButton = _this.$el.find('button[type=submit]');
        var url = _this.params['action'];
        if (_this.params['ajaxSubmit'] === false) {
            var modal = _this.$el.parents('.modal');
            modal.modal('hide');
            return true;
        }
        var afterCloseModal = _this.params['afterCloseModal'];
        var afterRedirect = _this.params['afterRedirect'];
        var redirectUrl = _this.params['redirectUrl'];
        var afterUpdateBlockId = _this.params['afterUpdateBlockId'];
        application.getComponent('request').ajax(url, _this.requestOptions())
            .done(function (data) {
                console.log(data);
            _this.$el.trigger('afterSubmit', data);
            if (afterCloseModal) {
                var modal = _this.$el.parents('.modal');
                modal.modal('hide');
            }
            if (afterRedirect) {
                window.location.href = redirectUrl;
            } else { // todo как-то для карточки нужно красиво сделать
                $this.loading('stop');
                submitButton.loading('stopIcon');
            }
            if (afterUpdateBlockId) {
                application.getWidgetById(afterUpdateBlockId).update();
            }
        }).fail(function (jqXHR, textStatus) {
            console.error('error form ' + textStatus);
            $this.loading('stop');
            submitButton.loading('stopIcon');
        });
        return false;
    },
    isMultipart: function () {
        return $.trim(this.$('form').attr('enctype')).toLowerCase() === 'multipart/form-data';
    }
});