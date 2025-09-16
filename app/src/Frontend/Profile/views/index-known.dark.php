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
