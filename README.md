# API Cache Pro #
A simple plugin to cache WP Rest API Requests.

**Contributors:** [bhubbard](https://profiles.wordpress.org/bhubbard)  
**Tags:** wp rest api, rest api, wp api, api, json, json api  
**Donate link:** [Donate](https://github.com/bhubbard/wp-rest-api-cache) <br />
**Requires at least:** 5.0 <br />
**Tested up to:** 5.2  
**Stable tag:** 0.0.1  
**License:** GPLv3 or later  
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html  

## Description ##

This plugin enables caching for the WordPress REST API to improve performance. Once enabled you can modify the basic settings via the Customizer.

### Customizer Options

- Disable Cache
- Default Cache Timeout

## Request Headers

Several Headers are added to all the API Requests. Here is an example of the available headers.

```
X-API-CACHE-PRO: Cached
X-API-CACHE-PRO-EXPIRES: January 20, 2019, 12:39 AM UTC
X-API-CACHE-PRO-EXPIRES-DIFF: 5 mins
X-API-CACHE-PRO-KEY: api_cache_pro_78be25416f69cd3a885dcf14017a0691
```

* **X-API-CACHE-PRO** - Displays Cached, or Not Cached.
* **X-API-CACHE-PRO-EXPIRES** - Displays the date/time the cache is set to expire.
* **X-API-CACHE-PRO-EXPIRES-DIFF** - Displays the difference from current time to the time cache is set to expire. 
* **X-API-CACHE-PRO-KEY** - Displays the key used for the cache.

This plugin offers several filters so you can disable these headers:

| Filter    | Type | Default
|-----------|-----------|-------------|
| api_cache_pro_header | boolean | true
| api_cache_pro_key_header | boolean | true
| api_cache_pro_expires_header | boolean | true
| api_cache_pro_expires_diff_header | boolean | true

You can use these filters to disable any of the headers. Here is an example to disable the Key Header.

```php
/**
 * Disable API Cache Pro Key Header.
 * 
 * @access public
 */
function disable_api_cache_pro_key_header() {
	return false;
}
add_action( 'api_cache_pro_key_header', 'disable_api_cache_pro_key_header' );

```
## Clearing Cache

The cache will automatically get cleared if you do any of the following:

* Disable the Cache
* Change the Default Cache Timeout Length
* Deactivate or Uninstall the plugin

## Installation ##

1. Copy the `api-cache-pro` folder into your `wp-content/plugins` folder
2. Activate the `API Cache Pro` plugin via the plugin admin page

## Changelog ##

Please see [CHANGELOG.MD](CHANGELOG.md)
