<?php
require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';

$keys = api_key_generator();

if( !isset($keys['goodreads']) || !isset($keys['goodreads_url'])) {

  die();
}

$status = new SimpleXMLElement(file_get_contents($keys['goodreads_url']));

$bookUpdates = [];
foreach($status->channel->item as $i) {
 if(strpos($i->guid,'Review')!== false || strpos($i->guid,'User')!== false ) {
  continue;
 }
 $updateID =  str_replace("ReadStatus","",$i->guid);
 $updateID = str_replace("UserStatus","",$i->guid);
 if(strpos($i->guid,'UserStatus')!== false) {

  $apiURL = 'https://www.goodreads.com/user_status/show/'.$updateID.'?format=xml&key='.$keys['goodreads'];


 } else {
   $apiURL = 'https://www.goodreads.com/read_statuses/'.$updateID.'?format=xml&key='.$keys['goodreads'];
 }

 $readStatus = new SimpleXMLElement(file_get_contents($apiURL));

 if(strpos($i->guid,'UserStatus')!== false) {
  $readStatus = $readStatus->user_status;
 } else {
   $readStatus = $readStatus->read_status;
 }
 $readStatus = $readStatus->read_status;

 $update = array(
  'status' => $i->guid,
  'percent' => $readStatus->percent.'',
  'title' => $readStatus->book->title.'',
   'img' => $readStatus->book->image_url.'',
   'timestamp' => $readStatus->updated_at.'',
   'status' => $readStatus->status.''
 );

 $bookUpdates[] = $update;

}
var_dump($bookUpdates);
die();
$wp_base = get_home_path();
if(!file_exists($wp_base.'wp-content/feed_dump/')) {
  mkdir($wp_base.'wp-content/feed_dump', 0777);
}
file_put_contents($wp_base.'wp-content/feed_dump/goodreads.json', json_encode($bookUpdates));
die();
