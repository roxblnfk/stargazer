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
                                    @if($campaign->description)
                                        <br><small class="text-muted">{{ mb_strlen($campaign->description) > 80 ? mb_substr($campaign->description, 0, 80) . '...' : $campaign->description }}</small>
                                    @endif
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
                                    <a href="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])"
                                       class="btn btn-outline-primary" title="[[View Details]]">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="@route(\App\Backend\Campaign\Controller::ROUTE_EDIT, ['uuid' => $campaign->uuid])"
                                       class="btn btn-outline-warning" title="[[Edit]]">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-success"
                                            title="{{ $campaign->visible ? '[[Hide]]' : '[[Show]]' }}"
                                            hx-post="@route(\App\Backend\Campaign\Controller::ROUTE_TOGGLE_VISIBILITY, ['uuid' => $campaign->uuid])"
                                            hx-target="closest td"
                                            hx-swap="innerHTML"
                                            hx-on::after-request="location.reload()">
                                        <i class="bi bi-{{ $campaign->visible ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="[[Delete]]"
                                            hx-delete="@route(\App\Backend\Campaign\Controller::ROUTE_DELETE, ['uuid' => $campaign->uuid])"
                                            hx-confirm="[[Are you sure you want to delete this campaign?]]"
                                            hx-target="closest tr" hx-swap="outerHTML">
                                        <i class="bi bi-trash"></i>
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
</define:body>
