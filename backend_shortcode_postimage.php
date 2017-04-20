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
  ob_start();

  ?>
<div class="post-image above-line <?php echo $type;?> <?php echo $post_type;?>">

  <?php if($type ==='phone'):?>
  <div class="phone-img-container">
    <div class="phone-img">
      <div class="poster-img" style="padding-top:<?php echo $ratio;?>%">
        <img
         class="lazy-img"
         style="visibility:hidden;"
         alt="<?php echo $baseurl;?>"
         data-src="<?php echo $allImgs['full']['url'];?>"
         data-srcset="<?php echo srcset_maker($allImgs);?>"
       />
      </div>
    </div>
    <div class="home-btn"></div>
  </div>
  <? endif;?>

  <?php if($type ==='desktop'):?>
  <div class="desktop-img-container">
      <div class="circles"></div>
      <div class="poster-img" style="padding-top:<?php echo $ratio;?>%">
        <img
         class="lazy-img"
         style="visibility:hidden;"
         alt="<?php echo $baseurl;?>"
         data-src="<?php echo $allImgs['full']['url'];?>"
         data-srcset="<?php echo srcset_maker($allImgs);?>"
       />
      </div>
  </div>
  <? endif;?>


  <?php if(empty($type) || !in_array($type, $available_types) || $type === 'normal'):?>
  <div class="normal-img " style="max-width: <?php echo $allImgs['full']['width'];?>px">
    <div class="poster-img" style="padding-top:<?php echo $ratio;?>%">
      <img
       class="lazy-img"
       style="visibility:hidden;"
       alt="<?php echo $baseurl;?>"
       data-src="<?php echo $allImgs['full']['url'];?>"
       data-srcset="<?php echo srcset_maker($allImgs);?>"
       sizes="100vw"
     />
    </div>

  </div>
  <?php endif;?>

  <?php if(!empty($caption)):?>
  <div class="caption font-sans"><?php echo $caption;?></div>
  <?php endif;?>

</div>

  <?php
  return ob_get_clean();
}
add_shortcode( 'postimage', 'postimage_shortcode' );
