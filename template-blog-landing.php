<?php
/**
 * Template Name: Blog Landing Page
 */
?>
<div class="landing-page-header">
  <h1 class="landing-page-header__title"><?= $post-post_title;?></h1>
  <?php
  $excerpt = get_the_excerpt();
  if($excerpt):?>
  <h2 class="landing-page-header__excerpt"><?= $excerpt;?></h2>
  <?php endif;?>
</div>

<?php
//GET THE POSTS
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
  'post_type' 		=> 'post',
  'posts_per_page' => 24,
  'paged' => $paged
);
query_posts($args);
while ( have_posts() ) : the_post(); ?>

<div class="post">
  
  
  
</div>


<?php endwhile;?>
