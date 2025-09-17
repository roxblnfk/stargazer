<extends:layout title="[[Profile - Stargazer]]"/>

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

    .campaign-suggestion .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--shadow-glow) !important;
    }

    .campaign-suggestion .card {
        transition: all 0.3s ease;
    }

    .campaign-suggestion .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
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
        z-index: 1;
    }

    .star-badge i {
        color: #fff;
        font-size: 14px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    /* Campaign carousel styling */
    .campaigns-carousel {
        position: relative;
        margin-bottom: 3rem;
    }

    .campaigns-scroll-container {
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: var(--glass-border) transparent;
        padding-bottom: 10px;
    }

    .campaigns-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    .campaigns-scroll-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .campaigns-scroll-container::-webkit-scrollbar-thumb {
        background: var(--glass-border);
        border-radius: 3px;
    }

    .campaigns-scroll-container::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .campaigns-track {
        display: flex;
        gap: 1.5rem;
        padding: 0.5rem 0;
    }

    .campaign-card {
        flex: 0 0 320px;
        max-width: 320px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        position: relative;
        overflow: hidden;
    }

    .campaign-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        border-color: var(--accent-purple);
        text-decoration: none;
        color: inherit;
    }

    .campaign-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
        position: relative;
    }

    .campaign-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-white);
        margin: 0;
        line-height: 1.3;
        padding-right: 1rem;
        flex: 1;
    }

    .campaign-card-status {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .campaign-status-active {
        background: linear-gradient(135deg, var(--accent-green), #059669);
        color: white;
    }

    .campaign-status-member {
        background: linear-gradient(135deg, var(--accent-blue), #1d4ed8);
        color: white;
    }

    .campaign-status-ended {
        background: linear-gradient(135deg, var(--text-gray-500), #4b5563);
        color: white;
    }

    .campaign-card-description {
        color: var(--text-gray-400);
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .campaign-card-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .campaign-stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-gray-400);
        font-size: 0.85rem;
    }

    .campaign-stat i {
        color: var(--accent-cyan);
    }

    .campaign-stat-value {
        color: var(--text-white);
        font-weight: 500;
    }

    .campaign-card-period {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-gray-500);
        font-size: 0.8rem;
        margin-top: auto;
    }

    .campaign-card-period i {
        color: var(--accent-orange);
    }

    .member-score {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        z-index: 2;
    }
</style>
<link href="/styles/profile.css" rel="stylesheet" type="text/css" />
</stack:push>

<define:body>
    <div class="min-vh-100 cosmic-bg position-relative">
        <div class="container py-4">
            <a href="@route(\App\Frontend\Index\Controller::ROUTE_INDEX)" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                [[Back]]
            </a>

            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="row align-items-center">
                            <div class="col-md-auto text-center text-md-start">
                                <img src="{{ $user->info->avatarUrl }}" alt="{{ $user->login }}" class="profile-avatar">
                            </div>
                            <div class="col-md flex-grow-1 text-center text-md-start mt-3 mt-md-0">
                                <h1 class="profile-name">{{ $user->login }}</h1>
                            </div>
                            <div class="col-md-auto">
                                <div class="stats-card">
                                    <span class="stats-number">{{$points}}</span>
                                    <span class="stats-label">[[Points]]</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Suggestion -->
                    @if($suggestCampaign !== null)
                    <div class="campaign-suggestion mb-5">
                        <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(139, 92, 246, 0.08) 100%); border: 1px solid var(--glass-border) !important;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <h1 class="mb-1" style="color: var(--accent-purple); font-weight: 600;">
                                        <i class="bi bi-megaphone-fill me-3"></i>
                                        {{ $suggestCampaign->title }}
                                    </h1>
                                </div>

                                <p class="mb-3 markdown-content" style="color: var(--text-gray-100); line-height: 1.5;"
                                >{{ $suggestCampaign->description }}</p>

                                @if($suggestCampaign->finishedAt)
                                <div class="d-flex align-items-center mb-4">
                                    <i class="bi bi-clock me-2" style="color: var(--accent-orange);"></i>
                                    <span style="color: var(--text-gray-400); font-size: 0.9rem;">
                                        [[Ends]]: {{ $suggestCampaign->finishedAt->format('M j, Y \a\t H:i') }}
                                    </span>
                                </div>
                                @endif

                                <div class="text-center">
                                    <form method="POST"
                                          action="@route(\App\Frontend\Profile\Controller::ROUTE_JOIN_CAMPAIGN)"
                                          class="d-inline"
                                    >
                                        <input type="hidden" name="campaign_uuid" value="{{ $suggestCampaign->uuid }}">
                                        <input type="hidden" name="username" value="{{ $user->login }}">
                                        <button type="submit" class="btn btn-lg px-4 py-2"
                                                style="background: linear-gradient(135deg, var(--accent-purple) 0%, var(--accent-blue) 100%);
                                                       border: none;
                                                       color: var(--text-white);
                                                       font-weight: 600;
                                                       border-radius: 12px;
                                                       box-shadow: 0 4px 15px var(--shadow-glow);
                                                       transition: all 0.3s ease;">
                                            <i class="bi bi-rocket-takeoff me-2"></i>
                                            [[Join Event]]
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Campaigns Carousel -->
                    @if(!empty($campaigns))
                    <div class="campaigns-carousel">
                        <div class="section-header mb-4 text-center">
                            <h2 style="color: var(--text-white); font-size: 1.5rem; font-weight: 600;">
                                <i class="bi bi-trophy me-2"></i>[[Events]]
                            </h2>
                        </div>

                        <div class="campaigns-scroll-container">
                            <div class="campaigns-track">
                                @foreach($campaigns as $userCampaign)
                                <div class="campaign-card">
                                    @if($userCampaign->user !== null)
                                        <div class="member-score">
                                            <i class="bi bi-star-fill me-1"></i>{{ $userCampaign->user->score }}
                                        </div>
                                    @endif

                                    <div class="campaign-card-header">
                                        <h3 class="campaign-card-title">{{ $userCampaign->campaign->title }}</h3>
                                        @if($userCampaign->user === null)
                                            <div class="campaign-card-status
                                                @if($userCampaign->finished)
                                                    campaign-status-ended
                                                @else
                                                    campaign-status-active
                                                @endif
                                            ">
                                                @if($userCampaign->finished)
                                                    [[Ended]]
                                                @else
                                                    [[NEW]]
                                                @endif
                                            </div>
                                        @endif
                                    </div>


                                    <div class="campaign-card-stats">
                                        <div class="campaign-stat">
                                            <i class="bi bi-folder"></i>
                                            <span class="campaign-stat-value">{{ $userCampaign->campaign->repositoryCount }}</span>
                                            <span>[[repos]]</span>
                                        </div>
                                        <div class="campaign-stat">
                                            <i class="bi bi-people"></i>
                                            <span class="campaign-stat-value">{{ $userCampaign->campaign->memberCount }}</span>
                                            <span>[[members]]</span>
                                        </div>
                                    </div>

                                    <div class="campaign-card-period">
                                        <i class="bi bi-calendar-event"></i>
                                        <span>{{ $userCampaign->campaign->startedAt->format('M j') }}
                                        @if($userCampaign->campaign->finishedAt)
                                            - {{ $userCampaign->campaign->finishedAt->format('M j, Y') }}
                                        @else
                                            - [[Ongoing]]
                                        @endif
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Repositories Section - Full Width -->
            <div class="container-fluid px-4">
                <div class="section-header mb-4 text-center">
                    <h2 style="color: var(--text-white); font-size: 1.5rem; font-weight: 600;">
                        <i class="bi bi-github me-2"></i>[[Projects that need support]]
                    </h2>
                </div>

                <!-- Repository Grid -->
                <div class="repo-grid">
                    @foreach($repositories as $repository)
                    <a href="{{ $repository->htmlUrl }}" target="_blank" class="repo-card {{ isset($stars[$repository->id]) ? 'starred' : '' }}"
                       style="text-decoration: none; color: inherit; position: relative; overflow: hidden; display: block;">

                        @if(isset($stars[$repository->id]))
                        <div class="star-badge">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        @endif

                        <div class="repo-header">
                            <img src="{{ $repository->owner->avatarUrl }}" alt="{{ $repository->fullName }}" class="repo-avatar">
                            <div>
                                <div class="repo-name">{{ $repository->name }}</div>
                                <div class="repo-owner">{{ $repository->owner->login }}</div>
                            </div>
                        </div>
                        <p class="repo-description">
                            {{ $repository->description }}
                        </p>
                        <div class="repo-footer">
                            <div class="repo-stats">
                                <div class="stat-item">
                                    <i class="bi bi-star"></i>
                                    {{ $repository->stargazersCount }}
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-diagram-3"></i>
                                    {{ $repository->forksCount }}
                                </div>
                            </div>

                            @if(!isset($stars[$repository->id]))
                                <div class="star-status not-starred">
                                    <i class="bi bi-star"></i>
                                    [[Put a star]]
                                </div>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</define:body>
