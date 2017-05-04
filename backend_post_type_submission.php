<?php
function submission_type_init() {
//PROPERTY
$args = array(
  'label' => 'Submissions',
  'public' => false,
  'labels' => array(
    'name' => 'Submissions',
  ),
  'show_ui' => true,
  'capability_type' => 'post',
  'has_archive' => false,
  'rewrite' => array('slug' => 'submission'),
  'query_var' => true,
  'menu_icon' =>'dashicons-email',
  'supports' => array(
      'title',
      'editor',
      'revisions',
      'page-attributes',
      'custom-fields'
    )
  );
register_post_type( 'submission', $args );

}
add_action( 'init', 'submission_type_init' );
 ?>
