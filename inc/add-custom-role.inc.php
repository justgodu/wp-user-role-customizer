<?php
require_once("../../../../wp-load.php");


if(isset($_POST['add-new-role-submit']) && isset($_POST['rolename']) &&  isset($_POST['slugs'])  &&  isset($_POST['permission']) &&  isset($_POST['preroles'])){
    $role_slug = slugify($_POST['rolename']);
    $role_name = $_POST['rolename'];
    $plugin_slugs = (array) $_POST['slugs'];
    $plugin_permissions = (array) $_POST['permission'];
    

    if(isset($_POST['preroles']) & $_POST['preroles'] !== "wurc-no-role-inherited" && is_string($_POST['preroles'])){
    
    $prerole = get_role($_POST['preroles']);
    $capabilities = $prerole->capabilities;
    }
    if(!role_exists($role_name, $role_slug)){
        
    

    $capabilities['level_0'] = true;
    foreach($plugin_slugs as $slug){
        $capabilities[constant("ROLE_CAP_PREFIX") . $slug] = true;
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