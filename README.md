# TravelCompanion: web

This is an introduction to the TravelCompanion platform.

This document will cover following items:

* [Introduction](https://github.com/iw-dbti-2016/travel-companion-web#introduction)
* [Requirements](https://github.com/iw-dbti-2016/travel-companion-web#requirements)
* [Setup](https://github.com/iw-dbti-2016/travel-companion-web#setup)
* [Functionalities](https://github.com/iw-dbti-2016/travel-companion-web#functionalities)
* [API Response format](https://github.com/iw-dbti-2016/travel-companion-web#api-response-format)

## Introduction

The TravelCompanion back-end is implemented using the PHP-framework [Laravel](https://www.laravel.com/).

JWT will be used for authentication over the API. Considering there is only a first-party app, JWT seems a good option. The package [JWT-auth](https://github.com/tymondesigns/jwt-auth) is used. In the Setup, the generation of a `JWT_SECRET`-key is included.

Front-end will use [Tailwind CSS](https://tailwindcss.com/) and (probably) [Vue.js](https://vuejs.org/). The front-end communicates with the back-end via the back-end api, which is also used by the Android application you can find [here](https://github.com/iw-dbti-2016/travel-companion-app). Both will use the JWT-authentication. Tokens will be stored in the secure storage on android. In Vue, we are currently looking into Vue's JWT options (external packages).

We plan to use following services at some point:

* [OpenStreetMap](https://www.openstreetmap.org/)\
	For showing maps in the reports.
* [Pusher](https://pusher.com/)\
	For pushing messages to clients and to the mobile app.
* [Open Exchange Rates](https://openexchangerates.org/)\
	To retreive the exchange rates for price conversion when payments are implemented and an overview of the cost must be generated.
* Probably [Let's Encrypt](https://letsencrypt.org/)\
	For SSL-certificates.
* We are looking into [DigitalOcean](https://www.digitalocean.com/)\
	For hosting
* We are looking into [Cloudflare](https://www.cloudflare.com/)\
	For DDoS Protection & Caching
* To be continued...

## Requirements

The requirements to run this back-end are the following (they mostly follow from the Laravel-requirements):

* PHP >= 7.1.3\
	With following extensions (from the [Laravel Docs page](https://laravel.com/docs/5.8)):
	* BCMath PHP Extension
	* Ctype PHP Extension
	* JSON PHP Extension
	* Mbstring PHP Extension
	* OpenSSL PHP Extension
	* PDO PHP Extension
	* Tokenizer PHP Extension
	* XML PHP Extension
* MySQL >= 5.6\
	With InnoDB 5.6.43 or higher. Starting at MySQL 5.7.4 InnoDB supports spatial indexes, which is something we'll definitely look into during development.
* Redis, version t.b.d. chances are very high that redis will be used though.
* Composer (v1.9.* preferred)
* npm (v6.\*)

## Setup

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

## Functionalities

Updated on: 21/08/2019

The core functionality will be written first. This includes the authentication (which is included in Laravel + JWT-auth), the trips, reports, sections, locations and actions.

Following functionalities are currently planned to be added in the future, they will be added after the full implementation of the core functionalities. The database migrations for these items are already available in the database-folder, they have been added in the initial design in order to allow for an overview of the full application (listed in random order):

* Payments
* Planning
* Documents
* Links
* Roles & Permissions
* Friends

# API Response Format

Because this complete application is based around the API, it seems appropriate to define an application-wide JSON-format for all responses.

In this section, this format will be defined. **Be aware that updates may occur during development.** At a later stage, this entire section along with other technical definitions (such as the expected auth-flow) will be extracted to a different file, more appropriate for these kinds of discussions (i.e. some sort of documentation).

First of all, an appropriate HTTP statuscode must be included in every response, this statuscode is the start for any connecting instances. The format of the response might change based on the statuscode. We try to keep this to a minimum.

Generally, the `success`-attribute is required for every response, a `message`-attribute is optional and describes the result or performed action. If the `success`-attribute is `true`, the `data`-attribute is required, though it must not contain anything. When `success` is `false`, data must not be present, and clients may ignore it completely.

## GET single item - 200 (OK)

When getting a single item, the `data`-attribute contains that one item directly.

```JSON
{
	"success": true,
	"data": {
		"id": 1,
		"name": "Jane Doe",
		"email": "jane@example.com",
	},
}
```

## GET multiple items - 200 (OK)

When getting multiple items, the `data`-attribute contains an array of items.

```JSON
{
	"success": true,
	"data": [
		{
			"id": 1,
			"name": "Jane Doe",
			"email": "jane@example.com",
		},
		{
			"id": 2,
			"name": "John Doe",
			"email": "john@example.com",
		},
	],
}
```

## POST - 201 (Created)

Upon creation, the created items are returned. **This will always be an array, even if only one item has been created.**

```JSON
{
	"success": true,
	"message": "Succes (or any optional custom message)",
	"data": [
		{
			"id": 3,
			"name": "Dan Doe",
			"email": "dan@example.com",
		},
		{
			"id": 4,
			"name": "Daisy Doe",
			"email": "daisy@example.com",
		},
	],
}
```

## PATCH - 200 (OK)

On successful update, the updated item is returned directly.

```JSON
{
	"success": true,
	"message": "entity updated",
	"data": {
		"id": 1,
		"name": "Janet Doe",
		"email": "janet@example.com",
	},
}
```

## DELETE - 200 (OK)

No data is supplied upon deletion.

```JSON
{
	"success": true,
	"message": "entity deleted",
	"data": [],
}
```

## POST/PATCH - 422 (Unprocessable Entity)

When validation fails, a 422 will be returned, with the `errors`-attribute containing the errors in the data. These errors have an optional code and a required message. They're grouped by field. Fields without errors are not present in the `errors`-attribute.

```JSON
{
	"success": false,
	"message": "Validation Failed",
	"errors": {
		"name": {
			"code": 1,
			"message": "This field is required",
		},
	}
}
```

## GET/PATCH/DELETE - 404 (Not Found)

The client can recognise this response by code and can show the message if desired, otherwise show a custom message.

```JSON
{
	"success": false,
	"message": "Not Found",
}
```

## 401 (Unauthorized)

If the client is not authenticated, this response will be returned. The `message`-attribute explains why access was denied. The client will have to try to refresh it's JWT-token. If that refresh-request still results in the 401-response, the user has to relogin.

```JSON
{
	"success": false,
	"message": "token expired",
}
```

## 403 (Forbidden)

If a user does not have sufficient permissions to view the requested item(s) this request will be returned.

```JSON
{
	"success": false,
	"message": "Access denied",
}
```

*Note that 401 and 403 are used a little differently than their names indicate (basically as 'Unauthenticated' and 'Unauthorized' respectively). This is completely intentionally. A full explanation can be found in [this stackoverflow answer](https://stackoverflow.com/a/6937030). These status codes are explained in [RFC7235](https://tools.ietf.org/html/rfc7235#section-3.1) and [RFC7231](https://tools.ietf.org/html/rfc7231#section-6.5.3) that make RFC2616, which is a little less clear about the distinction, obsolete.*

## 500 (Internal Server Error)

If there is a problem with the server, this response is provided.

```JSON
{
	"success": false,
	"message": "Server Error",
}
```
