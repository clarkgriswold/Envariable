# README #

Envariable
----------

##### A better way to keep environment variables outside of your project -- and outside of version control -- while keeping them available to the CLI (e.g., cron jobs).


##### Why does this exist?

I created Envariable because I have an old CodeIgniter project that I still have to maintain and I've never been too keen on how CI deals with environments. Also, I used this as an excuse to learn how to create composer packages, something I've never done before prior to this.

Currently Envariable only supports CodeIgniter, but I built it in such a way that it can be easily adapted to other frameworks or projects should the need arise.

Ok, so basic documentation until I get the urge to write this up a little bit better.


##### INSTALLATION:

These instructions will assume a CodeIgniter setup for the time being.

Currently this is not published on Packagist (not sure if I will at the moment) so you will need to first add this to your composer.json:

    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:clarkgriswold/Envariable.git"
        }
    ],

After that you'll have to add this:

    "require": {
        "clarkgriswold/envariable": "dev-master"
    }


Run composer install.

Within your front controller (index.php) be sure to require the composer autoloader:

    include_once './vendor/autoload.php';

Then just below that add:

    $envariable = new Envariable\Envariable();
    $envariable->execute();

Then, where CodeIgniter defines the ENVIRONMENT constant, change that to this:

    define('ENVIRONMENT', $envariable->getEnvironment()->getDetectedEnvironment());


Upon reloading the site you will now see an exception being thrown. Obviously you will only see the exception if you have error reporting enabled.

Envariable has placed a config file that you need to modify to your needs within CodeIgniter's application config folder application/config/Envariable/config.php.

... ah poop. Got interrupted by a phone call and couldn't finish cuz now it's bed time. I'll get back to ya...

