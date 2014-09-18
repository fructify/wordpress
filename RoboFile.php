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

/*
 * Include our local composer autoloader just in case
 * we are called with a globally installed version of robo.
 */
require_once('vendor/autoload.php');

class RoboFile extends Robo\Tasks
{
	// Include our Fructify Tasks
	use Fructify\Robo\Tasks;

	/*
	 * Feel free to add your own tasks here.
	 * For more info on the robo task runner,
	 * see: http://robo.li/
	 */
}