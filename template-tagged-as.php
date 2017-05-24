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






<?php include_once "footer.php";?>
