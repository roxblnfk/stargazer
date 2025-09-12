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
</style>
<link href="/styles/profile.css" rel="stylesheet" type="text/css" />
</stack:push>

<define:body>
    <div class="min-vh-100 cosmic-bg position-relative">
        <div class="container py-4">
            <a href="@route(\App\Feature\Index\Controller::ROUTE_INDEX)" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                [[Back]]
            </a>

            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="row align-items-center">
                            <div class="col-md-auto text-center text-md-start">
                                <img src="https://github.com/lee-to.png" alt="Avatar" class="profile-avatar">
                            </div>
                            <div class="col-md flex-grow-1 text-center text-md-start mt-3 mt-md-0">
                                <h1 class="profile-name">Lee To</h1>
                                <p class="profile-username">@lee-to</p>
                            </div>
                            <div class="col-md-auto">
                                <div class="stats-card">
                                    <span class="stats-number">1,247</span>
                                    <span class="stats-label">[[Points]]</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Repositories Section -->
                    <div class="section-header mb-4">
                        <h2 style="color: var(--text-white); font-size: 1.5rem; font-weight: 600;">
                            <i class="bi bi-github me-2"></i>[[Tracking repositories]]
                        </h2>
                        <p style="color: var(--text-gray-400); margin: 0;">[[Projects for which you can get points]]</p>
                    </div>

                    <!-- Repository Grid -->
                    <div class="repo-grid">
                        @foreach($repositories as $repository)
                        <div class="repo-card">
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

                                <!--<div class="star-status starred">
                                    <i class="bi bi-check-circle"></i>
                                    [[Star set]]
                                </div>-->

                                <a href="{{ $repository->htmlUrl }}" class="star-status not-starred">
                                    <i class="bi bi-star"></i>
                                    [[Put a star]]
                                </a>
                            </div>

                            @if($repository->homepage)
                            <div class="mt-3">
                                <a href="{{ $repository->homepage }}" target="_blank" class="visit-btn">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                    [[More]]
                                </a>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
