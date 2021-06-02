<?php
$mediaType = 'books';
include_once('media_cron_header.php');



createTerm('Book');

//SET UP CURL 
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$now = new DateTime("now");
$gr_key = $keys['goodreads'];


function getData($url) {
  global $ch; 
  curl_setopt($ch, CURLOPT_URL, $url);
  $xml = curl_exec($ch);
  if($xml === false ) {
      return false; 
  }
  return new SimpleXMLElement($xml); 
}
function readExists($status_id) {
  $posts = get_posts(array(
      'posts_per_page'   => -1,
      'post_type' => 'consumed',
      'meta_query' => array(
          'key' => "ReadStatus",
          "value" => $status_id
      )
  ));
  return count($posts) > 0;
}
function imageReplacer($o_url,$isbn,$desc=null, $type = 'ISBN') {

  $a_url = 'http://images.amazon.com/images/P/'.$isbn.'.01.LZZZZ.jpg';
  $size = @getimagesize($a_url);

  if($size && $size[0] > 50) {
    return $a_url;
  }
  if(!$desc) {
    return $o_url;
  }

  $doc = new DOMDocument();
  @$doc->loadHTML($desc);
  if(!$doc) {
    return $o_url;
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
    return $o_url;
   }
   break;
  }
  return $o_url;
}
$doc = new DOMDocument();
function descImg($desc) {
  global $doc;
  @$doc->loadHTML('<html><body>'.$desc.'</body></html');
  if(!$doc) {
    return null;
  }
  $imgs = $doc->getElementsByTagName('img');
  if(!$imgs) {
    return null;
  }
  //https://images.gr-assets.com/books/1345156830m/379894.jpg
  $url = parse_url($imgs[0]->getAttribute('src'));
  if($url['scheme'] !== 'https' || $url['host'] !== 'images.gr-assets.com') {
    return null;
  }
  $p_x = explode('/', $url['path']);
  array_shift($p_x);

  $p_x[1] = str_replace('s','m', $p_x[1]);

  $new_url = 'https://images.gr-assets.com/'.implode('/',$p_x);

  $size = @getimagesize($new_url);
  if(!$size || $size[0] < 20) {
    return null;
  }
  return $new_url ;
}
function getImgData($pass_url, $description) {
  $img_url = $pass_url;
  if(strpos($img_url, 'nophoto') !== false) {
    $img_url = descImg($description);
  }

  if(strpos($img_url, 'nophoto') !== false) {
    $img_url = null;
  }
  $dimensions = null;
  $final_img_url = httpcheck($img_url);
  if($final_img_url) {
    $size = getimagesize($final_img_url);
    $dimensions = array(
      "width" => $size[0],
      "height" => $size[1]
    );
  }
  return array(
    "url" => $final_img_url,
    "dimensions" => $dimensions
  );
}



function checkReview($i) {
    global $now;
    global $ch; 
    global $gr_key;
    $review_ID = str_replace(["Review","ReadStatus","UserStatus"],"",$i->guid);
     
    $new_status = false; 
    
    $review_url = "https://www.goodreads.com/review/show.xml?id=$reviewID&key=$gr_key";
    $xml_data = getData($review_url);
    if(!$xml_data) {
        return false; 
    }
    $statuses = $xml_data->review->read_statuses; 
    foreach ($statuses->read_status as $r) {
        //Make sure it's a read status
        if($r->status !== "read") {
            continue; 
        }
        //Check if read status already exists
        if(readExists($r->id)) {
            continue; 
        }
        //It's a read status that doesn't already exist and is new
        insertStatus(array(
          "status" => "read",
          "status_id" => $r->id,
          "book_xml" => $xmlData->review->book,
          "timestamp" => $r->updated_at,
          "description" => $i->description
        ));
        break; 
    }

}
function checkStatus($api_url, $xml, $status_type) {
  global $now;
  global $ch; 
  global $gr_key;
  $xml_data = getData($api_url);
    if(!$xmlData) {
      return false; 
  }
  $read_status = ($status_type == "UserStatus") ? $xml_data->user_status : $xml_data->read_status;
  if(!in_array($read_status->status, ["read","currently-reading"])) {
    return false ; 
  }  
  insertStatus(array(
    "status" => $read_status->status,
    "status_id" => $read_status->id,
    "book_xml" => $read_status->book,
    "timestamp" => $read_status->updated_at,
    "description" => $xml->description
  ));
}
function insertStatus($status_data) {
  $book_xml = $status_data["book_xml"];
  $book_image_data = getImgData($book_xml->img_url."", $status_data["description"]);
  $authors = [];
  foreach($book_xml->authors->author as $a) {
    $authors[] = htmlentities($a->name.'', ENT_QUOTES);
  }
  $data = array(
    'title' => htmlentities($book_xml->title.'', ENT_QUOTES),
    'img' => $book_image_data["url"],
    'timestamp' => strtotime($status_data["timestamp"]),
    'status' =>$status_data["status"] ,
    'type' => 'book',
    'authors' => $authors,
    'clickthru' => $book_xml->link.'',
    "dimensions" => $book_image_data["dimensions"],
    'GUID' => $status_data["status_id"]
  );
  $dates = dateMaker($data['timestamp']);
  $insert = wp_insert_post( array(
    'post_title' => $data['title'],
    'post_type' => 'consumed',
    'post_status'=> 'publish',
    'post_content' => json_encode($data,JSON_UNESCAPED_UNICODE),
    'post_date' => $dates['est'],
    'post_date_gmt'=> $dates['gmt']
  ) );
  if($insert) {
    wp_set_object_terms( $insert, 'book', 'consumed_types' );
    update_post_meta( $insert, 'ReadStatus', $data["GUID"]);
  }
}

$status = new SimpleXMLElement(file_get_contents('https://www.goodreads.com/user/updates_rss/'.$keys['goodreads_uid'].'?key='.$keys['goodreads']),LIBXML_NOCDATA);

$items = [];

foreach($status->channel->item as $i) {
 $items[] = $i;
}

foreach($items as $i):
    $stamp = new DateTime($i->pubDate);
    $interval = $stamp->diff($now);
    //Check if it's an old Status
    if(intval($interval->format("%R%d")) > 2) {
        continue; 
    }
    if(strpos($i->guid,'Review')!== false ) {
        checkReview($i);
    }
    $updateID =  str_replace(["ReadStatus","UserStatus"],"",$i->guid);
    if(readExists($updateID)) {
      continue; 
    }
    $status_type = (strpos($i->guid,'UserStatus')!== false) ? "UserStatus" : "ReadStatus";
    $api_type = ($status_type == "UserStatus") ? "https://www.goodreads.com/user_status/show/" : "https://www.goodreads.com/read_statuses/"; 
    $api_url = $api_type.$updateID.'?format=xml&key='.$keys['goodreads'];
    
    checkStatus($api_url, $i,$status_type);

endforeach;

/*$oldest_play = $items[count($items) -1]->pubDate.'';

$compare_posts = comparePosts(['book'], strtotime($oldest_play));*/

die();

//////////////////////////////// END OF RUNNING CODE //////////////////////////////////////////////

foreach($items as $i):
  if(strpos($i->guid,'UserStatus') === false && strpos($i->guid,'ReadStatus') === false) {
    continue;
  }
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
    $authors[] = htmlentities($a->name.'', ENT_QUOTES);
  }

  $imgURL = $readStatus->book->image_url.'';
  if(strpos($imgURL, 'nophoto') !== false) {
    $imgURL = descImg($i->description);
    /*
    if(!empty($readStatus->book->isbn.'')) {
      $imgURL = imageReplacer($imgURL, $readStatus->book->isbn.'',$i->description);
    } else {
      if(!empty($readStatus->book->isbn13.'')) {
      $imgURL = imageReplacer($imgURL, $readStatus->book->isbn13.'',$i->description);
      } else {
        $imgURL = imageReplacer($imgURL, '',$i->description);
      }
    }
    */
  }

  if(strpos($imgURL, 'nophoto') !== false) {
    $imgURL = null;
  }
  $dimensions = null;
  $finalImgURL = httpcheck($imgURL);
  if($finalImgURL) {
    $size = getimagesize($finalImgURL);
    $dimensions = array(
      "width" => $size[0],
      "height" => $size[1]
    );
  }

  if(htmlentities($readStatus->status.'', ENT_QUOTES) === "to-read") {
    continue; 
  }

  $data = array(
    'percent' => htmlentities($readStatus->percent.'', ENT_QUOTES),
    'title' => htmlentities($readStatus->book->title.'', ENT_QUOTES),
    'img' => $finalImgURL,
    'timestamp' => strtotime($readStatus->updated_at.''),
    'status' =>htmlentities($readStatus->status.'', ENT_QUOTES) ,
    'type' => 'book',
    'authors' => $authors,
    'clickthru' => $readStatus->book->link.'',
    "dimensions" => $dimensions,
    'GUID' => [$i->guid.'']
  );



  $dates = dateMaker($data['timestamp']);

  $insert = wp_insert_post( array(
    'post_title' => $data['title'],
    'post_type' => 'consumed',
    'post_status'=> 'publish',
    'post_content' => json_encode($data,JSON_UNESCAPED_UNICODE),
    'post_date' => $dates['est'],
    'post_date_gmt'=> $dates['gmt']
  ) );
  if($insert) {
    wp_set_object_terms( $insert, 'book', 'consumed_types' );
  }
//END ITEM LOOP
endforeach;


?>
