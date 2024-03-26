# Pact-PHP

## Pre Reqs

- PHP 8.x or greater
- FFI and Sockets extensions enabled in your php.ini

## Steps

1. Run `composer install`
   1. This will install php dependencies to `vendor`
   2. This will install pact libraries to `bin`
2. Run `composer test`
   1. This will run our unit tests
3. Run `composer lint`
   1. This will run the phpcs-lint
4. Run `composer fix`
   1. This will correct any auto fixable linter errors
5. Run `composer static-code-analysis`
   1. Run static code analysis

## CI Locally

### MacOS ARM

#### Pre Reqs

- MacOS ARM
- Tart.run
- Cirrus-CLI

#### Steps

Run all versions of PHP

- `cirrus run --output github-actions macos_arm64 -e CIRRUS_CLI=true`

Run a specified version of PHP

- `cirrus run --output github-actions 'macos_arm64 VERSION:8.2' -e CIRRUS_CLI=true`

### Linux ARM

#### Pre Reqs

- Docker
- x86_64 or arm64/aarch64 host

#### Steps

Run all versions of PHP

- `cirrus run --output github-actions linux_arm64`

Run a specified version of PHP

- `cirrus run --output github-actions 'macos_arm64 VERSION:8.2'`