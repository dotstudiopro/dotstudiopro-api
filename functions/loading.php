<?php

/**
 * Script and style loading/enqueing
 *
 */

/**
 * Load our various scripts and ensure that we have a version of jQuery that works with our plugin
 *
 * @return void
 */
function ds_scripts_load_cdn()
{

    global $wpdb, $post;
    // Deregister the included library
    wp_deregister_script('jquery');

    // Register the library again from Google's CDN
    wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', array(), null, false);

    // Register the script like this for a plugin:
    wp_register_script('dspdev-premvid-channel-functions', plugins_url('/../js/original/channel.functions.js', __FILE__), array('jquery'));
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script('dspdev-premvid-channel-functions');

    $channels = get_page_by_path('channels');

    $categories = get_page_by_path('channel-categories');

    $parent = get_post($post->post_parent);

    if ($post->post_parent == $categories->ID || $parent->post_parent == $channels->ID || $parent->post_parent == $categories->ID || $channels->ID == $post->ID || $categories->ID == $post->ID) {

        // Register the script like this for a plugin:
        wp_register_script('grid-script', plugins_url('js/jquery.gridder.min.js', __DIR__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('grid-script');

    }

}

/**
 * Set css to pull for the plugin based off of the style set in the DSP Options
 *
 * @return void
 */
function ds_plugin_style()
{

    // Check the style option and set up light or dark, depending
    $admin_option = get_option('ds_plugin_style');

    if (!$admin_option) {

        wp_enqueue_style(
            'ds-plugin-style',
            plugin_dir_url(__FILE__) . '../css/light-style.css'
        );

    } else {

        wp_enqueue_style(
            'ds-plugin-style',
            plugin_dir_url(__FILE__) . "../css/$admin_option.css"
        );

    }

}

/**
 * Register/enqueue all necessary styles
 *
 * @return void
 */
function ds_styles()
{

    wp_register_style('dspdev-premvid-font-awesome-style', plugins_url('/../css/font-awesome.min.css?v=1234', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('dspdev-premvid-font-awesome-style');

    wp_register_style('dspdev-premvid-animate-style', plugins_url('/../css/animate.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('dspdev-premvid-animate-style');

    wp_register_style('dspdev-premvid-grid-style', plugins_url('/../css/grid.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('dspdev-premvid-grid-style');

    wp_register_style('dspdev-premvid-style', plugins_url('/../css/style.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('dspdev-premvid-style');

    // Styles for the FancyFrame portion of this plugin:
    wp_register_style('dspdev-premvid-fancyframe-style', plugins_url('/../css/fancyframes.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('dspdev-premvid-fancyframe-style');

}
