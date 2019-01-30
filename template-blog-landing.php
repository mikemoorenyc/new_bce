<?php
/**
 * Template Name: Blog Landing Page
 */
?>
<?php include_once 'header.php';?>

<?php $landing_post = $post;?>

<?php include_once 'partial_landing_page_header.php';?>
<div class="bl post-list">
<?php
//GET THE POSTS
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
  'post_type' 		=> 'post',
  'posts_per_page' => 20,
  'paged' => $paged
);
query_posts($args);
while ( have_posts() ) : the_post(); ?>
<?php
$class_no_thumbnail = '';
if(has_post_thumbnail()) {
 $class_no_thumbnail = 'with-thumbnail';
}
?>

<article class="<?= $class_no_thumbnail;?> bl post  full-click-area  clearfix gl-line-hover">
  <a class="area" href="<?= get_the_permalink();?>" role="presentation" aria-hidden="true"></a>
  <div class="grid-container content-centerer ">
  <?php
  if(has_post_thumbnail()):?>
  <?php
  $all_images = get_all_image_sizes(get_post_thumbnail_id());
  ?>
  <a class="thumbnail-container" href="<?= get_the_permalink();?>">
    <?php echo postimage_shortcode(array(
      'type' => 'normal',
      'id' => get_post_thumbnail_id()
    ));?>
  </a>
  <?php endif; ?>
  <div class="copy  <?= $class_no_thumbnail;?>">
    <a href="<?= get_the_permalink();?>" class="date meta no-underline media-item">

      <?= get_the_date('M j Y') ?>
    </a>
    <h2 class="mid-heading media-item"><a class="no-underline h-child" href="<?= get_the_permalink();?>"><?= get_the_title();?></a></h2>
    <a class="excerpt type-smaller tagline no-underline media-item" href="<?= get_the_permalink();?>">
        <?php
        if(has_excerpt()) {
          $desc = get_the_excerpt();
        } else {
          $desc = global_excerpter(get_the_content(),100);
        }
        echo $desc.'...';
         ?>

    </a>
  </div>

</div>

</article>


<?php endwhile;?>

</div>

<?php
$older_link = get_next_posts_link();
$newer_link = get_previous_posts_link();
 ?>
<?php include_once 'partial_landing_page_pagination.php';?>


<?php include_once 'footer.php';?>
