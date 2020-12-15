<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wp_User_Role_Customizer
 * @subpackage Wp_User_Role_Customizer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_User_Role_Customizer
 * @subpackage Wp_User_Role_Customizer/public
 * @author     Your Name <email@example.com>
 */
class Wp_User_Role_Customizer_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wp_user_role_customizer    The ID of this plugin.
	 */
	private $wp_user_role_customizer;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wp_user_role_customizer       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_user_role_customizer, $version ) {

		$this->wp_user_role_customizer = $wp_user_role_customizer;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_User_Role_Customizer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_User_Role_Customizer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->wp_user_role_customizer, plugin_dir_url( __FILE__ ) . 'css/wp-user-role-customizer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_User_Role_Customizer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_User_Role_Customizer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->wp_user_role_customizer, plugin_dir_url( __FILE__ ) . 'js/wp-user-role-customizer-public.js', array( 'jquery' ), $this->version, false );

	}

    function check_role_access(){
        // Make sure user is logged in
        if(!is_user_logged_in()){
            return 0;
        }
        //Make sure its not admin
        if(in_array("administrator", wp_get_current_user()->roles)){
            return 0;
        }


        //Get capabilities
        $allcaps = wp_get_current_user()->allcaps;
        $can_access = array();
        //Get manu slugs user can access from capabilities
        foreach($allcaps as $key=>$value){
            if(strpos($key,$this->wp_user_role_customizer) !== FALSE){
                //Remove wp_user_role_customizer from capability name to get menu-slug
                array_push($can_access, substr($key, strlen($this->wp_user_role_customizer . "_"), strlen($key)));

            }
        }

        if(empty($can_access)){
            return 0;
        }

        // array_push($can_access, 'post.php');



        //Get path and query of requested page
        $uri = wp_parse_url($_SERVER['REQUEST_URI']);
        $path  = (isset($uri['path']) ? $uri['path'] : null);
        $query = (isset($uri['query']) ? $uri['query'] : null);
        $user_can_access = FALSE;
        //Make sure its wp-admin page
        if(strpos($path, 'wp-admin') !== FALSE){
            //Redirect to dashboard if requested page is either about.php or index.php
            if(strpos($path, '/wp-admin/about.php') !== FALSE || strpos($path, '/wp-admin/index.php') !== FALSE){
                wp_redirect(get_dashboard_url());
                exit();
            }

            if( strpos($path,'.php') !== FALSE || $query !== null){
                foreach($can_access as $menu_slug){
                    //Access page if user can access requested page
                    if($this->check_for_url_access($path,$query,$menu_slug)){
                        $user_can_access = TRUE;
                        break;

                    }
                }
                //Exit if user can't access requested page
                if($user_can_access === FALSE){
                    wp_die('You Cannot Access This Page', 'Access Denied',['exit'=> true]);
                }
            }
        }

    }

    // Check similarities between url and menu slug
    private function check_for_url_access($path,$query,$menu_slug){
        if(strpos('?'.$query .' ',$menu_slug. ' ') !== FALSE || strpos($path, '/' .$menu_slug) !== FALSE || strpos($path.'?'.$query .' ',$menu_slug. ' ') !== FALSE || strpos($query,$menu_slug) !== FALSE){
            return true;
        }
        return false;
    }

}
