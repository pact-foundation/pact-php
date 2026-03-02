<?php

use Behat\Config\Config;

return (new Config())
    ->import([
        'compatibility-suite/suites/v1/http/consumer.php',
        'compatibility-suite/suites/v1/http/provider.php',
        'compatibility-suite/suites/v2/http/consumer.php',
        'compatibility-suite/suites/v2/http/provider.php',
        'compatibility-suite/suites/v3/http/consumer.php',
        'compatibility-suite/suites/v3/http/provider.php',
        'compatibility-suite/suites/v3/message/consumer.php',
        'compatibility-suite/suites/v3/message/provider.php',
        'compatibility-suite/suites/v3/generators.php',
        'compatibility-suite/suites/v3/matching-rules.php',
        'compatibility-suite/suites/v4/http/consumer.php',
        'compatibility-suite/suites/v4/http/provider.php',
        'compatibility-suite/suites/v4/message/consumer.php',
        'compatibility-suite/suites/v4/message/provider.php',
        'compatibility-suite/suites/v4/combined.php',
        'compatibility-suite/suites/v4/sync-message/consumer.php',
        'compatibility-suite/suites/v4/matching-rules.php',
        'compatibility-suite/suites/v4/generators.php',
    ]);
