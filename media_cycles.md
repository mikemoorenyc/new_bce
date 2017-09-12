
## Adding to TXT file
- get working file (books.json)
- get new items (curl, api/)
- get all current GUIDs in a single array
- foreach new item
  - if item GUID is in_array(current_GUIDs){continue;}
  - else {do the work needed on item, append new item working array, `[inDB] === false`}
- array filter working array {only items with timestamp after 2 months}
- save file (books.json)

## Adding to DB / BOOKS & MOVIES
- Get working array (books.json)
- foreach item 
  - if(inDB){continue}
  - if tax term doesnt exist, create it
  - add item to db, if success (update in working array `[inDB] ==== true`)
- save working array (books.json)
## Adding to DB / Albums
- get working array
- get latest track or album entry
  - current = latest || null if no latest
    - DBid
    - play date
    - albumID
    - trackID
    - listenCount
- foreach item
  if(`inDB === true`){continue;}
  - if($itrackID == currentTrackID)
    - post meta listenCount = current listenCount+ 1;
    - currentListenCount++
    - mark as inDB in working array
    - continue
  - ($albumID == currentAlbumID && its on the same day) 
    - if "album" doesn't exist, create term
    - update current DBID with category 'album'
    - update current DBID title to album title
    - mark as inDB in working array
    - continue
  - we are good to reset
    - if "track" doesn't exist, create term
    - create new post
    - currentDBID (return on insertPOST)
    - currentListenCount = 1
    - currentTrackID = $itrackID
    - currentAlbumID = $albumID
    - play date
    - mark as inDB in working array
 - re-save working array

## Adding to DB / Shows
- get working array
- get latest show or episode entry
  - current = latest || null if no latest
    - DBid
    - play date
    - showID
    - bingeCount
- foreach item
  - if(`inDB === true`){continue;}
  - (showID == $ishowid && playdate is the same)
    - if "show" doesn't exist, create term
    - update current DBID with category 'show'
    - currentbingeCount++
    - update DBID bingecount meta to bingecount
    - mark as inDB in working array
    - continue
  - this is a new entry
  - if "track" doesn't exist, create term
    - if 'episode' doesn't exist, create term
    - create new post
    - currentDBID (return on insertPOST)
    - currentBingeCount = 1
    - currentShowID = $iShowID
    - play date
    - set DBID meta 'showID' to currentShowID
    - mark as inDB in working array

function checkShowImg($id) {
  $posts = get_media_posts whose meta value for 'showID' === $id;
  if(!$posts) {
    $posts = array();
  }
  foreach($posts as $p) {
    if(get_meta_value($p,'showImgURL')){
      return get_meta_value($p,'showImgURL');
      exit;
    }
  }
  global tmdbcurls;
  if(tmdbcurls < 25) { curl tmdb tmdbcurls++ if(there is show image){return image; }}
  global tvdbcurl;
  curl tvdb
  if(this is a tvdb image){return image;}
  return false;
}
    
## GETTING THE IMAGES
- get all 'episode', 'movie' & 'show'  where meta_query meta_key 'imgURL' doesn't exist **check if i can do this**
- curlcount = 0;
- foreach post

  - if 'movie'
    - if(tmdbcurls > 25){exit}
    - curl to get tmdb
    - tmdbcurl++
    - if success on img, set post id meta imgURL to url
    - continue;
  - if 'show'
    - $storedImg = checkShowImg($show meta showID);
    - if($storedImg){set post id meta showImgURL = $storedImg; continue;}
    - curl to get tmdb
    - curl++
    - 
