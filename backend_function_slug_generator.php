<?php
function slug_generator($id) {
  $thepost = get_post($id);
  if(!$thepost) {
   return false;
  }
  var_dump($thepost);
  if($thepost->post_type == 'post' ) {
   return 'blog';
  }
  if($thepost->post_type == 'project') {
    return 'projects';
  }
  return $thepost->post_name;
}


?>
