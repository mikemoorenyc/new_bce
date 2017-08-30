<?php
if(!$_GET['url']) {
  http_response_code(404);
  die();
}
$url = urldecode($_GET['url']);
$handle = fopen($url , 'rb');
$img = new Imagick();
$img->readImageFile($handle);
$img->setImageFormat( "jpg" );
header( "Content-Type: image/jpeg" );
echo $img;
die();
 ?>
