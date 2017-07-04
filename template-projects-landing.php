<?php
/**
 * Template Name: Projects Landing Page
 */
?>
<?php include 'header.php'; ?>
<?php $landing_post = $post;?>
<?php $navigation_spacer = 'navigation_spacer';?>
<?php include_once 'partial_landing_page_header.php';?>


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
<div class="pl projects mar-20">
<?php while ( have_posts() ) : the_post(); ?>

<?php
$pid = get_the_ID();
include 'partial_project_card.php';

 ?>


<?php endwhile;?>
</div>



<?php include_once 'partial_landing_page_pagination.php';?>

<?php include 'footer.php'; ?>
