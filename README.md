Brads Wordpress
================================================================================
**Wordpress + Composer + Robo Task Runner + WP-CLI + AssetMini + awesome stuff**

So this is my take on trying to get wordpress into the composer environment.
There are many other projects which also do this. I feel like until Wordpress
actually give us something built-in we are going to continue to see these sorts
of setups. 

*Checkout these for other ideas:*

  - http://roots.io/wordpress-stack/
  - https://github.com/fancyguy/webroot-installer
  - https://github.com/johnpbloch/wordpress-project

How do I use this:
--------------------------------------------------------------------------------
It's easy just run:

```
composer create-project brads/wordpress my-new-site
```

*You now have a wordpress project managed with composer.*

What do I get:
--------------------------------------------------------------------------------
So after you have created your project you will have the following:

  - A fresh copy of wordpress.

  - All wordpress plugins are managed by composer now.
    For more info see: http://wpackagist.org/

  - WP-CLI has been installed into ./vendors/bin/wp
    Checkout: http://wp-cli.org/

  - The Robo Task Runner is also installed into ./vendors/bin/robo
    Checkout: http://robo.li/

  - AssetMini, for auto-magically minifying your css and js.
    See: https://github.com/phpgearbox/assetmini

  - You get a .gitignore file that should ignore all the standard wordpress
    files except for a theme called "default", you can change this.

  - A seriously awesome environment aware wp-config.php file.

  - Finally we include a slightly modified version of the .htaccess file from
    http://html5boilerplate.com/ Its got the wordpress rewrite rules at the
    bottom.

Why did I create this?
--------------------------------------------------------------------------------
The issue I have found with the other approaches, is that they all modify
wordpress in some way or another. Wordpress gets put inside another directory.
Then we have to define extra index.php files and so on. The best example of this
is the Roots Bedrock Stack. It hardly looks anything like the original project.

Now for me as a seriously hardcore backend dev *pats back :)* I would love to
use something like the Roots project with Vagrant and other awesome server side
tech. Well in actual fact I would just throw out wordpress altogether.

The issue I can foresee though is that my other colleagues which perhaps aren't
so technically inclined (not for a second am I suggesting your dumb, you just
have other talents which are different than mine, hell I can't style a bootstrap
theme to save my life) won't have a clue where everything has gone and what
happened to wordpress??? Composer needs to be easy otherwise others wont use it.

Hence I built this project.

Making Contributions
--------------------------------------------------------------------------------
This project is first and foremost a tool to help me create awesome websites.
Thus naturally I am going to tailor for my use. I am just one of those really
kind people that have decided to share my code so I feel warm and fuzzy inside.
Thats what Open Source is all about, right :)

If you feel like you have some awesome new feature, or have found a bug I have
overlooked I would be more than happy to hear from you. Simply create a new
issue on the github project and optionally send me a pull request.

--------------------------------------------------------------------------------
Developed by Brad Jones - brad@bjc.id.au