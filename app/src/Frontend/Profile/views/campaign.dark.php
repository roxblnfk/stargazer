<extends:layout title="[[Events]] - {{ $campaign->title }}"/>

<stack:push name="styles">
<style>
    :root {
        --bg-primary: #0a0a0f;
        --bg-secondary: #111827;
        --accent-purple: #8b5cf6;
        --accent-blue: #3b82f6;
        --accent-cyan: #06b6d4;
        --accent-green: #10b981;
        --accent-orange: #f59e0b;
        --text-white: #ffffff;
        --text-gray-100: #f3f4f6;
        --text-gray-400: #9ca3af;
        --text-gray-500: #6b7280;
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --shadow-glow: rgba(139, 92, 246, 0.3);
    }

    /* Golden styling for starred repositories */
    .repo-card.starred {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%) !important;
        border: 1px solid rgba(255, 215, 0, 0.3) !important;
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2) !important;
    }

    .repo-card.starred:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3) !important;
    }

    .star-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
    }

    .star-badge i {
        color: #fff;
        font-size: 14px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .campaign-header {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .campaign-title {
        color: var(--text-white);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .campaign-meta {
        display: flex;
        gap: 2rem;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .campaign-stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-gray-400);
        font-size: 0.9rem;
    }

    .campaign-stat i {
        color: var(--accent-cyan);
    }

    .campaign-stat-value {
        color: var(--text-white);
        font-weight: 600;
    }

    .user-score-badge {
        background: linear-gradient(135deg, var(--accent-green), #059669);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
<link href="/styles/profile.css" rel="stylesheet" type="text/css" />
</stack:push>

<define:body>
    <div class="min-vh-100 cosmic-bg position-relative">
        <div class="container py-4">
            <a href="@route(\App\Frontend\Profile\Controller::ROUTE_INDEX, ['name' => $user->login])" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                [[Back]]
            </a>

            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <!-- Campaign Header -->
                    <div class="campaign-header">
                        <h1 class="campaign-title">{{ $campaign->title }}</h1>

                        <div class="campaign-meta">
                            <div class="campaign-stat">
                                <i class="bi bi-folder"></i>
                                <span class="campaign-stat-value">{{ $campaign->repositoryCount }}</span>
                                <span>[[repositories]]</span>
                            </div>
                            <div class="campaign-stat">
                                <i class="bi bi-people"></i>
                                <span class="campaign-stat-value">{{ $campaign->memberCount }}</span>
                                <span>[[members]]</span>
                            </div>
                            <div class="campaign-stat">
                                <i class="bi bi-calendar-event"></i>
                                <span>{{ $campaign->startedAt->format('M j') }}
                                @if($campaign->finishedAt)
                                    - {{ $campaign->finishedAt->format('M j, Y') }}
                                @else
                                    - [[Ongoing]]
                                @endif
                                </span>
                            </div>

                            @if($userCampaign?->user !== null)
<!--                                <div class="user-score-badge">-->
<!--                                    <i class="bi bi-star-fill"></i>-->
<!--                                    <span>[[Your score]]: {{ $userCampaign->user->score }}</span>-->
<!--                                </div>-->
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repositories Section - Full Width -->
            <div class="container-fluid px-4">
                <div class="section-header mb-4 text-center">
                    <h2 style="color: var(--text-white); font-size: 1.5rem; font-weight: 600;">
                        <i class="bi bi-github me-2"></i>[[Event Repositories]]
                    </h2>
                </div>

                <!-- Repository Grid -->
                <div class="repo-grid">
                    @foreach($repositories as $userRepo)
                    <a href="{{ $userRepo->repository->info->htmlUrl }}" target="_blank" class="repo-card d-flex flex-column text-decoration-none position-relative overflow-hidden {{ $userRepo->starred ? 'starred' : '' }}"
                       style="color: inherit;">

                        @if($userRepo->starred)
                        <div class="star-badge">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        @endif


                        <div class="repo-header">
                            <img src="{{ $userRepo->repository->info->owner->avatarUrl }}" alt="{{ $userRepo->repository->fullName }}" class="repo-avatar">
                            <div>
                                <div class="repo-name">{{ $userRepo->repository->name }}</div>
                                <div class="repo-owner">{{ $userRepo->repository->owner }}</div>
                            </div>
                        </div>
                        <p class="repo-description">
                            {{ $userRepo->repository->info->description }}
                        </p>
                        <div class="repo-footer mt-auto">
                            <div class="repo-stats">
                                <div class="stat-item">
                                    <i class="bi bi-star"></i>
                                    {{ $userRepo->repository->info->stargazersCount }}
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-diagram-3"></i>
                                    {{ $userRepo->repository->info->forksCount }}
                                </div>
                            </div>

                            <div class="star-status {{ $userRepo->starred ? 'starred' : 'not-starred' }}">
                                <i class="bi bi-gem"></i>
                                {{ $userRepo->campaignRepo->score }}
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</define:body>
