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
<img onload="posterSwap(this)" src="<?php echo $imgs['full']['url'];?>" srcset="<?php echo srcset_maker($imgs);?>" sizes="100vw" alt="<?php echo get_the_title();?>"/>
</div>


<?php endif; ?>
<div class="project-page-content-container <?php echo $noheaderClass;?>">
  <h1 class="project-page-title"><?php echo get_the_title();?></h1>
  <h2 class='project-page-tagline font-sans'><?php echo get_post_meta( $post->ID, 'tagline', true );?></h2>

  <div class="project-page-content">
    <?php echo md_sc_parse($post->post_content);?>

  </div>

</div>

<?php include 'footer.php'; ?>
