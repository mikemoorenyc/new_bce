
<?php
$imgs = get_all_image_sizes($img_id);
$srcset=[];

foreach($imgs as $i) {

      $srcset[] =  ($i['url'].' '.$i['width'].'w');

}
?>

<img src="<?= $imgs['preload']['url'];?>" 
  class="poster-image preload-image"
  data-src="<?= $imgs['full']['url'];?>"
  data-srcset="<?= implode(',',$srcset);?>"
  alt="<?= $alt_tag;?>"
/>

