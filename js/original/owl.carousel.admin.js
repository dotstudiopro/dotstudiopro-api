
jQuery(function ($) {
		var channels, category, title;
		$('#carousel-type').change(function() {
				$('.carousel-list').css('display', 'none');
				//var which = '#' + $(this).val() + '-carousel-list';
				var which = $(this).val();
				$('#' + which + '-carousel-list').css('display', 'block');
				$('#channel-or-cat').text(which);

				$('input[name="channel"]').removeAttr('checked');
				$('input[name="category"]').removeAttr('checked');
				clearVals();
				$('#dot-studioz-carousel-built-shortcode').text('');
		});


		$('.warn-templates-exist').click(function() {
				var c = confirm('WARNING - TEMPLATES WILL BE OVERWRITTEN!!!\n\nExecuting this function will overwrite the existing dotStudioPro for Wordpress templates in your theme directory. Are you CERTAIN you want to do this?\n\n\n');
				if (c) {
						window.location.href = $(this).attr('data-href');
				}
		})


		$('.menu-tab').click(function() {
				$('.tab-content').hide();
				$('.menu-tab').removeClass('admin-tab-active');
				var content = $(this).find('span').attr('data-content');
				$('#' + content).show();
				$(this).addClass('admin-tab-active');
		});

		$('input[name="category"]').change(function() {
				category = "category='" + $(this).val() + "'";
				displayShortcode();
		});

		$('.textinput').keyup(function() {
				displayShortcode();
		});

		$('#opts-notitle').click(function() {
				if ($(this).attr('checked') == 'checked') {
						$('#title').val('');
						$('.titleinfo').css('display', 'none');
				} else {
						$('#title').val('').removeAttr('readonly');
						$('.titleinfo').css('display', 'block');
				}
		})

		$('#dot-studioz-carousel-built-shortcode').focus(function() {
				$(this).select();
		});

		$('input[name="channel"]').change(function() {
				var channelList = $('input[name="channel"]');
				var channelCount = 0;
				clearVals();

				$.each(channelList, function() {
						if ($(this).attr('checked') == 'checked') {
								channels += $(this).val() + ','
								channelCount++;
						}
				});
				if (channelCount != 0) {
						channels = " channels='" + channels.substr(0, channels.length - 1) + "'";
				}

				displayShortcode();
		});

		$('.opt-change').change(function() {
				displayShortcode();
		});

		$('#opts-items').change(function() {
				$('.animate-type').attr('disabled', 'disabled');
				if ($(this).val() == '1') {
						$('.animate-type').removeAttr('disabled');
				}
		});

		function clearVals() {
				channels = '';
				category = '';
				title = '';

		}

		function getOptions() {
				var strOut = '';
				strOut += $('#opts-dots').attr('checked') == 'checked' ? " dots='1'" : '';
				strOut += $('#opts-autoplayHoverPause').attr('checked') == 'checked' ? " autoplay_hover_pause='1'" : ''
				strOut += $('#opts-autoplay').attr('checked') != 'checked' ? " autoplay='0'" : '';
				strOut += " autoplay_timeout='" + $('#opts-autoplayTimeout').val() + "'";
				strOut += " autoplay_speed='" + $('#opts-autoplaySpeed').val() + "'";
				strOut += $('#opts-slide-by').val() != '1' ? " slide_by='" + $('#opts-slide-by').val() + "'" : '';
				strOut += $('#opts-rtl').val() == '1' ? " rtl='1'" : '';
				strOut += $('#opts-notitle').attr('checked') == 'checked' ? " notitle='1'" : '';
				strOut += $('#opts-nav').attr('checked') == 'checked' ? " nav='1'" : '';
				strOut += $('#title').val() != '' ? " title='" + $('#title').val() + "'" : '';
				strOut += $('#titleclass').val() != '' ? " titleclass='" + $('#titleclass').val() + "'" : '';
				strOut += $('#opts-items').val() != '3' ? " items='" + $('#opts-items').val() + "'" : '';
				strOut += $('#opts-items').val() == '1' && $('#opts-animate-in').val() != '' ? " animate_in='" + $('#opts-animate-in').val() + "'" : '';
				strOut += $('#opts-items').val() == '1' && $('#opts-animate-out').val() != '' ? " animate_out='" + $('#opts-animate-out').val() + "'" : '';
				return strOut;
		}


		function displayShortcode() {
				var strOut = '';
				if ((category != undefined && category != '') || (channels != undefined && channels != '')) {
						strOut = '[dot_studioz_owl_carousel ' + category + channels + getOptions() + ']'

				}
				$('#dot-studioz-carousel-built-shortcode').text(strOut);
		}
});
