<?php
chdir(dirname(__FILE__));
/*REMOVE IN DEV*/
if( php_sapi_name() !== 'cli' ){die();}
/*END REMOVE IN DEV*/
date_default_timezone_set('UTC');
require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';
$wp_base = ABSPATH;
$keys = api_key_generator();

/*
if(file_exists($wp_base.'wp-content/feed_dump/'.$mediaType.'.json')) {
  $workingArray = json_decode(file_get_contents($wp_base.'wp-content/feed_dump/'.$mediaType.'.json'),true);
} else {
  $workingArray = array();
}

$GUIDs = array_map(function($i){
  return $i['GUID'];
},$workingArray);
*/

 ?>
