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
  $img_id = (has_post_thumbnail($pid)) ? get_post_thumbnail_id($pid) : get_option( 'social_icon_image', '' );
  $hide_image = ($img_id) ? false : true;

  ?>
  <a class="hp project-link no-underline above-line " href="<?= get_the_permalink($pid);?>">
    <span class="img-container">
      <?php
      include 'partial_lazy_load_img.php';
     ?>
    </span>

    <span class="callout ">
      <h2><?= $p->post_title;?></h2>
      <?php if(get_post_meta( $pid, 'tagline', true )):?>
<span class="tagline"><?= get_post_meta( $pid, 'tagline', true );?></span>
      <?php endif;?>
    </span>


  </a>

  <?php

}

 ?>
 </div>



</section>
<?php
$button_URL = $homeURL.'/projects';
$button_copy = 'See All Projects';
include 'partial-bottom-button.php';
 ?>
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
<h2 class="hp sub-heading with-line">From the Blog</h2>
<ul class="hp blog-posts">
<?php $posts = $post_query->get_posts(); ?>
<?php
foreach($posts as $p) {
  $pid = $p->ID;

  ?>
  <li class="post full-click-area ">
    <a class="area" href="<?= get_the_permalink($pid);?>" role="presentation" aria-hidden="true"></a>

    <a href="<?= get_the_permalink($pid);?>">
      <span class="time meta"><?= human_time_diff( get_the_time('U', $pid), current_time('timestamp') ) . ' ago'; ?></span>
      <h3 class="title mid-heading"> <span><?= get_the_title($pid);?></span></h3>

    </a>



  </li>

  <?php

}

 ?>
</ul>

</section>
<?php
$button_URL = $homeURL.'/blog';
$button_copy = 'See All Blog Posts';
include 'partial-bottom-button.php';
 ?>
<?php endif;?>


<?php include 'footer.php'; ?>
