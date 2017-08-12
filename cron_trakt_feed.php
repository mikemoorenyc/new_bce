<?php
/*
- get start timestamp
- get new items
- reverse array
- unshift each to front of array
- remove items that are older than a month
- check if count is over 50
- if over 50, get difference
- for loop and pop difference amount
- get currentTIME
- save new timestamp
- save new array

*/
date_default_timezone_set('UTC');
$current_time = date('c');

$month_ago = date('c',strtotime('-1 month'));

require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';

$keys = api_key_generator();
if( !isset($keys['trakt']) || !isset($keys['trakt_username'])) {
  die();
}

$start = time() ;
$end = strtotime('-1 month');


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.trakt.tv/users/".$keys['trakt_username']."/history/?start_at=".urlencode($month_ago).'&end_at='.$current_time);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "trakt-api-version: 2",
  "trakt-api-key: ".$keys['trakt']
));

$output = curl_exec($ch);

if ($output === FALSE) {
  echo "cURL Error: " . curl_error($ch);
  die();
}
curl_close($ch);

$items = json_decode($output);

$traktList = [];
$bingeID = 0;
$bingeCount = 1;
foreach($items as $i) {


  if($i->type!== 'movie' && $i->type !== 'episode') {
    continue;
  }
  if($i->type === 'movie') {
   $traktList[] = array(
    'title' => $i->movie->title,
    'tmdbID' => $i->movie->ids->tmdb,
    'type' => 'movie',
    'timestamp' => $i->watched_at
   );
   $bingeID = 0;
   $bingeCount = 1;
   continue;
  }

  $showID = $i->show->ids->tmdb;

  if($showID === $bingeID) {
    $bingeCount++;

    $traktList[count($traktList) - 1]['bingeCount'] = $bingeCount;

  } else {
   $bingeCount = 1;
   $bingeID = $showID;
   $traktList[] = array(
    'showTitle' => $i->show->title,
    'episodeData' => $i->episode,
    'showID' => $showID,
    'type' => 'show',
    'timestamp' => $i->watched_at,
    'bingeCount' => $bingeCount
   );
  }

}
var_dump($traktList);
die();
echo json_encode($traktList);
die();
$wp_base = get_home_path();
if(!file_exists($wp_base.'wp-content/feed_dump/')) {
  mkdir($wp_base.'wp-content/feed_dump', 0777);
}
file_put_contents($wp_base.'wp-content/feed_dump/trakt.json', json_encode($traktList));
die();

?>
