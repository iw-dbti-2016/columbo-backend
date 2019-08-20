# TravelCompanion: web

This is an introduction to the TravelCompanion platform.

This document will cover following items:

* Introduction
* Requirements
* Setup
* Functionalities

# Introduction

The TravelCompanion back-end is implemented using the PHP-framework [Laravel](https://www.laravel.com/).

Either Laravel Passport or JWT will be used for authentication over the API. Selection of which will be implemented is currently happening. Considering there is only a 1st party app, JWT could be used. Though because of possible extension to 3rd party applications later (unlikely though) and (mainly) because of the easy of consuming our own API for the SPA front-end through Vue and Axios, Passport might be a better solution.

Front-end will (propably) use [Tailwind CSS](https://tailwindcss.com/) and [Vue.js](https://vuejs.org/). The front-end communicates with the back-end via the back-end api, which is also used by the Android application you can find [here](https://github.com/iw-dbti-2016/travel-companion-app).

We plan to use following services:

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

* Clone this repo onto you local machine and cd into it's directory.
* Run `Composer install`
* Run `npm install`
* Create a `.env` file in the root directory and configure the required keys for this application (pusher, redis queue, mysql credentials). The `.env.example` can be used as an example to fill in the required keys.
* [Optionally] For testing add a `.env.testing` with the required keys. Preferably create a new database for testing, because the `RefreshDatabase` trait is used in the PHPUnit tests.
* [tbd] setup passport?
* [tbd] insert client grant?
* [tbd] setup jwt?
* [Optionally] Set up telescope by running `php artisan telescope:install`.

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
