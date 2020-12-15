<?php

/**
 * Plugin Name:       WP User Role Customizer
 * Description:       Create some custom roles for your wordpress website
 * Version:           0.0.4
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nika Goduadze
 * Author URI:        https://github.com/justgodu
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_USER_ROLE_CUSTOMIZER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-user-role-customizer-activator.php
 */
function activate_wp_user_role_customizer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-user-role-customizer-activator.php';
	Wp_User_Role_Customizer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-user-role-customizer-deactivator.php
 */
function deactivate_wp_user_role_customizer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-user-role-customizer-deactivator.php';
	Wp_User_Role_Customizer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_user_role_customizer' );
register_deactivation_hook( __FILE__, 'deactivate_wp_user_role_customizer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-user-role-customizer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_user_role_customizer() {

	$plugin = new Wp_User_Role_Customizer();
	$plugin->run();

}
run_wp_user_role_customizer();
