<?php
/*
Plugin Name: Anonymous WordPress Plugin Updates
Description: Anonymizes the data transmitted during plugin update checking and notification system.
Plugin URI:  http://f00f.de/blog/2007/10/02/plugin-anonymous-wordpress-plugin-updates.html
Version:     1.0
Author:      Hannes Hofmann
Author URI:  http://uwr1.de/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

$hh_fake_url = 'http://privacy.org/';
$hh_fake_version = '1984';

// debugging feature
$hh_send_debug_mail = false;
$hh_debug_mail_to = '';

// END OF CONFIGURATION


// This is a modified copy of the function wp_update_plugins() from WP 2.3
function hh_wp_update_plugins_2_3() {
	global $wp_version;
	// HH: mod
	global $hh_fake_url, $hh_fake_version;
	// HH: end mod
	// HH: debug
	global $hh_send_debug_mail, $hh_debug_mail_to;
	// HH: end debug

	if ( !function_exists('fsockopen') )
		return false;

	$plugins = get_plugins();
	$active  = get_option( 'active_plugins' ); // HH: list of filenames
	$current = get_option( 'update_plugins' );

	$plugin_changed = false;
	foreach ( $plugins as $file => $p ) {
		$new_option->checked[ $file ] = $p['Version'];

		if ( !isset( $current->checked[ $file ] ) ) {
			$plugin_changed = true;
			continue;
		}

		if ( $current->checked[ $file ] != $p['Version'] )
			$plugin_changed = true;
	}

	if (
		isset( $current->last_checked ) &&
		43200 > ( time() - $current->last_checked ) &&
		!$plugin_changed
	)
		return false;

	// HH: mod: strip additional infos
	$hh_version_  = isset($hh_fake_version) ? $hh_fake_version : $wp_version;
	$hh_blog_url_ = isset($hh_fake_url)     ? $hh_fake_url     : get_bloginfo('url');

	$plugin_files = array_keys($plugins);
	foreach ($plugin_files as $f) {
		$keys = array_keys($plugins[$f]);
		foreach ($keys as $k) {
			if (!in_array($k, array('Name', 'Version'))) {
				unset($plugins[$f][$k]);
			}
		}
	}
	$active = array();
	// HH: end of mod

	$to_send->plugins = $plugins;
	$to_send->active = $active;
	$send = serialize( $to_send );
	
	// HH: debug
	if ($hh_send_debug_mail) {
		$mailbody = "Die Plugin Informationen:\n\n" . print_r($to_send, true) . "\n------------\n\n";
	}
	// HH: end debug
	
	$request = 'plugins=' . urlencode( $send );
	$http_request  = "POST /plugins/update-check/1.0/ HTTP/1.0\r\n";
	$http_request .= "Host: api.wordpress.org\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	// HH: mod: anonymize wp-version and blog url
//	$http_request .= 'User-Agent: WordPress/' . $wp_version /*. '; ' . get_bloginfo('url')*/ . "\r\n";
	$http_request .= 'User-Agent: WordPress/' . $hh_version_ . '; ' . $hh_blog_url_ . "\r\n";
	// HH: end of mod
	$http_request .= "\r\n";
	$http_request .= $request;
	
	// HH: debug
	if ($hh_send_debug_mail) {
		$mailbody .= "Der gesamte HTTP Request:\n\n" . $http_request."\n------------\n\n";
	}
	// HH: end debug

	$response = '';
	if( false != ( $fs = @fsockopen( 'api.wordpress.org', 80, $errno, $errstr, 3) ) && is_resource($fs) ) {
		fwrite($fs, $http_request);

		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
	}

	// HH: debug
	if ($hh_send_debug_mail) {
		$mailbody .= "Die Antwort von wordpress.org:\n\n".$response."\n------------\n\n";
		wp_mail($hh_debug_mail_to, 'WP-Update', $mailbody);
	}
	// HH: end debug

	$response = unserialize( $response[1] );

	if ( $response )
		$new_option->response = $response;

	update_option( 'update_plugins', $new_option );
}
add_action( 'load-plugins.php', 'hh_wp_update_plugins_2_3' );

add_action( 'admin_menu', create_function( '$a', "remove_action( 'load-plugins.php', 'wp_update_plugins' );") );
	# Why use admin_menu? It's the only hook available between the above hook being added and being applied
//add_filter( 'pre_option_update_plugins', create_function( '$a', "return null;" ) );
?>