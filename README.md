# Create Multi Language System With Your Own Code #

There Are Many Ways To Built System With Multi Language you can use macamera backage as a default package to manage youe locales or this code
### What is this repository for? ###

* Quick summary
* Version 1.0
* This Configuration For Making Multi Lanuage System In Design Or Template 
* simple configuration to build your own multi language system

### How do I get set up? ###

* You Need To 
* 1- Make new File in config folder and name it locale.php 
* 2- Make new Middleware as LanguageMiddleware 
* 3- Make New Route Get to exchang the languages
* 4- you must save the route in route group in kernal.php file to run in every web request
* for each active language in locale config file make folder in lang folder in resources folder 


### How can i make the content multi language? ###
* There Are Many Ways To Built System With Multi Language you can use 
* 
* You Need To 
* 1- Make New File in config folder and name it with languages.php
* 2- in this file you can add more languages and to active language make the status key in array to true
* 3- Make Other Model For The Existed Model as Post for the common and shared colimns => PostTranslation for the translated columns
* the columns example post model has id and photo and created/updated _at  and translation => title/content/locale/post_id
