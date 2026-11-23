# Pact-PHP

## Pre Reqs

- PHP 8.2+ (see `composer.json`)
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

### Alpine (musl)

The `test-alpine` CI jobs run unit tests and examples on x86_64 and aarch64 using
`alpine:3.24` and its distro PHP 8.5 packages. Keep all PHP extension packages on
the same version as the runtime required by Alpine's
[`composer` package](https://pkgs.alpinelinux.org/package/v3.24/community/x86_64/composer).
Use a [supported Alpine release](https://alpinelinux.org/releases/) with current
PHP security patches: `roave/security-advisories` checks the PHP runtime as well
as dependencies, so an outdated runtime can fail `composer install` before any
tests run. Do not remove the advisories or ignore platform requirements.

To run the same checks locally, install Docker and run this from a fresh checkout
on an x86_64 or arm64/aarch64 host. Do not reuse `vendor` or downloaded `bin`
artifacts from a glibc-based host; Composer must install the musl libraries in
the container.

```shell
docker run --env PACT_DO_NOT_TRACK=true --rm -v "$PWD":/home alpine:3.24 /bin/sh -c '
  apk add --no-cache shared-mime-info php85-dev php85-ffi php85-pecl-grpc php85-sockets php85-tokenizer php85-dom php85-xml php85-xmlwriter php85-simplexml composer protobuf-dev curl file &&
  cd /home &&
  composer install &&
  curl --proto "=https" --tlsv1.2 -LsSf https://github.com/pact-foundation/pact-cli/releases/latest/download/pact-installer.sh | sh &&
  . "$HOME/.config/pact/env.sh" &&
  pact plugin install https://github.com/tienvx/pact-sse-plugin/releases/tag/0.1.0 -y &&
  composer test &&
  composer run-examples'
```

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