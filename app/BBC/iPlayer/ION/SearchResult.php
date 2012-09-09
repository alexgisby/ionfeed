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
}