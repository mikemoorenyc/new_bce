<?php
/**
 * Template Name: Media Archive
 */
 date_default_timezone_set('America/New_York');
?>
<?php include_once 'header.php';?>
<?php
$landing_excerpt = get_the_excerpt($post);
$landing_post = $post;

?>

<?php include_once 'partial_landing_page_header.php';?>

<?php
$csv_dir =  get_home_path().'wp-content/media_dumps';
 $dir_iterator = new DirectoryIterator($csv_dir);
$files = [];
foreach($dir_iterator as $d) {
  if(!$d->isFile()) {
    continue;
  }
  $filename = explode('.',$d->getFilename())[0];
  $dateSort = explode('_',explode('-',$a)[0]);
  $files[] = array(
    'filename_full' => $d->getFilename(),
    'dateSort' =>  intval($dateSort[0].$$dateSort[1]),
    'filename' => $filename
  );
}

usort($files,function($a,$b){
    return $a['dateSort'] - $b['dateSort'];
});
$files = array_reverse($files);


foreach($files as $f) {
    $dates = explode('-',$f);
    $from = explode('_',$dates[0]);
    $to = explode('_',$dates[1]);
    $fromStr = date('F Y', strtotime('1.'.$from[1].'.'.$from[0]));
    $toStr = date('F Y', strtotime('1.'.$to[1].'.'.$to[0]));
    echo $fromStr.' - '.$toStr.'<br/><br/>';
}
?>

<div class="reading-section">

<?php if(empty($files));?>
<p>There are no archives. </p>

<?php endif;?>

<?php if(!empty($files));?>
<ul>

<?php
foreach($files as $f) {
  ?>
<li>
  <?php
  $dates = explode('-',$f['filename']);
  $from = explode('_',$dates[0]);
  $to = explode('_',$dates[1]);
  $fromStr = date('F Y', strtotime('1.'.$from[1].'.'.$from[0]));
  $toStr = date('F Y', strtotime('1.'.$to[1].'.'.$to[0]));
  echo '<a href="'.content_url().'/media_dumps/'.$f['filename_full'].'">'.$fromStr.' - '.$toStr.'</a>';


   ?>


</li>

  <?php
}


 ?>


</ul>

<?php endif;?>


</div>

<?php include_once 'footer.php';?>
