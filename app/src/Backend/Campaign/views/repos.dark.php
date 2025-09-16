<extends:layout title="[[Campaign Repositories]] - {{ $campaign->title }}"/>

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
                    <li class="breadcrumb-item">
                        <a href="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])" class="text-decoration-none">
                            {{ $campaign->title }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">[[Repositories]]</li>
                </ol>
            </nav>

            <div class="btn-group">
                <a href="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])"
                   class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> [[Back to Campaign]]
                </a>
            </div>
        </div>

        <!-- Repositories Tabs -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-folder"></i> [[Campaign Repositories]]
                    </h3>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs card-header-tabs mt-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                                hx-get="@route(\App\Backend\Campaign\Controller::ROUTE_REPOS_ADDED, ['uuid' => $campaign->uuid])"
                                hx-target="#repos-content"
                                hx-swap="innerHTML"
                                type="button"
                                onclick="document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active')); this.classList.add('active');">
                            <i class="bi bi-check-circle"></i> [[Added Repositories]]
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link"
                                hx-get="@route(\App\Backend\Campaign\Controller::ROUTE_REPOS_AVAILABLE, ['uuid' => $campaign->uuid])"
                                hx-target="#repos-content"
                                hx-swap="innerHTML"
                                type="button"
                                onclick="document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active')); this.classList.add('active');">
                            <i class="bi bi-plus-circle"></i> [[Available Repositories]]
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div id="repos-content" class="card-body p-0">
                <!-- Default content will be loaded here via HTMX -->
                <div hx-get="@route(\App\Backend\Campaign\Controller::ROUTE_REPOS_ADDED, ['uuid' => $campaign->uuid])"
                     hx-trigger="load"
                     hx-swap="outerHTML">
                    <div class="p-4 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">[[Loading]]...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>