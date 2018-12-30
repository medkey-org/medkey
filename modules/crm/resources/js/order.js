var Crm_OrderItemCreateForm = FormWidget.extend({
    events: {
        'change #orderitem-service_id': 'changeService'
    },
    changeService: function (e) {
        var $this = this.$el;
        var serviceId = $(e.currentTarget).val();
        var url = application.getComponent('request').createUrl('/medical/rest/service-price/get-active-price-by-service-id?id=' + serviceId);
        application.getComponent('request').ajax(url)
            .done(function (service) {
                $this.find('#orderitem-currency_sum, #orderitem-final_currency_sum').val(service.cost);
            });
    }
});