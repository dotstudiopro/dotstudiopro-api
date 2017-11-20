# BETA

This plugin is currently in **beta**. You can download it and play with it as much as you want. We are very active with updates so make sure to watch out for new versions of the plugin.

# dotstudioPRO API Class

This class (`dotstudioPRO_API`) allows programmers to access our API without having to create the connections theirselves.


Installation
--------

1. Create a [dotstudioPRO account](http://dotstudiopro.com).
2. Once logged in, go to the *User Account* section and get your **API key**.
3. Install dotstudioPRO Wordpress Plugin from your Wordpress dashboard or unzip the plugin archive in the `wp-content/plugins` directory.
4. Activate the plugin through the *Plugins* menu in Wordpress.
5. Go to the **dotstudioPRO** left menu configuration page and fill in your **API key**.
6. Start using the provided class.

Instantiation
--------

The API class must be instantiated via the following:

```// Set up our class to connect with the DSP API
$dspdev_api_class = new dotstudioPRO_API();
// Check to make sure we have a current token
$token = dspdev_api_check();
// Set the token we get back from the check
$dspdev_api_class->set_token($token);
// Make sure we have a country for any API calls to channels
$dspdev_api_class->get_country();
```

dotstudioPRO_API Methods
--------

### get_token()

Used to get a new access token via the dotstudioPRO API

### set_token($token)

* **$token** _String_ The access token

Set the access token in the class object

### get_country()

Get the 2-letter country code of the current user and save it in the class object

### get_recommended($video_id, $rec_size = 8)

* **$video_id** _String_ The video id we need to base recommended videos off of
* **$rec_size** _Integer_ The number of items we want to get back

Get an array of recommended videos for a particular video ID.

### get_channels($detail = 'partial')

* **$detail** _String_ The level of detail we want from the channel call

Get an array with all of the published channels in a company.

### get_channel($slug, $category, $detail = 'partial', $child_slug = '')
* **$slug** _String_ The slug of the channel we wish to call
* **$category** _String_ The category of the channel we are trying to call
* **$detail** _String_ The level of detail we want from the channel call
* **$child_slug** _String_ The child channel slug, if we need to call a child channel

Get an array with a specific channel's info.

### get_categories()

Get an array with all of the categories in a company.

### get_category($category)

* **$category** _String_ The slug of the category we are getting information for

Get info on a specific category.

### get_video($video_id)

* **$video_id** _String_ The ID of the video you're trying to get info for

Get the info for a particular video, including title and various metadata.

### get_ip()

Get the IP of the current user; used in the `get_country()` call

Misc Functions
--------

### dspdev_api_check()

Check if we have an API access token, and it's current.  If it isn't, get a new one.

### dspdev_api_check_api_key_set()

Admin nag to make sure the api key is set.

### dspdev_api_new_token()

Get a new API token.

### dspdev_api_get_country()

Get the 2-letter country code from the user's IP.  Wrapper for `get_country()` method.

### dspdev_api_run_curl_command($curl_url, $curl_request_type, $curl_post_fields, $curl_header)

* **$curl_url** _String_ The URL to do the cUrl request to
* **$curl_request_type** _String_ The type of request, generally POST or GET
* **$curl_post_fields** _String_ The fields we want to POST, if it's a POST request
* **$curl_header** _Object_ Any necessary header values, like an API token

A wrapper for cURL functionality to reduce the code needed to make calls.

### dspdev_api_verify_post_var($var)

* **$var** _String_ The name of the posted field

Checks to see if a `$_POST` variable is set, and returns it if so.  Otherwise, it returns an empty string.

### dspdev_api_no_country()

Admin nag to notify that we can't get the country code.  This generally means that you are developing on local.



