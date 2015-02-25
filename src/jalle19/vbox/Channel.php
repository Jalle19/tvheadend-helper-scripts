<?php

namespace jalle19\vbox;

/**
 * Description of Channel
 *
 * @author sam
 */
class Channel
{

	private $_name;
	private $_url;
	private $_encrypted;

	public function getName()
	{
		return $this->_name;
	}

	public function getUrl()
	{
		return $this->_url;
	}

	public function isEncrypted()
	{
		return $this->_encrypted;
	}

	public function setName($name)
	{
		$this->_name = $name;
	}

	public function setUrl($url)
	{
		$this->_url = $url;
	}

	public function setEncrypted($encrypted)
	{
		$this->_encrypted = $encrypted;
	}

}
