FROM php:8.3-alpine
RUN apk add --no-cache linux-headers libffi-dev protoc protobuf-dev musl-dev autoconf gcc g++ make
COPY --from=composer/composer:2-bin /composer /usr/local/bin/composer
RUN docker-php-ext-install sockets
RUN docker-php-ext-install ffi
RUN pecl install grpc
RUN echo 'extension=grpc.so' >> /usr/local/etc/php/conf.d/grpc.ini
