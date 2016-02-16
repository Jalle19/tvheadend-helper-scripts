<?php

namespace Jalle19\vbox\request;

/**
 * Description of Request
 *
 * @author sam
 */
class Request
{

	const BASE_URL = '/cgi-bin/HttpControl/HttpControlApp?OPTION=1';

	/**
	 * @var string
	 */
	private $_method;

	/**
	 * @var array
	 */
	private $_parameters;

	public function __construct($method, $parameters = array())
	{
		$this->_method = $method;
		$this->_parameters = $parameters;
	}

	/**
	 * Returns the relative request URL
	 */
	public function getUrl()
	{
		$parameters = array_merge(array('Method'=>$this->_method), $this->_parameters);
		$url = self::BASE_URL;
		
		foreach ($parameters as $parameter => $value)
			$url .= '&' . urlencode($parameter) . '=' . urlencode($value);

		return $url;
	}

}
