<?php
/**
 * @package Callback Wordpress Plugin
 * @version 1.0
 */
/*
Plugin Name: Website Callback Plugin
Plugin URI: http://www.acumendevelopment.net
Description: Allows users to initiate a callback from the site, using the Netfuse service
Author: Leo Brown
Version: 1.0
Author URI: http://www.acumendevelopment.net
*/

// This just echoes the chosen line, we'll position it later
function callback_display($options=array()) {
	return '
	<div id="acumen_call_container">
	        <form id="acumen_call_form">
                	<div id="acumen_callfeedback"></div>
        	        <input id="acumen_callnumber"></input>
	                <input type="button" id="acumen_callbutton" value="Call"></input>
        	</form>
	</div>';
}

// We need some CSS to position the paragraph
function callback_css() {
	$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	echo '<link rel="stylesheet" type="text/css" href="'.$path.'callback.css"></link>';
}

function callback_js() {
	$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	echo '<script type="text/javascript">var pluginpath = "'.$path.'";</script>';
	echo '<script type="text/javascript" src="'.$path.'callback.js?"></script>';
}

function check_status(){
	return 'The Netfuse service is not responding - the callback box will not appear';
}

add_action( 'wp_head', 'callback_js' );
add_action( 'wp_head', 'callback_css' );

// warn admin if we won't be able to make calls
add_action( 'admin_notices', 'check_status' );

// shortcode manager
function callback_shortcode($atts){
	return callback_display($atts);
}
add_shortcode('callback', 'callback_shortcode');

// admin area
add_action('admin_menu', 'callback_admin');
function callback_admin() {
	add_options_page('Website Callback', 'Callback Settings', 'manage_options', 'callback', 'callback_options_page');
}

add_action('admin_init', 'callback_admin_init');
function callback_admin_init(){

	register_setting( 'callback_options', 'callback_options', 'callback_options_validate' );

	add_settings_section('callback_main', 'Netfuse API Details', 'callback_settings_text', 'callback');

	add_settings_field('callback_username', 'Netfuse API Username', 'callback_username_string', 'callback', 'callback_main');
	add_settings_field('callback_password', 'Netfuse API Password', 'callback_password_string', 'callback', 'callback_main');
	add_settings_field('callback_number', 'Number to Connect', 'callback_number_string', 'callback', 'callback_main');
	add_settings_field('callback_email', 'Email to Alert', 'callback_email_string', 'callback', 'callback_main');
}

function callback_options_page() {?>
	<div>
	<h2>Callback Options</h2>
	You need to configure these settings before your customers can contact you through the site.
	<form action="options.php" method="post">
		<?php settings_fields('callback_options'); ?>
		<?php do_settings_sections('callback'); ?>
		<br />
		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form></div>
	<?php
}
function callback_settings_text() {
	echo '<p>Please enter your Netfuse API details below:</p>';
}
function callback_username_string() {
	$options = get_option('callback_options');
	echo "<input id='netfuse_username' name='callback_options[callback_username]' size='40' type='text' value='{$options['callback_username']}' />";
}
function callback_password_string() {
	$options = get_option('callback_options');
	echo "<input id='callback_password' name='callback_options[callback_password]' size='40' type='text' value='{$options['callback_password']}' />";
}
function callback_number_string() {
	$options = get_option('callback_options');
	echo "<input id='callback_number' name='callback_options[callback_number]' size='40' type='text' value='{$options['callback_number']}' />";
}

function callback_email_string() {
	$options = get_option('callback_options');
	echo "<input id='callback_email' name='callback_options[callback_email]' size='40' type='text' value='{$options['callback_email']}' />";
}

function callback_options_validate($input) {
	return $input;
}

?>
