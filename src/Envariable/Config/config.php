<?php

return array(

    // An array of environments mapped to their respective
    // machine names (hostnames). See the README for further details.
    // @see http://php.net/manual/en/function.gethostname.php
    'environmentToHostnameMap' => array(
        // Examples:
        // 'production' => 'your-production-machine-name',
        // 'testing'    => 'your-testing-machine-name',
        // 'local'      => 'YourName-MacBook-Pro.local',
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

)
