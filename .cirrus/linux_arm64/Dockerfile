ARG VERSION
FROM php:${VERSION}
RUN apt update --yes && apt install --yes zip unzip git libffi-dev protobuf-compiler
COPY --from=composer/composer:2-bin /composer /usr/local/bin/composer
RUN docker-php-ext-install sockets
RUN docker-php-ext-install ffi
RUN pecl install grpc
RUN echo 'extension=grpc.so' >> /usr/local/etc/php/conf.d/grpc.ini
