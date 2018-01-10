<?php
include_once('media_cron_header.php');
$csv_dir =  get_home_path().'wp-content/media_dumps';
if (!file_exists($csv_dir) && !is_dir($csv_dir)) {
    mkdir($csv_dir);         
}
$afterDate = array(
  'year' => intval(date('Y')),
  'month'=> 1,
  'day' => 1,
  'hour' => 0,
  'minute' => 1
);
$beforeDate = array_flip(array_flip($afterDate));
$beforeDate['month'] = 6;
$beforeDate['minute'] = 0;
$fileName = $afterDate['year'].'_'.1.'-'.$beforeDate['year'].'_'.5;
if(intval(date('n')) === 1){
  $afterDate['year']--;
  $afterDate['month'] = 6;
  $beforeDate['month'] = 1; 
  $fileName = $afterDate['year'].'_'.6.'-'.($beforeDate['year']-1).'_'.12;
}
$posts = get_posts(array(
  'posts_per_page'   => -1,
  'post_type' => 'consumed',
  'date_query' => array(
		array(
      'before'    => $beforeDate,
			'after'     => $afterDate,
			'inclusive' => true,
		),
	),
));

if(empty($posts)) {
  die();
}
$filePath = $csv_dir.'/'.$fileName.'.csv';
$csv = fopen($filePath, 'w');
$headers = array(
  'Title',
  'Date',
  'Type',
  'GUIDs',
  'Permalink'
);
foreach ($posts as $p) {
  $data = json_decode($p->post_content,true);
  $fields[] = $p->post_title;
  $fields[] = strtotime($p->post_date);
  $type = get_the_terms($p->ID, 'consumed_types');
  if($type){$type = $type[0]->slug;}
  $fields[] = $type;
  $fields[] = json_encode($data['GUID']);
  $fields[] = $data['clickthru'];
  fputcsv($csv, $fields);
}

fclose($csv);



die();
?>
