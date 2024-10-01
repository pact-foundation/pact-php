<?php

use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->post('/api', function (ServerRequestInterface $request) {
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

        $body = json_decode((string) $request->getBody(), true);
        $query = $body['query'];
        $variableValues = $body['variables'] ?? null;

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

    return Response::json($output);
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    return new Response();
});

$app->run();
