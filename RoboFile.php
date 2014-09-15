<?php

// Set the timezone
// Sometimes scripts and php can be picky
date_default_timezone_set ('Australia/Melbourne');

// Include composer here just in case we are called
// with a globally installed version of robo.
require_once('vendor/autoload.php');

class RoboFile extends \Robo\Tasks
{
	/**
	 * Method: wpInstall
	 * =========================================================================
	 * This task will download the core wordpress files for you.
	 * It is automatically run by composer as a post-install-cmd so you
	 * really shouldn't need to worry about it.
	 * 
	 * But if you do want to call it, usage would look like:
	 * 
	 *     php vendor/bin/robo wp:install v4.*
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $version - This is optional, defaults to the latest version of wordpress.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function wpInstall($version = '*')
	{
		// Lets check if wordpress actually exists
		if (!file_exists('wp-includes/version.php'))
		{
			// Grab the resolved version number
			$version = $this->wpResolveVersionNo($version);

			// Download the core wordpress files
			$this->taskExec('vendor/bin/wp core download --version='.$version)->run();

			// Remove a few things we don't need
			@unlink('license.txt');
			@unlink('readme.html');
			@unlink('wp-config-sample.php');
			@unlink('./wp-content/plugins/hello.php');
		}
	}

	/**
	 * Method: wpUpdate
	 * =========================================================================
	 * This task will update the core wordpress files for you.
	 * It is automatically run by composer as a post-update-cmd so you
	 * really shouldn't need to worry about it.
	 * 
	 * But if you do want to call it, usage would look like:
	 * 
	 *     php vendor/bin/robo wp:update v4.*
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $version - This is optional, defaults to the latest version of wordpress.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function wpUpdate($version = '*')
	{
		// Lets attempt to update wordpress
		if (file_exists('wp-includes/version.php'))
		{
			// Grab the version of wordpress that is installed
			require('wp-includes/version.php');
			$installed_version = $wp_version;

			// Get the version we want to update to
			$new_version = $this->wpResolveVersionNo($version);

			// Nothing to do, same version.
			if ($installed_version == $new_version) return;

			// Now lets download the version of wordpress that we already have,
			// sounds silly I know but it will make sense soon I promise.
			$old_wp_tmp_path = sys_get_temp_dir().'/'.md5(microtime());
			$this->taskExec('mkdir '.$old_wp_tmp_path)->run();
			$this->taskExec('vendor/bin/wp core download --version='.$installed_version.' --path='.$old_wp_tmp_path)->run();

			// Now lets delete all the files that are stock wordpress files
			$finder = new Symfony\Component\Finder\Finder();
			$finder->files()->in($old_wp_tmp_path);
			foreach ($finder as $file)
			{
				if (file_exists($file->getRelativePathname()))
				{
					unlink($file->getRelativePathname());
				}
			}

			// Delete the wp-includes and wp-admin folders
			// If you have made modifications here, its time to refactor :)
			$this->taskDeleteDir('./wp-admin')->run();
			$this->taskDeleteDir('./wp-includes')->run();

			// Clean up
			$this->taskDeleteDir($old_wp_tmp_path)->run();
		}

		// Either we just deleted the old wordpress files or it didn't exist.
		// Regardless lets run the install functionality.
		$this->wpInstall($version);
	}

	/**
	 * Method: wpSalts
	 * =========================================================================
	 * This task will create a new set of salts and write them to the
	 * .salts.php file for you. Again this tied into composer as a
	 * post-install-cmd so you really shouldn't need to worry about it.
	 * 
	 * But if you do want to call it, usage would look like:
	 * 
	 *     php vendor/bin/robo wp:salts
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function wpSalts()
	{
		// Grab the salts from the wordpress server
		$salts = GuzzleHttp\get('https://api.wordpress.org/secret-key/1.1/salt/')->getBody();

		// Create the new .salts.php file
		$this->taskWriteToFile('.salts.php')->line('<?php')->text($salts)->run();
	}

	/**
	 * Method: permissionsSet
	 * =========================================================================
	 * This task simply loops through some folders and ensures they exist and
	 * have the correct permissions. It is automatically run by composer as a
	 * post-install-cmd so you really shouldn't need to worry about it.
	 * 
	 * But if you do want to call it, usage would look like:
	 * 
	 *     php vendor/bin/robo permissions:set
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function permissionsSet()
	{
		// These folders will be give full write permissions
		$folders =
		[
			// Wordpress likes to have write access to the entire wp-content
			// folder. I don't like this. And will only provide access to
			// the uploads folder.
			'./wp-content/uploads',

			// This is for AssetMini
			'./assets/cache'

			// Feel free to add your own folder here...
		];

		// Loop through each folder
		foreach ($folders as $folder)
		{
			$this->taskFileSystemStack()
				->mkdir($folder)
				->chmod($folder, 0777)
			->run();
		}
	}

	/**
	 * Method: wpResolveVersionNo
	 * =========================================================================
	 * This is a private helper method. It takes a version string with
	 * wild card characters (*) and resolves the actual version number.
	 * We use this page: http://wordpress.org/download/release-archive/
	 * As an offical list of released versions.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $version_string - A string with a version like: v1.5.*
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	private function wpResolveVersionNo($version_string)
	{
		// Remove a v at the start if it exists
		$version_string = str_replace('v', '', $version_string);

		// Make sure the wordpress version string
		// contains wildcards but is not a single wildcard
		if ($version_string != '*' && strpos($version_string, '*') !== false)
		{
			// Download a list of all the wordpress versions
			$html = GuzzleHttp\get('http://wordpress.org/download/release-archive/')->getBody();

			// Extract the version numbers
			preg_match_all("#><a href='http://wordpress.org/wordpress-[^>]+#", $html, $matches);
			
			foreach ($matches[0] as $match)
			{
				if (strpos($match, '.zip') !== false)
				{
					$result = str_replace(["><a href='http://wordpress.org/wordpress-", ".zip'"], '', $match);
					
					// We don't want any of the alpha, beta or rc releases
					if (strpos($result, '-') === false)
					{
						// Now search for the latest version number
						$pattern = preg_quote($version_string, '#');
						$pattern = str_replace('\*', '.*', $pattern).'\z';

						if (preg_match('#^'.$pattern.'#', $result))
						{
							$version = $result;
						}
					}
				}
			}

			// Return the last version we found
			return $version;
		}
		elseif ($version_string == '*')
		{
			// Get the latest version
			return GuzzleHttp\get('http://api.wordpress.org/core/version-check/1.7/')->json()['offers'][0]['version'];
		}
		else
		{
			// No wildcards so assume the version is accurate
			return $version_string;
		}
	}
}