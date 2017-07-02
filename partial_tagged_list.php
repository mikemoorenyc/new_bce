
<?php
$tagged_page = get_page_by_title( 'Tagged As Page' );
$t_url = get_permalink($tagged_page);
$tagged_terms = wp_get_post_terms( $tagged_post_id, 'post_tag' );
if(!empty($tagged_terms)):?>

<ul class="gl-mod tagged-list font-sans mar-20 gutter">

<?php foreach($tagged_terms as $t):?>
  <li  >
    <a href="<?= $t_url;?>?tags=<?= $t->term_id;?>"><?= $t->name;?></a>
  </li>
<?php endforeach;?>
</ul>


<?php endif;?>
