<?php
/**
 * Template Name: Blog Landing Page
 */
?>
<?php include_once 'header.php';?>

<?php $landing_post = $post;?>

<?php include_once 'partial_landing_page_header.php';?>
<ul class="blog-posts-list">
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
<li>
<article class="<?= $class_no_thumbnail;?> post">
  <?php
  if(has_post_thumbnail()):?>
  <?php
  $all_images = get_all_image_sizes(get_post_thumbnail_id());
  ?>
  <div class="thumbnail-container">
    <div class="thumbnail-spacer">
      <img
          width="<?= $all_images['medium']['width'];?>"
          height="<?= $all_images['medium']['height'];?>"
         src="<?= $all_images['medium']['url']; ?>"
         srcset="<?= srcset_maker($all_images);?>"
         alt="<?= get_the_title();?>"
       />
    </div>
  </div>
  <?php endif; ?>

  <h1 class="title"><a href="<?= get_the_permalink();?>"><?= get_the_title();?></a></h1>
  <div class="excerpt"><?= get_the_excerpt();?></div>
  <div class="date">Published on <?= get_the_date('M j Y'); ?></div>
  <a class="poster-fill see-thru" href="<?= get_the_permalink();?>" role="presentation"></a>
</article>
</li>

<?php endwhile;?>
</ul>

<?php include_once 'partial_landing_page_pagination.php';?>


<?php include_once 'footer.php';?>
