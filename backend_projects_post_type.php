<?php
function project_post_init() {
//PROPERTY
$args = array(
  'label' => 'Projects',
  'public' => true,
  'labels' => array(
    'add_new_item' => 'Add New Project',
    'name' => 'Projects',
    'edit_item' => 'Edit Project',
    'search_items' => 'Search Projects',
    'not_found' => 'No Projects found.',
    'all_items' => 'All Projects'
  ),
  'show_ui' => true,
  'capability_type' => 'page',
  'hierarchical' =>true,
  'has_archive' => false,
  'rewrite' => array('slug' => 'project'),
  'query_var' => true,
  'menu_icon' =>'dashicons-hammer',
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
register_post_type( 'project', $args );
register_taxonomy_for_object_type( 'post_tag', 'project' );
}
add_action( 'init', 'project_post_init' );
 ?>
