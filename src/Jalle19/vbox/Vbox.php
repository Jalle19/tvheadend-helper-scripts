<?php

namespace Jalle19\vbox;

use Jalle19\vbox\exception;

/**
 * Represents a VBox Gateway
 *
 * @author Sam Stenvall <neggelandia@gmail.com>
 */
class Vbox
{

	/**
	 * @var string
	 */
	private $_hostname;

	/**
	 * @var int
	 */
	private $_port;

	/**
	 * @var \Zend\Http\Client
	 */
	private $_httpClient;

	/**
	 * Class constructor
	 * @param string $hostname
	 * @param int $port
	 */
	public function __construct($hostname, $port)
	{
		$this->_hostname = $hostname;
		$this->_port = $port;
		$this->_httpClient = new \Zend\Http\Client();
	}

	/**
	 * Returns the list of channels
	 * @param boolean $includeEncrypted
	 * @return Channel[]
	 */
	public function getChannels($includeEncrypted = false)
	{
		$request = new request\Request('GetXmltvChannelsList', array(
			'FromChIndex'=>'FirstChannel',
			'ToChIndex'=>'LastChannel'));

		$response = $this->performRequest($request);
		$document = new \SimpleXMLElement($response);
		$channels = array();

		// Convert the XML into channel objects
		foreach ($document->channel as $channelXml)
		{
			$displayName = $channelXml->{'display-name'};

			if (count($displayName) < 4)
				throw new exception\VboxException('Unexpected channel list reply, expected at least 4 <display-name> elements');

			$channel = new Channel();
			$channel->setName((string) $displayName[0]);
			$channel->setEncrypted($displayName[3] != 'Free');
			$channel->setUrl((string) $channelXml->url['src']);

			if ($channel->isEncrypted() && !$includeEncrypted)
				continue;

			$channels[] = $channel;
		}

		return $channels;
	}

	/**
	 * Performs a request and returns the response
	 * @param request\Request $request
	 */
	private function performRequest($request)
	{
		// Create the HTTP request
		$httpRequest = new \Zend\Http\Request();
		$httpRequest->setUri('http://' . $this->_hostname . ':' . $this->_port . $request->getUrl());

		try {
			$response = $this->_httpClient->dispatch($httpRequest);
			return $response->getContent();
		}
		catch (\Exception $e) {
			// Rethrow exceptions under our own namespace
			throw new exception\VboxException($e->getMessage(), $e->getCode(), $e);
		}
	}

}
