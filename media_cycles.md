

## Books
1. Get lastRun .txt, parse, get lastRun on Books
2. Get booksJson, parse
3. Make newEntries array (only add from bookjson if inDB === false)
    - add booksJson key and array value on newEntries
4. if newEntres array is empty, die()
5. insert new book for for all new entries
6. on success, `bookJSON[$newEntry['bookJSONkey']]['inDB'] = true`
7. save bookJSON, die()
