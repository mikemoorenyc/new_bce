<?php

$mediaType = 'spotify';
include_once('media_cron_header.php');

if( !isset($keys['spotify_id']) || !isset($keys['spotify_secret']) || !isset($keys['spotify_refresh'])) {
  die();
}

createTerm("Album");
createTerm('Track');
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


$oldest_play = $items[count($items)-1]['played_at'];
$compare_posts = comparePosts(['album','track'], $oldest_play);

$items = array_filter($items, function($i) {
  global $compare_posts;
  return in_array($i['track']['id'].'_'.$i['played_at'],$compare_posts['GUID']) === false;
});


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

$items = array_filter($items, function($i){
	return !empty($i['track_info']);
});

$resetValues = array(
	'timestamp' => time(),
	'listenCount' => 1,
	'dbID' => null,
	'albumID' => null,
	'trackID' => null
);
$current = $resetValues;
$compareValues = $resetValues;
if(!empty($compare_posts['posts'])) {
 $data = json_decode($compare_posts['posts'][0]->post_content,true);
 $compareValues = array(
    'timestamp' => strtotime(get_the_date('c',$compare_posts['posts'][0]->ID)),
    'listenCount' => $data['listenCount'],
    'dbID' => $compare_posts['posts'][0]->ID,
	 	'albumID' => $data['album']['ID'],
    'trackID' => $data['ID']
 );
}
$current = $resetValues;
$workingArray = [];
$GUID = [];
foreach($items as $k => $i) {
	$info = $i['track_info'];
	$track_GUID = $i['track']['id'].'_'.$i['played_at'];
  $artists = array_map(function($a){
    return $a['name'];
  },$info['album']['artists']);

	//CHECK IF SAME TRACK
	if($current['type'] !== 'album' && bingeCheck($current['trackID'],$current['timestamp'],$i['track']['id'],strtotime($i['played_at']) )) {
		$current['listenCount']++;
		$workingArray[count($workingArray)-1]['GUID'][] = $track_GUID;
		$workingArray[count($workingArray)-1]['listenCount'] = $current['listenCount'];
		continue;
	}
	//CHECK IF SAME ALBUM
	if(bingeCheck($current['albumID'],$current['timestamp'],$info['album']['id'],strtotime($i['played_at']))) {
		$current['type'] = 'album';
		$workingArray[count($workingArray)-1]['type'] = 'album';
    $workingArray[count($workingArray)-1]['GUID'][] = $itrack_GUID;
		continue;
	}

	//NEW TRACK
	$workingArray[] = array(
		'GUID' => [$track_GUID],
    'ID' => $i['track']['id'],
		'type' => 'track',
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

	//RESET
	$GUID = [];
	$current = array(
		'timestamp'=> strtotime($i['played_at']),
		'listenCount'=>1,
		'albumID' =>  $info['album']['id'],
		'trackID' => $i['track']['id']
	);

}
foreach($compare_posts['posts'] as $c) {
  $data = json_decode($c->post_content,true);

  $workingArray[] = array(
    'dbID' => $c->ID,
    'content'=>$c->post_content,
    'trackID'=>$data['ID'],
    'albumID'=>$data['album']['ID'],
    'timestamp'=>strtotime($c->post_date_gmt)
  );
}

usort($workingArray, function($a, $b){
  return $a['timestamp'] - $b['timestamp'];
});
$workingArray = array_reverse($workingArray);
$keyValue = array();
foreach($workingArray as $k=>$w) {
	$dates = dateMaker($w['timestamp']);
	//CHECK TRACK
	if($w['type'] === 'track' && bingeCheck($w['ID'],$w['timestamp'],$keyValue['trackID'],$keyValue['timestamp'])){
    $dbID = $w['dbID'] ?: $keyValue['dbID'];
		$content = $w['content'] ?: $keyValue['content'];
		$trackData = json_decode($content,true);
		$trackData['listenCount'] = $w['listenCount'] + intval($trackData['listenCount']);
		foreach($w['GUID'] as $g) {
			$trackData['GUID'][] = $g;
		}
		$updated = wp_update_post( array(
			'ID'=>$dbID ,
			'post_content'=>json_encode($trackData)
		) );
		continue;
	}
	if(bingeCheck($w['album']['ID'],$w['timestamp'],$keyValue['albumID'],$keyValue['timestamp'])) {
    $dbID = $w['dbID'] ?: $keyValue['dbID'];
		$content = $w['content'] ?: $keyValue['content'];
		$trackData = json_decode($content,true);
		foreach($w['GUID'] as $g) {
			$trackData['GUID'][] = $g;
		}
		$updated = wp_update_post( array(
			'ID'=>$dbID ,
			'post_content'=>json_encode($trackData),
			'post_title' => $w['album']['title']
		) );
		if($updated) {
			 wp_set_object_terms($dbID , 'album', 'consumed_types' );
		}
		continue;


	}

  if($w['dbID']) {
		$keyValue = $w;
		continue;
	}

	//ALL NEW
	$post_title = $w['title'];
	if($w['type'] === 'album') {
		$post_title = $w['album']['title'];
	}
	$insert =  wp_insert_post( array(
		'post_title' => $post_title,
		'post_type' => 'consumed',
		'post_status'=> 'publish',
		'post_content' => json_encode($w),
		'post_date' => $dates['est'],
		'post_date_gmt'=> $dates['gmt']
	) );
	if($insert) {
    wp_set_object_terms($insert, $w['type'], 'consumed_types' );
  }
  $keyValue = $w;
}




 ?>
