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

<section class="home-projects">
  <?php
  
  $args = array(
    'post_type' 		=> 'post',
    'posts_per_page' => 4
  );
  query_posts($args);
while ( have_posts() ) : the_post();
?>
  
<article class="project">
  <h2>
  <a href="<?php echo get_the_permalink();?>">
    <div class="callout">  
      <span class="title"><?php echo get_the_title();?></span>
    </div>
  </a>
  </h2>
</article>
  
  
 <?php endwhile;?>
  
  <div class="project">
    
    
  </div>
  
  
</section>

<?php include 'footer.php'; ?>
