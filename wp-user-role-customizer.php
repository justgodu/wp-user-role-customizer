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
    //wurc_list_all_menu_items();
  
    echo '</div>';
    
}



$administrator = "null";
define("ROLE_CAP_PREFIX", 'plugin_access_');





// Creates from of creating new custom role
function wurc_plugin_page_role_creator(){

    do_action('wurc_page_styles');
    if(isset($_GET['resp'])){
        
        $resp = $_GET['resp'];

        if($resp == "success"){
            echo '<h3 class="wurc-success">Succesfully added</h3>';
        }
        else if($resp == "roleexicst"){
            echo '<h3 class="wurc-fail">Role already exists</h3>';
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

        echo '<div class="menu-checkboxes column">';
        if(isset($submenu[$value[2]])){
            echo '<input type="checkbox" name="slugs[]" value = "'. array_values($submenu[$value[2]])[0][2].'"/>';
        }else{
            echo '<input type="checkbox" name="slugs[]" value = "'. $value[2] .'"/>';
        }
        
        echo '<label  class="single-active-plugin-label">'. wurc_remove_numbers($value[0]) .' </label>';
        if(isset($submenu[$value[2]])){
            foreach($submenu[$value[2]] as $sm){    
                echo '<div class="sub-menu-checkboxes">';
                
                echo '<input type="checkbox" name="slugs[]" value = "'. wurc_urlize($sm[2]).'"/>';
                echo '<label  class="single-active-plugin-label">'. wurc_remove_numbers($sm[0]).' </label>';
                echo '</div>';
            }
        }
        echo '</div>';
    }
    echo '</div>';
    echo '<button type="submit" name="add-new-role-submit">Add Role</button>'; 
    echo '</from>';
    do_action('after_wurc_page_form');
}

// function can_use_plugin($plugin_slug){
//     $curr_user = wp_get_current_user();
//     $caps = (array ) $curr_user->allcaps;
//     foreach($caps as $cap){
//         if($cap == constant("ROLE_CAP_PREFIX") . $plugin_slug){
//             return true;
//         }
//     }
//     return false;
// }

//Lists all currently Avaliable menus (FOR DEBUGING)
function wurc_list_all_menu_items(){
    
        
    global $submenu, $menu, $pagenow;

    print_r($menu);
    foreach($menu as $key=>$value){
        if(isset($value[0]) && $value[0] != ''){
        echo '<h1>'. $key .", ". $value[0] .", ". wurc_urlize($value[2]) . '</h1> </br>';
        
        // foreach($value as $k=>$val){
            
        //     echo '<h3>' .$k .' '. urlize($val) . '</h3> </br>';
        // }
        echo '<h3>' . print_r($submenu[$value[2]]) . '</h3>'; 
    }
    }
        // for($i = 0; $i< count($menu); $i++){
            
        //     echo '<h3>'. $menu[$i][0] . ' '. $menu[$i][2] . '</h3>' . '<pre>'. $menu[$i][1] . '</pre>';
        //     for($a = 1; $a < count($submenu); $a++){
        //        // echo '<pre style="margin-left:20px">'.$submenu[$a][2] . '</pre>';
        //     }
        // }
        
        // print_r(get_plugins());
            

       
}


// Adds styles to the plugin page
add_action('wurc_page_styles', 'call_wurc_page_styles');

function call_wurc_page_styles(){
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
        .column {
            float: left;
            width: 33.33%;
            border-bottom: 1px black solid;
        }
            
            
        .row:after {
            content: "";
            display: table;
            clear: both;
            
        }
    </style>';
}

function wurc_js_scripts(){
    echo'
    <script>
        
    </script>
    ';
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
    if( strpos($path,'.php') !== FALSE || $query !== null){
        foreach($can_access as $menu_slug){
            if(strpos($query, $menu_slug) === FALSE && strpos($path,$menu_slug) === FALSE && strpos($path,'about.php') === FALSE){
              //  echo 'query: '.strpos($query, $menu_slug) . ' path: ' . strpos($path,$menu_slug) . ' about: ' . strpos($path,'about.php'). ' query: ' .$query.' path: '.$path .' menuslug: '.  $menu_slug;
                
            }else {
                $count++;
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
    

    // echo '<h1> All Caps </h1>';
    // print_r($allcaps);
    // echo '<h1> All menu </h1>';
    // print_r($menu);
    // echo '<h1> All submenu </h1>';
    // print_r($submenu);
    
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