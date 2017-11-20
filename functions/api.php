<?php

/**
 * All functions whose sole purpose is to interact with the dotstudioPRO API
 *
 */

// Set up our class to connect with the DSP API
$ds_curl = new DotStudioz_Commands;

/**
 * Nag the admin if we don't have an API key, since we need one to use the plugin
 *
 * @return void
 */
function ds_check_api_key_set()
{

    $api_key = get_option('ds_api_key');

    if ($api_key && strlen($api_key) > 0) {
        return false;
    }
    ?>
    <div class="notice notice-warning">
        <p>You need to enter your API Key in order to use its features. <a href="<?php echo home_url('wp-admin/admin.php?page=dot-studioz-options') ?>">Do so here.</a></p>
    </div>
    <?php
}

/**
 * Get a new token from the API key we have
 *
 * @return void
 */
function ds_new_token()
{
    // Acquire an API token and save it for later use.
    global $ds_curl;
    $token = $ds_curl->curl_command('token');
    update_option('ds_curl_token', $token);
    update_option('ds_curl_token_time', time());
}

/**
 * Get the current user's country based on IP
 *
 * @return void
 */
function ds_get_country()
{
    global $ds_curl;
    $country = $ds_curl->curl_command('country');
    return $country;
}

/**
 * Get a list of recommended videos from the API for displaying next to playing videos
 *
 * @param string $video_id The video id we need to base recommended videos off of
 * @param string $rec_size The number of items we want to get back
 *
 * @return void
 */
function list_recommended($video_id = '', $rec_size = 8)
{
    global $ds_curl;
    $result = $ds_curl->curl_command('recommended', array("rec_size" => $rec_size, "video_id" => $video_id));
    return $result;
}

/**
 * Get a list of all of the channels in the client's dashboard
 *
 * @return void
 */
function list_channels()
{
    global $ds_curl;
    $channels = $ds_curl->curl_command('all-channels');
    return $channels;
}

/**
 * Get a list of all of the categories in the client's dashboard
 *
 * @return void
 */
function list_categories()
{
    global $ds_curl;
    $categories          = $ds_curl->curl_command('all-categories');
    $categories_filtered = array();
    foreach ($categories as $cat) {
        if (!empty($cat->platforms) && !empty($cat->platforms[0]) && isset($cat->platforms[0]->website) && (string) $cat->platforms[0]->website === 'false' || !isset($cat->platforms[0]->website)) {
            continue;
        }
        $categories_filtered[] = $cat;
    }
    return $categories_filtered;
}

/**
 * Check if the channel has been revised at all; TODO: need to determine if this is still necessary
 *
 * @return void
 */
function channel_revision_check()
{
    // Check if we have revisions to the current channel page
    global $wpdb, $post;
    $results = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type = 'revision' AND post_parent = " . $post->ID);
    if (count($results) > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get a single channel
 *
 * @return void
 */
function grab_channel()
{
    global $ds_curl;
    $channels = $ds_curl->curl_command('single-channel');
    return $channels;
}

/**
 * Get a parent channel
 *
 * @return void
 */
function grab_parent_channel()
{
    global $ds_curl;
    $channels = $ds_curl->curl_command('parent-channel');
    return $channels;
}

/**
 * Get a category
 *
 * @param $category The slug of the category we are getting
 *
 * @return void
 */
function grab_category($category)
{
    global $ds_curl;
    $category = $ds_curl->curl_command('single-category', array("category" => $category));
    return $category;
}

/**
 * Get the information for a video
 *
 * @param $video The id of the video we are getting
 *
 * @return void
 */
function grab_video($video)
{
    global $ds_curl;
    $videoObj = $ds_curl->curl_command('play', array("video" => $video));
    return $videoObj;
}

/**
 * Check if we need to get a new token, and if we do, get one
 *
 * @return void
 */
function ds_check()
{
    global $ds_curl;
    $token      = get_option('ds_curl_token');
    $token_time = !$token ? 0 : get_option('ds_curl_token_time');
    $difference = floor((time() - $token_time) / 84600);
    if (!$token || $difference >= 25) {
        ds_new_token();
    }
}

/**
 * If the API key changes in any way, we need to delete the existing pages and grab new ones; this is a fairly intensive action once the key changes.
 *
 * @return void
 */
function ds_api_key_change()
{

    set_time_limit(120);

    global $wpdb, $ds_curl;

    // If the api key isn't posted, nothing to do here.
    if (!isset($_POST['ds_api_key'])) {

        return;

    }

    $api = get_option('ds_api_key');

    // If the api key is posted, but hasn't changed, nothing to do here.
    if ($api == $_POST['ds_api_key'] && !isset($_POST['ds_token_reset'])) {

        return;

    }

    update_option('ds_api_key', sanitize_text_field($_POST['ds_api_key'])); // Force early API key update, in case we haven't updated it yet, so we get a valid token.

    $token = $ds_curl->curl_command('token'); // Since we determined the API has changed, update token, since the new API key is being stored.

    update_option('ds_curl_token', $token);

    update_option('ds_curl_token_time', time());

    // If we have an API key change, we get to delete all of the pages we've created.
    // Please note that, because this function will be ran within ds_check(), we don't
    // need to do the re-creation.

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

}