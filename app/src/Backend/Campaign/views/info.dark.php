<extends:layout title="[[Campaign]] - {{ $campaign->title }}"/>

<stack:push name="styles">
</stack:push>

<stack:push name="scripts">
</stack:push>

<define:body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="@route(\App\Backend\Campaign\Controller::ROUTE_LIST)" class="text-decoration-none">
                            <i class="bi bi-list"></i> [[Campaign List]]
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $campaign->title }}</li>
                </ol>
            </nav>

            <div class="btn-group">
                <a href="@route(\App\Backend\Campaign\Controller::ROUTE_EDIT, ['uuid' => $campaign->uuid])"
                   class="btn btn-warning">
                    <i class="bi bi-pencil"></i> [[Edit]]
                </a>
                <button class="btn btn-outline-danger"
                        hx-post="@route(\App\Backend\Campaign\Controller::ROUTE_DELETE, ['uuid' => $campaign->uuid])"
                        hx-confirm="[[Are you sure you want to delete this campaign?]]"
                        hx-swap="none"
                        hx-on::after-request="if(event.detail.xhr.status === 200) window.location.href = '@route(\App\Backend\Campaign\Controller::ROUTE_LIST)'">
                    <i class="bi bi-trash"></i> [[Delete]]
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Main Information Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h2 class="card-title mb-0">
                            {{ $campaign->title }}
                            <span class="badge bg-{{ $campaign->visible ? 'success' : 'secondary' }} ms-2">
                                {{ $campaign->visible ? '[[Visible]]' : '[[Hidden]]' }}
                            </span>
                        </h2>
                    </div>
                    <div class="card-body">
                        <!-- Stats Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-folder text-primary me-2"></i>
                                    <strong>{{ $campaign->repositoryCount }}</strong>
                                    <span class="text-muted ms-1">[[repositories]]</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people text-success me-2"></i>
                                    <strong>{{ $campaign->memberCount }}</strong>
                                    <span class="text-muted ms-1">[[members]]</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                @if($campaign->description)
                <!-- Campaign Description Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> [[Description]]
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="markdown-content" id="campaign-description">{{ $campaign->description }}</div>
                    </div>
                </div>
                @endif

                <!-- Campaign Duration Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-clock"></i> [[Campaign Duration]]
                        </h3>
                    </div>
                    <div class="card-body">
                        @php
                            $now = new \DateTime();
                            $isActive = $campaign->startedAt <= $now && ($campaign->finishedAt === null || $campaign->finishedAt >= $now);
                            $isUpcoming = $campaign->startedAt > $now;
                            $isFinished = $campaign->finishedAt !== null && $campaign->finishedAt < $now;
                        @endphp

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="badge bg-{{ $isUpcoming ? 'warning' : 'secondary' }} fs-6 mb-2">
                                        @if($isUpcoming)
                                            <i class="bi bi-hourglass-split"></i> [[Upcoming]]
                                        @elseif($isActive)
                                            <i class="bi bi-play-circle"></i> [[Active]]
                                        @else
                                            <i class="bi bi-check-circle"></i> [[Finished]]
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <small class="text-muted d-block">[[Total Duration]]</small>
                                    <strong>
                                        @if($campaign->finishedAt)
                                            {{ $campaign->startedAt->diff($campaign->finishedAt)->format('%a [[days]]') }}
                                        @else
                                            [[Indefinite]]
                                        @endif
                                    </strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <small class="text-muted d-block">
                                        @if($isActive)
                                            [[Time Remaining]]
                                        @elseif($isUpcoming)
                                            [[Starts In]]
                                        @else
                                            [[Ended]]
                                        @endif
                                    </small>
                                    <strong>
                                        @if($isUpcoming)
                                            {{ $now->diff($campaign->startedAt)->format('%a [[days]]') }}
                                        @elseif($isActive && $campaign->finishedAt)
                                            {{ $now->diff($campaign->finishedAt)->format('%a [[days]]') }}
                                        @elseif($isFinished)
                                            {{ $campaign->finishedAt->diff($now)->format('%a [[days ago]]') }}
                                        @else
                                            --
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Technical Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> [[Technical Details]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>[[UUID]]:</strong><br>
                            <small class="text-muted font-monospace">{{ $campaign->uuid }}</small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Created]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-plus"></i>
                                {{ $campaign->createdAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Last Updated]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-check"></i>
                                {{ $campaign->updatedAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Campaign Phase]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-range"></i>
                                {{ $campaign->startedAt->format('Y-m-d H:i') }}
                                @if($campaign->finishedAt)
                                    — {{ $campaign->finishedAt->format('Y-m-d H:i') }}
                                @else
                                    — [[Ongoing]]
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-lightning"></i> [[Quick Actions]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="@route(\App\Backend\Campaign\Controller::ROUTE_EDIT, ['uuid' => $campaign->uuid])"
                               class="btn btn-warning">
                                <i class="bi bi-pencil"></i> [[Edit Campaign]]
                            </a>
                            <button class="btn btn-{{ $campaign->visible ? 'outline-secondary' : 'outline-success' }}"
                                    hx-post="@route(\App\Backend\Campaign\Controller::ROUTE_TOGGLE_VISIBILITY, ['uuid' => $campaign->uuid])"
                                    hx-on::after-request="location.reload()">
                                <i class="bi bi-{{ $campaign->visible ? 'eye-slash' : 'eye' }}"></i>
                                {{ $campaign->visible ? '[[Hide Campaign]]' : '[[Show Campaign]]' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
