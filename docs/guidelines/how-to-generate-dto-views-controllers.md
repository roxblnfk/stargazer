# DTO Views and Controllers Generation Guidelines

## Overview

This guideline provides a systematic approach for generating complete CRUD interfaces for DTOs in the GitHub Stars application. It covers automatic generation of list views, detail views, optional edit forms, and corresponding controllers using Bootstrap 5, HTMX, and Stempler templates.

**IMPORTANT**: Before implementing any templates, you MUST review and follow the [Stempler Template Guidelines](docs/guidelines/how-to-write-stempler-templates.md), which contains comprehensive standards for template syntax, security practices, internationalization, and component patterns.

## Technologies Stack

- **Stempler Template Engine** - PHP-based templating with automatic escaping
- **Bootstrap 5** - UI framework for styling and components
- **HTMX** - Progressive enhancement for dynamic interactions
- **Internationalization (i18n)** - Multi-language support using `[[key]]` syntax

## Generation Workflow

When requested to generate views and controllers for a specific DTO (e.g., `app/src/Module/Campaign/DTO/Campaign.php`), follow this systematic process:

### Step 1: Requirements Analysis

**ALWAYS ASK**: "Do you need to generate an edit form for this DTO?"

This determines whether to generate:
- **Read-only mode**: List + Detail views only
- **Full CRUD mode**: List + Detail + Edit form views

### Step 2: DTO Analysis

Before generating any code, analyze the DTO structure:

```php
// Example: Campaign DTO analysis
- UuidInterface $uuid (primary key, read-only)
- string $title (editable, required)
- string $description (editable, textarea)
- bool $visible (editable, checkbox/toggle)
- DateTimeInterface $startedAt (editable, datetime-local)
- ?DateTimeInterface $finishedAt (editable, optional datetime-local)
- int $repositoryCount (read-only, calculated)
- int $memberCount (read-only, calculated)
- DateTimeInterface $updatedAt (read-only, audit)
- DateTimeInterface $createdAt (read-only, audit)
```

### Step 3: File Structure Generation

Create the following files based on DTO `App\Module\Campaign\DTO\Campaign`:

```
app/src/Backend/Campaign/
├── Controller.php                    # Main controller
├── views/
│   ├── list.dark.php                # List view template
│   ├── info.dark.php                # Detail view template
│   └── form.dark.php                # Edit form (if requested)
```

## Controller Generation Standards

### Controller Structure

```php
<?php

declare(strict_types=1);

namespace App\Backend\Campaign;

use App\Module\Campaign\DTO\Campaign;
use App\Module\Campaign\CampaignService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Router\Annotation\Route;
use Spiral\Views\ViewsInterface;

final class Controller
{
    // Route constants following naming convention
    public const ROUTE_LIST = 'campaign:list';
    public const ROUTE_INFO = 'campaign:info';
    public const ROUTE_CREATE = 'campaign:create';     // if form needed
    public const ROUTE_EDIT = 'campaign:edit';         // if form needed
    public const ROUTE_UPDATE = 'campaign:update';     // if form needed
    public const ROUTE_DELETE = 'campaign:delete';     // if form needed

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly CampaignService $campaignService,
    ) {}

    #[Route(route: '/campaign/list', name: self::ROUTE_LIST, methods: ['GET'], group: 'backend')]
    public function list(): mixed
    {
        return $this->views->render('campaign:list', [
            'campaigns' => $this->campaignService->getCampaigns(),
        ]);
    }

    #[Route(route: '/campaign/info/<uuid>', name: self::ROUTE_INFO, methods: ['GET'], group: 'backend')]
    public function info(string $uuid): mixed
    {
        $campaign = $this->campaignService->getCampaign($uuid);

        return $this->views->render('campaign:info', [
            'campaign' => $campaign,
        ]);
    }

    // Additional CRUD methods if form is needed
    // ...
}
```

### Route Method Patterns

**List Route**:
- Pattern: `/[entity]/list`
- Name: `[entity]:list`
- Method: GET
- Returns: Collection of DTOs

**Info Route**:
- Pattern: `/[entity]/info/<id>`
- Name: `[entity]:info`
- Method: GET
- Returns: Single DTO

**Form Routes** (if edit form needed):
- Create: `/[entity]/create` → `[entity]:create` (GET)
- Store: `/[entity]/store` → `[entity]:store` (POST)
- Edit: `/[entity]/edit/<id>` → `[entity]:edit` (GET)
- Update: `/[entity]/update/<id>` → `[entity]:update` (POST)
- Delete: `/[entity]/delete/<id>` → `[entity]:delete` (POST)

### JSON Response Methods

For API endpoints that need to return JSON data (charts, HTMX responses, etc.), simply return an array from the controller method. Spiral Framework automatically converts arrays to JSON responses:

```php
#[Route(route: '/campaign/chart/<uuid>', name: self::ROUTE_CHART, methods: ['GET'], group: 'backend')]
public function chart(string $uuid): array
{
    $campaign = $this->campaignService->getCampaign($uuid);
    $chartData = $this->campaignService->getChartData($campaign->uuid);

    return $chartData; // Automatically converted to JSON response
}

#[Route(route: '/campaign/toggle-visibility/<uuid>', name: self::ROUTE_TOGGLE, methods: ['POST'], group: 'backend')]
public function toggleVisibility(string $uuid): array
{
    $visible = $this->campaignService->toggleVisibility($uuid);

    return ['visible' => $visible]; // Returns: {"visible": true}
}
```

**Key Points:**
- Return type should be `array`
- No need to use `ResponseInterface` or `$this->response->json()`
- Spiral automatically sets appropriate headers (`Content-Type: application/json`)
- Perfect for HTMX endpoints and chart data

## Template Generation Standards

### List View Template (`list.dark.php`)

```php
<extends:layout title="[[Campaign List]]"/>

<stack:push name="styles">
</stack:push>

<stack:push name="scripts">
</stack:push>

<define:body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>[[Campaign List]]</h1>
            <!-- If form is needed -->
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
                                    <!-- If form is needed -->
                                    <a href="@route(\App\Backend\Campaign\Controller::ROUTE_EDIT, ['uuid' => $campaign->uuid])"
                                       class="btn btn-outline-warning" title="[[Edit]]">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger" title="[[Delete]]"
                                            hx-delete="@route(\App\Backend\Campaign\Controller::ROUTE_DELETE, ['uuid' => $campaign->uuid])"
                                            hx-confirm="[[Are you sure you want to delete this campaign?]]"
                                            hx-target="closest tr" hx-swap="outerHTML">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <h4 class="mt-3 text-muted">[[No campaigns found]]</h4>
                                <p class="text-muted">[[Create your first campaign to get started]]</p>
                                <!-- If form is needed -->
                                <a href="@route(\App\Backend\Campaign\Controller::ROUTE_CREATE)"
                                   class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> [[Add Campaign]]
                                </a>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</define:body>
```

### Detail View Template (`info.dark.php`)

```php
<extends:layout title="[[Campaign]] - {{ $campaign->title }}"/>

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
                    <li class="breadcrumb-item active" aria-current="page">{{ $campaign->title }}</li>
                </ol>
            </nav>

            <!-- If form is needed -->
            <div class="btn-group">
                <a href="@route(\App\Backend\Campaign\Controller::ROUTE_EDIT, ['uuid' => $campaign->uuid])"
                   class="btn btn-warning">
                    <i class="bi bi-pencil"></i> [[Edit]]
                </a>
                <button class="btn btn-outline-danger"
                        hx-delete="@route(\App\Backend\Campaign\Controller::ROUTE_DELETE, ['uuid' => $campaign->uuid])"
                        hx-confirm="[[Are you sure you want to delete this campaign?]]"
                        hx-on::after-request="if(event.detail.xhr.status === 200) window.location.href = '@route(\App\Backend\Campaign\Controller::ROUTE_LIST)'">
                    <i class="bi bi-trash"></i> [[Delete]]
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Main Information Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h2 class="card-title mb-0">
                            {{ $campaign->title }}
                            <span class="badge bg-{{ $campaign->visible ? 'success' : 'secondary' }} ms-2">
                                {{ $campaign->visible ? '[[Visible]]' : '[[Hidden]]' }}
                            </span>
                        </h2>
                    </div>
                    <div class="card-body">
                        @if($campaign->description)
                            <p class="text-muted mb-3">{{ $campaign->description }}</p>
                        @endif

                        <!-- Stats Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-folder text-primary me-2"></i>
                                    <strong>{{ $campaign->repositoryCount }}</strong>
                                    <span class="text-muted ms-1">[[repositories]]</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people text-success me-2"></i>
                                    <strong>{{ $campaign->memberCount }}</strong>
                                    <span class="text-muted ms-1">[[members]]</span>
                                </div>
                            </div>
                        </div>

                        <!-- Period Information -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-event text-info me-2"></i>
                                    <div>
                                        <strong>[[Started]]:</strong><br>
                                        <small class="text-muted">{{ $campaign->startedAt->format('Y-m-d H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-check text-warning me-2"></i>
                                    <div>
                                        <strong>[[Finished]]:</strong><br>
                                        <small class="text-muted">
                                            @if($campaign->finishedAt)
                                                {{ $campaign->finishedAt->format('Y-m-d H:i') }}
                                            @else
                                                [[Ongoing]]
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Technical Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> [[Technical Details]]
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>[[UUID]]:</strong><br>
                            <small class="text-muted font-monospace">{{ $campaign->uuid }}</small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Created]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-plus"></i>
                                {{ $campaign->createdAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <strong>[[Last Updated]]:</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-check"></i>
                                {{ $campaign->updatedAt->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
```

### Edit Form Template (`form.dark.php`) - Only if Requested

```php
<extends:layout title="{{ isset($campaign) ? '[[Edit Campaign]]' : '[[Create Campaign]]' }} - {{ $campaign->title ?? '[[New Campaign]]' }}"/>

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
                    @if(isset($campaign))
                        <li class="breadcrumb-item">
                            <a href="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])" class="text-decoration-none">
                                {{ $campaign->title }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">[[Edit]]</li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">[[Create Campaign]]</li>
                    @endif
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title mb-0">
                            {{ isset($campaign) ? '[[Edit Campaign]]' : '[[Create Campaign]]' }}
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="@route(isset($campaign) ? \App\Backend\Campaign\Controller::ROUTE_UPDATE : \App\Backend\Campaign\Controller::ROUTE_STORE, isset($campaign) ? ['uuid' => $campaign->uuid] : [])">
                            @csrf

                            <!-- Title Field -->
                            <div class="mb-3">
                                <label for="title" class="form-label">[[Title]]</label>
                                <input type="text"
                                       class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $campaign->title ?? '') }}"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description Field -->
                            <div class="mb-3">
                                <label for="description" class="form-label">[[Description]]</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description"
                                          name="description"
                                          rows="4">{{ old('description', $campaign->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Visibility Toggle -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="visible"
                                           name="visible"
                                           value="1"
                                           {{ old('visible', $campaign->visible ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visible">
                                        [[Visible to users]]
                                    </label>
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="mb-3">
                                <label for="started_at" class="form-label">[[Start Date]]</label>
                                <input type="datetime-local"
                                       class="form-control @error('started_at') is-invalid @enderror"
                                       id="started_at"
                                       name="started_at"
                                       value="{{ old('started_at', isset($campaign) ? $campaign->startedAt->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @error('started_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Date (Optional) -->
                            <div class="mb-3">
                                <label for="finished_at" class="form-label">[[End Date]] <small class="text-muted">([[Optional]])</small></label>
                                <input type="datetime-local"
                                       class="form-control @error('finished_at') is-invalid @enderror"
                                       id="finished_at"
                                       name="finished_at"
                                       value="{{ old('finished_at', isset($campaign) && $campaign->finishedAt ? $campaign->finishedAt->format('Y-m-d\TH:i') : '') }}">
                                @error('finished_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="@route(isset($campaign) ? \App\Backend\Campaign\Controller::ROUTE_INFO : \App\Backend\Campaign\Controller::ROUTE_LIST, isset($campaign) ? ['uuid' => $campaign->uuid] : [])"
                                   class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> [[Cancel]]
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-{{ isset($campaign) ? 'save' : 'plus-circle' }}"></i>
                                    {{ isset($campaign) ? '[[Update Campaign]]' : '[[Create Campaign]]' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
```

## Field Type Mapping

Map DTO properties to appropriate form fields:

### Input Types
- `string` → `<input type="text">`
- `?string` → `<input type="text">` (not required)
- `int` → `<input type="number">`
- `float` → `<input type="number" step="0.01">`
- `bool` → `<input type="checkbox">` or `<div class="form-check form-switch">`
- `DateTimeInterface` → `<input type="datetime-local">`
- `?DateTimeInterface` → `<input type="datetime-local">` (not required)

### Special Cases
- **UUID/ID fields** → Read-only, display in tech details
- **Audit fields** (createdAt, updatedAt) → Read-only, display in tech details
- **Calculated fields** (counts, stats) → Read-only, display as badges/metrics
- **Long text** → `<textarea>` instead of `<input>`
- **URLs** → `<input type="url">`
- **Emails** → `<input type="email">`

## HTMX Integration Patterns

### Form Submissions
```php
<!-- Progressive enhancement for forms -->
<form hx-post="@route(\App\Backend\Campaign\Controller::ROUTE_STORE)"
      hx-on::after-request="if(event.detail.xhr.status === 200) window.location.href = event.detail.xhr.getResponseHeader('Location')">
```

### Delete Confirmations
```php
<button hx-delete="@route(\App\Backend\Campaign\Controller::ROUTE_DELETE, ['uuid' => $campaign->uuid])"
        hx-confirm="[[Are you sure you want to delete this campaign?]]"
        hx-target="closest tr"
        hx-swap="outerHTML">
```

### Live Updates
```php
<!-- Auto-refresh data every 30 seconds -->
<div hx-get="@route(\App\Backend\Campaign\Controller::ROUTE_INFO, ['uuid' => $campaign->uuid])"
     hx-trigger="every 30s"
     hx-select="#campaign-stats"
     hx-target="#campaign-stats">
```

## Internationalization Requirements

### Translation Keys for Standard Actions
Always add these translations to `app/locale/ru/messages.en.php`:

```php
// Entity-specific
'Campaign List' => 'Список кампаний',
'Campaign' => 'Кампания',
'Add Campaign' => 'Добавить кампанию',
'Edit Campaign' => 'Редактировать кампанию',
'Create Campaign' => 'Создать кампанию',
'Update Campaign' => 'Обновить кампанию',

// Common actions
'View Details' => 'Просмотреть детали',
'Edit' => 'Редактировать',
'Delete' => 'Удалить',
'Save' => 'Сохранить',
'Cancel' => 'Отменить',
'Back' => 'Назад',

// Status terms
'Visible' => 'Видимая',
'Hidden' => 'Скрытая',
'Active' => 'Активная',
'Inactive' => 'Неактивная',
'Ongoing' => 'Продолжается',

// Confirmation messages
'Are you sure you want to delete this campaign?' => 'Вы уверены, что хотите удалить эту кампанию?',
'No campaigns found' => 'Кампании не найдены',
'Create your first campaign to get started' => 'Создайте свою первую кампанию для начала работы',
```

### Field-Specific Translations
```php
// Field labels (adjust per DTO)
'Title' => 'Название',
'Description' => 'Описание',
'Start Date' => 'Дата начала',
'End Date' => 'Дата окончания',
'Started' => 'Начата',
'Finished' => 'Завершена',
'Created' => 'Создана',
'Last Updated' => 'Последнее обновление',
'UUID' => 'Идентификатор',
'Technical Details' => 'Технические детали',
'Optional' => 'Необязательно',
'Visible to users' => 'Видимо пользователям',
```

## Bootstrap 5 Component Standards

### Cards
```php
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">{{ $title }}</h3>
        <!-- Actions -->
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

### Tables
```php
<div class="table-responsive">
    <table class="table table-striped">
        <!-- Always include icons in headers -->
        <thead>
            <tr>
                <th><i class="bi bi-card-text"></i> [[Field Name]]</th>
            </tr>
        </thead>
    </table>
</div>
```

### Buttons and Actions
```php
<!-- Primary actions -->
<a href="#" class="btn btn-primary">
    <i class="bi bi-plus-circle"></i> [[Add]]
</a>

<!-- Button groups for multiple actions -->
<div class="btn-group btn-group-sm">
    <a href="#" class="btn btn-outline-primary" title="[[View]]">
        <i class="bi bi-eye"></i>
    </a>
    <a href="#" class="btn btn-outline-warning" title="[[Edit]]">
        <i class="bi bi-pencil"></i>
    </a>
    <button class="btn btn-outline-danger" title="[[Delete]]">
        <i class="bi bi-trash"></i>
    </button>
</div>
```

### Icons Standards
- **View/Info**: `bi-eye`
- **Edit**: `bi-pencil`
- **Delete**: `bi-trash`
- **Add/Create**: `bi-plus-circle`
- **Save**: `bi-save`
- **Cancel/Back**: `bi-arrow-left`
- **Date/Time**: `bi-calendar-event`, `bi-calendar-check`, `bi-calendar-plus`
- **Stats/Numbers**: `bi-people`, `bi-folder`, `bi-star`
- **Technical**: `bi-gear`, `bi-info-circle`

## Code Quality Standards

### Template Quality
1. **Consistent indentation** (4 spaces)
2. **Proper escaping** using `{{ }}` for all user data
3. **Semantic HTML** structure
4. **Accessibility** attributes (aria-label, alt, etc.)
5. **Responsive design** with Bootstrap grid classes

### Controller Quality
1. **Type declarations** for all parameters and return types
2. **Dependency injection** in constructor
3. **Route constants** for all endpoints
4. **Service layer usage** for business logic
5. **Consistent error handling**

### Translation Quality
1. **Complete translation coverage** for all user-facing text
2. **Consistent terminology** across templates
3. **Proper key organization** in locale files
4. **Context-appropriate translations**

## Testing Considerations

When generating DTO views and controllers, ensure:

1. **Null safety** - Handle optional fields gracefully
2. **Empty state handling** - Provide meaningful empty states
3. **Error state handling** - Show validation errors clearly
4. **Responsive behavior** - Test on different screen sizes
5. **HTMX functionality** - Verify dynamic interactions work
6. **Translation completeness** - Check all text is translatable

## Example Generation Command Response

When user requests: *"Generate views for `app/src/Module/Campaign/DTO/Campaign.php`"*

**Response:**
"I'll generate the Campaign views and controller. Do you need to generate an edit form for this DTO?

Based on the Campaign DTO analysis, I'll create:
- `app/src/Backend/Campaign/Controller.php` - Main controller with list/info routes
- `app/src/Backend/Campaign/views/list.dark.php` - Campaign list table
- `app/src/Backend/Campaign/views/info.dark.php` - Campaign details view
- `app/src/Backend/Campaign/views/form.dark.php` - Edit form (if requested)

The implementation will include:
- Bootstrap 5 responsive design
- HTMX progressive enhancement
- Complete i18n support with Russian translations
- Read-only fields (UUID, counts, audit dates)
- Editable fields (title, description, visibility, dates)"

This systematic approach ensures consistent, maintainable, and feature-complete CRUD interfaces for any DTO in the GitHub Stars application.
