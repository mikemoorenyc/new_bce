<?php
function get_all_image_sizes($id) {
  if ( !wp_attachment_is_image( $id ) ) {
    return false;
  }
  $images = array();
  $sizes = get_intermediate_image_sizes();
	$sizes[] = 'full';
  
  foreach($sizes as $size) {
    $image = wp_get_attachment_image_src( $id, $size );
    if(!$image) {
      continue;
    }
    $images[$size] = array(
      'url' => $image[0],
      'width =>$image[1],
      'height'=>$image[2]
    );
  }
		
  return $images;




}

?>
