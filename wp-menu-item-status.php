<?php
/*
Plugin Name: WP Menu Item Status
Plugin URI: https://github.com/linasalve28/WP-Menu-Item-Status
Description: You can enable disable items from menu
Version: 1.0
Author: Lina Salve
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wpmi-control

*/


if (!defined('ABSPATH')) {
    exit;
}


if ( !defined( 'WPMI_PLUGIN_DIR' ) ) {
	define( 'WPMI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'WPMI_PLUGIN_URL' ) ) {
	define( 'WPMI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'WPMI_PLUGIN_FILE' ) ) {
	define( 'WPMI_PLUGIN_FILE', __FILE__ );
}
if ( !defined( 'WPMI_PLUGIN_VERSION' ) ) {
	define( 'WPMI_PLUGIN_VERSION', '1.0' );
}

if( version_compare( PHP_VERSION, '5.3', '<' ) ) {

	add_action( 'admin_notices', 'wpmi_below_php_version_notice' );
	function edm_below_php_version_notice() {
		echo '<div class="error"><p>' . __( 'Your version of PHP is below the minimum version of PHP required by Enable Disable Menu Plugin. Please contact your host and request that your version be upgraded to 5.3 or later.', 'wpmi-control' ) . '</p></div>';
	}

} else {
	if( is_admin() ) {
		
		include( WPMI_PLUGIN_DIR . 'includes/class_WPMI_Status.inc.php' );
		$GLOBALS['class_WPMI_Status_control'] = class_WPMI_Status::instance();
	}
	
}