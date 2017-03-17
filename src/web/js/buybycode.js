$(document).on('keypress', '.buy-by-code-input', function(e) {
    if(e.which == 13) {
        $('.buy-by-code button').click();
    }
});

$(document).on('click', '.buy-by-code-input', function() {
    $(this).select();
});

$(document).on('click', '.buy-by-code button', function() {
    var input = this;
    $('.buy-by-code-input').css('opacity', '0.5');
    $('.buy-by-code-input').siblings('.error-block').hide();
    jQuery.post($(this).data('href'), {code: $('.buy-by-code-input').val()},
        function(json) {
            $('.buy-by-code-input').css('opacity', '1');
            $('.buy-by-code-input').click();
            if(json.status == 'success') {
                dvizh.createorder.updateCart();
            }
            else {
                $('.buy-by-code-input').siblings('.error-block').show().html(json.message);
            }
        }, 'json');
});
