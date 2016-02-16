<?php

namespace Jalle19\command;

use Jalle19\tvheadend\cli\TvheadendCommand;
use Jalle19\tvheadend;
use Jalle19\tvheadend\exception;

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
class TvheadendChannelImporter extends TvheadendCommand
{

	/**
	 * Initializes the command
	 */
	public function __construct()
	{
		parent::__construct('source');
		$command = $this->command;

		// Target tvheadend instance options
		$command->option('target-tvheadend-hostname')
				->require()
				->describeAs('The target hostname where tvheadend is running');

		$command->option('target-tvheadend-http-port')
				->default(9981)
				->describeAs('The target tvheadend HTTP port');

		$command->option('target-tvheadend-username')
				->describeAs('The target tvheadend username');

		$command->option('target-tvheadend-password')
				->describeAs('The target tvheadend password');

		// More parameters
		$command->option('source-network-filter')
				->describeAs('The network the source channel list should be filtered by before importing');

		$command->option('target-network-name')
				->require()
				->describeAs('The name of the network the channels will be imported to');

		$this->run();
	}

	/**
	 * Runs the actual command
	 */
	private function run()
	{
		// Parse options
		$sourceHostname = $this->command->getFlagValues()['source-tvheadend-hostname'];
		$sourcePort = $this->command->getFlagValues()['source-tvheadend-http-port'];
		$sourceUsername = $this->command->getFlagValues()['source-tvheadend-username'];
		$sourcePassword = $this->command->getFlagValues()['source-tvheadend-password'];

		$targetHostname = $this->command->getFlagValues()['target-tvheadend-hostname'];
		$targetPort = $this->command->getFlagValues()['target-tvheadend-http-port'];
		$targetUsername = $this->command->getFlagValues()['target-tvheadend-username'];
		$targetPassword = $this->command->getFlagValues()['target-tvheadend-password'];

		$sourceNetworkFilter = $this->command->getFlagValues()['source-network-filter'];
		$targetNetworkName = $this->command->getFlagValues()['target-network-name'];

		// Create the source and target tvheadend instance
		$sourceTvheadend = new tvheadend\Tvheadend($sourceHostname, $sourcePort, $sourceUsername, $sourcePassword);
		$targetTvheadend = new tvheadend\Tvheadend($targetHostname, $targetPort, $targetUsername, $targetPassword);

		// Create the network
		$this->logger->addNotice('Creating target network '.$targetNetworkName);

		$network = new tvheadend\model\network\IptvNetwork();
		$network->networkname = $targetNetworkName;
		$network->max_streams = 1;

		try
		{
			$targetTvheadend->createNetwork($network);

			// Retrieve the newly created network, we need the UUID
			$network = $targetTvheadend->getNetwork($targetNetworkName);
		}
		catch (exception\RequestFailedException $e)
		{
			$this->logger->addEmergency('Failed to create network ('.$e->getMessage.')');
		}

		// Get the filtered channel list
		$channelFilter = new tvheadend\model\Filter();
		$channelFilter->addDefinition('string', $sourceNetworkFilter, 'services');
		$channels = $sourceTvheadend->getChannels($channelFilter);

		foreach ($channels as $channel)
		{
			$channelName = $channel->name;

			// Add each channel as a mux on the target
			$url = $sourceTvheadend->getAbsoluteUrl($channel);
			$this->logger->addNotice('Creating multiplex for channel '.$channelName);

			$multiplex = new tvheadend\model\multiplex\IptvMultiplex();
			$multiplex->iptv_url = $url;
			$multiplex->iptv_muxname = $channelName;
			$multiplex->iptv_sname = $channelName;

			// Make sure EPG scanning is enabled
			$multiplex->epg = 1;

			try
			{
				$targetTvheadend->createMultiplex($network, $multiplex);
			}
			catch (exception\RequestFailedException $e)
			{
				$this->logger->addEmergency('Failed to create multiplex ('.$e->getMessage.')');
			}
		}

		$this->logger->addNotice('Done');
	}

}
