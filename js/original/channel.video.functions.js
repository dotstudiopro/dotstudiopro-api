var $ = jQuery;
$(document).ready(function() {
  var playerwrap = $('.dot-studioz-video-fluidMedia').first();
  var toggleFocusOn = false;
  var playerToggle = $('.dot-studioz-player-togglemode');
  var playlistMode = sessionStorage.getItem('playlistMode') != '' ? sessionStorage.getItem('playlistMode') : 'std';


  var player = playerwrap.find('.player');
  if (player.length === 0) {
    return;
  }



  var minifyVid = player.attr('data-minifyVid') == '1' ? true : false;
  var autoRedir = player.attr('data-autoRedir') == '1' ? true : false;
  var autoPlay = player.attr('data-autoPlay') == '1' ? true : false;
  var enableRecPlaylist = player.attr('data-recPlaylist') == '1' ? true : false;

  // adjustments to the theater mode playlist carousel and standard mode playlist

  $('.dot-studioz-owl-fa').remove(); // don't want the play buttons
  $('.owl-item div:nth-child(2)').remove(); // don't want the titles here
  $('.dot-studioz-playlist-standard-mode').css('height', $('.dot-studioz-playlist-standard-mode').parent().height());


  populatePlaylist();


  if (!enableRecPlaylist) {
    $('.dot-studioz-playlist-theater-mode').html('');
    $('.dot-studioz-video').removeClass('col-md-8').addClass('col-md-12');
    $('.dot-studioz-playlist-standard-mode').remove();
    playerToggle.remove();
  } else {
    if (playlistMode == 'theater') {
      showPlaylistTheaterMode();
    } else {
      showPlaylistStandardMode();
    }

    playerToggle.trigger('click');

  }



  $('.owl-item').on('mousemove', function(e) {
    var tip = $('.carousel-tooltip');
    var posX = $(this).offset().left - $(this).width();
    if (tip.length != 0) {
      tip.css({
        left: posX
      })
    }
  });

  $('.rec-list-item').click(function(e) {
    e.preventDefault();

    var videoId = $(this).attr('href').replace('#', '');
    var strUrl = window.location.href;
    if (strUrl.indexOf('?') != -1) {
      strUrl = strUrl.split('?')[0];
    }
    window.location.assign(strUrl + '?video=' + videoId);

  });


  $('.owl-item').hover(
    function() {
      // setup
      if ($('.dot-studioz-playlist-theater-mode .carousel-tooltip').length == 0) {
        $('.dot-studioz-playlist-theater-mode').append('<div class="carousel-tooltip"></div>');
      }
      var tooltip = $('.carousel-tooltip');
      var parentContainer = $('.dot-studioz-playlist-theater-mode');
      var posX = $(this).offset().left - $(this).width();
      var posY = 20;
      var parentX = parentContainer.offset().left - $(this).width();
      var parentWidth = parentContainer.width();
      var maxRight = parentWidth - parentX;
      var tipWidth = $(this).width();
      var parentWidth = $('.dot-studioz-playlist-theater-mode').width();

      if (parentX >= posX) {
        posX = parentX + 20;
      } else if (posX > maxRight) {
        posX = maxRight - $(this).width() / 2;
      }

      var toolText = $(this).find('a').attr('data-title');
      tooltip.html(toolText);
      tooltip.css({
        left: posX,
        top: posY,
        'min-width': tipWidth
      });

      tooltip.fadeIn(800);

      // make the image grow
      animateThumb($(this), 1);

    },


    function() {
      $('.carousel-tooltip').remove();
      animateThumb($(this), -1);
    }

  );

  function animateThumb(thumb, aniFactor) {

    var oThumbWidth = $('.owl-item').first().width() > $('.owl-item').last().width() ? $('.owl-item').first().width() : $('.owl-item').last().width();
    var oThumbHeight = $('.owl-item').first().height() > $('.owl-item').last().height() ? $('.owl-item').first().height() : $('.owl-item').last().height();

    var props = {};

    if (aniFactor == 1) {
      //props.width = oThumbWidth * 1.1;
      //props.height = oThumbHeight * 1.1;
      thumb.find('.owl-thumb').css('opacity', '1.0');
    } else {
      props.width = oThumbWidth;
      //props.height = oThumbHeight;
      thumb.find('.owl-thumb').css('opacity', '0.7');
    }

    /*
    thumb.animate(props, {
      duration: 200,
    });
    */
  }



  $(window).resize(function() {
    $('.dot-studioz-playlist-standard-mode').css('height', $('.video-js').height());
  });

  player.mouseenter(function() {
    // display the theater or standard mode button
    playerToggle.slideDown();
  });

  playerToggle.mouseenter(function() {
    // display the theater or standard mode button
    toggleFocusOn = true;
  });

  playerToggle.mouseleave(function() {
    // display the theater or standard mode button
    toggleFocusOn = false;
  });

  player.mouseleave(function() {
    // display the theater or standard mode button
    setTimeout(function() {
      if (!toggleFocusOn) {
        playerToggle.slideUp();
      }
    }, 100);
  });

  playerToggle.click(function() {
    if ($('.dot-studioz-playlist-standard-mode').hasClass('active-playlist')) {
      showPlaylistTheaterMode();
    } else {
      showPlaylistStandardMode();
    }

    setTimeout(function() {
      $(window).trigger('resize');
      $('.dot-studioz-playlist-standard-mode').css('height', $('.video-js').height());
    }, 100);
  });



  if (minifyVid) {
    if (playerwrap.length === 0) {
      return;
    }



    var anibox = $('#anibox');
    var scroll_offset = 130;
    anibox.css(getTargetProps());

    $(window).scroll(function(e) {

      if ($(window).width() <= 800) {
        return;
      }

      var vidFull = {
        width: '100%',
        height: '100%',
        boxShadow: 'none',
        outline: 0,
        position: 'inherit',
        right: 0,
        top: 0,
        borderTop: 'inherit'
      };

      var vidSmall = {
        zIndex: '10',
        width: $(window).width() * 0.2,
        height: 'auto',
        boxShadow: '0 5px 2px rgba(0, 0, 0, 0.4)',
        outline: '3px solid #fff',
        position: 'fixed',
        top: 100,
        right: $(window).width() * 0.02
      };


      var scroll_top = $(this).scrollTop();
      if (scroll_top > (playerwrap.offset().top + scroll_offset) && !player.hasClass('onsidebar')) {
        player.hide();


        if (!$('.active-playlist').hasClass('dot-studioz-playlist-theater-mode')) {
          $('.active-playlist').hide();
        }
        anibox.show();
        var targetProps = getTargetProps();
        anibox.animate(targetProps, {
          duration: 500,
          complete: function() {
            player.show();
            anibox.hide();
          }
        });
        player.addClass('onsidebar').css(vidSmall);
      }

      if (scroll_top < (playerwrap.offset().top + scroll_offset) && player.hasClass('onsidebar')) {
        player.hide();
        anibox.show();

        var targetProps = getTargetProps();

        anibox.animate(targetProps, {
          duration: 500,
          complete: function() {
            player.show();
            anibox.hide();
            if (!$('.active-playlist').hasClass('dot-studioz-playlist-theater-mode')) {
              $('.active-playlist').show();
            }
          }
        });
        player.removeClass('onsidebar').css(vidFull);
      }

      $(window).trigger('resize');

    });


    function showPlaylistStandardMode() {
      // show standard mode
      $('.dot-studioz-video').removeClass('col-md-12').addClass('col-md-8');
      $('.dot-studioz-playlist-standard-mode').addClass('col-md-4').addClass('active-playlist').show();
      $('.dot-studioz-playlist-theater-mode').removeClass('active-playlist').css({
        'width': '0',
        'display': 'none'
      });
      sessionStorage.setItem('playlistMode', 'std');
    }

    function showPlaylistTheaterMode() {
      // show theater mode
      $('.dot-studioz-video').removeClass('col-md-8').addClass('col-md-12');
      $('.dot-studioz-playlist-standard-mode').removeClass('col-md-4').removeClass('active-playlist').hide();
      $('.dot-studioz-playlist-theater-mode').addClass('active-playlist').css({
        'width': '100%',
        'display': 'block'
      });
      $('.owl-carousel').trigger('refresh.owl.carousel');
      sessionStorage.setItem('playlistMode', 'theater');
    }


    function getTargetProps() {
      var anibox = $('#anibox');
      var props = {};
      var videoJS = $('.video-js');


      if (!anibox.hasClass('full-display')) {
        // expanding - return the full-size properties
        anibox.removeClass('mini-display').addClass('full-display');
        props.width = $('.dot-studioz-video').width();
        props.height = $('.dot-studioz-video').height();
        props.left = $('.dot-studioz-video').offset().left;
        props.top = $('.dot-studioz-video').position().top;
        props['z-index'] = '1000';
      } else {

        // minifying - return the mini-size properties
        anibox.removeClass('full-display').addClass('mini-display');
        props.width = $(window).width() * 0.2;
        props.height = $(window).width() * .1125;
        props.top = 100;
        props.left = $(window).width() - ($(window).width() * 0.2);
        props['z-index'] = '1000';
      }

      return props;
    }

  }

  var i = 0;
  checkVidLoaded();

  function checkVidLoaded() {
    var max = 10;
    var vid = $('#dsp-vid-js-player_html5_api')[0];
    i++;

    if (vid == undefined && i < max) {
      setTimeout(function() {
        checkVidLoaded()
      }, 1000);
    } else {

      // resize the poster image
      var poster = $('.vjs-poster');
      var posterBkg = "";
      if($(poster).length > 0) {
        posterBkg = $(poster).css('background-image').replace('")', '/1000/562")');
        $(poster).css('background-image', posterBkg);
      }


      if (autoPlay) {
        try {
          vid.play();
        } catch (e) {
          console.log(e);
        }

      }

      // auto redirect functionality
      if (autoRedir) {
        vid.onended = function(e) {
          var aryVidList = $('ul.dot-studioz-video-thumbnails li');
          var aryURLs = [];

          $.each(aryVidList, function(key, val) {
            if ($(this).hasClass('selected')) {
              strToPush = 'selected'
            } else {
              strToPush = $(this).find('a').attr('href');
            }
            aryURLs.push(strToPush);
          });

          var vidIdx = aryURLs.indexOf('selected') + 1;
          var strURL = aryURLs[vidIdx];
          if (strURL == undefined) {
            strURL = aryURLs[0];
          }
          window.location.assign(strURL);
        }

      }

    }

  }


  function populatePlaylist() {

    // populate the right-hand side playlist with items from the Owl Carousel

    var carouselItems = $('.related-videos-carousel').find('a');

    if (carouselItems.length == 0) {
      enableRecPlaylist = false;
    } else {
      var strItemList = '';
      var strPlaylist = '<div><label>RELATED VIDEOS</label></div><ul>'
      $.each(carouselItems, function(key, val) {
        var itemName = $(this).attr('data-title');
        var itemDesc = $(this).attr('data-desc') != '' ? $(this).attr('data-desc') : 'No Description Currently Available';
        var videoId = $(this).attr('href');
        var imgSrc = $(this).find('img').attr('src');

        if (strItemList.indexOf(itemName) === -1) {
          // to make sure all list items are unique
          strItemList += itemName + ','

          strPlaylist += '<li>';
          strPlaylist += ' <a href="' + videoId + '" class="rec-list-item" data-title="' + itemName + '">' + '   <div class="playlist-item">';
          strPlaylist += '     <div class="playlist-img"><img src="' + imgSrc + '" /></div>';
          strPlaylist += '     <div class="playlist-info">';
          strPlaylist += '       <div class="playlist-title">' + itemName + '</div>';
          strPlaylist += '     </div>';
          strPlaylist += '   </div>';
          strPlaylist += ' </a>';
          strPlaylist += '</li>';
        }
      });

      strPlaylist += '</ul>';
      $('.dot-studioz-playlist-standard-mode').append(strPlaylist);
    }

    $('.dot-studioz-playlist-theater-mode').css({
      'width': '0',
      'display': 'none'
    });

  }


});
