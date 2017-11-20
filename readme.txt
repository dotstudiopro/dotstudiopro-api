=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Tags: streaming video, Netflix, Hulu, video monetization, monetized video,Apple TV, Roku, Mobile, Facebook
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

dotstudioPRO is a video monetization CMS used to manage, deploy, and monetize streaming video on devices like Apple TV, Roku, Mobile, Facebook and browsers. This plugin extends dotstudioPRO functionality into Wordpress turning it into a Netflix or Hulu style website.

== Description ==

dotstudioPRO is a video monetization CMS used to manage, deploy, and monetize streaming video on devices like Apple TV, Roku, Mobile, Facebook and browsers.
This plugin extends dotstudioPRO functionality into Wordpress turning it into a Netflix or Hulu style website.  In addition to serving streaming video, the 
dotstudioPRO Wordpress plugin also provides an attractive, highly configurable carousel-style shortcode that can be used throughout your site which allows you 
to display either channel lists or category lists as clickable thumbnails.  Shortcode can be inserted into any page or post, and is easily generated from the 
dotstudioPRO Wordpress carousel shortcode generator.

== Installation ==

[WordPress](http://wordpress.org/ "Your favorite software")

1. Create a [dotstudioPRO account](http://dotstudiopro.com/ "Click here to create or access your dotstudioPRO account")
2. Once logged in, go to the **User Account** section and get your **API key**
3. Install dotstudioPRO Wordpress Plugin from your Wordpress dashboard or unzip the plugin archive in the `wp-content/plugins` directory.
4. Activate the plugin through the *Plugins* menu in Wordpress
5. Go to the **dotstudioPRO** left menu configuration page and fill in your **API key**
7. Go to Appearance->Menu and use the autocreated menu as you wish
8. To customize you can copy the plugin templates into your active Wordpress Template
7. Click the "Flush" button to reseed the data

== Frequently Asked Questions ==

= Do I need a dotstudioPRO account to run this plugin? =

Yes. The dotstudioPRO Wordpress plugin is designed specifically to work with your dotstudioPRO dashboard. The data 
that will be displayed is fed from videos you upload and manage from the dotstudioPRO dashboard.You will need the 
API key furnished to you from the dotstudioPRO dashboard, as well as to be able to manage your videos, playlists, 
paywalls, advertising and other features.

= Are there any other plugins I need to run dotstudioPRO for Wordpress? =

You will need to install the "Featured Video Plus" Wordpress plugin by Alexander Höreth.  This plugin allows you to 
insert your videos from the dotstudioPRO into specific pages.

= What is the FancyLoad iFrame Loader? =

The FancyLoad iFrame loader is a feature that allows a type of lazyload and prerendering display on your videos.  This feature 
may not be desirable all the time, and can be enabled and disable globally or disabled for specific videos.

= How do I turn off FancyLoad for individual video entries? =

When inserting the video IFRAME code, simply put **class="nofancyframe"** into the iframe code and that particular video will not
display in a FancyFrame display.

= How do I create a video carousel rail? =

1. Select dotstudioPRO Options from the Admin menu on the left.
2. Select the Carousel Shortcode Generator from the tab at the top
3. Create the shortcode for your category or channel rail by completing the form provided.
4. As you complete the form, the shortcode for the rail will be displayed on the box on the right.
5. Once you are done selecting all the options for your video rail, COPY the code from the shortcode box
6. PASTE the shortcode into any page, post or directly into the some other HTML page you want to display

= Is there a limit to how many carousel rails can I have in a page? ==

Technically, no, but as with anything that has alot of images, the more you put on, the longer your page load.

= What is the best way to customize the video channel and category pages? =

Customization without modifying the original plugin can be achieved by copying the template files from the plugin folder
to your active theme folder.  This can be done by going to the dotstudioPRO Options and clicking the "COPY" button towards
the bottom of the Plugin Configuration tab.  Once you have copied these files into your active theme folder this way, you
can manipulate the styles and layouts any way you wish.  The files that are copied over are:

1. ds-all-categories.tpl.php
2. ds-home.tpl.php
3. ds-single-category.tpl.php
4. ds-single-channel-w-sidebar.tpl.php
5. ds-single-channel.tpl.php
6. video-channel.customization.css


= Where do I create video channels and categories? =
Video channels and categories are created through the dotstudioPRO dashboard.  Whenever you change any of your videos, 
channels or categories, you'll need to update the data on your website.  To re-sync and rebuild the categories and channels
on your site after you have modified them through the dashboard, click the "RE-SYNC" button in the dotstudioPRO Plugin 
Configuration area.

== Screenshots ==

1. dotstudioPRO setup & configuration options
2. Channel and category carousel rail shortcode generator
3. Example channel and category carousel rails
4. Channel Categories and Single Category Pages
5. Single Channel Page, Standard View Mode
6. Single Chanel Page, Theater View Mode
7. DotstudioPRO Dashboard video management 
8. DotstudioPRO Dashboard, main video and channel console
8. DotstudioPRO Status and summary console

