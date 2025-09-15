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

### Method Calls and Text Processing
```php
<!-- DateTime formatting -->
{{ $repository->createdAt->format('Y-m-d H:i:s') }}

<!-- Text truncation using PHP functions -->
{{ substr($description, 0, 80) }}{{ strlen($description) > 80 ? '...' : '' }}

<!-- Or using a more elegant approach -->
{{ mb_strlen($description) > 80 ? mb_substr($description, 0, 80) . '...' : $description }}

<!-- Number formatting -->
{{ number_format($repository->stargazersCount) }}

<!-- IMPORTANT: The pipe "|" is for DEFAULT VALUES, not filters -->
{{ $description | 'No description available' }}  <!-- Correct: default value -->
{{ $count | 0 }}  <!-- Correct: default value -->
```

### Default Values vs Filters
```php
<!-- CORRECT: Using pipe for default values -->
{{ $user->name | 'Anonymous' }}
{{ $repository->description | 'No description' }}

<!-- INCORRECT: Do NOT use pipe for filters -->
{{ $text|truncate:100 }}  <!-- This is WRONG -->
{{ $number|format }}       <!-- This is WRONG -->

<!-- CORRECT: Use PHP functions for text manipulation -->
{{ strlen($text) > 100 ? substr($text, 0, 100) . '...' : $text }}
{{ number_format($number) }}
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

<!-- Empty handling - CORRECT way in Stempler -->
@if(empty($items))
    <p>No items found</p>
@else
    @foreach($items as $item)
        {{ $item->name }}
    @endforeach
@endif

<!-- Alternative empty handling -->
@empty($items)
    <p>No items found</p>
@else
    @foreach($items as $item)
        {{ $item->name }}
    @endforeach
@endempty

<!-- INCORRECT: @forelse is NOT supported in Stempler -->
@forelse($items as $item)  <!-- WRONG - this doesn't work -->
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

## Internationalization (i18n)

The GitHub Stars project uses Spiral Framework's built-in internationalization system. All user-facing text must be translatable using the `[[key]]` syntax.

### Translation System Overview
- Translation files are stored in `app/locale/` directory
- Russian translations: `app/locale/ru/messages.en.php`
- The system uses English keys and Russian values
- Framework automatically detects user locale and applies appropriate translations

### Translation Key Syntax
```php
<!-- Basic translation -->
<h1>[[Repository Information]]</h1>
<span>[[Created]]:</span>
<button>[[Add Repository]]</button>

<!-- With HTML entities (automatically handled) -->
<p>[[We support open projects & community]]</p>

<!-- Translation keys should be descriptive and use English -->
[[Tracking repositories]]
[[Projects for which you can get points]]
[[Put a star]]
[[Star set]]
```

### Translation Key Naming Conventions
- Use clear, descriptive English phrases as keys
- Use title case for headings: `[[Repository Information]]`
- Use sentence case for descriptions: `[[Projects for which you can get points]]`
- Keep keys concise but meaningful: `[[More]]` instead of `[[Click here for more information]]`
- Use consistent terminology across the application

### Adding New Translations

**CRITICAL**: Every time you add translatable text to templates, you MUST add the corresponding translation to the locale file.

#### Step 1: Use Translation Brackets in Template
```php
<div class="first-time-banner">
    <span class="first-time-text">[[First time?]]</span>
</div>
```

#### Step 2: Add Translation to Locale File
Add the entry to `app/locale/ru/messages.en.php`:
```php
return [
    // ... existing translations
    'First time?' => 'Первый раз?',
];
```

### Common Translation Patterns

#### UI Elements
```php
<!-- Buttons -->
<button>[[Save]]</button>           → 'Save' => 'Сохранить'
<button>[[Cancel]]</button>         → 'Cancel' => 'Отменить'
<button>[[Delete]]</button>         → 'Delete' => 'Удалить'

<!-- Navigation -->
<a href="#">[[Back]]</a>            → 'Back' => 'Назад'
<a href="#">[[Home]]</a>            → 'Home' => 'Главная'
<a href="#">[[Profile]]</a>         → 'Profile' => 'Профиль'

<!-- Status messages -->
[[Success]]                         → 'Success' => 'Успешно'
[[Error]]                          → 'Error' => 'Ошибка'
[[Loading]]                        → 'Loading' => 'Загрузка'
```

#### GitHub-Specific Terms
```php
[[Repository]]                      → 'Repository' => 'Репозиторий'
[[Stargazer]]                      → 'Stargazer' => 'Звездочёт'
[[Stars]]                          → 'Stars' => 'Звезды'
[[Forks]]                          → 'Forks' => 'Форки'
[[Issues]]                         → 'Issues' => 'Проблемы'
[[Pull Requests]]                  → 'Pull Requests' => 'Pull Request-ы'
```

#### Form Labels and Placeholders
```php
<label>[[Username]]</label>         → 'Username' => 'Имя пользователя'
<input placeholder="[[Enter username]]"> → 'Enter username' => 'Введите имя пользователя'
```

### Complex Translation Cases

#### Interpolation with Variables
For dynamic content, keep the translation key simple and handle formatting in the controller:
```php
<!-- Template -->
<p>[[User has X stars]]</p>

<!-- Controller prepares the translated string with variable -->
$translator->trans('User has X stars', ['X' => $starCount])
```

#### Pluralization
Handle pluralization logic in the controller, not in templates:
```php
<!-- Template -->
<span>[[Star count]]</span>

<!-- Controller handles plural forms -->
$starText = $count === 1 ? $translator->trans('star') : $translator->trans('stars');
```

#### Long Descriptions
For multi-sentence text, use descriptive keys:
```php
[[Enter your GitHub username and see which projects you have backed]]
[[We create an open source code and really count on your stars!]]
```

### Translation File Structure
Keep the translation file organized and sorted:
```php
<?php
declare(strict_types=1);

return [
    // Navigation
    'Back' => 'Назад',
    'Home' => 'Главная',

    // GitHub terms
    'Repository' => 'Репозиторий',
    'Stargazer' => 'Звездочёт',
    'Stars' => 'Звезды',

    // UI elements
    'Loading' => 'Загрузка',
    'Save' => 'Сохранить',

    // Messages
    'Welcome message' => 'Добро пожаловать!',
];
```

### Best Practices for AI Development

When working on this codebase:

1. **Always use translation brackets** for any user-visible text
2. **Immediately add translations** to `app/locale/ru/messages.en.php` when adding new text
3. **Check existing translations** before creating new keys - reuse existing keys when possible
4. **Use meaningful English keys** that describe the content, not the location
5. **Test in both languages** if possible to ensure proper layout with different text lengths

### Testing Translations
- Russian text is typically longer than English - ensure UI accommodates this
- Test with both short and long translations
- Verify special characters display correctly
- Check text alignment and spacing with translated content

This i18n approach ensures the GitHub Stars application provides a seamless experience for both English and Russian users while maintaining code quality and consistency.

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

### Use PHP Functions for Formatting
```php
<!-- CORRECT: Use PHP functions for text/number formatting -->
{{ number_format($number) }}
{{ mb_strlen($text) > 100 ? mb_substr($text, 0, 100) . '...' : $text }}
{{ $date->format('Y-m-d') }}

<!-- INCORRECT: Do NOT use pipe syntax for filters -->
{{ $number|number }}        <!-- WRONG -->
{{ $text|truncate:100 }}    <!-- WRONG -->
{{ $date|date:'Y-m-d' }}    <!-- WRONG -->
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
            <strong>{{ number_format($repository->stargazersCount) }}</strong>
            <span class="text-muted ms-1">stars</span>
        </div>
    </div>
</div>
```

### Empty States
```php
<!-- CORRECT: Using proper Stempler syntax for empty collections -->
@if(empty($repositories))
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <h3 class="mt-3">No repositories found</h3>
        <p class="text-muted">Add your first repository to get started.</p>
        <a href="@route(\App\Feature\Repository\Controller::ROUTE_ADD)" class="btn btn-primary">
            Add Repository
        </a>
    </div>
@else
    @foreach($repositories as $repository)
        <!-- Repository item -->
    @endforeach
@endif

<!-- Alternative using @empty directive -->
@empty($repositories)
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <h3 class="mt-3">No repositories found</h3>
        <p class="text-muted">Add your first repository to get started.</p>
    </div>
@else
    @foreach($repositories as $repository)
        <!-- Repository item -->
    @endforeach
@endempty

<!-- INCORRECT: @forelse is NOT supported -->
@forelse($repositories as $repository)  <!-- WRONG -->
    <!-- Repository item -->
@empty
    <!-- Empty state -->
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