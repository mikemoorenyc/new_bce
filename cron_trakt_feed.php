<?php
require_once("../../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';

$keys = api_key_generator();
if( !isset($keys['trakt']) || !isset($keys['trakt_username'])) {
  die();
}


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.trakt.tv/users/".$keys['trakt_username']."/history/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "trakt-api-version: 2",
  "trakt-api-key: ".$keys['trakt']
));

$response = curl_exec($ch);
curl_close($ch);

$items = json_decode($response);
$traktList = [];
$bingeID = 0;
$bingeCount = 1;
foreach($items as $i) {
  if($i['type']!== 'movie' && $i['type'] !== 'episode') {
    continue;
  }
  if($i['type'] === 'movie') {
   $traktList[] = array(
    'title' => $i['movie']['title'],
    'tmdbID' => $i['movie']['ids']['tmdb'],
    'type' => 'movie',
    'timestamp' => $i['watched_at']
   ); 
   $bingeID = 0;
   $bingeCount = 1;
   continue;
  }
  
  $showID = $i['show']['ids']['tmdb'];

  if($showID === $bingeID) {
    $bingeCount++

    $traktList[count($traktList) - 1]['bingeCount'] = $bingeCount;

  } else {
   $bingeCount = 1;
   $bingeID = $showID;
   $traktList[] = array(
    'showTitle' => $i['show']['title'],
    'episodeData' => $i['episode'],
    'showID' => $showID,
    'type' => 'show',
    'timestamp' => $i['watched_at']
   );
  }
  
}

$wp_base = get_home_path();
if(!file_exists($wp_base.'wp-content/feed_dump/')) {
  mkdir($wp_base.'wp-content/feed_dump/', 0777);
}
file_put_contents($wp_base.'wp-content/feed_dump/goodreads.json', json_encode($bookUpdates));
die();

?>
