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
$item_types = ['album','track'];

//COMPARE POSTS are posts that we will compare with just downloaded items. we're fetching all GUIDs from oldest play in the history back to 2 days ago.
$compare_posts = comparePosts($item_types, $oldest_play);

//3ekij6XVw1F5dFX9oLvNU0_2018-12-07T03:57:17.086Z

//We're filtering out all the items whose GUID is present in the $compare_posts GUID array.

$items = array_filter($items, function($i) {
  global $compare_posts;
  return in_array($i['track']['id'].'_'.$i['played_at'],$compare_posts['GUID']) === false;
});
$items = array_values($items);

//IF ALL ITEMS have been filtered out, end it.
if(empty($items)) {
	echo "No new Items.";
	die();
}

//THIS IS ADDING THE DETAILED TRACK INFO TO ALL OF THE TRACKS
$track_blocks = [];
$track_fetch = [];
function trackFetch($tracks) {
  echo 'fetching <br/>';
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
  if($output === FALSE){echo curl_error($ch);}
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
function get_playlist_title($id) {
  global $token;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    'Authorization: Bearer '.$token
  ));
  curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/playlists/".$id);
  $output = curl_exec($ch);
  if($output === FALSE){return false;}
  $response = json_decode($output,true);
  return $response['name'];
}
$track_fetch = [];

foreach($items as $k => $i) {

  $block = array(
    'ID' => $i['track']['id'],
    'item_id' => $k
  );
  $track_fetch[] = $block;
  var_dump($k);
  var_dump(count($items));
  if(count($track_fetch) === 20 || $k === (count($items) - 1)) {
    echo "fetch";
    trackFetch($track_fetch);
    $track_fetch = [];
  }
}

$items = array_filter($items, function($i){
	return !empty($i['track_info']);
});




/*
$current = [];
$workingArray = [];
$GUID = [];
*/

//JUST ADD IN ALL THE TRACKS FIRST

foreach($items as $k => $i):
  $track_GUID = $i['track']['id'].'_'.$i['played_at'];
  $info = $i['track_info'];
  $artists = array_map(function($a){
    return htmlentities($a['name'], ENT_QUOTES);
  },$info['album']['artists']);
  $dates = dateMaker(strtotime($i['played_at']));
  $playlist = null;
  if($i['context']['type'] === "playlist_v2") {
    $playlist = [];
    $playlist['id'] = end(explode(":",$i['context']['uri'] ));
    $playlist['images'] = [$info['album']['images'][0]['url']];
    $playlist['url'] = $i['context']['external_urls']['spotify'];
  }

  $post_content = array(
		'GUID' => [$track_GUID],
    'ID' => $i['track']['id'],
		'type' => 'track',
    'timestamp' => strtotime($i['played_at']),
    'title' => htmlentities($info['name'], ENT_QUOTES),
    'img' =>  httpcheck($info['album']['images'][0]['url']),
    'listenCount'=> 1,
    'clickthru' => $info['external_urls']['spotify'],
    'playlist' => $playlist,
    'album' => array(
      'ID' => $info['album']['id'],
      'title' => htmlentities($info['album']['name'], ENT_QUOTES),
      'artists' => $artists,
      'img' => $info['album']['images'][0]['url'],
      'url' => ($info['album']['external_urls']['spotify'])
    )
	);

	$insert =  wp_insert_post( array(
		'post_title' => $post_content['title'],
		'post_type' => 'consumed',
		'post_status'=> 'publish',
		'post_content' => json_encode($post_content,JSON_UNESCAPED_UNICODE),
		'post_date' => $dates['est'],
		'post_date_gmt'=> $dates['gmt']
	) );
	if($insert) {
    wp_set_object_terms($insert, 'track', 'consumed_types' );
  }




endforeach;

//BRING IN ALL POSTS FROM OLDEST PLAY ON RUN BATCH TO PRESENT
$to_consolidate = returnBatch($item_types, $oldest_play);



//START AT THE OLDEST FIRST
$to_consolidate = array_reverse($to_consolidate);

foreach($to_consolidate as $k => $c):
  //NO NEED TO COMPARE OLDEST
  if($k === 0) {
    continue;
  }

  //THIS has already been consolidated, so we don't need to look at it
  if(get_the_terms($c->ID, 'consumed_types')[0]->slug === "album") {
    continue;
  }


  $prev = $to_consolidate[$k-1];
  $prev_data = json_decode($prev->post_content,true);
  $current_data = json_decode($c->post_content,true);
  $current_type = get_the_terms($c->ID, 'consumed_types')[0]->slug;
  $prev_type = get_the_terms($prev->ID, 'consumed_types')[0]->slug;

  //CHECK IF SAME TRACK HAS BEEN PLAYED TWICE AND IT'S NOT PART OF AN ALBUM
  $bingeTrack = bingeCheck($current_data['ID'],strtotime($c->post_date),$prev_data['ID'],strtotime($prev->post_date));
  //THE track has been played twice CONSECTIVELY
  if($current_type === 'track' && $prev_type === 'track' && $bingeTrack) :
    //Merge all current GUIDs with previous GUIDs
    $current_data['GUID'] = array_merge($prev_data['GUID'], $current_data['GUID']);

    //Update current listen count to include old listen count
    $current_data['listenCount'] = intval($prev_data['listenCount']) + intval($current_data['listenCount']);

    //Update Post with new listen count & GUIDs
    $updated = wp_update_post(array(
      'ID' => $c->ID,
      'post_content'=>json_encode($current_data,JSON_UNESCAPED_UNICODE)
    ));

    //Delete old post
    if($updated) {
      $to_consolidate[$k] = get_post($c->ID);
      $delete = wp_delete_post( $prev->ID, false );
    }
    continue;

  endif;

  /*WE KNOW HERE:
  - Current is a track
  - Current is not the same Previous Track
  */

  //CHECK both have playlists and are same playlist & happened on the same day
  if($current_data['playlist'] && $prev_data['playlist'] && bingeCheck($current_data['playlist']['id'],strtotime($c->post_date),$prev_data['playlist']['id'],strtotime($prev->post_date))):
    //MERGE GUIDs
    $current_data['GUID'] = array_merge($prev_data['GUID'], $current_data['GUID']);

    //MERGE Playlist IMAGES
    $current_data['playlist']['images'] = array_merge($prev_data['playlist']['images'], $current_data['playlist']['images']);
    //get_playlist_title is up top
    $current_data['playlist']['title'] = $prev_data['playlist']['title'] ?: get_playlist_title($current_data['playlist']['id']);
    $current_data['clickthru'] = $current_data['playlist']['url'];

    //Update Post with new GUIDs & Playlist info
    $updated = wp_update_post(array(
      'ID' => $c->ID,
      'post_content'=>json_encode($current_data,JSON_UNESCAPED_UNICODE),
      "post_title" => $current_data['playlist']['title']
    ));
    if($updated) {
      wp_set_object_terms($c->ID, 'album', 'consumed_types' );
      $to_consolidate[$k] = get_post($c->ID);
      $delete = wp_delete_post( $prev->ID, false );
    }

  endif;

  /*
  WE KNOW:
  Current is track
  Current is not the same as previous track
  Current or Previous is not part of a play listen

  **/
  // IF BOTH AREN'T PLAYLISTS && SAME ALBUM ON SAME DAY
  if (!$current_data['playlist'] && !$current_data['playlist'] && bingeCheck($current_data['album']['ID'],strtotime($c->post_date),$prev_data['album']['ID'],strtotime($prev->post_date))) :
    $current_data['GUID'] = array_merge($prev_data['GUID'], $current_data['GUID']);
    $current_data['clickthru'] = $current_data['album']['url'];
    $updated = wp_update_post(array(
      'ID' => $c->ID,
      'post_content'=>json_encode($current_data,JSON_UNESCAPED_UNICODE),
      'post_title' =>$current_data['album']['title']
    ));
    if($updated) {
      $to_consolidate[$k] = get_post($c->ID);
      $delete = wp_delete_post( $prev->ID, false );
      wp_set_object_terms($c->ID, 'album', 'consumed_types' );
    }
  endif;




endforeach;


die();

foreach($items as $k => $i) {
	$info = $i['track_info'];
	$track_GUID = $i['track']['id'].'_'.$i['played_at'];
  $artists = array_map(function($a){
    return htmlentities($a['name'], ENT_QUOTES);
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
    $workingArray[count($workingArray)-1]['clickthru'] = $info['album']['external_urls']['spotify'];
    $workingArray[count($workingArray)-1]['GUID'][] = $track_GUID;
		continue;
	}

	//NEW TRACK
	$workingArray[] = array(
		'GUID' => [$track_GUID],
    'ID' => $i['track']['id'],
		'type' => 'track',
    'timestamp' => strtotime($i['played_at']),
    'title' => htmlentities($info['name'], ENT_QUOTES),
    'img' =>  httpcheck($info['album']['images'][0]['url']),
    'listenCount'=> $current['listenCount'],
    'clickthru' => $info['external_urls']['spotify'],
    'album' => array(
      'ID' => $info['album']['id'],
      'title' => htmlentities($info['album']['name'], ENT_QUOTES),
      'artists' => $artists,
      'img' => $info['album']['images'][0]['url'],
      'url' => httpcheck($info['album']['external_urls']['spotify'])
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
foreach($workingArray as $w) {
  //ALL NEW
  $dates = dateMaker($w['timestamp']);
	$post_title = $w['title'];
	if($w['type'] === 'album') {
		$post_title = $w['album']['title'];
	}
	$insert =  wp_insert_post( array(
		'post_title' => $post_title,
		'post_type' => 'consumed',
		'post_status'=> 'publish',
		'post_content' => json_encode($w,JSON_UNESCAPED_UNICODE),
		'post_date' => $dates['est'],
		'post_date_gmt'=> $dates['gmt']
	) );
	if($insert) {
    wp_set_object_terms($insert, $w['type'], 'consumed_types' );
  }
}

$to_consolidate = returnBatch($item_types, $oldest_play);
$to_consolidate = array_reverse($to_consolidate);
foreach($to_consolidate as $k => $c) {
  if($k === 0) {
    continue;
  }
  $prev = $to_consolidate[$k-1];
  $prev_data = json_decode($prev->post_content,true);
  $current_data = json_decode($c->post_content,true);
  $current_type = get_the_terms($c->ID, 'consumed_types')[0]->slug;
  $prev_type = get_the_terms($prev->ID, 'consumed_types')[0]->slug;
  $bingeTrack = bingeCheck($current_data['ID'],strtotime($c->post_date),$prev_data['ID'],strtotime($prev->post_date));
  $bingeAlbum = bingeCheck($current_data['album']['ID'],strtotime($c->post_date),$prev_data['album']['ID'],strtotime($prev->post_date));
  $updated = false;
  //CHECK IF BOTH CONSECUTIVE PLAYS WERE SAME TRACK ON SAME DAY
  if($current_type === 'track' && $prev_type === 'track' && $bingeTrack) {
    $current_data['GUID'] = array_merge($prev_data['GUID'], $current_data['GUID']);
    $current_data['listenCount'] = intval($prev_data['listenCount']) + intval($current_data['listenCount']);
    $updated = wp_update_post(array(
      'ID' => $c->ID,
      'post_content'=>json_encode($current_data,JSON_UNESCAPED_UNICODE)
    ));
    if($updated) {
      $to_consolidate[$k] = get_post($c->ID);
      $delete = wp_delete_post( $prev->ID, false );
    }
    continue;
  }
  //CHECK IF SAME ALBUM CONSECTIVELY ON SAME DAY
  if($bingeAlbum) {
    $current_data['GUID'] = array_merge($prev_data['GUID'], $current_data['GUID']);
    $current_data['clickthru'] = $current_data['album']['url'];
    $updated = wp_update_post(array(
      'ID' => $c->ID,
      'post_content'=>json_encode($current_data,JSON_UNESCAPED_UNICODE),
      'post_title' =>$current_data['album']['title']
    ));
    if($updated) {
      $to_consolidate[$k] = get_post($c->ID);
      $delete = wp_delete_post( $prev->ID, false );
      wp_set_object_terms($c->ID, 'album', 'consumed_types' );
    }
    continue;
  }
}


 ?>
