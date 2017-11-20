<?php

class DotStudioz_Commands{

	public $country;

function curl_command($command, $args = array()){

	global $wpdb, $post;

	$api_key = get_option('ds_api_key');

	if(!$api_key || strlen($api_key) < 1){

		return array();

	}


	if($command == "token"){


		$result = ds_run_curl_command("http://api.myspotlight.tv/token",
			"POST", "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"key\"\r\n\r\n".$api_key."\r\n-----011000010111000001101001--",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
			));

		if ($result->err) {

			// Maybe log this somewhere?
			return false;

		} else {
			$r = json_decode($result->response);

			if($r->success){



				return $r->token;

			} else {

				// Maybe log this somewhere?
				return false;

			}
		}

	} else if($command == 'country'){

		/** DEV MODE **/

		$dev_check = get_option("ds_development_check");

		$dev_country = get_option("ds_development_country");

		if($dev_check){

			$this->country = $dev_country;

			return $this->country;

		}

		/** END DEV MODE **/



		$token = get_option('ds_curl_token');

		if(!$token){

			return false;

		}

		$curl = curl_init();


		$result = ds_run_curl_command("http://api.myspotlight.tv/country",
			"POST", "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$this->get_ip()."\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n\r\n-----011000010111000001101001--",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"x-access-token:".$token
			));



		if ($result->err) {

			$error = "cURL Error: $err";

		} else {
			$r = json_decode($result->response);



			if($r->success){

				$this->country = $r->data->countryCode;

				return $this->country;



			} else {

				// Maybe log this somewhere?
				return false;

			}
		}

	} else if ($command == 'recommended') {
			// return a list of recommended videos for a given video
			// requires a video ID and list size (default = 8)


		$token = get_option('ds_curl_token');
		$video_id = $args['video_id'];
		$rec_size = $args['rec_size'];


		if(!$token || !$video_id) {
				return array();
		}

		$curl = curl_init();

		$result = ds_run_curl_command("http://api.myspotlight.tv/search/recommendation?q=".$video_id."&size=".$rec_size."&from=0",
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:". $token
		));

		if ($result->err) {
			// you fucked up, homes...
			return array(false,$result->err);

		} else {
			$r = json_decode($result->response);
			if($r->success) {

					return $r->data->hits;
			} else {
					// Maybe log this somewhere?
					// yea... maybe.
					return false;
			}
		}


	} else if($command == 'all-channels'){

		$token = get_option('ds_curl_token');

		ds_get_country();

		if(!$token || !$this->country){

			return array();

		}

		$result = ds_run_curl_command("http://api.myspotlight.tv/channels/".$this->country."?detail=partial",
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));


		if ($result->err) {

			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...

		} else {
			$r = json_decode($result->response);

			if($r->success){

				// set_transient('all-channels', $r->channels, 600);

				return $r->channels;

			} else {

				// Maybe log this somewhere?
				return false;

			}
		}

	}  else if($command == 'single-channel'){

		$token = get_option('ds_curl_token');

		$category = get_post_meta($post->ID, "ds-category", TRUE);

		$duplicate = get_post_meta($post->ID, "ds-duplicate", false);

		ds_get_country();

		if(!$category){

			$category = 'featured';

		}

		if(!$token){

			return array();

		}

		$channel_check_grab = get_page_by_path('channels');

		$channel_parent = $channel_check_grab->ID;

		$channel_grandparent = wp_get_post_parent_id( $post->post_parent );

		$revision = channel_revision_check();

		if($duplicate && (int) $duplicate !== 0){

			$pop = explode( "-", $post->post_name );

			array_pop( $pop );

		}

		$postname = $duplicate && (int) $duplicate !== 0 ? implode( "-", $pop ) : $post->post_name;

		if($channel_grandparent == $channel_parent && !$revision){

			$parent = get_post($post->post_parent);

			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$parent->post_name."/".$postname."/?detail=partial";

		} else {

			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$postname."/?detail=partial";

		}

		// die('single-channel-' . $category . '-' . (!empty($parent) ? $parent->post_name : "none") . '-' . $postname);
		// $trans = get_transient('single-channel-' . $category . '-' . (!empty($parent) ? $parent->post_name : "none") . '-' . $postname);
		// if($trans) return $trans;

		$curl = curl_init();

		$channel_name = $post->post_name;

		$result = ds_run_curl_command($url,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {

			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...

		} else {
			$r = json_decode($result->response);

			if($r->success){

				set_transient('single-channel-' . $category . '-' . (!empty($parent) ? $parent->post_name : "none") . '-' . $postname, $r->channels, 600);

				return $r->channels;

			} else {

				// Maybe log this somewhere?
				return false;

			}
		}

	}  else if($command == 'single-channel-by-id'){

		$token = get_option('ds_curl_token');

		$channel_check_grab = get_page_by_path("channels/".$args['channel_slug']);

		$category = get_post_meta($channel_check_grab->ID, "ds-category", TRUE);

		ds_get_country();

		if(!$category){

			$category = 'featured';

		}

		if(!$token){

			return array();

		}

		$postname = $channel_check_grab->post_name;

		$trans = get_transient('single-channel-' . $category . '-' . $postname . '-partial');
		if($trans) return $trans;

		$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$postname."/?detail=partial";

		$curl = curl_init();

		$channel_name = $post->post_name;

		$result = ds_run_curl_command($url,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {

			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...

		} else {
			$r = json_decode($result->response);

			if($r->success){

				set_transient('single-channel-' . $category . '-' . $postname . '-partial', $r->channels, 600);

				return $r->channels;

			} else {

				// Maybe log this somewhere?
				return false;

			}
		}

	} else if($command == 'parent-channel'){

		$token = get_option('ds_curl_token');

		$category = get_post_meta($post->ID, "ds-category", TRUE);

		ds_get_country();

		if(!$category){

			$category = 'featured';

		}

		if(!$token){

			return array();

		}

		$channel_check_grab = get_page_by_path('channels');

		$channels_parent = $channel_check_grab->ID;

		$channel_grandparent = wp_get_post_parent_id( $post->post_parent );

		if($channel_grandparent == $channels_parent){

			$parent = get_post($post->post_parent);

			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$parent->post_name."/?detail=partial";

		} else {

			return false;

		}

		$curl = curl_init();

		$channel_name = $post->post_name;

		$result = ds_run_curl_command($url,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {

			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...

		} else {
			$r = json_decode($result->response);

			if(isset($r->channels[0])){

				return $r->channels[0];

			} else {


				// Maybe log this somewhere?
				return false;

			}
		}

	} else if($command == 'all-categories'){

		$token = get_option('ds_curl_token');

		ds_get_country();

		if(!$token || !$this->country){

			return array();

		}

		$curl = curl_init();

		$result = ds_run_curl_command("http://api.myspotlight.tv/categories/".$this->country,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {

			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...

		} else {
			$r = json_decode($result->response);

			if(count($r->categories)){

				return $r->categories;

			} else {

				// Maybe log this somewhere?
				return false;

			}
		}

	} else if($command == 'single-category'){

		$token = get_option('ds_curl_token');

		$cat = $args['category'];

		if(!$token || !$this->country || !$cat){

			return array();

		}

		$result = ds_run_curl_command("http://api.myspotlight.tv/channels/".$this->country."/".$cat."?detail=partial",
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {

			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...

		} else {
			$r = json_decode($result->response);

			if(isset($r->message)){

				$message = $r->message;

			} else {

				$message = '';

			}

			if(isset($r->channels) && count($r->channels) && strpos($message, "Error") !== TRUE){

				return $r->channels;

			} else {

				// Maybe log this somewhere?
				$empty_obj = new stdClass();
				return $empty_obj;

			}
		}

	} else if($command == 'play'){

		/** DEV MODE **/

		$dev_check = get_option("ds_development_check");

		$dev_country = get_option("ds_development_country");

		if($dev_check){

			$this->country = $dev_country;

		}

		/** END DEV MODE **/

		$video = $args['video'];

		$token = get_option('ds_curl_token');

		if(!$token){

			return false;

		}

		$curl = curl_init();


		$result = ds_run_curl_command("http://api.myspotlight.tv/video/play2/$video",
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"x-access-token:".$token
			));



		if ($result->err) {

			$error = "cURL Error: $err";

		} else {
			$r = json_decode($result->response);



			if($r->_id){

				return $r;

			} else {

				// Maybe log this somewhere?
				return false;

			}
		}

	}

}

	function get_ip(){

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;

	}

}
