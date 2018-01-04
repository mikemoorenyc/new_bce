<?php
/**
 * Template Name: About Page
 */
?>
<?php include_once 'header.php';?>
<?php $landing_post = $post;?>

<?php include_once 'partial_landing_page_header.php';?>
<div class="about-page gl-mod grid-blank col-2-setup content-centerer " >


 <?php
 $svgPath = get_template_directory().'/assets/svgs/';
 $svgs = new DirectoryIterator($svgPath);
 $profile_imgs = [];
 foreach($svgs as $s) {
  if(!$s->isFile() || $s->getExtension() !== 'svg' || strpos($s->getFilename(),'portrait') === false) {
   continue;
  }
  $profile_imgs[] = $s->getFilename();

 }
 if(!empty($profile_imgs)) {

  $rando = mt_rand(0, count($profile_imgs) - 1);
  $svg = file_get_contents($svgPath.$profile_imgs[$rando]);
  $dom = new SimpleXMLElement($svg);
  $view = $dom->attributes()['viewBox'];
  $view = explode(' ',$view);
  $percentage = round(($view[3] / $view[2])*100);

  ?>
<div class="right-col picture">
<div class="inner"  style="padding-top: <?php echo $percentage;?>%">
<?php echo $svg;?>
</div>

</div>
  <?php


 }

 ?>





<div id="main-about-content" class=" left-col about-page reading-section no-padding">
 <div class="content"><?=  md_sc_parse($post->post_content); ?></div>
</div>

<div class="right-col about-page like-lists">
<?php
$things_i_like = input_to_array(get_post_meta( $post->ID, 'things_i_like', true));

if(!empty($things_i_like)):?>
<div class="about-page like-block font-sans">
  <h2>Things I Like</h2>
  <ul>
    <?php foreach($things_i_like as $t):?>
    <li><?= $t[0];?></li>
  <?php endforeach;?>
  </ul>
</div>

<?php endif;?>
<?php
$things_i_dont_like = input_to_array(get_post_meta( $post->ID, 'things_i_dont_like', true));
if(!empty($things_i_dont_like)):?>
<div class="about-page like-block font-sans">
  <h2>Things I Don't Like</h2>
  <ul>
    <?php foreach($things_i_dont_like as $t):?>
    <li><?= $t[0];?></li>
  <?php endforeach;?>
  </ul>
</div>

<?php endif;?>
</div>

</div>


<?php include_once 'footer.php';?>
