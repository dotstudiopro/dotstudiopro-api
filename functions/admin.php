<?php

/**
 * Admin-related functions; currently only the option save
 *
 */

 /**
  * Save the various admin options so we can use them within the plugin
  *
  * @return void
  */
function dspdev_api_save_admin_options()
{

    if (isset($_POST['dspdev-api-save-admin-options'])) {

        dspdev_api_key_change();

        update_option('dspdev_api_key', dspdev_api_verify_post_var('dspdev_api_key'));

        update_option('dspdev_api_development_check', dspdev_api_verify_post_var('dspdev_api_development_check'));

        update_option('dspdev_api_development_country', dspdev_api_verify_post_var('dspdev_api_development_country'));

        update_option('dspdev_api_token_reset', dspdev_api_verify_post_var('dspdev_api_token_reset'));


    }

}

/**
 * If the API key changes in any way, we need to delete the existing pages and grab new ones; this is a fairly intensive action once the key changes.
 *
 * @return void
 */
function dspdev_api_key_change()
{
    // If the api key isn't posted, nothing to do here.
    if (!isset($_POST['dspdev_api_key'])) return;

    $api = get_option('dspdev_api_key');

    // If the api key is posted, but hasn't changed, nothing to do here.
    if ($api == $_POST['dspdev_api_key'] && !isset($_POST['dspdev_token_reset'])) return;

    dspdev_api_new_token();
}
