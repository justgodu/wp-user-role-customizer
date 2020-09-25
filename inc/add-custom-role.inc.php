<?php
require_once("../../../../wp-load.php");
//define("ROLE_CAP_PREFIX", 'plugin_access_');

if(isset($_POST['add-new-role-submit'])){
    $role_slug = slugify($_POST['rolename']);
    $role_name = $_POST['rolename'];
    $plugin_slugs = (array) $_POST['slugs'];
    $plugin_permissions = (array) $_POST['permission'];
    print_r( (array) $_POST['slugs']);
    if(!role_exists($role_name, $role_slug)){
        
    
    $editor = get_role('editor');
    $capabilities = $editor->capabilities;
    $capabilities['level_1'] = true;
    foreach($plugin_slugs as $slug){
        $capabilities[constant("ROLE_CAP_PREFIX") . $slug] = true;
    }

    foreach($plugin_permissions as $permission){
        $capabilities[$permission] = true;
    }
    print_r($capabilities);
    add_role(
        $role_slug,
        $role_name,
        $capabilities
    );

    header("Location: ".$_SERVER['HTTP_REFERER'].'&resp=success');
    exit();
}
else{
    echo '<h1>role exists</h1>';
    header("Location: ".$_SERVER['HTTP_REFERER'].'&resp=roleexicst');
    exit();
}
  
}




function role_exists($rolename){
    global $wp_roles;
	
    $roles = $wp_roles->roles;
    foreach($roles as $role){
        if($role['name'] == $rolename){
            return true;
        }
    }
	return false;
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