<?php

/**
 * File: .env.example.php
 * =============================================================================
 * This is an example of what a real .env.php file might look like.
 * I know all this new config arrangement seems overkill however for large
 * projects the extra setup will pay off I promise.
 * 
 * If you really can't be bothered with the environment variable configuration.
 * And you have your code committed into a private repo or some other setup
 * that keeps this information safe enough for your purposes. Then by all means
 * feel free to throw this file and the rest of the .env.php code in the bin.
 * I am not forcing this on you.
 * 
 * However a few notes for those that do want to use this setup:
 * 
 *   1. Ensure that a real .env.php never gets committed to your vcs.
 * 
 *   2. If it does see this page, it should help you out:
 *      https://help.github.com/articles/remove-sensitive-data
 * 
 *   3. Ensure that .env.php can not be accessed via HTTP. This is why the
 *      filename starts with a *dot*. Most web servers will forbid access to
 *      *dot* files by default.
 *      
 *      **HOWEVER DO NOT RELY ON THIS PLEASE TEST WITH YOUR OWN BROWSER**
 */

return
[
	'DB_NAME' => '',
	'DB_USER' => '',
	'DB_PASSWORD' => '',
	'DB_HOST' => ''
];