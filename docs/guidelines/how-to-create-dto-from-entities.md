# DTO Creation Guidelines for Entity Mapping

## Overview

Data Transfer Objects (DTOs) are used to prevent domain entities from leaking into application layers. Each entity should have a corresponding DTO and a `toDTO()` method that performs the mapping. This approach ensures clean separation between domain layer and application boundaries.

## Core Principles

### 1. Entity-DTO Isolation
- **Entities** remain in `Internal/ORM/` directories and never leave the domain module
- **DTOs** are placed in module's `DTO/` directory and serve as public contracts
- **Mapping** is always performed via the entity's `toDTO()` method

### 2. Immutability and Type Safety
- DTOs MUST be final readonly classes using constructor property promotion
- All properties MUST have explicit type declarations using PHP 8.1+ features
- Use `declare(strict_types=1);` in all DTO files

### 3. Value Object Integration
- Prefer project-specific value objects over primitive types
- Use existing value objects like `GithubUser`, `GithubRepository`, `GithubOwner`
- Maintain consistency with domain vocabulary

## Directory Structure

```
app/src/Module/{ModuleName}/
├── DTO/
│   └── EntityName.php        # Public DTO
└── Internal/ORM/
    └── EntityNameEntity.php  # Private entity with toDTO() method
```

## Implementation Pattern

### DTO Structure

```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\DTO;

final class {EntityName} implements \Stringable
{
    public function __construct(
        public readonly int $id,
        public readonly SomeValueObject $valueObject,
        public readonly bool $active,
        public readonly ?\SomeInfo $info,
        public readonly \DateTimeInterface $updatedAt,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return (string) $this->valueObject;
    }
}
```

### Entity toDTO() Method

```php
public function toDTO(): EntityNameDto
{
    return new EntityNameDto(
        id: $this->id,
        valueObject: new SomeValueObject($this->property),
        active: $this->active,
        info: $this->info,
        updatedAt: $this->updatedAt,
        createdAt: $this->createdAt,
    );
}
```

## Required Elements

### 1. DTO Class Declaration
- Must be `final class`
- Must implement `\Stringable` interface
- Use constructor property promotion with `readonly` properties
- All properties must have explicit types

### 2. Constructor Parameters
- **Required order**: Core properties first, then timestamps
- **Naming**: Use same names as entity properties where possible
- **Types**: Use value objects instead of primitives when available
- **Nullability**: Mirror entity property nullability

### 3. String Representation
- Implement `__toString(): string` method
- Return type annotation: `@return non-empty-string`
- Typically delegates to a primary value object's string representation

### 4. Entity Mapping Method
- Method name must be `toDTO()`
- Return type must match the DTO class
- Use named arguments for clarity
- Map all entity properties to DTO constructor

## Type Mapping Guidelines

### Common Mappings
- `int $id` → `int $id` (direct mapping)
- `string $login` → `GithubUser $login` (wrap in value object)
- `string $name, string $owner` → `GithubRepository $fullName` (combine into value object)
- `bool $active` → `bool $active` (direct mapping)
- `SomeInfo $info` → `?SomeInfo $info` (respect nullability)
- `\DateTimeInterface` → `\DateTimeInterface` (direct mapping)

### Value Object Usage
- **GitHub entities**: Use `GithubUser`, `GithubOwner`, `GithubRepository`
- **Composite data**: Combine related properties into single value objects
- **Domain concepts**: Create new value objects for domain-specific data

### Relationship Handling
- **Simple relations**: Include related entity IDs
- **Complex relations**: Consider separate DTOs or collections
- **Lazy loading**: Don't trigger additional queries in `toDTO()`

## Best Practices

### 1. Consistency
- Follow existing DTO patterns in the codebase
- Use consistent naming between entity properties and DTO properties
- Maintain same property order across related DTOs

### 2. Performance
- Avoid expensive operations in `toDTO()` methods
- Don't load relationships unless already available
- Keep mapping logic simple and direct

### 3. Testing
- Test DTO creation from entities
- Verify all properties are correctly mapped
- Test string representation output
- Ensure immutability is preserved

### 4. Documentation
- Add property-level PHPDoc when types need clarification
- Document complex mapping logic
- Explain business rules that affect the mapping

## Examples

### Simple Entity-DTO Mapping
```php
// Entity
class UserEntity extends ActiveRecord
{
    public int $id;
    public string $login;
    public UserInfo $info;
    public \DateTimeInterface $createdAt;

    public function toDTO(): User
    {
        return new User(
            id: $this->id,
            login: new GithubUser($this->login),
            info: $this->info,
            createdAt: $this->createdAt,
        );
    }
}

// DTO
final class User implements \Stringable
{
    public function __construct(
        public readonly int $id,
        public readonly GithubUser $login,
        public readonly UserInfo $info,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    public function __toString(): string
    {
        return (string) $this->login;
    }
}
```

### Complex Entity-DTO Mapping
```php
// Entity with relationships
class CampaignEntity extends ActiveRecord
{
    public UuidInterface $uuid;
    public string $title;
    public bool $visible;
    public \DateTimeInterface $startedAt;
    public ?\DateTimeInterface $finishedAt;
    public array $repositories = [];

    public function toDTO(): Campaign
    {
        return new Campaign(
            uuid: $this->uuid,
            title: $this->title,
            visible: $this->visible,
            startedAt: $this->startedAt,
            finishedAt: $this->finishedAt,
            repositoryCount: count($this->repositories),
            createdAt: $this->createdAt,
        );
    }
}
```

## Validation Checklist

Before considering a DTO complete, verify:

- [ ] DTO is `final class` with `readonly` properties
- [ ] DTO implements `\Stringable` interface
- [ ] All properties have explicit type declarations
- [ ] Constructor uses named parameters
- [ ] Entity has `toDTO()` method returning correct type
- [ ] All entity properties are mapped to DTO
- [ ] Value objects are used instead of primitives where appropriate
- [ ] `__toString()` method is properly implemented
- [ ] File uses `declare(strict_types=1);`
- [ ] Follows project naming conventions

This pattern ensures consistent, type-safe, and maintainable data transfer between domain entities and application layers.