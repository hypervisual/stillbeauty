/*! bigSlide - v0.4.3 - 2014-01-25
* http://ascott1.github.io/bigSlide.js/
* Copyright (c) 2014 Adam D. Scott; Licensed MIT */
!function(a){"use strict";a.fn.bigSlide=function(b){var c=a.extend({menu:"#menu",push:".push",side:"left",menuWidth:"15.625em",speed:"300"},b),d=this,e=a(c.menu),f=a(c.push),g=c.menuWidth,h={position:"fixed",top:"0",bottom:"0","settings.side":"-"+c.menuWidth,width:c.menuWidth,height:"100%"},i={"-webkit-transition":c.side+" "+c.speed+"ms ease","-moz-transition":c.side+" "+c.speed+"ms ease","-ms-transition":c.side+" "+c.speed+"ms ease","-o-transition":c.side+" "+c.speed+"ms ease",transition:c.side+" "+c.speed+"ms ease"};return e.css(h),f.css(c.side,"0"),e.css(i),f.css(i),e._state="closed",e.open=function(){e._state="open",e.css(c.side,"0"),f.css(c.side,g)},e.close=function(){e._state="closed",e.css(c.side,"-"+g),f.css(c.side,"0")},d.on("click.bigSlide",function(a){a.preventDefault(),"closed"===e._state?e.open():e.close()}),d.on("touchend",function(a){d.trigger("click.bigSlide"),a.preventDefault()}),e}}(jQuery);

$.fn.outerHtml = function() { return $(this).clone().wrap('<div></div>').parent().html(); }
$.fn.serializeObject=function(){var o={};var a=this.serializeArray();$.each(a,function(){if(o[this.name]!==undefined){if(!o[this.name].push){o[this.name]=[o[this.name]];}o[this.name].push(this.value||"");}else{o[this.name]=this.value||"";}});return o;};

$(document).ready(function () {

    var mutex = false;

	$("form").validationEngine();

	$('.timepicker').timepicker({
		minuteStep: 15,
		showInputs: false,
		disableFocus: true,
		defaultTime: '10:00 AM'
	});

    $('.datepicker').datepicker();

    if ($('#treatments-menu').length) {
    	$('#treatments-menu a').on('click', function(e) {
    		e.preventDefault();
    		var $self = $(this);

            $('#treatments-menu a.active').removeClass('active');
            $(this).addClass('active');

    		if ($('.category:visible').attr('id') == $(this).attr('href').substr(1)) return;

    		$('.category:visible').fadeOut(function() {
    			$($self.attr('href')).fadeIn();
    		});
    	});

        $('#treatments-menu a').eq(0).addClass('active');
    }

    $('.product-block h4 a').on('click', function(e) {
        e.preventDefault();

        var $section = $(this).parents('.product-block');

        if (!$section.hasClass('open')) {
            $('.product-block.open').removeClass('open');
            $section.addClass('open');

            $('html, body').animate({
                scrollTop: $( $.attr(this, 'href') ).offset().top - 80
            }, 500);
        } else {
            $('.product-block.open').removeClass('open');
        }
    });

    $(window).on('sb:updateCart', function(e, items, price) {
        $('.cart .cart-total').text(price);
        $('.cart .cart-items').text(items);
    });

    $(document).on('click', '.shopping-cart .checkout', function(e) {
        console.log('Checkout handler');
        $('body').off('click');
    });

    $(document).on('click', '.shopping-cart .remove', function(e) {
        e.stopImmediatePropagation();
        e.preventDefault();
        
        var $self = $(this);

        if (!mutex) {
            mutex = true;

            var payload = {
                action: 'sb_rm_from_cart',
                id: $self.data('product')
            }

            var tpl  = "<p><span class='ion-loading-c'></span> Removing %product% %category% from cart</p>";
            StillBeauty.App.Modal.showMessage('#modal-message',tpl.replace('%product%', $self.data('product')).replace('%category%', $self.data('type')));
 
            $.post(StillBeauty.ajaxurl, payload, function(data) { 
                StillBeauty.App.Modal.hide();
                $('body, html').animate({ scrollTop : 0 }, 500);
                $(window).trigger('sb:updateCart', [data['items'], data['total']]);
                $(window).trigger('sb:renderCart', [data['cart'], data['total']]);
            }, 'json')

            .fail(function() {
                StillBeauty.App.Modal.hide();
                console.error('Error');
                // Report error
            })

            .always(function() {
                mutex = false;
            });     
        }        
    });

    $('.close-cart').on('click', function(e) {
        e.preventDefault();
        $('.shopping-cart').addClass('hide');
    });

    $(window).on('sb:renderCart', function(e, cart, total) {
        var html = {
            "empty" : "<p class=\"empty\">There are no items in your shopping cart</p>",
            "list" : "<ul>%items%</ul>",
            "item" : "<li><a href=\"#\" data-product=\"%id%\" data-name=\"%name%\" data-type=\"%type%\" class=\"ion-ios7-close remove\"></a>%img%<h5>%name% %type%</h5><p>Qty %qty%, <strong>$%subtotal%</strong></p></li>",
            "total": "<ul class=\"shopping-cart-total\"><li><span>%total%</span>Total</li></ul><a href=\"%checkout%\"  class=\"checkout\">Checkout</a>"
        }

        if (cart == null || cart.length == 0) {
            $('.shopping-cart-content').empty().append(html['empty']);
        } else {
            var list = "";

            for(i in cart) {
                subtotal = (parseFloat(cart[i]['quantity']) * parseFloat(cart[i]['product']['price'])).toFixed(2);
                list += html['item'].replace('%id%', cart[i]['product']['id'])
                                    .replace('%img%', cart[i]['product']['src'])
                                    .replace(/%name%/g, cart[i]['product']['name'])
                                    .replace(/%type%/g, cart[i]['product']['type'])
                                    .replace('%qty%', cart[i]['quantity'])
                                    .replace('%subtotal%', subtotal);
            }

            $('.shopping-cart-content').empty();
            $('.shopping-cart-content').append(html['list'].replace('%items%', list));
            $('.shopping-cart-content').append(html['total'].replace('%total%', total).replace('%checkout%', StillBeauty.checkouturl));
        }


    });



    $('.cart').on('click', function(e) {
        
        e.preventDefault();

        if ($('.shopping-cart').hasClass('hide')) {
            e.stopImmediatePropagation();
            $('.shopping-cart').removeClass('hide');


            $('body').on('click', function(e) {
                if (!$(e.target).parents('.shopping-cart').length) {
                    $('.shopping-cart').addClass('hide');
                    $('body').off('click');
                }
            });
        }
    });


    $('.addToCart').on('click', function(e) {
        e.preventDefault();

        var $self = $(this);

        if (!mutex) {
            mutex = true;

            var payload = {
                action: 'sb_add_to_cart',
                nonce: $self.parent('form').find('input[name=nonce]').val(),
                id: $self.parent('form').find('input[name=id]').val()
            }

            var tpl  = "<p><span class='ion-loading-c'></span> Adding %product% %category% to cart</p>";
            StillBeauty.App.Modal.showMessage('#modal-message',tpl.replace('%product%', $self.data('product')).replace('%category%', $self.data('category')));
 
            $.post(StillBeauty.ajaxurl, payload, function(data) { 
                StillBeauty.App.Modal.hide();
                $('body, html').animate({ scrollTop : 0 }, 500);
                $(window).trigger('sb:updateCart', [data['items'], data['total']]);
                $(window).trigger('sb:renderCart', [data['cart'], data['total']]);
                $('.cart').trigger('click');
            }, 'json')

            .fail(function() {
                StillBeauty.App.Modal.hide();
                console.error('Error');
                // Report error
            })

            .always(function() {
                mutex = false;
            });     
        }
    });

    $(document).on('click', '.modal-close', function(e) {
        e.preventDefault();

        StillBeauty.App.Modal.hide();
    });

    $('#booking-form').on('submit', function(e) {
        e.preventDefault();

        if (!mutex && $("#booking-form").validationEngine('validate')) {

            mutex = true;

            var payload = {
                action: 'sb_send_booking'
            }

            $.extend(payload, $("#booking-form").serializeObject());

            var html  = "<p><span class='ion-loading-c'></span> Sending booking...</p>";
            StillBeauty.App.Modal.showMessage('#modal-message', html);
 
            $.post(StillBeauty.ajaxurl, payload, function(data) { 
                StillBeauty.App.Modal.hide();
                html  = "<p>Message sent.</p><a href=\"#\" class=\"modal-close\"></a>";
                StillBeauty.App.Modal.showMessage('#modal-message', html);
                $('#booking-form')[0].reset();
            }, 'json')

            .fail(function() {
                StillBeauty.App.Modal.hide();
                console.error('An error occurred while sending the booking.');
                // Report error
            })

            .always(function() {
                mutex = false;
            });

            mutex = false;     
        }

        return false;
    });

    $('#confirmation form button[type=submit]').on('click', function(e) {
        e.preventDefault();

        var payload = {
            action: 'sb_record_tx',
            custom: confirmation.custom,
            promo: confirmation.promo,
            transaction: confirmation.tx
        }

        if (!mutex) {

            var html  = "<p><span class='ion-loading-c'></span> Redirecting you to PayPal...</p>";
            StillBeauty.App.Modal.showMessage('#modal-message', html);

            $.post(StillBeauty.ajaxurl, payload, function(data) {
                $('#confirmation form').trigger('submit');
            }, 'json')
            .always(function() {
                mutex = false;
            });;
        }
    })

    $('#contact-form').on('submit', function(e) {
        e.preventDefault();

        if (!mutex && $("#contact-form").validationEngine('validate')) {

            mutex = true;

            var payload = {
                action: 'sb_send_contact'
            }

            $.extend(payload, $("#contact-form").serializeObject());

            var html  = "<p><span class='ion-loading-c'></span> Sending booking...</p>";
            StillBeauty.App.Modal.showMessage('#modal-message', html);
 
            $.post(StillBeauty.ajaxurl, payload, function(data) { 
                StillBeauty.App.Modal.hide();
                html  = "<p>Message sent.</p><a href=\"#\" class=\"modal-close\"></a>";
                StillBeauty.App.Modal.showMessage('#modal-message', html);
                $('#contact-form')[0].reset();
            }, 'json')

            .fail(function() {
                StillBeauty.App.Modal.hide();
                console.error('An error occurred while sending the contact form.');
                // Report error
            })

            .always(function() {
                mutex = false;
            });

            mutex = false;     
        }

        return false;
    });

    $('#vouchers-form input[name=delivery]').on('change', function(e) {
        if($('#vouchers-form #post_to_me').is(':checked')) {
            $('#vouchers-form #expresscheck').parents('div').eq(0).fadeIn();
        } else {
            $('#vouchers-form #expresscheck').parents('div').eq(0).fadeOut();
            $('#vouchers-form #expresscheck').prop('checked', false);
        }
    });

    if ($('#vouchers-form input[name=delivery]').length) $('#vouchers-form input[name=delivery]').trigger('change');

    (function() {

        var payload = {
            action: 'sb_init_cart'
        }


        $.post(StillBeauty.ajaxurl, payload, function(data) { 
            $(window).trigger('sb:updateCart', [data['items'], data['total']]);
            $(window).trigger('sb:renderCart', [data['cart'], data['total']]);
        }, 'json')

        .fail(function() {

            console.error('Error');
            // Report error
        })

    })();

    if ($('#checkout-form').length) $('#checkout-form').submit();

    StillBeauty.App.Infotip.init();
    StillBeauty.App.Modal.init();

});
