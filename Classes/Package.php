<?php
namespace Sandstorm\Heroku;


use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;

/**
 * The Heroku Package
 */
class Package extends BasePackage {

	/**
	 * @param Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(Bootstrap $bootstrap) {
		$databaseUrl = getenv('DATABASE_URL');
		if ($databaseUrl) {
			$databaseConfig = parse_url($databaseUrl);
			define('ENV_DB_NAME', ltrim($databaseConfig['path'],'/'));
			define('ENV_DB_USER', $databaseConfig['user']);
			define('ENV_DB_PASSWORD', $databaseConfig['pass']);
			define('ENV_DB_HOST', $databaseConfig['host']);
			define('ENV_DB_PORT', $databaseConfig['port']);
		}
	}
}
