<?php
/**
 * Template Name: Projects Landing Page
 */
?>
<?php include 'header.php'; ?>
<?php $landing_post = $post;?>
<?php $navigation_spacer = 'navigation_spacer';?>
<?php include_once 'partial_landing_page_header.php';?>

<div class="gl-mod project-card-container">
<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type' 		=> 'project',
    'orderby' 			=> 'menu_order',
    'order' 			=> 'ASC',
    'posts_per_page' => 24,
    'paged' => $paged
  );
query_posts($args);
?>

<?php while ( have_posts() ) : the_post(); ?>

<?php
$hide_image = true;
if(has_post_thumbnail()) {
  $img_id = get_post_thumbnail_id();
  $hide_image = false;
}
$pid = get_the_ID();
include 'partial_project_card.php';

 ?>


<?php endwhile;?>





</div>
<?php
$older_link = get_next_posts_link();
$newer_link = get_previous_posts_link();


 ?>
<?php include_once 'partial_landing_page_pagination.php';?>

<?php include 'footer.php'; ?>
