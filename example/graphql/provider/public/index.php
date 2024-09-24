<?php

use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../autoload.php';

$app = AppFactory::create();

$app->post('/api', function (Request $request, Response $response) {
    try {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'echo' => [
                    'type' => Type::string(),
                    'args' => [
                        'message' => ['type' => Type::string()],
                    ],
                    'resolve' => static fn ($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'sum' => [
                    'type' => Type::int(),
                    'args' => [
                        'x' => ['type' => Type::int()],
                        'y' => ['type' => Type::int()],
                    ],
                    'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
                ],
            ],
        ]);

        // See docs on schema options:
        // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
        $schema = new Schema(
            (new SchemaConfig())
            ->setQuery($queryType)
            ->setMutation($mutationType)
        );

        $rawInput = file_get_contents('php://input');
        if ($rawInput === false) {
            throw new RuntimeException('Failed to get php://input');
        }

        $input = json_decode($rawInput, true);
        $query = $input['query'];
        $variableValues = $input['variables'] ?? null;

        $rootValue = ['prefix' => 'You said: '];
        $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
        $output = $result->toArray();
    } catch (Throwable $e) {
        $output = [
            'error' => [
                'message' => $e->getMessage(),
            ],
        ];
    }

    $response->getBody()->write(json_encode($output, JSON_THROW_ON_ERROR));

    return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
});

$app->post('/pact-change-state', function (Request $request, Response $response) {
    return $response;
});

$app->run();
