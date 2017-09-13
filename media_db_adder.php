<?php
include 'media_cron_header.php';

//BOOKS
if(file_exists($wp_base.'wp-content/feed_dump/books.json')) {
  $bookArray = json_decode(file_get_contents($wp_base.'wp-content/feed_dump/books.json'),true);
} else {
  $bookArray = array();
}
function dateMaker($b) {
  $stamp = $b['timestamp'];
  $datetime = new DateTime(date('Y-m-d H:i:s',$stamp));
  $ny_time = new DateTimeZone('America/New_York');
  $datetime->setTimezone($ny_time);
  return array(
    'gmt' => date('Y-m-d H:i:s',$stamp),
    'est' => $datetime->format('Y-m-d H:i:s')
  );
}

function createTerm($term) {
  if(!term_exists($term, 'consumed_types')) {
    wp_insert_term(
      $term, // the term
      'consumed_types', // the taxonomy
      array('slug' => strtolower($term))
    );
  }
}
foreach($bookArray as $k => $b) {

  if($b['inDB']){continue;}
  $dates = dateMaker($b);
  createTerm('Book');

  $insert = wp_insert_post( array(
    'post_title' => $b['title'],
    'post_type' => 'consumed',
    'post_status'=> 'publish',
    'post_content' => json_encode($b),
    'post_date' => $dates['est'],
    'post_date_gmt'=> $dates['gmt']

  ) );
  if($insert) {
    $bookArray[$k]['inDB'] = true;
    wp_set_object_terms( $insert, 'book', 'consumed_types' );
  }

}
file_put_contents($wp_base.'wp-content/feed_dump/books.json', json_encode($bookArray));


//trakt
if(file_exists($wp_base.'wp-content/feed_dump/trakt.json')) {
  $traktArray = json_decode(file_get_contents($wp_base.'wp-content/feed_dump/trakt.json'),true);
} else {
  $traktArray = array();
}
$latest_ep = get_posts(array(
  'posts_per_page'   => 1,
  'post_type' => 'consumed',
	'tax_query' => array(
		array(
			'taxonomy' => 'consumed_types',
			'field'    => 'slug',
			'terms'    => ['episode','show',],
		),
	)
));
if(empty($latest_ep)) {
  $current = array(
    'timestamp' => time(),
    'bingeCount' => 0,
    'dbID' => null,
    'showID' => null
  );
} else {
  $data = json_decode($latest_ep[0]->post_content,true);
  $current = array(

    'timestamp' => intval($data['timestamp']),
    'bingeCount' => intval(get_post_meta($latest_ep[0]->ID,'bingeCount',true)),
    'dbID' => $latest_ep[0]->ID,
    'showID' => get_post_meta($latest_ep[0]->ID,'showID',true)
  );
}

foreach($traktArray as $k => $t) {
  if($t['inDB']){continue;}
  $dates = dateMaker($t);
  if($t['type'] === 'movie') {
    createTerm('Movie');

    $insert = wp_insert_post( array(
      'post_title' => $t['title'],
      'post_type' => 'consumed',
      'post_status'=> 'publish',
      'post_content' => json_encode($t),
      'post_date' => $dates['est'],
      'post_date_gmt'=> $dates['gmt']

    ) );
    if($insert) {
      $traktArray[$k]['inDB'] = true;
      wp_set_object_terms( $insert, 'movie', 'consumed_types' );
    }
  }
  if($t['type'] === 'episode') {
    // SAME DAY BINGE
    if( $current['showID'] === $t['show']['ID'] && date('j-n-Y',$current['timestamp']) === date('j-n-Y',intval($t['timestamp'])) ){
      createTerm('Show');
      wp_set_object_terms( $insert, 'show', 'consumed_types' );
      $updated = wp_update_post( array(
        'ID'=>$current['dbID'],
        'post_title' =>$t['show']['title']
      ) );
      $current['bingeCount']++;
      update_post_meta($current['dbID'], 'bingeCount', $current['bingeCount']);
      $traktArray[$k]['inDB'] = true;
      continue;
    }
    //NEW episode
    createTerm('Episode');
    $insert = wp_insert_post( array(
      'post_title' => $t['title'],
      'post_type' => 'consumed',
      'post_status'=> 'publish',
      'post_content' => json_encode($t),
      'post_date' => $dates['est'],
      'post_date_gmt'=> $dates['gmt']
    ) );
    if($insert) {
      $traktArray[$k]['inDB'] = true;
      $current = array(
        'timestamp' => intval($t['timestamp']),
        'bingeCount' => 1,
        'dbID' => $insert,
        'showID' => $t['show']['ID']
      );
      wp_set_object_terms( $insert, 'episode', 'consumed_types' );
      update_post_meta($current['dbID'], 'showID', $current['showID']);
    }
  }
}
file_put_contents($wp_base.'wp-content/feed_dump/trakt.json', json_encode($traktArray));


 ?>
