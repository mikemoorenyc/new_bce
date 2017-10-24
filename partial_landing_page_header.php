<?php
if(!$landing_header_title) {
  $landing_header_title = $post->post_title;
}

 ?>

<div class="gl-mod landing-header  ">
  <div class=" gl-mod content-centerer grid-blank">
    <h1 class="landing-header__title article-heading "><?= $landing_header_title;?></h1>
    <?php
  
    if($landing_excerpt):?>
    <div class="landing-header__excerpt tagline"><?= md_sc_parse($landing_excerpt);?></div>
    <?php endif;?>
  </div>

</div>
<hr class="gl-mod landing-header-rule" />
