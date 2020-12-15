<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wp_User_Role_Customizer
 * @subpackage Wp_User_Role_Customizer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_User_Role_Customizer`
 * @subpackage Wp_User_Role_Customizer/admin
 * @author     Your Name <email@example.com>
 */
class Wp_User_Role_Customizer_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $wp_user_role_customizer The ID of this plugin.
     */
    private $wp_user_role_customizer;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $wp_user_role_customizer The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($wp_user_role_customizer, $version)
    {

        $this->wp_user_role_customizer = $wp_user_role_customizer;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

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

        wp_enqueue_style($this->wp_user_role_customizer, plugin_dir_url(__FILE__) . 'css/wp-user-role-customizer-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->wp_user_role_customizer . 'bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

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

        wp_enqueue_script($this->wp_user_role_customizer . 'popper', "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js", array('jquery'), $this->version, false);
        wp_enqueue_script($this->wp_user_role_customizer . 'bootstrap', plugin_dir_url(__FILE__) . 'js/vendor/bootstrap.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->wp_user_role_customizer, plugin_dir_url(__FILE__) . 'js/wp-user-role-customizer-admin.js', null, $this->version, false);


    }

    public function post_add_role(){
        if(isset($_POST['add-new-role-submit']) && isset($_POST['rolename']) &&  isset($_POST['slugs'])  &&  isset($_POST['permission']) &&  isset($_POST['preroles'])){
            $role_slug = $this->slugify($_POST['rolename']);
            $role_name = $_POST['rolename'];
            $plugin_slugs = is_array($_POST['slugs']) ? (array)$_POST['slugs'] : $_POST['slugs'];
            $plugin_permissions = (array) $_POST['permission'];

            if(isset($_POST['preroles']) & $_POST['preroles'] !== "wurc-no-role-inherited" && is_string($_POST['preroles'])){

                $prerole = get_role($_POST['preroles']);
                $capabilities = $prerole->capabilities;
            }
            if(!$this->role_exists($role_name, $role_slug)){



                $capabilities['level_0'] = true;
                foreach($plugin_slugs as $slug){
                    $capabilities[$this->wp_user_role_customizer . '_'. $slug] = true;
                }

                foreach($plugin_permissions as $permission){
                    $capabilities[$permission] = true;
                }

                add_role(
                    $role_slug,
                    $role_name,
                    $capabilities
                );
                wp_redirect($_SERVER['HTTP_REFERER'].'&resp=success');

                exit();
            }
            else{
                echo '<h1>role exists</h1>';
                wp_redirect($_SERVER['HTTP_REFERER'].'&resp=roleexicst');

                exit();
            }

        }
        wp_redirect($_SERVER['HTTP_REFERER'].'&resp=roleexicst');

    }



    public function add_plugin_to_menu()
    {
        $curr_user = wp_get_current_user();
        $roles = (array )$curr_user->roles;
        if (in_array("administrator", $roles)) {
            add_management_page(
                'WP User Role Customizer',
                'WURC',
                'manage_options',
                'create-custom-role',
                array(&$this, 'admin_page')
            );

        }
    }

    public function admin_page()
    {

        include('partials/wp-user-role-customizer-admin-display.php');
        $this->enqueue_styles();
        $this->enqueue_scripts();
    }


        function remove_unwanted_menu(){
        global $menu, $submenu;
        // Check if user logged in
        if(!is_user_logged_in()){
            return 0;
        }
        // Make sure its not admin
        if(in_array("administrator", wp_get_current_user()->roles)){
            return 0;
        }
        // Get every capability of current user
        $allcaps = wp_get_current_user()->allcaps;


        // Get every access added from plugin
        $can_access = array();
        foreach($allcaps as $key=>$value){
            if(strpos($key,$this->wp_user_role_customizer) !== FALSE){

                array_push($can_access, substr($key, strlen($this->wp_user_role_customizer . "_"), strlen($key)));

            }

        }
        if(empty($can_access)){
            return 0;
        }

        // Go through every menu and submenu and remove if user has no access to it
        foreach($menu as $menu_item){
            //Make sure its not separator
            if(strpos($menu_item[2], 'separator') !== FALSE){
                continue;
            }
            //Check if submenu exists
            if(!isset($submenu[$menu_item[2]])){
                continue;
            }

            //Go trough ever submenu of menu
            foreach($submenu[$menu_item[2]] as $submenu_item){
                $isaccessable = false;
                foreach($can_access as $access_slug){
                    if(esc_html($submenu_item[2]) == esc_html($access_slug)){
                        $isaccessable = true;
                        break;
                    }

                }
                //Remove submenu if user has no access to it else leave it and add menu to accessable menus
                if(!$isaccessable){

                    remove_submenu_page($menu_item[2],$submenu_item[2]);
                }else{

                    array_push($can_access,$menu_item[2]);
                }
            }
            // Remove menus user has no access to
            if(!in_array($menu_item[2],$can_access)){

                remove_menu_page($menu_item[2]);
                continue;
            }
        }
    }

    // Checks whether or not role can access curent page



    private function get_editable_roles()
    {
        global $wp_roles;

        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);

        return $editable_roles;
    }

    private function remove_numbers($string)
    {
        return preg_replace('/[0-9]+/', '', $string);
    }

    private function slugify($text)
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
    private function role_exists($rolename){
        global $wp_roles;

        $roles = $wp_roles->roles;
        foreach($roles as $role){
            if($role['name'] == $rolename){
                return true;
            }
        }
        return false;
    }



}
