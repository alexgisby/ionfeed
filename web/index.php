<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app['debug'] = true;

/**
 * --------- Service Providers -------------------
 */

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/../views',
));


/**
 * -------------- App Routes ----------------------
 */

/**
 * Just displays a massive search box for the user to hit
 */
$app->get('/', function() use($app) {
	return $app['twig']->render('index.twig');
});

/**
 * Action that does the searching and displays the results
 */
$app->get('search', function(Application $app, Request $request) {
	
	$searchterm = $request->get('q');
	
	if($searchterm !== null)
	{
		// Run the search
		$query = new BBC\iPlayer\ION\SearchQuery();
		
		$query->setParam('search_availability', 'iplayer');
		$query->setParam('service_type', 'radio');
		$query->setParam('q', $searchterm);
		
		$result = $query->execute();
		var_dump($result); exit;
	}
	
	if($request->isXmlHttpRequest())
	{
		$return_result = array(
			'searchterm' => $searchterm,
			'results' => array(),
		);
		
		return $app->json($return_result);
	}
	else
	{
		return $app['twig']->render('results.twig', array(
			'results' => array(),
			'searchterm' => $searchterm,
		));
	}
	
});


/**
 * ------------ Run boy run ---------------------
 */
$app->run();
