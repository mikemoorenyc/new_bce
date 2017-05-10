The wordpress theme to power the future becreativeeveryday.com

# Steps
1. Template Code  
    1. ~~Homepage~~
    2. ~~Project Landing~~
    3. ~~Blog Landing~~
    4. About Page
    5. Contact Page
    6. ~~Project Single~~
    7. ~~Post Single~~
    8. Tagged as page
2. Write CSS
2. Write Desktop CSS
3. Refactor
4. Javascript
    1. Colormode
    2. History API
    3. Ajax on form
    4. Content Stream
5. Refactor

## Contact form business logic
1. If a user has never submitted a message before: 
    * they are presented with the form
2. If the user submits the form and there is an error: 
    * they are presented with the form, with error messaging and asked try again
3. If the user submits the form and there are no errors:
    * The submission is saved
    * A notification email is sent
    * A cookie is set saying that they have submitted
    * they are presented with a thank you message and no form
4. If the user has a cookie set saying they have already sent a message
    * they are presented with a form asking to prove they are human
5. If the user submits the form proving they are human and there is an error
    * they are presented with a form asking to prove they are human and a message asking to try again
6. If the user submits the form proving they are human and there is no error
    * they are presented with the form
