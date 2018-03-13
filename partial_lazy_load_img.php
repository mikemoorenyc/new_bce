
<?php
$imgs = get_all_image_sizes($img_id);
$srcset=[];
$real_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $alt_tag;
foreach($imgs as $i) {

      $srcset[] =  ($i['url'].' '.$i['width'].'w');

}
?>

<img src="<?= $imgs['preload']['url'];?>" 
  class="poster-image preload-image"
  data-src="<?= $imgs['full']['url'];?>"
  data-srcset="<?= implode(',',$srcset);?>"
  alt="<?= $real_alt;?>"
/>

