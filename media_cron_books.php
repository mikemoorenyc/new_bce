<?php
$mediaType = 'books';
include_once('media_cron_header.php');

createTerm('Book');

function imageReplacer($o_URL,$isbn,$desc=null, $type = 'ISBN') {

  $a_URL = 'http://images.amazon.com/images/P/'.$isbn.'.01.LZZZZ.jpg';
  $size = @getimagesize($a_URL);

  if($size && $size[0] > 50) {
    return $a_URL;
  }
  if(!$desc) {
    return $o_URL;
  }

  $doc = new DOMDocument();
  @$doc->loadHTML($desc);
  if(!$doc) {
    return $o_URL;
  }
  $img = $doc->getElementsByTagName('img');

  foreach($img as $i) {
   $url = $i->getAttribute('src');
   $url = str_replace('books/','REPLACELATER',$url);
   $url = str_replace('s/','m/',$url);
   $url = str_replace('REPLACELATER','books/',$url);

   $size = @getimagesize($url);
   if($size && $size[0] > 50) {
    return $url;
   } else {
    return $o_URL;
   }
   break;
  }
  return $o_URL;
}

$status = new SimpleXMLElement(file_get_contents('https://www.goodreads.com/user/updates_rss/'.$keys['goodreads_uid'].'?key=18ioDaauDhEjysrttqWKDR03F_rvL_JFKT4MUW5jz8sl5px7'),LIBXML_NOCDATA);

$items = [];

foreach($status->channel->item as $i) {
 $items[] = $i;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$oldest_play = $items[count($items) -1]->pubDate.'';

$compare_posts = comparePosts(['book'], strtotime($oldest_play));

foreach($items as $i) {

  if(in_array($i->guid,$compare_posts['GUID']) || strpos($i->guid,'Review')!== false ) {
   continue;
  }
  $updateID =  str_replace(["ReadStatus","UserStatus"],"",$i->guid);

  if(strpos($i->guid,'UserStatus')!== false) {
    $apiURL = 'https://www.goodreads.com/user_status/show/'.$updateID.'?format=xml&key='.$keys['goodreads'];
  } else {
    $apiURL = 'https://www.goodreads.com/read_statuses/'.$updateID.'?format=xml&key='.$keys['goodreads'];
  }
  curl_setopt($ch, CURLOPT_URL, $apiURL);
  $xml = curl_exec($ch);
  if($xml === false) {
    continue;
  }
  $readStatus = new SimpleXMLElement($xml);
  if(strpos($i->guid,'UserStatus')!== false) {
    $readStatus = $readStatus->user_status;
  } else {
    $readStatus = $readStatus->read_status;
  }
  $authors = [];
  foreach($readStatus->book->authors->author as $a) {
    $authors[] = $a->name.'';
  }

  $imgURL = $readStatus->book->image_url.'';
  if(strpos($imgURL, 'nophoto') !== false) {

    if(!empty($readStatus->book->isbn.'')) {
      $imgURL = imageReplacer($imgURL, $readStatus->book->isbn.'',$i->description);
    } else {
      if(!empty($readStatus->book->isbn13.'')) {
      $imgURL = imageReplacer($imgURL, $readStatus->book->isbn13.'',$i->description);
      } else {
        $imgURL = imageReplacer($imgURL, '',$i->description);
      }
    }
  }

  $data = array(
    'percent' => $readStatus->percent.'',
    'title' => $readStatus->book->title.'',
    'img' => $imgURL,
    'timestamp' => strtotime($readStatus->updated_at.''),
    'status' => $readStatus->status.'',
    'type' => 'book',
    'authors' => $authors,
    'clickthru' => $readStatus->book->link.'',
    'GUID' => [$i->guid.'']
  );



  $dates = dateMaker($data['timestamp']);

  $insert = wp_insert_post( array(
    'post_title' => $data['title'],
    'post_type' => 'consumed',
    'post_status'=> 'publish',
    'post_content' => json_encode($data),
    'post_date' => $dates['est'],
    'post_date_gmt'=> $dates['gmt']
  ) );
  if($insert) {
    wp_set_object_terms( $insert, 'book', 'consumed_types' );
  }
}


?>
