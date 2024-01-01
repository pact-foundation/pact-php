<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/movies', function (Request $request, Response $response) {
    $response->getBody()->write(
        <<<XML
        <?xml version='1.0' standalone='yes'?>
        <movies>
            <movie>
                <title>PHP: Behind the Parser</title>
                <characters>
                    <character>
                        <name>Ms. Coder</name>
                        <actor>Onlivia Actora</actor>
                    </character>
                    <character>
                        <name>Mr. Coder</name>
                        <actor>El Act&#211;r</actor>
                    </character>
                </characters>
                <plot>
                So, this language. It's like, a programming language. Or is it a
                scripting language? All is revealed in this thrilling horror spoof
                of a documentary.
                </plot>
                <great-lines>
                    <line>PHP solves all my web problems</line>
                </great-lines>
                <rating type="thumbs">7</rating>
                <rating type="stars">5</rating>
            </movie>
        </movies>
        XML
    );

    return $response->withHeader('Content-Type', 'application/movies+xml');
});

$app->post('/pact-change-state', function (Request $request, Response $response) {
    return $response;
});

$app->run();
