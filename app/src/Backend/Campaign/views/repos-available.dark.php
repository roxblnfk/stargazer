<div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th><i class="bi bi-folder"></i> [[Repository]]</th>
                <th><i class="bi bi-star"></i> [[Stars]]</th>
                <th><i class="bi bi-calendar-plus"></i> [[Created]]</th>
                <th><i class="bi bi-gear"></i> [[Actions]]</th>
            </tr>
        </thead>
        <tbody>
            @if(empty($availableRepos))
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <i class="bi bi-folder-plus display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">[[No available repositories]]</h4>
                        <p class="text-muted">[[All repositories are already added to this campaign]]</p>
                    </td>
                </tr>
            @else
                @foreach($availableRepos as $index => $repo)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="text-muted">#{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <div class="fw-bold">
                                    <a href="{{ $repo->htmlUrl }}"
                                       target="_blank"
                                       class="text-decoration-none"
                                    >
                                        <i class="bi bi-github"></i>
                                    </a>
                                    {{ $repo->fullName }}
                                </div>
                                @if($repo->description)
                                    <small class="text-muted">{{ mb_strlen($repo->description) > 80 ? mb_substr($repo->description, 0, 80) . '...' : $repo->description }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong>{{ number_format($repo->stargazersCount) }}</strong>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="bi bi-calendar-event"></i>
                            {{ $repo->createdAt->format('M j, Y') }}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ $repo->htmlUrl }}"
                               target="_blank"
                               class="btn btn-outline-primary"
                               title="[[View Repository]]">
                                <i class="bi bi-github"></i>
                            </a>
                            <button class="btn btn-outline-success"
                                    title="[[Add to Campaign]]"
                                    hx-post="@route(\App\Backend\Campaign\Controller::ROUTE_REPO_ADD, ['uuid' => $campaign->uuid, 'repoId' => $repo->id])"
                                    hx-target="closest tr"
                                    hx-swap="outerHTML">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@if(!empty($availableRepos))
<div class="card-footer text-muted">
    <div class="row align-items-center">
        <div class="col-md-6">
            <small>
                <i class="bi bi-info-circle"></i>
                [[Showing]] {{ count($availableRepos) }} [[available repositories]]
            </small>
        </div>
        <div class="col-md-6 text-end">
            <small>
                [[Total Stars]]:
                <strong>{{ number_format(array_sum(array_column($availableRepos, 'stargazersCount'))) }}</strong>
            </small>
        </div>
    </div>
</div>
@endif