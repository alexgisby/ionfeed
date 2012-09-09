<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('BASEDIR', __DIR__ . '/..');

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
	$searchresults = array();
	
	if($searchterm !== null && trim($searchterm) != '')
	{
		// Run the search
		$query = new BBC\iPlayer\ION\SearchQuery();
		
		$query->setParam('search_availability', 'iplayer');
		$query->setParam('service_type', 'radio');
		$query->setParam('q', $searchterm);
		$query->setParam('perpage', 20);
		
		$result = $query->execute();
		
		$searchresults = $result->groupedByShow();
	}
	
	if($request->isXmlHttpRequest())
	{
		$return_result = array(
			'searchterm' => $searchterm,
			'results' => $searchresults,
		);
		
		return $app->json($return_result);
	}
	else
	{
		return $app['twig']->render('results.twig', array(
			'searchterm' => $searchterm,
			'results' => $searchresults
		));
	}
	
});


/**
 * ------------ Run boy run ---------------------
 */
$app->run();
