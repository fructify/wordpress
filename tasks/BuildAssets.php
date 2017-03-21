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
            ->debug($opts['debug'])
            ->autoprefix(true)
            ->cachebust(true)
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
            ->debug($opts['debug'])
            ->cachebust(true)
            ->gz(true)
        ->run();
    }
}
