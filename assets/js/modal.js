/* This has been adapted from http://lab.hakim.se/avgrund */

var StillBeauty = StillBeauty || {};
StillBeauty.App = StillBeauty.App || {};

StillBeauty.App.Modal = (function(){

	var container = document.documentElement,
		popup = document.querySelector( '.modal-popup-animate' ),
		cover = document.querySelector( '.modal-cover' ),
		callback = null,
		currentState = null;
		custom_event = { target : null, type: null, data : null };

	container.className = container.className.replace( /\s+$/gi, '' ) + ' modal-ready';

	// Deactivate on ESC
	function onDocumentKeyUp( event ) {
		if( event.keyCode === 27 ) {
			if ($('.infotip-modal').length) {
				StillBeauty.App.Infotip.close();
			} else {
				deactivate();	
			}	
		}
	}

	// Deactivate on click outside
	function onDocumentClick( event ) {
		if( event.target === cover || $(event.target).hasClass('content-holder') ) {

			if ($('.infotip-modal').length) {
				StillBeauty.App.Infotip.close();
			} else {
				deactivate();	
			}
			
		}
	}

	function activate( state ) {
		/*
		Medibank.Util.addEventHandler(document, 'keyup', onDocumentKeyUp);
		Medibank.Util.addEventHandler(document, 'click', onDocumentClick);
		Medibank.Util.addEventHandler(document, 'touchstart', onDocumentClick);
		*/
		$(document).on('keyup', onDocumentKeyUp);
		$('.modal-cover').on('click', onDocumentClick);


		if (!empty(currentState)) removeClass( popup, currentState );
		addClass( popup, 'no-transition' );
		if (!empty(state)) addClass( popup, state );

		setTimeout( function() {
			removeClass( popup, 'no-transition' );
			addClass( container, 'modal-active' );
		}, 0 );

		currentState = state;
	}

	function deactivate() {

		$(document).off('keyup');
		$('.modal-cover').off('click');


		removeClass( container, 'modal-active' );
		removeClass( popup, 'modal-popup-animate');
		popup.style.display = 'none';

		if (typeof callback === 'string') eval(callback);

		if (!empty(custom_event.type)) {
			$(custom_event.target).trigger(custom_event.type, custom_event.data);
		}
	}

	function disableBlur() {
		addClass( document.documentElement, 'no-blur' );
	}

	function empty(s) {
		return s==null || s=="";
	}

	function addClass( element, name ) {
		element.className = element.className.replace( /\s+$/gi, '' ) + ' ' + name;
	}

	function removeClass( element, name ) {
		element.className = element.className.replace( name, '' );
	}

	function show(selector){
		popup = document.querySelector( selector );
		addClass(popup, 'modal-popup-animate');
		popup.style.display = 'block';
		activate();
		return this;
	}

	function showMessage(selector, html) {
		$(selector).empty().append(html);
		show(selector);
	}

	function hide() {
		deactivate();
	}

	function init() {
		$('.modal-popup').each(function() {
			
			var dim = { w: $(this).outerWidth(), h: $(this).outerHeight() }

			if (!$(this).hasClass('modal-fullscreen')) {
				$(this).css({
					marginTop: parseInt(-dim.h/2),
					marginLeft: parseInt(-dim.w/2)
				});
			}

			$(this).css({
				display: 'none'
			});

			var html = $(this).outerHtml();
			$(this).remove();
			$('body').append(html);
		})	

		$('a[rel=modal-popup]').on('click', function(e) { 
			e.preventDefault(); 
			callback = $(this).data('deactivate');
			show($(this).attr('href')); 

			custom_event.target = $(this).attr('href');
			custom_event.type = $(this).data('event');
			custom_event.data = { "action" : "close" };

			if (!empty(custom_event.type)) {
				$(custom_event.target).trigger(custom_event.type, { "action" : "open" });
			}
		})

		$('a.modal-close , a.cancel').on('click', function(e) {
			e.preventDefault();
			hide();
		});

	}

	return {
		activate: activate,
		deactivate: deactivate,
		disableBlur: disableBlur,
		showMessage: showMessage,
		show: show,
		hide: hide,
		init: init
	}

})();