<?php

declare(strict_types=1);

namespace App\Application\Router\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Core\Attribute\Scope;
use Spiral\Framework\Spiral;
use Spiral\Http\ResponseWrapper;
use Spiral\Session\SessionInterface;

/**
 * Middleware to handle invite campaign codes in the URL.
 *
 * This middleware checks for an 'invite_code' query parameter in the incoming request.
 * If found, it stores the code in the session and redirects the user to the same URL
 * without the 'invite_code' parameter to keep the URL clean.
 */
#[Scope(Spiral::Http)]
class InviteCampaign implements MiddlewareInterface
{
    public const SECTION_NAME = 'campaign';
    public const QUERY_PARAM = 'invite_code';

    public function __construct(
        private readonly SessionInterface $session,
        private readonly ResponseWrapper $response,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        # Check that an invitation code is present in the query parameters
        $inviteCode = $request->getQueryParams()[self::QUERY_PARAM] ?? null;

        if ($inviteCode !== null) {
            # Store the invite code in the session for later use
            $this->session->getSection(self::SECTION_NAME)->set(self::QUERY_PARAM, $inviteCode);

            # Remove the invite code from the query parameters to clean up the URL
            $queryParams = $request->getQueryParams();
            unset($queryParams[self::QUERY_PARAM]);
            $uri = $request->getUri()->withQuery(\http_build_query($queryParams));

            # Redirect to the same URL without the invite code
            return $this->response->redirect($uri);
        }

        return $handler->handle($request);
    }
}
