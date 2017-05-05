<?php
function message_type_init() {
//PROPERTY
$args = array(
  'label' => 'Messages',
  'public' => false,
  'show_ui' => true,
  'capability_type' => 'post',
  'hierarchical' => false,
  'has_archive' => false,
  'rewrite' => array('slug' => 'message'),
  'query_var' => true,
  'menu_icon' =>'dashicons-admin-comments',
  'supports' => array(
      'title',
      'editor',
      'revisions',
      'page-attributes',
      'custom-fields'
    )
  );
register_post_type( 'message', $args );

}
add_action( 'init', 'message_type_init' );
 ?>
