<?php

/**
 * Template serving functions
 *
 */

/**
 * Get the template to display all categories
 *
 * @param string $single_template The template filename/location
 *
 * @return void
 */
function ds_all_categories_template($single_template)
{

    global $post;

    if ($post->post_name == 'channel-categories') {

        $single_template = locate_template('ds-all-categories.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {
            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
            $single_template = dirname(__FILE__) . '/../templates/ds-all-categories.tpl.php';
        }
    }
    // Return either the template we made, or the template in the theme folders.

    return $single_template;
}

/**
 * Get the template to display one category
 *
 * @param string $single_template The template filename/location
 *
 * @return void
 */
function ds_get_category_template($single_template)
{

    global $post;

    $category_check_grab = get_page_by_path('channel-categories');

    $category_parent = $category_check_grab->ID;

    if ($post->post_parent == $category_parent) {

        $single_template = locate_template('ds-single-category.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {

            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
            $single_template = dirname(__FILE__) . '/../templates/ds-single-category.tpl.php';

        }

    }

    // Return either the template we made, or the template in the theme folders.

    return $single_template;
}

/**
 * Get the template to display a channel
 *
 * @param string $single_template The template filename/location
 *
 * @return void
 */
function ds_get_channel_template($single_template)
{

    global $post;

    $channel_check_grab = get_page_by_path('channels');

    $channel_parent = $channel_check_grab->ID;

    $channel_grandparent = wp_get_post_parent_id($post->post_parent);

    if ($post->post_parent == $channel_parent || $channel_grandparent == $channel_parent) {

        $template_option = get_option('ds_channel_template');

        $single_template = locate_template($template_option . '.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {

            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...

            if (!$template_option || $template_option == 'default') {

                $template_option = "ds-single-channel";

            }

            if($template_option === "ds-all-categories") {
                // We should not have all-categories as a single channel template
                $template_option = "ds-single-channel";
            }

            $single_template = dirname(__FILE__) . '/../templates/channel/' . $template_option . '.tpl.php';

        }

    }

    // Return either the template we made, or the template in the theme folders.

    return $single_template;
}

/**
 * Get the home template
 *
 * @param string $single_template The template filename/location
 *
 * @return void
 */
function ds_home_template($single_template)
{

    global $post;

    if ($post->post_name == 'home') {

        $single_template = locate_template('ds-home.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {
            // If we can't locate a file named ds-home.tpl.php that should be the template file, we use our own template...
            $single_template = dirname(__FILE__) . '/../templates/ds-home.tpl.php';
        }
    }
    // Return either the template we made, or the template in the theme folders.
    return $single_template;
}
