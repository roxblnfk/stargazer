<extends:layout title="[[Stargazer]]"/>

<stack:push name="styles">
<!--    <link rel="stylesheet" href="/styles/welcome.css"/>-->
</stack:push>

<define:body>
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <form class="d-flex flex-column align-items-center">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control" placeholder="[[username]]">
                    <button class="btn btn-primary" type="submit">
                        [[Go]]
                    </button>
                </div>
            </form>

            <div class="mt-3">
                <a href="@route(\App\Feature\Repository\Controller::ROUTE_LIST)" class="text-muted">[[Repository List]]</a>
            </div>
        </div>
    </div>
</define:body>
