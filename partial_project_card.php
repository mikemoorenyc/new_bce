
<article class="project-card above-line drop-shadow">
  <?php
  $hider = '';
  if(!has_post_thumbnail($pid) && $hide_image) {
    $hider = 'hide';
  }

   ?>
  <a href="<?=get_the_permalink($pid);?>" class="poster-image-container preload-image-container <?= $hider;?>">
    <?php
  if(!has_post_thumbnail($pid)) {

    $socialImg = get_all_image_sizes(get_option( 'social_icon_image', '' ));
    echo '<img src="'.$socialImg['full']['url'].'" alt="'.get_the_title($pid).'" class="poster-image"/>';
  } else {
    $imgs = get_all_image_sizes(get_post_thumbnail_id($pid));
    $srcset=[];
    foreach($imgs as $i) {

      $srcset[] =  ($i['url'].' '.$i['width'].'w');


    }
    ?>
    <img src="<?= $imgs['preload']['url'];?>" class="poster-image preload-image" />
    <img
    sizes="100vw"
    style="visibility:hidden;"
    class="poster-image"
    data-src="<?= $imgs['medium']['url'];?>"
    data-srcset="<?= implode(',',$srcset);?>"
    alt="<?= $p->post_title;?>"
    />
    <?php
  }

   ?>





  </a>

  <h3>
  <a href="<?=get_the_permalink($pid);?>">
    <div class="callout ">
      <span class="title"><?= get_the_title($pid);?></span>
      <span class="tagline font-serif"><?= get_post_meta( $pid, 'tagline', true );?></span>
    </div>
  </a>
</h3>
</article>
