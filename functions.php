<?php
add_theme_support( 'menus' );

//New image sizes
add_image_size ( 'preload', 20 , 20 , false ) ;

add_post_type_support('page', 'excerpt');

add_filter( 'user_can_richedit' , '__return_false', 50 );

// Custom functions

// Tidy up the <head> a little. Full reference of things you can show/remove is here: http://rjpargeter.com/2009/09/removing-wordpress-wp_head-elements/
remove_action('wp_head', 'wp_generator');// Removes the WordPress version as a layer of simple security

add_theme_support('post-thumbnails');



add_action( 'admin_init', 'my_theme_add_editor_styles' );
function my_theme_add_editor_styles() {
  unregister_taxonomy_for_object_type( 'category', 'post' );
    add_editor_style( 'css/editor-styles.css' );
}



$dir = new DirectoryIterator(get_template_directory());

foreach ($dir as $i) {

    if($i->getExtension() !== 'php' || strpos( $i->getFilename() , 'backend_' ) === false || !$i->isFile()) {
     continue;
    }

    include_once $i->getPathname();
}

?>
