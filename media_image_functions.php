<?php
function checkCachedImage($showID) {
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
  } else {
    return false;
  }
}
$ch = curl_init();
$end = '?api_key='.$keys['tmdb'].'&language=en-US';
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$tmdbCURLs = 0;
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
  $storedImg = checkCachedImage($showID);

  if(!empty($storedImg)) {
  	return $storedImg;
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
function getMovieImg($ID) {
  $response = curlTMDB('https://api.themoviedb.org/3/movie/'.$ID);
  if($response['poster_path']) {
    return 'https://image.tmdb.org/t/p/w185'.$response['poster_path'];
  }
  return false;
}


 ?>
