<?php
function httpcheck($url) {
  if(!$url) {
    return null;
  }
  $parsed_url = parse_url($url);

  if($parsed_url['scheme'] === 'https' || empty($url)) {
		return $url;
	}
  /*
  $img = @file_get_contents($url);
  if(!$img) {
    return '';
  }
  $filename_info = pathinfo($url);
  $upload = wp_upload_bits($filename_info['basename'], null, $img);
  if($upload['url']) {
    return $upload['url'];
  }
  */
  return null;
}


 ?>
