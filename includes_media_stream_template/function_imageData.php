<?php
function imageData($p) {
  global $siteDir;
  $preloadClass = 'preload-image';
	if(has_post_thumbnail($p->ID)) {
		$urls =  get_all_image_sizes(get_post_thumbnail_id($p->ID));
		$imgURL = $urls['medium']['url'];
    return array(
      'url' => $urls['medium']['url'],
      'preload' => $preloadClass
    );
	}
 $data = json_decode($p->post_content,true);
 $type = get_the_terms($p->ID, 'consumed_types');
 if($type){$type = $type[0]->slug;}
	$imgURL = '';
	switch ($type) {
		case "movie":
			$imgURL = get_post_meta($p->ID, 'imgURL',true);
			break;
		case "episode":
      $imgURL = get_post_meta($p->ID, 'imgURL',true) ?: get_post_meta($p->ID, 'showImgURL',true);
      break;
		case "show":
			$imgURL = get_post_meta($p->ID, 'showImgURL',true) ?: "";
			break;
		default:
			$imgURL = $data['img'] ?: $imgURL ;
			break;
	}



	$pURL = parse_url($imgURL);
	if($pURL['scheme'] !== 'https' && !empty($imgURL)) {
		$imgURL = '';
	}
 if(empty($imgURL)) {
  $preloadClass = '';
 }
 return array(
  'url' => $imgURL,
  'preload' => $preloadClass

 );

}


 ?>
