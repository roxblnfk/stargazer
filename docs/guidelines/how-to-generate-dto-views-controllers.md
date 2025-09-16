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

app/src/Module/Campaign/Form/         # Form validators (if form needed)
├── CreateCampaign.php               # Create form filter
└── UpdateCampaign.php               # Update form filter
```

## Controller Generation Standards

### Controller Structure

```php
<?php

declare(strict_types=1);

namespace App\Backend\Campaign;

use App\Module\Campaign\DTO\Campaign;
use App\Module\Campaign\CampaignService;
use App\Module\Campaign\Form\CreateCampaign;
use App\Module\Campaign\Form\UpdateCampaign;
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

## Form Filter Generation Standards

When generating edit forms, you MUST create corresponding Spiral Filter classes for input validation. Filters provide automatic request validation, data sanitization, and type safety.

### Create Filter Structure

```php
<?php

declare(strict_types=1);

namespace App\Module\Campaign\Form;

use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\Setter;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

class CreateCampaign extends Filter implements HasFilterDefinition
{
    #[Post]
    #[Setter(filter: 'trim')]
    public string $title = '';

    #[Post]
    #[Setter(filter: 'trim')]
    public string $description = '';

    #[Post(key: 'started_at')]
    public ?\DateTimeImmutable $startedAt = null;

    #[Post(key: 'finished_at')]
    public ?\DateTimeImmutable $finishedAt = null;

    public function filterDefinition(): FilterDefinition
    {
        return new FilterDefinition([
            'title' => [
                'required',
                'string',
                ['string::shorter', 255],
            ],
            'description' => [
                'string',
                ['string::shorter', 64000],
            ],
            'startedAt' => [
                'required',
                'datetime::valid',
                ['datetime::future', 'orNow' => false],
            ],
            'finishedAt' => [
                'datetime::valid',
            ],
        ]);
    }
}
```

### Update Filter Structure (with inheritance)

**Best Practice**: Inherit Update filter from Create filter to avoid code duplication:

```php
<?php

declare(strict_types=1);

namespace App\Module\Campaign\Form;

use Ramsey\Uuid\UuidInterface;
use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\Setter;
use Spiral\Validator\FilterDefinition;

final class UpdateCampaign extends CreateCampaign
{
    #[Post]
    public UuidInterface $uuid;

    #[Post]
    #[Setter(filter: 'boolval')]
    public bool $visible = false;

    public function filterDefinition(): FilterDefinition
    {
        $parentDefinition = parent::filterDefinition();

        return new FilterDefinition(
            \array_merge(
                $parentDefinition->validationRules(),
                [
                    'visible' => [
                        // HTML checkbox sends "1" or nothing, so we need to handle string to bool conversion
                        'boolean',
                    ],
                    'startedAt' => [
                        'required',
                        'datetime::valid',
                        // No 'datetime::future' for update - allow past dates
                    ],
                ],
            ),
            $parentDefinition->mappingSchema(),
        );
    }
}
```

### Inheritance Advantages:
1. **Avoid duplication** - all fields and their processing are inherited
2. **Easy maintenance** - changes in base filter are automatically applied
3. **Override only differences** - only different validation rules need to be defined
4. **Clear relationship** - explicitly shows that Update extends Create

### Important Implementation Details:

#### 1. Create Filter must NOT be final
```php
// CORRECT: allows inheritance
class CreateCampaign extends Filter implements HasFilterDefinition

// INCORRECT: prevents inheritance
final class CreateCampaign extends Filter implements HasFilterDefinition
```

#### 2. Use validationRules() and mappingSchema() methods
```php
// CORRECT: get parent validation schema and mapping
$parentDefinition = parent::filterDefinition();
return new FilterDefinition(
    \array_merge($parentDefinition->validationRules(), [...]),
    $parentDefinition->mappingSchema(),
);

// INCORRECT: toArray() does not exist
$parentRules = parent::filterDefinition()->toArray();
```

#### 3. array_merge is more readable than spread operator
```php
// RECOMMENDED: clear merge sequence
return new FilterDefinition(
    \array_merge(
        ['uuid' => 'uuid'],           // New rules
        $parentRules,                 // Inherited rules
        ['startedAt' => [...]],       // Overridden rules
    ),
);

// Less readable: order may be unclear
return new FilterDefinition([
    'uuid' => ['required', 'uuid'],
    ...$parentRules,
    'startedAt' => [...],
]);
```

### Field Type to Validation Mapping

Map DTO properties to appropriate Spiral validation rules based on the official documentation:

#### Basic Validation Rules
- `required` / `notEmpty` - Value cannot be empty
- `boolean` - Validates boolean value
- `integer` - Validates integer type
- `numeric` - Validates numeric value
- `string` - Validates string type
- `array` - Validates array type
- `email` - Validates email format
- `url` - Validates URL format

#### String Fields
```php
// Required string with max length
'title' => ['required', 'string', ['string::shorter', 255]]

// Optional string with max length
'description' => ['string', ['string::shorter', 1000]]

// String with exact length
'code' => ['required', 'string', ['string::length', 6]]

// String with length range
'username' => ['required', 'string', ['string::range', 3, 20]]

// String with min length
'password' => ['required', 'string', ['string::longer', 8]]

// String with regex pattern
'phone' => ['string', ['string::regexp', '/^\+\d{10,15}$/']]

// Email validation
'email' => ['required', 'string', 'email']

// URL validation
'website' => ['string', 'url']
```

#### Boolean Fields
```php
// Required boolean
'active' => ['required', 'boolean']

// Optional boolean (checkbox behavior)
'visible' => ['boolean']  // Defaults handled in property initialization
```

#### Date/Time Fields

**Use `\DateTimeImmutable` type** - Spiral automatically converts form input strings to DateTimeImmutable objects:

```php
// Basic datetime validation
#[Post(key: 'started_at')]
public ?\DateTimeImmutable $startedAt = null;

// Validation rules
'startedAt' => ['required', 'datetime::valid']

// Future date only
'eventDate' => ['required', 'datetime::valid', ['datetime::future', 'orNow' => false]]

// Past date only
'birthDate' => ['datetime::valid', ['datetime::past', 'orNow' => false]]

// Date format validation
'customDate' => [['datetime::format', 'format' => 'Y-m-d']]

// Date before another field
'startDate' => ['datetime::valid']
'endDate' => ['datetime::valid', ['datetime::after', 'field' => 'startDate']]

// Date range with parameters
'deadline' => ['datetime::valid', ['datetime::future', 'orNow' => true, 'useMicroSeconds' => false]]
```

#### Numeric Fields
```php
// Basic integer validation
'count' => ['required', 'integer']

// Integer with minimum value
'quantity' => ['required', 'integer', ['number::higher', 0]]

// Integer with maximum value
'maxItems' => ['integer', ['number::lower', 100]]

// Integer within range
'rating' => ['required', 'integer', ['number::range', 1, 5]]

// Numeric (float/decimal)
'price' => ['required', 'numeric', ['number::higher', 0]]

// Percentage (0-100)
'percentage' => ['numeric', ['number::range', 0, 100]]
```

#### UUID Fields

**Use `\Ramsey\Uuid\UuidInterface` type** - Spiral has built-in UUID support:

```php
// UUID field (typically for IDs)
#[Post]
public ?\Ramsey\Uuid\UuidInterface $parentId = null;

// Validation rules
'parentId' => ['uuid']  // Validates UUID format

// Required UUID
'categoryId' => ['required', 'uuid']
```

#### Enum Fields

**Use PHP 8.1+ Enums** - Spiral automatically handles enum conversion:

```php
// Define enum
enum CampaignStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}

// Use in filter
#[Post]
public ?CampaignStatus $status = null;

// Validation rules
'status' => [
    'required',
    ['array::expectedValues', ['draft', 'published', 'archived']]
]

// Or using enum values
'status' => [
    'required',
    ['array::expectedValues', array_column(CampaignStatus::cases(), 'value')]
]
```

#### Array Fields
```php
// Basic array validation
'tags' => ['array']

// Array with size limits
'items' => ['array', ['array::shorter', 10]]  // Max 10 items
'categories' => ['array', ['array::longer', 1]]   // Min 1 item
'options' => ['array', ['array::range', 2, 5]]    // 2-5 items
'selections' => ['array', ['array::count', 3]]    // Exactly 3 items

// Array with expected values
'status' => ['required', 'string', ['array::expectedValues', ['draft', 'published', 'archived']]]

// Validate as numeric indexed array
'list' => ['array', 'array::isList']

// Validate as associative array
'config' => ['array', 'array::isAssoc']
```

#### UUID/ID Fields
- Never include in create filters (auto-generated)
- Never include in update filters (immutable)

#### Read-only Fields
- Audit fields (createdAt, updatedAt) → Never include
- Calculated fields (counts, stats) → Never include

### Input Attributes and Data Sanitization

```php
// Text fields - always trim whitespace
#[Post]
#[Setter(filter: 'trim')]
public string $title = '';

// Email fields - trim and lowercase
#[Post]
#[Setter(filter: 'trim')]
#[Setter(filter: 'strtolower')]
public string $email = '';

// Boolean fields - handle checkbox behavior (HTML sends "1" or nothing)
#[Post]
#[Setter(filter: 'boolval')]  // Convert "1" string to true, empty to false
public bool $visible = false;  // Default false for checkboxes

// Field name mapping (when form field names differ from property names)
#[Post(key: 'started_at')]    // Maps form field "started_at" to property $startedAt
public string $startedAt = '';

#[Post(key: 'finished_at')]   // Maps form field "finished_at" to property $finishedAt
public ?string $finishedAt = null;

// Optional fields - use nullable types
#[Post]
public ?string $optionalField = null;

// Arrays - specify type hints
#[Post]
public array $tags = [];
```

### Form Field Naming Conventions

**Important**: HTML form field names often use snake_case, but PHP properties use camelCase. Use the `key` parameter to map between them:

```php
// HTML form sends: started_at, finished_at, user_id
// PHP properties use: $startedAt, $finishedAt, $userId

#[Post(key: 'started_at')]
public string $startedAt = '';

#[Post(key: 'finished_at')]
public string $finishedAt = '';

#[Post(key: 'user_id')]
public int $userId = 0;
```

### Controller Integration

Update controller methods to use filters:

```php
#[Route(route: '/campaign/store', name: self::ROUTE_STORE, methods: ['POST'], group: 'backend')]
public function store(CreateCampaign $filter): mixed
{
    // Filter automatically validates and populates (DateTimeImmutable objects are ready to use)
    $campaign = $this->campaignService->createCampaign([
        'title' => $filter->title,
        'description' => $filter->description,
        'visible' => $filter->visible,
        'startedAt' => $filter->startedAt,  // Already DateTimeImmutable
        'finishedAt' => $filter->finishedAt, // Already DateTimeImmutable or null
    ]);

    return $this->views->render('campaign:info', [
        'campaign' => $campaign,
    ]);
}

#[Route(route: '/campaign/update/<uuid>', name: self::ROUTE_UPDATE, methods: ['POST'], group: 'backend')]
public function update(string $uuid, UpdateCampaign $filter): mixed
{
    // Filter handles validation automatically (DateTimeImmutable objects are ready to use)
    $campaign = $this->campaignService->updateCampaign($uuid, [
        'title' => $filter->title,
        'description' => $filter->description,
        'visible' => $filter->visible,
        'startedAt' => $filter->startedAt,  // Already DateTimeImmutable
        'finishedAt' => $filter->finishedAt, // Already DateTimeImmutable or null
    ]);

    return $this->views->render('campaign:info', [
        'campaign' => $campaign,
    ]);
}
```

### Validation Error Handling

Filters automatically handle validation. For custom error handling in templates:

```php
// In controller - check if validation failed
public function store(CreateCampaign $filter): mixed
{
    if (!$filter->isValid()) {
        return $this->views->render('campaign:form', [
            'campaign' => null,
            'errors' => $filter->getErrors(),
        ]);
    }

    // Process valid data...
}
```

```php
// In template - display errors
@if(isset($errors) && $errors->has('title'))
    <div class="invalid-feedback d-block">
        {{ $errors->first('title') }}
    </div>
@endif
```

### Key Differences Between Create and Update Filters

#### Create Filter Characteristics:
- **No UUID field** - auto-generated on creation
- **Stricter validation** - e.g., `datetime::future` for event dates
- **Default values** - for optional fields
- **All required fields** must be present

#### Update Filter Characteristics:
- **Includes UUID field** - required for identifying record to update
- **Relaxed validation** - e.g., allow past dates for started events
- **Existing data preservation** - nullable fields may remain unchanged
- **Validation focuses on data integrity** rather than business rules

```php
// CREATE: No UUID, stricter date validation
final class CreateCampaign extends Filter
{
    // No UUID field
    public ?\DateTimeImmutable $startedAt = null; // Must be future

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'startedAt' => ['required', 'datetime::valid', ['datetime::future', 'orNow' => false]]
        ]);
    }
}

// UPDATE: Has UUID, relaxed date validation
final class UpdateCampaign extends Filter
{
    public ?\Ramsey\Uuid\UuidInterface $uuid = null; // Required for updates
    public ?\DateTimeImmutable $startedAt = null; // Can be past date

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'uuid' => ['required', 'uuid'],
            'startedAt' => ['required', 'datetime::valid'] // No future requirement
        ]);
    }
}
```

### Best Practices for Filter Generation

1. **Use inheritance for Update filters** - extend Create filter and add only UUID + overrides
   ```php
   final class UpdateCampaign extends CreateCampaign
   {
       #[Post] public ?UuidInterface $uuid = null;

       public function filterDefinition(): FilterDefinitionInterface
       {
           $parentRules = parent::filterDefinition()->validationRules();
           return new FilterDefinition(\array_merge([...], $parentRules, [...]));
       }
   }
   ```

2. **Create filters must NOT be final** - allow inheritance for Update filters
3. **Use validationRules() method** - correct way to get parent validation rules
4. **Prefer array_merge over spread** - more explicit merge order and readability
5. **Always create both Create and Update filters** - different validation rules and field sets
6. **Include UUID in Update filters** - required for record identification
7. **Use meaningful validation rules** - match business requirements and use cases
8. **Apply proper type hints** - `DateTimeImmutable`, `UuidInterface`, Enums
9. **Apply data sanitization** - trim strings, convert checkboxes with `boolval`
10. **Set appropriate defaults** - especially for boolean fields and nullable types
11. **Never validate read-only fields in Create** - UUID, audit timestamps, calculated values
12. **Use specific date validation** - future-only for new events, flexible for updates
13. **Include length limits** - match database constraints
14. **Follow naming convention** - `Create[Entity]`, `Update[Entity]`

This ensures robust form handling with automatic validation and clean separation of concerns.

### Composite Filters for Complex Forms

For DTOs with nested objects or arrays, use Composite Filters with `#[NestedFilter]` and `#[NestedArray]` attributes:

```php
<?php

declare(strict_types=1);

namespace App\Backend\Campaign\Form;

use App\Backend\Repository\Form\RepositoryFilter;
use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\NestedArray;
use Spiral\Filters\Attribute\NestedFilter;
use Spiral\Filters\Attribute\Setter;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\FilterDefinitionInterface;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

final class CreateCampaignWithRepositories extends Filter implements HasFilterDefinition
{
    #[Post]
    #[Setter(filter: 'trim')]
    public string $title = '';

    #[Post]
    #[Setter(filter: 'trim')]
    public string $description = '';

    #[Post]
    public bool $visible = true;

    // Single nested object
    #[NestedFilter(class: CampaignSettingsFilter::class)]
    public CampaignSettingsFilter $settings;

    // Array of nested filters
    #[NestedArray(class: RepositoryFilter::class, input: new Post)]
    public array $repositories = [];

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'visible' => ['boolean'],
            // Nested validations are handled by child filters
        ]);
    }
}
```

```php
// Child filter for nested objects
final class CampaignSettingsFilter extends Filter implements HasFilterDefinition
{
    #[Post]
    public bool $autoStart = false;

    #[Post]
    public int $maxParticipants = 100;

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'autoStart' => ['boolean'],
            'maxParticipants' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);
    }
}
```

```php
// Child filter for arrays
final class RepositoryFilter extends Filter implements HasFilterDefinition
{
    #[Post]
    public string $name = '';

    #[Post]
    public string $owner = '';

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'name' => ['required', 'string', 'max:100'],
            'owner' => ['required', 'string', 'max:50'],
        ]);
    }
}
```

#### Use Cases for Composite Filters:
- **Campaign with multiple repositories**
- **User profile with nested address**
- **Order with line items array**
- **Survey with multiple questions**
- **Form with dynamic sections**

### Complete Spiral Validation Rules Reference

Based on the official Spiral documentation, here are all available validation rules:

#### Core Validation Rules
```php
// Basic type validation
'required'    // Value cannot be empty
'notEmpty'    // Alternative to required
'boolean'     // Must be boolean
'integer'     // Must be integer
'numeric'     // Must be numeric (int/float)
'string'      // Must be string
'array'       // Must be array
'email'       // Valid email format
'url'         // Valid URL format
```

#### String Validation Rules (prefix `string::`)
```php
['string::shorter', 255]                    // Length <= 255
['string::longer', 10]                      // Length >= 10
['string::length', 6]                       // Length exactly 6
['string::range', 3, 20]                    // Length between 3-20
['string::regexp', '/^pattern$/']           // Matches regex pattern
```

#### Numeric Validation Rules (prefix `number::`)
```php
['number::range', 1, 100]                   // Value between 1-100
['number::higher', 0]                       // Value >= 0
['number::lower', 50]                       // Value <= 50
```

#### Array Validation Rules (prefix `array::`)
```php
['array::count', 5]                         // Exactly 5 elements
['array::shorter', 10]                      // Max 10 elements
['array::longer', 1]                        // Min 1 element
['array::range', 2, 5]                      // 2-5 elements
['array::expectedValues', ['a', 'b', 'c']]  // Values must be in list
'array::isList'                             // Numeric indexed array
'array::isAssoc'                            // Associative array
```

#### DateTime Validation Rules (prefix `datetime::`)
```php
'datetime::valid'                           // Valid datetime
'datetime::timezone'                        // Valid timezone
['datetime::format', 'format' => 'Y-m-d']  // Matches specific format
['datetime::future', 'orNow' => false]     // In future (options: orNow, useMicroSeconds)
['datetime::past', 'orNow' => false]       // In past (same options)
['datetime::before', 'field' => 'endDate'] // Before another field
['datetime::after', 'field' => 'startDate'] // After another field
```

#### Parameter Examples for DateTime Rules
```php
// Future date with options
['datetime::future', 'orNow' => true, 'useMicroSeconds' => false]

// Before another field with options
['datetime::before', 'field' => 'deadline', 'orEquals' => true, 'useMicroSeconds' => false]

// Custom format validation
['datetime::format', 'format' => 'd.m.Y H:i:s']
```

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
