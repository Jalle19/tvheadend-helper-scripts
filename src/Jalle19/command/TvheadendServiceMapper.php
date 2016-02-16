<?php

namespace Jalle19\command;

use Jalle19\tvheadend\cli\TvheadendCommand;
use Jalle19\tvheadend\model\ServiceMapperRequest;
use Jalle19\tvheadend\Tvheadend;

class TvheadendServiceMapper extends TvheadendCommand
{

	/**
	 * @inheritdoc
	 */
	public function __construct()
	{
		parent::__construct();
		$command = $this->command;

		$command->option('include-encrypted')
		        ->boolean()
		        ->default(false)
		        ->describe('Whether to include encrypted services (defaults to false)');

		$command->option('check-availability')
		        ->boolean()
		        ->default(false)
		        ->describe('Whether to check service availability (defaults to false)');

		$this->run();
	}


	/**
	 * Runs the command
	 */
	private function run()
	{
		// Parse options
		$tvheadendHostname = $this->command->getFlagValues()['tvheadend-hostname'];
		$tvheadendPort     = $this->command->getFlagValues()['tvheadend-http-port'];
		$tvheadendUsername = $this->command->getFlagValues()['tvheadend-username'];
		$tvheadendPassword = $this->command->getFlagValues()['tvheadend-password'];
		$includeEncrypted  = $this->command->getFlagValues()['include-encrypted'];
		$checkAvailability = $this->command->getFlagValues()['check-availability'];

		// Create a tvheadend instance
		$this->logger->addNotice('Connecting to tvheadend at ' . $tvheadendHostname . ':' . $tvheadendPort);
		$tvheadend = new Tvheadend($tvheadendHostname, $tvheadendPort, $tvheadendUsername, $tvheadendPassword);

		// Create a service mapper request
		$serviceMapperRequest                     = new ServiceMapperRequest();
		$serviceMapperRequest->encrypted          = $includeEncrypted;
		$serviceMapperRequest->check_availability = $checkAvailability;

		// Add services to the request
		$services = $tvheadend->getServices();
		$serviceMapperRequest->setServices($services);

		// Map the services
		try
		{
			$tvheadend->mapServices($serviceMapperRequest);

			// Log which services where mapped
			foreach ($services as $service)
			{
				if (!$includeEncrypted && $service->encrypted)
					$this->logger->info('Skipping encrypted service ' . $service->svcname);
				else
					$this->logger->info('Successfully mapped service ' . $service->svcname);
			}
		}
		catch (\Exception $e)
		{
			$this->logger->error('Failed to map services: ' . $e->getMessage());
		}

		$this->logger->info('Done');
	}

}
