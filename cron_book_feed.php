<?php
chdir(dirname(__FILE__));
/*REMOVE IN DEV*/
if( php_sapi_name() !== 'cli' ){die();}
/*END REMOVE IN DEV*/



date_default_timezone_set('UTC');

$current_time = date('c');

$month_ago = date('c',strtotime('-1 month'));
require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';

$wp_base = ABSPATH;

$keys = api_key_generator();


function imageReplacer($o_URL,$isbn, $type = 'ISBN') {

  $a_URL = 'http://images.amazon.com/images/P/'.$isbn.'.01.LZZZZ.jpg';
  if($size[0] > 50) {
    return $o_URL;
  }
  return $a_URL;

}



if( !isset($keys['goodreads']) || !isset($keys['goodreads_uid'])) {

  die();
}

if(file_exists($wp_base.'wp-content/feed_dump/goodreads.json')) {
  $old_data = json_decode(file_get_contents($wp_base.'wp-content/feed_dump/goodreads.json'),true);

  $start_time = $old_data['last_run'];
  $old_array = $old_data['items'];
} else {
  $old_array = [];
  $start_time = $month_ago;
}

$status = new SimpleXMLElement(file_get_contents('https://www.goodreads.com/user/updates_rss/'.$keys['goodreads_uid'].'?key=18ioDaauDhEjysrttqWKDR03F_rvL_JFKT4MUW5jz8sl5px7'));


$bookUpdates = [];

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
foreach($status->channel->item as $i) {
  //var_dump($i);

 if(strpos($i->guid,'Review')!== false  ) {
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
   echo "cURL Error: " . curl_error($ch);
   die();
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
//IMAGE STUFF

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
$rating = null;
$book_id = $readStatus->book->id.'';
curl_setopt($ch, CURLOPT_URL, 'https://www.goodreads.com/review/show_by_user_and_book.xml?book_id='.($book_id).'&key='.$keys['goodreads'].'&user_id='.$keys['goodreads_uid']);
$rating_XML = new SimpleXMLElement(curl_exec($ch));

$r_num = intval($rating_XML->review->rating.'');
if($r_num > 0 ) {
 $rating = $r_num;
}



 $update = array(
  'percent' => $readStatus->percent.'',
  'title' => $readStatus->book->title.'',
   'img' => $imgURL,
   'timestamp' => strtotime($readStatus->updated_at.''),
   'status' => $readStatus->status.'',
   'type' => 'book',
   'authors' => $authors,
   'rating' => $rating
 );

 if(strtotime($readStatus->updated_at.'') < strtotime($start_time)) {
   continue;
 }

 $bookUpdates[] = $update;

}
curl_close($ch);
$bookUpdates = array_reverse($bookUpdates);

foreach($bookUpdates as $b) {
  array_unshift($old_array,$b);
}
$new_array = [];
foreach($old_array as $i) {
  if($i['timestamp'] >= strtotime('-2 months')) {
    $new_array[] = $i;
  }
}

$traktObject = array(
  'last_run' => $current_time,
  'items' => $new_array
);

var_dump($traktObject);




if(!file_exists($wp_base.'wp-content/feed_dump/')) {
  mkdir($wp_base.'wp-content/feed_dump', 0777);
}
file_put_contents($wp_base.'wp-content/feed_dump/goodreads.json', json_encode($traktObject));
die();
