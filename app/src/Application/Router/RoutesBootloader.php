<?php

declare(strict_types=1);

namespace App\Application\Router;

use App\Application\Router\Middleware\LocaleSelector;
use App\Application\Router\Middleware\RedirectFirewall;
use App\Backend\Home\Controller;
use App\Frontend\Profile\Middleware\InviteCampaign;
use Nyholm\Psr7\Uri;
use Spiral\Auth\Middleware\AuthMiddleware;
use Spiral\Bootloader\Http\RoutesBootloader as BaseRoutesBootloader;
use Spiral\Cookies\Middleware\CookiesMiddleware;
use Spiral\Core\Container\Autowire;
use Spiral\Csrf\Middleware\CsrfMiddleware;
use Spiral\Debug\Middleware\DumperMiddleware;
use Spiral\Debug\StateCollector\HttpCollector;
use Spiral\Filter\ValidationHandlerMiddleware;
use Spiral\Http\Middleware\ErrorHandlerMiddleware;
use Spiral\Http\Middleware\JsonPayloadMiddleware;
use Spiral\Router\Bootloader\AnnotatedRoutesBootloader;
use Spiral\Router\GroupRegistry;
use Spiral\Router\Loader\Configurator\RoutingConfigurator;
use Spiral\Session\Middleware\SessionMiddleware;

/**
 * A bootloader that configures the application's routes and middleware.
 *
 * @link https://spiral.dev/docs/http-routing
 */
final class RoutesBootloader extends BaseRoutesBootloader
{
    protected const DEPENDENCIES = [AnnotatedRoutesBootloader::class];

    #[\Override]
    protected function globalMiddleware(): array
    {
        return [
            LocaleSelector::class,
            ErrorHandlerMiddleware::class,
            DumperMiddleware::class,
            JsonPayloadMiddleware::class,
            HttpCollector::class,
        ];
    }

    #[\Override]
    protected function middlewareGroups(): array
    {
        return [
            'web' => [
                CookiesMiddleware::class,
                SessionMiddleware::class,
                InviteCampaign::class,
                ValidationHandlerMiddleware::class,
            ],
            'backend' => [
                CookiesMiddleware::class,
                SessionMiddleware::class,
                CsrfMiddleware::class,
                ValidationHandlerMiddleware::class,
                AuthMiddleware::class,
                new Autowire(RedirectFirewall::class, ['uri' => new Uri('/'), 'exclude' => [Controller::ROUTE_AUTH]]),
            ],
        ];
    }

    #[\Override]
    protected function configureRouteGroups(GroupRegistry $groups): void
    {
        $groups
            ->getGroup('backend')
            ->setNamePrefix('backend.')
            ->setPrefix('/backend');
    }

    #[\Override]
    protected function defineRoutes(RoutingConfigurator $routes): void
    {
        // Fallback route if no other route matched
        // Will show 404 page
        // $routes->default('/<path:.*>')
        //    ->callable(function (ServerRequestInterface $r, ResponseInterface $response) {
        //        return $response->withStatus(404)->withBody('Not found');
        //    });
    }
}
