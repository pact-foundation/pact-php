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
                    'resolve' => static function (array $rootValue, array $args): string {
                        $prefix = $rootValue['prefix'] ?? '';
                        $message = $args['message'] ?? '';
                        if (!is_string($prefix) || !is_string($message)) {
                            throw new RuntimeException('Invalid arguments');
                        }

                        return $prefix . $message;
                    },
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
                    'resolve' => static function (array $calc, array $args): int {
                        $x = $args['x'] ?? 0;
                        $y = $args['y'] ?? 0;
                        if (!is_int($x) || !is_int($y)) {
                            throw new RuntimeException('Invalid arguments');
                        }

                        return $x + $y;
                    },
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

        /** @var array{query?: string, variables?: array<string, mixed>} $body */
        $body = json_decode((string) $request->getBody(), true);
        $query = $body['query'] ?? '';
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
