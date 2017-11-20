<?php

/**
 * Functions whose sole purpose involves channels; checking, displaying, formatting, etc.
 *
 */

/**
 * Determine if the channel we are on is a parent channel
 *
 * @return bool
 */
function ds_channel_is_parent()
{
    global $wpdb, $post;
    $channel_check_grab = get_page_by_path('channels');
    $channel_check      = $channel_check_grab->ID;
    if ($post->post_parent != $channel_check) {
        return false;
    }
    $results = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type != 'revision' AND post_parent = " . $post->ID);
    if (count($results) > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Determine if the channel we are on is a child channel
 *
 * @return bool
 */
function ds_channel_is_child()
{
    global $post;
    if ($post->post_parent == 0) {
        return false;
    }
    $channel_check_grab  = get_page_by_path('channels');
    $channel_parent      = $channel_check_grab->ID;
    $channel_grandparent = wp_get_post_parent_id($post->post_parent);
    if ($channel_parent == $channel_grandparent) {
        return true;
    } else {
        return false;
    }
}

/**
 * Completely reprocess/recreate all channel pages; this is done when the admin requests a flush
 *
 * @return void
 */
function channels_check()
{
    // This process can take a moment, so we make sure we have time
    set_time_limit(240);

    global $wpdb;

    $channels = list_channels();

    $channel_check = get_page_by_path('channels');

    // Check if we have a main channel page; if not, create one
    if (!$channel_check) {

        $channel_check_page_id = wp_insert_post(array(
            'post_title'   => "All Channels",
            'post_type'    => 'page',
            'post_name'    => "channels",
            'post_status'  => 'publish',
            'post_excerpt' => 'Channels',
        ));

    } else {

        $channel_check_page_id = $channel_check->ID;

    }

    // If we don't have any channels, no need to try to process a non-existent array
    if(!is_array($channels)) return;

    foreach ($channels as $c) {

        if (!isset($c->categories[0]->slug)) {
            continue;
        }

        $slug = rtrim(ltrim($c->slug, '-'), '-');

        $check = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "' AND post_parent = $channel_check_page_id");

        if (count($check) > 0) {
            continue;
        }

        $channel_info = "";

        if(!empty($c->description)) {
            $channel_info = $c->description;
        } else if(!empty($c->video->description)) {
            $channel_info = $c->video->description;
        } else if(!empty($c->title)) {
            $channel_info = $c->title;
        } else {
            $channel_info = "No description.";
        }

        $page_id = wp_insert_post(array(
            'post_title'   => !empty($c->title) ? $c->title : ucwords(str_replace('-', ' ', $slug)),
            'post_type'    => 'page',
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_excerpt' => $channel_info,
            'post_parent'  => $channel_check_page_id,
        ));

        update_post_meta($page_id, 'ds-category', $c->categories[0]->slug);

        if (!empty($c->spotlight_poster)) {
            // Set up a spotlight poster for use in things like the iframe replacement div
            update_post_meta($page_id, 'ds-spotlight-poster', $c->spotlight_poster);
        }

        if (count($c->childchannels) > 0) {

            $parent_id = $page_id;

            foreach ($c->childchannels as $ch) {

                $chname = isset($ch->name) ? $ch->name : '';

                $slug2 = rtrim(ltrim($ch->slug, '-'), '-');

                $page_id = wp_insert_post(array(
                    'post_title'   => $ch->title,
                    'post_type'    => 'page',
                    'post_name'    => $slug2,
                    'post_status'  => 'publish',
                    'post_excerpt' => 'Channel ' . $chname,
                    'post_parent'  => $parent_id,
                ));

                update_post_meta($page_id, 'ds-category', $c->categories[0]->slug);

                if (!empty($c->spotlight_poster)) {
                    // Set up a spotlight poster for use in things like the iframe replacement div
                    update_post_meta($page_id, 'ds-spotlight-poster', $c->spotlight_poster);
                }

            }

        }

    }

}

/**
 * Loop through the channels within a category to display them
 *
 * @return void
 */
function channel_loop()
{

    $channels = list_channels();

    if (!$channels || count($channels) < 1) {

        echo "No channels to display.";

        return;
    }

    foreach ($channels as $c) {

        echo "Title: " . $c->title . "<br/>";

        echo "Image: <img src='" . $c->videos_thumb . "/380/215' /><br/>";

    }

}

/**
 * Loop through categories on the channel categories page
 *
 * @return void
 */
function categories_loop()
{

    set_time_limit(120);

    $cat = list_categories();

    foreach ($cat as $c) {

        $post = get_page_by_path('channel-categories/' . $c->slug);

        $show_check = get_post_meta($post->ID, 'ds_show_category', true);

        if ($show_check != 1) {
            continue;
        }

        $image = get_option('ds-category-image-' . $c->slug);

        if ($image) {

            $thumb_id = $image;

        } else if (isset($c->image->poster)) {

            $thumb_id = $c->image->poster . "/960/540";

        } else if (isset($c->image->videos_thumb)) {

            $thumb_id = $c->image->videos_thumb . "/960/540";

        } else {

            $thumb_id = 'https://placehold.it/960x540';

        }

        echo "<li><a href='" . home_url("channel-categories/" . $c->slug) . "'><img class='img img-responsive' src='$thumb_id' /><label class='delay' style='display: inline-block;'><h1>" . $c->name . "</h1></label></a></li>";

    }

}

/**
 * Completely reprocess/recreate all category pages.  This is done when the admin requests a flush.
 *
 * @return void
 */
function categories_check()
{

    global $wpdb;

    $categories = list_categories();

    $category_post_check = get_page_by_path('channel-categories');

    if (!$category_post_check) {

        $category_page_id = wp_insert_post(array(
            'post_title'   => "Channel Categories",
            'post_type'    => 'page',
            'post_name'    => "channel-categories",
            'post_status'  => 'publish',
            'post_excerpt' => 'Channel Categories',
        ));

    } else {

        $category_page_id = $category_post_check->ID;

    }

    foreach ($categories as $c) {

        $check = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $c->slug . "' AND post_type != 'nav_menu_item'");

        if (count($check) > 0) {

            continue;

        }

        $page_id = wp_insert_post(array(
            'post_title'   => $c->name,
            'post_type'    => 'page',
            'post_name'    => $c->slug,
            'post_status'  => 'publish',
            'post_excerpt' => 'Channel Category ' . $c->name,
            'post_parent'  => $category_page_id,
        ));

        update_post_meta($page_id, 'ds_show_category', 1);

        if ($c->homepage != 1) {

            update_post_meta($page_id, 'ds_show_category', 0);

        }

        if (!empty($c->image->spotlight_poster)) {
            update_post_meta($page_id, 'ds-spotlight-poster', $c->image->spotlight_poster);
        }

    }

}

/**
 * Create the channel category menu; we add this so the admin can set up a menu to go to the categories directly
 *
 * @param string $video_id The video id we need to base recommended videos off of
 * @param string $rec_size The number of items we want to get back
 *
 * @return void
 */
function ds_create_channel_category_menu()
{

    // Check if the menu exists
    $menu_name   = 'Browse Channel Categories';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    // If it doesn't exist, let's create it.
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);

        // Set up default menu items

        $cat = list_categories();

        foreach ($cat as $c) {

            $channels = grab_category($c->slug);

            $args = array(
                'menu-item-title'   => __($c->name),
                'menu-item-classes' => $c->slug,
                'menu-item-url'     => home_url('/channel-categories/' . $c->slug . '/'),
                'menu-item-status'  => 'publish');

            wp_update_nav_menu_item($menu_id, 0, $args);

        }

        $auto_assign = get_option('ds_auto_assign_menu');

        if (empty($auto_assign)) {
            return;
        }

        // Set the main menu up in the correct spot post-flush.
        $locations               = get_theme_mod('nav_menu_locations');
        $locations['main_nav']   = $menu_id;
        $locations['header_nav'] = $menu_id;
        $locations['top']        = $menu_id; // WP 2017
        set_theme_mod('nav_menu_locations', $locations);

    }

}

// Completely delete and recreate all category and channel pages.
/**
 * Completely delete and recreate all category and channel pages.
 *
 * This is necessary when channels are created or deleted in the DSP dashboard, as well as categories.  There are other use cases, but that is the most common.
 *
 * @return void
 */
function ds_site_flush()
{

    global $wpdb;

    $all_cat_page = get_page_by_path('channel-categories');

    if (isset($all_cat_page->ID)) {

        $cats = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_cat_page->ID);

        foreach ($cats as $cat) {

            wp_delete_post($cat->ID, true); // Delete category page, force true deletion

            $wpdb->query("DELETE FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $cat->post_name . "' AND post_type = 'nav_menu_item'");

        }

    }

    $all_chan_page = get_page_by_path('channels');

    if (isset($all_chan_page->ID)) {

        $chans = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_chan_page->ID);

        foreach ($chans as $chan) {

            $child_check = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $chan->ID);

            foreach ($child_check as $child) {

                wp_delete_post($child->ID, true); // Delete child channel page, force true deletion

            }

            wp_delete_post($chan->ID, true); // Delete channel page, force true deletion

        }

    }

    wp_delete_nav_menu("Browse Channel Categories");

    // Rebuild Categories
    categories_check();

    // Rebuild Channels
    channels_check();

    ds_create_channel_category_menu();

    wp_redirect(site_url() . "/wp-admin/admin.php?page=dot-studioz-options&resynced=1");
    exit;

}

/**
 * Initialize the necessary functions for extra fields on Category pages
 *
 * @return void
 */
function ds_category_images_init()
{
    if (empty($_GET['post'])) {
        return false;
    }

    $post       = get_post($_GET['post']);
    $categories = get_page_by_path("channel-categories");

    if ((int) $post->post_parent === (int) $categories->ID) {
        add_meta_box("ds_category_image", "Category Image", "ds_category_image_field", "page", "normal", "high");
    }
}

/**
 * Create the field for uploading custom category images
 *
 * @param string $video_id The video id we need to base recommended videos off of
 * @param string $rec_size The number of items we want to get back
 *
 * @return void
 */
function ds_category_image_field()
{
    $post      = get_post($_GET['post']);
    $image     = get_option('ds-category-image-' . $post->post_name);
    $cat_image = '';
    if ($image) {
        $cat_image = "<img src='$image' />";
    }
    ?>
            <table class='widefat'>
                <tbody>
                    <tr><td><h3>Upload new image</h3></td><td><input type='file' name='ds-category-image' /></td></tr>
                    <tr><td><h3>Current image</h3></td><td><?php echo $cat_image; ?></td></tr>
                </tbody>
            </table>
    <?php
}

/**
 * Get a random filename for image upload; TODO: see if we even need to/should do this
 *
 * @param string $dir The directory for the uploaded file; not used
 * @param string $name The uploaded filename; not used
 * @param string $ext The extension of the uploaded file
 *
 * @return string
 */
function ds_cust_filename($dir, $name, $ext)
{
    return $_FILES['ds-category-image']['name'] . rand(100, 999) . time() . $ext;
}

/**
 * Save the category image on post save
 *
 * @return void
 */
function ds_save_category_image_field()
{
    if (!isset($_FILES['ds-category-image'])) {
        return;
    }

    global $post;
    $slug         = $post->post_name;
    $uploadedfile = $_FILES['ds-category-image'];
    $movefile     = wp_handle_upload($uploadedfile, array('test_form' => false, 'unique_filename_callback' => 'ds_cust_filename'));
    if ($movefile && !isset($movefile['error'])) {
        update_option("ds-category-image-$slug", $movefile['url']);
    }
}

/**
 * Include the video player template if we have it
 *
 * @return void
 */
function display_channel_video_player()
{
    $vidPlayerFile = "channel-video.php";

    if (is_file(dirname(__FILE__) . "/../templates/components/" . $vidPlayerFile)) {
        include dirname(__FILE__) . "/../templates/components/" . $vidPlayerFile;
    }

}

//
/**
 * Check if the current channel is a parent channel for redirect
 *
 * If we have a channel that is a parent, we redirect to the first child, since we don't have pages for parent channel displays
 *
 * @return void
 */
function ds_is_channel_parent_check()
{

    if (ds_channel_is_parent()) {

        $videos = grab_channel();

        $children = $videos[0]->childchannels;

        $child_slug = $children[0]->slug;

        $current = get_post(get_the_ID());

        $category = get_query_var("channel_category", false);

        if (!$category) {

            $category = 'featured';

        }

        $url = home_url("channels/" . $current->post_name . "/" . $child_slug . "/");

        wp_redirect($url);
        die();
    }
}

/**
 * Grab a a channel from within a channel page
 *
 * @return void
 */
function igrab_channel()
{

    global $post;

    $video = false;

    $is_child = ds_channel_is_child();

    $videos = grab_channel();

    if (!is_array($videos)) {

        return array();

    }

    $channel_title = $videos[0]->title;

    $company = $videos[0]->company;

    $company_id = isset($videos[0]->video->company_id) ? $videos[0]->video->company_id : '';

    $title = $is_child ? $videos[0]->childchannels[0]->title : $videos[0]->title;

    $description = "";
    if($is_child && !empty($videos[0]->childchannels[0]) && !empty($videos[0]->childchannels[0]->description)) {
        $description = $videos[0]->childchannels[0]->description;
    } else if(!empty($videos[0]->description)) {
        $videos[0]->description;
    }

    $actors = $videos[0]->actors;

    $writers = $videos[0]->writers;

    $directors = $videos[0]->directors;

    $playlist = $is_child ? $videos[0]->childchannels[0]->playlist : $videos[0]->playlist;

    $image_id = "";

    if($is_child && !empty($playlist[0]->thumb)) {
        $image_id = "https://image.myspotlight.tv/" . $playlist[0]->thumb;
    } else if(!empty($videos[0]->playlist) && !empty($videos[0]->playlist[0]->thumb)) {
        $image_id = "https://image.myspotlight.tv/" . $videos[0]->playlist[0]->thumb;
    } else if(!empty($videos[0]->video) && !empty($videos[0]->video->thumb)) {
        $image_id = "https://image.myspotlight.tv/" . $videos[0]->video->thumb;
    }

    $playlist = $is_child ? $videos[0]->childchannels[0]->playlist : $videos[0]->playlist;

    $channel_parent = get_post($post->post_parent);

    $poster = !empty($videos[0]->poster) ? $videos[0]->poster : "";

    $to_return['playlist'] = $playlist;

    $to_return['details'] = array('description' => $description, 'actors' => $actors, 'writers' => $writers, 'directors' => $directors, 'poster' => $poster);

    $to_return['link_url'] = $is_child ? home_url("channels/" . $channel_parent->post_name . "/" . $post->post_name) : home_url("channels/" . $post->post_name . "/");

    $to_return['count'] = count($playlist);

    $video = get_query_var("video", false);

    if ($video) {

        $id = get_query_var("video", false);

        $url = home_url("channels/" . $channel_parent->post_name . "/" . $post->post_name . "/video=$id");

        foreach ($playlist as $pl) {

            if ($pl->_id == $id) {

                $title = $pl->title;

                $duration = round($pl->duration / 60);

                $description = $pl->description;

                $country = $pl->country;

                $language = $pl->language;

                $image_id = "http://image.myspotlight.tv/" . $pl->thumb;

                break;

            }

        }

    }

    $to_return['for_meta'] = (object) array('description' => $description, 'url' => $to_return['link_url'], 'channel_title' => $channel_title, 'title' => $title, 'image_id' => $image_id);

    return $to_return;

}

/**
 * Get the headline video for a channel in the video-channel template
 *
 * @return void
 */
function channel_headline_video()
{

    global $ds_curl;

    $video = get_query_var("video", false);

    if (ds_channel_is_child()) {

        $videos = grab_channel();

        if (!is_array($videos)) {

            $videos = new stdClass;

            return $videos;

        }

        $playlist = new stdClass;
        if(!empty($videos[0]->childchannels[0]) && !empty($videos[0]->childchannels[0]->playlist)) {
            $playlist = $videos[0]->childchannels[0]->playlist;
        } else if(!empty($videos[0]->playlist)) {
            $playlist = $videos[0]->playlist;
        }

        $id = !empty($playlist->_id) ? $playlist->_id : "";

        $title = !empty($playlist->title) ? $playlist->title : "";

        $duration = !empty($playlist->duration) ? round($playlist->duration / 60) : 0;

        $description = isset($videos[0]->description) ? $videos[0]->description : '';

        $company = isset($videos[0]->company) ? $videos[0]->company : '';

        $company_id = isset($videos[0]->childchannels[0]->company_id) ? $videos[0]->childchannels[0]->company_id : $videos[0]->spotlight_company_id;

        $country = isset($playlist->country) ? $playlist->country : '';

        $language = isset($playlist->language) ? $playlist->language : '';

        $year = isset($videos[0]->year) ? $videos[0]->year : '';

        $rating = isset($videos[0]->rating) ? $videos[0]->rating : '';

        if ($video) {
            $id = get_query_var("video", false);

            foreach ($videos[0]->childchannels[0]->playlist as $pl) {

                if ($pl->_id == $id) {

                    $title = $pl->title;

                    $duration = round($pl->duration / 60);

                    $description = $pl->description;

                    $country = $pl->country;

                    $language = $pl->language;

                    break;

                }

            }

        }

        $player_url = "https://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

        $to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $description, 'company' => $company, 'country' => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url);

        return $to_return;

    } else {

        $videos = grab_channel();

        if (!is_array($videos)) {

            $videos = new stdClass;

            return $videos;

        }

        $id       = $videos[0]->playlist[0]->_id;
        $playlist = !empty($videos[0]->playlist[0]) ? $videos[0]->playlist[0] : new stdClass;
        // If we don't have a video as part of this, set the video as the first video in the playlist
        $video = !empty($videos[0]->video) ? $videos[0]->video : $playlist;

        $title = $duration = '';

        if (isset($playlist->title)) {
            $title = $playlist->title;
        } else if ($video->title) {
            $title = $video->title;
        }

        if (isset($playlist->duration)) {
            $duration = round($playlist->duration / 60);
        } else if (isset($video->duration)) {
            $duration = round($video->duration / 60);
        }

        $chdescription = "";
        if (isset($video->description)) {

            $chdescription = $video->description;

        } else if (isset($playlist->description)) {

            $chdescription = $playlist->description;

        } else if (isset($video->country)) {

            $chdescription = $video->country;

        }

        $company = isset($videos[0]->company) ? $videos[0]->company : '';

        $company_id = isset($playlist->company_id) ? $playlist->company_id : $videos[0]->spotlight_company_id;

        $country = !empty($playlist) && !empty($playlist->country)
        ? $playlist->country :
        !empty($video) && !empty($video->country)
        ? $video->country : '';

        $language = isset($playlist->language) ? $playlist->language : isset($video->language) ? $video->language : '';

        $year = isset($videos[0]->year) ? $videos[0]->year : '';

        $rating = isset($videos[0]->rating) ? $videos[0]->rating : '';

        if ($video) {

            $id = get_query_var("video", false);

            foreach ($videos[0]->playlist as $pl) {

                if ($pl->_id == $id) {

                    $title = $pl->title;

                    $duration = round($pl->duration / 60);

                    $chdescription = $pl->description;

                    $country = $pl->country;

                    $language = $pl->language;

                    break;

                }

            }

        }

        if (!$id) {

            $id = $video->_id;

        }

        wp_register_script('channel-video-functions', plugins_url('../js/channel.video.functions.min.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('channel-video-functions');

        wp_register_script('channel-display-functions', plugins_url('../js/channel.display.functions.min.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('channel-display-functions');

        wp_enqueue_style('video-playlist', plugins_url( 'css/video-playlist.css', __DIR__ ));

        $video_custom_css = locate_template('video.channel.customization.css');

        if (!empty($video_custom_css)) {
            wp_enqueue_style('video-custom', get_template_directory_uri() . '/video.channel.customization.css');
        } else {
            wp_enqueue_style('video-custom', plugins_url( 'css/video.channel.customization.css', __DIR__ ));
        }

        $player_url = "https://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

        $to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $chdescription, 'company' => $company, 'country' => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url);

        return $to_return;

    }

}

/**
 * Get the siblings of a child channel for displaying in templates
 *
 * @return void
 */
function get_child_siblings()
{

    if (!ds_channel_is_child()) {
        return false;
    }

    global $post;

    $parent = grab_parent_channel();

    if (!$parent) {
        return '';
    }

    $parent_slug = $parent->slug;

    $siblings = '';

    foreach ($parent->childchannels as $ch) {

        $selected = '';

        if ($ch->slug == $post->post_name) {

            $selected = "active";

        }

        $sibling_thumb = !empty($ch) && !empty($ch->playlist[0]) ? $ch->playlist[0]->thumb : "";

        $siblings .= "

        <a href='" . home_url("channels/" . $parent->slug . "/" . $ch->slug . "/") . "' class='$selected'>
            <img src='http://image.myspotlight.tv/" . $sibling_thumb . "/400/225' />
            <h3>" . $ch->title . "</h3>
        </a>";

    }

    return $siblings;

}

/**
 * Check if the front page is a channel, and nag the admin to tell them that will break the channel
 *
 * @return void
 */
function ds_is_front_page_channel()
{
    $frontpage_id = (int) get_option('page_on_front');
    // The ID will be 0 if it's not set, so we can ignore it if so
    if ($frontpage_id < 1) {
        return;
    }

    $page = get_post($frontpage_id);
    // If the page doesn't exist for whatever reason, no reason to keep going
    if (!$page) {
        return;
    }

    // If the post doesn't have a parent, it's not a channel page
    if ($page->post_parent == 0) {
        return;
    }

    $channels_check_grab = get_page_by_path('channels');
    $channels_parent     = $channels_check_grab->ID;
    $channel_grandparent = wp_get_post_parent_id($page->post_parent);
    $channel_parent      = get_post($page->post_parent);

    // If the page parent isn't the main All Channels page and isn't a channel, no need to nag
    if ($channels_parent != $channel_parent->ID && $channel_parent->post_parent != $channel_grandparent) {
        return;
    }

    ?>
    <div class="notice notice-warning">
        <p><b>dotstudioPRO Premium Video Plugin Notice:</b> It appears you've set a channel as your front page.  <b>DO NOT DO THIS!</b> This will cause that channel to not work properly.  Please change it as soon as possible to a non-channel front page. <a class='button button-primary' href='<?php echo strpos($_SERVER['REQUEST_URI'], '?') !== false ? $_SERVER['REQUEST_URI'] . '&dspdev_set_frontpage_to_category=1' : $_SERVER['REQUEST_URI'] . '?dspdev_set_frontpage_to_category=1'; ?>'>Set Front Page to Channel Categories</a></p>
    </div>
    <?php
}

/**
 * Set the front page to the channel-categories page
 *
 * @return void
 */
function ds_set_front_page_to_categories()
{
    if (empty($_GET['dspdev_set_frontpage_to_category']) || $_GET['dspdev_set_frontpage_to_category'] != 1) {
        return;
    }

    $cats = get_page_by_path('channel-categories');
    if (!$cats) {
        return;
    }

    update_option('page_on_front', $cats->ID);
    update_option('show_on_front', 'page');
    $url = str_replace('&dspdev_set_frontpage_to_category=1', '', str_replace('dspdev_set_frontpage_to_category=1', '', $_SERVER['HTTP_REFERER']));
    wp_redirect($url);
    exit;
}

/**
 * Display admin nag for not finding any channels on a resync
 *
 * @return void
 */
function dspdev_no_channels_check_nag() {
    ?>
    <div class="notice notice-warning">
        <p><b>dotstudioPRO Premium Video Plugin Notice:</b> We were unable to recreate channel pages.  It appears that no channels were returned when we requested them from our API.  Please <a href='mailto:support@dotstudiopro.com'>contact us</a> immediately.</p>
    </div>
    <?php
}

/**
 * Get the number of channel pages we have as children of the All Channels page
 *
 * @return integer The count of the child pages
 */
function dspdev_get_channel_page_children_count() {
    $channel_parent = get_page_by_path('channels');
    if(!$channel_parent) return 0;
    $args = array(
        'post_parent' => $channel_parent->ID,
        'post_type'   => 'any',
        'numberposts' => -1,
        'post_status' => 'any'
    );
    $children = get_children( $args );
    // If the children var isn't an array, we assume we have no child pages
    if(!is_array($children)) return 0;
    // We only need the count, so we return said count
    return count($children);
}
