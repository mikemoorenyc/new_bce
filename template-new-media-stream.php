<?php
/**
 * Template Name: NEW Media Stream
 */
 date_default_timezone_set('America/New_York');
?>
<?php include_once 'header.php';?>
<?php
$landing_excerpt = get_the_excerpt($post);
$landing_post = $post;

?>

<?php include_once 'partial_landing_page_header.php';?>


<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = 32;

$posts = get_posts(
  array(
    'posts_per_page'   => $per_page,
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


function media_alt_tag_creator($type, $title) {
$title = strip_tags($title);
	switch ($type) {
    case "movie":
        return "Poster for ".$title;
        break;
    case "track":
    case "album":
    case "book":
        return "Cover for ".$title;
        break;
    case "show":
    case "episode":
        return "Still of ".$title;
        break;
    default:
       return $title;
  }

}

function imageData($p,$imgClass) {
  global $siteDir;
  $preloadClass = 'preload-image';
	if(has_post_thumbnail($p->ID)) {
		$urls =  get_all_image_sizes(get_post_thumbnail_id($p->ID));
		$imgURL = $urls['medium']['url'];
    return array(
      'url' => $urls['medium']['url'],
      'preload' => $preloadClass
    );
	}
 $data = json_decode($p->post_content,true);
 $type = get_the_terms($p->ID, 'consumed_types');
 if($type){$type = $type[0]->slug;}
	$imgURL = '';
	switch ($type) {
		case "movie":
			$imgURL = get_post_meta($p->ID, 'imgURL',true);
			break;
		case "episode":
		case "show":
			$imgURL = get_post_meta($p->ID, 'imgURL',true) ?: get_post_meta($p->ID, 'showImgURL',true);
			break;
		default:
			$imgURL = $data['img'] ?: $imgURL ;
			break;
	}



	$pURL = parse_url($imgURL);
	if($pURL['scheme'] !== 'https' && !empty($imgURL)) {
		$imgURL = '';
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

    $diff = ($today[1]+($today[2]*12)) - ($timeA[1]+($timeA[2]*12));

    return $diff.' month'.returnS($diff).' ago';
  }
  //WEEKS
  if($largeDiff > 6) {
    $diff = floor($largeDiff / 7);
    return $diff.' week'.returnS($diff).' ago';
  }
  //DAYS
  return ($largeDiff).' day'.returnS($largeDiff).' ago';
}
 ?>
<div id="media-stream" class=" media-stream container gl-mod content-centerer">
<?php
$time_marker = "";
foreach($posts as $k => $p) {
  $data = json_decode($p->post_content,true);
  $time = timeSet(strtotime($p->post_date));
  $type = get_the_terms($p->ID, 'consumed_types');
  if($type){$type = $type[0]->slug;}
  $imgClass = 'other';

	if(in_array($type, array('movie','book','episode','show','track','album')) ){
		$imgClass = $type;
	}
  if(in_array($imgClass,array('episode','show') )) {
    $imgClass = 'tv';
  }
  if(in_array($imgClass,array('track','album'))) {
    $imgClass = 'cd';
  }
  $imgData = imageData($p,$imgClass);
  if($time !== $time_marker) {
    if($k > 0) {
			echo '</div>';
    }
    echo '<h2 class="sub-heading with-line"><span>'.$time.'</span></h2>';
		echo '<div class="media-item-block">';

  }
  $time_marker = $time;
  $clickClass = ($data['clickthru']) ? 'clickthru' : '';

  ?>


	<?php
	$book_width = "";
	if($type === "book" && $data['dimensions']) {
		if($data['dimensions']['width'] > $data['dimensions']['height']) {
			$book_width = 'style="width: auto; height: auto; max-width: 74em;"';
		} else {
			$w = intval($data['dimensions']['width']);
			$h = intval( $data['dimensions']['height']);
			$d = 74 / $h;
			$new_w = $w * $d;
			$book_width = 'style="width: '.$new_w.'em;"';
		}
		
	}



	?>

  <?php
  $empty = "";
  if(!$imgData['url']) {
    $empty = "empty";
  }

   ?>

  <div data-key="<?= $k; ?>" class="media-item <?= $clickClass;?> type-<?=$type;?>  above-line">

    <div class="img-container">
      <div class="media-image type-<?= $imgClass;?> <?= $empty;?>">
				<?php if(!$imgData['url'] && $imgClass !== "other"): ?>
          <span class="media-blank type-<?= $imgClass;?> before-block after-block">
              <?php
                if($imgClass === "movie") {
                  echo file_get_contents(get_template_directory().'/assets/svgs/icon_'.$imgClass.'.svg');
                }
                ?>
          </span>
          <?php if($imgClass === "tv" || $imgClass == "book"): echo "<span class='helper-1 type-{$imgClass} before-block after-block'></span>"; endif;?>

				<?php else:?>

          <img
            class="<?= $imgData['preload'];?> no-blur"
            src="<?= $siteDir;?>/assets/imgs/blank_<?= $imgClass ?>.png"
            data-src="<?= $imgData['url'];?>"
            alt="<?= media_alt_tag_creator($type, $p->post_title);?>"
            <?= $book_width; ?>
          />

				<?php endif;?>
      </div>
    </div>
    <div class="info">
			<?php
				$showTitle = $data['show']['title'] ?: $p->post_title;
			?>

      <?php switch_media_info(
        array(
         'title'=>$p->post_title,
         'type' => $type,
         'show' => array(
           'title'=> $showTitle
          ),
         'album' => array(
          'title' => $data['album']['title'],
          'artists' => $data['album']['artists']
         ),
         'bingeCount' => $data['bingeCount'],
         'listenCount' => $data['listenCount'],
         'authors' => $data['authors'],
         'percent' => $data['percent'],
         'status' => $data['status'],
					'other_meta' => get_post_meta($p->ID, 'other_meta',true)

        )
       );?>
    </div>
    <?php if($data['clickthru']):?>
      <a class="poster" target="_blank" href="<?= $data['clickthru'];?>"></a>

    <?php endif;?>
  </div>

  <?php
	if($k === count($posts) -1) {
		echo '</div>';
	}

}
?>



</div>
<?php

$newer_link = ($paged > 1) ? array('url'=> get_the_permalink()."page/".($paged-1).'/', 'copy' => 'More Recent') : false ;
$older_link = (($paged-1) * $per_page + count($posts) < intval(wp_count_posts('consumed')->publish)) ?  array('url'=> get_the_permalink().'page/'.($paged+1).'/', 'copy' => 'Less Recent') : false;

include_once 'partial_landing_page_pagination.php';

?>


<?php include_once 'footer.php';?>
