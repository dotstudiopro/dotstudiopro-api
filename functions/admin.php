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
function ds_save_admin_options()
{

    if (isset($_POST['ds-save-admin-options'])) {

        ds_api_key_change();

        update_option('ds_fb_app_id', ds_verify_post_var('ds_fb_app_id'));

        update_option('ds_twitter_handle', ds_verify_post_var('ds_twitter_handle'));

        update_option('ds_player_slider_color', ds_verify_post_var('ds_player_slider_color'));

        update_option('ds_plugin_style', ds_verify_post_var('ds_plugin_style'));

        update_option('ds_light_theme_shadow', ds_verify_post_var('ds_light_theme_shadow'));

        update_option('ds_channel_template', ds_verify_post_var('ds_channel_template'));

        update_option('ds_development_check', ds_verify_post_var('ds_development_check'));

        update_option('ds_development_country', ds_verify_post_var('ds_development_country'));

        update_option('ds_plugin_custom_css', ds_verify_post_var('ds_plugin_custom_css'));

        update_option('ds_comment_type', ds_verify_post_var('ds_comment_type'));

        update_option('ds_player_mute', ds_verify_post_var('ds_player_mute'));

        update_option('ds_token_reset', ds_verify_post_var('ds_token_reset'));

        update_option('ds_auto_assign_menu', ds_verify_post_var('ds_auto_assign_menu'));

        update_option('ds_player_autoplay', ds_verify_post_var('ds_player_autoplay'));

        update_option('ds_player_autoredir', ds_verify_post_var('ds_player_autoredir'));

        update_option('ds_player_minivid', ds_verify_post_var('ds_player_minivid'));

        update_option('ds_player_recplaylist', ds_verify_post_var('ds_player_recplaylist'));

        update_option('ds_fancy_load', ds_verify_post_var('ds_fancy_load'));

        update_option('ds_show_playlist_above_meta', ds_verify_post_var('ds_show_playlist_above_meta'));

    }

}
