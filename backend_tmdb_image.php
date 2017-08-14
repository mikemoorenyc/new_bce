
<?php
function ajax_tmdb_init() {
  add_action( 'wp_ajax_nopriv_tmdbimage', 'tmdb_image_getter' );
}
add_action('init', 'ajax_tmdb_init');

function tmdb_image_getter() {
 check_ajax_referer( 'ajax-request-nonce', 'security' );
 require_once get_template_directory().'/partial_api_key_generator.php';
 $keys = api_key_generator();
 if(!isset($keys['tmdb'])) {
  echo '[]';
  die();
 }
 $end = '?api_key='.$keys['tmdb'].'&language=en-US';
 $type = $_POST['type'];
 $url = $_POST['url'];
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($ch, CURLOPT_URL, $url.$end);
 $info = curl_exec($ch);  
 if($info === false) {
   echo json_encode(
    array(
    "status" => 'error'
    );
   )
   die();
 } 
 curl_close($ch);
 $info = json_decode($info, true);
 $response = [];
 $response['status'] = 'success';
 if($type === 'movie') {
   $response['url'] = 'https://image.tmdb.org/t/p/w185'.$info['poster_path'];
 }
 if($type === 'show') {
   $response['url'] = 'https://image.tmdb.org/t/p/w300'.$movieData['backdrop_path'];
 }
 if($type === 'episode') {
   $response['url'] = 'https://image.tmdb.org/t/p/w300'.$movieData['still_path'];
 }
 echo json_encode($response);
 die();
}



?>
