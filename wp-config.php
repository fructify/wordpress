<?php
////////////////////////////////////////////////////////////////////////////////
//             ___________                     __   __  _____                   
//             \_   _____/______ __ __   _____/  |_|__|/ ____\__ __             
//              |    __) \_  __ \  |  \_/ ___\   __\  \   __<   |  |            
//              |     \   |  | \/  |  /\  \___|  | |  ||  |  \___  |            
//              \___  /   |__|  |____/  \___  >__| |__||__|  / ____|            
//                  \/                      \/               \/                 
// -----------------------------------------------------------------------------
//                          https://github.com/fructify                         
//                                                                              
//          Designed and Developed by Brad Jones <brad @="bjc.id.au" />         
// -----------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////

/**
 * Section: The base dir for wordpress
 * =============================================================================
 * WordPress needs to know where it is installed. Normally one of the last
 * things that get defined in the standard wp-config.php file. However we have
 * moved it up the chain a little bit. So that we can use it ourselves.
 */

if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__).'/');

/**
 * Section: Include the Composer Autoloader
 * =============================================================================
 * Now this is the whole point of this project, to get composer loaded into
 * the wordpress environment. We assume the vendors dir will be located in the 
 * root of the project.
 */

require(ABSPATH.'vendor/autoload.php');

/**
 * Section: Environment Variables
 * =============================================================================
 * Right so this is super simple way to get environment variables loaded.
 * Most WordPress sites are hosted on shared servers and we can't easily add
 * real environment variables. Inspired by https://github.com/vlucas/phpdotenv
 * But simplified heaps, because we are using actual PHP arrays and not trying
 * to parse a text file the performance hit should be negligible.
 * 
 * **IT GOES WITHOUT SAYING - BUT I'LL SAY IT ANYWAY - DO NOT COMMIT .env.php**
 */

if (file_exists(ABSPATH.'.env.php'))
{
	foreach (require(ABSPATH.'.env.php') as $key => $value)
	{
		if (getenv($key) === false)
		{
			putenv("$key=$value");
			$_ENV[$key] = $value;
			$_SERVER[$key] = $value;
		}
	}
}

/**
 * Section: Environment Specific Configuration
 * =============================================================================
 * Here we define our database connection details and any other environment
 * specific configuration. We use some simple environment detection so that
 * we can easily define different values regardless of where we run.
 */

call_user_func(function()
{
	// This is where the magic happens
	$env = function($host)
	{
		// Do we have a direct match with the hostname of the OS / webserver
		// NOTE: The HTTP_HOST can be spoofed, remove if super paranoid.
		if ($host == gethostname() || $host == @$_SERVER['HTTP_HOST'])
			return true;

		// This next bit is stolen from Laravel's str_is helper
		$pattern = '#^'.str_replace('\*', '.*', preg_quote($host, '#')).'\z#';
		if ((bool) preg_match($pattern, gethostname())) return true;
		if ((bool) preg_match($pattern, @$_SERVER['HTTP_HOST'])) return true;

		// No match
		return false;
	};

	// Here you can define as many `cases` or environments as you like.
	// Here are the usual 3 for starters.
	switch(true)
	{
		// Local
		case $env('*dev*'):
		case $env('*local*'):
		{
			define('FRUCTIFY_ENV', 'local');
			define('DB_NAME', 'wordpress');
			define('DB_USER', 'root');
			define('DB_PASSWORD', '');
			define('DB_HOST', 'localhost');
			define('WP_DEBUG', true);
			break;
		}

		// Staging
		case $env('stag*'):
		{
			define('FRUCTIFY_ENV', 'staging');
			define('DB_NAME', $_ENV['DB_NAME']);
			define('DB_USER', $_ENV['DB_USER']);
			define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
			define('DB_HOST', $_ENV['DB_HOST']);
			define('WP_DEBUG', false);
			break;
		}

		// Production
		default:
		{
			define('FRUCTIFY_ENV', 'production');
			define('DB_NAME', $_ENV['DB_NAME']);
			define('DB_USER', $_ENV['DB_USER']);
			define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
			define('DB_HOST', $_ENV['DB_HOST']);
			define('WP_DEBUG', false);
		}
	}
});

/**
 * Section: Database Charset and Collate
 * =============================================================================
 * If this is different across your environments I think you have some issues...
 * Hence I have defined them outside of the above section.
 */

define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');

/**
 * Section: WordPress Database Table prefix.
 * =============================================================================
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */

$table_prefix  = 'wp_';

/**
 * Section: Authentication Unique Keys and Salts.
 * =============================================================================
 * Salts should never be committed to git, and because we commit this file
 * we need to refrence a non tracked file that contains the salts.
 * 
 * The file '.salts.php' should get automatically created for
 * you when you create a new project using:
 * 
 *     composer create-project brads/wordpress my-site
 * 
 * If not you can run the command:
 * 
 *     ./vendor/bin/robo wp:salts
 * 
 * Or alternatively do it yourself by going to:
 * 
 *     https://api.wordpress.org/secret-key/1.1/salt/
 */

require('.salts.php');

/**
 * Section: Disable Auto Updates
 * =============================================================================
 * As we are now managing the version of wordpress with composer we disable the
 * automatic updater. If you feel strongly otherwise feel free to re-enable it
 * by setting the following to false.
 */

define('AUTOMATIC_UPDATER_DISABLED', true);

/**
 * Section: Bootstrap WordPress
 * =============================================================================
 * That's all, stop editing! Happy blogging.
 */

require_once(ABSPATH.'wp-settings.php');