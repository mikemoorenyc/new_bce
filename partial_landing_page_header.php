<?php
if(!$landing_header_title) {
  $landing_header_title = $post->post_title;
}

 ?>

<div class="gl-mod landing-header <?= $navigation_spacer;?> gutter  mw-800">

  <h1 class="landing-header__title article-heading mar-10"><?= $landing_header_title;?></h1>
  <?php
  $excerpt = get_the_excerpt();
  if($excerpt):?>
  <div class="landing-header__excerpt tagline type-smaller"><?= $excerpt;?></div>
  <?php endif;?>

</div>
<hr class="gl-mod landing-header-rule mar-20" />
