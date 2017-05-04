<?php
$older_posts = get_next_posts_link();
$newer_posts = get_previous_posts_link();
if(!empty($older_posts)||!empty($newer_posts)):?>
<div class="bottom_pagination_links">

  <?php if(!empty($newer_posts)):?>
  <a href="<?= $homeURL.'/'.$landing_post->post_name.'/page/'.($paged-1).'/' ?>" class="pagination_link newer_posts">Newer Posts</a>
  <?php endif;?>

  <?php if(!empty($older_posts)):?>

  <a href="<?= $homeURL.'/'.$landing_post->post_name.'/page/'.($paged+1).'/' ?>" class="pagination_link older_posts">Older Posts</a>
  <?php endif;?>

</div>
<?php endif;?>
