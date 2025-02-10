<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->get('/movies', function (ServerRequestInterface $request) {
    return Response::xml(
        <<<XML
        <?xml version='1.0' standalone='yes'?>
        <movies>
            List of movies
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
                <release-date>
                    08-12-2022
                    <china>
                        21-11-2024
                    </china>
                </release-date>
                <specs runtime="1 hours 07 minutes" aspect-ratio="3:2" color="color"></specs>
                <also-known-as xmlns:aka="http://example.com/movies">
                    <aka:united-states>
                        PHP: The movie
                    </aka:united-states>
                    <aka:australia>
                        PHP: The language
                    </aka:australia>
                    <aka:argentina>
                        PHP: La película
                    </aka:argentina>
                    <aka:brazil>
                        PHP: O filme
                    </aka:brazil>
                </also-known-as>
            </movie>
        </movies>
        XML
    )
    ->withHeader('Content-Type', 'application/movies+xml');
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    return new Response();
});

$app->run();
