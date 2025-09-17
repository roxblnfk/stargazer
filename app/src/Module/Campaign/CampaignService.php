<?php

declare(strict_types=1);

namespace App\Module\Campaign;

use App\Module\Campaign\DTO\Campaign;
use App\Module\Campaign\DTO\CampaignRepo;
use App\Module\Campaign\DTO\CampaignUser;
use App\Module\Campaign\DTO\UserCampaign;
use App\Module\Campaign\DTO\UserRepositoryDetails;
use App\Module\Campaign\Form\CreateCampaign;
use App\Module\Campaign\Form\UpdateCampaign;
use App\Module\Campaign\Internal\ORM\CampaignEntity;
use App\Module\Campaign\Internal\ORM\CampaignRepoEntity;
use App\Module\Campaign\Internal\ORM\CampaignRepoRepository;
use App\Module\Campaign\Internal\ORM\CampaignRepository;
use App\Module\Campaign\Internal\ORM\CampaignUserEntity;
use App\Module\Campaign\Internal\ORM\CampaignUserRepository;
use App\Module\Campaign\Internal\Scoring;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Dto\GithubUser;
use App\Module\Main\DTO\Repository;
use App\Module\Main\DTO\User;
use App\Module\Main\RepositoryService;
use App\Module\Main\StargazerService;
use App\Module\Main\UserService;
use Ramsey\Uuid\UuidInterface;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final class CampaignService
{
    public function __construct(
        private readonly CampaignUserRepository $campaignUserRepository,
        private readonly CampaignRepository $campaignRepository,
        private readonly CampaignRepoRepository $campaignRepoRepository,
        private readonly UserService $userService,
        private readonly RepositoryService $repositoryService,
        private readonly StargazerService $stargazerService,
        private readonly Scoring $scoring,
    ) {}

    /**
     * @return \Iterator<int, Campaign>
     */
    public function getCampaigns(?bool $visibility = null): \Iterator
    {
        $q = $this->campaignRepository;
        $visibility === null or $q = $q->visible($visibility);
        foreach ($q->findAll() as $e) {
            yield $e->toDTO();
        }
    }

    public function createCampaign(CreateCampaign $data): Campaign
    {
        $entity = CampaignEntity::create(
            title: $data->title,
            description: $data->description,
            startedAt: $data->startedAt,
            finishedAt: $data->finishedAt,
        );

        $entity->saveOrFail();
        return $entity->toDTO();
    }

    public function getCampaign(UuidInterface $uuid): Campaign
    {
        return $this->campaignRepository->findByPK($uuid)?->toDTO()
            ?? throw new \RuntimeException('Campaign not found.');
    }

    public function findCampaignByInvite(string $code): ?Campaign
    {
        return $this->campaignRepository->invitationCode($code)->findOne()?->toDTO();
    }

    public function updateCampaign(UpdateCampaign $form): Campaign
    {
        $e = $this->campaignRepository->findByPK($form->uuid) ?? throw new \RuntimeException('Campaign not found.');
        $e->title = $form->title;
        $e->description = $form->description;
        $e->startedAt = $form->startedAt;
        $e->finishedAt = $form->finishedAt;
        $e->visible = $form->visible;
        $e->inviteCode = $form->inviteCode ?: null;
        $e->oldStarsCoefficient = $form->oldStarsCoefficient;
        $e->saveOrFail();
        return $e->toDTO();
    }

    public function deleteCampaign(UuidInterface $uuid): void
    {
        $e = $this->campaignRepository->findByPK($uuid) ?? throw new \RuntimeException('Campaign not found.');
        if ($e->countUsers > 0) {
            throw new \RuntimeException('Cannot delete a campaign with members.');
        }

        $e->deleteOrFail();
    }

    /**
     * @throws \Throwable
     */
    public function joinCampaign(
        UuidInterface $campaignUuid,
        GithubUser $username,
        ?string $invite,
    ): CampaignUser {
        # Fetch user
        $user = $this->userService->getByUsername($username);
        $user instanceof User or throw new \RuntimeException('Unknown user.');

        # Do it in a transaction because we are going to write to multiple records
        return CampaignEntity::transact(function () use ($campaignUuid, $user, $invite): CampaignUser {
            # Fetch campaign
            $campaign = $this->campaignRepository->forUpdate()->findByPK($campaignUuid)
                ?? throw new \RuntimeException('Campaign not found.');

            # The invite code is required if the campaign is private
            $campaign->visible or $invite === null or $campaign->inviteCode === $invite or throw new \RuntimeException(
                'Invalid invite code.',
            );

            # Check if the user is already a member
            $entity = $this->campaignUserRepository
                ->withCampaignUuid($campaign->uuid)
                ->withUserId($user->id)
                ->findOne();

            if ($entity !== null) {
                return $entity->toDTO();
            }

            $entity = CampaignUserEntity::create(
                userId: $user->id,
                userName: $user->login,
                campaignId: $campaign->uuid,
            );
            $campaign->countUsers += 1;

            $campaign->saveOrFail();
            $entity->saveOrFail();

            return $entity->toDTO();
        });
    }

    /**
     * @return array<int, CampaignUser>
     */
    public function getCampaignMembers(UuidInterface $uuid): array
    {
        $members = [];
        foreach ($this->campaignUserRepository->withCampaignUuid($uuid)->sortByScore()->findAll() as $e) {
            $members[] = $e->toDTO();
        }
        return $members;
    }

    /**
     * @return array<int, UserRepositoryDetails>
     */
    public function getCampaignUserRepositories(UuidInterface $campaignUuid, int $userId): array
    {
        $repos = $this
            ->campaignRepoRepository
            ->loadRepository()
            ->withCampaignUuid($campaignUuid)->findAll();

        # todo optimize
        $stars = $this->stargazerService->getRepositoryIdsByUserId($userId);

        $result = [];
        foreach ($repos as $e) {
            $result[] = new UserRepositoryDetails(
                campaignRepo: $e->toDTO(),
                repository: $e->repository->toDTO(),
                starred: \array_key_exists($e->repository->id, $stars),
            );
        }
        return $result;
    }

    public function addRepoToCampaign(UuidInterface $campaignUuid, GithubRepository $repository): CampaignRepo
    {
        # Fetch repo
        $repo = $this->repositoryService->getRepository($repository);

        # Do it in a transaction because we are going to write to multiple records
        return CampaignEntity::transact(function () use ($campaignUuid, $repo): CampaignRepo {
            # Fetch campaign
            $campaign = $this->campaignRepository->forUpdate()->findByPK($campaignUuid)
                ?? throw new \RuntimeException('Campaign not found.');

            # Check if the repo is already added
            $entity = $this->campaignRepoRepository
                ->withCampaignUuid($campaign->uuid)
                ->withRepoId($repo->id)
                ->findOne();

            if ($entity !== null) {
                return $entity->toDTO();
            }

            $entity = CampaignRepoEntity::create(
                repoId: $repo->id,
                repoName: $repo->fullName,
                campaignId: $campaign->uuid,
            );
            $campaign->countRepositories += 1;

            $campaign->saveOrFail();
            $entity->saveOrFail();

            return $entity->toDTO();
        });
    }

    public function removeRepoFromCampaign(UuidInterface $campaignUuid, GithubRepository $repository): void
    {
        # Fetch repo
        $repo = $this->repositoryService->getRepository($repository);

        # Do it in a transaction because we are going to write to multiple records
        CampaignEntity::transact(function () use ($campaignUuid, $repo): void {
            # Fetch campaign
            $campaign = $this->campaignRepository->forUpdate()->findByPK($campaignUuid)
                ?? throw new \RuntimeException('Campaign not found.');

            # Check if the repo is added
            $entity = $this->campaignRepoRepository
                ->withCampaignUuid($campaign->uuid)
                ->withRepoId($repo->id)
                ->findOne();

            if ($entity === null) {
                return;
            }

            $campaign->countRepositories -= 1;

            $campaign->saveOrFail();
            $entity->deleteOrFail();
        });
    }

    /**
     * @return array<int, CampaignRepo>
     */
    public function getAddedRepos(UuidInterface $campaignUuid): array
    {
        $result = [];
        $entities = $this->campaignRepoRepository->withCampaignUuid($campaignUuid)->findAll();

        foreach ($entities as $e) {
            $result[] = $e->toDTO();
        }

        return $result;
    }

    /**
     * @return \Iterator<int, Repository>
     */
    public function getNotAddedRepos(UuidInterface $campaignUuid): \Iterator
    {
        $data = $this->campaignRepoRepository->withCampaignUuid($campaignUuid)->select()->fetchData();
        /** @see CampaignRepoEntity::repoId */
        $ids = \array_map(static fn(array $record): int => (int) $record['repoId'], $data);

        return $this->repositoryService->getRepositories(exclude: $ids);
    }

    public function changeRepoScore(UuidInterface $campaignUuid, int $repoId, int $change): CampaignRepo
    {
        return CampaignRepoEntity::transact(function () use ($campaignUuid, $repoId, $change): CampaignRepo {
            $e = $this->campaignRepoRepository
                ->forUpdate()
                ->withCampaignUuid($campaignUuid)
                ->withRepoId($repoId)
                ->findOne() ?? throw new \RuntimeException('Repository not found in campaign.');

            if ($change !== 0) {
                $e->score += $change;
                $e->saveOrFail();
            }

            return $e->toDTO();
        });
    }

    /**
     * @param bool|null $joined If true, only campaigns the user has joined will be returned.
     *        If false, only campaigns the user has not joined will be returned.
     *        If null, all campaigns will be returned.
     * @return \Iterator<int, UserCampaign>
     */
    public function getUserCampaigns(int $userId, ?bool $joined = null): \Iterator
    {
        $campaigns = match ($joined) {
            null => $this->campaignRepository->withLoadedMembers([$userId])->findAll(),
            true => $this->campaignRepository->forMember($userId, true)->withLoadedMembers([$userId])->findAll(),
            false => $this->campaignRepository->forMember($userId, false)->findAll(),
        };

        foreach ($campaigns as $e) {
            /** @var CampaignUserEntity|null $member */
            $member = \reset($e->members) ?: null;
            if ($member === null && $e->visible || $member?->userId === $userId) {
                yield new UserCampaign($e->toDTO(), $member?->toDTO());
            }
        }
    }

    public function getUserCampaign(int $userId, UuidInterface $campaignUuid): UserCampaign
    {
        $campaign = $this->campaignRepository->withLoadedMembers([$userId])->findByPK($campaignUuid)
            ?? throw new \RuntimeException('Campaign not found.');
        $member = \reset($campaign->members) ?: null;
        if ($member === null && !$campaign->visible || $member !== null && $member->userId !== $userId) {
            throw new \RuntimeException('Campaign not found.');
        }

        return new UserCampaign($campaign->toDTO(), $member?->toDTO());
    }

    /**
     * Recalculate scores for all repositories in the campaign.
     */
    public function calculateScores(UuidInterface $campaignUuid): void
    {
        $this->scoring->calculateScores($campaignUuid);
    }
}
