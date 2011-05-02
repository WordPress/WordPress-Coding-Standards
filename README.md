### Wordpress Coding Standards for Codesniffer 1.3.0

This is an version of the Coding Standards available at [Urban Giraffe][], which were missing a `ruleset.xml` file, that stopped them being detected when I downloaded them and tried passing some Wordpress core code through them. 

I know very little about Codesniffer beyond what I picked up in the last hour or two of reading the docs but I'm aiming to find a happy medium between letting developers stay productive, but stopping really shocking code being committed on projects, and me stumbling through this CodeSniffer tutorial here on [pear.php.net][]

### How to use this 

Once you've installed PEAR, install Codesniffer:

    pear install --alldeps PHP_CodeSniffer

Then install Wordpress standards

git clone git@github.com:mrchrisadams/Wordpress-Coding-Standards.git $(pear config-get php_dir)/PHP/CodeSniffer/Standards/Wordpress    

Normally when working with PEAR, we'd use the pear install command, but github automatically names the files, in a way that think will confuse the pear install command, so we're falling back to git instead.

Then run the PHP code sniffer commandline tool on a given file, for example `wp-cron.php`. 

    phpcs --standard=Wordpress -s wp-cron.php

You can use this to sniff individual files, or use different flags to recursively scan all the directories in a project. This command will show you each file it's scanning, and how many errors it's finding:

    phpcs -p -s -v --standard=Wordpress .  

Output will like this:

    Registering sniffs in Wordpress standard... DONE (11 sniffs registered)
    Creating file list... DONE (705 files in queue)
    Processing index.php [47 tokens in 31 lines]... DONE in < 1 second (2 errors, 0 warnings)
    Processing wp-activate.php [750 tokens in 102 lines]... DONE in < 1 second (47 errors, 2 warnings)
    Processing admin-ajax.php [14523 tokens in 1475 lines]... DONE in 2 seconds (449 errors, 44 warnings)
    Processing admin-footer.php [183 tokens in 43 lines]... DONE in < 1 second (19 errors, 0 warnings)
    Processing admin-functions.php [43 tokens in 16 lines]... DONE in < 1 second (2 errors, 0 warnings)
    Processing admin-header.php [1619 tokens in 196 lines]... DONE in < 1 second (110 errors, 1 warnings)
    Processing admin-post.php [144 tokens in 33 lines]... DONE in < 1 second (8 errors, 0 warnings)
    Processing admin.php [1906 tokens in 238 lines]... DONE in 1 second (128 errors, 1 warnings)
    Processing async-upload.php [623 tokens in 70 lines]... DONE in < 1 second (41 errors, 0 warnings)
    Processing comment.php [2241 tokens in 289 lines]... DONE in < 1 second (110 errors, 3 warnings)
    Processing colors-classic-rtl.css [517 tokens in 1 lines]... DONE in < 1 second (0 errors, 0 warnings)
    Processing colors-classic-rtl.dev.css [661 tokens in 79 lines]... DONE in < 1 second (0 errors, 0 warnings)
    Processing colors-classic.css ^C

    ... and so on...

### Caveats

Right now, this standard is so pedantic it's almost useless, but over the coming weeks, I'm hoping to work out how to make it more reasonable, so can serve a useful purpose on future coding projects.


### Troubleshooting


Check your PATH if it includes new binaries added into the pear directories. I had to add `:/usr/local/php/bin` before I could call `phpcs` on the command line.

Remember that you can see where pear is looking for stuff, and putting things, by calling `pear config-show`. This is how I found out where the Codesniffer binary was added, and where the pear library is by default.


[pear.php.net]: http://pear.php.net/manual/en/package.php.php-codesniffer.coding-standard-tutorial.php
[Urban Giraffe]: http://urbangiraffe.com/articles/wordpress-codesniffer-standard/
