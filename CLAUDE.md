# Claude Instructions for GitHub Stars Project

## Project Overview

This is a **GitHub Stars Analytics Application** built to collect and analyze GitHub stargazers for promotional events and community engagement. The primary use case is identifying users who starred specific repositories to include them in events like merchandise giveaways, contests, or community activities.

### Core Business Logic
1. **Repository Management** - Track multiple GitHub repositories of interest
2. **Stargazer Collection** - Fetch and store users who starred tracked repositories  
3. **Data Analytics** - Analyze stargazer patterns, dates, and overlaps between repositories
4. **Export Functionality** - Generate participant lists for events and marketing campaigns

### Technical Architecture
- **Framework**: Spiral Framework v3.15+ (PHP 8.1+)
- **Server**: RoadRunner for high-performance HTTP/CLI processing
- **Database**: Cycle ORM with Active Record pattern
- **API Integration**: GitHub API v3 with token rotation system
- **Frontend**: Bootstrap 5 + Stempler template engine

This project has established guidelines that should be followed for all development work. Before making any code changes, please review and apply the relevant guidelines from the `docs/guidelines/` directory.

## Available Guidelines

### PHP Development
- **[PHP Best Practices](docs/guidelines/how-to-write-php-code-best-practices.md)** - Core PHP coding standards including modern PHP 8.1+ features, enums, immutability, type system, and security practices. Always follow these standards when writing or modifying PHP code.

### Console Commands
- **[Console Command Guidelines](docs/guidelines/how-to-write-console-command.md)** - Standards for creating console commands, including structure, type system usage, interactive patterns, and best practices. Use when implementing any command-line functionality.

### Testing
- **[Testing Guidelines](docs/guidelines/how-to-write-tests.md)** - Unit testing standards using PHPUnit with modern PHP 8.1+ features, AAA pattern, module testing, and mock object guidelines. Follow when writing or modifying tests.

### Documentation Translation
- **[Translation Guide](docs/guidelines/how-to-translate-readme-docs.md)** - Process for translating documentation using LLMs with multilanguage README pattern. Use when translating project documentation.

### Frontend Development
- **[Stempler Template Guidelines](docs/guidelines/how-to-write-stempler-templates.md)** - Comprehensive guide for writing Stempler templates including variable interpolation, control structures, Bootstrap 5 integration, security practices, and component patterns. Follow when creating or modifying any view templates.
- **Bootstrap 5** - This project uses Bootstrap 5 for frontend styling and components. Always use Bootstrap 5 utility classes, grid system, and components when working with views and templates. Bootstrap CSS and JS are loaded from CDN in the layout files.

### Data Transfer Objects (DTOs)
- **[DTO Creation Guidelines](docs/guidelines/how-to-create-dto-from-entities.md)** - Complete guide for creating DTOs from domain entities, including immutable patterns, value object integration, mapping methods, and entity isolation principles. Follow when creating DTOs for any domain entities.
- **[DTO Views and Controllers Generation](docs/guidelines/how-to-generate-dto-views-controllers.md)** - Systematic approach for generating complete CRUD interfaces for DTOs, including list views, detail views, optional edit forms, and corresponding controllers. Use when asked to generate views and controllers for specific DTOs.

## Domain-Specific Development Guidelines

### GitHub API Integration
- **Rate Limiting**: Always use token rotation from `TokenPool` to avoid API rate limits
- **Pagination**: Use the existing `getStargazers()` pattern for paginated API calls
- **Error Handling**: Implement proper GitHub API error handling for 403, 404, and rate limit responses
- **Data Consistency**: Ensure stargazer data includes repository context and timestamp information

### Data Modeling Patterns
- **Entity Relationships**: Use Cycle ORM annotations for defining relationships between Repository, User, and Stargazer entities
- **UUID Strategy**: Apply UUID7 behavior for all new entities following the `GithubToken` pattern
- **Timestamps**: Include CreatedAt/UpdatedAt behaviors for audit trails
- **Value Objects**: Use DTOs like `GithubRepository`, `GithubUser` for data transfer between layers

### Business Logic Implementation
- **Stargazer Collection**: Implement deduplication logic to handle users starring multiple tracked repositories
- **Analytics Features**: Build aggregate functions for stargazer statistics and overlap analysis
- **Event Management**: Design participant selection logic with configurable criteria (date ranges, repository combinations)

## Project Standards Enforcement

When working on this codebase:

1. **Always consult the relevant guideline** before implementing new features or making changes
2. **Follow established patterns** found in existing code and documented guidelines  
3. **Use modern PHP 8.1+ features** as specified in the PHP best practices guide
4. **Write comprehensive tests** following the testing guidelines for all new functionality
5. **Apply proper type annotations** and use project value objects where appropriate
6. **Consider GitHub API constraints** when designing data collection and processing workflows

## Code Quality Requirements

- Use `declare(strict_types=1);` in all PHP files
- Apply final classes by default unless inheritance is needed
- Follow PER-2 coding standards (extends PSR-12)
- Use enums instead of class constants for fixed value sets
- Implement proper error handling with meaningful exceptions
- Write tests for all concrete implementations following the AAA pattern

These guidelines ensure consistency, maintainability, and code quality across the entire project.