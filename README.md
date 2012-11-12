Ideas - a simple idea voting platform
=====================================

with Active Directory connection

Requirements
------------

1. Apache with PHP 5.3+ support
  - other webservers might work, but need special attention to rewriting
  - PHP needs to have the LDAP, Multibyte String and MySQL extensions available and enabled
2. MySQL 4.1+
3. An Active Directory Server for user authentication

Setup
-----

Create an empty database for the application:

```
CREATE database ideas;
```

Install the files to your Webserver webroot or a subdirectory of your choice and make sure your webserver honors the
rewriting configuration in .htaccess files.

Make the `application/cache` and `application/log` directories writable by the webserver.

Go to the `application/config` directory and copy the `*.dist` files to the same name without the `.dist`:

In `application/config/databases.php` configure the access to the database you created above.

In `application/config/adldap.php` configure access to your Active Directory server that will be used to manage the
logins.

In `application/config/privileges.php` configure the users and/or groups that should have moderator rights.

Open the application in your webbrowser and you're ready to go.

Tuning Search Results
---------------------

This application relies on MySQL's fulltext search mechanism.

Please note that the search will ignore words that are found in more than 50% of all items in the index.

By default it requires a minimum word length of four characters. If you'd like to use shorter words, you need to
reconfigure your MySQL Server:

In your `my.cnf` file add the following in the `[mysqld]` section and restart MySQL.

```
ft_min_word_len = 3
```

To reindex existing ideas, use the following command in a MySQL console:

```
REPAIR TABLE ideas QUICK;
```


Acknowledgements
-------

Made by [CosmoCode](http://www.cosmocode.de/).

Made with [CodeIgniter](http://www.codeigniter.com/), [adLDAP](http://adldap.sourceforge.net/),
[Twig](http://twig.sensiolabs.org/), [Bootstrap](http://twitter.github.com/bootstrap/),
[lessphp](http://leafo.net/lessphp/) and [JQuery](http://jquery.com/).