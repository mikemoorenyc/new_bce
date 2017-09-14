<?php

$mediaType = 'spotify';
include_once('media_cron_header.php');

if( !isset($keys['spotify_id']) || !isset($keys['spotify_secret']) || !isset($keys['spotify_refresh'])) {
  die();
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
$track_fetch = [];
function trackFetch($tracks) {
  global $token;
  global $items;
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    'Authorization: Bearer '.$token
  ));
  $tids = array_map(function($k){
    return $k['ID'];
  }, $tracks);
  $tids = implode(',',$tids);
  curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/tracks?ids=".$tids);
  $output = curl_exec($ch);
  if($output === FALSE){return false;}
  $response = json_decode($output,true);
  $response = $response['tracks'];
  foreach($response as $t) {
    $id = $t['id'];
    foreach($items as $k => $i) {
      if($i['track']['id'] == $id) {
        $items[$k]['track_info'] = $t;
        break;
      }
    }
  }


}
foreach($items as $k => $i) {
  $GUID = $i['track']['id'].'_'.$i['played_at'];
  if(in_array($GUID,$GUIDs)) {
   continue;
  }
  $block = array(
    'ID' => $i['track']['id'],
    'item_id' => $k
  );
  $track_fetch[] = $block;
  if(count($track_fetch) === 20 || $k === (count($items) - 1)) {
    trackFetch($track_fetch);
    $track_fetch = [];
  }


}
//TRACK INFO GOT!!!!
foreach($items as $i) {
  $GUID = $i['track']['id'].'_'.$i['played_at'];
  if(in_array($GUID,$GUIDs)) {
   continue;
  }
  $info = $i['track_info'];
  $artists = array_map(function($a){
    return $a['name'];
  },$info['album']['artists']);
  $workingArray[] = array(
    'GUID' => $GUID,
    'ID' => $i['track']['id'],
    'timestamp' => strtotime($i['played_at']),
    'title' => $info['name'],
    'img' =>  $info['album']['images'][0]['url'],
    'album' => array(
      'ID' => $info['album']['id'],
      'title' => $info['album']['name'],
      'artists' => $artists,
      'img' => $info['album']['images'][0]['url']
    )

  );
}
var_dump($workingArray);
include 'media_cron_footer.php';




 ?>