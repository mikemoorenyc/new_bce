<?php
/**
 * Template Name: Media Stream
 */

 date_default_timezone_set('America/New_York');

?>
<?php include_once 'header.php';?>
<?php $landing_post = $post;?>

<?php include_once 'partial_landing_page_header.php';?>

<div class="media-stream copy reading-section">
  <?= md_sc_parse($post->post_content);?>
</div>

<div class="media-stream container">
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
   if($i['bingeCount'] < 2 && $i['ID'] !== null) {
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
  );
}
include_once 'switch_media_info.php';
$time_marker = "";
foreach($items as $k => $i ){
  $imgClass = $i['type'];
  if(in_array($imgClass,array('episode','show') )) {
    $imgClass = 'tv';
  }
  if(in_array($imgClass,array('track','album'))) {
    $imgClass = 'cd';
  }

  $today = array(
   'd' => intval(date('j')),
   'm' => intval(date('n')),
   'y' => intval(date('Y'))
  );
  $stamp = array(
   'd' => intval(date('j',$i['timestamp'])),
   'm' => intval(date('n',$i['timestamp'])),
   'y' => intval(date('Y',$i['timestamp']))
  );
  $time = human_time_diff($i['timestamp'] ).' ago';
  if($today['m'] == $stamp['m'] && $today['y'] == $stamp['y']) {
   if($today['d'] == $stamp['d']) {
    $time = 'Today';
   }
   if(($today['d'] - 1) == $stamp['d']) {
    $time = 'Yesterday';
   }
  }
  
  ?>
  <?php if($time !== $time_marker){
   ?>
   <h2 class="sub-heading with-line"><span><?= $time ;?></span></h2>
   <?php
   $time_marker = $time;
  }
  ?>
  <div  class="media-item type-<?=$i['type'];?> above-line">


<div class="inner">

    <div class="img-container">
    <div class="media-image type-<?= $imgClass;?>">

      <?php
      $imgURL = $i['img'];
      $preload = "preload-image";
      $pURL = parse_url($imgURL);
      if($pURL['scheme'] !== 'https') {
        $imgURL = $siteDir.'/image_proxy.php?url='.urlencode($imgURL);
      }
      if(!$i['img']) {
        $preload="";
      }

      ?>
      <img title="<?=$title;?>" class="<?= $preload;?> no-blur"  src="<?= $siteDir;?>/assets/imgs/blank_<?= $imgClass ?>.png" data-src="<?= $imgURL;?>" alt="<?= $i['title'];?>" />
    </div>
    </div>
    <div class="info">

      <?php switch_media_info($i);?>


    </div>
  </div>
  </div>
  <?php

}

 ?>

</div>





<?php include_once 'footer.php';?>
