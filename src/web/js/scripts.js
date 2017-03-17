if (typeof dvizh == "undefined" || !dvizh) {
    var dvizh = {};
}

dvizh.order = {
    outcomingAction: null,
    init: function() {
        $(document).on('change', ".order-widget-shipping-type select", this.updateShippingType);
        
        $('.outcomingWidget button').on('click', this.outcoming);
        
        $('.outcomingWidget input[type=text]').on('click', function() {
            $(this).select();
        });
        
        $('.outcomingWidget input[type=text]').on('keyup', function(e) {
            if(e.which == 13) {
                $('.outcomingWidget button').click();
            }
        });

    },
    outcoming: function() {
        var data = {};
        
        var widget = $(this).parents('.outcomingWidget');
        var input = $(widget).find('input[name=count]');
        
        data.stock_id = $(input).data('stock-id');

        if(!data.stock_id) {
            return false;
        }
        
        data.order_id = $(input).data('order-id');
        data.product_id = $(input).data('product-id');

        data.count = $(input).val();

        if(parseInt(data.count) <= 0) {
            return false;
        }
        
        if($(widget).hasClass('write-offed')) {
            if(!confirm('Списать еще раз?')) {
                return false;
            }
        }
        
        $(widget).css('opacity', '0.3');
        
        $.post(dvizh.order.outcomingAction, data, function(json) {
            $(widget).css('opacity', '1');
            
            if(json.result == 'success') {
                $(widget).find('.amount').html(json.amount);
                $(widget).css({'text-decoration': 'line-through'});
            }
        }, 'json');
    },
    updateCartUrl: '',
    updateShippingType: function() {
        jQuery.post(dvizh.order.updateShippingType, {shipping_type_id: $(this).val()},
            function(json) {
                $('.dvizh-cart-price>span').html(json.total);
            }, "json");

        return true;
    },
};

dvizh.order.init();
