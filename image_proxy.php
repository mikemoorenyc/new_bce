<?php
if(!$_GET['url']) {
  http_response_code(404);
  die();
}
$url = urldecode($_GET['url']);
$ext = pathinfo($url, PATHINFO_EXTENSION);
//var_dump($ext);
switch ($ext) {
    case "gif":
        header('Content-Type: image/gif');
        echo file_get_contents($url);
        break;
    case "png":
        header('Content-Type: image/png');
        echo file_get_contents($url);
        break;
    case "jpg":
    default:
        header('Content-Type: image/jpeg');
        echo file_get_contents($url);
        break;
}
die();
 ?>
