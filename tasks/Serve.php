<?php declare(strict_types=1);
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

namespace Tasks;

use Gears\String\Str;

class Serve extends \Robo\Tasks
{
    /**
     * Starts a new PHP Dev Server.
     *
     * Automatically watches for changes inside `./wp-content`,
     * rebuilds assets and reloads the browser automatically.
     *
     * @param array $opts
     *
     * @option $port A custom port to run the development server on.
     */
    public function serve(array $opts = ['port' => 8080])
    {
        // Ensure we start with a fresh build.
        $this->taskExec('./robo asset:build')->option('debug')->run();

        // Start the live reload web socket server.
        // NOTE: This does not actually serve any PHP files.
        $this->taskExec('./vendor/bin/reload')
            ->arg('server:run')
            ->option('no-watch')
            ->background()
        ->run();

        // Start the PHP server, this actually serves Wordpress for us.
        // NOTE: You could use Apache or Nginx instead if desired.
        $this->taskServer($opts['port'])
            ->host('localhost')
            ->dir(realpath(__DIR__.'/../'))
            ->background()
        ->run();

        // Open a browser pointing to the dev server we just started.
        $this->taskOpenBrowser('http://localhost:'.$opts['port'])->run();

        /*
         * Additionally there is a piece of Middleware in the base fructify
         * theme that will insert the following script tag:
         *
         * <script src="http://localhost:35729/livereload.js"></script>
         *
         * But only when wordpress it's self thinks it is running on a local
         * development environment as per wp-config.php.
         */

        // Watch the source folders for changes and rebuild our assets.
        $this->taskWatch()
            ->monitor('./wp-content', function(\Lurker\Event\FilesystemEvent $e)
            {
                $changedFile = new \SplFileInfo((string)$e->getResource());

                if (Str::s($changedFile->getPath())->startsWith(realpath('./wp-content/themes/child/assets/src/js')))
                {
                    $this->taskExec('./robo asset:js')->option('debug')->run();
                }
                elseif (Str::s($changedFile->getPath())->startsWith(realpath('./wp-content/themes/child/assets/src/scss')))
                {
                    $this->taskExec('./robo asset:css')->option('debug')->run();
                }

                $this->reloadBrowser();
            })
        ->run();
    }

    /**
     * Reloads any browsers connected to our live reload server.
     *
     * This is used instead of having 2 sets of file watchers running.
     * We also don't care what was updated, css or js, we want the browser
     * to reload so it picks up on the renamed assets.
     *
     * @return void
     */
    protected function reloadBrowser()
    {
        $this->yell('Reloading browser!');

        (new \GuzzleHttp\Client)->get
        (
            'http://localhost:35729/changed',
            ['query' => ['files' => '*']]
        );
    }
}
