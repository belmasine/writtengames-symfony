Written Games website, using Symfony 2
======================================

Symfony-based website for Written Games

Prerequisites
-------------

Apache, PHP 5.3.3, MySQL 5.5

As Symfony uses Composer to manage its dependencies (the PHP equivalent to
Bundler), download it following the instructions on http://getcomposer.org/
or just run the following command:

    curl -s http://getcomposer.org/installer | php

Seeing as Composer can be used to manage dependencies for all your Symfony
projects regardless of their version, it's recommended to put the composer.phar
file into a global bin dir, e.g. /usr/bin - but you can also keep it in the
project directory (it's included in the .gitignore file for that reason).

Symfony does not do the Bring Your Own Server thing, so you'll have to create a
virtual host for it. Point the DocumentRoot to the `web` subfolder of the project
directory.

If you're using lighttpd instead of Apache, add the following to your vhost
definition:

```
url.rewrite-if-not-file = (
  "(.+)" => "/app.php$1"
)
```

If you are using Apache, you don't need to worry about that as there is a `.htaccess`
file in the `web` folder that contains the equivalent directive for Apache.

Get the code
------------

Use Git to clone this repository:

    git clone git@github.com:userfriendly/writtengames-symfony.git

(Adapt the repository URL if you forked it, which I would recommend)

Configuration
-------------

Copy `app/config/parameters.yml-dist` to `app/config/parameters.yml` and fill in
the app id and app secret values for Facebook and the other social networks.
If you only want to use Facebook for the time being, replace the squiggly lines
for Google and Yahoo with any string, e.g. foo and baa.

Installation of dependencies
----------------------------

Run Composer to pull the vendor bundles (Symfony's equivalent to Gems):

    composer.phar install

Permissions setup
-----------------

Since your user account will likely not be the account which your web server is using
you will have to set up the permissions for the `app/cache` and `app/logs` directories
so that both users can write there. One way to do that you can find here:

http://symfony.com/doc/current/book/installation.html#configuration-and-setup

Follow one of the two suggested ways (`chmod +a` or `setfacl`, depending on your Linux
distribution). You might want to run the following command in the shell first, though:

    sudo rm -rf app/cache/* && sudo rm -rf app/logs/*

Database
--------

Create an empty database and add the connection data for it to the settings in
`app/config/parameters.yml`. Then run the Symfony console command for creating
the tables:

    php app/console doctrine:schema:update --force

It should tell you that a number of queries have been executed.

Hello World
-----------

There are two front controllers in the `web` folder: `app.php` and `app_dev.php`.
The former is hidden in the URL by Rewrite rules in the `.htaccess` file. The
latter must be called explicitly.

When calling it in the browser, try using the `app_dev.php` front controller:

    http://www.writtengames.local/app_dev.php

Note the debug / profiler bar at the bottom which will be rendered in the `dev`
environment.

Structure
---------

The structure of a Symfony project is fairly simple: `app` houses the project
configuration, `src` contains the application level source code, and `vendor`
is where the vendor code lives.

Each bundle, whether that's an application level bundle or a vendor bundle,
has its own controllers, resources (assets, templates, bundle config), model,
and all other classes it needs.

Connecting an identity to an existing account
---------------------------------------------

This app allows users to connect more than one social login to their accounts.
If an authenticated user connects a social login to their account that was
currently in use by another account (the use case here would be to merge an
account that was created in error into the account they want to keep using),
an event is fired which you can catch and react to (e.g. for assigning the
merging user account to existing data of the merged account). The ID of that
event is `security.user_accounts_merged`.
