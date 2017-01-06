AuthJoomla3
===========
#### DokuWiki plugin to authenticate against a Joomla 3 installation

To make it work:
  - Download the source from github
  - Create a folder inside dokuwiki/lib/plugins called authjoomla3
  - Paste the downloaded source inside authjoomla3
  - Navigate to your doku wiki config page (doku.php?id=start&do=admin&page=config)
  - The first thing to do is to go to the Authentication page and change `Authentication backend` with authjoomla3. Do not save yet.
  - Scroll down to the last row and you should see `plugin authjoomla3 joomlaPath` there you have to specify a valid joomla3 installation path
  - Save and everything should work.

Now a few notes:

  - Since we're using joomla users and group, I left the management to joomla itself, so you cannot add/delete/update users or groups from dokuwiki but instead you want to do it from joomla.
  - Joomla's `Super Users` and `Administrator` group are locally renamed (means it happens only on dokuwiki) to `admin` in order to make them administrator on DokuWiki without any other change.
