<?php

return array(

    // Array of all hosts your application will use mapped
    // to their respective environment names.
    'environmentToHostMap' => array(
        // Examples:
        // 'production' => 'example.com',
        // 'testing'    => 'test.example.com',
    ),

    // The default environment with CLI. You may wish to change
    // this when testing on your testing, staging or QA servers
    // (whatever you call it).
    'cliDefaultHost' => 'production',

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
