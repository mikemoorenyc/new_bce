<?php
chdir(dirname(__FILE__));
/*REMOVE IN DEV*/
if( php_sapi_name() !== 'cli' ){die();}
/*END REMOVE IN DEV*/
date_default_timezone_set('UTC');
require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';
include_once('function_httpcheck.php');


$wp_base = ABSPATH;
$keys = api_key_generator();


function createTerm($term) {
  if(!term_exists($term, 'consumed_types')) {
    wp_insert_term(
      $term, // the term
      'consumed_types', // the taxonomy
      array('slug' => strtolower($term))
    );
  }
}

function dateMaker($b) {
  $stamp = $b;
  $datetime = new DateTime(date('Y-m-d H:i:s',$stamp));
  $ny_time = new DateTimeZone('America/New_York');
  $datetime->setTimezone($ny_time);
  return array(
    'gmt' => date('Y-m-d H:i:s',$stamp),
    'est' => $datetime->format('Y-m-d H:i:s')
  );
}

function bingeCheck($currentID, $currentTimestamp, $itemID, $itemTimestamp) {
  $binge = true;
  if ($currentID !== $itemID) {
   return false;
  }
  $currentDate = date('j-n-Y',intval($currentTimestamp));
  $itemDate = date('j-n-Y',intval($itemTimestamp));
  if($currentDate !== $itemDate)  {
   return false;
  }
  return true;
}

function returnBatch($types, $oldest_play) {
  $posts = get_posts(array(
    'posts_per_page'   => -1,
    'post_type' => 'consumed',
    'tax_query' => array(
      array(
        'taxonomy' => 'consumed_types',
        'field'    => 'slug',
        'terms'    => $types
      ),
    ),
    'date_query' => array(
      'after'=> date('c', strtotime('-2 days', $oldest_play))
    )
  ));
  if(empty($posts)) {
    return [];
  }
  return $posts;

}

function comparePosts($types, $oldest_play) {
  $compare_posts = returnBatch($types, $oldest_play);
  $GUIDs = array();
  foreach($compare_posts as $p) {
   $data = json_decode($p->post_content,true);
   if($data['GUID']) {
     foreach($data['GUID'] as $guid) {
       $GUIDs[] = (string)$guid;
     }
   }

  }

  return array(
    'posts' => $compare_posts,
    'GUID' => $GUIDs
  );
}


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
