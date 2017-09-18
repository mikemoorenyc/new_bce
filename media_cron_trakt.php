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

if($items === null) {var_dump($output);die();}

$oldest_play = $items[count($items)-1]['watched_at'];


$compare_posts = comparePosts(['episode','show','movie'], $oldest_play);

$items = array_filter($items, function($i) {
  global $compare_posts;

  return in_array($i['id'],$compare_posts['GUID']) === false;
});



$resetValues = array(
      'timestamp' => time(),
    'bingeCount' => 1,
    'dbID' => null,
    'showID' => null
);
$current = $resetValues;
$workingArray = [];
if(!empty($compare_posts['posts'])) {
 $data = json_decode($compare_posts['posts'][0]->post_content,true);
 $current = array(
    'timestamp' => strtotime(get_the_date('c',$compare_posts['posts'][0])),
    'bingeCount' => intval(get_post_meta($compare_posts['posts'][0]->ID,'bingeCount',true)),
    'dbID' => $compare_posts[0]->ID,
    'showID' => get_post_meta($compare_posts['posts'][0]->ID,'showID',true)
 );
}
$GUID = [];
$current = $resetValues;
foreach($items as $k => $i) {

  $data = array();
	$dates = dateMaker(strtotime($i['watched_at']));

	if($i['type'] === 'movie') {
		$workingArray[] = array(
      'GUID' =>  [$i['id']],
      'title' => $i['movie']['title'],
      'ID' => $i['movie']['ids']['tmdb'],
      'type' => 'movie',
      'timestamp' => strtotime($i['watched_at']),
    );

		$current = $resetValues;
		$GUID = [];
		continue;
	}


	if(bingeCheck($current['showID'],$current['timestamp'],$i['show']['ids']['tmdb'],strtotime($i['watched_at']))) {
		$current['bingeCount']++;

		$workingArray[count($workingArray)-1]['bingeCount'] = $current['bingeCount'];
    $workingArray[count($workingArray)-1]['type'] = 'show';
    $workingArray[count($workingArray)-1]['GUID'][] = $i['id'];
    continue;
	}

  //NEW ONE
  $workingArray[] = array(
    'GUID'=>[$i['id']],
    'type' => 'episode',
    'title' => $i['episode']['title'],
    'ID' => $i['episode']['ids']['tmdb'],
    'timestamp' => strtotime($i['watched_at']),
    'season' => $i['episode']['season'],
    'number' => $i['episode']['number'],
    'tvdb_ID' => $i['episode']['ids']['tvdb'],
    'bingeCount' => 1,
    'show' => array(
      'title' => $i['show']['title'],
      'ID' => $i['show']['ids']['tmdb'],
      'tvdb_ID' => $i['show']['ids']['tvdb']
    )
  );



	//RESET
	$GUID = [];
	$current = array(

		'timestamp'=> strtotime($i['watched_at']),
		'bingeCount'=>1,
		'showID' => $i['show']['ids']['tmdb']
	);


}

usort($workingArray, function($a, $b){
  return $a['timestamp'] - $b['timestamp'];
});
foreach($workingArray as $k => $w) {
  $dates = dateMaker($w);
  if($k === 0 && bingeCheck($w['show']['ID'],$w['timestamp'],get_post_meta($compare_posts['posts'][0]->ID,'showID',true),strtotime($compare_posts['posts'][0]->post_date_gmt))) {
    //UPDATE
    $newBinge = $w['bingeCount'] + intval(get_post_meta($compare_posts['posts'][0]->ID,'bingeCount',true));
    $data = json_decode($compare_posts['posts'][0]->post_content,true);
    foreach($w['GUID'] as $guid) {
      $data["GUID"][] = $guid;
    }
    $updated = wp_update_post( array(
			'ID'=>$compare_posts['posts'][0]->ID,
			'post_title' =>$w['show']['title'],
			'post_content'=>json_encode($data)
		) );
    if($updated) {
      wp_set_object_terms($compare_posts['posts'][0]->ID, 'show', 'consumed_types' );
    }
    continue;
  }

  //EVERYONE ELSE IS NEW
  $post_title = $w['title'];
  if($w['type'] === 'show') {
    $post_title = $w['show']['title'];
  }
  $insert = wp_insert_post( array(
		'post_title' => $post_title,
		'post_type' => 'consumed',
		'post_status'=> 'publish',
		'post_content' => json_encode($w),
		'post_date' => $dates['est'],
		'post_date_gmt'=> $dates['gmt']
	) );
  if($insert) {
    wp_set_object_terms($insert, $w['type'], 'consumed_types' );
    if($w['type'] === 'episode' || $w['type'] === 'show') {
      update_post_meta($insert, 'bingeCount', $w['bingeCount']);
      update_post_meta($insert, 'showID', $w['show']['ID']);
    }
  }

}
?>
