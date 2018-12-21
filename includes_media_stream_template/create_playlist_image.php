<?php
function create_playlist_image($data) {
  $images = $data['playlist']['images'];

  array_reverse($images);
  echo '<span class="playlist-blank">';
  foreach($images as $k => $i):
    if($k > 3) {
      break;
    }
    ?><span class="playlist-image <?= $pos[$k];?>" style="background-image: url(<?= $i;?>)" ></span><?php


  endforeach;
  echo '</span><span class="playlist-tag">Playlist</span>';

}
 ?>
