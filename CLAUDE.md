# Claude Instructions for this Project

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

## Project Standards Enforcement

When working on this codebase:

1. **Always consult the relevant guideline** before implementing new features or making changes
2. **Follow established patterns** found in existing code and documented guidelines  
3. **Use modern PHP 8.1+ features** as specified in the PHP best practices guide
4. **Write comprehensive tests** following the testing guidelines for all new functionality
5. **Apply proper type annotations** and use project value objects where appropriate

## Code Quality Requirements

- Use `declare(strict_types=1);` in all PHP files
- Apply final classes by default unless inheritance is needed
- Follow PER-2 coding standards (extends PSR-12)
- Use enums instead of class constants for fixed value sets
- Implement proper error handling with meaningful exceptions
- Write tests for all concrete implementations following the AAA pattern

These guidelines ensure consistency, maintainability, and code quality across the entire project.