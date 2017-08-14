<?php
/**
 * Template Name: Media Stream
 */
?>
<?php include_once 'header.php';?>
<?php
$feeds = ['goodreads','spotify','trakt'];
$itemList = [];
foreach($feeds as $f) {
  $file = json_decode(file_get_contents(ABSPATH.'wp-content/feed_dump/'.$f.'.json'),true);
  $itemList[$f] = $file['items'];
}
$items = [];
$items = array_merge($items, $itemList['goodreads']);
include_once 'loader_trakt.php';
$items = array_merge($items, $traktItems);
include_once 'loader_spotify.php';
$items = array_merge($items, $sItems);
function date_compare($a, $b)
{
    $t1 = $a['timestamp'];
    $t2 = $b['timestamp'];
    return $t1 - $t2;
}
usort($items, 'date_compare');
$items = array_reverse($items);
var_dump($items);
die();
 ?>









<?php include_once 'footer.php';?>
