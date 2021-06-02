<?php
$month_ago = date('c',strtotime('-2 months'));
$workingArray = array_filter($workingArray, function($i){
  return $i['timestamp'] >= $month_ago;
});

if(!file_exists($wp_base.'wp-content/feed_dump/')) {
  mkdir($wp_base.'wp-content/feed_dump', 0777);
}
file_put_contents($wp_base.'wp-content/feed_dump/'.$mediaType.'.json', json_encode($workingArray));
die();
?>