<extends:layout title="[[Admin Dashboard]]"/>

<stack:push name="styles">
    <style>
        .card-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</stack:push>

<stack:push name="scripts">
</stack:push>

<define:body>
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="bi bi-speedometer2"></i> [[Admin Dashboard]]
                </h1>

                <div class="row g-4">
                    <!-- Quick Stats -->
                    <div class="col-xl-3 col-md-6">
                        <a href="@route(\App\Backend\Repository\Controller::ROUTE_LIST)" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 card-hover">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-folder2-open fs-2 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">[[Repositories]]</h6>
                                            <h3 class="mb-0 text-dark">{{ $dashboard->countRepositories }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-star fs-2 text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">[[Total Stars]]</h6>
                                        <h3 class="mb-0">{{ $dashboard->countStars }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-people fs-2 text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">[[Stargazers]]</h6>
                                        <h3 class="mb-0">{{ $dashboard->countUsers }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
