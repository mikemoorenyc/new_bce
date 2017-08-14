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

function lazyImg($i) {
  if($i['type'] === 'movie') {
      $title = $i['title'];
      $url= 'https://api.themoviedb.org/3/movie/'.$i['ID'];
  } else {
   if($i['bingeCount'] < 2) {
     $title = $i['title'];
      $url= 'https://api.themoviedb.org/3/tv/'.$i['show']['ID'].'/season/'.$i['season'].'/episode/'.$i['number'];
   } else {
     $title = $i['show']['title'];
      $url= 'https://api.themoviedb.org/3/tv/'.$i['show']['ID'];
   }
    
  }
  
  return array(
    'title' => $title,
    'url' => $url
  )
}
include_once 'switch_media_info.php';
foreach($items as $i {
  $imgClass = $i['type'];
  if(in_array(array('episode','show'), $imgClass)) {
    $imgClass = 'tv';
  }
  if(in_array(array('track','album'),$imgClass)) {
    $imgClass = 'cd';
  }
  
  ?>
  <div class="media-item type-<?=$i['type'];?>">
    <div class="media-image type-<?= $imgClass;?>">
      <?php
      if(in_array(array('movie','episode','show'),$i['type'])){
        $lazy = lazyImg($i);
        ?>
        <img class="tmdb-post" data-type="type-<?= $i['type'];?>" data-url="<?= urlencode($lazy['url']);?>" alt="<?= $lazy['title'];?>" />
        <?php
      }else {
        ?>
        <img src="<?= $i['img'];?>" alt="<?= $i['title'];?>" />
        <?php
      }
  
      ?>
    </div>
    <div class="info">
      <div class="time">
        <?= human_time_diff($i['timestamp'] ).' ago' ;?>
      </div>
      <?php switch_media_info($i);?>
      
      
    </div>
  </div>
  <?php
 
}
die();
 ?>









<?php include_once 'footer.php';?>
