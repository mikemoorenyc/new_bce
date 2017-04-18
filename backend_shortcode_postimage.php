<?php
function postimage_shortcode( $atts, $content = null ) {
  global $post;
  $post_type = $post->post_type;
  $caption = md_sc_parse($content);
  $type = $atts['type'];
  $id = $atts['id'];
  $allImgs = get_all_image_sizes($id);
  

}
add_shortcode( 'postimage', 'postimage_shortcode' );
