<?php

function create_imgClass($p) {
  $imgClass = 'other';
  $terms =  get_the_terms($p->ID, 'consumed_types');
  if(!$terms) {
    return "other";
  }
  $c_type = get_the_terms($p->ID, 'consumed_types')[0]->slug;

  if(in_array($c_type, array('movie','book')) ){
		return $c_type;
	}
  if(in_array($c_type, ['episode', 'show'])) {
    return 'tv';
  }
  if(in_array($c_type, ['album','track'])) {
    return 'cd';
  }


}

 ?>
