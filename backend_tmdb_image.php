<?php

function add_ajax_actions() {
  add_action( 'wp_ajax_nopriv_tmdbimage', 'tmdb_image_getter' );
  add_action( 'wp_ajax_tmdbimage', 'tmdb_image_getter' );
}
add_action('init', 'add_ajax_actions');
function tmdb_image_getter() {

check_ajax_referer( 'ajax-request-nonce', 'security' );

 require_once get_template_directory().'/partial_api_key_generator.php';
 $backup = get_all_image_sizes(get_option( 'social_icon_image', '' ));
 $backup_url = $backup['medium']['url'];
 $keys = api_key_generator();
 if(!isset($keys['tmdb'])) {
  echo json_encode(
    array(
      "status" => 'error',
      "url" => $backup_url
    )
  );
  die();
 }
 $end = '?api_key='.$keys['tmdb'].'&language=en-US';
 $type = $_POST['type'];

 $url = $_POST['url'];
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($ch, CURLOPT_URL, urldecode($url).$end);
 $info = curl_exec($ch);
 if($info === false) {
   echo json_encode(
    array(
    "status" => 'error_FALSE',
    "URL" => $url.$end
    )
   );
   die();
 }

 $info = json_decode($info, true);

 $response = [];
 $response['status'] = 'error';
 $response['url'] = $backup_url;
 if($type === 'movie') {

   if($info['poster_path'] !== null) {
     $response['url'] = 'https://image.tmdb.org/t/p/w185'.$info['poster_path'];
   }
 }
 if($type === 'show') {
   if($info['backdrop_path'] !== null) {
     $response['url'] = 'https://image.tmdb.org/t/p/w300'.$info['backdrop_path'];
   }

 }
 if($type === 'episode') {
   if($info['still_path']) {
     $response['url'] = 'https://image.tmdb.org/t/p/w300'.$info['still_path'];
   }
 }
 if($response['url'] !== $backup_url) {
   $response['status'] = 'success';
 }
 echo json_encode($response);
  curl_close($ch);
 die();
}



?>
