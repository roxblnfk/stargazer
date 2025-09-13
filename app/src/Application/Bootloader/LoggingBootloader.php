<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Level;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Http\Middleware\ErrorHandlerMiddleware;
use Spiral\Monolog\Bootloader\MonologBootloader;
use Spiral\Monolog\Config\MonologConfig;

/**
 * The bootloader is responsible for configuring the application specific loggers.
 *
 * @link https://spiral.dev/docs/basics-logging
 */
final class LoggingBootloader extends Bootloader
{
    public function __construct(
        private readonly ConfiguratorInterface $config,
    ) {}

    public function init(MonologBootloader $monolog, EnvironmentInterface $env): void
    {
        $handler = new SocketHandler($env->get('MONOLOG_SOCKET_HOST'), chunkSize: 1);
        $handler->setFormatter(new JsonFormatter(JsonFormatter::BATCH_MODE_NEWLINES));
        $monolog->addHandler('socket', $handler);
        if ($env->get('MONOLOG_DEFAULT_CHANNEL') !== 'socket') {
            $handler = null;
        }

        // HTTP level errors
        $monolog->addHandler(
            channel: ErrorHandlerMiddleware::class,
            handler: $monolog->logRotate(
                directory('runtime') . 'logs/http.log',
            ),
        );

        // SQL logs
        $monolog->addHandler(
            channel: 'database',
            handler: $handler ?? $monolog->logRotate(
                filename: directory('runtime') . 'logs/sql.log',
                maxFiles: 1,
            ),
        );

        // app level errors
        $monolog->addHandler(
            channel: MonologConfig::DEFAULT_CHANNEL,
            handler: $handler ?? $monolog->logRotate(
                filename: directory('runtime') . 'logs/error.log',
                level: Level::Error,
                maxFiles: 25,
                bubble: false,
            ),
        );

        // debug and info messages via global LoggerInterface
        $monolog->addHandler(
            channel: MonologConfig::DEFAULT_CHANNEL,
            handler: $monolog->logRotate(
                filename: directory('runtime') . 'logs/debug.log',
            ),
        );
    }
}
