# API Cache Pro #
A simple plugin to cache WP Rest API Requests.

**Contributors:** [bhubbard](https://profiles.wordpress.org/bhubbard)  
**Tags:** wp rest api, rest api, wp api, api, json, json api  
**Donate link:** [Donate](https://github.com/bhubbard/wp-rest-api-cache)
**Requires at least:** 5.0
**Tested up to:** 5.2  
**Stable tag:** 0.0.1  
**License:** GPLv3 or later  
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html  

## Description ##

This plugin enables caching for the WordPress REST API to improve performance. Once enabled you can modify the basic settings via the Customizer.

** Customizer Options **

* Disable Cache
* Default Cache Timeout

Several Headers are added to all the API Requests. Here is an example of the available headers.

```
X-API-CACHE-PRO: Cached
X-API-CACHE-PRO-EXPIRES: January 20, 2019, 12:39 AM UTC
X-API-CACHE-PRO-EXPIRES-DIFF: 5 mins
X-API-CACHE-PRO-KEY: rest_api_cache_78be25416f69cd3a885dcf14017a0691
```

**X-API-CACHE-PRO** - Displays Cached, or Not Cached


## Installation ##

1. Copy the `api-cache-pro` folder into your `wp-content/plugins` folder
2. Activate the `API Cache Pro` plugin via the plugin admin page

## Changelog ##

Please see [CHANGELOG.MD](CHANGELOG.md)
