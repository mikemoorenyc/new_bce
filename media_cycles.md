
## Adding to TXT file
- get working file (books.json)
- get new items (curl, api/)
- get all current GUIDs in a single array
- foreach new item
-- if item GUID is in_array(current_GUIDs){continue;}
-- else {do the work needed on item, append new item working array, `[inDB] === false`}
- array filter working array {only items with timestamp after 2 months}
- save file (books.json)

## Adding to DB / BOOKS & MOVIES
- Get working array (books.json)
- foreach item 
-- if(inDB){continue}
-- add item to db, if success (update in working array `[inDB] ==== true`)
-- save working array (books.json)
## Adding to DB / Albums
-- get working array (tracks.json)
-- get la
**NOTE DONE**
