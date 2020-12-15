<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wp_User_Role_Customizer
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
//include('../../../wp-load.php');
//require_once ABSPATH . 'wp-admin/includes/user.php';
$roles = get_editable_roles();
$plugin_roles = array();

foreach($roles as $role){
    $capabilities = $role['capabilities'];

    foreach($capabilities as $capability=>$value){

        if(strpos($capability,"wp-user-role-customizer") !== FALSE){
            array_push($plugin_roles, $role['name']);
            break;
        }
    }
}
foreach ($plugin_roles as $plugin_role){
    $args = array(
        'role'    => $plugin_role,
        'orderby' => 'user_nicename',
        'order'   => 'ASC'
    );
    $users = get_users( $args );

    foreach ($users as $user){
        $myuser = new WP_User($user->ID);
        $myuser->remove_role($plugin_role);
        $default_role = get_option('default_role') ? get_option('default_role') : 'subscriber';
        $myuser->add_role($default_role);
    }


// remove_role functions removes by slug for some reason so we slugify role name
    remove_role(slugify($plugin_role));

}

function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}
