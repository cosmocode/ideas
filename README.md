Ideas - a simple idea voting platform
=====================================

Requirements
------------

1. Apache with PHP 5.3+ support
  - other webservers might work, but need special attention to rewriting
  - PHP needs to have the LDAP, Multibyte String and MySQL extensions available and enabled
2. MySQL 4.1+

Setup
-----

Create an empty database for the application:

```
CREATE database ideas;
```

Install the files to your Webserver webroot or a subdirectory of your choice and make sure your webserver honors the rewriting configuration in .htaccess files.

Make the `FIXME` directory writable by the webserver.

Go to the `application/config` directory and copy the `*.dist` files to the same name without the `.dist`:

In `application/config/databases.php` configure the access to the database you created above.

In `application/config/adLDAP.php` configure access to your Active Directory server that will be used to manage the logins.

In `application/config/privileges.php` configure the users and/or groups that should have moderator rights.

Open the application in your webborwer and you're ready to go.