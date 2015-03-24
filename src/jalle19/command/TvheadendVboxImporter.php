<?php

namespace jalle19\command;

use jalle19\vbox;
use jalle19\tvheadend;
use jalle19\tvheadend\cli\TvheadendCommand;
use jalle19\tvheadend\exception;
use jalle19\tvheadend\model;

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
class TvheadendVboxImporter extends TvheadendCommand
{

	/**
	 * Class constructor. It extends the command options provided by the base 
	 * command.
	 */
	public function __construct()
	{
		parent::__construct();
		$command = $this->command;

		// Required tvheadend arguments
		$command->option('network-name')
				->require()
				->describeAs('The name of the network the channels will be imported to');

		// Required VBox arguments
		$command->option('vbox-hostname')
				->require()
				->describeAs('The hostname of the VBox Gateway');

		// Optional VBox arguments
		$command->option('vbox-http-port')
				->default(80)
				->describeAs('The port used to communicate with the VBox Gateway');

		$command->option('hostname-port-override')
				->describeAs('Overrides the address:port combination in the retrieved channel URLs');

		$command->option('sid-as-channel-number')
				->boolean()
				->default(false)
				->describeAs('Use the service ID as channel number (use if LCN cannot be parsed)');

		$command->option('include-encrypted')
				->boolean()
				->default(false)
				->describeAs('Whether to import encrypted channels only');

		$command->option('interactive')
				->boolean()
				->default(false)
				->describeAs('Whether to interactively select which channels to import');

		$this->run();
	}

	/**
	 * Runs the actual command
	 */
	private function run()
	{
		// Parse options
		$vboxHostname = $this->command->getFlagValues()['vbox-hostname'];
		$vboxPort = $this->command->getFlagValues()['vbox-http-port'];
		$tvheadendHostname = $this->command->getFlagValues()['tvheadend-hostname'];
		$tvheadendPort = $this->command->getFlagValues()['tvheadend-http-port'];
		$tvheadendUsername = $this->command->getFlagValues()['tvheadend-username'];
		$tvheadendPassword = $this->command->getFlagValues()['tvheadend-password'];
		$networkName = $this->command->getFlagValues()['network-name'];
		$includeEncrypted = $this->command->getFlagValues()['include-encrypted'];
		$interactive = $this->command->getFlagValues()['interactive'];
		$sidChannelNumber = $this->command->getFlagValues()['sid-as-channel-number'];

		// Retrieve the list of channels from the VBox Gateway
		$this->logger->addNotice('Fetching channel list from VBox Gateway at '.$vboxHostname);
		$vbox = new vbox\Vbox($vboxHostname, $vboxPort);
		$channels = $vbox->getChannels($includeEncrypted);

		// Create a tvheadend instance
		$this->logger->addNotice('Connecting to tvheadend at '.$tvheadendHostname.':'.$tvheadendPort);

		$tvheadend = new tvheadend\Tvheadend($tvheadendHostname, $tvheadendPort, $tvheadendUsername, $tvheadendPassword);

		// Create the target network
		$this->logger->addNotice('Creating IPTV network '.$networkName);
		$network = new model\network\IptvNetwork();
		$network->networkname = $networkName;
		$network->max_streams = 1;
		$network->sid_chnum = $sidChannelNumber;

		try {
			$tvheadend->createNetwork($network);
		}
		catch (exception\RequestFailedException $e) {
			$this->logger->addEmergency('Failed to create network ('.$e->getMessage().'), aborting');
			exit(1);
		}

		// Query tvheadend for the network again, we need its UUID
		try {
			$network = $tvheadend->getNetwork($networkName);

			if ($network === null)
			{
				$this->logger->addEmergency('Network not found after saving, aborting');
				exit(1);
			}
		}
		catch (exception\BaseException $e) {
			$this->logger->addEmergency('Failed to retrieve network list ('.$e->getMessage().'), aborting');
			exit(1);
		}

		// Ask which channels should be imported when in interactive mode
		$selectedChannels = array();

		if ($interactive)
		{
			$stopAsking = false;

			foreach ($channels as $channel)
			{
				if (!$stopAsking)
				{
					$answer = $this->askQuestion('Import channel '.$channel->getName().'?', 'Y/n/all/done', 'Y');

					if ($answer === 'n')
						continue;
					elseif ($answer === 'all')
						$stopAsking = true;
					elseif ($answer === 'done')
						break;

					$selectedChannels[] = $channel;
				}
			}
		}
		else
			$selectedChannels = $channels;

		// Start adding the selected channels as muxes
		foreach ($selectedChannels as $channel)
		{
			$name = $channel->getName();

			$this->logger->addNotice('Creating multiplex for channel '.$name);
			$mux = new model\multiplex\IptvMultiplex($network);
			$mux->iptv_url = $channel->getUrl();
			$mux->iptv_muxname = $name;

			// Workarounds for missing stream data
			$mux->iptv_sname = $channel->getName();
			$mux->epg = 0;

			$tvheadend->createMultiplex($network, $mux);
		}

		$this->logger->addNotice('Done');
	}

}
