# Troubleshooting

## Can not find/load FFI library

This is because you install Pact-PHP in one environment, then share/mount the project to other environment and use it, like virtual machine or container.

### Failed loading pact.so/pact.dll/pact.dylib

```
[FFI\Exception] Failed loading 'vendor/pact-php/bin/pact-ffi-lib/pact.so'
```

* Reason: For example, install on Windows, but use on Linux virtual machine/container
* Solution: On virtual machine or container - Simply run `composer install` or `composer update`

### Invalid ELF header

```
FFI\Exception: Failed loading 'vendor/pact-php/bin/pact-ffi-lib/pact.so' (/lib/x86_64-linux-gnu/libc.so: invalid ELF header)
```

* Reason: For example, install on Ubuntu (doesn't has musl), but use on Alpine (has musl) virtual machine/container
* Solution: On virtual machine or container - First, force remove the file with `rm vendor/pact-php/bin/pact-ffi-lib/pact.so`, then install again with `composer install` or `composer update`


## Output Logging

There are several ways to print the logs:

### Logger Singleton Instance

You can run these code (once) before running tests:

```php
use PhpPact\Log\Logger;
use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\Model\File;
use PhpPact\Log\Model\Buffer;
use PhpPact\Log\Model\Stdout;
use PhpPact\Log\Model\Stderr;

$logger = Logger::instance();
$logger->attach(new File('/path/to/file', LogLevel::DEBUG));
$logger->attach(new Buffer(LogLevel::ERROR));
$logger->attach(new Stdout(LogLevel::WARN));
$logger->attach(new Stderr(LogLevel::INFO));
$logger->apply();
```

* Pros
    * Flexible, can be used in any test framework
    * Support plugins (e.g. csv, gRPC)
    * Support multiple sinks
* Cons
    * Need to modify the code (once, before the tests)

### PHPUnit Extension

You can put these elements to PHPUnit's configuration file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php" colors="true">
    ...
    <php>
        <env name="PACT_LOG" value="./log/pact.txt"/>
        <env name="PACT_LOGLEVEL" value="DEBUG"/>
    </php>
    <extensions>
        <bootstrap class="PhpPact\Log\PHPUnit\PactLoggingExtension"/>
    </extensions>
</phpunit>
```

* Pros
    * Support plugins (e.g. csv, gRPC)
    * No need to modify the code
* Cons
    * Support only single sink (stdout or file, depend on the value of the environment variables)
    * Only for PHPUnit

### Config

Consumer:

```php
use PhpPact\Standalone\MockService\MockServerConfig;

$config = new VerifierConfig();
$config->setLogLevel('DEBUG');
```

Provider:

```php
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;

$config = new VerifierConfig();
$config->setLogLevel('DEBUG');
```

* Pros
    * Simple
    * Do not support plugins (e.g. csv, gRPC)
* Cons
    * Only single sink (stdout)

### Windows process isolation

On Windows, running `composer run-examples` can randomly cause this error:

```
Script cd example/protobuf-sync-message/consumer && phpunit handling the run-example:protobuf-sync-message event returned with error code -1073741819
Script composer run-example:protobuf-sync-message handling the run-examples event returned with error code -1073741819
```

According to [Microsoft](https://learn.microsoft.com/en-us/answers/questions/379441/error-code-exited-with-code-1073741819#answer-379350):

> it indicates that your program was terminated due to an access violation. This error can occur for a variety of reasons, including de-referencing a NULL pointer or referencing an invalid address.

The solution is `composer run-examples:windows`. It attach option `--process-isolation`, which fixed this error.
