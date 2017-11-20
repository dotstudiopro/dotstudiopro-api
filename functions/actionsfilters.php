<?php

/**
 * All of the actions and filters used within the plugin.
 *
 */

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['flush']) && $_GET['flush'] == 1) {
    add_action('init', 'ds_site_flush');
}

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['templatecopy']) && $_GET['templatecopy'] == 1) {
    add_action('init', 'ds_template_copy');
}

add_filter('query_vars', 'ds_video_var');
add_filter('page_template', 'ds_get_channel_template', 11);
add_filter('page_template', 'ds_get_category_template', 11);
add_filter('page_template', 'ds_all_categories_template', 11);

add_action('admin_notices', 'ds_no_country');
add_action('admin_notices', 'ds_is_front_page_channel');
add_action('wp_enqueue_scripts', 'ds_scripts_load_cdn');
add_action('wp_head', 'ds_light_theme_shadows', 990);
add_action('wp_enqueue_scripts', 'ds_styles');
add_action('admin_notices', 'ds_check_api_key_set');
add_action('init', 'ds_get_country');
add_action('wp', 'ds_iframe_replace');
add_action('init', 'ds_create_channel_category_menu');
add_action('wp_head', 'ds_add_custom_css', 999);
add_action('post_edit_form_tag', 'add_post_enctype');
add_action('admin_init', 'ds_category_images_init');
add_action('save_post', 'ds_save_category_image_field');
add_action('admin_init', 'ds_set_front_page_to_categories');
