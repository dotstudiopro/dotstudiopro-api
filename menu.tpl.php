<?php
   if(isset($_GET['ds-admin-action']) && $_GET['ds-admin-action'] == 'flush'){

   	ds_check();

   }

   $colors = array('blue', 'pink', 'purple', 'orange', 'red', 'yellow', 'green');

   $current_slider_color = get_option('ds_player_slider_color');

   $styles = array('light-style', 'dark-style');

   $autoplay = get_option('ds_player_autoplay');

   $template_files = scandir(dirname( __FILE__ ) . '/templates/channel/');

   foreach($template_files as $template) {
    if(strpos($template, '.php') !== false) $templates[] = $template;
   }

   $current_style = get_option('ds_plugin_style');

   $current_template = get_option('ds_channel_template');

   $custom_css = get_option('ds_plugin_custom_css');

   $selector_colors = $template_styles = $template_list = '';

   foreach($colors as $v){

   	if($v == $current_slider_color){

   		$selector_colors .= "<option value='$v' selected='selected'>".ucfirst($v)."</option>";

   	} else {

   		$selector_colors .= "<option value='$v'>".ucfirst($v)."</option>";

   	}

   }

   foreach($styles as $v){

   	$exp = explode("-", $v);

   	if($v == $current_style){

   		$template_styles .= "<option value='$v' selected='selected'>".ucfirst($exp[0])."</option>";

   	} else {

   		$template_styles .= "<option value='$v'>".ucfirst($exp[0])."</option>";

   	}

   }

   foreach($templates as $v){

   	$v = str_replace(".tpl.php", "", $v);

   	$name = ucwords(str_replace("-", " ", str_replace("ds-", "", $v)));

   	if($v == $current_template){

   		$template_list .= "<option value='$v' selected='selected'>".$name."</option>";

   	} else {

   		$template_list .= "<option value='$v'>".$name."</option>";

   	}

   }

   /** Fancy load **/

   $fancy_load_option = get_option("ds_fancy_load");

   $sel_yes = $fancy_load_option ? 'selected' : '';
   $sel_no = !$fancy_load_option ? 'selected' : '';
   $fancy_load = "<option value='1' $sel_yes>Yes</option><option value='0' $sel_no>No</option>";

   /** End Fancy **/

   $resynced = isset($_GET['resynced'])  && $_GET['resynced'] == 1;

   $channels = dspdev_get_channel_page_children_count();

   if ($resynced && empty($channels)) {
      dspdev_no_channels_check_nag();
   }

   ?>
<div class='container'>
   <h2>dotstudioPRO Plugin Options</h2>
   <div id='menu-tabs'>
      <div class='menu-tab admin-tab-active'>
         <span data-content='tab-0'>Plugin Configuration</span>
      </div>
      <div class='menu-tab'>
         <span data-content='tab-1'>Carousel Shortcode Generator</span>
      </div>
   </div>
   <div id='tab-0' class='tab-content'>
      <form action='' method='POST' enctype='multipart/form-data'>
         <table class='form-table widefat'>
            <thead>
            </thead>
            <tbody>
               <tr>
                  <td colspan=2><b>Please make sure to set as many of these options as you can to ensure a good user experience.</b><br/></td>
               </tr>
               <tr>
                  <td>dotstudioPRO Account Dashboard<br/><span class='description'>Opens in a new window</span></td>
                  <td><a class="button" href="https://www.dotstudiopro.com/user/login" target="_blank">LOGIN</a></td>
               </tr>
               <tr>
                  <td>dotstudioPRO API Key<br/><span class='description'>Don't have an API Key? <a href="https://beta.dotstudiopro.com/user/register" target="_blank">Click Here.</a></span></td>
                  <td><input type='text' name='ds_api_key' value='<?php echo get_option('ds_api_key') ?>' /></td>
               </tr>
               <tr>
                  <td>Re-sync and Rebuild<br/><span class='description'>Resync video, channel and category data from the dotstudioPRO dashboard</span></td>
                  <td><a class='button' href='<?php echo site_url().'/wp-admin/admin.php?page=dot-studioz-options&flush=1'; ?>'>RE-SYNC</a></td>
               </tr>
               <tr>
                  <td>Facebook App ID<br/><span class='description'>For Facebook sharing and commenting to work properly you need a Facebook App Id. Don't have one? <a href="http://dotstudiopro.com" target="_blank">Click Here.</a></span></td>
                  <td><input type='text' name='ds_fb_app_id' value='<?php echo get_option('ds_fb_app_id') ?>' /></td>
               </tr>
               <tr>
                  <td>Twitter Handle</td>
                  <td><input type='text' name='ds_twitter_handle' value='<?php echo get_option('ds_twitter_handle') ?>' /></td>
               </tr>
               <tr>
                  <td>Comment Type<br/><span class='description'>Select which comment type will show on pages created by the plugin.</span></td>
                  <td>
                     <select name='ds_comment_type'>
                        <option value='facebook'>Facebook</option>
                        <option value='wordpress'>Wordpress</option>
                        <option value='none'>None</option>
                     </select>
                  </td>
               </tr>
               <tr>
                  <td>Player Slider Color</td>
                  <td><select name='ds_player_slider_color'><?php echo $selector_colors ?></select></td>
               </tr>
               <tr>
                  <td>Template</td>
                  <td><select name='ds_channel_template'><?php echo $template_list ?></select></td>
               </tr>
               <tr>
                  <td>Template Color Style</td>
                  <td><select name='ds_plugin_style'><?php echo $template_styles ?></select></td>
               </tr>
               <tr>
                  <td>Light Theme Shadow</td>
                  <td>
                     <select name='ds_light_theme_shadow'>
                        <option value='1' <?php echo get_option('ds_light_theme_shadow') == 1 ? 'selected="selected"' : ''; ?>>On</option>
                        <option value='0' <?php echo get_option('ds_light_theme_shadow') == 0 ? 'selected="selected"' : ''; ?>>Off</option>
                     </select>
                  </td>
               </tr>
               <tr>
                  <td>Use FancyLoad Iframe Loader</td>
                  <td><select name='ds_fancy_load'><?php echo $fancy_load ?></select></td>
               </tr>
               <tr>
                  <td>Autoplay Video On Page Load<br/><span class='description'>Check to autoplay video on channel page</span></td>
                  <td><input type='checkbox' name='ds_player_autoplay' value='1' <?php echo get_option("ds_player_autoplay") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Mute Audio On Page Load<br/><span class='description'>Check to mute audio when autoplay is enabled and videos load</span></td>
                  <td><input type='checkbox' name='ds_player_mute' value='1' <?php echo get_option("ds_player_mute") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Auto-Redirect Page On Video End<br/><span class='description'>Check to automatically redirect the page to the next video in the channel collection</span></td>
                  <td><input type='checkbox' name='ds_player_autoredir' value='1' <?php echo get_option("ds_player_autoredir") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Theater Mode Playlist Location<br/><span class='description'>Check to display above video meta information.  Leave unchecked to display below video</span></td>
                  <td><input type='checkbox' name='ds_show_playlist_above_meta' value='1' <?php echo get_option("ds_show_playlist_above_meta") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Minified Video On Vertical Scroll<br/><span class='description'>Check to minify channel video to the right side of the screen when scrolling down</span></td>
                  <td><input type='checkbox' name='ds_player_minivid' value='1' <?php echo get_option("ds_player_minivid") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Enable Recommended Videos Playlist<br/><span class='description'>Will display the recommended videos playlist when enabled</span></td>
                  <td><input type='checkbox' name='ds_player_recplaylist' value='1' <?php echo get_option("ds_player_recplaylist") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Auto-assign 'Browse' Menu on Flush<br/><span class='description'>Set the 'Browse Channel Categories' menu as the main nav on flush.</span></td>
                  <td><input type='checkbox' name='ds_auto_assign_menu' value='1' <?php echo get_option("ds_auto_assign_menu") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr <?php if(ds_templates_exist()) { ?>style='background-color:yellow;' <?php } ?>>
                  <td>Copy plugin template files to my theme folder <?php if(ds_templates_exist()) { ?>(USE WITH CAUTION)<?php } ?></b><br/><span class='description'>For custom template changes.</span></td>
                  <td><a class='button <?php if(ds_templates_exist()) { ?>warn-templates-exist <?php } ?>'  data-href='<?php echo site_url().'/wp-admin/admin.php?page=dot-studioz-options&templatecopy=1'; ?>'>Copy</a></td>
               </tr>
               <tr>
                  <td>Custom CSS</td>
                  <td><textarea name='ds_plugin_custom_css' class='widefat' rows='10'><?php echo $custom_css ?></textarea></td>
               </tr>
               <tr>
                  <td colspan=2><b>Development Options</b><br/><span class='description'>Please note: any options set here will override normal settings.  Please make sure to turn these settings off when you are done testing.</span></td>
               </tr>
               <tr>
                  <td>Development Mode</td>
                  <td><input type='checkbox' name='ds_development_check' value='1' <?php echo get_option("ds_development_check") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <tr>
                  <td>Development Country (Abbreviation)</td>
                  <td><input type='text' name='ds_development_country' value='<?php echo get_option("ds_development_country") ?>' /></td>
               </tr>
               <tr>
                  <td>Reset Token on Save<br><span class='description'>Use in case you believe you have issues with token authentication.</span></td>
                  <td><input type='checkbox' name='ds_token_reset' value='1' <?php echo get_option("ds_token_reset") == 1 ? 'checked="checked"' : '' ?> /></td>
               </tr>
               <input type='hidden' name='ds-save-admin-options' value='1' />
            </tbody>
            <tfoot>
               <tr>
                  <td colspan=2><button>Save</button></td>
               </tr>
            </tfoot>
         </table>
      </form>
   </div>
   <div id='tab-1' style='display:none;' class='tab-content'>
      <table class='ds-carousel-admin form-table widefat'>
         <tbody>
            <tr>
               <td colspan="2" style='text-align:left;'>
                  <b>Select the options below to build a new carousel shortcode.</b>
               </td>
            </tr>
            <tr>
               <td><strong>Select your carousel type:</strong></td>
               <td>
                  <select id="carousel-type">
                     <option value="channels">Channels Carousel</option>
                     <option value="category">Category Carousel</opion>
                  </select>
               </td>
            </tr>
            <tr>
               <td>
                  <strong>
                     Select your
                     <lable id="channel-or-cat">
                     channels</label>:
                  </strong>
               </td>
               <td>
                  <div  id="channels-carousel-list" class="carousel-list">
                     <?php echo ds_owl_carousel_local_channels_list(); ?>
                  </div>
                  <div id="category-carousel-list" class="carousel-list" style="display:none;">
                     <?php
                        $catList = list_categories();
                        //var_dump($catList);
                        foreach($catList as $cat) {
                        		$catName = $cat -> name;
                        		$catSlug = $cat -> slug;
                        		$strOut = '<input type="radio" name="category" id="category-'.$catName.'" value="'.$catSlug.'">';
                        		$strOut .= '<label for="category-'.$catName.'">'.$catName.'</label><br />';
                        		echo $strOut;
                        }
                        ?>
                  </div>
               </td>
            </tr>
            <tr>
               <td>
                  <strong>Carousel Options:</strong>
               </td>
               <td>
                  <table class='carousel-opts'>
                     <tr>
                        <td>Transition Time:</td>
                        <td><select id="opts-autoplayTimeout" class="opts-select opt-change">
                           <?php
                              for($i = 1; $i <= 10; $i++) {
                              	$s = $i != 1 ? 's':'';
                              	$sel = $i == 3 ? " selected" : "";
                              	echo '<option value="' . $i*1000 .'"'. $sel .'>' . $i . ' second'.$s.'</option>';
                              }
                              ?>
                           </select>
                        </td>
                     </tr>
                     <tr>
                        <td>Transition Speed:</td>
                        <td><select id="opts-autoplaySpeed" class="opts-select opt-change">
                           <?php
                              for($i = 1; $i <= 5; $i++) {
                              	$s = $i != 1 ? 's':'';
                              	$sel = $i == 1 ? " selected" : "";
                              	echo '<option value="' . $i*1000 .'"'. $sel .'>' . $i . ' second'.$s.'</option>';
                              }
                              ?>
                           </select>
                        </td>
                     </tr>
                     <tr>
                        <td>Slides to Show:</td>
                        <td><select id="opts-items" class="opts-select opt-change">
                           <?php
                              for($i = 1; $i <= 8; $i++) {
                              	$s = $i != 1 ? 's':'';
                              	$sel = $i == 3 ? " selected" : "";
                              	echo '<option value="' . $i . '"'. $sel .'>' . $i . ' slide'.$s.'</option>';
                              }
                              ?>
                           </select>
                        </td>
                     </tr>
                     <tr>
                        <td>Autoplay Direction:</td>
                        <td>
                           <select id="opts-rtl" class="opts-select opt-change">
                              <option value="0">Left To Right</option>
                              <option value="1">Right To Left</option>
                           </select>
                        </td>
                     </tr>
                     <tr>
                        <td>Slides per Transition:</td>
                        <td><select id="opts-slide-by" class="opts-select opt-change">
                           <?php
                              for($i = 1; $i <= 10; $i++) {
                              	$s = $i != 1 ? 's':'';
                              	echo '<option value="' . $i . '">' . $i . ' slide'.$s.'</option>';
                              }
                              ?>
                           </select>
                        </td>
                     </tr>
                     <tr>
                        <td>Autoplay:</td>
                        <td><input type="checkbox" id="opts-autoplay" checked class="opt-change"></td>
                     </tr>
                     <tr>
                        <td>Pause On Hover:</td>
                        <td><input type="checkbox" id="opts-autoplayHoverPause" checked class="opt-change"></td>
                     </tr>
                     <tr>
                        <td>Navigation Dots:</td>
                        <td><input type="checkbox" id="opts-dots" class="opt-change"></td>
                     </tr>
                     <tr>
                        <td>Navigation Buttons:</td>
                        <td><input type="checkbox" id="opts-nav" class="opt-change"></td>
                     </tr>
                     <tr>
                        <td>No Title:</td>
                        <td><input type="checkbox" id="opts-notitle" class="opt-change"></td>
                     </tr>
                     <tr>
                        <td>Animation In:</td>
                        <td><?php echo ds_owl_admin_animation_select('opts-animate-in','animate-type opt-change'); ?></td>
                     </tr>
                     <tr>
                        <td>Animation Out:</td>
                        <td><?php echo ds_owl_admin_animation_select('opts-animate-out','animate-type opt-change'); ?></td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
            <tr>
               <td><strong>Carousel Title:</strong><br />(leave blank for default)</td>
               <td><input type="text" id="title" value="" class="textinput" style="width:300px;"/>
            </tr>
            <tr>
               <td><strong>Title CSS Class:</strong><br />(leave blank for none)</td>
               <td><input type="text" id="titleclass" class="textinput" value="" style="width:300px;"/>
            </tr>
      </table>
      <div id="ds-shortcode">
         <label>Copy+paste generated shortcode below:</label><br />
         <textarea id="ds-carousel-built-shortcode" readonly></textarea>
      </div>
   </div>
</div>