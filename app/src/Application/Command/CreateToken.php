<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Backend\Home\Controller;
use Spiral\Auth\TokenStorageInterface;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;
use Spiral\Cycle\Auth\Token;
use Spiral\Router\RouterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run `php app.php auth:create-token`
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[AsCommand(name: 'auth:create-token', description: 'Create a new admin auth token')]
final class CreateToken extends Command
{
    public function __invoke(TokenStorageInterface $storage, OutputInterface $output, RouterInterface $router): int
    {
        $token = $storage->create([]);

        \assert($token instanceof Token);

        $output->writeln('Token was created.');

        $output->writeln('');
        $output->writeln('Now go to:');
        $id = \urlencode($token->getID());
        $output->writeln($router->uri(Controller::ROUTE_AUTH) . "?token={$id}");

        return self::SUCCESS;
    }
}
