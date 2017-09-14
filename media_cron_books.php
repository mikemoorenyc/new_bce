<?php
$mediaType = 'books';
include_once('media_cron_header.php');

function imageReplacer($o_URL,$isbn,$desc=null, $type = 'ISBN') {

  $a_URL = 'http://images.amazon.com/images/P/'.$isbn.'.01.LZZZZ.jpg';
  $size = @getimagesize($a_URL);

  if(!$size || $size[0] < 50) {


    if(!$desc){return $o_URL;}
    $doc = new DOMDocument();
    @$doc->loadHTML($desc);
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
  }
  return $a_URL;

}
$status = new SimpleXMLElement(file_get_contents('https://www.goodreads.com/user/updates_rss/'.$keys['goodreads_uid'].'?key=18ioDaauDhEjysrttqWKDR03F_rvL_JFKT4MUW5jz8sl5px7'),LIBXML_NOCDATA);



$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

foreach($status->channel->item as $i) {

  if(in_array($i->guid,$GUIDs) || strpos($i->guid,'Review')!== false ) {
   continue;
  }
  $updateID =  str_replace("ReadStatus","",$i->guid);
  $updateID = str_replace("UserStatus","",$updateID);
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

  $update = array(
    'percent' => $readStatus->percent.'',
    'title' => $readStatus->book->title.'',
    'img' => $imgURL,
    'timestamp' => strtotime($readStatus->updated_at.''),
    'status' => $readStatus->status.'',
    'type' => 'book',
    'authors' => $authors,
    'inDB' => false,
    'GUID' => $i->guid.''
  );
  $workingArray[] = $update;
}

include 'media_cron_footer.php';

?>
