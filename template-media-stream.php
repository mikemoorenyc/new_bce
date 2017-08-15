<?php
/**
 * Template Name: Media Stream
 */
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

  ?>
  <div  class="media-item type-<?=$i['type'];?>">
    <?php

      $time = human_time_diff($i['timestamp'] ).' ago';
      if(strpos($time, 'hours')!== false) {
        $time = "Today";
      }
      if($time === '1 day ago') {
        $time = "Yesterday";
      }
     ?>
     <?php if($time !== $time_marker){
      ?>
      <div class="time font-sans">
      <strong><?= $time ;?></strong>
      </div>
      <?php
      $time_marker = $time;
     }
     ?>
<div class="inner">

    <div class="img-container">
    <div class="media-image type-<?= $imgClass;?>">

      <?php
      if(in_array($i['type'],array('movie','episode','show'))){
        if(!$i['img']) {
          $lazy = lazyImg($i);
          ?>
        <img src="<?= $siteDir;?>/assets/imgs/blank.png" class="tmdb-post" data-key="<?= $k;?>" data-type="type-<?= $i['type'];?>" data-url="<?= urlencode($lazy['url']);?>" alt="<?= $lazy['title'];?>" />
        <?php
        } else {
          ?>
        <img src="<?= $i['img'];?>" alt="<?= $i['title'];?>" />
        <?php
        }
       
        
      }else {
        ?>
        <img src="<?= $i['img'];?>" alt="<?= $i['title'];?>" />
        <?php
      }

      ?>
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





<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>
  var lazyImgs = document.querySelectorAll('img.tmdb-post');
  var securityCode = '<?= wp_create_nonce( "ajax-request-nonce") ;?>';
  var ajaxURL = '<?= admin_url( 'admin-ajax.php' );?>';
  lazyImgs.forEach( function(e, i){
    var img = e;
    setTimeout(function(){
      axios.post(ajaxURL, {
        security: securityCode,
        action: 'tmdbimage',
        type: img.getAttribute('data-type'),
        url: img.getAttribute('data-url')
      })
      .then(function (response) {
        var data = JSON.parse(response);
        img.setAttribute('src', data.url);
      });
      
      
      
    }, i*250);
  });
  
  
</script>

<?php include_once 'footer.php';?>
