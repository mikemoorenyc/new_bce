<?php
/**
 * Template Name: Home Page
 */
?>


<?php include 'header.php'; ?>
<section class="hp top-content nav-spacer">
  <h1 class="story-title">
  <?= md_sc_parse($post->post_content);?>
  </h1>
</section>


  <?php

  $args = array(
    'post_type' 		=> 'project',
    'posts_per_page' => 3,
    'orderby' 			=> 'menu_order',
    'order' 			=> 'ASC'
  );
  $project_query = new WP_Query($args);
?>
<?php if ( $project_query->have_posts() ) :?>
<section class="hp projects clearfix">
<h2 class="hp sub-heading">Projects</h2>
<?php $projects = $project_query->get_posts(); ?>

<?php
foreach($projects as $p) {
  $pid = $p->ID;

  ?>
  <article class="project above-line poster-img">
    <?php
    if(!has_post_thumbnail($pid)) {
      $socialImg = get_all_image_sizes(get_option( 'social_icon_image', '' ));
      echo '<img src="'.$socialImg['full']['url'].'" alt="'.get_the_title($pid).'" class="project-img"/>';
    } else {
      $imgs = get_all_image_sizes(get_post_thumbnail_id($pid));
      $srcset="";
      $looper = 0;
      foreach($imgs as $i) {
        if($looper > 0) {
          $srcset .= ',';
        }
        $srcset .= ($i['url'].' '.$i['width'].'w');

        $looper++;
      }
      ?>
      <img
      sizes="100vw"
      width="<?= $imgs['medium']['width'];?>"
      class="project-img"
      src="<?= $imgs['medium']['url'];?>"
      srcset="<?= $srcset;?>"
      alt="<?= $p->post_title;?>"
      />
      <?php
    }

     ?>
    <h3>
    <a href="<?=get_the_permalink($pid);?>">
      <div class="callout ">
        <span class="title"><?= $p->post_title;?></span>
        <span class="tagline font-sans"><?= get_post_meta( $pid, 'tagline', true );?></span>
      </div>
    </a>
  </h3>
  </article>

  <?php

}

 ?>
 <a href="<?= $homeURL;?>/projects" class="button-style bottom-button">See All Projects <span class="bug "><?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?></span></a>
</section>

<?php endif;?>
<?php
$pargs = array(
  'post_type' 		=> 'post',
  'posts_per_page' => 4
);
$post_query = new WP_Query($pargs);
 ?>
<?php if ( $post_query->have_posts() ) :?>
<section class="hp blog clearfix">
<h2 class="hp sub-header">From the Blog</h2>
<?php $posts = $post_query->get_posts(); ?>
<?php
foreach($posts as $p) {
  $pid = $p->ID;

  ?>
  <article class="post">
    <h3>
    <a href="<?= get_the_permalink($pid);?>"><span class="title"><?= get_the_title($pid);?></span></a>
    <span class="time font-sans">
      <?= human_time_diff( get_the_time('U', $pid), current_time('timestamp') ) . ' ago'; ?>
    </span>
    </h3>

  </article>

  <?php

}

 ?>

<a href="<?= $homeURL;?>/blog" class="button-style bottom-button">See All Blog Posts <span class="bug "><?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?></span></a>
</section>
<?php endif;?>


<?php include 'footer.php'; ?>
