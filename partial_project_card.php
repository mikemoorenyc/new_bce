
<article class="project-card above-line full-click-area gl-box-shadow bs-child bs-trans bs-2 gl-line-hover ">
  <a href="<?= get_the_permalink($pid);?>" class="area" aria-hidden="true" role="presentation"></a>
  <!-- IF YOU DO WANT AN IMAGE -->
  <?php if($hide_image !== true):?>
  <a href="<?=get_the_permalink($pid);?>" class="poster-image-container preload-image-container <?= $post_type;?>">
    <?php
    include 'partial_lazy_load_img.php';
   ?>
    <?php if($type_label):?>
    <span class="type-label"><?= $type_label;?></span>
    <?php endif;?>

  </a>

  <?php endif;?> <!-- HIDE IMAGE -->

  <h3>
  <a href="<?=get_the_permalink($pid);?>">
    <div class="callout ">
      <?php if($card_meta):?>
      <span class="meta"><?= $card_meta;?></span>
      <?php endif;?>
      <span class="title h-child <?= $post_type;?>"><?= get_the_title($pid);?></span>
      <span class="tagline font-serif"><?= get_post_meta( $pid, 'tagline', true );?></span>
    </div>
  </a>
</h3>

</article>
