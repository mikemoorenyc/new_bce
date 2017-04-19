<?php
function postimage_shortcode( $atts, $content = null ) {
  global $post;
  $post_type = $post->post_type;
  $caption = md_sc_parse($content);
  $type = $atts['type'];
  $id = $atts['id'];
  $allImgs = get_all_image_sizes($id);
  $ratio = ($allImgs['full']['height'] / $allImgs['full']['width']) * 100;
  $baseurl = basename($allImgs['full']['url']);
  ob_start();
  
  ?>
<div class="post-image <?php echo $type;?> <?php echo $post_type;?>">
  
 
  
  <?php if(!empty($caption)):?>
  <div class="caption"><?php echo $caption;?></div>
  <?php endif;?>
    
</div>

  <?php
  return ob_get_clean();
}
add_shortcode( 'postimage', 'postimage_shortcode' );
