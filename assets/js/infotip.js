var StillBeauty = StillBeauty || {};
StillBeauty.App = StillBeauty.App || {};

StillBeauty.App.Infotip = (function(){

	var w = 84;

	var defaults = {
		direction: "west",
		width: "4",
		text: "[ Empty ]"
	}

	var markup = "<div class='infotip-modal %direction%' style='top: %top%; left: %left%; width: %width%; visibility: hidden;'><span class='infotip-arrow'></span><div>%text%</div></div>";

	function close() {
		$('.infotip-modal').remove();
		$('body').off('click keyup');
	}

	function init() {
		$('.infotip').each(function() {
			$(this).on('click', function(e) {
				e.stopImmediatePropagation();
				e.preventDefault();

				if (typeof StillBeauty.App.Infotip === 'undefined') {
					alert(":(");
				} else {

					var data = defaults;

					data['direction'] = $(this).data('placement');
					data['text'] = ($(this).data('original-title') + $(this).data('content'))
									.replace(/&lt;/, '<')
									.replace(/&gt;/, '>');

					width = w * data['width'];

					var $icon = $('span[class^=ion-]', $(this)), top = 0, left = 0;

					var html = markup.replace(/%color%/g, data['color'])
									 .replace(/%background%/g, data['background'])
									 .replace(/%direction%/g, data['direction'])
									 .replace(/%width%/g, width + 'px')
									 .replace(/%text%/g, data['text']);


					$('body').append(html);

					switch(data['direction']) {
						case 'east':
							top = ($icon.offset().top + $icon.outerHeight()/2) - ($('.infotip-modal').height()/2) + 'px';
							left = ($icon.offset().left - width - 10) + 'px';
							break;
						case 'west':
							top = ($icon.offset().top + $icon.outerHeight()/2) - ($('.infotip-modal').height()/2) + 'px';
							left = ($icon.offset().left + $icon.outerWidth() + 10) + 'px';
							break;
						case 'north':
							top = ($icon.offset().top + $icon.outerHeight() + 10) + 'px';
							left = ($icon.offset().left + $icon.outerWidth()/2) - width/2 + 'px';

							break;
						case 'south':
							top = ($icon.offset().top - $('.infotip-modal').height() - 10) + 'px';
							left = ($icon.offset().left + $icon.outerWidth()/2) - width/2 + 'px';
							break;
					}

					$('.infotip-modal').css({ top: top, left: left, visibility: 'visible' });

					$('.infotip-modal').on('click', function(e) {
						e.stopImmediatePropagation();
						e.preventDefault();
						$(this).off('click');
					});

					$('body').on('keyup', function(e) {
						e.preventDefault();
						console.log('[infotip] keyup event');

						if (e.keyCode === 27) {
							e.stopImmediatePropagation();
							close();
						}
					});

					$('body').on('click', function(e) {
						e.preventDefault();
						close();
					});
				}
			});
		})	
	}

	return {
		init: init,
		close : close
	}

})();