# My awesome Web application

Hello developer! Welcome to your new awesome `Web` application built with the Spiral framework.

We're excited that you've chosen Spiral for your project and we hope that our installer package has made the
installation process a breeze.

To help you get started, we've provided some instructions for configuring the individual packages that were installed.
Depending on the packages you chose during the installation, you'll find the following next steps:

## Configuration

### Environment variables

- Please, configure the environment variables in the `.env` file at the application's root.


### RoadRunnerBridge

- The settings for RoadRunner are in a file `.rr.yaml` at the main folder of the app.
- Documentation: https://spiral.dev/docs/start-server


### CycleBridge

- Database configuration file: `app/config/database.php`
- Migrations configuration file: `app/config/migration.php`
- Cycle ORM configuration file: `app/config/cycle.php`
- Documentation: `https://spiral.dev/docs/basics-orm`


### SpiralValidator

- Read more about validation in the Spiral Framework: https://spiral.dev/docs/validation-factory
- Documentation: https://spiral.dev/docs/validation-spiral


### StemplerBridge

- Read more about views in the Spiral Framework: https://spiral.dev/docs/views-configuration
- Documentation: https://spiral.dev/docs/views-stempler


### DataGridBridge

- Documentation: `https://spiral.dev/docs/component-data-grid`


### LeagueEvent

- Documentation: https://spiral.dev/docs/advanced-events


### Scheduler

- Documentation: https://spiral.dev/docs/advanced-scheduler


### Translator

- Documentation: https://spiral.dev/docs/advanced-i18n


### TemporalBridge

- Documentation: https://spiral.dev/docs/temporal-configuration


### SentryBridge

- Configure the `SENTRY_DSN` environment variable to enable Sentry error reporting.
- Documentation: https://spiral.dev/docs/basics-errors


## Usage

To create your first controller effortlessly, use the scaffolding command:

```bash
php app.php create:controller CurrentDate
```

After executing this command, a new controller class will be created in the `src/Endpoint/Web` directory. The
class will look like this:

```php
namespace App\Endpoint\Web;

final class CurrentDateController
{
    public function show(): string
    {
        return \date('Y-m-d H:i:s');
    }
}
```

The next step involves associating a route with your controller.

Spiral simplifies route definition in your application by utilizing PHP attributes. You just need to add the #[Route]
attribute to the controller's method, as shown below:

```php
use Spiral\Router\Annotation\Route;

// ...

#[Route(route: '/date', name: 'current-date', methods: 'GET')]
public function show(): string
{
    return \date('Y-m-d H:i:s');
}
```

To view the list of routes, use the following command:

```bash
php app.php route:list
```

You should observe your current-date route within the displayed list:

```bash
+--------------+--------+----------+------------------------------------------------+--------+
| Name:        | Verbs: | Pattern: | Target:                                        | Group: |
+--------------+--------+----------+------------------------------------------------+--------+
| current-date | GET    | /date    | App\Endpoint\Web\CurrentDateController->show   | web    |
+--------------+--------+----------+------------------------------------------------+--------+
```

#### What's Next?

Now, dive deeper into the fundamentals by reading some articles:

* [Routing](https://spiral.dev/docs/http-routing)
* [Annotated Routing](https://spiral.dev/docs/http-routing#attribute-based-routing)
* [Middleware](https://spiral.dev/docs/http-middleware)
* [Error Pages](https://spiral.dev/docs/http-errors)
* [Custom HTTP handler](https://spiral.dev/docs/cookbook-psr-15)
* [Scaffolding](https://spiral.dev/docs/basics-scaffolding)


### RoadRunner Queue server

Before you can start the queue server, you need to configure it.

#### Queue broker configuration

Firs of all, you need to configure the queue broker. All information about the queue broker configuration can be found
in the [RoadRunner documentation](https://roadrunner.dev/docs/queues-overview).


#### Spiral configuration

You can read more about the configuration options in
the [Spiral documentation](https://spiral.dev/docs/queue-roadrunner/current/en).

#### Starting the queue server

To start the queue server using RoadRunner, run the following command in your project directory:

```bash
./rr serve
```


### RoadRunner HTTP server

To start HTTP server using RoadRunner, run the following command in your project directory:

```bash
./rr serve
```

Once the server is running, you can access your application in a web browser by going to the following
URL: http://127.0.0.1:8080.

> **Note**:
> For more information on how to use RoadRunner with Spiral, please consult
> the [official documentation](https://spiral.dev/docs/start-server).


## Console commands

### Download or update RoadRunner

Allows to install the latest version of the RoadRunner compatible with your environment (operating system, processor
architecture, runtime, etc...).

```bash
composer rr:download
# or
./vendor/bin/rr get-binary
```

## Useful resources

- [**Spiral Framework documentation**](https://spiral.dev/docs)
- [**Roadmap of Learning Spiral Framework**](https://spiral.dev/roadmap) - For all the newcomers who are eager to dive into the Spiral Environment, this roadmap will be your guiding star. We understand the challenges beginners face, and with this structured path, our aim is to simplify your learning journey.
- [**RoadRunner documentation**](https://roadrunner.dev/docs)
- [Community packages](https://github.com/spiral-packages)
- [Buggregator](https://github.com/buggregator/server) — OpenSource tool that offers a range of debugging features for Long running PHP applications.
- [Birddog](https://github.com/roadrunner-server/birddog) — OpenSource tool for monitoring RoadRunner instances.
- [Support us](https://github.com/sponsors/roadrunner-server)
- [Contributing](https://spiral.dev/docs/about-contributing/)

## Project Structure

If you chose to install the default application skeleton, your project will have the following directory structure:

```
- Endpoint
    - Web
        - UserController.php
        - Filter
            - CreateUserFilter.php
        - Middleware
            - LocaleMiddleware.php
        - Interceptor
            - ValidateFiltersInterceptor.php
        - routes.php
    - Console
        - Interceptor
            - PromptRequiredArguments.php
        - CreateUserCommand.php
    - RPC
        - ...
    - Temporal
        - Workflow
            - ...
        - Activity
            - ...
- Application
    - Bootloader
        - RoutesBootloader.php
        - UserModuleBootloader.php
    - Exception
        - SomeException.php
        - Renderer
            - ViewRenderer.php
    - AppDirectories.php
    - Kernel.php
- Domain
    - User
        - Entity
            - User.php
        - Service
            - StoreUserService.php
        - Repository
            - UserRepositoryInterface.php
        - Exception
            - UserNotFoundException.php
- Infrastructure
    - Persistence
        - CycleUserRepository.php
    - CycleORM
        - Typecaster
            - UuidTypecast.php
    - Interceptor
        - LogInterceptor.php
        - ExceptionHandlerInterceptor.php
```

#### Here's a brief explanation of the directories and files in this structure:

- **Endpoint**: This directory contains the entry points for your application, including HTTP endpoints (in the Web
  subdirectory), command-line interfaces (in the Console subdirectory), and gRPC services (in the RPC subdirectory).

- **Application**: This directory contains the core of your application, including the Kernel class that boots your
  application, the Bootloader classes that register services with the container, and the Exception directory that
  contains exception handling logic.

- **Domain**: This directory contains your domain logic, organized by subdomains. For example, an Entity for the User
  model, a Service for storing new users, a Repository for fetching users from the database, and an Exception for
  handling user-related errors.

- **Infrastructure**: This directory contains the infrastructure code for your application, including the Persistence
  directory for database-related code, the CycleORM directory for ORM-related code, and the Interceptor directory for
  global interceptors.

The project structure we provided is a common structure used in many PHP applications, and it can serve as a starting
point for your projects By following this structure, you can organize your code in a logical and maintainable
way, making it easier to build and scale your applications over time. Of course, you may need to make adjustments to fit
the specific needs of your project, but this structure provides a solid foundation for most applications.

**Good luck with your project!**

## Support

If you have any questions or need help with the project, please don't hesitate to reach out! You can find us on Discord
at the following link:

[Discord Server](https://discord.gg/TFeEmCs)

Alternatively, you can create an issue on GitHub to report a bug or request a feature:

[Create an Issue on GitHub](https://github.com/spiral/framework/issues/new/choose)

We welcome any feedback or suggestions you may have, and are always happy to help troubleshoot any issues you may
encounter.
