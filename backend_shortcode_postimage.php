<?php
function postimage_shortcode( $atts, $content = null ) {
  global $post;
  $available_types = ['poster','phone','desktop','normal'];
  $post_type = $post->post_type;
  $caption = md_sc_parse($content);
  $type = $atts['type'];
  $id = $atts['id'];
  $allImgs = get_all_image_sizes($id);
  $ratio = ($allImgs['full']['height'] / $allImgs['full']['width']) * 100;
  $baseurl = basename($allImgs['full']['url']);
  if($allImgs['full']['width'] < 1000) {
    $max_width = 'max-width: '.$allImgs['full']['width'].'px;';
  }
  ob_start();

  ?>
<figure class="post-image above-line <?php echo $type;?> <?php echo $post_type;?>">

  <?php if($type ==='phone'):?>
  <div class="phone-image-container" >
    <div class="phone-image">
      <div class="poster-image-container" style="padding-top:<?php echo $ratio;?>%">
       <?php
        $img_id = $id;
       $alt_tag = $baseurl;
       include 'partial_lazy_load_img.php';
        ?>
      </div>
    </div>
    <div class="home-btn"></div>
  </div>
  <? endif;?>

  <?php if($type ==='desktop'):?>
  <div class="desktop-image-container" >
      <div class="circles"></div>
      <div class="poster-image-container" style="padding-top:<?php echo $ratio;?>%">
        <?php
        $img_id = $id;
       $alt_tag = $baseurl;
       include 'partial_lazy_load_img.php';
        ?>
      </div>
  </div>
  <? endif;?>


  <?php if(empty($type) || !in_array($type, $available_types) || $type === 'normal' || $type === 'poster'):?>
  <?php
  if($type === 'poster') {
   $full_bleed_class = 'full-bleed';
  }
  ?>
  <div class="normal-image-container gl-box-shadow bs-4 <?= $full_bleed_class;?>" style="<?= $max_width;?>">
    <div class="poster-image-container bs-child" style="padding-top:<?php echo $ratio;?>%">
      <?php
        $img_id = $id;
       $alt_tag = $baseurl;
       include 'partial_lazy_load_img.php';
        ?>
    </div>

  </div>
  <?php endif;?>

  <?php if(!empty($caption)):?>
  <figcaption class="font-sans"><?php echo $caption;?></figcaption>
  <?php endif;?>

</figure>

  <?php
  return ob_get_clean();
}
add_shortcode( 'postimage', 'postimage_shortcode' );
?>
