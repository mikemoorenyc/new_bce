<?php
require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';
$wp_base = ABSPATH;
if(!file_exists($wp_base.'wp-content/feed_dump/trakt.json')) {
 die(); 
}
$keys = api_key_generator();
if( !isset($keys['tmdb']) ) {
  die();
}

$items = json_decode(file_get_contents($wp_base.'wp-content/feed_dump/trakt.json'),true)['items'];
$need_img = [];
foreach($item as $k => $i) {
 if(!$i['has_img']) {
  if($i['type'] === 'movie') {
    $add = $i;
    $add['key'] = $k;
    $need_img[] = $add; 
  }
  if($i['type'] === 'show') {
    $add = $i;
    $add['key'] = $k;
    $need_img[] = $add; 
    $add = $i;
    $add['key'] = $k;
    $add['type'] = 'episode';
    $need_img[] = $add; 
  }
 }
}
if(count($need_img) < 1) {
  die();
}
$get_array = [];
function in_get_array($i, $type) {
 global $get_array; 
 $in_array = false;
 foreach($get_array as $k => $g) {
  if($g['url'] === $i['url']) {
   $in_array = $k;
   break;
  }
 }
 return $in_array;
}
foreach($need_img as $k => $n) {
 if(count($get_array) > 25) {
  break; 
 }
 $u = [];
 if($n['type'] === 'movie') {
   $u = array(
    'type' => 'movie',
    'url' => 'https://api.themoviedb.org/3/movie/'.$n['ID']
   );
 }
 if($n['type'] === 'episode') {
   $u = array(
    'type' => 'episode',
    'url' => 'https://api.themoviedb.org/3/tv/'.$n['show']['ID'].'/season/'.$n['season'].'/episode/'.$n['number'];
   );
 }
 if($n['type'] === 'episode') {
   $u = array(
    'type' => 'show',
    'url' => 'https://api.themoviedb.org/3/tv/'.$n['show']['ID'];
   );
 }
  
 if(in_get_array($u) !== false) {
  $need_img[$k]['get_key'] = in_get_array($u) 
  continue;
 }
 $get_array[] = $u;
 $need_img[$k]['get_key'] = count($get_array) - 1;
 
}

//GET IMAGES
$ch = curl_init();
$end = '?api_key='.$keys['tmdb'].'&language=en-US';
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
foreach($get_array as $k => $g) {
  curl_setopt($ch, CURLOPT_URL, $g['url'].$end);
  $output = curl_exec($ch);
  if($output === false || json_decode($output,true)['status_code'] === 34) {
   $get_array[$k]['success'] = false;
   continue;
  }
  $response = json_decode($output,true);
  if($g['type'] === 'movie') {
    $get_array[$k]['returned_url']  = 'https://image.tmdb.org/t/p/w185'.$response['poster_path'];
  }
  if($g['type'] === 'show') {
    $get_array[$k]['returned_url']  = 'https://image.tmdb.org/t/p/w300'.$response['backdrop_path'];
  }
  if($g['type'] === 'episode') {
    $get_array[$k]['returned_url']  = 'https://image.tmdb.org/t/p/w300'.$response['still_path'];
  }
  $get_array[$k]['success'] = true;
}
curl_close($ch);

foreach($need_img as $k => $n) {
 $get = $get_array[$n['get_key']];
 if(!$get['success']) {
  continue;
 }
 if($n['type'] === 'movie' || $n['type'] === 'episode') {
  $items[$n['key']]['img'] = $get['returned_url'];
 }
 if($n['type'] === 'show') {
   $items[$n['key']]['show']['img'] = $get['returned_url'];
 }
 $items[$n['key']]['has_img'] = true;
}
var_dump($items);
file_put_contents($wp_base.'wp-content/feed_dump/trakt.json', json_encode($items));
die();
  


?>
