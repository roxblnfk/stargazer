<?php

declare(strict_types=1);

namespace App\Frontend\Profile;

use App\Application\Router\Middleware\InviteCampaign;
use App\Frontend\Profile\Form\ProfileRequest;
use App\Module\Campaign\CampaignService;
use App\Module\Github\Dto\GithubUser;
use App\Module\Main\DTO\UnknownUser;
use App\Module\Main\RepositoryService;
use App\Module\Main\StargazerService;
use App\Module\Main\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Router\Annotation\Route;
use Spiral\Session\SessionInterface;
use Spiral\Views\ViewsInterface;

final class Controller
{
    use PrototypeTrait;

    public const ROUTE_ENTER = 'profile:enter';
    public const ROUTE_INDEX = 'profile:index';
    public const ROUTE_JOIN_CAMPAIGN = 'profile:join-campaign';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly StargazerService $stargazerService,
        private readonly PointsService $pointsService,
        private readonly RepositoryService $repositoryService,
        private readonly UserService $userService,
        private readonly CampaignService $campaignService,
    ) {}

    #[Route(route: '/enter', name: self::ROUTE_ENTER, methods: ['GET'])]
    public function enter(ProfileRequest $input, SessionInterface $session): mixed
    {
        $username = new GithubUser($input->username);
        $session->getSection('user')->set('name', $username->name);

        return $this->response->redirect($this->router->uri(self::ROUTE_INDEX, [
            'name' => $username->name,
        ]));
    }

    #[Route(route: '/profile/<name>', name: self::ROUTE_INDEX, methods: ['GET'])]
    public function index(string $name, SessionInterface $session): mixed
    {
        $username = new GithubUser($name);
        $user = $this->userService->getByUsername($username);

        if ($user instanceof UnknownUser) {
            return $this->views->render('profile:index-unknown', [
                'user' => $user,
                'repositories' => $this->repositoryService->getTrackedRepositoriesInfo(),
            ]);
        }

        # Check for an invitation code in the session to suggest a campaign
        $invite = $session->getSection(InviteCampaign::SECTION_NAME)->get(InviteCampaign::QUERY_PARAM);
        $suggestCampaign = $invite === null ? null : $this->campaignService->findCampaignByInvite($invite);

        $stars = $this->stargazerService->getRepositoryIdsByUserId($user->id);
        return $this->views->render('profile:index-known', [
            'user' => $user,
            'stars' => $stars,
            'points' => $this->pointsService->calculate($stars),
            'repositories' => $this->repositoryService->getTrackedRepositoriesInfo(),
            'suggestCampaign' => $suggestCampaign,
        ]);
    }

    #[Route(route: '/profile/join-campaign', name: self::ROUTE_JOIN_CAMPAIGN, methods: ['POST'])]
    public function joinCampaign(ServerRequestInterface $request, SessionInterface $session): ResponseInterface
    {
        $campaignUuid = Uuid::fromString($request->getParsedBody()['campaign_uuid'] ?? '');
        $username = new GithubUser($request->getParsedBody()['username'] ?? '');

        # Check for an invitation code in the session
        $invite = $session->getSection(InviteCampaign::SECTION_NAME)->get(InviteCampaign::QUERY_PARAM);

        # Try to join the campaign if the invite code matches
        $this->campaignService->joinCampaign($campaignUuid, $username, $invite);

        if ($invite !== null) {
            # Remove the invite code from the session after using it
            $session->getSection(InviteCampaign::SECTION_NAME)->clear();
        }

        return $this->response->redirect($this->router->uri(self::ROUTE_INDEX, ['name' => $username]));
    }
}
