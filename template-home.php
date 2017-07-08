<?php
/**
 * Template Name: Home Page
 */
?>


<?php include 'header.php'; ?>
<section class="hp top-content nav-spacer mw-800">
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
<h2 class="hp sub-heading">Projects</h2>
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
 <a href="<?= $homeURL;?>/projects" class="button-style hp bottom-button">See All Projects <span class="bug "><?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?></span></a>
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
<h2 class="hp sub-heading">From the Blog</h2>
<ul class="hp blog-posts">
<?php $posts = $post_query->get_posts(); ?>
<?php
foreach($posts as $p) {
  $pid = $p->ID;

  ?>
  <li class="post">
    <h3>
    <a href="<?= get_the_permalink($pid);?>">
      <span class="title mid-heading"> <?= get_the_title($pid);?></span>
      <span class="time meta"><?= human_time_diff( get_the_time('U', $pid), current_time('timestamp') ) . ' ago'; ?></span>
    </a>

    </h3>

  </li>

  <?php

}

 ?>
</ul>
<a href="<?= $homeURL;?>/blog" class="button-style hp bottom-button">See All Blog Posts <span class="bug "><?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?></span></a>
</section>
<?php endif;?>


<?php include 'footer.php'; ?>
