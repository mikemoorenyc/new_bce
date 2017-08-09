
<?php
function ajax_tmdb_init() {
  add_action( 'wp_ajax_nopriv_tmdbimage', 'tmdb_image' );
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
 $ID = $_POST['id'];
 if($type === 'movie') {
   $movieData = json_decode(file_get_contents('https://api.themoviedb.org/3/movie/'.$ID.$end));
   echo 'https://image.tmdb.org/t/p/w185'.$movieData['poster_path'];
   die();
 }
 if($type === 'show') {
   $movieData = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/'.$ID.$end));
   echo 'https://image.tmdb.org/t/p/w300'.$movieData['backdrop_path'];
   die();
 }
 if($type === 'episode') {
   $movieData = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/'.$ID.'/season/'.$_POST['season'].'/episode/'$_POST['episode'].$end));
   echo 'https://image.tmdb.org/t/p/w300'.$movieData['still_path'];
   die();
 }
  
}
die();


?>
