<?php

declare(strict_types=1);

namespace App\Application\Router\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Auth\Middleware\Firewall\AbstractFirewall;
use Spiral\Core\Attribute\Scope;
use Spiral\Router\RouterInterface;

#[Scope('http')]
class RedirectFirewall extends AbstractFirewall
{
    private readonly array $routes;

    /**
     * @param list<non-empty-string> $exclude Route names to exclude from the firewall
     */
    public function __construct(
        protected readonly UriInterface $uri,
        protected readonly ResponseFactoryInterface $responseFactory,
        RouterInterface $router,
        protected readonly int $status = 302,
        protected readonly array $exclude = [],
    ) {
        $this->routes = \array_map(static fn($route) => $router->getRoute($route), $exclude);
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        foreach ($this->routes as $route) {
            if ($route->match($request)) {
                return $handler->handle($request);
            }
        }

        return parent::process($request, $handler);
    }

    protected function denyAccess(Request $request, RequestHandlerInterface $handler): Response
    {
        return $this->responseFactory->createResponse($this->status)->withHeader('Location', (string) $this->uri);
    }
}
