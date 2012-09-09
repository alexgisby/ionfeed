<?php

namespace BBC\iPlayer\ION;

/**
 * ION Search Query Class.
 *
 * This class is for querying the iPlayer ION search feed for programmes.
 *
 * @author 	Alex Gisby <alex@solution10.com>
 */
class SearchQuery extends APICall
{
	/**
	 * Base URL for the search feeds
	 */
	const SEARCH_URL = 'http://www.bbc.co.uk/iplayer/ion/searchextended';

	/**
	 * Constructor. Builds up the ruleset for this API Call and returns the instance.
	 */
	public function __construct()
	{
		$this
			->addParam('category')
			->addParam('coming_soon_within', array(
				'validation' => function($value)
				{
					return (is_numeric($value) && $value >= 1 && $value <= 168);
				}
			))
			->addParam('local_radio', array(
				'validation' => function($value)
				{
					return (in_array($value, array('include', 'exclude', 'exclusive')));
				}
			))
			->addParam('masterbrand')
			->addParam('max_tleos', array(
				'validation' => function($value)
				{
					return (is_numeric($value) && $value <= 4 && $value >= 0);
				}
			))
			->addParam('media_set', array(
				'validation' => function($value)
				{
					return preg_match('/^[a-zA-Z]+$/', $value);
				},
			))
			->addParam('page', array(
				'validation' => function($value)
				{
					return (is_numeric($value) && $value >= 1);
				}
			))
			->addParam('perpage', array(
				'validation' => function($value)
				{
					return (is_numeric($value) && $value >= 1);
				}
			))
			->addParam('q')
			->addParam('search_availability', array(
				'validation' => function($value){
					return (in_array($value, array('iplayer', 'any', 'discoverable', 'ondemand', 'simulcast', 'comingup')));
				},
				'default' => 'any',
			))
			->addParam('service_type', array(
				'validation' => function($value)
				{
					return (in_array($value, array('tv', 'radio')));
				}
			))
			->addParam('signed', array(
				'validation' => function($value)
				{
					return (is_numeric($value) && ($value == 0 || $value == 1));
				}
			));
	}
}