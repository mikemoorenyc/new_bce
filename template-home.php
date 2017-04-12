<?php
/**
 * Template Name: Home Page
 */
?>


<?php include 'header.php'; ?>
<section class="top-content">
  <h1>
  <?php echo md_sc_parse($post->post_content);?>
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
<section class="home-projects">
<h2>Projects</h2>
<?php $projects = $project_query->get_posts(); ?>

<?php
foreach($projects as $p) {
  $pid = $p->ID;

  ?>
  <article class="project">
    <h3>
    <a href="<?php echo get_the_permalink($pid);?>">
      <div class="callout">
        <span class="title"><?php echo get_the_title($pid);?></span>
        <span class="tagline"><?php echo get_post_meta( $pid, 'tagline', true );?></span>
      </div>
    </a>
  </h3>
  </article>

  <?php

}

 ?>

</section>

<?php endif;?>
<?php
$pargs = array(
  'post_type' 		=> 'post',
  'posts_per_page' => 2
);
$post_query = new WP_Query($pargs);
 ?>
<?php if ( $post_query->have_posts() ) :?>
<section class="blog">
<h2>From the Blog</h2>
<?php $posts = $post_query->get_posts(); ?>
<?php
foreach($posts as $p) {
  $pid = $p->ID;

  ?>
  <article class="posts">
    <h3>
    <a href="<?php echo get_the_permalink($pid);?>">

        <span class="title"><?php echo get_the_title($pid);?></span>


    </a>
    </h3>
  </article>

  <?php

}

 ?>


</section>
<?php endif;?>


<?php include 'footer.php'; ?>
