<?php

use AaronDDM\XMLBuilder\Writer\XMLWriterService;
use AaronDDM\XMLBuilder\XMLBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$xmlWriterService = new XMLWriterService();
$xmlBuilder = new XMLBuilder($xmlWriterService);
$xmlBuilder
    ->createXMLArray()
        ->start('movies')
            ->start('movie')
                ->add('title', 'PHP: Behind the Parser')
                ->start('characters')
                    ->start('character')
                        ->add('name', 'Ms. Coder')
                        ->add('actor', 'Onlivia Actora')
                    ->end()
                    ->start('character')
                        ->add('name', 'Mr. Coder')
                        ->add('actor', 'El Act&#211;r')
                    ->end()
                ->end()
                ->add('plot', <<<PLOT
                So, this language. It's like, a programming language. Or is it a
                scripting language? All is revealed in this thrilling horror spoof
                of a documentary.
                PLOT)
                ->start('great-lines')
                    ->add('line', 'PHP solves all my web problems')
                ->end()
                ->add('rating', 7, ['type' => 'thumbs'])
                ->add('rating', 5, ['type' => 'stars'])
            ->end()
        ->end();

$app = AppFactory::create();

$app->get('/movies', function (Request $request, Response $response) use ($xmlBuilder) {
    $response->getBody()->write($xmlBuilder->getXml());

    return $response->withHeader('Content-Type', 'text/xml');
});

$app->post('/pact-change-state', function (Request $request, Response $response) {
    return $response;
});

$app->run();
