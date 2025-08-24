<extends:layout title="[[Repository List]]"/>

<stack:push name="styles">
</stack:push>

<define:body>
    <div class="container py-4">
        <h1 class="mb-4">[[Repository List]]</h1>

        <form method="POST"
              action="@route(\App\Feature\Repository\Controller::ROUTE_ADD)"
              class="mb-4"
        >
            <div class="input-group">
                <input
                    type="text"
                    class="form-control"
                    name="repository_name"
                    placeholder="owner/repository"
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
                        <th><i class="bi bi-folder2-open"></i> Repository</th>
                        <th><i class="bi bi-star"></i> Stars</th>
                        <th><i class="bi bi-clock"></i> Last Updated</th>
                        <th><i class="bi bi-gear"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($repositories))
                        <tr>
                            <td colspan="4" class="text-muted text-center">
                                <i class="bi bi-inbox"></i> No repositories tracked yet.
                            </td>
                        </tr>
                    @else
                        @foreach($repositories as $repository)
                            <tr>
                                <td>
                                    <i class="bi bi-github"></i>
                                    <a href="@route(\App\Feature\Repository\Controller::ROUTE_INFO, ['owner' => $repository->owner->name, 'name' => $repository->name])"
                                       class="text-decoration-none">{{ $repository }}</a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-star"></i> --
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3"></i> --
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Refresh">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <button class="btn btn-outline-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Remove">
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
