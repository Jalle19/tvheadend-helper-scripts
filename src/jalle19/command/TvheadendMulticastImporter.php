<?php

namespace jalle19\command;

use jalle19\tvheadend\cli\TvheadendCommand;
use jalle19\tvheadend;
use jalle19\tvheadend\exception;

/**
 * Copyright (C) 2015 Sam Stenvall
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @author Sam Stenvall <neggelandia@gmail.com>
 */
class TvheadendMulticastImporter extends TvheadendCommand
{

	/**
	 * Initializes the command
	 */
	public function __construct()
	{
		parent::__construct();
		$command = $this->command;

		// Add required parameters
		$command->option('network-name')
				->require()
				->describeAs('The name of the network the muxes should be added to');

		$command->option('multicast-subnet')
				->require()
				->must(function($subnet)
				{
					return count(explode('/', $subnet)) === 2;
				})
				->describeAs('The multicast subnet that should be added, e.g. 224.3.0.0/24');

		$command->option('url-format')
				->require()
				->describeAs('The URL format, where %s is the multicast address (e.g. udp://%s:5555');

		// Add optional parameters
		$command->option('interface')
				->describeAs('The interface the multicast is received on');

		$command->option('enable-epg-scan')
				->boolean()
				->default(false)
				->describeAs('Whether to enable EPG scanning for the added muxes');

		$this->run();
	}

	/**
	 * Runs the command
	 */
	private function run()
	{
		// Parse options
		$hostname = $this->command->getFlagValues()['tvheadend-hostname'];
		$port = $this->command->getFlagValues()['tvheadend-http-port'];
		$username = $this->command->getFlagValues()['tvheadend-username'];
		$password = $this->command->getFlagValues()['tvheadend-password'];
		$networkName = $this->command->getFlagValues()['network-name'];
		$subnet = $this->command->getFlagValues()['multicast-subnet'];
		$urlFormat = $this->command->getFlagValues()['url-format'];
		$interface = $this->command->getFlagValues()['interface'];
		$enableEpgScan = $this->command->getFlagValues()['enable-epg-scan'];

		// Create the tvheadend instance
		$tvheadend = new tvheadend\Tvheadend($hostname, $port, $username, $password);

		// Create the network
		$this->logger->addNotice('Creating network '.$networkName);

		$network = new tvheadend\model\network\IptvNetwork();
		$network->networkname = $networkName;
		$network->max_streams = 1;

		try {
			$tvheadend->createNetwork($network);

			// Retrieve the newly created network, we need the UUID
			$network = $tvheadend->getNetwork($networkName);
		}
		catch (exception\RequestFailedException $e) {
			$this->logger->addEmergency('Failed to create network ('.$e->getMessage.')');
		}

		// Add each multicast address as a multiplex
		foreach ($this->getSubnetAddresses($subnet) as $address)
		{
			$url = sprintf($urlFormat, $address);
			$this->logger->addNotice('Creating multiplex for address '.$url);

			$multiplex = new tvheadend\model\multiplex\IptvMultiplex();
			$multiplex->iptv_url = $url;
			$multiplex->epg = $enableEpgScan ? 1 : 0;

			if ($interface)
				$multiplex->iptv_interface = $interface;

			// Add the multiplex
			try {
				$tvheadend->createMultiplex($network, $multiplex);
			}
			catch (exception\RequestFailedException $e) {
				$this->logger->addEmergency('Failed to create multiplex ('.$e->getMessage.')');
			}
		}
		
		$this->logger->addNotice('Done');
	}

	/**
	 * Converts the specified subnet into a list of addresses. Copied from 
	 * http://stackoverflow.com/a/8880170
	 * @param string $subnet the subnet
	 * @return string[] the addresses in the subnet
	 */
	private function getSubnetAddresses($subnet)
	{
		$addresses = array();
		list($ip, $len) = explode('/', $subnet);

		if (($min = ip2long($ip)) !== false)
		{
			$max = ($min | (1 << (32 - $len)) - 1);
			for ($i = $min; $i < $max; $i++)
				$addresses[] = long2ip($i);
		}

		return $addresses;
	}

}
