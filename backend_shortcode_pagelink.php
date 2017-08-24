<?php
function pagelink_shortcode( $atts, $content = null ) {

  $id = $atts['id'];

  $url = get_permalink($id);
  if(!$id || !$url) {
    return '';
  }
  $text = $content;
  if(!$content) {
    $text = get_the_title($id);
  }

	return '<a href="'.$url.'">'.$text.'</a>';
}
add_shortcode( 'pagelink', 'pagelink_shortcode' );
?>
