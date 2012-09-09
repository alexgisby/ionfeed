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
	 * 			'default' => null,
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
}