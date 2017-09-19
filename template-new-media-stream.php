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
<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts = get_posts(
  array(
    'posts_per_page'   => 25,
    'post_type' => 'consumed',
    'paged' => $paged
  )
);
if(empty($posts)) {
  echo '<h2>There is nothing to show you.</h2>';
  include 'footer.php'; 
  die();
}
?>
<?php
include_once 'switch_media_info.php';

function imageData($p) {
	if(has_post_thumbnail($p->ID)) {
		$urls =  get_all_image_sizes(get_post_thumbnail_id($p->ID));
		$imgURL = $urls['medium']['url'];
		} else {
			$imgURL = '';
	}
 $data = json_decode($p->post_content,true);
 $type = get_the_terms($p->ID, 'consumed_types');
 if($type){$type = $type[0]->slug;}
 $preloadClass = 'preload-image';
 $imgURL = $data['img'] ?: $imgURL ;
 if($type === 'movie' || $type === 'show') {
  $imgURL = get_post_meta($p->ID, 'imgURL',true);
 }
 if($type === 'episode') {
  if( get_post_meta($p->ID, 'imgURL',true)) {
   $imgURL =  get_post_meta($p->ID, 'imgURL',true);
  } else {
    $imgURL =  get_post_meta($p->ID, 'showImgURL',true);
  }
 }

	$pURL = parse_url($imgURL);
	if($pURL['scheme'] !== 'https') {
		$imgURL = $siteDir.'/image_proxy.php?url='.urlencode($imgURL);
	}
 if(empty($imgURL)) {
  $preloadClass = ''; 
 }
 return array(
  'url' => $imgURL,
  'preload' => $preloadClass
  
 );
  
}


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
<div id="media-stream" class=" media-stream container">
<?php 
$time_marker = "";
foreach($posts as $k => $p) {
  $data = json_decode($p->post_content,true);
  $time = timeSet($i['timestamp']);
  $type = get_the_terms($p->ID, 'consumed_types');
  if($type){$type = $type[0]->slug;}
  $imgClass = 'other';
	$imgData = imageData($p);
	if(in_array($type, array('movie','book','episode','show','track','album')) {
		$imgClass = $type;
	}
  if(in_array($imgClass,array('episode','show') )) {
    $imgClass = 'tv';
  }
  if(in_array($imgClass,array('track','album'))) {
    $imgClass = 'cd';
  }
  if($time !== $time_marker) {
    echo '<h2 class="sub-heading with-line"><span>'.$time.'</span></h2>'
  }
  $time_marker = $time;
  ?>
  <div data-key="<?= $k; ?>" class="media-item type-<?=$type;?> blank above-line">
    <div class="img-container">
      <div class="media-image type-<?= $imgClass;?>">
        <img
          class="<?= $imgData['preload'];?> no-blur" 
          src="<?= $siteDir;?>/assets/imgs/blank_<?= $imgClass ?>.png" 
          data-src="<?= $imgURL;?>" 
          alt="<?= $p->post_title;?>" />
      </div>
    </div>
    <div class="info">
      <?php infoSwitch(
        array(
         'title'=>$p->post_title,
         'type' => $type,
         'show' => array(
           'title'=> $data['show']['title']
          ),
         'album' => array(
          'title' => $data['album'['title'],
          'artists' => $data['album']['artists']
         ),
         'bingeCount' => intval(get_post_meta($p->ID, 'bingeCount',true)),
         'listenCount' => intval(get_post_meta($p->ID, 'listenCount',true)),
         'authors' => $data['authors'],                 
         'percent' => $data['percent'],
         'status' => $data['status']
        )
       );?>
    </div>
    
  </div>
  
  <?php
  
  
}
?>
  
  
  
</div>



<?php include_once 'footer.php';?>
