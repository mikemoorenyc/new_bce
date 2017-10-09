<?php

include 'media_cron_header.php';
include 'partial_tvdb_getter.php';
include 'media_image_functions.php';

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








foreach($posts as $p) {
	
	if(has_post_thumbnail($p->ID)) {continue;}
	
	
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
