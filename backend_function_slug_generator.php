<?php
function slug_generator($id) {
  $thepost = get_post($id);
  if(!$thepost) {
   return false; 
  }
  if($thepost->post_type == 'post' || $thepost->post_name == 'blog') {
   return 'blog'; 
  }
  if($thepost->post_type == 'project') {
    return 'projects';
  }
  return $thepost->post_name;
}


?>
