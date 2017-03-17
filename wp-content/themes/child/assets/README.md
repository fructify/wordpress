# Theme Assets
This is where all our JavaScript and Stylesheets (Vanila Css / Less / Sass) will
live. The ```src``` folder contains the original source before it's been compiled,
transpiled, concatened & minified.

The current asset build pipeline setup in _"tasks/BuildAssets.php"_ will output
the final assets into a ```dist``` folder. This has been git ignored. Minified
assets do not play well with git diffs, merges etc.

So your scripted deployments _(your scripting your deployments right)_ will need
to take this into account and build the assets.

## What about, binary assets, like images, etc.
For now add these directly into the ```assets``` folder and commit as usual.
Fructify makes no provisions for automatic image optimatisations,
sprite generation, etc...
