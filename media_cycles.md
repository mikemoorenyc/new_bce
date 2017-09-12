
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
    - update current DBID with category 'album'
    - update current DBID title to album title
    - mark as inDB in working array
    - continue
  - we are good to reset
    - create new post
    - currentDBID (return on insertPOST)
    - currentListenCount = 1
    - currentTrackID = $itrackID
    - currentAlbumID = $albumID
    - play date
    - mark as inDB in working array
 - re-save working array

