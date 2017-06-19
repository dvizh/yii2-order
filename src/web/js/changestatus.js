if (typeof dvizh == "undefined" || !dvizh) {
    var dvizh = {};
}

dvizh.changestatus = {
    csrf: null,
    csrf_param: null,
    init: function() {
        dvizh.changestatus.csrf = $('meta[name=csrf-token]').attr("content");
        dvizh.changestatus.csrf_param = $('meta[name=csrf-param]').attr("content");
        $(document).on('change', ".dvizh-change-order-status", this.changeStatus);
    },
    changeStatus: function() {
        var link = $(this);
        $(link).css('opacity', '0.2');
        
        data = {};
        data['status'] = $(this).val();
        data['id'] = $(this).data('id');
        data[dvizh.changestatus.csrf_param] = dvizh.changestatus.csrf;

        $.post($(this).data('link'), data,
            function(json) {
                if(json.result == 'success') {
                    $(link).css('opacity', '1');
                }
                else {
                    console.log(json.error);
                }

            }, "json");
        
        return false;
    },
};

dvizh.changestatus.init();
