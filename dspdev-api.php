<?php

/*
Plugin Name: dotStudioPRO API Connector
Plugin URI: #
Description: This plugin provides a connector class to the dotstudioPRO API for use in plugin/theme development
Version: 1.00
Author: Matt Armstrong, dotstudioPRO
Text Domain: dspdev-api
Author URI: http://www.dotstudiopro.com
License: GPLv3
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016-2017 dotStudioPRO
*/

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if (!class_exists('dotstudioPRO_API')) {
    require_once "class.dspdev_api_commands.php";
}

require_once "functions.php";

// Plugin Update Checker
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'http://updates.wordpress.dotstudiopro.com/wp-update-server/?action=get_metadata&slug=dspdev-premium-video',
    __FILE__,
    'dspdev-api'
);

/** Add Menu Entry **/
function dspdev_api_menu()
{

    add_menu_page('dotstudioPRO API Options', 'dotstudioPRO API Options', 'manage_options', 'dspdev-api-options', 'dspdev_api_menu_page', plugins_url( 'images/dsp.png', __FILE__ ));

}

add_action('admin_menu', 'dspdev_api_menu');

// Set up the page for the plugin, pulling the content based on various $_GET global variable contents
function dspdev_api_menu_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    echo "<div class='wrap'>";

    include "menu.tpl.php";

    echo "</div>";

}
/** End Menu Entry **/

/** Save Admin Menu Options **/

add_action("init", "dspdev_api_save_admin_options");

/** End Save Admin Menu Options **/
