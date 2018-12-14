<?php
function create_playlist_image($data) {
  $images = $data['playlist']['images'];
  if(!$images):
    ?>
    <span class="media-blank type-cd before-block after-block">

    </span>
    <span class="playlist-tag">Playlist</span>
    <?php
    return;
  endif;
  array_reverse($images);
  if(count($images) < 4):
    ?>
    <span class="playlist-blank" style="background-image: url(<?= $images[0]; ?>);"> </span>
    <span class="playlist-tag"> Playlist</span>
    <?php
    return;
  endif;
  echo '<span class="playlist-blank">';
    $pos = ['top left', 'top right', 'bottom left', 'bottom right'];
    foreach ($images as $k => $i):

      if($k > 3):
        break;
      endif;
      ?>
        <span class="playlist-image <?= $pos[$k];?>" style="background-image: url(<?= $i;?>)" ></span>
      <?php
    endforeach;
  echo '</span><span class="playlist-tag">Playlist</span>';
}
 ?>
