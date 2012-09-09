<?php

namespace BBC\iPlayer\ION\Tests;

class SearchQueryTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$query = new \BBC\iPlayer\ION\SearchQuery();
		$this->assertTrue($query instanceof \BBC\iPlayer\ION\SearchQuery);
		$this->assertTrue($query instanceof \BBC\iPlayer\ION\APICall);
	}
	
	/**
	 * Testing the adding of values to the query
	 */
	public function testValidValue()
	{
		$query = new \BBC\iPlayer\ION\SearchQuery();
		$query->setParam('service_type', 'tv');
		$this->assertTrue($query->setParam('service_type', 'tv') instanceof \BBC\iPlayer\ION\SearchQuery);
	}
	
	/**
	 * Testing adding a bad value to a param with validation
	 *
	 * @expectedException 	\BBC\iPlayer\ION\QueryException
	 * @expectedErrorCode 	2
	 */
	public function testInvalidValue()
	{
		$query = new \BBC\iPlayer\ION\SearchQuery();
		$query->setParam('service_type', 'web');
	}
	
	/**
	 * Testing adding a param value we don't know about
	 *
	 * @expectedException 	\BBC\iPlayer\ION\QueryException
	 * @expectedErrorCode 	1
	 */
	public function testUnknownValue()
	{
		$query = new \BBC\iPlayer\ION\SearchQuery();
		$query->setParam('unknown_param_name', 'value');
	}
	
	/**
	 * ------------------------ Per-Field validations -------------------------------------
	 */
	
	/**
	 * Testing the per-field values with valid values. Bit naughty to loop this, but saving some time.
	 */
	public function testPerField()
	{
		$values = array(
			'coming_soon_within' => 27,
			'local_radio' => 'include',
			'max_tleos' => 3,
			'media_set' => 'exampleSet',
			'page' => 27,
			'perpage' => 5,
			'search_availability' => 'ondemand',
			'service_type' => 'radio',
			'signed' => 1,
		);
		
		$query = new \BBC\iPlayer\ION\SearchQuery();
		
		foreach($values as $paramName => $value)
		{
			$this->assertTrue($query->setParam($paramName, $value) instanceof \BBC\iPlayer\ION\SearchQuery);
		}
	}
	
	/**
	 * Testing invalid values. Again, naughty to loop this, but trying to save time.
	 */
	public function testPerFieldInvalid()
	{
		$invalid_count = 0;
		
		$values = array(
			'coming_soon_within' => 27000,
			'local_radio' => 'badger',
			'max_tleos' => 33,
			'media_set' => 'exampleSet7654',
			'page' => -4,
			'perpage' => -1,
			'search_availability' => 'never',
			'service_type' => 'telepathic',
			'signed' => 90,
		);
		
		$query = new \BBC\iPlayer\ION\SearchQuery();
		
		foreach($values as $paramName => $value)
		{
			try
			{
				$query->setParam($paramName, $value);
			}
			catch(\BBC\iPlayer\ION\QueryException $e)
			{
				$this->assertEquals(\BBC\iPlayer\ION\QueryException::FAILED_VALIDATION, $e->getCode());
				$invalid_count ++;
			}
		}
		
		$this->assertEquals(count($values), $invalid_count);
	}
	 
	
}