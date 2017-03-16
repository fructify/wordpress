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

// This is an example of a Robo Task Class, feel free to create others like it
// in the tasks folder. Here we have setup my "gears/asset" Robo Task to build
// some css and js assets. You may choose to extend this and make it work for
// your theme or trash this. No hard feelings, every site is different.

namespace Tasks;

class BuildAssets extends \Robo\Tasks
{
    use \Gears\Asset\loadTasks;

    /**
     * Deletes all files inside the `assets/dist` folder.
     */
    public function assetClean()
    {
        $this->taskCleanDir(['./wp-content/themes/child/assets/dist'])->run();
    }

    /**
     * Builds the both js & css assets for the child theme.
     *
     * @param array $opts
     *
     * @option $debug If this flag is set then minifcation will be skipped.
     */
    public function assetBuild(array $opts = ['debug' => false])
    {
        $this->assetClean();
        $this->assetCss($opts);
        $this->assetJs($opts);
    }

    /**
     * Builds the primary css asset for the child theme.
     *
     * @param array $opts
     *
     * @option $debug If this flag is set then minifcation will be skipped.
     */
    public function assetCss(array $opts = ['debug' => false])
    {
        $this->taskBuildAsset('./wp-content/themes/child/assets/dist/styles.css')
            ->source('./wp-content/themes/child/assets/src/scss/styles.scss')
            ->template('./wp-content/themes/child/views/layouts/master.php')
            ->debug($opts['debug'])
            ->autoprefix(true)
            ->gz(true)
        ->run();
    }

    /**
     * Builds the primary javascript asset for the child theme.
     *
     * @param array $opts
     *
     * @option $debug If this flag is set then minifcation will be skipped.
     */
    public function assetJs(array $opts = ['debug' => false])
    {
        $this->taskBuildAsset('./wp-content/themes/child/assets/dist/script.js')
            ->source
            ([
                './vendor/bower/jquery/dist/jquery.js',
                './vendor/bower/bootstrap-sass/assets/javascripts/bootstrap.js',
                './wp-content/themes/child/assets/src/js'
            ])
            ->template('./wp-content/themes/child/views/layouts/master.php')
            ->debug($opts['debug'])
            ->gz(true)
        ->run();
    }

    /**
     * Watches the source asset files for changes and rebuilds automatically.
     */
    public function assetWatch()
    {
        // We don't need to minify our assets when in development.
        $opts = ['debug' => true];

        // Ensure we start with a fresh build.
        $this->assetBuild($opts);

        // Start the live reload web socket server.
        // NOTE: This does not actually serve any PHP files.
        // You will still need Apache or Nginx to actully serve this project.
        $this->taskExec('./vendor/bin/reload')
            ->arg('server:run')
            ->option('no-watch')
            ->background()
        ->run();

        /*
         * Additionally there is a piece of Middleware in the base fructify
         * theme that will insert the following script tag:
         *
         * <script src="http://localhost:35729/livereload.js"></script>
         *
         * But only when wordpress it's self thinks it is running on a local
         * development environment as per wp-config.php.
         */

        $monitor = function(string $assetType) use($opts)
        {
            return function() use($assetType, $opts)
            {
                $assetType = 'asset'.ucfirst($assetType);
                $this->{$assetType}($opts);
                $this->reloadBrowser();
            };
        };

        // Watch the source folders for changes and rebuild our assets.
        $this->taskWatch()
            ->monitor('./wp-content/themes/child/assets/src/scss', $monitor('css'))
            ->monitor('./wp-content/themes/child/assets/src/js', $monitor('js'))
        ->run();
    }

    /**
     * Reloads any browsers connected to our live reload server.
     *
     * This used instead of having 2 sets of file watchers running.
     * We also don't care what was updated, css or js, we want the browser
     * to reload so it picks up on the renamed assets.
     *
     * @return void
     */
    protected function reloadBrowser()
    {
        (new \GuzzleHttp\Client)->get
        (
            'http://localhost:35729/changed',
            ['query' => ['files' => '*']]
        );
    }
}
