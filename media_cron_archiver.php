<?php
include_once('media_cron_header.php');

$csv_dir = str_replace('/uploads','',wp_upload_dir()['basedir']).'/media_dumps' ;

if (!file_exists($csv_dir) && !is_dir($csv_dir)) {
    mkdir($csv_dir);
}
/*
$afterDate = array(
  'year' => intval(date('Y')),
  'month'=> 1,
  'day' => 1,
  'hour' => 0,
  'minute' => 1
);
$beforeDate = $afterDate;
*/
$six_months_ago = strtotime('-6 months');
/*
$beforeDate['month'] = 6;
$beforeDate['minute'] = 0;
$fileName = $afterDate['year'].'_'.(1).'-'.$beforeDate['year'].'_'.(5);
if(intval(date('n')) === 1){
  $afterDate['year']--;
  $afterDate['month'] = 6;
  $beforeDate['month'] = 1;
  $fileName = $afterDate['year'].'_'.(6).'-'.($beforeDate['year']-1).'_'.(12);
}
*/

$posts = get_posts(array(
  'posts_per_page'   => -1,
  'post_type' => 'consumed',
  'date_query' => array(
		array(
      'before'    => date('Y-m-d',strtotime('-6 months')),
			/*'after'     => $afterDate,*/
			'inclusive' => true,
		),
	),
));

if(empty($posts)) {
  die();
}
/*
$filePath = $csv_dir.'/'.$fileName.'.csv';
$csv = fopen($filePath, 'w');
if(!$csv) {
  die();
}
$headers = fputcsv($csv, array(
  'Title',
  'Date',
  'Type',
  'GUIDs',
  'Permalink'
));
*/
foreach ($posts as $p) {
  $fileDate = date('m',strtotime($p->post_date)).'_'.date('Y',strtotime($p->post_date));

  $filePath = $csv_dir.'/'.$fileDate.'.json';
  if(file_exists($filePath)) {
    $stream = json_decode(file_get_contents($filePath,TRUE));
  } else {
    $stream = [];
  }

  $data = json_decode($p->post_content,true);
  $fields = [];
  $fields['title'] = $p->post_title;
  $fields['date'] = strtotime($p->post_date);
  $type = get_the_terms($p->ID, 'consumed_types');
  if($type){$type = $type[0]->slug;}
  $fields['type'] = $type;
  $fields['GUID'] = json_encode($data['GUID']);
  $fields['permalink'] = $data['clickthru'];
  $stream[] = $fields;
  usort($stream,function($a,$b){
      return $a['date'] - $b['date'];
  });
  $stream = array_reverse($stream);
  $put = file_put_contents($filePath,json_encode($stream));
  if($put) {
   $delete = wp_trash_post( $p->ID, false );
  }

}

//fclose($csv);



die();
?>
