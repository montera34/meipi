# Introduction #

The following instructions will guide you through the installation of a _meipi_ in your own server.

**Note:** If you just want to create a meipi online in meipi.org, go to http://meipi.org/meipimatic.php

**Note:** The current instructions are intended for a Linux installation.


# Previous requirements #

  * Install a web server.
  * Install php and php-mysql packages (php5-mysql, php-apache2, libapache-mod-php5).
  * Install imagemagick and php-gd (imagemagick, php5-gd). Your hosting web server might have them already available.
  * [Create a database](http://code.google.com/p/meipi/wiki/CreateDatabase). Keep the configuration details handy: you need them soon.

# Installation #

  * [Download](http://code.google.com/p/meipi/downloads/list) the latest available version of the **Meipi** code.
  * ?Upload the files of the downloaded folder /meipi to your server?
  * Execute rc/install.sql in the database in order to create the necessary tables. Note: At the end of the file you can find the categories to create. You can update category names and descriptions.
  * Copy configuration files from meipi/config/ and remove ".default" from their name:
    * dbConfig.php.default to dbConfig.php
    * meipiConfig.php.default to meipiConfig.php
  * Update file meipi/config/dbConfig.php with your database details:
    * $usr = Database user
    * $pass = Database password
    * $server = Database server
    * $db = Database name
  * Update meipi/config/meipiConfig.php. Check the comments for more configuration options, but make sure you update the following mandatory parameters at least:
    * mainUrl = Change it to your own URL.
    * baseFolder = Path of the files in the web servers filesystem. Must end in '/' (to obtain this path you can upload a file .php with the content <? phpinfo(); ?> in the folder where the meipi is installed).
    * commonFiles = change it to the folder where you have uploaded the content of /meipi¿
    * google\_maps\_keys = Google Maps API keys. It is not needed for testing meipi in localhost but it is required to [get one Google Maps API key](http://code.google.com/apis/maps/signup.html) for each domain used.
    * reCaptchaPublicKey and reCaptchaPrivateKey = ReCAPTCHA pair of keys. There is already a pair selected, but it is strongly recommended to register your domain for a new pair of keys in [recaptcha.net](http://recaptcha.net/), as otherwise the service will probably fail soon.
  * Give read and write permissions to the user running the web server to all folders under meipi/images/ and all folders under meipi/profile/images/

# Post installation #

Some basic checks to ensure that your _meipi_ is running properly:

  * Check the meipi/meipi.php file using a web server. If images and css files are not loading, check $commonFiles variable
  * Register a user and log in
  * Create a new entry with an image
    * If entry is saved and entry images are displayed -> Ok! :-)
    * If entry is saved but images are not displayed
      * Check write permissions for web server user in images files
      * Check imagemagick installation (when you install it in a web server as Godaddy you might have to change "convert" to "/usr/local/bin/convert" in the file "functions/meipi.php").
  * Go to map page. If label is not displayed -> Check gd installation
  * Check if the icons of map, mosaic, list and chanel are displayed. If not, correct the url in style/meipi.css
  * Add admin and editor users to meipi\_permission table. Check your id\_user in "My profile" page or in database. Values for type in meipi\_permission table are 1 for Admin and 2 for Editor