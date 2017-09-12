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

//BOOKS
if(file_exists($wp_base.'wp-content/feed_dump/books.json')) {
  $workingArray = json_decode(file_get_contents($wp_base.'wp-content/feed_dump/books.json'),true);
} else {
  $workingArray = array();
}
$status = new SimpleXMLElement(file_get_contents('https://www.goodreads.com/user/updates_rss/'.$keys['goodreads_uid'].'?key=18ioDaauDhEjysrttqWKDR03F_rvL_JFKT4MUW5jz8sl5px7'));
$GUIDs = array_map(function($i){
  return $['GUID'];
},$workingArray);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

function imageReplacer($o_URL,$isbn, $type = 'ISBN') {
  $a_URL = 'http://images.amazon.com/images/P/'.$isbn.'.01.LZZZZ.jpg';
  if($size[0] > 50) {
    return $o_URL;
  }
  return $a_URL;
}

foreach($status->channel->item as $i) {
  if(in_array($i->guid,$GUIDs) || strpos($i->guid,'Review')!== false ) {
   continue; 
  }
  $updateID =  str_replace("ReadStatus","",$i->guid);
  $updateID = str_replace("UserStatus","",$updateID);
  if(strpos($i->guid,'UserStatus')!== false) {
    $apiURL = 'https://www.goodreads.com/user_status/show/'.$updateID.'?format=xml&key='.$keys['goodreads'];
  } else {
    $apiURL = 'https://www.goodreads.com/read_statuses/'.$updateID.'?format=xml&key='.$keys['goodreads'];
  }
  curl_setopt($ch, CURLOPT_URL, $apiURL);
  $xml = curl_exec($ch);
  if($xml === false) {
    continue; 
  }
  $readStatus = new SimpleXMLElement($xml);
  if(strpos($i->guid,'UserStatus')!== false) {
    $readStatus = $readStatus->user_status;
  } else {
    $readStatus = $readStatus->read_status;
  }
  $authors = [];
  foreach($readStatus->book->authors->author as $a) {
    $authors[] = $a->name.'';
  }
  $imgURL = $readStatus->book->image_url.'';
  if(strpos($imgURL, 'nophoto') !== false) {
    if(!empty($readStatus->book->isbn.'')) {
      $imgURL = imageReplacer($imgURL, $readStatus->book->isbn.'');
    } else {
      if(!empty($readStatus->book->isbn13.'')) {
      $imgURL = imageReplacer($imgURL, $readStatus->book->isbn13.'');
      }
    }
  }
  
  $update = array(
    'percent' => $readStatus->percent.'',
    'title' => $readStatus->book->title.'',
    'img' => $imgURL,
    'timestamp' => strtotime($readStatus->updated_at.''),
    'status' => $readStatus->status.'',
    'type' => 'book',
    'authors' => $authors,
    'inDB' => false
  );
  $workingArray[] = $update;  
}

$workingArray = array_filter()





?>
