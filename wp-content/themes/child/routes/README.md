# The HTTP Router
The final piece of middleware in the parent fructify theme, hands the request
and response objects to the http://route.thephpleague.com/. Similar to hooks &
middleware. Routes are defined in a route file.

> TIP: Your theme may not require any custom routes, in which case just delete the folder.

## What is a route file?
Each route file is included in such a way that the only variable availiable is: ```$route```.
This is an instance of ```League\Route\RouteCollectionInterface```.

So a basic route file might look like:

```php
$route->get('/foo/bar', function()
{
    return 'foo bar';
});
```

### Filenaming and ordering.
Again because the order of the routes is important, the order is
manipulated by the filename, in the same way as middleware.

> TIP: Completely up to you but the filename format fructify uses
> looks like this: ```123-a-description-or-hyphenated-path.HTTP_METHOD.php```

### Dependency Injection
There are 2 places we can have services injected. The first looks like this:

```php
$route->get('/foo/bar', function(IView $view)
{
    return $view->render('foo/bar');
});
```

The second option is to use a wrapping annoymous function similar to hooks:

```php
return function(IFoo $foo) use ($route)
{
    $route->get('/foo/bar', function(IView $view) use ($foo)
    {
        return $view->render($foo->viewName);
    });
};
```

This is handy when you want to register many routes at once,
using some sort of automatic registeration logic.

> TIP: You could inject the route collection instead of "using" $route.

## Fructify Default Route
Out of the box the parent fructify theme automatically registers routes for all
views inside of ```views/pages```. The routes are based on the view filename.
This is so that static pages can easily be created without the need to create a
wordpress page in wp-admin, nor do you have to create a custom route file that
just returns a view.

It also registers routes for all Wordpress pages stored in the database,
based on the permalink. If a matching page view exists it will be used otherwise
it will be rendered using a the default view.

## More Examples
If you want to see more examples of route files,
look in the parent fructify theme routes folder.
