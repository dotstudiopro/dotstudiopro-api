<?php

/**
 * All functions whose sole purpose is to interact with the dotstudioPRO API
 *
 */

// Set up our class to connect with the DSP API
$dspdev_api_class = new dotstudioPRO_API();
// Check to make sure we have a current token
$token = dspdev_api_check();
$dspdev_api_class->set_token($token);
// Make sure we have a country for any API calls to channels
$dspdev_api_class->get_country();

/**
 * Check if we have a token and if it is expired, and get a new one if expired or missing
 *
 * @return String|Bool The access token or false if something went wrong
 */
function dspdev_api_check()
{
    $token = get_option('dspdev_api_token');
    $token_time = !$token ? 0 : get_option('dspdev_api_token_time');
    $difference = floor((time() - $token_time) / 84600);
    if (!$token || $difference >= 25) {
        $token = dspdev_api_new_token();
        if(empty($token)) return false;
    }
    return $token;
}

/**
 * Nag the admin if we don't have an API key, since we need one to use the plugin
 *
 * @return void
 */
function dspdev_api_check_api_key_set()
{

    $api_key = get_option('dspdev_api_key');

    if ($api_key && strlen($api_key) > 0) return false;

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
function dspdev_api_new_token()
{
    // Acquire an API token and save it for later use.
    global $dspdev_api_class;
    $token = $dspdev_api_class->get_token();
    update_option('dspdev_api_token', $token);
    update_option('dspdev_api_token_time', time());
    return $token;
}

/**
 * Get the current user's country based on IP
 *
 * @return void
 */
function dspdev_api_get_country()
{
    global $dspdev_api_class;
    $country = $dspdev_api_class->get_country();
    return $country;
}

/**
 * Simplify the cURL execution for various API commands within the curl commands class
 *
 * @param string $curl_url The URL to do the cUrl request to
 * @param string $curl_request_type The type of request, generally POST or GET
 * @param string $curl_post_fields The fields we want to POST, if it's a POST request
 * @param object $curl_header Any necessary header values, like an API token
 *
 * @return Object The curl response object
 */
function dspdev_api_run_curl_command($curl_url, $curl_request_type, $curl_post_fields, $curl_header)
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
 * Determine if a given $_POST value is set; used for sanity checks
 *
 * @param string $var The variable to evaluate
 *
 * @return bool|string|int|object|array|null
 */
function dspdev_api_verify_post_var($var)
{
    return isset($_POST[$var]) ? sanitize_text_field($_POST[$var]) : '';
}

/**
 * Nag the admin if we can't get a country
 *
 * This generally either means that they need to set up a development mode environment for US, or the API key is bad
 * @return void
 */
function dspdev_api_no_country()
{
    $country = dspdev_api_get_country();
    if ($country) {
        return;
    }
    ?>
    <div class="notice notice-warning">
        <p>Please check your dotstudioPRO API key.  We cannot determine a country for your server using our geolocation server.  If you are in a local development environment, please set the development mode option and country in the dotstudioPRO Premium Video Options.  If you are not, please contact us.</p>
    </div>
    <?php
}

