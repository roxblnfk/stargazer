<extends:layout title="Repository Info - {{ $repository->fullName }}"/>

<stack:push name="styles">
</stack:push>

<stack:push name="scripts">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script>
        let starsChart;

        function initializeChart() {
            const chartCanvas = document.getElementById('starsChart');
            if (!chartCanvas) return;

            const ctx = chartCanvas.getContext('2d');
            starsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Total Stars',
                        data: [],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0,
                        pointBackgroundColor: '#007bff',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#007bff',
                            borderWidth: 1
                        },
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true
                                },
                                drag: {
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                    borderColor: '#007bff',
                                    borderWidth: 1,
                                    modifierKey: null
                                },
                                mode: 'x',
                                onZoomComplete: function({chart}) {
                                    adjustYScale(chart);
                                    console.log('Zoom completed:', {
                                        min: chart.scales.x.min,
                                        max: chart.scales.x.max
                                    });
                                }
                            },
                            pan: {
                                enabled: true,
                                mode: 'x',
                                onPanComplete: function({chart}) {
                                    adjustYScale(chart);
                                    console.log('Pan completed:', {
                                        min: chart.scales.x.min,
                                        max: chart.scales.x.max
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Total Stars'
                            },
                            beginAtZero: false, // Позволяет графику адаптироваться к данным
                            min: 0, // Минимальное значение всегда 0 или больше
                            grace: '5%', // Добавляет небольшой отступ сверху и снизу
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        function updateChart(data) {
            if (starsChart && data.labels && data.data) {
                starsChart.data.labels = data.labels;
                starsChart.data.datasets[0].data = data.data;
                starsChart.update('none');
            }
        }

        function adjustYScale(chart) {
            if (!chart || !chart.data.datasets[0].data) return;

            const xScale = chart.scales.x;
            const data = chart.data.datasets[0].data;

            // Получаем индексы видимого диапазона
            const startIndex = Math.max(0, Math.floor(xScale.min) || 0);
            const endIndex = Math.min(data.length - 1, Math.ceil(xScale.max) || data.length - 1);

            // Находим мин/макс значения в видимом диапазоне
            let minValue = Infinity;
            let maxValue = -Infinity;

            for (let i = startIndex; i <= endIndex; i++) {
                const value = data[i];
                if (value !== null && value !== undefined) {
                    minValue = Math.min(minValue, value);
                    maxValue = Math.max(maxValue, value);
                }
            }

            if (minValue === Infinity || maxValue === -Infinity) return;

            // Добавляем небольшой отступ (5% от диапазона)
            const range = maxValue - minValue;
            const padding = Math.max(1, range * 0.05);

            // Устанавливаем новые границы Y-оси
            chart.scales.y.options.min = Math.max(0, minValue - padding);
            chart.scales.y.options.max = maxValue + padding;

            // Обновляем график
            chart.update('none');
        }

        function resetZoom() {
            if (starsChart) {
                // Сброс осей X и Y
                starsChart.resetZoom();

                // Возвращаем Y-ось к автоподстройке, но сохраняем минимум = 0
                starsChart.scales.y.options.min = 0;
                starsChart.scales.y.options.max = undefined;
                starsChart.update('none');
            }
        }

        function zoomToLast30Days() {
            if (starsChart && starsChart.data.labels && starsChart.data.labels.length > 0) {
                const labels = starsChart.data.labels;
                const totalDays = labels.length;
                const last30Days = Math.min(30, totalDays);
                const startIndex = Math.max(0, totalDays - last30Days);

                starsChart.zoomScale('x', {
                    min: startIndex,
                    max: totalDays - 1
                });

                // Подстраиваем Y-ось под выбранный период
                setTimeout(() => adjustYScale(starsChart), 50);
            }
        }

        function zoomToLast7Days() {
            if (starsChart && starsChart.data.labels && starsChart.data.labels.length > 0) {
                const labels = starsChart.data.labels;
                const totalDays = labels.length;
                const last7Days = Math.min(7, totalDays);
                const startIndex = Math.max(0, totalDays - last7Days);

                starsChart.zoomScale('x', {
                    min: startIndex,
                    max: totalDays - 1
                });

                // Подстраиваем Y-ось под выбранный период
                setTimeout(() => adjustYScale(starsChart), 50);
            }
        }

        // Initialize chart when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();

            // Setup HTMX listeners for data fetcher
            const dataFetcher = document.getElementById('chartDataFetcher');
            if (dataFetcher) {
                dataFetcher.addEventListener('htmx:responseError', function(evt) {
                    console.error('Chart data loading error:', evt.detail);
                });

                dataFetcher.addEventListener('htmx:afterRequest', function(evt) {
                    console.log('HTMX request completed', evt.detail);
                    if (evt.detail.successful) {
                        try {
                            const data = JSON.parse(evt.detail.xhr.responseText);
                            console.log('Chart data received:', data);
                            updateChart(data);
                        } catch (e) {
                            console.error('Error parsing chart data:', e);
                        }
                    }
                });
            }
        });
    </script>
</stack:push>

<define:body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="@route(\App\Backend\Repository\Controller::ROUTE_LIST)" class="text-decoration-none">
                            <i class="bi bi-list"></i> [[Repository List]]
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $repository->fullName }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h2 class="card-title mb-0">
                            <i class="bi bi-github"></i>
                            <a href="{{ $repository->htmlUrl }}" target="_blank" class="text-decoration-none">
                                {{ $repository->fullName }}
                            </a>
                            @if($repository->private)
                            <span class="badge bg-warning text-dark ms-2">
                                    <i class="bi bi-lock"></i> Private
                                </span>
                            @endif
                            @if($repository->fork)
                            <span class="badge bg-info ms-2">
                                    <i class="bi bi-git"></i> Fork
                                </span>
                            @endif
                        </h2>
                        <button class="btn btn-outline-primary btn-sm"
                                title="Refresh"
                                hx-post="@route(\App\Backend\Repository\Controller::ROUTE_TOUCH)"
                                hx-vals='{"repository_name": "{{ $repository->fullName }}"}'
                                hx-swap="none">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        @if($repository->description)
                        <p class="text-muted mb-3">{{ $repository->description }}</p>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-star-fill text-warning me-2"></i>
                                    <strong>{{ $repository->stargazersCount }}</strong>
                                    <span class="text-muted ms-1">stars</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-git text-primary me-2"></i>
                                    <strong>{{ $repository->forksCount }}</strong>
                                    <span class="text-muted ms-1">forks</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-eye text-info me-2"></i>
                                    <strong>{{ $repository->watchersCount }}</strong>
                                    <span class="text-muted ms-1">watchers</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-circle text-danger me-2"></i>
                                    <strong>{{ $repository->openIssuesCount }}</strong>
                                    <span class="text-muted ms-1">open issues</span>
                                </div>
                            </div>
                        </div>

                        @if($repository->topics)
                        <div class="mt-3">
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($repository->topics as $topic)
                                <span class="badge bg-secondary">{{ $topic }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($repository->language)
                        <div class="mt-3">
                                <span class="badge bg-primary">
                                    <i class="bi bi-code"></i> {{ $repository->language }}
                                </span>
                        </div>
                        @endif

                        @if($repository->homepage)
                        <div class="mt-3">
                            <a href="{{ $repository->homepage }}" target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-link-45deg"></i> [[Homepage]]
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-graph-up"></i> [[Stars Over Time]]
                        </h3>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="zoomToLast7Days()" title="[[Last 7 days]]">
                                7d
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="zoomToLast30Days()" title="[[Last 30 days]]">
                                30d
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetZoom()" title="[[Reset zoom]]">
                                <i class="bi bi-arrows-fullscreen"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> [[Drag to select period, wheel to zoom, drag to pan]]
                            </small>
                        </div>
                        <div style="height: 400px;">
                            <canvas id="starsChart"></canvas>
                            <div id="chartDataFetcher"
                                 hx-get="@route(\App\Backend\Repository\Controller::ROUTE_CHART, ['owner' => $repository->owner->login, 'name' => $repository->name])"
                                 hx-trigger="load, every 30s"
                                 hx-swap="none"
                                 style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-person-circle"></i> [[Owner Information]]
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="{{ $repository->owner->avatarUrl }}"
                                 alt="{{ $repository->owner->login }}"
                                 class="me-3"
                                 width="48"
                                 height="48">
                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ $repository->owner->htmlUrl }}" target="_blank"
                                       class="text-decoration-none">
                                        {{ $repository->owner->login }}
                                    </a>
                                </h5>
                                <small class="text-muted">ID: {{ $repository->owner->id }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> [[Repository Details]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>[[Created]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-plus"></i>
                                {{ $repository->createdAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Last Updated]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-check"></i>
                                {{ $repository->updatedAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Last Push]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-upload"></i>
                                {{ $repository->pushedAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Default Branch]]:</strong><br>
                            <span class="badge bg-success">{{ $repository->defaultBranch }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>[[Size]]:</strong><br>
                            <small class="text-muted">{{ $repository->size }} KB</small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Visibility]]:</strong><br>
                            <span
                                class="badge {{ $repository->visibility === 'public' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $repository->visibility }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-gear"></i> [[Features]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between">
                                <span>[[Issues]]:</span>
                                <i class="bi {{ $repository->hasIssues ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Projects]]:</span>
                                <i class="bi {{ $repository->hasProjects ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Wiki]]:</span>
                                <i class="bi {{ $repository->hasWiki ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Pages]]:</span>
                                <i class="bi {{ $repository->hasPages ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Downloads]]:</span>
                                <i class="bi {{ $repository->hasDownloads ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>[[Discussions]]:</span>
                                <i class="bi {{ $repository->hasDiscussions ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-link-45deg"></i> [[Clone URLs]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small">HTTPS:</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" value="{{ $repository->cloneUrl }}" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="navigator.clipboard.writeText('{{ $repository->cloneUrl }}')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">SSH:</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" value="{{ $repository->sshUrl }}" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="navigator.clipboard.writeText('{{ $repository->sshUrl }}')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
