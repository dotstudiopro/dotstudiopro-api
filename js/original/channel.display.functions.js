var jQuery = $;
$(document).ready(function() {

		$('ul.dot-studioz-video-thumbnails > li').click(function() {
				var href = $(this).find('a').attr('href');
				window.location = href;
		});

		$(window).resize(function() {
				$('.hoverimg').remove();
		});


		$('ul.dot-studioz-video-thumbnails > li').mouseenter(function() {
				var href = $(this).find('a').attr('href');
				var thumb = $(this).find('img.img');
				var src = thumb.attr('src');

				if ($(this).find('img.hoverimg').length == 0) {
						var strHoverImg = '<img class="img hoverimg" src="' + src + '" style="display:none;position:absolute;" />';
						$(this).append(strHoverImg);
				}
				var hoverImg = $(this).find('.hoverimg');

				var doHover = !hoverImg.hasClass('hovering') && !hoverImg.hasClass('ishovered');

				if (doHover) {
						hoverImg.addClass('hovering');
						var props = {
								width: thumb.width(),
								height: thumb.height(),
								left: thumb.position().left,
								top: thumb.position().top,
								'z-index': 1,
						}
						hoverImg.css(props).show();

						var newProps = {
								width: thumb.width() * 1.1,
								height: thumb.height() * 1.1,
								opacity: 1.0
						}
						newProps.left = props.left - ((newProps.width - thumb.width()) / 2);
						newProps.top = props.top - ((newProps.height - thumb.height()) / 2);

						hoverImg.animate(newProps, {
								duration: 250,
								complete: function() {
										hoverImg.removeClass('hovering').addClass('hovered');
								}
						});

				}


		});

		$('ul.dot-studioz-video-thumbnails > li').mouseleave(function() {
				var hoverImg = $(this).find('.hoverimg');
				var doHover = !hoverImg.hasClass('hovering') && !hoverImg.hasClass('ishovered');
				var thumb = $(this).find('img.img');


				if (doHover) {
						hoverImg.addClass('hovering').removeClass('hovered');
						var props = {
								width: thumb.width(),
								height: thumb.height(),
								left: thumb.position().left,
								top: thumb.position().top,
								'z-index': 1,
						}

						var newProps = {
								width: thumb.width(),
								height: thumb.height(),
								left: thumb.position().left,
								top: thumb.position().top,
								opacity: 0.5
						}

						hoverImg.animate(newProps, {
								duration: 250,
								complete: function() {
										hoverImg.remove();
								}
						});
				}

		});


});
