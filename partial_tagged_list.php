
<?php
$tagged_page = get_page_by_title( 'Tagged As Page' );
$t_url = get_permalink($tagged_page);
$tagged_terms = wp_get_post_terms( $tagged_post_id, 'post_tag' );
if(!empty($tagged_terms)):?>
<h2 style="position:fixed; left: -9999px; top: -9999px; font-size: .0001em;">This article has been tagged in the following categories</h2>
<ul class="gl-mod tagged-list font-sans media-item">

<?php foreach($tagged_terms as $t):?>
  <li  class="gl-box-shadow bs-trans bs-1">
    <a class="bs-child" href="<?= $t_url;?>?tags=<?= $t->term_id;?>"><?= $t->name;?></a>
  </li>
<?php endforeach;?>
</ul>


<?php endif;?>
