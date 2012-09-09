<?php

namespace BBC\iPlayer\ION;

/**
 * The exception which is thrown should something go bang with an ION API Call
 *
 * @author 	Alex Gisby <alex@solution10.com>
 */
class QueryException extends \Exception
{
	/**
	 * Error-code constants.
	 */
	const UNKNOWN_PARAM = 1;
	const FAILED_VALIDATION = 2;
}