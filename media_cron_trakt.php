<?php
$mediaType = 'trakt';
include_once('media_cron_header.php');
if( !isset($keys['trakt']) || !isset($keys['trakt_username'])) {
  die();
}

createTerm('Show');
createTerm('Episode');
createTerm('Movie');

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

$oldest_play = $items[count($items)-1]['watched_at'];


$compare_posts = comparePosts(['episode','show'], $oldest_play);

$items = array_filter($items, function($i) {
  global $GUIDs;
  return in_array($i['id'],$compare_posts['GUID']) === false;
});

$resetValues = array(
      'timestamp' => time(),
    'bingeCount' => 1,
    'dbID' => null,
    'showID' => null
);
$current = $resetValues;
if(!empty($compare_posts['posts'])) {
 $data = json_decode($compare_posts['posts'][0]->post_content,true);
 $current = array(
    'timestamp' => intval($data['timestamp']),
    'bingeCount' => intval(get_post_meta($compare_posts['posts'][0]->ID,'bingeCount',true)),
    'dbID' => $compare_posts[0]->ID,
    'showID' => get_post_meta($compare_posts['posts'][0]->ID,'showID',true)
 ); 
}
$GUID = [];

foreach($items as $k => $i) {
  $GUID[] = $i['id'];
  $data = array();
	$dates = dateMaker(strtotime($i['watched_at']));
	
	if($i['type'] === 'movie') {
		$data = array(
      'GUID' =>  $GUID,
      'title' => $i['movie']['title'],
      'ID' => $i['movie']['ids']['tmdb'],
      'type' => 'movie',
      'timestamp' => strtotime($i['watched_at']),
    );
		$insert = wp_insert_post( array(
      'post_title' => $data['title'],
      'post_type' => 'consumed',
      'post_status'=> 'publish',
      'post_content' => json_encode($data),
      'post_date' => $dates['est'],
      'post_date_gmt'=> $dates['gmt']
    ) );
    if($insert) {
      wp_set_object_terms( $insert, 'movie', 'consumed_types' );
    }
		$current = $resetValues;
		$GUID = []
		continue;
	}
	
	
	
	
  if($current['showID'] === $i['show']['ID'] && date('j-n-Y',$current['timestamp']) === date('j-n-Y',intval($i['timestamp']))) {
    $current['bingeCount']++;
    
		if($k !== count($items) - 1) {
			continue;
		}
		
  }
	
  //BREAK
  //UPDATE CURRENT ONE
  if($current['bingeCount']>1) {
		 $current_post = get_post($current['dbID']);
		 $data = json_decode($current_post->post_content,true);
		 $data['GUID'] = $GUID;
		 $updated = wp_update_post( array(
			 'ID'=>$current['dbID'],
			 'post_title' =>$i['show']['title'],
			 'post_content'=>json_encode($data)
		 ) );
		update_post_meta($current['dbID'], 'bingeCount', $current['bingeCount']);
		wp_set_object_terms( $current['dbID'], 'show', 'consumed_types' );
  } else {
		//INSERT NEW
    $data = array(
			'GUID'=>$GUID,
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
		$insert = wp_insert_post( array(
      'post_title' => $i['episode']['title'],
      'post_type' => 'consumed',
      'post_status'=> 'publish',
      'post_content' => json_encode($data),
      'post_date' => $dates['est'],
      'post_date_gmt'=> $dates['gmt']
    ) );
		if($insert) {
			wp_set_object_terms( $insert, 'episode', 'consumed_types' );
      update_post_meta($insert, 'showID', $data['show']['ID']);
		}
  }
	
	//RESET
	$GUID = [];
	$current = array(
		'dbID' => $insert,
		'timestamp'=> $data['timestamp'],
		'bingeCount'=>1,
		'showID' => $data['show']['ID']
	);
  
 
}

die();
?>
