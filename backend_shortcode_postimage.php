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
  
 
  <?php if(empty($type) || !in_array($type, $available_types) || $type === 'normal'):?>
  <div class="normal-img ">
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
  <?php endif;?>
  
  <?php if(!empty($caption)):?>
  <div class="caption"><?php echo $caption;?></div>
  <?php endif;?>
    
</div>

  <?php
  return ob_get_clean();
}
add_shortcode( 'postimage', 'postimage_shortcode' );
