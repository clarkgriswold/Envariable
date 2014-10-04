# README #

Envariable
----------

##### A better way to keep environment variables outside of your project -- and outside of version control -- while keeping them available to the CLI (e.g., cron jobs).


##### Why does this exist?

I created Envariable because I have an old CodeIgniter project that I still have to maintain and I've never been too keen on how CI deals with environments. In seeing how Laravel handles this with .env files, I set out to flagrantly steal that idea and use it for my own needs (falling short of a complete rewrite using Laravel or even Symfony which, believe me, I would love to do). Also, I used this as an excuse to learn how to create composer packages. Something I've never done prior to this.

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

    include_once __DIR__ . '/vendor/autoload.php';

Then, just below that add:

    $envariable = new Envariable\Envariable();
    $envariable->execute();

Then, where CodeIgniter defines the ENVIRONMENT constant, change that to this:

    define('ENVIRONMENT', $envariable->getEnvironment()->getDetectedEnvironment());


Upon reloading the site you will now see an exception being thrown. Obviously you will only see the exception if you have error reporting enabled.

Envariable has placed a config file that you need to modify to your needs within CodeIgniter's application config folder application/config/Envariable/config.php.

Within the Envariable config file you will see three items:

    • environmentToDetectionMethodMap
    • cliDefaultEnvironment
    • customEnvironmentConfigPath

environmentToDetectionMethodMap is the main one we're concerned with here. There are two types of detection methods used: hostname and servername. You can use either one or both depending on your situation. Idealy you would use only hostname as hostname is the name of the machine that your app is running on and is not at all spoofable. But, in the situation where your app is on shared hosting and is load balanced across multiple servers, hostname will no longer suffice. In this case you'll want to stick with servername. You can use this to map just your subdomain if you'd like. A third scenario is where your hostname is stable and is safe to use, but you have multiple subdomains which all use separate database connections (as an example). In this case you can use both the hostname and the servername together. See below examples:

    // Hostname example
    'environmentToDetectionMethodMap' => array(
        'production' => array(
            'hostname' => 'production-machine-name',
        ),
        'testing' => array(
            'hostname' => 'testing-machine-name',
        ),
    )

    // Servername example
    'environmentToDetectionMethodMap' => array(
        'production' => array(
            'servername' => 'www.example.com',
        ),
        'testing' => array(
            'servername' => 'testing.example.com',
        ),

        // or...
        'whatever' => array(
            'servername' => 'whatever.com',
        ),
        'something' => array(
            'servername' => 'something.com',
        ),
    )

    // Hostname and Servername example
    'environmentToDetectionMethodMap' => array(
        'production' => array(
            'hostname'   => 'some-machine-name',
            'servername' => 'www',
        ),
        'testing' => array(
            'hostname'   => 'some-machine-name',
            'servername' => 'testing',
        ),
    )

The main thing to note here is that keys of each element within the environmentToDetectionMethodMap will be the name of the .env file that Envariable will be looking for. So, for example:

    // .env.production.php
    'production' => array(
        'servername' => 'www.example.com',
    ),

    // .env.testing.php
    'testing' => array(
        'servername' => 'testing.example.com',
    ),

    // .env.whatever.php
    'whatever' => array(
        'servername' => 'whatever.com',
    ),


