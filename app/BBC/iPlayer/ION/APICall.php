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
		
		$this->check_cache_dir();
		
		// Curl. Yay.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		return $result;
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
	
}