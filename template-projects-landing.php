<?php
/**
 * Template Name: Projects Landing Page
 */
?>
<?php include 'header.php'; ?>
<?php $landing_post = $post;?>

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
<ul class="project-posts-list">
<?php while ( have_posts() ) : the_post(); ?>
<li>
  <article class="project-post">
    <h1><a href="<?= get_the_permalink();?>"><?= get_the_title();?></a></h1>
    <?php if(!empty(get_the_excerpt())): ?>
      <div class="excerpt"><?= get_the_excerpt();?></div>

    <?php endif;?>

  </article>



</li>
<?php endwhile;?>


</ul>



<?php include_once 'partial_landing_page_pagination.php';?>

<?php include 'footer.php'; ?>
