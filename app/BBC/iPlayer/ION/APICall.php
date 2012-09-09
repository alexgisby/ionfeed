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
	
}