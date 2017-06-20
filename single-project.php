<?php
//PROJECT TEMPLATE
?>
<?php include 'header.php'; ?>
<?php
$noheaderClass="header-top-padding";

 ?>
<?php if(has_post_thumbnail()):?>

<div class="p-s top-hero poster-image-container preload-image-container above-line mar-10">
<?php
$noheaderClass="";
$imgs = get_all_image_sizes(get_post_thumbnail_id());
 ?>
<img class="poster-image preload-image" src="<?php echo $imgs['preload']['url'];?>" />
<img class="poster-image" style="visibility:hidden;" src="<?php echo $imgs['full']['url'];?>" srcset="<?php echo srcset_maker($imgs);?>" sizes="100vw" alt="<?php echo get_the_title();?>"/>
</div>


<?php endif; ?>
<div class="project-page-content-container content-padding-spacer <?php echo $noheaderClass;?>">
  <div class="p-s heading mar-20 gutter">
    <h1 class="p-s article-heading"><?= $post->post_title;?></h1>
    <?php if(!empty(get_post_meta( $post->ID, 'tagline', true ))):?>
   <h2 class='p-s tagline'><?php echo get_post_meta( $post->ID, 'tagline', true );?></h2>
   <?php endif; ?>
  </div>


  <?php
  $toplinks = input_to_array(get_post_meta( $post->ID, 'toplinks', true ));
  if(!empty($toplinks)):?>


  <?php
   if(count($toplinks)>2) {
    $fullCount = 'full-count';
   }
  ?>

  <div class="p-s top-links gutter mar-20 <?= $fullCount; ?>">
    <h3 class="">Links:</h3>
    <?php
    $linkArray = [];
    foreach($toplinks as $l) {
      $linkArray[] = ' <a class="font-sans" href="'.$l[1].'" target="_blank">'.$l[0].'</a>';
    }
    echo implode(',',$linkArray);
     ?>



  </div>


  <?php endif; ?>

  <div class="p-s reading-section gutter mar-10">

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
