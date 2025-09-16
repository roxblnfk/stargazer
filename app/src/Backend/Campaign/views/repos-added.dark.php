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
                                    <a href="{{ $repo->repoName->htmlUrl }}"
                                       target="_blank"
                                       class="text-decoration-none"
                                    >
                                        <i class="bi bi-github"></i>
                                    </a>
                                    {{ $repo->repoName->fullName }}
                                </div>
                                @if($repo->repoName->description)
                                    <small class="text-muted">{{ mb_strlen($repo->repoName->description) > 80 ? mb_substr($repo->repoName->description, 0, 80) . '...' : $repo->repoName->description }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong>{{ $repo->score }}</strong>
                    </td>
                    <td>
                        <strong>{{ number_format($repo->repoName->stargazersCount) }}</strong>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="bi bi-calendar-event"></i>
                            {{ $repo->createdAt->format('M j, Y H:i') }}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ $repo->repoName->htmlUrl }}"
                               target="_blank"
                               class="btn btn-outline-primary"
                               title="[[View Repository]]">
                                <i class="bi bi-github"></i>
                            </a>
                            <button class="btn btn-outline-danger"
                                    title="[[Remove from Campaign]]"
                                    hx-delete="@route(\App\Backend\Campaign\Controller::ROUTE_REPO_REMOVE, ['uuid' => $repo->campaignUuid, 'repoId' => $repo->repoId])"
                                    hx-confirm="[[Are you sure you want to remove this repository from the campaign?]]"
                                    hx-target="closest tr"
                                    hx-swap="outerHTML">
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