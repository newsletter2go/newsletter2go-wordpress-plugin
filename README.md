Newsletter2Go WordPress Plugin
==============================

Installation
------------

For installation instructions please see the official [help article](https://www.newsletter2go.com/help/integration-api/set-up-wordpress-plug-in/ "How do I set up the WordPress newsletter plug-in?").

Build and deploy
----------------

In order to build the plugin you can run the following command:

    make version=3.0.05
    make clean

This will create `newsletter2go_v3.0.05.zip` which you can then upload to your WordPress installation.

Currently the plugin is deployed by uploading the built package to our website.


Add language
------------
Translations are located in `\wp-content\plugins\newsletter2go\lang\` directory.

Use `.pot` file as template, it has all translation strings set.

Create `.po` file for new language. It must be named in format `[plugin-name]-[locale].po ` (eg newsletter2go-de_DE.po). Locale code is according to WordPress coding standard (https://wpcentral.io/internationalization/).

When translation is finished, file `.mo` should be compiled from `.po` source. File `.mo` is machine readable format that WP system uses. At production location, the `.mo` file is only necessary.

For generating `.mo` files, translating and creating `.po` files as well, Poedit software could be used (https://poedit.net/).