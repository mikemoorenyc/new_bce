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

$oldest_play = $items[count($items)-1]['watched_at'];

$compare_posts = get_posts(array(
  'posts_per_page'   => -1,
  'post_type' => 'consumed',
	'tax_query' => array(
		array(
			'taxonomy' => 'consumed_types',
			'field'    => 'slug',
			'terms'    => ['episode','show',],
		),
	),
  'date_query' => array(
    'after'=> $oldest_play
  )

));
$GUIDs = array_map(function($i){
  $data = json_decode($i->post_content,true);
  return $data['GUID'];
},$compare_posts);
$current = array(
      'timestamp' => time(),
    'bingeCount' => 0,
    'dbID' => null,
    'showID' => null
);
if(!empty($compare_posts)) {
 $data = json_decode($compare_posts[0]->post_content,true);
 $current = array(
    'timestamp' => intval($data['timestamp']),
    'bingeCount' => intval(get_post_meta($compare_posts[0]->ID,'bingeCount',true)),
    'dbID' => $compare_posts[0]->ID,
    'showID' => get_post_meta($compare_posts[0]->ID,'showID',true)
 ); 
}


foreach($items as $i) {
  if(in_array($i['id'],$GUIDs)){continue;}
  $dates = dateMaker($i);
  if($i['type'] === 'movie') {
    createTerm('Movie');
    $insert = wp_insert_post( array(
      'post_title' => $i['title'],
      'post_type' => 'consumed',
      'post_status'=> 'publish',
      'post_content' => json_encode($i),
      'post_date' => $dates['est'],
      'post_date_gmt'=> $dates['gmt']
    ) );
    if($insert) {
      wp_set_object_terms( $insert, 'movie', 'consumed_types' );
    }
  }
  if($i['type'] === 'episode') {
     // SAME DAY BINGE
    if( $current['showID'] === $i['show']['ID'] && date('j-n-Y',$current['timestamp']) === date('j-n-Y',intval($i['timestamp'])) ){
      createTerm('Show');
      wp_set_object_terms( $current['dbID'], 'show', 'consumed_types' );
      $updated = wp_update_post( array(
        'ID'=>$current['dbID'],
        'post_title' =>$i'show']['title']
      ) );
      $current['bingeCount']++;
      update_post_meta($current['dbID'], 'bingeCount', $current['bingeCount']);
      continue;
    }
    // NEW EP
    createTerm('Episode');
    $insert = wp_insert_post( array(
      'post_title' => $i['title'],
      'post_type' => 'consumed',
      'post_status'=> 'publish',
      'post_content' => json_encode($i),
      'post_date' => $dates['est'],
      'post_date_gmt'=> $dates['gmt']
    ) );
    if($insert) {
      $current = array(
        'timestamp' => intval($t['timestamp']),
        'bingeCount' => 1,
        'dbID' => $insert,
        'showID' => $i['show']['ID']
      );
      wp_set_object_terms( $insert, 'episode', 'consumed_types' );
      update_post_meta($current['dbID'], 'showID', $current['showID']);
    }
    
    
    
    
  }
  
  
 
}



include 'media_cron_footer.php';

?>
