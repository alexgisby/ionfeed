# Features

Frontend:
- Searches the BBC iPlayer ION feeds for Radio shows
- Returns matching shows and recent episodes

Backend:
- Caching system to speed up results.
- Query builder interface for more advanced queries.
- (some) PHPUnit Tests (/tests)
- PSR-0 compliant
- Namespacing
- Anon functions for validation in APICall

## Structure / Tech

This app is built on [Silex](silex.sensiolabs.org). The request handling and output occurs in web/index.php
The clever code that does the querying is in app/

The frontend is built on [Twitter Bootstrap](http://twitter.github.com/bootstrap).

I've used [Composer](http://getcomposer.org) for dependancy management, and you'll need this to install the app.

## Installation

	git clone https://github.com/alexgisby/ionfeed.git ionfeed
	cd ionfeed
	mkdir cache
	chmod -R 0777 cache
	composer install

## Tests

Tests are written with PHPUnit and should run fine from the top of the repo.

## Live Demo

There's a live demo here: [ionfeed.solution10.com](http://ionfeed.solution10.com)