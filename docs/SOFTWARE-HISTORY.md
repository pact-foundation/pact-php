# Pact-PHP Software History

10.X updates internal dependencies and libraries + adds support for pact specification 3.X & 4.X via Pact FFI.

9.X updates internal dependencies and libraries including pact-ruby-standalone v2.x which adds support for ARM64 CPU's for Linux/MacOS and providing x86 and x86_64 Windows via pact-ruby-standalone v2.x.   This results in dropping PHP 7.4

8.X updates internal dependencies and libraries.   This results in dropping PHP 7.3

7.x updates internal dependencies and libraries, mostly to Guzzle 7.X.  This results in dropping support for PHP 7.2.
6.x updates internal dependencies, mostly surrounding the Amp library.  This results in dropping support for PHP 7.1.

5.X adds preliminary support for async messages and pact specification 3.X.  This does not yet support the full pact specification 3.X as the backend implementations are incomplete. However, pact-messages are supported.

The 4.X tags are accompany changes in PHPUnit 7.X which requires a PHP 7.1 or higher.  Thus, 4.X drops support for PHP 7.0.

The 3.X tags are a major breaking change to the 2.X versions.   To be similar to the rest of the Pact ecosystem, Pact-PHP migrated to leverage the Ruby backend.  This mirrors the .Net, JS, Python, and Go implementations.

If you wish to stick with the 2.X implementation, you can continue to pull from the [latest 2.X.X tag](https://github.com/pact-foundation/pact-php/tree/2.2.1).
