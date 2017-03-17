# The HTTP Middleware Layer
Fructify hijacks the normal wordpress workflow from
[wp_loaded](https://developer.wordpress.org/reference/hooks/wp_loaded/) onwards.
It replaces it with a [PSR-7](http://www.php-fig.org/psr/psr-7/) Middleware layer,
implemented with [RelayPHP](http://relayphp.com/).

> TIP: Your theme may not require any custom middleware, in which case just delete the folder.

## What does middleware look like in fructify?
Similar to hooks, each piece of middleware is defined in a single middleware file.
Each file must return a PHP callable that has the following signature:

```php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

return function (
    Request $request,   // the request
    Response $response, // the response
    callable $next      // the next middleware
) {
    // ...
};
```

### Filenaming and ordering.
Because the order that each piece middleware is executed in is super important,
the filename is used to sort all middleware. For example the parent fructify
theme uses the format: ```123-FooBar.php```.

> NOTE: Middleware files in the child theme with the same name as a middleware
> file in the parent fructify theme will override that piece of middleware.

### Dependency Injection
Middleware functions may have additional parameters after the "$next" parameter
that act as DI hints. For example:

```php
return function(Request $request, Response $response, callable $next, IFoo $foo)
{
    $foo->do('something');
};
```

## Off the Shelf Middleware
There is lots of portable middleware that has already been written.
Efforts such as: https://github.com/oscarotero/psr7-middlewares
Fructify is already using some of the middleware from here so you
don't need to add any further composer packages.

## More Examples
If you want to see more examples of middleware files,
look in the parent fructify theme middleware folder.
