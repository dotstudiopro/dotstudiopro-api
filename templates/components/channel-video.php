	<?php
		$headline_video = channel_headline_video();
		if(isset($headline_video->player)) {
				$minifyVid = get_option('ds_player_minivid');
				$autoRedir = get_option('ds_player_autoredir');
				$autoPlay = get_option('ds_player_autoplay');
				$recPlaylist = get_option('ds_player_recplaylist');
				$videoId = $headline_video->_id;
				$show_playlist_above_meta = get_option('ds_show_playlist_above_meta');
		?>


	    <!-- VIDEO PLAYER -->
			<div class='ds-video-headliner'>
					<div id="anibox">&nbsp;</div>
			    <div class='row'>
			    		<div class='col-md-8 ds-video'>
									<div class='ds-video-fluidMedia'>
											<?php if($recPlaylist === '1'): ?><div class='ds-player-togglemode'><i class='fa fa-arrows-alt fa-2x'>&nbsp;</i></div><?php endif;?>
											<div class="player" data-minifyVid='<?php echo $minifyVid;?>' data-autoRedir='<?php echo $autoRedir;?>' data-autoPlay='<?php echo $autoPlay;?>' data-recPlaylist='<?php echo $recPlaylist ?>'></div>
											<script id='videoloader' src="<?php echo $headline_video->player ?>"></script>
									</div>
			    		</div>
			    		<!-- STANDARD MODE PLAYLIST -->
			    		<div class='col-md-4 ds-vid-playlist ds-playlist-standard-mode active-playlist'>
			    				
			    		</div>
			    </div>

			   <?php if($show_playlist_above_meta) {
			   		echo ds_theater_mode_playlist($videoId);
			   	} ?>

			    <div class='row'>
			    		<div class='col-md-12 col-sm-12 col-xs-12'>

								<div class='ds-metabox'>

										<!-- TITLE -->
										<div class='row'>
												<div class='col-md-12 col-sm-12 col-xs-12'>
													<h1 class='ds-video-headliner-title'><?php echo $headline_video->title ?></h1>
												</div>
										</div>

										<!-- DESCRIPTION -->
						        <div class="row">
						        		<div class='col-md-12 col-sm-12 col-xs-12 ds-metabox'>
								      		<span class='ds-video-headliner-description'><?php echo $headline_video->description ?></span>
								      		<hr>
													<a class='ds-more'>Show More</a>
						        		</div>
						        </div>

						        <!-- METADATA AND SHARING -->
										<div class='row'>
												<!-- meta -->
												<div class='col-md-8 col-sm-12 col-xs-12'>
													<ul class='ds-videometalist'>
												  			<li><?php echo $headline_video->duration ?> min</li>
									              <li><?php echo $headline_video->country ?></li>
									              <li>Rating:<?php echo $headline_video->rating ?></li>
									              <li><?php echo $headline_video->language ?></li>
									              <li><?php echo $headline_video->year ?></li>
								              	<li><?php echo $headline_video->company ?></li>
								         	</ul>
							       	 	</div>
							       	 	<!-- sharing -->
							       	 	<div class='col-md-4 col-sm-12 col-xs-12'>
									        <?php
													if(is_file( dirname( __FILE__ ) ."/../components/sharing.php" ) ){
														include( dirname( __FILE__ ) ."/../components/sharing.php" );
													} else if( is_file( dirname( __FILE__ ) . "/ds-sharing.php" ) ){
														include( dirname( __FILE__ ) . "/ds-sharing.php" );
													}
													?>
							       	 	</div>
						        </div>
						    </div>


			    		</div>
			    </div>

			   <?php if(!$show_playlist_above_meta) {
			   		echo ds_theater_mode_playlist($videoId);
			   	} ?>

			</div>






		<?php

		}
