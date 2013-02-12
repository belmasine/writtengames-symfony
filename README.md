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

Clone this repository:

    git clone git@github.com:userfriendly/writtengames-symfony.git

(Adapt the repository URL if you forked it, which I would recommend)

Installation
------------

Run Composer to pull the vendor bundles (Symfony's equivalent to Gems):

    composer.phar install

Symfony does not do the Bring Your Own Server thing, so you'll have to create a
virtual host for it. Point the DocumentRoot to the `web` subfolder of the project
directory.

Hello World
-----------

There are two front controllers in the `web` folder: `app.php` and `app_dev.php`.
The former is hidden in the URL by Rewrite rules in the `.htaccess` file. The
latter must be called explicitly.

When calling it in the browser, try using the `app_dev.php` front controller:

    http://www.writtengames.sf2/app_dev.php

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
