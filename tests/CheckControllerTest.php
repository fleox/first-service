<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use TypeError;

/**
 * @phpstan-type RouteParamYaml array{
 *      'parameters'?: array{
 *          'role'?: string,
 *          'graph_ql'?: array<string,array{
 *              locale?: string,
 *              body?: array<mixed>,
 *              response-status-code?: int,
 *              assertJsonContains?: string,
 *              content-type?: string
 *          }>,
 *          'route_params'?: array<string,array{
 *              'getUIDFromEM'?: array{
 *                  'entity': string,
 *                  'query': array<string,string>
 *              },
 *              'response-status-code'?: int,
 *              'body'?: array<string,string>,
 *              'params'?: array<string,string|int>,
 *              'role'?: string,
 *              'userId'?: string,
 *              'assertJsonContains'?: string,
 *              'content-type'?: string,
 *          }>
 *      }
 * }
 */
class CheckControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    use LoginTrait;

    private const ROUTES_NOT_TESTED = [
        'overblog_graphql_endpoint',
        'overblog_graphql_batch_endpoint',
        'overblog_graphql_multiple_endpoint',
        'overblog_graphql_batch_multiple_endpoint'
    ];

    public function testAllRoutes(): void
    {
        /** @psalm-var RouteParamYaml */
        $routeYaml = Yaml::parseFile(__DIR__ . '/../config/packages/test/routes_params.yaml');

        if (!isset($routeYaml['parameters'])) {
            throw new TypeError('parameters key not defined on routes_params.yaml');
        }
        $yaml = $routeYaml['parameters'];

        if (!isset($yaml['role'])) {
            throw new TypeError('parameters.role key not defined on routes_params.yaml');
        }
        //$token = $this->createUser('User::' . $yaml['role']);
        $token = false;

        if (!isset($yaml['route_params'])) {
            throw new TypeError('parameters.route_params key not defined on routes_params.yaml');
        }
        $routeParams = $yaml['route_params'];

        self::bootKernel();

        /** @var Router */
        $router = static::$kernel->getContainer()
            ->get('router');

        $routes = $router->getRouteCollection();

        if (!isset($yaml['graph_ql'])) {
            throw new TypeError('parameters.graph_ql key not defined on routes_params.yaml');
        }
        // GraphQL tests.
        foreach ($yaml['graph_ql'] as $graphQl) {
            $locale = $graphQl['locale'] ?? 'fr';
            $body = $graphQl['body'] ?? [];
            $responseStatusCode = $graphQl['response-status-code'] ?? 200;

            $url = $router->generate('overblog_graphql_endpoint', ['locale' => $locale]);
            $this->client->request('POST', $url, $body, [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            ]);
            $this->assertResponseStatusCodeSame($responseStatusCode);
            $this->assertResponseHeaderSame('content-type', $graphQl['content-type'] ?? 'application/json');
        }

        foreach ($routes as $routeName => $route) {
            if (in_array($routeName, self::ROUTES_NOT_TESTED)) {
                continue;
            }

            // get params from yaml.
            $params = $routeParams[$routeName]['params'] ?? [];
            if (isset($routeParams[$routeName]['role'])) {
                $token = $this->createUser('User::' . $routeParams[$routeName]['role'], $routeParams[$routeName]['userId'] ?? '');
            }
            if (isset($routeParams[$routeName]['getUIDFromEM'])) {
                /** @var EntityManagerInterface */
                $em = static::$kernel->getContainer()
                    ->get('doctrine.orm.entity_manager');

                /** @var class-string */
                $class = 'App\\Entity\\' . $routeParams[$routeName]['getUIDFromEM']['entity'];
                $entityUseForTest = $em
                    ->getRepository($class)
                    ->findOneBy($routeParams[$routeName]['getUIDFromEM']['query']);
                if (!$entityUseForTest || !method_exists($entityUseForTest, 'getId')) {
                    throw new TypeError('Bad entity use for test id');
                }
                $params['id'] = $entityUseForTest->getId()->__toString();
            }

            $url = $router->generate($routeName, $params);
            $contentType = $routeParams[$routeName]['content-type'] ?? null;
            $body = $routeParams[$routeName]['body'] ?? [];
            $responseStatusCode = $routeParams[$routeName]['response-status-code'] ?? 200;

            if ($route->getMethods() && in_array(Request::METHOD_PUT, $route->getMethods())) {
                $this->client->request('PUT', $url, $body, [], [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                ]);
            }

            if ($route->getMethods() && in_array(Request::METHOD_POST, $route->getMethods())) {
                $this->client->request('POST', $url, $body, [], [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                ]);
            }

            if ($route->getMethods() && in_array(Request::METHOD_PATCH, $route->getMethods())) {
                $this->client->request('PATCH', $url, $body, [], [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                ]);
            }

            if ($route->getMethods() && in_array(Request::METHOD_DELETE, $route->getMethods())) {
                $this->client->request('DELETE', $url, [], [], [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                ]);
            }

            if (!$route->getMethods() || in_array(Request::METHOD_GET, $route->getMethods())) {
                $this->client->request('GET', $url, [], [], [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                ]);
            }

            $this->assertResponseStatusCodeSame($responseStatusCode, 'url: ' . $url . ' methods: [' . join(', ', $route->getMethods()) . ']');

            if ($contentType) {
                $this->assertResponseHeaderSame('content-type', $contentType);
            }
        }
    }
}
