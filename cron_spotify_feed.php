<?php

require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';
$keys = api_key_generator();
if( !isset($keys['spotify_id']) || !isset($keys['spotify_secret']) || !isset($keys['spotify_refresh'])) {
  echo 'aasdf';
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
$items = json_decode($output);
$items = $items->items;
$track_blocks = [];
$track_it = [];

foreach($items as $k => $i) {
  $block = array(
    'time_stamp' => $i->played_at.'',
    'ID' => $i->track->id.''
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
  $tracks = json_decode($output);
  $allTracks = array_merge($allTracks, $tracks->tracks);

}
//var_dump($allTracks);
curl_close($ch);
$tracksClean = [];
$bingeAlbum = '';
$previousTrack = '';
$multiplePlayCount = 1;
foreach($items as $k => $i) {

  if($i->track->id.'' === $previousTrack) {
    $multiplePlayCount++ ;
    $tracksClean[count($tracksClean)-1]['play_count'] = $multiplePlayCount;
    continue;
  }
  $track = $allTracks[$k];
  $playType = 'track';
  $albumID = $track->album->id.'';
  $artistArray = $track->album->artists;
  $artist = array_map(function($a){
    return $a->name.'';
  }, $artistArray);
  $artist = implode(' & ', $artist);
  if($albumID === $bingeAlbum) {
    $tracksClean[count($tracksClean)-1]['play_type'] = 'album';
    $tracksClean[count($tracksClean)-1]['title'] = $track->album->name;
    continue;
  }
  $tracksClean[] = array(
    'artist' => $artist,
    'play_type' => $playType,
    'timestamp' => $i->played_at.'',
    'title' => $track->name.'',
    'img' => $track->album->images[0]->url,
    'play_count' => $multiplePlayCount
  );
  $bingeAlbum = $albumID;
  $previousTrack = $i->track->id.'';
  $multiplePlayCount = 1;
}
echo json_encode($tracksClean);
die();

?>
