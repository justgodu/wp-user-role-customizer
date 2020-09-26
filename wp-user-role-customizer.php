<?php 
/**
 * Plugin Name:       WP User Role Customizer  
 * Description:       Create some custom roles for your wordpress website
 * Version:           0.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nika Goduadze
 * Author URI:        https://github.com/justgodu
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

 //Adds Plugin page on Admin menu
 add_action('admin_menu', 'add_wurc_plugin_to_menu');
function add_wurc_plugin_to_menu(){
    $curr_user = wp_get_current_user();
    $roles = (array ) $curr_user->roles;
    if(in_array("administrator", $roles)){
    add_submenu_page(
        'tools.php',
        'WP User Role Customizer',
        'WURC',
        'manage_options',
        'create-custom-role',
        'wp_user_role_customizer_page'
    );

}
}








// Plugin page html
function wp_user_role_customizer_page(){
   echo '
    <div class="wrap">
      <h1>'. esc_html( get_admin_page_title() ).'</h1>';
      
      wurc_plugin_page_role_creator();
    //
    //echo '<div class="card">'.wurc_list_all_menu_items().'</div>';
  
    echo '</div>';
    
}



$administrator = "null";
define("ROLE_CAP_PREFIX", 'plugin_access_');





// Creates from of creating new custom role
function wurc_plugin_page_role_creator(){
    add_action('wp_enqueue_scripts','wurc_js_scripts');
    do_action('wurc_page_styles');
    if(isset($_GET['resp'])){
        
        $resp = $_GET['resp'];

        if($resp == "success"){
            echo '<h3 class="text-success">Succesfully added</h3>';
        }
        else if($resp == "roleexicst"){
            echo '<h3 class="text-danger">Role already exists</h3>';
        }
    }
    global $submenu, $menu;
    
    $roles = wurc_get_editable_roles();
    
    echo '<form onload="onLoad();" name="add-role" class="list-of-active-plugins" action = "'. plugins_url( '/inc/add-custom-role.inc.php', __FILE__ ).'" method=post> ';
    echo '<label class="" for="rolename">New role name: </label>';
    echo '<input class="role-name-input" type="text" name="rolename">';
    echo '<label for="preroles">Inherit permissions from: </label>';
    echo '<select name="preroles">';
        echo '<option value="wurc-no-role-inherited">No role</option>';
        foreach($roles as $role_slug => $role){
            
         echo '<option value ="'.$role_slug.'">'.$role['name'].'</option>';
        }
    
    echo '</select>';
    echo '<div class="row">';
    
    foreach($menu as $key => $value) {
    
        if($value[0] === ''){
            continue;
        }

        echo '<div class="menu-checkboxes col-md-3 card dropdown">';
        
        if(isset($submenu[$value[2]])){
           
           echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           '. wurc_remove_numbers($value[0]) .'
      </button>';
        }else{
            echo '<input  type="checkbox" onClick="handleChange(this,\''. $value[1] .'\' );"  name="slugs[]" value = "'. $value[2] .'"/>';
            echo '<label  class="single-active-plugin-label">'. wurc_remove_numbers($value[0]) .' </label>';
           
        }
        
        
        
        echo '<div class="dropdown-menu">';
        if(isset($submenu[$value[2]])){
            foreach($submenu[$value[2]] as $sm){    
                echo '<div class="sub-menu-checkboxes dropdown-item">';
                
                echo '<input type="checkbox" onClick="handleChange(this,\''. $sm[1] .'\');"  name="slugs[]" value = "'. wurc_urlize($sm[2]).'"/>';
               
                echo '<label  class="single-active-plugin-label">'. wurc_remove_numbers($sm[0]).' </label>';
                echo '</div>';
            }
        }
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '<div id="hiddeninput" style="display:none;">';
    echo '</div>';
    echo '<button class="btn btn-primary wurc-submit-button" type="submit" name="add-new-role-submit">Add Role</button>'; 
    echo '</from>';


    do_action('after_wurc_page_form');
    
}

//Lists all currently Avaliable menus (FOR DEBUGING)
// add_action('after_wurc_page_form', 'wurc_list_all_menu_items');
function wurc_list_all_menu_items(){
    
        
    global $submenu, $menu, $pagenow;
    
    print_r($menu);
    foreach($menu as $key=>$value){
        if(isset($value[0]) && $value[0] != ''){
        echo '<h1>'. $key .", ". $value[0] .", ". wurc_urlize($value[2]) . '</h1> </br>';
        echo '<h3>' . print_r($submenu[$value[2]]) . '</h3>'; 
    }
    }
     
            

       
}
// Adds styles to the plugin page
add_action('wurc_page_styles', 'call_wurc_page_styles');

function call_wurc_page_styles(){
    echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">';
    echo '<style>
    
        .list-of-active-plugins{
            display: flex;
            flex-direction: column;
        }
        .role-name-input{
            max-width: 120px;
        }
        .single-active-plugin-label{
            font-size: 15px;
            color: black;
            weight: bold;
            display: inline-block;
        }
   
        .menu-checkboxes{
            display:block;
            margin-top: 10px;
        }
        .sub-menu-checkboxes{
            display:block;
            margin-top: 10px;
            margin-left: 10px;
            
        }
         label::first-letter {
            text-transform: uppercase;
        }
        .wurc-success{
            color: green;
        }
        .wurc-fail{
            color: red;
        }
        .wurc-submit-button{
            max-width: 200px;
            margin: 10px;
            
        }
    </style>';
}

//Include scripts on plugin page
add_action('after_wurc_page_form', 'wurc_inner_scipts');
function wurc_inner_scipts(){
    echo'
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    ';

    echo '<script src="'.plugins_url( '/js/wurc-scripts.js', __FILE__ ).'"</script>';

}


function wurc_js_scripts() {
    
  //  wp_enqueue_script( 'wurc-scripts', plugins_url( '/js/wurc-scripts.js', __FILE__ ));
}








// Checks whether or not role can access curent page
add_action('init', 'wurc_check_role_access');
function wurc_check_role_access(){
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
        if(strpos($key,constant("ROLE_CAP_PREFIX")) !== FALSE){
            //Remove ROLE_CAP_PREFIX from capability name to get menu-slug
            array_push($can_access, substr($key, strlen(constant("ROLE_CAP_PREFIX")), strlen($key)));

        }
    }
    
  

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
            if(strpos('?'.$query .' ',$menu_slug. ' ') !== FALSE || strpos($path, '/' .$menu_slug) !== FALSE || strpos($path.'?'.$query .' ',$menu_slug. ' ') !== FALSE){
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

// add_action('admin_menu', 'wurc_debbug_admin_menu');
// Debuggin
function wurc_debbug_admin_menu(){
    if(in_array("administrator", wp_get_current_user()->roles)){
        return 0;
    }
    
    $allcaps = wp_get_current_user()->allcaps;
    print_r($allcaps);
}

// Remove menus user role has no access to 
add_action('admin_menu', 'wurc_remove_unwanted_menu');
function wurc_remove_unwanted_menu(){
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
        if(strpos($key,constant("ROLE_CAP_PREFIX")) !== FALSE){
          
            array_push($can_access, substr($key, strlen(constant("ROLE_CAP_PREFIX")), strlen($key)));
            
        }
      
    }

    // Go through every menu and submenu and remove if user has no access to it
    foreach($menu as $menu_item){
        //Make sure its not separator
        if(strpos($menu_item[2], 'separator') !== FALSE){
            continue;
        }
        //Go trough ever submenu of menu
        foreach($submenu[$menu_item[2]] as $submenu_item){
            //Remove submenu if user has no access to it else leave it and add menu to accessable menus
            if(!in_array($submenu_item[2],$can_access)){
                
                remove_submenu_page($menu_item[2],$submenu_item[2]);
            }else{
                //
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
// Fixes url to use it properly
function wurc_urlize($slug){
    if($slug == "woocommerce"){
        $slug = "wc-admin";
    }
    if(strpos($slug, ".php") === FALSE){
        $slug = 'page=' . $slug;
    }
    return $slug;
}

// Removes numbers from strings 
function wurc_remove_numbers($string){

    return preg_replace('/[0-9]+/', '', $string);

}
//Gets every  currently available roles
function wurc_get_editable_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);

    return $editable_roles;
}