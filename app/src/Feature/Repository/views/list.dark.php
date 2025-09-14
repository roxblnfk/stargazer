<extends:layout title="[[Repository List]]"/>

<stack:push name="styles">
</stack:push>

<stack:push name="scripts">
</stack:push>

<define:body>
    <div class="container py-4">
        <h1 class="mb-4">[[Repository List]]</h1>

        <form hx-post="@route(\App\Feature\Repository\Controller::ROUTE_ACTIVATE)"
              hx-on::after-request="if(event.detail.xhr.status === 200) location.reload()"
              hx-swap="none"
              class="mb-4"
        >
            <div class="input-group">
                <input
                    type="text"
                    class="form-control"
                    name="repository_name"
                    placeholder="[[owner/repository]]"
                    required
                >
                <button class="btn btn-primary" type="submit">
                    [[Add]]
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><i class="bi bi-folder2-open"></i> [[Repository]]</th>
                        <th><i class="bi bi-star"></i> [[Stars]]</th>
                        <th><i class="bi bi-clock"></i> [[Last Updated]]</th>
                        <th><i class="bi bi-gear"></i> [[Actions]]</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($repositories))
                        <tr>
                            <td colspan="4" class="text-muted text-center">
                                <i class="bi bi-inbox"></i> [[No repositories tracked yet.]]
                            </td>
                        </tr>
                    @else
                        @foreach($repositories as $repository)
                            <tr>
                                <td>
                                    <i class="bi bi-github"></i>
                                    <a href="@route(\App\Feature\Repository\Controller::ROUTE_INFO, ['owner' => $repository->owner, 'name' => $repository->name])"
                                       class="text-decoration-none">{{ $repository }}</a>
                                </td>
                                <td>
                                    @if($repository->info)
                                        <span class="badge" style="color: gold; background: transparent;">
                                            <i class="bi bi-star-fill"></i> {{ number_format($repository->info->stargazersCount) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-star"></i> --
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($repository->info)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3"></i> {{ $repository->info->updatedAt->format('M j, Y') }}
                                        </small>
                                    @else
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3"></i> --
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary"
                                                title="[[Refresh]]"
                                                hx-post="@route(\App\Feature\Repository\Controller::ROUTE_TOUCH)"
                                                hx-vals='{"repository_name": "{{ $repository }}"}'
                                                hx-target="closest tr"
                                                hx-swap="none">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <button class="btn btn-outline-success"
                                                title="[[Reactivate]]"
                                                hx-post="@route(\App\Feature\Repository\Controller::ROUTE_ACTIVATE)"
                                                hx-vals='{"repository_name": "{{ $repository }}"}'
                                                hx-target="closest tr"
                                                hx-swap="none">
                                            <i class="bi bi-arrow-up-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-warning"
                                                title="[[Deactivate]]"
                                                hx-post="@route(\App\Feature\Repository\Controller::ROUTE_DEACTIVATE)"
                                                hx-vals='{"repository_name": "{{ $repository }}"}'
                                                hx-target="closest tr"
                                                hx-swap="none">
                                            <i class="bi bi-arrow-down-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary hide-btn @if(!$repository->active) d-none @endif"
                                                title="[[Hide]]"
                                                hx-post="@route(\App\Feature\Repository\Controller::ROUTE_HIDE)"
                                                hx-vals='{"repository_name": "{{ $repository }}"}'
                                                hx-swap="none"
                                                hx-on::after-request="if(JSON.parse(event.detail.xhr.response).visible === false) { this.classList.add('d-none'); this.parentElement.querySelector('.show-btn').classList.remove('d-none'); }">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                        <button class="btn btn-outline-info show-btn @if($repository->active) d-none @endif"
                                                title="[[Show]]"
                                                hx-post="@route(\App\Feature\Repository\Controller::ROUTE_SHOW)"
                                                hx-vals='{"repository_name": "{{ $repository }}"}'
                                                hx-swap="none"
                                                hx-on::after-request="if(JSON.parse(event.detail.xhr.response).visible === true) { this.classList.add('d-none'); this.parentElement.querySelector('.hide-btn').classList.remove('d-none'); }">
                                            <i class="bi bi-eye"></i>
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
