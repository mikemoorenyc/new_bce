<?php
if(!$landing_header_title) {
  $landing_header_title = $post->post_title;
}

 ?>

<div class="gl-mod landing-header  ">
  <div class="content-centerer grid-blank">
    <h1 class="landing-header__title article-heading "><?= $landing_header_title;?></h1>
    <?php
    $excerpt = $excerpt ?: get_the_excerpt();
    if($excerpt):?>
    <div class="landing-header__excerpt tagline"><?= md_sc_parse($excerpt);?></div>
    <?php endif;?>
  </div>

</div>
<hr class="gl-mod landing-header-rule" />
