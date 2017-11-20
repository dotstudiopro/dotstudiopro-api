<?php

/**
 * Misc functions used for various purposes
 *
 */

/**
 * Simplify the cURL execution for various API commands within the curl commands class
 *
 * @param string $curl_url The URL to do the cUrl request to
 * @param string $curl_request_type The type of request, generally POST or GET
 * @param string $curl_post_fields The fields we want to POST, if it's a POST request
 * @param object $curl_header Any necessary header values, like an API token
 *
 * @return void
 */
function ds_run_curl_command($curl_url, $curl_request_type, $curl_post_fields, $curl_header)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $curl_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => $curl_request_type,
        CURLOPT_POSTFIELDS     => $curl_request_type == 'POST' ? $curl_post_fields : "",
        CURLOPT_HTTPHEADER     => $curl_header,
    ));

    $response = curl_exec($curl);
    $err      = curl_error($curl);

    curl_close($curl);
    return (object) compact('response', 'err');
}

/**
 * Determine if a given variable value is set; used for sanity checks
 *
 * @param string $var The variable to evaluate
 *
 * @return bool|string|int|object|array|null
 */
function ds_verify_var($var)
{
    return isset($var) ? sanitize_text_field($var) : '';
}

/**
 * Determine if a given $_POST value is set; used for sanity checks
 *
 * @param string $var The variable to evaluate
 *
 * @return bool|string|int|object|array|null
 */
function ds_verify_post_var($var)
{
    return isset($_POST[$var]) ? sanitize_text_field($_POST[$var]) : '';
}

/**
 * Sets box shadows based on the plugin style given in the DSP Options
 *
 * @return void
 */
function ds_light_theme_shadows()
{

    if (get_option('ds_plugin_style') != 'light-style' || get_option('ds_light_theme_shadow') == 0) {
        return;
    }

    echo '
        <style>
            /* Box shadows for light theme. Comment these to remove on grid */
            .og-expander-inner.light-theme-shadow{
                -webkit-box-shadow: 0px 0px 10px 0px rgba(0,0,0,1);
                -moz-box-shadow: 0px 0px 10px 0px rgba(0,0,0,1);
                box-shadow: 0px 0px 10px 0px rgba(0,0,0,1);

            }

            .gridder-list.light-theme-shadow img{
                -webkit-box-shadow: 0px 0px 15px 1px rgba(0,0,0,0.75);
                -moz-box-shadow: 0px 0px 15px 1px rgba(0,0,0,0.75);
                box-shadow: 0px 0px 15px 1px rgba(0,0,0,0.75);
            }
        </style>
    ';

}

/**
 * Add the video query var to WP
 *
 * @param array $public_query_vars WP's query vars
 *
 * @return void
 */
function ds_video_var($public_query_vars)
{
    $public_query_vars[] = 'video';
    return $public_query_vars;
}

/**
 * Replace embedded dsp iframes on templates
 *
 * TODO: See if this is still necessary
 *
 * @return void
 */
function ds_iframe_replace()
{
    if (is_admin()) {
        return;
    }
    // Start output and check HTML
    ob_start('ds_iframe_html');
}

/**
 * Embedded iframe html for replace
 *
 * TODO: See if this is still necessary
 *
 * @param string $html The HTML for the current iframe we are switching out
 *
 * @return void
 */
function ds_iframe_html($html)
{
    // Replace <iframe> code with a div that loads the iframe based on scroll.
    $iframe_split = explode('<iframe', $html);
    foreach ($iframe_split as $if) {
        $split_one = explode('</iframe>', $if);
        $split_two = $split_one[0];
        $params    = explode(' ', $split_two);
        $source    = '';
        foreach ($params as $param) {
            if (strpos($param, 'src') !== false && strpos($split_two, 'nofancyframe') === false) {
                $source_split1 = explode('src="', str_replace("'", '"', $param));
                $source_split2 = explode('"', $source_split1[1]);
                $source        = $source_split2[0];
                if (strpos($source, "dotstudiopro") !== false || strpos($source, 'dotstudiodev') !== false) {
                    $video_explode1 = explode("/player/", $source);
                    $video_explode2 = explode("?", $video_explode1[1]);
                    $video          = $video_explode2[0];
                    $videoObj       = grab_video($video);
                    $posterImg      = $videoObj->thumb . "/1000/562";
                    $rndID          = generateRandomString(5);
                    $strOut         = '';
                    $strOut .= '<div id="' . $rndID . '_container" class="iframe_container" data-vidurl="' . $source . '" data-isplaying="0">';
                    $strOut .= '<a href="#' . $rndID . '" id="' . $rndID . '_link" class="iframe_launch"><i class="iframe_fa fa fa-play-circle-o"></i><img class="iframe_thumb" id="' . $rndID . '_thumb" src="' . $posterImg . '" /></a>';
                    $strOut .= '<div id="' . $rndID . '_spinner" class="iframe_spinner_container" style="display:none;"><div class="iframe_spinner"></div></div>';
                    $iframe = '<iframe' . $split_two . '</iframe>';
                    $html   = str_replace($iframe, $strOut, $html);
                }
            }
        }
    }
    return $html;
}

/**
 * Generate a random string for various purposes
 *
 * @param int $length The length of the random string
 *
 * @return string
 */
function generateRandomString($length = 5)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

/**
 * Nag the admin if we can't get a country
 *
 * This generally either means that they need to set up a development mode environment for US, or the API key is bad
 * @return void
 */
function ds_no_country()
{
    $country = ds_get_country();
    if ($country) {
        return;
    }
    ?>
    <div class="notice notice-warning">
        <p>Please check your dotstudioPRO API key.  We cannot determine a country for your server using our geolocation server.  If you are in a local development environment, please set the development mode option and country in the dotstudioPRO Premium Video Options.  If you are not, please contact us.</p>
    </div>
    <?php
}

/**
 * Get the video info we are supposed to display on a template
 *
 * @return void
 */
function ds_headliner_video_for_template()
{
    if (!ds_channel_is_parent() && !ds_channel_is_child()) {
        echo get_query_var("video", false) ? channel_selected_video() : channel_first_video();
    } else if (ds_channel_is_child()) {
        echo get_query_var("video", false) ? child_channel_selected_video() : child_channel_first_video();
    }
}

/**
 * Write the proper meta tags to the header
 *
 * This is done so we don't have to depend on plugins or custom implementations to give us the right meta info
 *
 * @return void
 */
function ds_meta_tags()
{

    global $channel;

    if (!$channel) {
        return;
    }

    $meta = $channel['for_meta'];

    $description = str_replace('"', "'", $meta->description);

    if ($meta->channel_title == $meta->title) {

        $name_site = $meta->title . " - " . get_bloginfo('name');

        $name = $meta->title;

    } else {

        $name_site = $meta->title . " - " . $meta->channel_title . " - " . get_bloginfo('name');

        $name = $meta->title . " - " . $meta->channel_title;

    }

    ?><meta name="description" content="<?php echo $description; ?>">
    <meta property="fb:app_id" content="<?php echo get_option('ds_fb_app_id'); ?>" >
    <!-- OG meta --><meta property="og:site_name" content="<?php echo $name_site; ?>">
    <meta property="og:description" content="<?php echo $description; ?>">
    <meta property="og:type" content="website" >
    <meta property="og:url" content="<?php echo $meta->url; ?>">
    <meta property="og:title" content="<?php echo $name; ?>" >
    <meta property="og:image" content="<?php echo $meta->image_id; ?>">
    <meta property="og:image:width" content="640" >
    <meta property="og:image:height" content="360" >
    <!-- Twitter Summay Card -->
    <meta name="twitter:card" content="summary_large_image" >
    <meta name="twitter:title" content="<?php echo $name; ?>">
    <meta name="twitter:site" content="<?php echo get_option('ds_twitter_handle'); ?>">
    <meta name="twitter:creator" content="<?php echo get_option('ds_twitter_handle'); ?>">
    <meta name="twitter:description" content="<?php echo $description; ?>">
    <meta name="twitter:image" content="<?php echo $meta->image_id; ?>/640/360"><?php

}

/**
 * Output FB comment code for templates
 *
 * @return void
 */
function ds_template_fb_code()
{

    ?>
    <div id="ds-comments">
       <div class="fb-comments" data-colorscheme="dark" data-href="<?php echo home_url(); ?>" data-width="100%" data-numposts="5"></div>
        </div>

      <div id="fb-root"></div>
      <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=243289792365862";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>

    <?php

}

/**
 * Determine if we have all of the template files copied over to the theme
 *
 * Check if our various templates exist to determine if we need to prompt the admin to copy the templates to the currently active
 *
 * @return void
 */
function ds_templates_exist()
{
    $templates = array("ds-all-categories.tpl.php",
        "ds-single-category.tpl.php",
        "ds-home.tpl.php",
        "ds-single-channel.tpl.php",
        "ds-single-channel-w-sidebar.tpl.php",
        "video.channel.customization.css",
        "ds-sharing.php");

    foreach ($templates as $t) {
        $file_path = get_stylesheet_directory() . '/' . $t;
        if (!file_exists($file_path)) {
            return false;
        }
    }
    return true;
}

/**
 * Copy plugin templates to the currently active theme
 *
 * Copy the page templates to the current active theme directory for manipulation by the admin without having to edit our specific template files
 *
 * @return void
 */
function ds_template_copy()
{

    $error                    = "";
    $templates                = array("ds-all-categories.tpl.php", "ds-single-category.tpl.php", "ds-home.tpl.php");
    $single_channel_templates = array("ds-single-channel.tpl.php", "ds-single-channel-w-sidebar.tpl.php");

    foreach ($templates as $t) {
        $plugin_dir = plugin_dir_path(__FILE__) . '../templates/' . $t;
        $theme_dir  = get_stylesheet_directory() . '/' . $t;

        if (!copy($plugin_dir, $theme_dir)) {
            $error = "&error=1";
        }
    }

    foreach ($single_channel_templates as $t) {
        $plugin_dir = plugin_dir_path(__FILE__) . '../templates/' . $t;
        $theme_dir  = get_stylesheet_directory() . '/' . $t;

        if (!copy($plugin_dir, $theme_dir)) {
            $error = "&error=1";
        }
    }

    $plugin_dir = plugin_dir_path(__FILE__) . '../templates/components/sharing.php';
    $theme_dir  = get_stylesheet_directory() . '/ds-sharing.php';

    if (!copy($plugin_dir, $theme_dir)) {
        $error = "&error=1";
    }

    $plugin_dir = plugin_dir_path(__FILE__) . '../css/video.channel.customization.css';
    $theme_dir  = get_stylesheet_directory() . '/video.channel.customization.css';

    if (!copy($plugin_dir, $theme_dir)) {
        $error = "&error=1";
    }

    wp_redirect(site_url() . "/wp-admin/admin.php?page=dot-studioz-options$error");

}

/**
 * Stub in the custom CSS from the plugin template menu
 *
 * @return void
 */
function ds_add_custom_css()
{
    echo "\n<style>" . get_option('ds_plugin_custom_css') . "</style>\n\n";

}

/**
 * Set up wp post form to upload files/images/etc
 *
 * @return void
 */
function add_post_enctype()
{
    echo ' enctype="multipart/form-data"';
}