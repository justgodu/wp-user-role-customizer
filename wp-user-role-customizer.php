<?php 
/**
 * Plugin Name:       WP User Role Customizer  
 * Description:       Create some custom roles for your wordpress website
 * Version:           0.0.2
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
    
    
    echo '<form class="list-of-active-plugins" action = "'. plugins_url( '/inc/add-custom-role.inc.php', __FILE__ ).'" method=post> ';
    echo '<label class="" for="rolename">New role name: </label>';
    echo '<input class="role-name-input" type="text" name="rolename">';
    echo '<div class="row">';
    
    foreach($menu as $key => $value) {
    
        if($value[0] === ''){
            continue;
        }

        echo '<div class="menu-checkboxes col-md-3 card dropdown">';
        
        if(isset($submenu[$value[2]])){
            // echo '<input type="checkbox" onClick="handleChange(this,\''.array_values($submenu[$value[2]])[0][1].' \');"  name="slugs[]" value = "'. array_values($submenu[$value[2]])[0][2].'"/>';
           // echo '<input name="permission[]" type="hidden" value = "'. array_values($submenu[$value[2]])[0][1] .'"/>';
        //    echo '<label  class="single-active-plugin-label">'. wurc_remove_numbers($value[0]) .' </label>';
           echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           '. wurc_remove_numbers($value[0]) .'
      </button>';
        }else{
            echo '<input  type="checkbox" onClick="handleChange(this,\''. $value[1] .'\' );"  name="slugs[]" value = "'. $value[2] .'"/>';
            echo '<label  class="single-active-plugin-label">'. wurc_remove_numbers($value[0]) .' </label>';
           // echo '<input name="permission[]" type="hidden" value = "'. $value[1] .'"/>';
        }
        
        
        
        echo '<div class="dropdown-menu">';
        if(isset($submenu[$value[2]])){
            foreach($submenu[$value[2]] as $sm){    
                echo '<div class="sub-menu-checkboxes dropdown-item">';
                
                echo '<input type="checkbox" onClick="handleChange(this,\''. $sm[1] .'\');"  name="slugs[]" value = "'. wurc_urlize($sm[2]).'"/>';
               // echo '<input name="permission[]" type="hidden" value = "'. $sm[1] .'"/>';
                echo '<label  class="single-active-plugin-label">'. wurc_remove_numbers($sm[0]).' </label>';
                echo '</div>';
            }
        }
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '<div id="hiddeninput" style="displa:none;">';
    echo '</div>';
    echo '<button class="btn btn-primary wurc-submit-button" type="submit" name="add-new-role-submit">Add Role</button>'; 
    echo '</from>';
    do_action('after_wurc_page_form');
    
}
//Lists all currently Avaliable menus (FOR DEBUGING)
function wurc_list_all_menu_items(){
    
        
    global $submenu, $menu, $pagenow;
    echo '<h1> IDK </h1>';
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
            
        }
    </style>';
}
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
    
    wp_enqueue_script( 'wurc-scripts', plugins_url( '/js/wurc-scripts.js', __FILE__ ));
}








// Checks whether or not role can access curent page
add_action('init', 'wurc_check_role_access');
function wurc_check_role_access(){
    if(!is_user_logged_in()){
        return 0;
    }
    if(in_array("administrator", wp_get_current_user()->roles)){
        return 0;
    }
    $uri = wp_parse_url($_SERVER['REQUEST_URI']);
    $allcaps = wp_get_current_user()->allcaps;
    if (isset($uri['query'])) {
        parse_str($uri['query'], $params);
    }

    

    
    $can_access = array();
    
    foreach($allcaps as $key=>$value){
        if(strpos($key,constant("ROLE_CAP_PREFIX")) !== FALSE){
            
            array_push($can_access, substr($key, strlen(constant("ROLE_CAP_PREFIX")), strlen($key)));

        }
    }
  

    
    
    $path  = (isset($uri['path']) ? $uri['path'] : null);
    $query = (isset($uri['query']) ? $uri['query'] : null);
    $count = 0;
    if(strpos($path, '/wp-admin/about.php') !== FALSE){
        wp_redirect(get_dashboard_url());
        exit();
    }
    if( strpos($path,'.php') !== FALSE || $query !== null){
        foreach($can_access as $menu_slug){
            if(strpos($path.'?'.$query,$menu_slug) !== FALSE || strpos($path,$menu_slug) !== FALSE){
                $count++;
            break;
                
        }
    }
        if($count === 0){
            wp_die('You Cannot Access This Page', 'Access Denied',['exit'=> true]);
        }
}

    
}


// Remove menus user role has no access to 
add_action('admin_menu', 'wurc_remove_unwanted_menu');
function wurc_remove_unwanted_menu(){
    global $menu, $submenu;

    if(!is_user_logged_in()){
        return 0;
    }
    if(in_array("administrator", wp_get_current_user()->roles)){
        return 0;
    }
    
    $allcaps = wp_get_current_user()->allcaps;
    

    
    
    
    $can_access = array();
    foreach($allcaps as $key=>$value){
        if(strpos($key,constant("ROLE_CAP_PREFIX")) !== FALSE){
        
            array_push($can_access, substr($key, strlen(constant("ROLE_CAP_PREFIX")), strlen($key)));

        }
    }
    foreach($menu as $menu_item){
         
   
        
        if(!in_array($menu_item[2],$can_access)){
            remove_menu_page($menu_item[2]);
            continue;
        }
        foreach($submenu as $submenu_parent){
            foreach($submenu_parent as $submenu_item)
            
            if(!in_array($submenu_item[2],$can_access)){
                remove_submenu_page($menu_item[2],$submenu_item[2]);
            }
        }
    }
}


function wurc_urlize($slug){
    if($slug == "woocommerce"){
        $slug = "wc-admin";
    }
    if(strpos($slug, ".php") === FALSE){
        $slug = 'page=' . $slug;
    }
    return $slug;
}


function wurc_remove_numbers($string){

    return preg_replace('/[0-9]+/', '', $string);

}