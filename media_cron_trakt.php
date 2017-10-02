<?php
$mediaType = 'trakt';
include_once('media_cron_header.php');
include 'media_image_functions.php';
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
$post_types = ['episode','show','movie'];

$compare_posts = comparePosts($post_types, $oldest_play);

$items = array_filter($items, function($i) {
  global $compare_posts;

  return in_array($i['id'],$compare_posts['GUID']) === false;
});

if(empty($items)){echo 'no new posts'; die();}

$workingArray = [];

$GUID = [];
$current = null;
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
			'clickthru' => 'https://trakt.tv/movies/'.$i['movie']['ids']['slug']
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
		$workingArray[count($workingArray)-1]['clickthru'] = 'https://trakt.tv/shows/'.$i['show']['ids']['slug'];
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
		'clickthru' => 'https://trakt.tv/shows/'.$i['show']['ids']['slug'].'/seasons/'.$i['episode']['season'].'/episodes/'.$i['episode']['number'],
    'show' => array(
			'slug' => $i['show']['ids']['slug'],
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

foreach($workingArray as $w) {
  $dates = dateMaker($w['timestamp']);
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
      $cached_show_image = checkCachedImage($w['show']['ID']);
      if($cached_show_image) {
        update_post_meta( $insert, 'showImgURL', $cached_show_image);
      }
      update_post_meta($insert, 'showID', $w['show']['ID']);
    }

  }
}
$to_consolidate = returnBatch($post_types, $oldest_play);
$to_consolidate = array_reverse($to_consolidate);
$i = 0;
$the_count = count($to_consolidate)
while($i < $the_count) {
	if($i === 0) {
		$i++;
		continue;
	}
	$prev = $to_consolidate[$i-1];
	$current = $to_consolidate[$i];
	$current_type = get_the_terms($current->ID, 'consumed_types')[0]->slug;
  $prev_type = get_the_terms($prev->ID, 'consumed_types')[0]->slug;
	
	if($current_type === 'movie' || $prev_type === 'movie') {
		$i++;
    continue;
  }
  
  $prev_data = json_decode($prev->post_content,true);
  $current_data = json_decode($current->post_content,true);

	$current_ID = $current_data['show']['ID'] ?: $current_data['show']['tvdb_ID'] ?: $current_data['show']['title'];
	$prev_ID = $prev_data['show']['ID'] ?: $prev_data['show']['tvdb_ID'] ?: $prev_data['show']['title'] ;

  $bingeShow = bingeCheck($current_ID,strtotime($c->post_date_gmt),$prev_ID,strtotime($prev->post_date_gmt));
  //CHECK IF CONSECUTIVE SHOW PLAYS
  if($bingeShow) {
    $current_data['GUID'] = array_merge($prev_data['GUID'], $current_data['GUID']);
    $current_data['bingeCount'] = intval($prev_data['bingeCount']) + intval($current_data['bingeCount']);
		$current_data['clickthru'] = 'https://trakt.tv/shows/'.$current_data['show']['slug'];
    $updated = wp_update_post(array(
      'ID' => $current->ID,
      'post_content'=>json_encode($current_data),
      'post_title' => $current_data['show']['title']
    ));
    if($updated) {
      $to_consolidate[$k] = get_post($current->ID);
      $delete = wp_delete_post( $prev->ID, false );
      wp_set_object_terms($c->ID, 'show', 'consumed_types' );
    }
    
  }
	$i++;

}
?>
