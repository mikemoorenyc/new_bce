<?php
//PROJECT TEMPLATE
?>
<?php include 'header.php'; ?>
<?php
$noheaderClass="no-header";

 ?>
<?php if(has_post_thumbnail()):?>

<div class="project-page-header poster-img above-line">
<?php
$noheaderClass="";
$imgs = get_all_image_sizes(get_post_thumbnail_id());
 ?>
<img class="preload" src="<?php echo $imgs['preload']['url'];?>" />
<img class="main-img" style="visibility:hidden;" src="<?php echo $imgs['full']['url'];?>" srcset="<?php echo srcset_maker($imgs);?>" sizes="100vw" alt="<?php echo get_the_title();?>"/>
</div>


<?php endif; ?>
<div class="project-page-content-container <?php echo $noheaderClass;?>">
  <h1 class="project-page-title"><?php echo get_the_title();?></h1>
  <h2 class='project-page-tagline font-sans'><?php echo get_post_meta( $post->ID, 'tagline', true );?></h2>
  <?php
  $toplinks = input_to_array(get_post_meta( $post->ID, 'toplinks', true ));
  if(!empty($toplinks)):?>
  <div class="project-top-links ">
    <?php foreach($toplinks as $l):?>
      <a class="font-sans" href="<?= $l[1];?>" target="_blank" style="width: <?= 100/count($toplinks);?>%">
        <?= $l[0];?>
      </a>
    <?php endforeach;?>
  </div>


  <?php endif; ?>

  <div class="project-page-content">

    <?php echo md_sc_parse($post->post_content);?>

  </div>
  <?php
  $whatilearned = input_to_array(get_post_meta( $post->ID, 'whatilearned', true ));
   if(!empty($whatilearned)):?>
<div class="project-what-i-learned font-sans">
  <h3 class="sub-header">What I Learned</h3>
  <ul class="clearfix">
    <?php foreach($whatilearned as $w):?>
      <li ><?= $w[0];?></li>
    <?php endforeach;?>
  </ul>

</div>
 <?php endif;?>

 <?php
     $tagged = wp_get_post_terms( $post->ID, 'post_tag' );
     if(!empty($tagged)):?>

<div class="project-tagged-in font-sans">
<h3>Tagged in:</h3>
<?php foreach($tagged as $t):?>

<span><?= $t->name;?></span>
<?php endforeach;?>
</div>

<?php endif; ?>

</div>

<?php
bottom_cta_maker(
  'project',
  'menu_order',
  array(
    "title" => 'All Projects',
    "url" => $homeURL.'/projects/'
  )
);

?>



<?php include 'footer.php'; ?>
