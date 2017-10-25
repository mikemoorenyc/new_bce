<?php
/**
 * Template Name: Tagged As Page
 */
?>
<?php

function readableList($ids, $type) {
  $listItems = [];
  if($type === 'type') {
    foreach ($ids as $i) {
      $listItems[] = get_post_type_object( $i )->labels->name;
    }
  }
  if($type === 'tag') {
   foreach($ids as $t) {
     $listItems[] = get_term($t)->name;
   }
  }
  if(count($listItems) < 2) {
    return $listItems[0];
  }
  $output = '';
  foreach($listItems as $k => $li) {
    if($k > 0 && $k < count($listItems) - 1) {
     $output .= ', ';
    }
    if($k === count($listItems)-1) {
     $output .= ' & ';
    }
    $output .= $li;
  }
  return $output;
}

$content_ids = (!empty($_GET['types'])) ? explode("|",$_GET['types']) : array();
$tagged_ids = (!empty($_GET['tags'])) ? explode("|",$_GET['tags']) : array();

function readableTags($ids) {
  $output = '';
  $loop = 0;
  $names = [];
  foreach($ids as $k => $i) {
    $name = get_term($i)->name;
    if(!$name) {
      continue;
    }
    $names[] = '&ldquo;'.$name.'&rdquo;';
  }
  foreach($names as $k=>$n) {
    if($k === 0) {
      $output .= $n;
      continue;
    }
    if($k !== count($names)-1) {
      $output.= ', '.$n;
    } else {
      $output .= ' & '.$n;
    }
  }
  return $output;
}


$content_title = (empty($content_ids)) ? 'content' : readableList($content_ids, 'type');
$content_tag = (empty($tagged_ids)) ? '' : ' tagged with: '.readableTags($tagged_ids);


$landing_header_title = 'Content Archive';
$landing_excerpt = (empty($content_ids)&& empty($tagged_ids)) ? '' : 'Showing '.$content_title.$content_tag;
$tagged_as_page = $landing_excerpt;

 ?>
<?php include_once "header.php";?>

<?php

 ?>
<?php include_once 'partial_landing_page_header.php';?>

<?php
//$all_content = get_post_types( array('public' => true), 'objects' );
$all_tags = get_tags(  );

$all_content = array(
  array(
    'slug' => 'post',
    'label' => 'Posts'
  ),
  array(
    'slug' => 'project',
    'label' => 'Projects'
  )
);

$all_content_ids = array_map(function ($c) { return $c['slug']; }, $all_content);
$all_tag_ids = array_map(function ($c) { return $c->term_id; }, $all_tags);

?>

<ul class="content-types hide">
 <?php foreach($all_content as $c):?>

  <?php
  $content_links = $content_ids;
  $selected ='';
  if(in_array($c['slug'], $content_ids)) {
   $selected = 'selected';
   $content_links = array_filter($content_links, function($l){
    return $l !== $c['slug'];
   });
  } else {
    $content_links[] = $c['slug'];
  }

  ?>

  <li class="<?= $selected;?>">
    <a href="<?= get_permalink();?>?types=<?= implode('|', $content_links) ?>&tags=<?= implode('|',$tagged_ids); ?>">
      <?= $c['label'];?>
    </a>
  </li>


  <?php endforeach;?>

</ul>

<ul class="tags hide">
 <?php
 foreach($all_tags as $i => $c):?>
  <?php
  $t_links = $tagged_ids;
  $selected = '';
  if(in_array($c->term_id, $content_ids)) {
    $selected = 'true';
    $t_links = array_filter($t_links, function($l){
      return $l !== $c->term_id;
    });
  } else {
   $t_links[] = $c->term_id;
  }

  ?>


  <li class="<?= $selected;?>">
    <a href="<?= get_permalink();?>?types=<?= implode('|', $content_ids) ?>&tags=<?= implode('|',$t_links); ?>">
      <?= $c->name;?>
    </a>
  </li>




 <?php endforeach; ?>


</ul>
<div class="gl-mod project-card-container content-centerer">
<?php

if(empty($content_ids)) {
 $content_ids = ['post','project'];
}
$query_args = array(
 'post_type' => $content_ids,
 'posts_per_page' => -1
);
if(!empty($tagged_ids)) {
 $query_args['tag__in'] = $tagged_ids;
}
$files_in_cat_query = new WP_Query($query_args);
?>
<?php if ( $files_in_cat_query->have_posts() ) :?>
<?php
$posts = $files_in_cat_query->get_posts();
foreach($posts as $p):?>

<?php

$pid = $p->ID;
$alt_tag = $p->post_title;

$img_id = get_post_thumbnail_id($pid);
$hide_image = ($img_id) ? false : true;

if(get_post_type($pid) === 'post') {
 $card_meta = 'A blog post from '.get_the_date('F Y',$pid);
}
if(get_post_type($pid) === 'project') {
 $card_meta = 'A project from '.get_the_date('F Y',$pid);
}

$post_type = get_post_type($pid);

include 'partial_project_card.php';

 ?>

<?php endforeach;?>


<?php endif;?>

<?php if ( !$files_in_cat_query->have_posts() ) :?>
<h2>Sorry, there's no posts for that.</h2>
<?php endif;?>

</div>
<?php include_once "footer.php";?>
