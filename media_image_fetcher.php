<?php

include 'media_cron_header.php';
include 'partial_tvdb_getter.php';


$posts = get_posts(array(
  'posts_per_page'   => -1,
  'post_type' => 'consumed',
	'tax_query' => array(
		array(
			'taxonomy' => 'consumed_types',
			'field'    => 'slug',
			'terms'    => ['episode','show','movie'],
		),
	),
  'meta_query' => array(
    array(
     'key' => 'imgURL',
     'compare' => 'NOT EXISTS' // this should work...
    ),
  )
));

if(empty($posts)){echo 'dead';die();}

$tmdbCURLs = 0;


$ch = curl_init();
$end = '?api_key='.$keys['tmdb'].'&language=en-US';
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

function curlTMDB($url) {
  global $tmdbCURLs;
  global $ch;
  global $end;
  if($tmdbCURLs > 25){return false;}
  curl_setopt($ch, CURLOPT_URL, $url.$end);
  $output = curl_exec($ch);

  $tmdbCURLs++;
  if($output === false || json_decode($output,true)['status_code'] === 34) {
    return [];
  }
  return json_decode($output,true);
}

function getShowImgURL($showID,$tvdbID) {
  $storedImg = get_posts(
  array(
    'posts_per_page'   => 1,
    'post_type' => 'consumed',

    'meta_query' => array(
      array(
       'key' => 'showImgURL',
       'compare' => 'EXISTS'
      ),
      array(
       'key' => 'showID',
       'value' => $showID
      ),
    )
  )
  );
	
  if(!empty($storedImg)) {
  	return get_post_meta($storedImg[0]->ID, 'showImgURL',true);
  }
	
  $response = curlTMDB('https://api.themoviedb.org/3/tv/'.$showID);
  if($response && !empty($response['backdrop_path'])) {
   return 'https://image.tmdb.org/t/p/w300'.$response['backdrop_path'];
  }
	
  $response = get_tvdb('show', $tvdbID);
  if($response) {
  	return $response;
  }
  return false;

}



foreach($posts as $p) {
 $data = json_decode($p->post_content,true);

 $type = get_the_terms($p->ID, 'consumed_types');
 if($type){$type = $type[0]->slug;}
	
	switch ($type):
		case "movie":
			$response = curlTMDB('https://api.themoviedb.org/3/movie/'.$data['ID']);
			if($response['poster_path']) {
				update_post_meta( $p->ID, 'imgURL', 'https://image.tmdb.org/t/p/w185'.$response['poster_path'] );
			}
			break;
	
		case "show":
			$showImgURL = getShowImgURL($data['show']["ID"],$data['show']["tvdb_ID"]);
			if($showImgURL) {
				update_post_meta( $p->ID, 'imgURL', $showImgURL);
  			update_post_meta( $p->ID, 'showImgURL', $showImgURL);
			}
			break;
	
		case "episode":
			$showImgURL = getShowImgURL($data['show']["ID"],$data['show']["tvdb_ID"]);
  		if($showImgURL) {
    		update_post_meta( $p->ID, 'showImgURL', $showImgURL);
  		}
			$response = curlTMDB('https://api.themoviedb.org/3/tv/'.$data['show']['ID'].'/season/'.$data['season'].'/episode/'.$data['number']);
			if($response['still_path']) {
				update_post_meta( $p->ID, 'imgURL', 'https://image.tmdb.org/t/p/w300'.$response['still_path']);
				break;
			}
			$response = get_tvdb('episode', $data['tvdb_ID']);
			if($response) {
    		update_post_meta( $p->ID, 'imgURL', $response);
  		}
			break;
	endswitch;



}

?>
