<?php

if(!empty($newer_link)||!empty($older_link)):?>

<?php

$two_links = 'full-width';
if(!empty($newer_link)&&!empty($older_link)) {
  $two_links = 'half-width';
}
$older_copy = (gettype($older_link) === 'array' ) ? $older_link['copy'] : 'Older Posts';
$newer_copy = (gettype($newer_link) === 'array') ? $newer_link['copy'] : 'More Recent Posts';
$older_url = (gettype($older_link) === 'array') ? $older_link['url'] : get_the_permalink($landing_post).'page/'.($paged+1).'/';
$newer_url = (gettype($newer_link) === 'array') ? $newer_link['url'] : get_the_permalink($landing_post).'page/'.($paged-1).'/';
 ?>

<div class="gl-mod landing-pagination font-sans media-item">

<div class="inner <?=$two_links;?> clearfix">
  <?php if(!empty($newer_link)):?>
  <a href="<?= $newer_url ?>" class=" pagination-link newer-posts <?= $two_links;?>"><?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_left.svg');?> <?= $newer_copy;?></a>
  <?php endif;?>


  <?php if(!empty($older_link)):?>

  <a href="<?= $older_url ?>" class="pagination-link older-posts <?= $two_links;?>"><?= $older_copy;?> <?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?></a>
  <?php endif;?>

</div>
</div>
<?php endif;?>
