<?php
$older_posts = get_next_posts_link();
$newer_posts = get_previous_posts_link();
if(!empty($older_posts)||!empty($newer_posts)):?>

<?php
$two_links = 'full-width';
if(!empty($older_posts)&&!empty($newer_posts)) {
  $two_links = 'half-width';
}

 ?>

<div class="gl-mod landing-pagination font-sans">

<div class="inner <?=$two_links;?> clearfix">
  <?php if(!empty($newer_posts)):?>
  <a href="<?= $homeURL.'/'.$landing_post->post_name.'/page/'.($paged-1).'/' ?>" class=" pagination-link newer-posts <?= $two_links;?>"><?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_left.svg');?> Newer Posts</a>
  <?php endif;?>


  <?php if(!empty($older_posts)):?>

  <a href="<?= $homeURL.'/'.$landing_post->post_name.'/page/'.($paged+1).'/' ?>" class="pagination-link older-posts <?= $two_links;?>">Older Posts <?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?></a>
  <?php endif;?>

</div>
</div>
<?php endif;?>
