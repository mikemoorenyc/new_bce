<?php
$mediaType = 'trakt';
include_once('media_cron_header.php');
if( !isset($keys['trakt']) || !isset($keys['trakt_username'])) {
  die();
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.trakt.tv/users/".$keys['trakt_username']."/history/?limit=50");
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
$items = json_decode($output, true);
foreach($items as $i) {
  if(in_array($i['id'],$GUIDs)){continue;}
  if($i['type'] === 'movie') {
    $workingArray[] = array(
      'GUID' => $i['id'],
      'inDB' => false,
      'title' => $i['movie']['title'],
      'ID' => $i['movie']['ids']['tmdb'],
      'type' => 'movie',
      'timestamp' => strtotime($i['watched_at']),
    );
  }
  if($i['type'] === 'episode') {
    $workingArray[] = array(
      'GUID' => $i['id'],
      'inDB' => false,
      'type' => 'episode',
      'title' => $i['episode']['title'],
      'ID' => $i['episode']['ids']['tmdb'],
      'timestamp' => strtotime($i['watched_at']),
      'season' => $i['episode']['season'],
      'number' => $i['episode']['number'],
      'tvdb_ID' => $i['episode']['ids']['tvdb'],
      'show' => array(
        'title' => $i['show']['title'],
        'ID' => $i['show']['ids']['tmdb'],
        'tvdb_ID' => $i['show']['ids']['tvdb']
      )
    );
  }
}
include 'media_cron_footer.php';
?>
