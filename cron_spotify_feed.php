<?php
/*REMOVE IN DEV*/
if( php_sapi_name() !== 'cli' ){die();}
/*END REMOVE IN DEV*/

date_default_timezone_set('UTC');

$current_time = date('c');

$month_ago = date('c',strtotime('-2 months'));
require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';

$wp_base = ABSPATH;

$keys = api_key_generator();
if( !isset($keys['spotify_id']) || !isset($keys['spotify_secret']) || !isset($keys['spotify_refresh'])) {
  echo 'aasdf';
  die();
}

if(file_exists($wp_base.'wp-content/feed_dump/spotify.json')) {
  $old_data = json_decode(file_get_contents($wp_base.'wp-content/feed_dump/spotify.json'),true);

  $start_time = $old_data['last_run'];
  $old_array = $old_data['items'];
} else {
  $old_array = [];
  $start_time = $month_ago;
}


//GET REFRESH
$headers = array(
            "Accept: */*",
            "Content-Type: application/x-www-form-urlencoded",
            "User-Agent: runscope/0.1",
            "Authorization: Basic " . base64_encode($keys['spotify_id'].':'.$keys['spotify_secret']));

$data = 'grant_type=refresh_token&refresh_token='.$keys['spotify_refresh'];

$ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
       $output = curl_exec($ch);
       if ($output === FALSE) {
         echo "cURL Error: " . curl_error($ch);
         die();
       }

       $response = json_decode($output, true);

       curl_close($ch);



$token = $response['access_token'];


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/player/recently-played?limit=50");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  'Authorization: Bearer '.$token
));
$output = curl_exec($ch);
if ($output === FALSE) {
  echo "cURL Error: " . curl_error($ch);
  die();
}
curl_close($ch);
$items = json_decode($output,true);

$items = $items['items'];

$track_blocks = [];
$track_it = [];

foreach($items as $k => $i) {
  $block = array(
    'timestamp' => $i['played_at'],
    'ID' => $i['track']['id']
  );
  $track_it[] = $block;
  if(count($track_it) === 20 || $k === (count($items) - 1)) {
    $track_blocks[] = $track_it;
    $track_it = [];
  }


}
$tracksFull = [];
$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  'Authorization: Bearer '.$token
));
$allTracks = [];
foreach($track_blocks as $tb) {
  $tids = array_map(function($k){
    return $k['ID'];
  }, $tb);
  $tids = implode(',',$tids);
  curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/tracks?ids=".$tids);
  $output = curl_exec($ch);
  if ($output === FALSE) {
    echo "cURL Error: " . curl_error($ch);
    die();
  }
  $tracks = json_decode($output,true);
  $allTracks = array_merge($allTracks, $tracks['tracks']);

}

curl_close($ch);
$tracksClean = [];
foreach($items as $k => $i) {
  if(strtotime($i['played_at']) < strtotime($start_time)) {
    continue;
  }
  $track = $allTracks[$k];

  $artists = [];
  foreach($track['album']['artists'] as $a) {
   $artists[] = $a['name'];
  }

  $tracksClean[] = array(
    'timestamp' => strtotime($i['played_at']),
    'ID' => $i['track']['id'],
    'title' => $track['name'],
    'img' =>  $track['album']['images'][0]['url'],
    'album' => array(
      'ID' => $track['album']['id'],
      'title' => $track['album']['name'],
      'artists' => $artists,
      'img' => $track['album']['images'][0]['url']
    )
  );
}

$tracksClean = array_reverse($tracksClean);
foreach($tracksClean as $t) {
  array_unshift($old_array,$t);
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
if(!file_exists($wp_base.'wp-content/feed_dump/')) {
  mkdir($wp_base.'wp-content/feed_dump', 0777);
}
var_dump($traktObject);
file_put_contents($wp_base.'wp-content/feed_dump/spotify.json', json_encode($traktObject));
die();
