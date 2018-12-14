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
//GET ALL Stream Functions
$inc = new DirectoryIterator(get_template_directory().'/includes_media_stream_template');
foreach( $inc as $i):

  if($i->getExtension() === 'php'  && $i->isFile()) {
      include_once $i->getPathname();
  }
endforeach;
 ?>



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
//include_once 'switch_media_info.php';



 ?>
<div id="media-stream" class=" media-stream container gl-mod content-centerer">
<?php
$time_marker = "";
foreach($posts as $k => $p):
  $data = json_decode($p->post_content,true);
  $time = timeSet(strtotime($p->post_date));

  $type = (get_the_terms($p->ID, 'consumed_types')) ?
    get_the_terms($p->ID, 'consumed_types')[0]->slug : '' ;
  $imgClass = create_imgClass($p);

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
  <div data-key="<?= $k; ?>" class="media-item <?= $clickClass;?> type-<?=$type;?>  above-line">
    <div class="img-container">
      <?php create_media_image($p,$k); ?>
    </div>
    <div class="info">
			<?php

				//$showTitle = $data['show']['title'] ?: $p->post_title;
        create_media_info($p);
			?>
    </div>
    <?php if($data['clickthru']):?>
      <a class="poster" target="_blank" href="<?= $data['clickthru'];?>"></a>

    <?php endif;?>
  </div>

  <?php
	if($k === count($posts) -1) {
		echo '</div>';
	}

//End for the post Loop
endforeach;
?>



</div>
<?php

$newer_link = ($paged > 1) ? array('url'=> get_the_permalink()."page/".($paged-1).'/', 'copy' => 'More Recent') : false ;
$older_link = (($paged-1) * $per_page + count($posts) < intval(wp_count_posts('consumed')->publish)) ?  array('url'=> get_the_permalink().'page/'.($paged+1).'/', 'copy' => 'Less Recent') : false;

include_once 'partial_landing_page_pagination.php';

?>


<?php include_once 'footer.php';?>
