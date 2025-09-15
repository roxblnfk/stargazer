<extends:layout title="{{ isset($campaign) ? '[[Edit Campaign]]' : '[[Create Campaign]]' }} - {{ $campaign->title ?? '[[New Campaign]]' }}"/>

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
                    @if(isset($campaign))
                        <li class="breadcrumb-item">
                            <a href="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])" class="text-decoration-none">
                                {{ $campaign->title }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">[[Edit]]</li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">[[Create Campaign]]</li>
                    @endif
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title mb-0">
                            <i class="bi bi-{{ isset($campaign) ? 'pencil' : 'plus-circle' }}"></i>
                            {{ isset($campaign) ? '[[Edit Campaign]]' : '[[Create Campaign]]' }}
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="@route(isset($campaign) ? \App\Backend\Campaign\Controller::ROUTE_UPDATE : \App\Backend\Campaign\Controller::ROUTE_STORE)">

                            @if(isset($campaign))
                                <input type="hidden" name="uuid" value="{{ $campaign->uuid }}">
                            @endif

                            <!-- Title Field -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    [[Title]] <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="title"
                                       name="title"
                                       value="{{ $campaign->title ?? '' }}"
                                       required
                                       maxlength="255"
                                       placeholder="[[Enter campaign title]]">
                                <div class="form-text">
                                    [[Campaign title should be clear and descriptive]]
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div class="mb-3">
                                <label for="description" class="form-label">[[Description]]</label>
                                <textarea class="form-control"
                                          id="description"
                                          name="description"
                                          rows="8"
                                          placeholder="[[Describe the purpose and goals of this campaign]]">{{ $campaign->description ?? '' }}</textarea>
                                <div class="form-text">
                                    [[Detailed description helps users understand campaign goals]]
                                </div>
                            </div>

                            @if(isset($campaign))
                                <!-- Visibility Toggle -->
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="visible"
                                               name="visible"
                                               value="1"
                                               {{ ($campaign->visible ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="visible">
                                            <strong>[[Visible to users]]</strong>
                                        </label>
                                        <div class="form-text">
                                            [[Hidden campaigns are not visible to regular users]]
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Date Range Section -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-calendar-range"></i> [[Campaign Period]]
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Start Date -->
                                        <div class="col-md-6 mb-3">
                                            <label for="started_at" class="form-label">
                                                [[Start Date]] <span class="text-danger">*</span>
                                            </label>
                                            <input type="datetime-local"
                                                   class="form-control"
                                                   id="started_at"
                                                   name="started_at"
                                                   value="{{ isset($campaign) ? $campaign->startedAt->format('Y-m-d\TH:i') : '' }}"
                                                   required>
                                        </div>

                                        <!-- End Date (Optional) -->
                                        <div class="col-md-6 mb-3">
                                            <label for="finished_at" class="form-label">
                                                [[End Date]] <small class="text-muted">([[Optional]])</small>
                                            </label>
                                            <input type="datetime-local"
                                                   class="form-control"
                                                   id="finished_at"
                                                   name="finished_at"
                                                   value="{{ isset($campaign) && $campaign->finishedAt ? $campaign->finishedAt->format('Y-m-d\TH:i') : '' }}">
                                            <div class="form-text">
                                                [[Leave empty for indefinite campaign]]
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Read-only Information (for editing) -->
                            @if(isset($campaign))
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-info-circle"></i> [[Current Statistics]]
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-folder text-primary me-2"></i>
                                                        <strong class="fs-4">{{ $campaign->repositoryCount }}</strong>
                                                    </div>
                                                    <small class="text-muted">[[repositories]]</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-people text-success me-2"></i>
                                                        <strong class="fs-4">{{ $campaign->memberCount }}</strong>
                                                    </div>
                                                    <small class="text-muted">[[members]]</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-calendar-check text-info me-2"></i>
                                                        <strong class="fs-6">{{ $campaign->updatedAt->format('M j') }}</strong>
                                                    </div>
                                                    <small class="text-muted">[[last updated]]</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="@route(isset($campaign) ? \App\Backend\Campaign\Controller::ROUTE_INFO : \App\Backend\Campaign\Controller::ROUTE_LIST, isset($campaign) ? ['uuid' => $campaign->uuid] : [])"
                                   class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> [[Cancel]]
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-{{ isset($campaign) ? 'save' : 'plus-circle' }}"></i>
                                    {{ isset($campaign) ? '[[Update Campaign]]' : '[[Create Campaign]]' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
