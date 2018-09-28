<?php

class dotstudioPRO_API {

	public $country;
	public $api_key;
	public $token;

	function __construct() {
		$this->api_key = get_option('dspdev_api_key');
	}

	/**
	 * Get an access token from the API
	 *
	 * @return String|Boolean The API access token, or false if it couldn't get one
	 */
	function get_token() {
		// If we don't have an api key, we can't get a token
		if(empty($this->api_key)) return false;

		$result = dspdev_api_run_curl_command("http://api.myspotlight.tv/token",
			"POST", "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"key\"\r\n\r\n".$this->api_key."\r\n-----011000010111000001101001--",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
			));

		if ($result->err) {
			return false;
		} else {
			$r = json_decode($result->response);
			if($r->success){
				$token = $r->token;
				return $r->token;
			} else {
				return false;
			}
		}
	}

	/**
	 * Set the token variable if we have the value outside of the class
	 *
	 * @param string $token The token to set
	 *
	 * @return String|Boolean Returns the 2 letter country code, or false if there was an issue
	 */
	function set_token($token) {
		$this->token = $token;
	}

	/**
	 * Get a new token from the API key we have
	 *
	 * @return void
	 */
	function api_new_token()
	{
	    // Acquire an API token and save it for later use.
	    $token = $this->get_token();
	    update_option('dspdev_api_token', $token);
	    update_option('dspdev_api_token_time', time());
	    return $token;
	}

	/**
	 * Check if we have a token and if it is expired, and get a new one if expired or missing
	 *
	 * @return String|Bool The access token or false if something went wrong
	 */
	function api_token_check()
	{
	    $token = get_option('dspdev_api_token');
	    $token_time = !$token ? 0 : get_option('dspdev_api_token_time');
	    $difference = floor((time() - $token_time) / 84600);
	    if (!$token || $difference >= 25) {
	        $token = $this->api_new_token();
	        if(empty($token)) return false;
	    }
	    return $token;
	}

	/**
	 * Get the country code of the user
	 *
	 * @return String|Boolean Returns the 2 letter country code, or false if there was an issue
	 */
	function get_country() {

		$token = $this->api_token_check();

		/** DEV MODE **/

		$dev_check = get_option("dspdev_api_development_check");

		$dev_country = get_option("dspdev_api_development_country");

		if($dev_check){
			$this->country = $dev_country;
			return $this->country;
		}

		/** END DEV MODE **/

		// If we don't have a token, we can't get a country
		if(empty($token)) return false;

		$result = dspdev_api_run_curl_command("http://api.myspotlight.tv/country",
			"POST", "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$this->get_ip()."\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n\r\n-----011000010111000001101001--",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"x-access-token:".$token
			));

		if ($result->err) {
			return false;
		} else {
			$r = json_decode($result->response);
			if($r->success){
				$this->country = $r->data->countryCode;
				return $this->country;
			} else {
				return false;
			}
		}
	}

	/**
	 * Get a list of recommended videos for a given video ID
	 *
	 * @param string $video_id The video id we need to base recommended videos off of
	 * @param integer $rec_size The number of items we want to get back
	 *
	 * @return Array Returns an array of recommended videos, or an empty array if something is wrong or there are no recommended videos
	 */
	function get_recommended($video_id, $rec_size = 8) {

		$token = $this->api_token_check();
		// If we don't have a token, we can't access the API
		if(empty($token)) return array();
		// If we don't have a video id, we can't get a recommended list
		if(empty($video_id)) return array();

		$result = dspdev_api_run_curl_command("http://api.myspotlight.tv/search/recommendation?q=".$video_id."&size=".$rec_size."&from=0",
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:". $token
		));

		if ($result->err) {
			return array();
		} else {
			$r = json_decode($result->response);
			if($r->success) {
				return $r->data->hits;
			} else {
				return array();
			}
		}
	}

	/**
	 * Get an array with all of the published channels in a company
	 *
	 * @param string $detail The level of detail we want from the channel call
	 *
	 * @return Array Returns an array of channels, or an empty array if something is wrong or there are no channels
	 */
	function get_channels($detail = 'partial') {

		$token = $this->api_token_check();

		// If we have no token, or we have no country, the API call will fail, so we return an empty array
		if(!$token || !$this->country) return array();

		$result = dspdev_api_run_curl_command("http://api.myspotlight.tv/channels/".$this->country."?detail=" . $detail,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {
			return array();
		} else {
			$r = json_decode($result->response);
			if($r->success){
				return $r->channels;
			} else {
				return array();
			}
		}
	}

	/**
	 * Get an array with a specific channel
	 *
	 * @param string $slug The slug of the channel we wish to call
	 * @param string $category The category of the channel we are trying to call
	 * @param string $detail The level of detail we want from the channel call
	 * @param string $child_slug The child channel slug, if we need to call a child channel
	 *
	 * @return Array Returns an array of with the channel object in it, or an empty array if something is wrong or there is no channel
	 */
	function get_channel($slug, $category, $detail = 'partial', $child_slug = '') {

		$token = $this->api_token_check();
		// If we have no token, no country, no category, or no slug, the API call will fail, so we return an empty array
		if(!$token || !$this->country || empty($slug) || empty($category)) return array();

		if(!empty($child_slug)){
			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$slug."/".$child_slug."/?detail=" . $detail;
		} else {
			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$slug."/?detail=" . $detail;
		}

		$result = dspdev_api_run_curl_command($url,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {
			return array();
		} else {
			$r = json_decode($result->response);
			if(!empty($r->success)){
				return $r->channels;
			} else {
				return array();
			}
		}
	}

	/**
	 * Get an array with all of the categories in a company
	 *
	 * @return Array Returns an array of with the categories, or an empty array if something is wrong or there are no categories
	 */
	function get_categories() {

		$token = $this->api_token_check();
		// If we have no token, or we have no country, the API call will fail, so we return an empty array
		if(!$token || !$this->country) return array();

		$result = dspdev_api_run_curl_command("http://api.myspotlight.tv/categories/".$this->country,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {
			return array();
		} else {
			$r = json_decode($result->response);
			if(count($r->categories)){
				return $r->categories;
			} else {
				return array();
			}
		}
	}

	/**
	 * Get an array with a specific category
	 *
	 * @param string $category The slug of the category we want to grab
	 *
	 * @return Object Returns an object with the category info, or an empty object if something went wrong
	 */
	function get_category($category) {

		$token = $this->api_token_check();
		// If we don't have a token, country, or category, the API call will fail
		if(!$token || !$this->country || !$category) return array();

		$result = dspdev_api_run_curl_command("http://api.myspotlight.tv/categories/".$this->country."/".$category,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
			));

		if ($result->err) {
			return new stdClass;
		} else {
			$r = json_decode($result->response);
			if(isset($r->category)){
				return $r->category;
			} else {
				return new stdClass;
			}
		}
	}

	/**
	 * Get information for a specific video
	 *
	 * @param string $video_id The id of the video to get info for
	 *
	 * @return Object Returns an object with the video, or an empty object if something went wrong
	 */
	function get_video($video_id) {

		$token = $this->api_token_check();
		// If we don't have a token, country, or video id, the API call will fail
		if(!$token || !$this->country || !$video_id) return array();

		$result = dspdev_api_run_curl_command("http://api.myspotlight.tv/video/play2/" . $video_id,
			"GET", "",
			array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"x-access-token:" . $token
			));

		if ($result->err) {
			return new stdClass;
		} else {
			$r = json_decode($result->response);
			if(!empty($r->_id)){
				return $r;
			} else {
				return new stdClass;
			}
		}
	}

	/**
	 * Get the IP of the user
	 *
	 * @return String The IP address of the user
	 */
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
