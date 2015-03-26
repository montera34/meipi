# Introduction #
Meipi uses a [MySql](http://www.mysql.com) database to store the information. One of the requirements to install and configure a meipi is to have a running database where the data can be stored.

Many web hosting companies offer MySql databases together with the web hosting. You will need to check with your hosting company how to create a new database but it should be one of the options in the control panel.

If you plan to host your own meipi you will need to create and configure your own database. Check "Installing MySql server" section in this page.

You will need to know the following parameters to configure meipi database. Look for them when you create the database in your hosting server.

  * Server: Address of the database server. Where the database is hosted.
  * User: Database user. Together with password, it gives access to the database.
  * Password: Database password . Together with user, it gives access to the database.
  * Database name: Name of the database. A database server can host many databases, this identifies what database to use.

# Installing MySql server #

To host a meipi in your own servers you will need to install and configure the database server. You can follow these steps to configure your database server with a web application.

  * Install mysql-server and phpmyadmin and take note of the superuser password for the database server. You will need it to create meipi database user.
  * Log in to phpmyadmin with your database superuser account. You can find phpmyadmin in http://localhost/phpmyadmin/ if you installed it locally or `http://<server_address>/phpmyadmin` from a different computer.
  * Go back to home and create a new database
  * Go to "Privileges" and create a new user with access to the database created
  * You can test if everything was created ok by trying to log into phpmyadmin with the new user and select the new database. You can execute the install.sql queries from phpmyadmin "Sql" tab once you select the database.