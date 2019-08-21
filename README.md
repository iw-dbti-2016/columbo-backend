# TravelCompanion: web

This is an introduction to the TravelCompanion platform.

This document will cover following items:

* Introduction
* Requirements
* Setup
* Functionalities

# Introduction

The TravelCompanion back-end is implemented using the PHP-framework [Laravel](https://www.laravel.com/).

JWT will be used for authentication over the API. Considering there is only a first-party app, JWT seems a good option. The package [JWT-auth](https://github.com/tymondesigns/jwt-auth) is used. In the Setup, the generation of a `JWT_SECRET`-key is included.

Front-end will use [Tailwind CSS](https://tailwindcss.com/) and (probably) [Vue.js](https://vuejs.org/). The front-end communicates with the back-end via the back-end api, which is also used by the Android application you can find [here](https://github.com/iw-dbti-2016/travel-companion-app). Both will use the JWT-authentication. Tokens will be stored in the secure storage on android. In Vue, we are currently looking into Vue's JWT options (external packages).

We plan to use following services at some point:

* [OpenStreetMap](https://www.openstreetmap.org/)

	For showing maps in the reports.
* [Pusher](https://pusher.com/)

	For pushing messages to clients and to the mobile app.
* [Open Exchange Rates](https://openexchangerates.org/)

	To retreive the exchange rates for price conversion when payments are implemented and an overview of the cost must be generated.
* To be continued...

# Requirements

The requirements to run this back-end are the following (they mostly follow from the Laravel-requirements):

* PHP >= 7.1.3

	With following extensions (from the [Laravel Docs page](https://laravel.com/docs/5.8)):
	* BCMath PHP Extension
	* Ctype PHP Extension
	* JSON PHP Extension
	* Mbstring PHP Extension
	* OpenSSL PHP Extension
	* PDO PHP Extension
	* Tokenizer PHP Extension
	* XML PHP Extension
* MySQL >= 5.6

	With InnoDB 5.6.43 or higher. Starting at MySQL 5.7.4 InnoDB supports spatial indexes, which is something we'll definitely look into during development.
* Redis, version t.b.d. chances are very high that redis will be used though.
* Composer
* npm

# Setup

Following are the steps for the project-setup (all commands are run from the root directory):

* Clone this repo onto you local machine and cd into it's directory.
* Create a `.env` file in the root directory and configure the required keys for this application (pusher, redis queue, mysql credentials). The `.env.example` can be used as an example to fill in the required keys.
* [Optionally] For testing add a `.env.testing` with the required keys. Preferably create a new database for testing, because the `RefreshDatabase` trait is used in the PHPUnit tests. More on tests below.
* Run `Composer install`
* Run `npm install`
* Run `php artisan key:generate` to create a unique key for the application
* Run `php artisan storage:link`
* Run `php artisan migrate`
* Run `php artisan jwt:secret` to generate a `JWT_SECRET` in your `.env` (confirm with `yes` when asked to invalidate tokens and override secret key).
* [Unnecessary] Set up telescope by running `php artisan telescope:install`.
* Run `npm run dev`
* Run `php artisan serve`
* Go to the browser, this application is now live on you machine.
* [Optionally] You can run `npm run watch` when making changes to the resource-files (js/css).

For a production environment one would obviously run `npm run production`. Note that Laravel mix automatically adds versioning to the resource-files and uses purgeCSS to decrease the final css-file even further! Autoprefixer is always used, also in development. Thus no need to worry about this.

Following are the steps for running the tests:

* Make sure you have a `.env.testing`-file in the root directory (next to the `.env`-file). This file can be identical to `.env` with the distinction that there should be a different database for running the tests. Our PHPUnit-tests use the `RefreshDatabase` trait extensively. This will refresh the database after each test. Refreshing the application database is not desirable, hence the seperate database.
* From the root directory, run `./vendor/phpunit/phpunit/phpunit`. This will run all tests. To filter to specific tests or files, use the `--filter` option.

# Functionalities

Updated on: 20/08/2019

The core functionality will be written first. This includes the authentication (which is included in Laravel + laravel passport/a JWT package), the trips, reports, sections and locations.

Following functionalities are currently planned to be added in the future, they will be added after the full implementation of the core functionalities. The database migrations for these items are already available in the database-folder, they have been added in the initial design in order to allow for an overview of the full application (listed in random order):

* Payments
* Planning
* Documents
* Actions
* Links
* Roles & Permissions
* Friends
