<?php

// Include composer first up, so we have it available at all times
require('vendor/autoload.php');

/**
 * Section: The base dir for wordpress
 * =============================================================================
 * In case you need to use in any of the config below we define it
 * here instead of all the way at the bottom of the file.
 */

if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__).'/');

/**
 * Section: Environment Specific Configuration
 * =============================================================================
 * Here we define our database connection details and any other environment
 * specific configuration. We use some simple environment detection so that
 * we can easily define different values regardless of where we run.
 */

// Do we have a local .env.php file
if (file_exists('.env.php'))
{
	foreach (require('.env.php') as $key => $value)
	{
		if (getenv($key) === false)
		{
			putenv("$key=$value");
			$_ENV[$key] = $value;
			$_SERVER[$key] = $value;
		}
	}
}

// Wrap this up in a closure so we don't pollute the global name space.
// One laughs.., considering what WordPress is about to do but oh well.
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
		case $env('my-local-pc-hostname'):
		case $env('*dev*'):
		case $env('*local*'):
		{
			define('WP_ENV', 'local');
			define('DB_NAME', 'wordpress');
			define('DB_USER', 'root');
			define('DB_PASSWORD', '');
			define('DB_HOST', 'localhost');
			break;
		}

		// Staging
		case $env('stag*'):
		{
			define('WP_ENV', 'staging');
			define('DB_NAME', $_ENV['DB_NAME']);
			define('DB_USER', $_ENV['DB_USER']);
			define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
			define('DB_HOST', $_ENV['DB_HOST']);
			break;
		}

		// Production
		default:
		{
			define('WP_ENV', 'production');
			define('DB_NAME', $_ENV['DB_NAME']);
			define('DB_USER', $_ENV['DB_USER']);
			define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
			define('DB_HOST', $_ENV['DB_HOST']);
		}
	}
});

/**
 * Section: Database Charset and Collate
 * =============================================================================
 * If this is different across your environments I think you have some issues...
 * I quote markjaquith "You almost certainly do not want to change these"
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
 * Section: Uploads .htaccess
 * =============================================================================
 * When you are working locally or on the staging environment. Or any other
 * environment that is not production. It is always really annoying when you
 * are missing media assets from the uploads folder. This fixes that for us.
 */

// define('ASSETS_URL', 'http://example.com/wp-content/uploads/');

call_user_func(function()
{
	if (WP_ENV != 'production' && defined('ASSETS_URL'))
	{
		// The uploads path
		$uploads_path = ABSPATH.'/wp-content/uploads';

		// The htaccess contents
		$htaccess =
			'<IfModule mod_rewrite.c>'."\n".
			"\t".'RewriteEngine On'."\n".
			"\t".'RewriteBase /wp-content/uploads/'."\n".
			"\t".'RewriteCond %{REQUEST_FILENAME} !-f'."\n".
			"\t".'RewriteCond %{REQUEST_FILENAME} !-d'."\n".
			"\t".'RewriteRule ^(.*) '.ASSETS_URL.'$1 [L,P]'."\n".
			'</IfModule>'
		;

		// Does the uploads dir exist?
		if (!is_dir($uploads_path))
		{
			// Lets attempt to create it
			if (!@mkdir($uploads_path))
			{
				// Fail silently, when they login to wordpress they will soon
				// get a message telling them the uploads dir is not writable.
				return;
			}
		}

		// Attempt to write to the .htaccess file
		// If we can create it awesome, if not im not too worried.
		@file_put_contents($uploads_path.'/.htaccess', $htaccess);
	}
	elseif (file_exists($uploads_path.'/.htaccess'))
	{
		// We should remove the file if it has been
		// uploaded by acident to production for example.
		@unlink($uploads_path.'/.htaccess');
	}
});

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
 * Section: WordPress debugging mode.
 * =============================================================================
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */

define('WP_DEBUG', false);

/**
 * Section: Bootstrap WordPress
 * =============================================================================
 * That's all, stop editing! Happy blogging.
 */

require_once(ABSPATH . 'wp-settings.php');