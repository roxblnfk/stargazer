<extends:layout title="[[Campaign List]]"/>

<stack:push name="styles">
</stack:push>

<stack:push name="scripts">
</stack:push>

<define:body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>[[Campaign List]]</h1>
            <a href="@route(\App\Backend\Campaign\Controller::ROUTE_CREATE)"
               class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> [[Add Campaign]]
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><i class="bi bi-card-text"></i> [[Title]]</th>
                        <th><i class="bi bi-calendar"></i> [[Period]]</th>
                        <th><i class="bi bi-people"></i> [[Stats]]</th>
                        <th><i class="bi bi-eye"></i> [[Status]]</th>
                        <th><i class="bi bi-gear"></i> [[Actions]]</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($campaigns))
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <h4 class="mt-3 text-muted">[[No campaigns found]]</h4>
                                <p class="text-muted">[[Create your first campaign to get started]]</p>
                                <a href="@route(\App\Backend\Campaign\Controller::ROUTE_CREATE)"
                                   class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> [[Add Campaign]]
                                </a>
                            </td>
                        </tr>
                    @else
                        @foreach($campaigns as $campaign)
                            <tr>
                                <td>
                                    <a href="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])"
                                       class="text-decoration-none fw-bold">
                                        {{ $campaign->title }}
                                    </a>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ $campaign->startedAt->format('M j, Y') }}
                                    @if($campaign->finishedAt)
                                        - {{ $campaign->finishedAt->format('M j, Y') }}
                                    @else
                                        - [[Ongoing]]
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div class="d-flex gap-3">
                                    <span class="badge bg-primary">
                                        {{ $campaign->repositoryCount }} [[repos]]
                                    </span>
                                    <span class="badge bg-success">
                                        {{ $campaign->memberCount }} [[members]]
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $campaign->visible ? 'success' : 'secondary' }}">
                                    {{ $campaign->visible ? '[[Visible]]' : '[[Hidden]]' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</define:body>
