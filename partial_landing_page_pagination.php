<?php
$older_posts = get_next_posts_link();
$newer_posts = get_previous_posts_link();
if(!empty($older_posts)||!empty($newer_posts)):?>
<div class="gl-mod bottom-ctas meta">

  <?php if(!empty($newer_posts)):?>
  <a href="<?= $homeURL.'/'.$landing_post->post_name.'/page/'.($paged-1).'/' ?>" class="previous-link"><?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?> Newer Posts</a>
  <?php endif;?>
  <?php if(empty($newer_posts)):?>

    <a ></a>
  <?php endif;?>

  <?php if(!empty($older_posts)):?>

  <a href="<?= $homeURL.'/'.$landing_post->post_name.'/page/'.($paged+1).'/' ?>" class="pagination_link older_posts">Older Posts <?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?></a> 
  <?php endif;?>
  <?php if(empty($older_posts)):?>

    <a ></a>
  <?php endif;?>

</div>
<?php endif;?>
