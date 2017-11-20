<?php get_header();?>

<div id="main">
    <ul class="gridder">


    <?php

$category = get_post(get_the_ID());

$category_check_grab = get_page_by_path('channel-categories');

$category_parent = $category_check_grab->ID;

$post_slug = $category->post_name;

if ($category->post_parent == $category_parent) {

    $channels = grab_category($post_slug);

    if ($channels && is_array($channels)) {

        foreach ($channels as $ch) {

            $id = $ch->_id;

            $thumb_id = isset($ch->videos_thumb) ? $ch->videos_thumb : '';

            $slug = $ch->slug;

            $slug_check = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_name LIKE '%" . $ch->slug . "%'");

            if (count($slug_check) > 1) {

                foreach ($slug_check as $sl) {

                    $category = get_post_meta($sl->ID, 'ds-category', true);

                    if (strtolower($category) == strtolower($post_slug)) {

                        $slug = $sl->post_name;
                        break;

                    }

                }

            }

            $title = isset($ch->channel_logo) && strlen($ch->channel_logo) > 0 ? "<img src='$ch->channel_logo' />" : $ch->title;

            if (!empty($ch->spotlight_poster)) {
                $spotlight_poster = $ch->spotlight_poster;
            } else if (!empty($ch->poster)) {
                $spotlight_poster = $ch->poster;
            } else if (!empty($ch->videos_thumb)) {
                $spotlight_poster = $ch->videos_thumb;
            } else if (!empty($ch->channel_logo)) {
                $spotlight_poster = $ch->channel_logo;
            } else {
                $spotlight_poster = '';
            }

            $poster = isset($ch->poster) ? $ch->poster : '';

            if(empty($poster) && !empty($spotlight_poster)) $poster = $spotlight_poster;

            $year = isset($ch->year) ? $ch->year : '';

            $language = isset($ch->language) ? $ch->language : '';

            $rating = isset($ch->rating) ? $ch->rating : '';

            $company = $ch->company;

            $description = "";

            if(isset($ch->description)) {
                $description = $ch->description;
            } else if(isset($ch->video) && isset($ch->video->description)) {
                $description = $ch->video->description;
            }

            $children = $ch->childchannels;

            $child_urls = '';

            if (count($children) > 0) {

                foreach ($children as $kid) {

                    $child_urls .= "<a href='" . home_url("channels/$slug/" . $kid->slug . "/") . "' class='ds-button'>" . $kid->title . "</a>";

                }

                $description = !empty($ch->description) ? $ch->description : "";

            }

            ?>

            <li class='gridder-list light-theme-shadow' data-griddercontent='#<?php echo $slug ?>'>
                <a href='<?php echo home_url("channels/$slug/") ?>' class="gridder-og-play"><i class="fa fa-play-circle-o"></i></a>
                <i class="fa fa-chevron-down"></i>
                <img class='channel-spotlight-poster' src='<?php echo $spotlight_poster ?>/400/225'>
                <div id='<?php echo $slug ?>' class='gridder-content'>
                    <div class='og-expander-inner light-theme-shadow clearfix'>
                        <a class='og-fullimg' href='<?php echo home_url("channels/$slug/") ?>'><object class='channel-poster animated fadeIn' data='<?php echo !empty($poster) ? $poster : $thumb_id ?>/1080/610' type='image/png'></object></a>
                        <div class="og-mask"></div>
                        <div class='ds-details animated fadeInRight'>
                            <h2 class='channel-title'><?php echo $title ?></h2>
                            <ul class='ds-channelmetalist'>
                                <li class='channel-year'><?php echo $year ?></li>
                                <li class='channel-language'><?php echo $language ?></li>
                                <li class='channel-company'><?php echo $company ?></li>
                            </ul>
                            <span class='ds-channel-description'>Description: <?php echo strlen($description) > 300 ? substr($description, 0, 299) . "..." : $description ?></span>
                            <?php if (count($children) < 1) {?>

                                <a href= '<?php echo home_url("channels/$slug/") ?>' class='ds-button'>
                                    Watch Now
                                </a>
                            <?php

                            } else {

                                echo $child_urls;

                            }

                            ?>

                            </span>
                        </div>
                    </div>
                </div>
            </li>


            <?php

        }

    } else {

        echo "No channels to display.";

    }

}

?>
    </ul>

</div><!--main-->

<?php get_footer();?>