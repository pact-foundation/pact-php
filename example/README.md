# Pact PHP Usage examples

This folder contains some integration tests which demonstrate the functionality of `pact-php`.
All examples could be run within tests.

## Consumer Tests

    docker-compose up -d 
    vendor/bin/phpunit -c example/phpunit.consumer.xml
    docker-compose down
    
## Provider Verification Tests

    vendor/bin/phpunit -c example/phpunit.provider.xml
    
## Consumer Tests for Message Processing

    vendor/bin/phpunit -c example/phpunit.message.consumer.xml

## Provider Verification Tests for Message Processing

    vendor/bin/phpunit -c example/phpunit.message.provider.xml
    
## All tests together 

    docker-compose up -d 
    vendor/bin/phpunit -c example/phpunit.all.xml
    docker-compose down
