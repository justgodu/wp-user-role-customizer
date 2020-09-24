<?php 
/**
 * Plugin Name:       WP User Role Customizer  
 * Description:       Create some custom roles for your wordpress website
 * Version:           0.0.1
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
      
      list_all_active_plugins();
        
    // list_all_menu_items();
  
    echo '</div>';
    
}



$administrator = "null";
define("ROLE_CAP_PREFIX", 'plugin_access_');





// Lists all Currently Active Plugins in List
function list_all_active_plugins(){
    // $the_plugs = get_option('active_plugins');
    
    // print_r($the_plugs);
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
    //foreach($the_plugs as $key => $value) {
    foreach($menu as $key => $value) {
        //$string = explode('/',$value);
        //print_r($value);
        if($value[0] === ''){
            continue;
        }

        echo '<div class="menu-checkboxes column">';
        if(isset($submenu[$value[2]])){
            echo '<input type="checkbox" name="slugs[]" value = "'. array_values($submenu[$value[2]])[0][2].'"/>';
        }else{
            echo '<input type="checkbox" name="slugs[]" value = "'. esc_html($value[2]) .'"/>';
        }
        echo '<label  class="single-active-plugin-label" for="' .$value[0].'">'. $value[0] .' </label>';
        if(isset($submenu[$value[2]])){
            foreach($submenu[$value[2]] as $sm){    
                echo '<div class="sub-menu-checkboxes">';
                
                echo '<input type="checkbox" name="slugs[]" value = "'. urlize($sm[2]).'"/>';
                echo '<label  class="single-active-plugin-label" for="' . $sm[0].'">'. $sm[0].' </label>';
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

function list_all_menu_items(){
    
        
    global $submenu, $menu, $pagenow;

    print_r($menu);
    foreach($menu as $key=>$value){
        if(isset($value[0]) && $value[0] != ''){
        echo '<h1>'. $key .", ". $value[0] .", ". urlize($value[2]) . '</h1> </br>';
        
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

function urlize($slug){
    if($slug == "woocommerce"){
        $slug = "wc-admin";
    }
    if(strpos($slug, ".php") === FALSE){
        $slug = 'page=' . $slug;
    }
    return $slug;
}

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

function js_scripts(){
    echo'
    <script>
        
    </script>
    ';
}

// Checks whether or not role can access curent page
add_action('init', 'check_role_access');
function check_role_access(){
    
    if(wp_get_current_user()->roles[0] == "administrator"){
        return 0;
    }
    $uri = wp_parse_url($_SERVER['REQUEST_URI']);
    $allcaps = wp_get_current_user()->allcaps;
    if (isset($uri['query'])) {
        parse_str($uri['query'], $params);
    }

    

    print_r($uri);
    $can_access = array();
    foreach($allcaps as $key=>$value){
        if(strpos($key,constant("ROLE_CAP_PREFIX")) !== FALSE){
            
            array_push($can_access, substr($key, strlen(constant("ROLE_CAP_PREFIX")), strlen($key   )));

        }
    }
    print_r($can_access);


    
    $path  = (isset($uri['path']) ? $uri['path'] : null);
    $query = (isset($uri['query']) ? $uri['query'] : null);
    
    if( strpos($path,'.php') !== FALSE || $query !== null){
        foreach($can_access as $menu_slug){
        
        if(strpos($query, $menu_slug) ===FALSE && strpos($path,$menu_slug) ===FALSE && strpos($path,'about.php') === FALSE){
            echo 'query: '.strpos($query, $menu_slug) . ' path: ' . strpos($path,$menu_slug) . ' about: ' . strpos($path,'about.php'). ' ' .$query.' '.$path .' '.  $menu_slug;
            wp_die('You Cannot Access This Page', 'Access Denied',);
        }
    }
}
    //$match = (!empty($path) ? $object->findMatch($path, $params) : false);
}

