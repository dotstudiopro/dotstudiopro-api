jQuery(document).ready(function(){

  //Tabs
  jQuery(function($) {

      $('ul.ds-tabs li').click(function() {
        var tab_id = $(this).attr('data-tab');

        console.log("tab_id", tab_id);

        $('ul.ds-tabs li').removeClass('current animated fadeIn');
        $('.ds-tab-link').removeClass('current animated fadeIn');
        $('.ds-tab-content.current').removeClass('current animated fadeIn');

        $(this).addClass('current animated fadeIn');
        $("#" + tab_id).addClass('current animated fadeIn');
      })

    })
    //Scroll to anchors
  jQuery(function($) {
    $('a[href*="#"]:not([href="#"])').click(function() {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html, body').animate({
            scrollTop: target.offset().top
          }, 1000);
          return false;
        }
      }
    });
  });
  //Gridder

  jQuery(function($) {

    if (!$('.gridder').length) return;

    // Call Gridder
    $('.gridder').gridderExpander({
      scroll: true,
      scrollOffset: 30,
      scrollTo: "panel", // panel or listitem
      animationSpeed: 400,
      animationEasing: "easeInOutExpo",
      showNav: true, // Show Navigation
      nextText: "Next", // Next button text
      prevText: "Previous", // Previous button text
      closeText: "Close", // Close button text
      onStart: function() {
        //Gridder Inititialized
      },
      onContent: function() {
        //Gridder Content Loaded
      },
      onClosed: function() {
        //Gridder Closed
      }
    });

  });

  // Toggle and Reveal
  jQuery(function($) {

    if (typeof $('.dot-studioz-video-headliner-description')[0] == "undefined" || $('.dot-studioz-video-headliner-description').length < 1) {

      var h = 0;

    } else {

      var h = $('.dot-studioz-video-headliner-description')[0].scrollHeight;

    }

    $('.dot-studioz-more').click(function(e) {
      e.stopPropagation();
      $('.dot-studioz-video-headliner-description').animate({
        'height': h
      })
    });

    $(document).click(function() {
      $('.dot-studioz-video-headliner-description').animate({
        'height': '100px'
      })
    });

  });


  jQuery(function($) {
    var contList = $('.iframe_container');

    $(window).scroll(function() {
      // when window scrolls, play the video that is in view
      $.each(contList, function(key, val) {
        var theID = $(this).attr('id').replace('_container', '');
        var IsAlreadyPlaying = $(this).attr('data-isplaying') == '1';

        var thisTop = $(this).offset().top - $(window).scrollTop();
        var theLink = $('#' + theID + '_link');

        if (thisTop <= ($(window).height() * .6) && thisTop >= -100) {
          if (!IsAlreadyPlaying) {
            $(this).attr('data-isplaying', '1');
            playTheVid(theLink);
          }
        } else {
          $(this).attr('data-isplaying', '0');
          $(this).find('.iframe_vid').remove();
          theLink.removeClass('hidden');
        }
      });

    });


    function playTheVid(which) {

      var theID = which.attr('href').replace('#', '');
      var iframeSrc = $('#' + theID + '_container').attr('data-vidurl');
      var fmeWidth = which.find('img').width();
      var fmeHeight = which.find('img').height();

      $('.iframe_vid').remove();
      $('.iframe_launch').removeClass('hidden');
      $('.iframe_spinner_container').fadeOut('fast');
      which.addClass('hidden');
      $('#' + theID + '_spinner').fadeIn('medium');

      var strOut = '' + '<iframe name="' + theID + '_fme" class="iframe_vid" src="' + iframeSrc + '&autostart=true&muteonstart=true" width="' + fmeWidth + '" height="' + fmeHeight + '" style="width:' + fmeWidth + 'px\;height:' + fmeHeight + 'px\;" scrolling="no" frameborder="0" allowfullscreen></iframe>'
      $('#' + theID + '_link').after(strOut);

      setTimeout(function() {
        $('#' + theID + '_spinner').fadeOut('slow');
      }, 2000);



    }

    // video image preview iframe swap onclick
    $('a.iframe_launch').click(function(e) {
      e.preventDefault();
      playTheVid($(this));
    });


  });



  //Limit Characters

  jQuery(function($) {
    $(".character-limit-90").each(function(i) {
      len = $(this).text().length;
      if (len > 90) {
        $(this).text($(this).text().substr(0, 90) + '...');
      }
    });
  });

  // Sharing POP UP

  (function($, window) {
    function windowPopup(url, width, height) {
      // Calculate the position of the popup so
      // it’s centered on the screen.
      var left = (screen.width / 2) - (width / 2),
        top = (screen.height / 2) - (height / 2);
      window.open(
        url,
        "_new",
        "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=" + width + ",height=" + height + ",top=" + top + ",left=" + left
      );
    }
    $(function() {
      $(".js-social-share").on("click", function(e) {
        console.log("FIRING");
        e.preventDefault();

        windowPopup($(this).attr("href"), 500, 300);
      });
    });
  })(jQuery, window);


  // Super quick and dirty lazy loader script
  function elementInViewport(el) {
    var top = el.offsetTop;
    var left = el.offsetLeft;
    var width = el.offsetWidth;
    var height = el.offsetHeight;

    while (el.offsetParent) {
      el = el.offsetParent;
      top += el.offsetTop;
      left += el.offsetLeft;
    }

    return (
      top < (window.pageYOffset + window.innerHeight) &&
      left < (window.pageXOffset + window.innerWidth) &&
      (top + height) > window.pageYOffset &&
      (left + width) > window.pageXOffset
    );
  }

  jQuery(window).scroll(function() {
    jQuery("img.lazy:not([src])").each(function() {
      //console.log(jQuery(jQuery(this).parent().find('.dot-studioz-overlay')));
      jQuery(jQuery(this).parent().find('.dot-studioz-overlay')).hide();
      // If the element is in the viewport, set the image source
      if (elementInViewport(this)) {
        jQuery(this).hide().attr('src', jQuery(this).attr('data-original')).fadeIn('slow');
        jQuery(this).parent().find('.dot-studioz-overlay').delay(800).fadeIn('slow');
      }
    });
  });
});
