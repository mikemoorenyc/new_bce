<?php
function media_post_init() {
//PROPERTY
$args = array(
  'label' => 'Consumed',
  'public' => false,
  'labels' => array(
    'add_new_item' => 'Add New Consumed',
    'name' => 'Consumed',
    'edit_item' => 'Edit Consumed',
    'search_items' => 'Search Consumed',
    'not_found' => 'No Consumed found.',
    'all_items' => 'All Consumed'
  ),
  'show_ui' => true,
  'capability_type' => 'post',
  'hierarchical' =>false,
  'has_archive' => false,
  'rewrite' => array('slug' => 'consumed'),
  'query_var' => true,
  'menu_icon' =>'dashicons-desktop',
  'supports' => array(
      'title',
      'editor',
      'revisions',
      'page-attributes',
      'thumbnail',
      'excerpt',
      'custom-fields'
    )
  );
register_post_type( 'consumed', $args );
$tax_args = array(
    'hierarchical'      => true,
		'label'            => 'Type',
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'consumed_types' ),
);
register_taxonomy( 'consumed_types', array( 'consumed' ), $tax_args );
}
add_action( 'init', 'media_post_init' );


 ?>
