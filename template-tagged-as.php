<?php
/**
 * Template Name: Tagged As Page
 */
?>
<?php
$content_ids = explode("|",$_GET['types']);
$tagged_ids = explode("|",$_GET['tags']);
if(empty($content_ids)) {
 $content_title = 'Content';
} else {
 $name_array = [];
 foreach($content as $c) {
  $name_array[] = get_post_type_object( $c )->labels['name'];
 }
 $content_title = implode(' & '$name_array);
}
if(empty($tagged_ids)) {
 $content_tag = '';
} else {
 $tag_list = []
 foreach($tagged_ids as $t)) {
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
<h1><?= $tagged_as_page;?></h1>

<?php
$all_content = get_post_types( array('public' => true), 'objects' );
$all_tags = get_tags(  );
$all_content_ids = array_map(function ($c) { return $c->name; }, $all_content);
$all_tag_ids = array_map(function ($c) { return $c->term_id; }, $all_content);

?>

<ul class="content-types">
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
   <a href="<?= $href; ?>"><?= $all_content[$i]->labels['name'];?></a>
 </li>
  <?php
 }
 
 
 ?>
 
 
</ul>

<ul class="tags">
 <?php 
 foreach($all_tag_ids as $i => $c) {
  $c_ids = $content_ids;
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




<?php include_once "footer.php";?>
