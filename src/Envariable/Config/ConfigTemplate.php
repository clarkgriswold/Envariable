<?php

return array(

    // An array of environments mapped to detection methods.
    // See the README for further details.
    // @see http://php.net/manual/en/function.gethostname.php
    'environmentToDetectionMethodMap' => array(
        // Examples:
        // 'production' => array(
        //     'hostname' => 'your-production-machine-name', // Using hostname for detection
        // ),
        // 'testing'    => array(
        //     'servername' => 'your-testing-machine-name', // Using servername for detection
        // ),
    ),

    // The default environment with CLI. You may wish to change
    // this when testing on your testing, staging or QA servers
    // (whatever you call it).
    'cliDefaultEnvironment' => 'production',

    // Path to your custom environment config. It would be
    // preferrable to keep these outside of your servers public
    // folder. This likely won't be possible on shared hosting
    // so set to null for root.
    //
    // Envariable figures out the path to the front controller
    // (index.php) and the path you set in here starts from
    // there.
    //
    // Example:
    //
    //     'customEnvironmentConfigPath' => '../' <- Parent directory of application root
    //
    // NOTE: Trailing slash is unnecessary, but you can use it if you wish.
    'customEnvironmentConfigPath' => null,

);
