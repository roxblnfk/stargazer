# Stempler Template Guidelines for GitHub Stars Project

## Overview

This guide establishes standards for writing Stempler templates in the GitHub Stars application. Stempler is Spiral Framework's powerful template engine that provides PHP-based syntax with automatic escaping and component system.

## Template File Structure

### File Naming
- Use `.dark.php` extension for all template files
- Follow kebab-case naming: `repository-info.dark.php`, `user-list.dark.php`
- Place templates in feature-specific `views/` directories: `app/src/Feature/Repository/views/`

### Basic Template Structure
```php
<extends:layout title="Page Title"/>

<stack:push name="styles">
    <!-- Additional CSS if needed -->
</stack:push>

<define:body>
    <!-- Template content -->
</define:body>
```

## Variable Interpolation

### Escaped Output (Default)
```php
<!-- Safe, automatically escaped -->
{{ $repository->name }}
{{ $user->email }}
```

### Unescaped Output
```php
<!-- Only for trusted content -->
{!! $htmlContent !!}
```

### Property Access
```php
<!-- Object properties use -> syntax -->
{{ $repository->fullName }}
{{ $repository->owner->login }}

<!-- Array access -->
{{ $data['key'] }}
```

### Method Calls
```php
<!-- DateTime formatting -->
{{ $repository->createdAt->format('Y-m-d H:i:s') }}

<!-- Filters -->
{{ $repository->stargazersCount|number }}
{{ $description|truncate:100 }}
```

## Control Structures

### Conditionals
```php
@if($repository->private)
    <span class="badge bg-warning">Private</span>
@endif

@if($repository->description)
    <p>{{ $repository->description }}</p>
@else
    <p class="text-muted">No description available</p>
@endif

<!-- Multiple conditions -->
@if($repository->fork && $repository->private)
    <span class="badge bg-info">Private Fork</span>
@elseif($repository->fork)
    <span class="badge bg-info">Fork</span>
@endif
```

### Loops
```php
@foreach($repository->topics as $topic)
    <span class="badge bg-secondary">{{ $topic }}</span>
@endforeach

<!-- With key access -->
@foreach($repositories as $key => $repo)
    <div data-index="{{ $key }}">{{ $repo->name }}</div>
@endforeach

<!-- Empty handling -->
@forelse($items as $item)
    {{ $item->name }}
@empty
    <p>No items found</p>
@endforelse
```

### Switch Statements
```php
@switch($repository->visibility)
    @case('public')
        <span class="badge bg-success">Public</span>
        @break
    @case('private')
        <span class="badge bg-warning">Private</span>
        @break
    @default
        <span class="badge bg-secondary">Unknown</span>
@endswitch
```

## Bootstrap 5 Integration

### Grid System
```php
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Main content -->
        </div>
        <div class="col-lg-4">
            <!-- Sidebar -->
        </div>
    </div>
</div>
```

### Components
```php
<!-- Cards -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">{{ $title }}</h3>
    </div>
    <div class="card-body">
        {{ $content }}
    </div>
</div>

<!-- Badges -->
<span class="badge bg-{{ $repository->private ? 'warning' : 'success' }}">
    {{ $repository->visibility }}
</span>
```

### Icons (Bootstrap Icons)
```php
<i class="bi bi-github"></i>
<i class="bi bi-star-fill text-warning"></i>
<i class="bi {{ $repository->hasIssues ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}"></i>
```

## Navigation and Links

### Internal Routes
```php
<!-- Controller route constants -->
<a href="@route(\App\Feature\Repository\Controller::ROUTE_LIST)">
    Repository List
</a>

<!-- With parameters -->
<a href="@route(\App\Feature\Repository\Controller::ROUTE_INFO, ['id' => $repository->id])">
    View Details
</a>
```

### External Links
```php
<a href="{{ $repository->htmlUrl }}" target="_blank" rel="noopener">
    {{ $repository->fullName }}
</a>
```

### Breadcrumbs
```php
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="@route(\App\Feature\Repository\Controller::ROUTE_LIST)">
                Repositories
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            {{ $repository->name }}
        </li>
    </ol>
</nav>
```

## Form Handling

### Form Structure
```php
<form method="POST" action="@route(\App\Feature\Repository\Controller::ROUTE_ADD)">
    @csrf
    
    <div class="mb-3">
        <label for="repositoryUrl" class="form-label">Repository URL</label>
        <input type="url" class="form-control" id="repositoryUrl" name="url" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Add Repository</button>
</form>
```

### Validation Errors
```php
@if($errors->has('url'))
    <div class="invalid-feedback d-block">
        {{ $errors->first('url') }}
    </div>
@endif
```

## Internationalization

### Translation Keys
```php
<!-- Use [[key]] syntax for translation keys -->
<h1>[[Repository Information]]</h1>
<span>[[Created]]:</span>
<button>[[Add Repository]]</button>
```

## Security Best Practices

### Always Use Escaped Output
```php
<!-- Good: Automatically escaped -->
{{ $userInput }}

<!-- Bad: Only for trusted content -->
{!! $userInput !!}
```

### CSRF Protection
```php
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

### External Link Security
```php
<a href="{{ $externalUrl }}" target="_blank" rel="noopener noreferrer">
    External Link
</a>
```

## Component Organization

### Reusable Components
```php
<!-- Repository card component -->
<define:repository-card>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $repository->name }}</h5>
            <p class="card-text">{{ $repository->description }}</p>
        </div>
    </div>
</define:repository-card>
```

### Using Components
```php
<use:repository-card repository="{{ $repo }}"/>
```

## Error Handling

### Null Safety
```php
@if($repository->description)
    <p>{{ $repository->description }}</p>
@endif

<!-- Or with null coalescing -->
{{ $repository->description ?? 'No description available' }}
```

### Optional Chaining
```php
{{ $repository->owner->login ?? 'Unknown' }}
```

## Performance Considerations

### Minimize PHP in Templates
```php
<!-- Good: Logic in controller -->
{{ $formattedDate }}

<!-- Bad: Logic in template -->
{{ date('Y-m-d', strtotime($repository->createdAt)) }}
```

### Use Filters for Formatting
```php
{{ $number|number }}
{{ $text|truncate:100 }}
{{ $date|date:'Y-m-d' }}
```

## Code Quality Standards

### Indentation and Formatting
- Use 4 spaces for indentation
- Keep PHP and HTML properly aligned
- Close tags on same indentation level as opening tags

### Comments
```php
{{-- Template comments (not rendered) --}}

<!-- HTML comments (rendered) -->
```

### Naming Conventions
- Use camelCase for variables: `$repositoryList`
- Use kebab-case for template files: `repository-info.dark.php`
- Use PascalCase for component names: `<RepositoryCard>`

## Common Patterns

### Data Display Cards
```php
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">{{ $title }}</h3>
        @if($showActions)
            <div class="btn-group btn-group-sm">
                <a href="#" class="btn btn-outline-primary">Edit</a>
                <a href="#" class="btn btn-outline-danger">Delete</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        {{ $content }}
    </div>
</div>
```

### Statistics Display
```php
<div class="row g-3">
    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-star-fill text-warning me-2"></i>
            <strong>{{ $repository->stargazersCount|number }}</strong>
            <span class="text-muted ms-1">stars</span>
        </div>
    </div>
</div>
```

### Empty States
```php
@forelse($repositories as $repository)
    <!-- Repository item -->
@empty
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <h3 class="mt-3">No repositories found</h3>
        <p class="text-muted">Add your first repository to get started.</p>
        <a href="@route(\App\Feature\Repository\Controller::ROUTE_ADD)" class="btn btn-primary">
            Add Repository
        </a>
    </div>
@endforelse
```

## Testing Templates

### Template Variables
- Always test with null/empty values
- Verify proper escaping of user input
- Test conditional rendering paths

### Browser Compatibility
- Validate HTML structure
- Test responsive design on different screen sizes
- Verify Bootstrap classes render correctly

This guideline ensures consistent, secure, and maintainable Stempler templates across the GitHub Stars application.