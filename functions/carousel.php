<?php

/**
 * Functions dealing with the Owl Carousel plugin and instantiation
 *
 */

/**
 * Retrieve and display the theater mode playlist from the given videoId
 *
 * @param string $videoId The video id we need to base recommended videos off of
 *
 * @return string
 */
function ds_theater_mode_playlist($videoId)
{
    $strOut = "";
    $strOut .= "<div class='row'>";
    $strOut .= "    <!-- THEATER MODE PLAYLIST -->";
    $strOut .= "    <div class='col-md-12 col-sm-12 col-xs-12 ds-vid-playlist ds-playlist-theater-mode'>";
    $strOut .= "        <div class='ds-playlist-theater-outer-container'>";
    $strOut .= "            <div><label>Related Videos</label></div>";
    $strOut .= "                <div class='ds-playlist-theater-inner-container'>";
    $strOut .= "                    <div class='ds-playlist-theater-mode-wrapper'>";
    $strOut .= "                         <div class='related-videos-carousel'>";
    $strOut .= ds_owl_recommended_videos_html(array('video_id' => $videoId, 'rec_size' => 8));
    $strOut .= "                          </div>";
    $strOut .= "                     </div>";
    $strOut .= "                </div>";
    $strOut .= "          </div>";
    $strOut .= "    </div>";
    $strOut .= "</div>";
    return $strOut;
}

/**
 * Display the nag message if the dotStudioPRO plugin is not installed
 *
 * @return void
 */
function ds_owl_carousel_check_main_plugin()
{

    ?>

    <div class="update-nag">
        <p>dotstudioPRO Premium Video plugin is not installed, is inactive, or the version is too low for this add-on.  The dotstudioPRO Premium Owl Carousel plugin has been deactivated.</p>
    </div>

    <?php

}

/**
 * Display a select box of available animation effects for the owl carousel plugin display
 *
 * @param string $name The name and ID of the select field
 * @param string $className The class for the select field
 *
 * @return string
 */
function ds_owl_admin_animation_select($name, $className = '')
{
    $aryAnimations = ['bounce', 'flash', 'pulse', 'rubberBand', 'shake', 'swing', 'tada', 'wobble', 'jello', 'bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp', 'bounceOut', 'bounceOutDown', 'bounceOutLeft', 'bounceOutRight', 'bounceOutUp', 'fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig', 'fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeOutLeft', 'fadeOutLeftBig', 'fadeOutRight', 'fadeOutRightBig', 'fadeOutUp', 'fadeOutUpBig', 'flipInX', 'flipInY', 'flipOutX', 'flipOutY', 'lightSpeedIn', 'lightSpeedOut', 'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight', 'rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight', 'hinge', 'rollIn', 'rollOut', 'zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp', 'zoomOut', 'zoomOutDown', 'zoomOutLeft', 'zoomOutRight', 'zoomOutUp', 'slideInDown', 'slideInLeft', 'slideInRight', 'slideInUp', 'slideOutDown', 'slideOutLeft', 'slideOutRight', 'slideOutUp'];
    $strOut        = '<select name=' . $name . ' id=' . $name . ' class="' . $className . '" disabled=disabled>';
    $strOut .= '<option value="">-- None --</option>';
    for ($i = 0; $i <= count($aryAnimations) - 1; $i++) {
        $strOut .= '<option value=' . $aryAnimations[$i] . '>' . $aryAnimations[$i] . '</option>';
    }
    $strOut .= '<select>';
    return $strOut;

}

/**
 * Enqueue scripts and styles for the owl carousel plugin
 *
 * @return void
 */
function ds_owl_carousel()
{
    wp_enqueue_script('owl-carousel', plugins_url( 'js/owl.carousel.min.js', __DIR__ ), array('jquery'));
    wp_enqueue_style('owl-carousel-min', plugins_url( 'css/owl.carousel.min.css', __DIR__ ));
    wp_enqueue_style('ds-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');

}

/**
 * Renders the appropriate html for the owl carousel based on whether a channel or category is being displayed
 *
 * @param string $args The arguments for the Owl Carousel
 *
 * @return string
 */
function ds_owl_carousel_html($args)
{

    if ($args['channels'] !== '') {
        // generate the code for showing items within a channel
        return ds_owl_channel_html($args);
    }
    if ($args['category'] !== '') {
        // generate the code for showing items within a category
        return ds_owl_category_html($args);
    }
}

/**
 * Renders the recommended videos playlist for the channel video player
 *
 * @param string $args The arguments for the Owl Carousel
 *
 * @return string
 */
function ds_owl_recommended_videos_html($args)
{
    $video_id = $args['video_id'];
    $rec_size = $args['rec_size'];

    $recommended = list_recommended($video_id, $rec_size);

    // error checking
    if (!empty($recommended) && $recommended[0] === false) {
        $strOut = $recommended[1];
    } else {
        $opts = ds_owl_create_opts(array(
            'autoplay_hover_pause' => '1',
            'autoplay'             => '0',
            'autoplay_timeout'     => '3000',
            'autoplay_speed'       => '1000',
            'notitle'              => '1',
            'items'                => '8',
        ));

        $rndId  = ds_owl_carousel_rnd_id(5);
        $strOut = "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
        $strOut .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

        foreach ($recommended as $video) {
            $info     = $video->_source;
            $video_id = $video->_id;

            $title       = $info->title;
            $image       = $info->thumb;
            $id          = $video->_id;
            $company_id  = $info->company_id;
            $description = 'No description currently available';
            $slug        = '';

            if (trim($title . '') !== '') {
                $description = strlen($description) > 150 ? substr($description, 0, 150) . "..." : $description;
                $title       = strlen($title) > 50 ? substr($title, 0, 50) . "..." : $title;
                $strOut .= "<div class='center-container item'>";
                $strOut .= "        <div>";
                $strOut .= "            <i class='ds-owl-fa fa fa-play-circle-o fa-3' aria-hidden='true'></i>";
                $strOut .= "            <a href='#$video_id' class='vert-center rec-list-item' data-title='$title' data-desc='$description'>";
                $strOut .= "                <img class='owl-thumb' src='https://image.dotstudiopro.com/$image/177/100' />";
                $strOut .= "            </a>";
                $strOut .= "        </div>";
                $strOut .= "        <div><strong><small class='owl-carousel-subtitle'>$title</small></strong></div>";
                $strOut .= "</div>";
            }

        }
        $strOut .= "</div>";

    }

    return $strOut;

}

/**
 * Renders the owl carousel based on category slug
 *
 * @param string $args The arguments for the Owl Carousel
 *
 * @return string
 */
function ds_owl_category_html($args)
{

    $category_slug = $args['category'];
    $category      = get_page_by_path('/channel-categories/' . $category_slug, OBJECT);
    $title         = $args['title'] !== '' ? $args['title'] : $category->post_title;
    $opts          = ds_owl_create_opts($args);
    $rndId         = ds_owl_carousel_rnd_id(5);
    $titleclass    = $args['titleclass'];

    $carousel = "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
    if ($args['notitle'] != true) {
        $carousel .= "<div class='owl-carousel-title' style='position:relative;'><h2 class='$titleclass'>$title</h2><a class='owl-carousel-ellipsis' href='/channel-categories/$category_slug/' title='More...'>...</a></div>";
    }
    $carousel .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

    $catItems = grab_category($category_slug);

    if ($catItems && is_array($catItems)) {

        foreach ($catItems as $ch) {
            // iterate thru the channels, get the applicable thumbnails, create the HTML output

            $id               = $ch->_id;
            $thumb_id         = isset($ch->videos_thumb) ? $ch->videos_thumb : '';
            $slug             = $ch->slug;
            $spotlight_poster = isset($ch->spotlight_poster) ? $ch->spotlight_poster : '';

            $carousel .= "<div class='center-container item'>";
            $carousel .= "      <div>";
            $carousel .= "          <a href='" . home_url("channels/$slug") . "' class='vert-center'>";
            $carousel .= "              <img class='owl-thumb' src='$spotlight_poster/1280/720' />";
            $carousel .= "          </a>";
            $carousel .= "      </div>";
            $carousel .= "</div>";
        }
    }

    $carousel .= "</div>";
    return $carousel;
}

/**
 * Renders the owl carousel based on channel slug
 *
 * @param string $args The arguments for the Owl Carousel
 *
 * @return string
 */
function ds_owl_channel_html($args)
{

    if (strpos($args['channels'], ',') !== false) {
        $channels = explode(',', $args['channels']);
    } else {
        $channels = array($args['channels']);
    }
    $rndId      = ds_owl_carousel_rnd_id(5);
    $objects    = ds_owl_carousel_build_objects($channels);
    $opts       = ds_owl_create_opts($args);
    $title      = $args['title'] !== '' ? $args['title'] : 'Featured Channels';
    $titleclass = $args['titleclass'];

    $carousel = "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
    if ($args['notitle'] != true) {
        $carousel .= "<div class='owl-carousel-title' style='position:relative;'><h2 class='$titleclass'>$title</h2></div>";
    }
    $carousel .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

    foreach ($objects as $o) {
        if (trim($o->title . '') !== '') {
            $description = strlen($o->description) > 150 ? substr($o->description, 0, 150) . "..." : $o->description;
            $title       = strlen($o->title) > 20 ? substr($o->title, 0, 20) . "..." : $o->title;
            $imageexp    = explode("/", $o->poster);
            $image       = $imageexp[3];
            $carousel .= "<div class='center-container item'>";
            $carousel .= "      <div>";
            $carousel .= "          <i class='ds-owl-fa fa fa-play-circle-o fa-3' aria-hidden='true'></i>";
            $carousel .= "          <a href='" . home_url("channels/$o->slug") . "' class='vert-center' data-title='$o->title' data-desc='$description'>";
            $carousel .= "              <img class='owl-thumb' src='https://image.dotstudiopro.com/$image/1280/720' />";
            $carousel .= "          </a>";
            $carousel .= "      </div>";
            $carousel .= "      <div><strong><small class='owl-carousel-subtitle'>$o->title</small></strong></div>";
            $carousel .= "</div>";
        }

    }

    $carousel .= "</div>";

    return $carousel;
}

/**
 * Formats the carousel options in a way that the plugin can process
 *
 * @param string $args The arguments for the Owl Carousel
 *
 * @return array
 */
function ds_owl_create_opts($args)
{
    unset($args['channels']);
    unset($args['category']);
    unset($args['title']);

    $opts = implode(', ', array_map(function ($v, $k) {return sprintf("%s=%s", trim($k), trim($v));},
        $args,
        array_keys($args)
    ));

    return $opts;
}

/**
 * Generate a random id for each owl carousel item
 *
 * @param int $length The length of the random id
 *
 * @return string
 */
function ds_owl_carousel_rnd_id($length = 10)
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Returns an array of objects used by owl carousel based off of the array of ids
 *
 * @param array $ids The ids of the channels to build the carousel with
 *
 * @return array
 */
function ds_owl_carousel_build_objects($ids = array())
{
    $objs = array();
    foreach ($ids as $id) {
        $obj    = ds_owl_grab_channel_by_id($id);
        $objs[] = $obj[0];
    }
    return $objs;
}

/**
 * Returns channel information based channel id via curl command
 *
 * @param string $id The id of the channel
 *
 * @return object
 */
function ds_owl_grab_channel_by_id($id)
{
    global $ds_curl;
    $channel = $ds_curl->curl_command('single-channel-by-id', array('channel_slug' => str_replace(" ", "", $id)));
    return $channel;

}

/**
 * Returns a list of channels for use in the owl carousel admin area
 *
 * @return string
 */
function ds_owl_carousel_local_channels_list()
{

    global $wpdb;

    $channel_parent = get_page_by_path("channels");

    $channels = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $channel_parent->ID . " ORDER BY post_name ASC");

    $channels_list = "";

    foreach ($channels as $ch) {

        $channels_list .= "<input type='checkbox' name='channel' value='$ch->post_name'> $ch->post_title<br/>";

    }

    return $channels_list;

}

/**
 * Renders an owl carousel from the shortcode input thru the wordpress admin
 *
 * @param array $atts The arguments passed to the shortcode
 *
 * @return string
 */
function ds_owl_carousel_display_shortcode($atts)
{

    $args = shortcode_atts(array(

        'channels'             => '',
        'category'             => '',
        'title'                => '',
        'autoplay'             => true,
        'dots'                 => false,
        'autoplay_timeout'     => 3000,
        'autoplay_speed'       => 1000,
        'autoplay_hover_pause' => false,
        'items'                => 3,
        'slide_by'             => 1,
        'animate_out'          => '',
        'animate_in'           => '',
        'dots'                 => false,
        'rtl'                  => false,
        'nav'                  => false,
        'notitle'              => false,
        'titleclass'           => '',

    ), $atts, 'ds_owl_carousel');

    if (!count($args['channels']) || !count($args['category'])) {
        return;
    }

    return ds_owl_carousel_html($args);

}
add_shortcode('ds_owl_carousel', 'ds_owl_carousel_display_shortcode');
