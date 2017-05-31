<?php

function add_tagged_as_parameters( $vars ){
  $vars[] = "types";
  $var[] = "tags";
  return $vars;
}
add_filter( 'query_vars', 'add_tagged_as_parameters' );

?>
