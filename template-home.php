<?php
/**
 * Template Name: Home Page
 */
?>


<?php include 'header.php'; ?>
<section class="hp top-content nav-spacer gl-mod grid-blank">
  <h1 class="story-title">
  <?= md_sc_parse($post->post_content);?>
  </h1>
</section>


  <?php

  $args = array(
    'post_type' 		=> 'project',
    'posts_per_page' => 4,
    'orderby' 			=> 'menu_order',
    'order' 			=> 'ASC'
  );
  $project_query = new WP_Query($args);
?>
<?php if ( $project_query->have_posts() ) :?>
<section class="hp projects clearfix mw-800">
<h2 class="hp sub-heading with-line">Projects</h2>
<?php $projects = $project_query->get_posts(); ?>
<div class="hp project-list">
<?php
foreach($projects as $p) {
  $pid = $p->ID;
  $alt_tag = $p->post_title;

  if(!has_post_thumbnail($pid)) {
   if(!empty(get_option( 'social_icon_image', '' ))) {
     $img_id = get_option( 'social_icon_image', '' );
   } else {
    $hide_image = true;
   }
  } else {
    $img_id = get_post_thumbnail_id($pid);
  }

  include 'partial_project_card.php';
}

 ?>
 </div>


 <?php
 $button_URL = $homeURL.'/projects';
 $button_copy = 'See All Projects';
 include 'partial-bottom-button.php';
  ?>
</section>

<?php endif;?>
<?php
$pargs = array(
  'post_type' 		=> 'post',
  'posts_per_page' => 4
);
$post_query = new WP_Query($pargs);
 ?>
<?php if ( $post_query->have_posts() ) :?>
<section class="hp blog clearfix mw-800">
<h2 class="hp sub-heading width">From the Blog</h2>
<ul class="hp blog-posts">
<?php $posts = $post_query->get_posts(); ?>
<?php
foreach($posts as $p) {
  $pid = $p->ID;

  ?>
  <li class="post full-click-area stripe-hover">
    <a class="area" href="<?= get_the_permalink($pid);?>" role="presentation" aria-hidden="true"></a>

    <a href="<?= get_the_permalink($pid);?>">
      <h3 class="title mid-heading"> <?= get_the_title($pid);?></h3>
      <span class="time meta"><?= human_time_diff( get_the_time('U', $pid), current_time('timestamp') ) . ' ago'; ?></span>
    </a>



  </li>

  <?php

}

 ?>
</ul>
<?php
$button_URL = $homeURL.'/blog';
$button_copy = 'See All Blog Posts';
include 'partial-bottom-button.php';
 ?>
</section>
<?php endif;?>


<?php include 'footer.php'; ?>
