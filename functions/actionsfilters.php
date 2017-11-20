<?php

/**
 * All of the actions and filters used within the plugin.
 *
 */

add_action('admin_notices', 'dspdev_api_no_country');
add_action('admin_notices', 'dspdev_api_check_api_key_set');
add_action('init', 'dspdev_api_get_country');
