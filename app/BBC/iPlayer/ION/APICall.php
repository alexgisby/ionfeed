<?php

namespace BBC\iPlayer\ION;

/**
 * Grand-daddy API call to the ION feed API (I'm assuming there's more than one endpoint in ION)
 *
 * Performs validation on the input to API calls, preps and prepares them.
 *
 * @author 	Alex Gisby <alex@solution10.com>
 */
class APICall
{
	/**
	 * @var 	array 	Parameter values
	 */
	private $_params = array();
	

	/**
	 * Adding an allowed parameter to the API call
	 *
	 *	<code>
	 * 		$call->addParam('query', array(
	 * 			'default' => 'chris',
	 * 			'validation' => function($value)
	 * 			{
	 * 				return (strlen(trim($value)) > 0);
	 * 			}
	 * 		));
	 * </code>
	 *
	 * Everything in the paramDetails array is optional, include it only if you need it.
	 *
	 * @param 	string 		Param name
	 * @param 	array 		Array containing validation, default values etc.
	 * @return 	this 		Chainable.
	 */
	protected function addParam($paramName, array $paramDetails = array())
	{
		$this->_params[$paramName] = $paramDetails;
		return $this;
	}
	
	/**
	 * Set a parameter to the query
	 *
	 * @param 	string 		Parameter name
	 * @param 	mixed 		Value
	 * @return 	this 		Chainable
	 * @throws 	BBC\iPlauyer\ION\QueryException 	If the validation fails or the param is not known.
	 */
	public function setParam($paramName, $value)
	{
		if(!array_key_exists($paramName, $this->_params))
			throw new QueryException('Unknown parameter "' . $paramName . '"', QueryException::UNKNOWN_PARAM);
		
		// Check for and run validation:
		$param = $this->_params[$paramName];
		
		if(array_key_exists('validation', $this->_params[$paramName]))
		{
			if(!$this->_params[$paramName]['validation']($value))
			{
				throw new QueryException('Param "' . $paramName . '" fails validation with value: "' . $value . '"', QueryException::FAILED_VALIDATION);
			}
		}
		
		$this->_params[$paramName]['value'] = $value;
		
		return $this;
	}
	
	/**
	 * Executes the query against the API and returns the result.
	 *
	 * @return 	\BBC\iPlayer\ION\SearchResult
	 * @throws 	\BBC\iPlayer\ION\QueryException
	 */
	public function execute()
	{
		$url = $this->requestURL();
		
		$cacheDirState = $this->checkCacheDir();
		
		$cache_key 		= sha1($url) . '.json';
		$cached_item 	= $this->readCache($cache_key);
		
		if(!$cached_item || $cacheDirState === false)
		{
			// Curl. Yay.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
			$curl_result = curl_exec($ch);
			curl_close($ch);
						
			$cached_item = $this->writeCache($cache_key, $curl_result);
		}
		
		$resultObj = new SearchResult($cached_item);
		
		// $resultObj = json_decode($cached_item);
		// echo '<pre>' . print_r($resultObj, true) . '</pre>'; exit;
		
		return $resultObj;
	}
	
	/**
	 * Returns the full URL that will be used for this request
	 *
	 * @return 	string
	 */
	public function requestURL()
	{
		// We need this later to work out the URL of the API Call:
		$called_class 	= get_called_class();

		$query_string 	= $this->buildQueryString();
		$request_url 	= $called_class::API_URL . $query_string;

		// Add in the JSON format:
		$request_url .= '/format/json';
		
		return $request_url;
	}
	
	
	/**
	 * Builds up the "query string" for this request. URLencodes everything, and puts it in the format:
	 *
	 * 	/{key1}/{value1}/{key2}/{value2}
	 *
	 * Any parameters which have a value or default are filled in, all others are ommitted.
	 *
	 * @return 	string
	 */
	protected function buildQueryString()
	{
		$qs = '';
		
		foreach($this->_params as $key => $param)
		{
			if(array_key_exists('value', $param))
			{
				$qs .= '/' . urlencode($key) . '/' . urlencode($param['value']);
			}
			elseif(array_key_exists('default', $param))
			{
				$qs .= '/' . urlencode($key) . '/' . urlencode($param['default']);
			}
		}
		
		return $qs;
	}
	
	/**
	 * -------------------- Caching Functions ----------------------
	 */
	
	/**
	 * Returns the path of the cachedir
	 *
	 * @return 	string
	 */
	private function cachedir()
	{
		return BASEDIR . DIRECTORY_SEPARATOR . 'cache';
	}
	
	
	/**
	 * Checks that the cache dir exists and is writeable
	 * Largely useless as you'll get errors anyway usually, so mkdir and chmod
	 * yourself :)
	 */
	private function checkCacheDir()
	{
		if(!file_exists($this->cachedir()))
		{
			mkdir($this->cachedir(), 0777);
		}
		
		if(!is_writeable($this->cachedir()))
		{
			chmod($this->cachedir(), 0777);
		}
	}
	
	/**
	 * Reads the cache to see if an item exists or not
	 *
	 * @param 	string 	The cache filename to find
	 * @param 	int 	The max length of time to consider the cache valid. In seconds.
	 * @return 	mixed 	Either the contents of the cache file, or FALSE
	 */
	private function readCache($cache_key, $maxcache_age = 36)
	{
		$fullpath = $this->cachedir() . '/' . $cache_key;
		if(file_exists($fullpath) && filemtime($fullpath) > time() - $maxcache_age)
		{
			return file_get_contents($fullpath);
		}
		
		return false;
	}
	
	/**
	 * Writing into the cache
	 *
	 * @param 	string 	Cache key
	 * @param 	string 	Response to cache
	 * @return 	mixed 	Returns the object it just wrote
	 */
	private function writeCache($cache_key, $item)
	{
		$fullpath = $this->cachedir() . '/' . $cache_key;
		file_put_contents($fullpath, $item);
		return $item;
	}
}