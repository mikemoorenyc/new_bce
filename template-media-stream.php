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

<div id="media-stream" class=" media-stream container">
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
?>

<?php
function returnS($s) {
  if($s > 1) {
    return 's';
  } else {
    return '';
  }
}
function timeSet($stamp) {
  $today = array(intval(date('j')),intval(date('n')),intval(date('Y')));
  $timeA =  array(intval(date('j',$stamp)),intval(date('n',$stamp)),intval(date('Y',$stamp)));

  $largeDiff = ($today[0]+($today[1]*30)+($today[2]*365)) - ($timeA[0]+($timeA[1]*30)+($timeA[2]*365));

  if(date('j-n-Y') === date('j-n-Y',$stamp)) {
    return 'Today';
  }
  if(date('j-n-Y',strtotime('-1 days')) === date('j-n-Y',$stamp)) {
    return 'Yesterday';
  }
  //YEARS
  if($largeDiff >= 365) {
    $diff = $today[2] - $timeA[2];
    return $diff.' year'.returnS($diff).' ago';
  }
  //MONTHS
  if($largeDiff > 30) {
    $diff = $today[1] - $timeA[1];
    return $diff.' month'.returnS($diff).' ago';
  }
  //WEEKS
  if($largeDiff > 6) {
    $diff = floor($largeDiff / 7);
    return $diff.' week'.returnS($diff).' ago';
  }
  //DAYS
  return ($today[0] - $timeA[0]).' day'.returnS($today[0] - $timeA[0]).' ago';
}

 ?>

<?php
foreach($items as $k => $i ){
 if($k > 99) {break;}
  $imgClass = $i['type'];
  if(in_array($imgClass,array('episode','show') )) {
    $imgClass = 'tv';
  }
  if(in_array($imgClass,array('track','album'))) {
    $imgClass = 'cd';
  }
  /*
  $today = date('j-n-Y');
  $yesterday = date('j-n-Y',strtotime('-1 days'));
  $stamp = date('j-n-Y',$i['timestamp']);

  $datetime = new DateTime(date('c',$i['timestamp']) );
  $ny_time = new DateTimeZone('America/New_York');
  $datetime->setTimezone($ny_time);
  $time = human_time_diff($datetime->format('U') ).' ago';
  if($stamp === $today) {
    $time = "Today";
  }
  if($stamp === $yesterday || $time == "1 day ago") {
    $time = "Yesterday";
  }*/
  $time = timeSet($i['timestamp']);

  ?>
  <?php if($time !== $time_marker){
   ?>
   <h2 class="sub-heading with-line"><span><?= $time ;?></span></h2>
   <?php
   $time_marker = $time;
  }

  ?>

  <div data-key="<?= $k; ?>" class="media-item type-<?=$i['type'];?> blank above-line">



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
  <?php

}

 ?>

</div>





<?php include_once 'footer.php';?>
