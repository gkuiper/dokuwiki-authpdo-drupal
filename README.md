authpdodrupal
===========
#### DokuWiki plugin to authenticate against a Drupal installation

To make it work:
  - Download the source from github
  - Create a folder inside dokuwiki/lib/plugins called authpdodrupal
  - Paste the downloaded source inside authpdodrupal
  - Navigate to your doku wiki config page (doku.php?id=start&do=admin&page=config)
  - The first thing to do is to go to the Authentication page and change `Authentication backend` with authpdodrupal. Do not save yet.
  - Scroll down to the last row and you should see `Absolute path to a Drupal installation` there you have to specify a valid Drupal installation path
  - Save and everything should work.
