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
<?php
if(!has_post_thumbnail()) {
 $class_no_thumbnail = 'no_thumbnail'; 
}
?>
<article class="<?= $class_no_thumbnail;?> post">
  <?php
  if(has_post_thumbnail()):?>
  <?php
  $all_images = get_all_image_sizes(get_post_thumbnail_id());
  ?>
  <div class="thumbnail-container"> 
    <div class="thumbnail-spacer">
      <img
         src="<?= $all_images['small']['url']; ?>"  
         srcset="<?= srcset_maker($all_images);?>"
         alt="<?= get_the_title();?>"
       />
    </div>
  </div>
  <?php endif; ?>
  
  <h2 class="title"><?= get_the_title();?></h2>
  <div class="excerpt"><?= get_the_excerpt();?></div>
  <div class="date">Published on <?= get_the_date('M j Y'); ?></div>
  <a class="poster-fill see-thru" href="<?= get_the_permalink();?>" role="presentation"><?=get_the_title();?></a>
</article>

<?php endwhile;?>

<?php
$older_posts = get_next_posts_link();
$newer_posts = get_previous_posts_link();
if(!empty($older_posts)||!empty($newer_posts)):?>
<div class="bottom_pagination_links">
  
  <?php if(!empty($newer_posts)):?>
  <a href="<?= $homeURL.'/'.$post->post_name.'/'.($paged-1).'/' ?>" class="pagination_link newer_posts">Newer Posts</a>
  <?php endif;?>
  
  <?php if(!empty($older_posts)):?>
  <a href="<?= $homeURL.'/'.$post->post_name.'/'.($paged+1).'/' ?>" class="pagination_link older_posts">Older Posts</a>
  <?php endif;?>
  
</div>
<?php endif;?>

