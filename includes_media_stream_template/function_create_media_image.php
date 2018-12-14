<?php

function create_media_image($p,$k) {
  $type = (get_the_terms($p->ID, 'consumed_types')) ? get_the_terms($p->ID, 'consumed_types')[0]->slug : '' ;
  $imgClass = create_imgClass($p);
  $imgData = imageData($p);
  $data = json_decode($p->post_content,true);
  $empty =(!$imgData['url']) ? "empty" :  "";
  $is_playlist = ($type === 'album' && $data['playlist']) ? true : false;
  $template_vars = array(
    '$imgClass' => $imgClass,
    '$empty' => $empty
  );
  $book_width = "";
	if($type === "book" && $data['dimensions']) {
		if($data['dimensions']['width'] > $data['dimensions']['height']) {
			$book_width = 'style="width: auto; height: auto; max-width: 74em;"';
		} else {
			$w = intval($data['dimensions']['width']);
			$h = intval( $data['dimensions']['height']);
			$d = 74 / $h;
			$new_w = $w * $d;
			$book_width = 'style="width: '.$new_w.'em;"';
		}
	}
  $container_top = strtr('<div class="media-image type-$imgClass $empty">',$template_vars);
  $container_bottom = "</div>";
  //PLAY LIST
  if($is_playlist):
    echo $container_top;
      create_playlist_image($data);
    echo $container_bottom;

    return false;
  endif;
  if (!$imgData['url'] && $imgClass !== "other" && !$is_playlist):
    $movie_svg = ($imgClass === "movie") ? file_get_contents(get_template_directory().'/assets/svgs/icon_'.$imgClass.'.svg') : '';
    ?>
  <?= $container_top ?>
    <span class="media-blank type-<?= $imgClass ?> before-block after-block">
      <?= $movie_svg ?>
    </span>
    <?php if($imgClass === "tv" || $imgClass == "book"): ?>
      <span class='helper-1 type-<?= $imgClass ?> before-block after-block'></span>
    <?php endif;?>

  <?= $container_bottom ?>
    <?php
  //END BLANK IF
    return false;
  endif;
  ?>
  <?= $container_top ?>
  <img
              class="<?= $imgData['preload'];?> no-blur"
              src="<?= get_bloginfo('template_url');?>/assets/imgs/blank_<?= $imgClass ?>.png"
              data-src="<?= $imgData['url'];?>"
              alt="<?= media_alt_tag_creator($type, $p->post_title);?>"
              <?= $book_width; ?>
  />
  <?= $container_bottom ?>

  <?php


}


 ?>
