if (typeof dvizh == "undefined" || !dvizh) {
    var dvizh = {};
}

dvizh.orders_list = {
    elementsUrl: null,
    init: function() {
        $('.order-index .show-details').on('click', function(e) {
            var self = this;
            var thisTableRow = $(self).closest('tr');

            if(thisTableRow.next('tr').hasClass('order-detail')) {
                thisTableRow.next('tr').remove();
            }
            else {
                var id = thisTableRow.data('key');

                if(id) {
                    $(tr).find('td').css('opacity', '0.3');

                    $.post(dvizh.orders_list.elementsUrl, {ajax: true, orderId: id},
                        function(json) {
                            $(tr).after('<tr class="order-detail"><td colspan="100">'+json.elementsHtml+'</td></tr>');
                            $(tr).find('td').css('opacity', '1');
                        }, "json");
                }

                var tr = $(thisTableRow);
            }
        });
    },
};

dvizh.orders_list.init();
