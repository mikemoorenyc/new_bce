<div class="landing-page-header">
  <h1 class="landing-page-header__title"><?= $post->post_title;?></h1>
  <?php
  $excerpt = get_the_excerpt();
  if($excerpt):?>
  <div class="landing-page-header__excerpt"><?= $excerpt;?></div>
  <?php endif;?>
</div>
