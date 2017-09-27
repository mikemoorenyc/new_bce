<?php
if(!$_GET['url']) {
  http_response_code(404);
  die();
}
$url = urldecode($_GET['url']);

$im = @imagecreatefromjpeg( urldecode($_GET['url']));
if(!$im) {
  http_response_code(404);
  die();
}

header( "Content-Type: image/jpeg" );
imagejpeg($im);
imagedestroy($im);
die();
 ?>
