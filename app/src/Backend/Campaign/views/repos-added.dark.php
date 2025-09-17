<div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th><i class="bi bi-folder"></i> [[Repository]]</th>
                <th><i class="bi bi-award"></i> [[Value]]</th>
                <th><i class="bi bi-star"></i> [[Stars]]</th>
                <th><i class="bi bi-calendar-plus"></i> [[Added]]</th>
                <th><i class="bi bi-gear"></i> [[Actions]]</th>
            </tr>
        </thead>
        <tbody>
            @if(empty($addedRepos))
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-folder display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">[[No added repositories]]</h4>
                        <p class="text-muted">[[This campaign has no repositories added yet]]</p>
                    </td>
                </tr>
            @else
                @foreach($addedRepos as $index => $repo)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="text-muted">#{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <div class="fw-bold">
                                    <a href="https://github.com/{{ $repo->repoName }}"
                                       target="_blank"
                                       class="text-decoration-none"
                                    >
                                        <i class="bi bi-github"></i>
                                    </a>
                                    {{ $repo->repoName }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary btn-sm me-1"
                                    title="[[Decrease score]]"
                                    hx-post="@route(\App\Backend\Campaign\Controller::ROUTE_REPO_SCORE, ['uuid' => $repo->campaignUuid, 'repoId' => $repo->repoId])"
                                    hx-vals='{"change": -1}'
                                    hx-target="next .score-value"
                                    hx-swap="innerHTML"
                            >
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="score-value mx-2">
                                <strong>{{ $repo->score }}</strong>
                            </span>
                            <button class="btn btn-outline-secondary btn-sm ms-1"
                                    title="[[Increase score]]"
                                    hx-post="@route(\App\Backend\Campaign\Controller::ROUTE_REPO_SCORE, ['uuid' => $repo->campaignUuid, 'repoId' => $repo->repoId])"
                                    hx-vals='{"change": 1}'
                                    hx-target="previous .score-value"
                                    hx-swap="innerHTML"
                            >
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="text-muted">—</span>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="bi bi-calendar-event"></i>
                            {{ $repo->createdAt->format('M j, Y H:i') }}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-danger"
                                    title="[[Remove from Campaign]]"
                                    hx-delete="@route(\App\Backend\Campaign\Controller::ROUTE_REPO_REMOVE, ['uuid' => $repo->campaignUuid, 'owner' => $repo->repoName->owner, 'name' => $repo->repoName->name])"
                                    hx-target="closest tr"
                                    hx-swap="outerHTML"
                            >
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

@if(!empty($addedRepos))
<div class="card-footer text-muted">
    <div class="row align-items-center">
        <div class="col-md-6">
            <small>
                <i class="bi bi-info-circle"></i>
                [[Showing]] {{ count($addedRepos) }} [[added repositories]]
            </small>
        </div>
        <div class="col-md-6 text-end">
            <small>
                [[Total Value]]:
                <strong>{{ array_sum(array_column($addedRepos, 'score')) }}</strong>
            </small>
        </div>
    </div>
</div>
@endif
