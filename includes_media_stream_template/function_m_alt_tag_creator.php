<?php
function media_alt_tag_creator($type, $title) {
$title = strip_tags($title);
	switch ($type) {
    case "movie":
        return "Poster for ".$title;
        break;
    case "track":
    case "album":
    case "book":
        return "Cover for ".$title;
        break;
    case "show":
    case "episode":
        return "Still of ".$title;
        break;
    default:
       return $title;
  }

}

 ?>
