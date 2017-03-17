if (typeof dvizh == "undefined" || !dvizh) {
    var dvizh = {};
}

dvizh.createorder = {
    init: function() {
        $(document).on('change', ".order-create-container form input[name='Order[user_id]']", this.findUser);
        $(document).on('keypress', ".order-create-container form input[name='Order[user_id]']", function(e) {
            if(e.which == 13) {
                dvizh.createorder.chooseUser();
                return false;
            }
        });
        
        $(document).on('keypress', function(event) {
            if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
                $(".order-create-container form").submit();
            }
        });
        
		$(document).on("promocodeEnter", function(e, code) {
			if($(".order-create-container form input[name='Order[user_id]']").val() == '') {
				dvizh.createorder.chooseUser(code);
			}
		});
		
        //$(document).on('click', ".render-cart", this.updateCart);
    },
    chooseUser: function(id) {
		if(id) {
			$(".order-create-container form input[name='Order[user_id]']").val(id);
		} else {
			id = $(".order-create-container form input[name='Order[user_id]']").val();
		}
        $(".order-create-container form input[name='Order[user_id]']").change();
        $(document).trigger("chooseUserToOrder", id);
        $('#usersModal').modal('hide');
    },
    updateCartUrl: '',
    updateCart: function() {
        $.post(dvizh.createorder.updateCartUrl, {},
            function(json) {
                $('.dvizh-cart-block').replaceWith(json.cart);
                $('.total').html(json.total);
                $('.dvizh-cart-count').html(json.count);
            }, "json");

        return true;
    },
    findUser: function() {
        var input = $(this);
        userId = $(this).val();

        if(userId != '') {
            $(input).css('opacity', '0.2');
            $.post($(this).data('info-service'), {userId: userId},
                function(json) {
                    $(input).css('opacity', '1');
                    if(json.status == 'success') {
                        $(".order-create-container form input[name='Order[user_id]']").val(json.id);
                        $(".order-create-container form input[name='Order[email]']").val(json.email);
                        $(".order-create-container form input[name='Order[phone]']").val(json.phone);
                        $(".order-create-container form input[name='Order[client_name]']").val(json.client_name);

                        if(json.promocode) {
                            $(".promo-code-enter input").val(json.promocode).change();
                        } else {
                            if($(".promo-code-enter input[type=text]").val() != '') {
                                $(".promo-code-enter .promo-code-clear-btn").click();
                            }
                        }
                        
                        $(document).trigger("orderFindUser", json);
                    }
                    else {
                        console.log(json.message);
                    }
                    
                }, "json");
        }
    }
};

dvizh.createorder.init();
