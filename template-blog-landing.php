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

<article class="post">
  <h2 class="title"><?= get_the_title();?></h2>
  <div class="excerpt"><?= get_the_excerpt();?></div>
  <div class="date">Published on <?= get_the_date('M j Y')?></div>
  <a class="poster-fill see-thru" href="<?= get_the_permalink();?>" role="presentation"><?=get_the_title();?></a>
</article>


<?php endwhile;?>
