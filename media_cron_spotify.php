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
  global $GUIDs;
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

$resetValues = array(
	'timestamp' => time(),
	'listenCount' => 1,
	'dbID' => null,
	'albumID' => null,
	'trackID' => null
);
$current = $resetValues;
if(!empty($compare_posts['posts'])) {
 $data = json_decode($compare_posts['posts'][0]->post_content,true);
 $current = array(
    'timestamp' => strtotime(get_the_date('c',$compare_posts['posts'][0]->ID)),
    'listenCount' => intval(get_post_meta($compare_posts['posts'][0]->ID,'listenCount',true)) ?: 1,
    'dbID' => $compare_posts['posts'][0]->ID,
	 	'albumID' -> $data['album']['ID'],
    'trackID' => $data['ID']
 ); 
}

$GUID = [];
//TRACK INFO GOT!!!!
foreach($items as $k => $i) {
  $dates = dateMaker($i);
  $info = $i['track_info'];
  $artists = array_map(function($a){
    return $a['name'];
  },$info['album']['artists']);
	
	$GUID[] = $i['track']['id'].'_'.$i['played_at'];
	
	//SAME TRACK
	if($i['track']['id'] === $current['trackID']) {
		$current['listenCount']++;
		if($k !== count($items) -1 ) {
			continue;
		}
	}
	// SAME ALBUM
	if($info['album']['id'] === $current['albumID'] {
		$current['type'] = 'album';
		$current['listenCount'] = 1;
		if($k !== count($items) -1 ) {
			continue;
		}
	}
	
	//STREAK BROKEN
	$data = array(
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
	
	//UPDATE CURRENT TO ALBUM
	if($current['type'] === 'album') {
		$current_post = get_post($current['dbID']);
		$data = json_decode($current_post->post_content,true);
		$data['GUID'] = $GUID;
		$updated = wp_update_post( array(
			'ID'=>$current['dbID'],
			'post_title' =>$data['album']['title'],
			'post_content'=>json_encode($data)
		) );
		if($updated) {
			wp_set_object_terms( $current['dbID'], 'show', 'consumed_types' );
			delete_post_meta($current['dbID'], 'listenCount');
		}
		if(count($items) - 1 === $k) {
			continue;
		}

		
	}
	if($current['type']!== 'album' && $current['listenCount'] > 1) {
		$current_post = get_post($current['dbID']);
		$data = json_decode($current_post->post_content,true);
		$data['GUID'] = $GUID;
		$updated = wp_update_post( array(
			'ID'=>$current['dbID'],
			'post_content'=>json_encode($data)
		) );
		if($updated) {
			update_post_meta($current['dbID'], 'listenCount', $current['listenCount']);
		}
		if(count($items) - 1 === $k) {
			continue;
		}
	}
		 
	//CREATE NEW ENTRY
	$insert = wp_insert_post( array(
		'post_title' => $data['title'],
		'post_type' => 'consumed',
		'post_status'=> 'publish',
		'post_content' => json_encode($data),
		'post_date' => $dates['est'],
		'post_date_gmt'=> $dates['gmt']
	) )
	if($insert) {
		wp_set_object_terms( $current['dbID'], 'show', 'consumed_types' );
		$current = array(
			'dbID' => $insert,
			'trackID'=>$data['ID'],
			'listenCount'=>1,
			'albumID'=>$data['album']['ID'],
			'timestamp'=>$data['timestamp']
		);
		$GUID = [];
	} else {
		break;
	}
	
	
  
}




 ?>
