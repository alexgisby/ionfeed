<?php

namespace BBC\iPlayer\ION;

/**
 * Search Result for API Calls.
 *
 * Implements Countable, ArrayAccess and Iterator so you can use it like a normal array.
 *
 * @author 	Alex Gisby <alex@solution10.com>
 */
class SearchResult implements \Countable, \Iterator
{
	private $_result;
	
	/**
	 * Constructor
	 *
	 * @param 	string 	API Result to build from
	 * @return 	this
	 */
	public function __construct($result)
	{
		$this->_result = json_decode($result);
		
		// Bit cheeky, but we can make a stab at an image filename based off some data in the
		// results, so hey, let's do that ;)
		foreach($this->_result->blocklist as $item)
		{
			$parts = explode('/', $item->my_short_url);
			$key = $parts[2];
			
			$item->image_url = 'http://static.bbci.co.uk/programmeimages/176x99/episode/' . $key . '.jpg?nodefault=true';
		}
	}
	
	/**
	 * ----------- Implementing Countable -------------------
	 */
	
	public function count()
	{
		return count($this->_result->blocklist);
	}
	
	/**
	 * Returns the total number of results as given by the API
	 *
	 * @return 	int
	 */
	public function totalResults()
	{
		return $this->_result->count;
	}
	
	/**
	 * ------------- Implement Iterator --------------------
	 */
	
	private $iter_pos = 0;
	
	public function rewind() { $this->iter_pos = 0; }
	public function current() { return $this->_result->blocklist[$this->iter_pos]; }
	public function key() { return $this->iter_pos; }
	public function next() { ++ $this->iter_pos; }
	public function valid(){ return isset($this->_result->blocklist[$this->iter_pos]); }
	
	/**
	 * --------------- Data Digging Functions -----------------
	 */
	
	/**
	 * Returns the unique shows from this result-set. So say we have six Chris
	 * Moyles' Shows and three Chris Evan's shows, this will return a single
	 * Chris Moyles and a single Chris Evan's. The episode returned will be
	 * the first discovered in the array.
	 *
	 * @return 	array
	 */
	public function uniqueShows()
	{
		$found_shows = array();
		$return = array();
		foreach($this->_result->blocklist as $item)
		{
			if(!in_array($item->brand_title, $found_shows))
			{
				$found_shows[] = $item->brand_title;
				$return[] = $item;
			}
		}
		
		return $return;
	}
	
	/**
	 * Returns the results grouped by 'show' (brand_title)
	 *
	 * @param 	bool 	Whether the availability should be "current" or not.
	 * @return 	array
	 */
	public function groupedByShow($currentOnly = true)
	{
		$result = array();
		foreach($this->_result->blocklist as $item)
		{
			if(!$currentOnly || ($currentOnly && $item->availability == 'CURRENT'))
			{
				$result[$item->brand_title][] = $item;
			}
		}
		
		return $result;
	}
	
}