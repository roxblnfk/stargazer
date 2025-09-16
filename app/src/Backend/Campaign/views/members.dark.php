<extends:layout title="[[Campaign Members]] - {{ $campaign->title }}"/>

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
                    <li class="breadcrumb-item active" aria-current="page">[[Members]]</li>
                </ol>
            </nav>

            <div class="btn-group">
                <a href="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])"
                   class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> [[Back to Campaign]]
                </a>
            </div>
        </div>


        <!-- Members Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="bi bi-people"></i> [[Members List]]
                </h3>
                <div class="badge bg-info">
                    {{ count($members) }} [[total]]
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="bi bi-person"></i> [[User]]</th>
                                <th><i class="bi bi-award"></i> [[Score]]</th>
                                <th><i class="bi bi-star"></i> [[Stars]]</th>
                                <th><i class="bi bi-calendar-plus"></i> [[Joined]]</th>
                                <th><i class="bi bi-gear"></i> [[Actions]]</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($members))
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-people display-1 text-muted"></i>
                                        <h4 class="mt-3 text-muted">[[No members found]]</h4>
                                        <p class="text-muted">[[This campaign has no members yet]]</p>
                                    </td>
                                </tr>
                            @else
                                @foreach($members as $index => $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <span class="text-muted">#{{ $index + 1 }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-bold">
                                                    <a href="https://github.com/{{ $member->userName->name }}"
                                                       target="_blank"
                                                       class="text-decoration-none"
                                                    >
                                                        <i class="bi bi-github"></i>
                                                    </a>
                                                    {{ $member->userName->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $member->score }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $member->stars }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-event"></i>
                                            {{ $member->createdAt->format('M j, Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="https://github.com/{{ $member->userName->name }}"
                                               target="_blank"
                                               class="btn btn-outline-primary"
                                               title="[[View GitHub Profile]]">
                                                <i class="bi bi-github"></i>
                                            </a>
                                            <button class="btn btn-outline-info"
                                                    title="[[View User Details]]"
                                                    disabled>
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if(!empty($members))
            <div class="card-footer text-muted">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            [[Showing all]] {{ count($members) }} [[members]]
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small>
                            [[Total Score]]:
                            <strong>{{ array_sum(array_column($members, 'score')) }}</strong>
                        </small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</define:body>
