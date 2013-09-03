ABT ZendSkeletonApplication
=======================

Introduction
------------
This is a simple, skeleton application using the ZF2 MVC layer and module
systems. Additionally it has Doctrine2 integrated into its configuration and ZfcUser module (with Doctrine support) added for basic user entity CRUD. This should speed up the ramp-up time for custom site projects.
The project also has a FlashMessenger view helper created and configured, as well as a nagivation view helper in place. It comes with Twitter Bootstrap and AngularJS out of the box.


Installation
------------

* Create a fork of this repository.
* Copy the file config/autoload/doctrine.local.php.dist to config/autoload/doctrine.local.php and fill in the database connection settings accordingly.
* Start development!


Entities
--------
This application is configured for use with Doctrine2 (http://www.doctrine-project.org/) with entities using annotations. All entities should inherif from the Base entity which is a mapped superclass providing an ID column as well as some helper functions.

User Entity
-----------
The User entity (and two example subclasses) are in the Application module. Look in module/Application/src/Application/Entity/Base/User.php

ACL
---
The ACL controlling where users may go is located in the config/autoload/acl.global.php file

Navigation
----------
The navigation is configured in config/autoload/nav.global.php
