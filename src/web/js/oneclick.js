if (typeof dvizh == "undefined" || !dvizh) {
    var dvizh = {};
}

dvizh.oneclick = {
    init: function() {
        $('.dvizh_order_oneclick_form form').on('submit', this.sendOrder)
    },
    sendOrder: function() {
        var form = $(this);
        $(form).css('opacity', '0.3');
        var data = $(form).serialize();
        data = data+'&ajax=1';

        jQuery.post($(form).attr('action'), data,
            function(json) {
                if(json.result == 'success') {
                    $(form).parents('.modal').modal('hide');
                    $(form).find('input,textarea').val('');
                    document.location = json.redirect;
                }
                else {
                    console.log(json.errors);
                    alert(json.errors);
                }
                
                $(form).css('opacity', '1');

                return true;

            }, "json");
            
        return false;
    }
};

dvizh.oneclick.init();
