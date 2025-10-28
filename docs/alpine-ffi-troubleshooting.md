# Pact PHP — Alpine (musl) FFI Troubleshooting

This page explains how to fix a common issue when running **Pact PHP v10** on **Alpine Linux (musl)**.

---

## The problem

When running Pact on Alpine, you may see:

~~~text
error while loading shared libraries: libgcc_s.so.1: cannot open shared object file
~~~

This happens because Alpine images are minimal:
- Required runtime libraries (like `libgcc`) aren’t installed.
- The PHP **FFI** extension (used by Pact v10) is missing or disabled.

---

## The fixes

### 1) Official PHP Alpine images (e.g., `php:8.3-cli-alpine`)

Add this to your Dockerfile:

~~~dockerfile
FROM php:8.3-cli-alpine

RUN set -eux; \
    apk add --no-cache $PHPIZE_DEPS libgcc; \
    docker-php-ext-install ffi; \
    echo "ffi.enable=true" > /usr/local/etc/php/conf.d/ffi-enable.ini
~~~

### 2) Alpine’s distro PHP (not Docker Hub’s `php:` image)

~~~bash
apk add --no-cache php83-ffi
echo "ffi.enable=true" > /etc/php83/conf.d/ffi-enable.ini
~~~

### 3) Debian/Ubuntu base images (e.g., `php:8.3-cli`)

~~~dockerfile
FROM php:8.3-cli

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends libgcc-12-dev; \
    docker-php-ext-install ffi; \
    rm -rf /var/lib/apt/lists/*; \
    echo "ffi.enable=true" > /usr/local/etc/php/conf.d/ffi-enable.ini
~~~

---

## Notes

- Pact PHP v10 requires **PHP 8.1+** and the **FFI** extension **enabled** (`ffi.enable=true`).
- If you use multi-stage Docker builds, make sure the **final** image still has `libgcc` and FFI enabled.
- On Apple Silicon (ARM64), use matching `arm64` images; Pact will load the correct native library automatically.
