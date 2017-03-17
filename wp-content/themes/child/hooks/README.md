# Wordpress Hooks, otherwise known as Actions & Filters.
In a normal wordpress theme, your probably used to registering all your
actions and filters in the "functions.php" file. After a while that file can
get rather large and hard to maintain.

In a fructify theme, you create one file that contains a single hook.
Occasionally you might like to group multiple related hooks together.

Hook files operate in the global scope, so you can create a new file and simply
add the exact same code you might have had inside your "functions.php" file and
for most part it should work just like it did.

For example:

```php
function myInit()
{
    // do some foo ...
}

add_action('init', 'myInit');
```

> TIP: Your theme may not require any custom hooks, in which case just delete the folder.

## Annoymous Functions
Ok so if you don't know what an Annoymous Function or a Closure is, it's just a
function without a name. Read more about it here http://php.net/manual/en/functions.anonymous.php

__Hooks Love Annoymous Functions__

Lets rewrite the above as:

```php
add_action('init', function()
{
    // do some foo ...
});
```

## Hooks and Dependency Injection
Sometimes our hooks require some help from other classes or services.
We could write our hook like this:

```php
add_action('init', function()
{
    $foo = new Foo();
    $foo->do('something');
});
```

This is super duper not cool, instead we can wrap our hook in another annoymous
function and return that function. The wrapping annoymous function can define
it's dependencies as parameters and they will get injected for you.

```php
return function(Foo $foo)
{
    add_action('init', function() use($foo)
    {
        $foo->do('something');
    });
};
```

> NOTE: In the above example "Foo" should really be "IFoo".
> If your not sure why please start reading about Dependency Injection
> and Inversion of Control.

## Hierarchical File System
Most of fructify implements a Hierarchical File System.
This means hook files in the child theme with the same name as a hook file
in the parent fructify theme will override that hook file.

## More Examples
If you want to see more examples of hook files,
look in the parent fructify theme hooks folder.
