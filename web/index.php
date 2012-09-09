<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

/**
 * --------- Service Providers -------------------
 */

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/../views',
));


/**
 * -------------- App Routes ----------------------
 */

$app->get('/', function() use($app) {
	return $app['twig']->render('index.twig');
});


$app->get('search', function() use($app){
	
	
	
});


/**
 * ------------ Run boy run ---------------------
 */
$app->run();
