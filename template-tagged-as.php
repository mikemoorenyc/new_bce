<?php
/**
 * Template Name: Tagged As Page
 */
?>
<?php
$content_ids = [];
$tagged_ids = [];
if(!empty($_GET['types'])) {
  $content_ids = explode("|",$_GET['types']);
}
if(!empty($_GET['tags'])) {
  $tagged_ids = explode("|",$_GET['tags']);
}


if(empty($content_ids)) {
 $content_title = 'Content';
} else {
 $name_array = [];
 foreach($content_ids as $c) {
  $name_array[] = get_post_type_object( $c )->labels->name;
 }
 $content_title = implode(' & ',$name_array);
}
if(empty($tagged_ids)) {
 $content_tag = '';
} else {
 $tag_list = [];
 foreach($tagged_ids as $t) {
  $tag_list[] = get_term($t)->name;
 }
 $content_tag = ' tagged with: '.implode(', ',$tag_list);
}


$tagged_as_page = $content_title.$content_tag;
if(empty($content_ids)&& empty($tagged_ids)) {
 $tagged_as_page = 'Content Archive';
}
 ?>
<?php include_once "header.php";?>

<?php
$landing_header_title = $tagged_as_page;

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
 <?php
 foreach($all_content_ids as $i => $c) {
  $c_ids = $content_ids;
  if(($key = array_search($c, $c_ids)) !== false) {
    unset($c_ids[$key]);
  } else {
   $c_ids[] = $c;
  }
  $href = get_permalink().'?tags='.implode('|',$tagged_ids).'&types='.implode('|',$c_ids);
  ?>
  <li>

   <a href="<?= $href; ?>"><?= $all_content[$i]['label']; ?></a>
 </li>
  <?php
 }


 ?>


</ul>

<ul class="tags hide">
 <?php
 foreach($all_tag_ids as $i => $c) {
  $c_ids = $tagged_ids;
  if(($key = array_search($c, $c_ids)) !== false) {
    unset($c_ids[$key]);
  } else {
   $c_ids[] = $c;
  }
  $href = get_permalink().'?tags='.implode('|',$c_ids).'&types='.implode('|',$content_ids);
  ?>
  <li>

   <a href="<?= $href; ?>"><?= $all_tags[$i]->name;?></a>
 </li>
  <?php
 }


 ?>


</ul>
<div class="gl-mod project-card-container">
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
$hide_image = false;
if(!has_post_thumbnail($pid)) {
   $hide_image = true;
}
else {
   $img_id = get_post_thumbnail_id($pid);
}
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
