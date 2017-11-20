<?php

ds_is_channel_parent_check();

/*********************************************************/

/** MUST BE CALLED BEFORE HEADER FUNCTION! **/

$channel = igrab_channel();

$siblings = get_child_siblings();

$category = get_query_var("channel_category", FALSE);
remove_action( 'wp_head' , 'swp_add_header_meta' , 1 );
add_action('wp_head', 'ds_meta_tags');

/********************************************/



get_header();

global $post;

?>
<div id="main" class="container">

<?php display_channel_video_player(); ?>


<?php
	if(is_array($channel) && count($channel) > 0){
	?>

		<div id='primary' class='content-area'>
		    <?php if ($channel['count'] > 1) {?>
		    <ul class="ds-tabs">

		        <li class='ds-tab-link current' data-tab='ds-tab-1'>More Episodes</li>
		        <li class='ds-tab-link' data-tab='ds-tab-2'>Details</li>
		        <?php if ($siblings && strlen($siblings) > 0) {?>
		            <li class='ds-tab-link' data-tab='ds-tab-3'>Seasons</li>
		        <?php }?>
		        <?php if (!empty($post->post_content)) {?>
		            <li class='ds-tab-link' data-tab='ds-tab-4'>Additional Info</li>
		        <?php }?>
		        <li class='ds-tab-link'><a href='#ds-comments'>Comments</a></li>
		    </ul>
		    <?php }?>

		    <div id='ds-tab-1' class='ds-tab-content current'>
		        <div id="loading"><h5>Loading...</h5></div>

		        <ul class='ds-video-thumbnails ds-lazyload'>
		        <?php

		        $this_post = get_post(get_the_ID());

		        $channel_parent = '';

		        $category = get_query_var("channel_category", false);

		        $counter = 1;

		        foreach ($channel['playlist'] as $pl) {

		            $selected = '';

		            $id = $pl->_id;

		            $thumb_id = $pl->thumb;

		            $title = isset($pl->title) ? substr($pl->title,0,50) : '';

		            $duration = isset($pl->duration) ? round($pl->duration / 60) : '';

		            $description = isset($pl->description) ? $pl->description : '';

		            $company = isset($pl->company) ? $pl->company : '';

		            $country = isset($pl->country) ? $pl->country : '';

		            $language = isset($pl->language) ? $pl->language : '';

		            $year = isset($pl->year) ? $pl->year : '';

		            $rating = isset($pl->rating) ? $pl->rating : '';

		            $channel_parent = get_post($this_post->post_parent);

		            $epnum = key($pl);

		            $selected_id = get_query_var("video", false);

		            if ($id == $selected_id || $counter == 1 && !$selected_id) {

		                $selected = "class='selected'";

		            }

		            $counter++;

		            ?>

		            <li <?php echo $selected; ?>>
		                <img class="img img-responsive lazy" data-original='http://image.myspotlight.tv/<?php echo $thumb_id ?>/380/215' />
		                <div class='ds-overlay animated fadeIn'>

		                <?php if (!$siblings) {?>

		                    <a href='<?php echo home_url("channels/" . $this_post->post_name . "/?video=$id&channel_category=$category") ?>'>

		                <?php } else {?>

		                    <a href='<?php echo home_url("channels/" . $channel_parent->post_name . "/" . $this_post->post_name . "/?video=$id&channel_category=$category") ?>'>

		                <?php }?>


		                 <i class='fa fa-play-circle-o fa-3x'></i>
		                </a>
		                <label class='delay' style='display: inline-block;'><small><?php echo $duration ?> min</small></label>
		                </div>
		                <h3 class='character-limit-90'><?php echo $title ?></h3>
		                <span class='ds-video-year animated fadeIn'><small>Year: <?php echo $year ?></small></span>
		                <span class='ds-video-country animated fadeIn'><small>Country: <?php echo $country ?></small></span>
		                <span class='ds-video-description character-limit-90 animated fadeIn'><?php echo $description ?></span>
		            </li>
		            <?php

		        }

		        ?>


		            </ul>
		    </div>
		    <div id='ds-tab-2' class='ds-tab-content'>
		        <span class='ds-video-headliner-description'>
		        <?php echo $channel['details']['description']; ?>

		        Actors: <?php echo implode(", ", $channel['details']['actors']); ?>

		        Writers: <?php echo implode(", ", $channel['details']['writers']); ?>

		        Directors: <?php echo implode(", ", $channel['details']['directors']); ?>

		        </span>
		    </div>
		    <div id='ds-tab-3' class='ds-tab-content'>
		        <?php echo $siblings; ?>
		    </div>
		    <div id='ds-tab-4' class='ds-tab-content'>

		    <?php
				echo $post->post_content;
			?>

		    </div>
		    <div class='ds-commenting-sidebar'>
		    <?php ds_template_fb_code();?>
		    </div>
		    <?php
		} else {?>

            <h1>This channel is not available in your country.</h1>

        <?php } ?>


	</div>

</div><!--main-->
<?php get_sidebar();?>
<?php get_footer();?>
