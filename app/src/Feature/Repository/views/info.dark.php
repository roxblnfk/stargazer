<extends:layout title="Repository Info - {{ $repository->fullName }}"/>

<stack:push name="styles">
</stack:push>

<define:body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="@route(\App\Feature\Repository\Controller::ROUTE_LIST)" class="text-decoration-none">
                            <i class="bi bi-list"></i> [[Repository List]]
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $repository->fullName }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h2 class="card-title mb-0">
                            <i class="bi bi-github"></i>
                            <a href="{{ $repository->htmlUrl }}" target="_blank" class="text-decoration-none">
                                {{ $repository->fullName }}
                            </a>
                            @if($repository->private)
                            <span class="badge bg-warning text-dark ms-2">
                                    <i class="bi bi-lock"></i> Private
                                </span>
                            @endif
                            @if($repository->fork)
                            <span class="badge bg-info ms-2">
                                    <i class="bi bi-git"></i> Fork
                                </span>
                            @endif
                        </h2>
                        <button class="btn btn-outline-primary btn-sm"
                                title="Refresh"
                                hx-post="@route(\App\Feature\Repository\Controller::ROUTE_TOUCH)"
                                hx-vals='{"repository_name": "{{ $repository->fullName }}"}'
                                hx-swap="none">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        @if($repository->description)
                        <p class="text-muted mb-3">{{ $repository->description }}</p>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-star-fill text-warning me-2"></i>
                                    <strong>{{ $repository->stargazersCount }}</strong>
                                    <span class="text-muted ms-1">stars</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-git text-primary me-2"></i>
                                    <strong>{{ $repository->forksCount }}</strong>
                                    <span class="text-muted ms-1">forks</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-eye text-info me-2"></i>
                                    <strong>{{ $repository->watchersCount }}</strong>
                                    <span class="text-muted ms-1">watchers</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-circle text-danger me-2"></i>
                                    <strong>{{ $repository->openIssuesCount }}</strong>
                                    <span class="text-muted ms-1">open issues</span>
                                </div>
                            </div>
                        </div>

                        @if($repository->topics)
                        <div class="mt-3">
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($repository->topics as $topic)
                                <span class="badge bg-secondary">{{ $topic }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($repository->language)
                        <div class="mt-3">
                                <span class="badge bg-primary">
                                    <i class="bi bi-code"></i> {{ $repository->language }}
                                </span>
                        </div>
                        @endif

                        @if($repository->homepage)
                        <div class="mt-3">
                            <a href="{{ $repository->homepage }}" target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-link-45deg"></i> [[Homepage]]
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-person-circle"></i> [[Owner Information]]
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="{{ $repository->owner->avatarUrl }}"
                                 alt="{{ $repository->owner->login }}"
                                 class="me-3"
                                 width="48"
                                 height="48">
                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ $repository->owner->htmlUrl }}" target="_blank"
                                       class="text-decoration-none">
                                        {{ $repository->owner->login }}
                                    </a>
                                </h5>
                                <small class="text-muted">ID: {{ $repository->owner->id }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> [[Repository Details]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>[[Created]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-plus"></i>
                                {{ $repository->createdAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Last Updated]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-check"></i>
                                {{ $repository->updatedAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Last Push]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-upload"></i>
                                {{ $repository->pushedAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Default Branch]]:</strong><br>
                            <span class="badge bg-success">{{ $repository->defaultBranch }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>[[Size]]:</strong><br>
                            <small class="text-muted">{{ $repository->size }} KB</small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Visibility]]:</strong><br>
                            <span
                                class="badge {{ $repository->visibility === 'public' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $repository->visibility }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-gear"></i> [[Features]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between">
                                <span>[[Issues]]:</span>
                                <i class="bi {{ $repository->hasIssues ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Projects]]:</span>
                                <i class="bi {{ $repository->hasProjects ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Wiki]]:</span>
                                <i class="bi {{ $repository->hasWiki ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Pages]]:</span>
                                <i class="bi {{ $repository->hasPages ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Downloads]]:</span>
                                <i class="bi {{ $repository->hasDownloads ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Discussions]]:</span>
                                <i class="bi {{ $repository->hasDiscussions ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-link-45deg"></i> [[Clone URLs]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small">HTTPS:</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" value="{{ $repository->cloneUrl }}" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="navigator.clipboard.writeText('{{ $repository->cloneUrl }}')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">SSH:</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" value="{{ $repository->sshUrl }}" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="navigator.clipboard.writeText('{{ $repository->sshUrl }}')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
