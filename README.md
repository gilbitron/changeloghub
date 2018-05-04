### This repo is no longer maintained. If you would like to take over ownership please [get in touch](mailto:&#103;&#105;&#108;&#098;&#101;&#114;&#116;&#064;&#112;&#101;&#108;&#108;&#101;&#103;&#114;&#111;&#109;&#046;&#109;&#101;).

Changeloghub
============

Generate HTML changelogs from your GitHub commits.

Requirements
------------

Your server needs to be running PHP (v5.x) and probably Apache or Nginx.
Any LAMP stack will do.

Install
-------

1. Download the latest release of the Changeloghub github repo
2. Run `composer install` in the root directory (this can be done on the server if your prefer)
3. Rename `sample_config.php` to `config.php` and fill in the details
4. Transfer the files (normally FTP) to your server/hosting provider
5. Make sure the `cache` dir is writeable

Thats it. When you visit your site you should now see your automatically generated changelog page.
Feel free to customize the HTML structure in `index.php` or the styles via `assets/css/style.css`.

Demo
----

See http://dev7studios.com/changelog/example

Credits
-------

Changeloghub was created by [Gilbert Pellegrom](http://gilbert.pellegrom.me) from
[Dev7studios](http://dev7studios.com). Released under the MIT license.
